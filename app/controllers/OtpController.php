<?php
/**
 * OtpController Class
 * Handles OTP verification page, request dispatch, resend logic, and API validation
 */

class OtpController extends Controller
{
    /**
     * Render OTP Verification View
     */
    public function index()
    {
        // Check if there is a pending authentication context or current session
        $pendingUser = $_SESSION['pending_otp_user'] ?? null;
        $currentUser = Auth::user();

        $identifier = $pendingUser['phone'] ?? ($pendingUser['email'] ?? ($currentUser['phone'] ?? ($currentUser['email'] ?? '')));

        $otpModel = $this->model('OtpModel');
        $cooldown = $identifier ? $otpModel->getResendCooldownRemaining($identifier) : 0;
        $activeOtp = $identifier ? $otpModel->getActiveOtpInfo($identifier) : null;

        $maskedIdentifier = $this->maskIdentifier($identifier);

        // Perform real backend check to determine actual connection status
        $connCheck = $otpModel->checkConnection();

        $data = [
            'title' => 'Verifikasi OTP - ' . APP_NAME,
            'active_menu' => 'otp_verify',
            'identifier' => $identifier,
            'masked_identifier' => $maskedIdentifier,
            'cooldown' => $cooldown,
            'seconds_remaining' => $activeOtp ? $activeOtp['seconds_remaining'] : (OTP_EXPIRY_MINUTES * 60),
            'max_attempts' => OTP_MAX_ATTEMPTS,
            'current_attempts' => $activeOtp['attempts'] ?? 0,
            'channel' => $activeOtp['channel'] ?? 'whatsapp',
            'provider' => OTP_PROVIDER,
            'connection' => $connCheck,
            'has_token' => $connCheck['connected']
        ];

        $this->view('templates/header', $data);
        $this->view('auth/otp_verify', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Request a new OTP code (via POST form or internal trigger)
     */
    public function requestOtp()
    {
        $pendingUser = $_SESSION['pending_otp_user'] ?? null;
        $currentUser = Auth::user();

        $userId = $pendingUser['id'] ?? ($currentUser['id'] ?? null);
        $identifier = trim($_POST['identifier'] ?? ($pendingUser['phone'] ?? ($pendingUser['email'] ?? ($currentUser['phone'] ?? ''))));
        $channel = trim($_POST['channel'] ?? 'whatsapp');

        if (empty($identifier)) {
            Flasher::setFlash('Gagal mengirim OTP!', 'Nomor telepon atau email tujuan tidak ditemukan.', 'danger');
            header('Location: ' . BASE_URL . '/index.php?url=otp');
            exit;
        }

        $otpModel = $this->model('OtpModel');

        // 1. Check Rate Limit (Max per hour)
        if (!$otpModel->checkRateLimit($identifier)) {
            Flasher::setFlash('Batas permintaan terlampaui!', 'Anda telah meminta OTP melebihi batas ' . OTP_RATE_LIMIT_PER_HOUR . ' kali per jam. Silakan coba lagi nanti.', 'warning');
            header('Location: ' . BASE_URL . '/index.php?url=otp');
            exit;
        }

        // 2. Check Resend Cooldown
        $cooldown = $otpModel->getResendCooldownRemaining($identifier);
        if ($cooldown > 0) {
            Flasher::setFlash('Harap tunggu!', "Anda dapat mengirim ulang kode OTP dalam {$cooldown} detik.", 'warning');
            header('Location: ' . BASE_URL . '/index.php?url=otp');
            exit;
        }

        // 3. Generate Cryptographically Secure OTP
        $otpCode = $otpModel->generateOtpCode(6);

        // 4. Store in Database
        $otpModel->createOtp($userId, $identifier, $otpCode, $channel);

        // 5. Dispatch via Configured Service API
        $dispatchResult = $otpModel->dispatchOtp($identifier, $otpCode, $channel);

        // Store active session identifier
        $_SESSION['otp_active_identifier'] = $identifier;

        if ($dispatchResult['sent']) {
            Flasher::setFlash('Kode OTP Terkirim!', "Kode verifikasi 6 digit telah dikirim ke {$this->maskIdentifier($identifier)} melalui {$channel}.", 'success');
        } else {
            // Check if simulated mode due to unconfigured/mock token
            if ($dispatchResult['mode'] === 'simulated') {
                $_SESSION['last_simulated_otp'] = $otpCode; // For local developer testing
                Flasher::setFlash('Mode Simulasi Aktif', "Token API belum diatur pada server. [KODE SIMULASI: {$otpCode}]", 'info');
            } else {
                Flasher::setFlash('Layanan Gateway', $dispatchResult['message'], 'warning');
            }
        }

        header('Location: ' . BASE_URL . '/index.php?url=otp');
        exit;
    }

    /**
     * Verify submitted OTP from Form POST
     */
    public function verify()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php?url=otp');
            exit;
        }

        $pendingUser = $_SESSION['pending_otp_user'] ?? null;
        $currentUser = Auth::user();

        $identifier = trim($_POST['identifier'] ?? ($pendingUser['phone'] ?? ($pendingUser['email'] ?? ($currentUser['phone'] ?? ''))));
        $otpCode = trim($_POST['otp'] ?? '');

        // Also support 6 separate inputs if combined
        if (empty($otpCode) && isset($_POST['otp_1'])) {
            $otpCode = trim($_POST['otp_1']) . trim($_POST['otp_2']) . trim($_POST['otp_3']) . trim($_POST['otp_4']) . trim($_POST['otp_5']) . trim($_POST['otp_6']);
        }

        if (empty($identifier) || empty($otpCode)) {
            Flasher::setFlash('Input tidak lengkap!', 'Silakan masukkan 6 digit kode OTP.', 'danger');
            header('Location: ' . BASE_URL . '/index.php?url=otp');
            exit;
        }

        $otpModel = $this->model('OtpModel');
        $verifyResult = $otpModel->verifyOtp($identifier, $otpCode);

        if ($verifyResult['success']) {
            // If user was pending 2FA login, complete authentication
            if ($pendingUser) {
                $_SESSION['user_id']       = $pendingUser['id'];
                $_SESSION['user_name']     = $pendingUser['name'] ?? $pendingUser['full_name'];
                $_SESSION['user_username'] = $pendingUser['username'];
                $_SESSION['user_email']    = $pendingUser['email'] ?? '';
                $_SESSION['user_role']     = $pendingUser['role'];
                $_SESSION['user_phone']    = $pendingUser['phone'] ?? '';
                $_SESSION['otp_verified']  = true;
                unset($_SESSION['pending_otp_user']);

                $roleTitle = ucfirst(str_replace('_', ' ', $pendingUser['role']));
                Flasher::setFlash('Verifikasi Berhasil!', 'Selamat datang, ' . $_SESSION['user_name'] . '! Anda berhasil masuk.', 'success');
                header('Location: ' . BASE_URL . '/index.php?url=dashboard');
                exit;
            } else {
                $_SESSION['otp_verified'] = true;
                Flasher::setFlash('Verifikasi Sukses!', 'Identitas Anda telah terverifikasi dengan aman via OTP.', 'success');
                header('Location: ' . BASE_URL . '/index.php?url=dashboard');
                exit;
            }
        } else {
            Flasher::setFlash('Verifikasi Gagal!', $verifyResult['message'], 'danger');
            header('Location: ' . BASE_URL . '/index.php?url=otp');
            exit;
        }
    }

    /**
     * AJAX API Endpoint: Request OTP
     */
    public function request_api()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $rawInput = file_get_contents('php://input');
        $dataInput = json_decode($rawInput, true) ?: $_POST;

        $pendingUser = $_SESSION['pending_otp_user'] ?? null;
        $currentUser = Auth::user();

        $userId = $pendingUser['id'] ?? ($currentUser['id'] ?? null);
        $identifier = trim($dataInput['identifier'] ?? ($pendingUser['phone'] ?? ($pendingUser['email'] ?? ($currentUser['phone'] ?? ''))));
        $channel = trim($dataInput['channel'] ?? 'whatsapp');

        if (empty($identifier)) {
            echo json_encode(['success' => false, 'message' => 'Identifier telepon/email wajib diisi.']);
            exit;
        }

        $otpModel = $this->model('OtpModel');

        // Check Rate limit
        if (!$otpModel->checkRateLimit($identifier)) {
            echo json_encode([
                'success' => false,
                'message' => 'Batas permintaan telah habis (' . OTP_RATE_LIMIT_PER_HOUR . 'x per jam). Silakan coba lagi nanti.'
            ]);
            exit;
        }

        // Check Resend Cooldown
        $cooldown = $otpModel->getResendCooldownRemaining($identifier);
        if ($cooldown > 0) {
            echo json_encode([
                'success' => false,
                'cooldown' => $cooldown,
                'message' => "Tunggu {$cooldown} detik sebelum meminta ulang kode OTP."
            ]);
            exit;
        }

        $otpCode = $otpModel->generateOtpCode(6);
        $otpModel->createOtp($userId, $identifier, $otpCode, $channel);
        $dispatchResult = $otpModel->dispatchOtp($identifier, $otpCode, $channel);

        echo json_encode([
            'success' => true,
            'message' => $dispatchResult['sent'] ? 'Kode OTP berhasil dikirim.' : ($dispatchResult['mode'] === 'simulated' ? 'Mode simulasi aktif (token belum disetel).' : $dispatchResult['message']),
            'mode' => $dispatchResult['mode'],
            'cooldown' => OTP_RESEND_COOLDOWN,
            'masked_identifier' => $this->maskIdentifier($identifier),
            'debug_simulated_otp' => ($dispatchResult['mode'] === 'simulated') ? $otpCode : null
        ]);
        exit;
    }

    /**
     * AJAX API Endpoint: Verify OTP
     */
    public function verify_api()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $rawInput = file_get_contents('php://input');
        $dataInput = json_decode($rawInput, true) ?: $_POST;

        $pendingUser = $_SESSION['pending_otp_user'] ?? null;
        $currentUser = Auth::user();

        $identifier = trim($dataInput['identifier'] ?? ($pendingUser['phone'] ?? ($pendingUser['email'] ?? ($currentUser['phone'] ?? ''))));
        $otpCode = trim($dataInput['otp'] ?? '');

        if (empty($identifier) || empty($otpCode)) {
            echo json_encode(['success' => false, 'message' => 'Identifier dan 6 digit OTP wajib disertakan.']);
            exit;
        }

        $otpModel = $this->model('OtpModel');
        $verifyResult = $otpModel->verifyOtp($identifier, $otpCode);

        if ($verifyResult['success']) {
            if ($pendingUser) {
                $_SESSION['user_id']       = $pendingUser['id'];
                $_SESSION['user_name']     = $pendingUser['name'] ?? $pendingUser['full_name'];
                $_SESSION['user_username'] = $pendingUser['username'];
                $_SESSION['user_email']    = $pendingUser['email'] ?? '';
                $_SESSION['user_role']     = $pendingUser['role'];
                $_SESSION['user_phone']    = $pendingUser['phone'] ?? '';
                $_SESSION['otp_verified']  = true;
                unset($_SESSION['pending_otp_user']);
            } else {
                $_SESSION['otp_verified'] = true;
            }

            echo json_encode([
                'success' => true,
                'message' => 'Verifikasi OTP berhasil!',
                'redirect_url' => BASE_URL . '/index.php?url=dashboard'
            ]);
            exit;
        }

        echo json_encode([
            'success' => false,
            'message' => $verifyResult['message'],
            'code' => $verifyResult['code'] ?? 'INVALID',
            'remaining_attempts' => $verifyResult['remaining_attempts'] ?? 0
        ]);
        exit;
    }

    /**
     * Helper to mask phone/email for privacy
     */
    private function maskIdentifier($id)
    {
        if (empty($id)) return 'Pengguna';

        if (strpos($id, '@') !== false) {
            $parts = explode('@', $id);
            $name = $parts[0];
            $domain = $parts[1] ?? '';
            $len = strlen($name);
            if ($len <= 2) return $name . '***@' . $domain;
            return substr($name, 0, 2) . str_repeat('*', $len - 2) . '@' . $domain;
        } else {
            $len = strlen($id);
            if ($len <= 4) return '****';
            return substr($id, 0, 4) . str_repeat('*', max(0, $len - 7)) . substr($id, -3);
        }
    }
}
