<?php
/**
 * OtpModel Class
 * Handles DB persistence, cryptographic token generation, rate limiting, and verification for OTP
 */

class OtpModel
{
    private $db;
    private $table = 'otp_codes';

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Generate a cryptographically secure 6-digit numeric OTP
     */
    public function generateOtpCode($length = 6)
    {
        $min = (int)pow(10, $length - 1);
        $max = (int)pow(10, $length) - 1;
        return (string)random_int($min, $max);
    }

    /**
     * Check rate limiting: max requests within the last hour
     */
    public function checkRateLimit($identifier, $maxPerHour = OTP_RATE_LIMIT_PER_HOUR)
    {
        if (!$this->db->isConnected()) {
            return true;
        }

        $oneHourAgo = date('Y-m-d H:i:s', time() - 3600);
        $sql = "SELECT COUNT(*) as total FROM {$this->table}
                WHERE identifier = :identifier AND created_at >= :one_hour_ago";

        $this->db->query($sql);
        $this->db->bind(':identifier', $identifier);
        $this->db->bind(':one_hour_ago', $oneHourAgo);
        $res = $this->db->single();

        return ($res && (int)$res['total'] >= $maxPerHour) ? false : true;
    }

    /**
     * Check cooldown: seconds remaining until the user can request a new OTP
     */
    public function getResendCooldownRemaining($identifier, $cooldownSeconds = OTP_RESEND_COOLDOWN)
    {
        if (!$this->db->isConnected()) {
            return 0;
        }

        $sql = "SELECT created_at FROM {$this->table}
                WHERE identifier = :identifier
                ORDER BY created_at DESC LIMIT 1";

        $this->db->query($sql);
        $this->db->bind(':identifier', $identifier);
        $latest = $this->db->single();

        if ($latest && !empty($latest['created_at'])) {
            $lastCreatedTime = strtotime($latest['created_at']);
            $diff = time() - $lastCreatedTime;
            if ($diff < $cooldownSeconds) {
                return $cooldownSeconds - $diff;
            }
        }
        return 0;
    }

    /**
     * Create a new OTP entry in the database.
     * Invalidates any previous unused OTPs for the same identifier.
     */
    public function createOtp($userId, $identifier, $otpCode, $channel = 'whatsapp')
    {
        $otpHash = password_hash($otpCode, PASSWORD_BCRYPT);
        $expiresAt = date('Y-m-d H:i:s', time() + (OTP_EXPIRY_MINUTES * 60));

        if ($this->db->isConnected()) {
            // Mark any previous unused OTP for this identifier as used/invalid
            $invalidateSql = "UPDATE {$this->table} SET is_used = 1 WHERE identifier = :identifier AND is_used = 0";
            $this->db->query($invalidateSql);
            $this->db->bind(':identifier', $identifier);
            $this->db->execute();

            // Insert new OTP record
            $sql = "INSERT INTO {$this->table} 
                    (user_id, identifier, otp_hash, channel, attempts, max_attempts, is_used, expires_at)
                    VALUES (:user_id, :identifier, :otp_hash, :channel, 0, :max_attempts, 0, :expires_at)";

            $this->db->query($sql);
            $this->db->bind(':user_id', $userId ?: null);
            $this->db->bind(':identifier', $identifier);
            $this->db->bind(':otp_hash', $otpHash);
            $this->db->bind(':channel', $channel);
            $this->db->bind(':max_attempts', OTP_MAX_ATTEMPTS);
            $this->db->bind(':expires_at', $expiresAt);
            $this->db->execute();

            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Verify submitted OTP against backend records
     * Returns array: ['success' => bool, 'message' => string, 'code' => string]
     */
    public function verifyOtp($identifier, $submittedCode)
    {
        if (!$this->db->isConnected()) {
            return [
                'success' => false,
                'message' => 'Koneksi database tidak tersedia.',
                'code' => 'DB_ERROR'
            ];
        }

        $now = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM {$this->table}
                WHERE identifier = :identifier AND is_used = 0
                ORDER BY created_at DESC LIMIT 1";

        $this->db->query($sql);
        $this->db->bind(':identifier', $identifier);
        $otpRecord = $this->db->single();

        if (!$otpRecord) {
            return [
                'success' => false,
                'message' => 'Kode OTP tidak ditemukan atau telah kedaluwarsa. Silakan minta kode baru.',
                'code' => 'NOT_FOUND'
            ];
        }

        // Check if max attempts exceeded
        if ($otpRecord['attempts'] >= $otpRecord['max_attempts']) {
            // Mark as used/invalidated due to brute-force protection
            $this->markAsUsed($otpRecord['id']);
            return [
                'success' => false,
                'message' => 'Batas percobaan verifikasi telah habis (' . $otpRecord['max_attempts'] . 'x salah). Silakan minta kode OTP baru.',
                'code' => 'MAX_ATTEMPTS_EXCEEDED'
            ];
        }

        // Check if expired
        if (strtotime($otpRecord['expires_at']) < time()) {
            $this->markAsUsed($otpRecord['id']);
            return [
                'success' => false,
                'message' => 'Kode OTP telah kedaluwarsa (masa berlaku ' . OTP_EXPIRY_MINUTES . ' menit). Silakan minta kode baru.',
                'code' => 'EXPIRED'
            ];
        }

        // Validate hash
        if (password_verify($submittedCode, $otpRecord['otp_hash'])) {
            // Successfully verified! Mark as used immediately (single-use)
            $this->markAsUsed($otpRecord['id']);
            return [
                'success' => true,
                'message' => 'Verifikasi OTP berhasil.',
                'code' => 'VERIFIED',
                'user_id' => $otpRecord['user_id']
            ];
        } else {
            // Increment attempt counter
            $newAttempts = $otpRecord['attempts'] + 1;
            $updateSql = "UPDATE {$this->table} SET attempts = :attempts WHERE id = :id";
            $this->db->query($updateSql);
            $this->db->bind(':attempts', $newAttempts);
            $this->db->bind(':id', $otpRecord['id']);
            $this->db->execute();

            $remainingAttempts = max(0, $otpRecord['max_attempts'] - $newAttempts);

            if ($remainingAttempts <= 0) {
                $this->markAsUsed($otpRecord['id']);
                return [
                    'success' => false,
                    'message' => 'Kode OTP salah. Batas percobaan telah habis. Silakan kirim ulang kode OTP.',
                    'code' => 'MAX_ATTEMPTS_EXCEEDED'
                ];
            }

            return [
                'success' => false,
                'message' => "Kode OTP salah. Sisa kesempatan: {$remainingAttempts}x percobaan.",
                'code' => 'INVALID_CODE',
                'remaining_attempts' => $remainingAttempts
            ];
        }
    }

    /**
     * Mark an OTP record as used
     */
    public function markAsUsed($id)
    {
        if ($this->db->isConnected()) {
            $sql = "UPDATE {$this->table} SET is_used = 1 WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            return $this->db->execute();
        }
        return false;
    }

    /**
     * Get active OTP record info (for countdown & status UI without leaking code)
     */
    public function getActiveOtpInfo($identifier)
    {
        if (!$this->db->isConnected()) {
            return null;
        }

        $sql = "SELECT id, identifier, channel, attempts, max_attempts, expires_at, created_at
                FROM {$this->table}
                WHERE identifier = :identifier AND is_used = 0
                ORDER BY created_at DESC LIMIT 1";

        $this->db->query($sql);
        $this->db->bind(':identifier', $identifier);
        $record = $this->db->single();

        if ($record) {
            $expiresTime = strtotime($record['expires_at']);
            $secondsLeft = max(0, $expiresTime - time());
            if ($secondsLeft <= 0 || $record['attempts'] >= $record['max_attempts']) {
                return null;
            }
            $record['seconds_remaining'] = $secondsLeft;
            return $record;
        }

        return null;
    }

    private function getGatewaySettings()
    {
        $settings = [
            'provider'     => strtolower(OTP_PROVIDER),
            'api_token'    => OTP_API_TOKEN,
            'endpoint'     => OTP_API_ENDPOINT,
            'country_code' => '62',
            'service_name' => APP_NAME
        ];

        // If ConfigModel exists and table has DB configs, prioritize them dynamically
        if ($this->db && $this->db->isConnected()) {
            try {
                $this->db->query("SELECT config_key, config_value FROM system_configs");
                $rows = $this->db->resultSet();
                if ($rows) {
                    foreach ($rows as $row) {
                        $k = $row['config_key'];
                        $v = trim($row['config_value'] ?? '');
                        if ($k === 'gateway_provider' && !empty($v)) $settings['provider'] = strtolower($v);
                        if ($k === 'api_token' && !empty($v)) $settings['api_token'] = $v;
                        if ($k === 'api_endpoint' && !empty($v)) $settings['endpoint'] = $v;
                        if ($k === 'default_country_code' && !empty($v)) $settings['country_code'] = $v;
                        if ($k === 'service_name' && !empty($v)) $settings['service_name'] = $v;
                    }
                }
            } catch (Exception $e) {
                // Keep default settings
            }
        }

        return $settings;
    }

    /**
     * Test and check connection with configured OTP Gateway
     * Returns: [
     *   'status' => 'connected' | 'invalid_token' | 'empty_token' | 'connection_error' | 'disconnected_device',
     *   'connected' => bool,
     *   'message' => string,
     *   'device_name' => string|null,
     *   'device_status' => string|null
     * ]
     * Never exposes or logs the actual token!
     */
    public function checkConnection()
    {
        $gw = $this->getGatewaySettings();
        $apiToken = $gw['api_token'];
        $provider = $gw['provider'];

        if (empty($apiToken) || $provider === 'mock') {
            return [
                'status' => 'empty_token',
                'connected' => false,
                'message' => 'Token API belum dikonfigurasi pada server.',
                'device_name' => null,
                'device_status' => null
            ];
        }

        if ($provider === 'fonnte') {
            $ch = curl_init('https://api.fonnte.com/device');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 8);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: {$apiToken}"
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                return [
                    'status' => 'connection_error',
                    'connected' => false,
                    'message' => 'Gagal terhubung ke server Fonnte (koneksi timeout / jaringan).',
                    'device_name' => null,
                    'device_status' => null
                ];
            }

            $data = json_decode($response, true);
            if (is_array($data)) {
                $isStatusTrue = isset($data['status']) && ($data['status'] === true || $data['status'] === 'true');
                if ($isStatusTrue) {
                    $deviceStatus = strtolower($data['device_status'] ?? '');
                    $deviceName = $data['name'] ?? 'Fonnte Device';
                    
                    // Device status in Fonnte: 'connect' or 'disconnect'
                    if ($deviceStatus === 'connect') {
                        return [
                            'status' => 'connected',
                            'connected' => true,
                            'message' => 'Layanan Fonnte siap digunakan (Device terhubung).',
                            'device_name' => $deviceName,
                            'device_status' => $deviceStatus
                        ];
                    } else {
                        return [
                            'status' => 'disconnected_device',
                            'connected' => false,
                            'message' => 'Token valid, namun status perangkat WhatsApp di Fonnte sedang terputus (Disconnect).',
                            'device_name' => $deviceName,
                            'device_status' => $deviceStatus
                        ];
                    }
                }

                $reason = strtolower($data['reason'] ?? '');
                if (strpos($reason, 'token') !== false || strpos($reason, 'invalid') !== false) {
                    return [
                        'status' => 'invalid_token',
                        'connected' => false,
                        'message' => 'Token API Fonnte tidak valid atau salah.',
                        'device_name' => null,
                        'device_status' => null
                    ];
                }

                return [
                    'status' => 'connection_error',
                    'connected' => false,
                    'message' => 'Layanan Fonnte menolak permintaan: ' . ($data['reason'] ?? 'Respon tidak dikenali'),
                    'device_name' => null,
                    'device_status' => null
                ];
            }

            return [
                'status' => 'connection_error',
                'connected' => false,
                'message' => 'Format respon gateway tidak valid (HTTP ' . $httpCode . ').',
                'device_name' => null,
                'device_status' => null
            ];
        }

        // Generic / other providers
        return [
            'status' => 'connected',
            'connected' => true,
            'message' => 'Penyedia gateway terkonfigurasi.',
            'device_name' => null,
            'device_status' => null
        ];
    }

    /**
     * Dispatch OTP via configured Gateway/Provider
     * Integrates with external API (e.g. Fonnte / Wablas / Twilio / Generic curl)
     * Handles dry-run / unconfigured token gracefully.
     */
    public function dispatchOtp($recipient, $otpCode, $channel = 'whatsapp')
    {
        $gw = $this->getGatewaySettings();
        $apiToken = $gw['api_token'];
        $endpoint = $gw['endpoint'];
        $provider = $gw['provider'];
        $countryCode = $gw['country_code'] ?: '62';
        $appName = $gw['service_name'] ?: APP_NAME;

        $expiryMinutes = OTP_EXPIRY_MINUTES;
        $message = "*{$appName}*\n\nKode verifikasi (OTP) Anda adalah:\n*{$otpCode}*\n\nMasa berlaku: {$expiryMinutes} Menit.\nDemi keamanan, JANGAN berikan kode ini kepada siapa pun termasuk pihak {$appName}.";

        // If token is missing, report honest status
        if (empty($apiToken) || $provider === 'mock') {
            return [
                'sent' => false,
                'mode' => 'simulated',
                'provider' => $provider,
                'message' => 'API Token belum dikonfigurasi di server environment/config. Mode simulasi aktif.',
                'debug_otp' => (defined('DEBUG_MODE') && DEBUG_MODE) ? $otpCode : null
            ];
        }

        // Dispatch via cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($provider === 'fonnte') {
            // Sanitize phone number: strip non-digits
            $cleanTarget = preg_replace('/[^0-9]/', '', $recipient);

            curl_setopt($ch, CURLOPT_URL, $endpoint ?: 'https://api.fonnte.com/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'target' => $cleanTarget,
                'message' => $message,
                'countryCode' => $countryCode
            ]);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: {$apiToken}"
            ]);
        } elseif ($provider === 'wablas') {
            curl_setopt($ch, CURLOPT_URL, $endpoint ?: 'https://wa.wablas.com/api/send-message');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'phone' => $recipient,
                'message' => $message
            ]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: {$apiToken}",
                "Content-Type: application/x-www-form-urlencoded"
            ]);
        } else {
            // Generic JSON API Gateway
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'to' => $recipient,
                'message' => $message,
                'otp' => $otpCode
            ]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer {$apiToken}",
                "Content-Type: application/json"
            ]);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return [
                'sent' => false,
                'mode' => 'api_error',
                'provider' => $provider,
                'http_code' => $httpCode,
                'message' => 'Gagal terhubung ke gateway pengiriman (jaringan/timeout).'
            ];
        }

        $jsonResp = json_decode($response, true);
        // Fonnte returns HTTP 200 even on some application errors like invalid token: {"status":false,"reason":"invalid token"}
        $isSuccess = ($httpCode >= 200 && $httpCode < 300);
        if ($isSuccess && is_array($jsonResp) && isset($jsonResp['status'])) {
            $isSuccess = ($jsonResp['status'] === true || $jsonResp['status'] === 'true');
        }

        $detailMsg = '';
        if (is_array($jsonResp)) {
            $detailMsg = $jsonResp['reason'] ?? ($jsonResp['message'] ?? ($jsonResp['detail'] ?? ''));
        }

        // Clean user-friendly message without leaking raw payload or internal secrets
        if ($isSuccess) {
            $statusMsg = 'OTP berhasil dikirim ke nomor WhatsApp penerima.';
        } else {
            if (stripos($detailMsg, 'token') !== false || stripos($detailMsg, 'invalid') !== false) {
                $statusMsg = 'Pengiriman gagal: Token API Fonnte tidak valid.';
            } elseif (stripos($detailMsg, 'device') !== false || stripos($detailMsg, 'disconnect') !== false) {
                $statusMsg = 'Pengiriman gagal: Perangkat WhatsApp Fonnte sedang terputus (Disconnect).';
            } elseif (stripos($detailMsg, 'target') !== false || stripos($detailMsg, 'number') !== false) {
                $statusMsg = 'Pengiriman gagal: Nomor tujuan WhatsApp tidak valid.';
            } elseif (!empty($detailMsg)) {
                $statusMsg = 'Pengiriman gagal: ' . htmlspecialchars($detailMsg);
            } else {
                $statusMsg = 'Pengiriman gagal: Layanan gateway mengembalikan respon kesalahan (HTTP ' . $httpCode . ').';
            }
        }

        return [
            'sent' => $isSuccess,
            'mode' => $isSuccess ? 'live_api' : 'api_error',
            'provider' => $provider,
            'http_code' => $httpCode,
            'message' => $statusMsg
        ];
    }
}
