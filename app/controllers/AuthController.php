<?php
/**
 * AuthController Class
 * Login, Logout, Session & Access Denied Route Handling
 */

class AuthController extends Controller
{
    public function index()
    {
        $this->login();
    }

    public function login()
    {
        // If user is already logged in, redirect to dashboard
        if (Auth::check()) {
            header('Location: ' . BASE_URL . '/index.php?url=dashboard');
            exit;
        }

        $data = [
            'title' => 'Login - TKJ Service Center',
            'active_menu' => 'login'
        ];

        $this->view('templates/header', $data);
        $this->view('auth/login', $data);
        $this->view('templates/footer', $data);
    }

    public function processLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($username) || empty($password)) {
                Flasher::setFlash('Username dan Password', 'wajib diisi!', 'danger');
                header('Location: ' . BASE_URL . '/index.php?url=auth/login');
                exit;
            }

            $userModel = $this->model('UserModel');
            $user = $userModel->authenticate($username, $password);

            if ($user) {
                $requireOtp = isset($_POST['require_otp']) && $_POST['require_otp'] === '1';

                if ($requireOtp) {
                    // Set pending user context for 2FA OTP verification
                    $_SESSION['pending_otp_user'] = $user;
                    $identifier = $user['phone'] ?? ($user['email'] ?? '');
                    
                    // Trigger initial OTP dispatch via OtpModel
                    $otpModel = $this->model('OtpModel');
                    $otpCode = $otpModel->generateOtpCode(6);
                    $otpModel->createOtp($user['id'], $identifier, $otpCode, 'whatsapp');
                    $dispatch = $otpModel->dispatchOtp($identifier, $otpCode, 'whatsapp');

                    if ($dispatch['mode'] === 'simulated') {
                        $_SESSION['last_simulated_otp'] = $otpCode;
                        Flasher::setFlash('Verifikasi 2FA Diperlukan', "Kode OTP simulasi telah dibuat: [{$otpCode}]. Masukkan kode untuk menyelesaikan login.", 'info');
                    } else {
                        Flasher::setFlash('Verifikasi OTP Terkirim', "Kode verifikasi telah dikirim ke nomor Anda.", 'success');
                    }

                    header('Location: ' . BASE_URL . '/index.php?url=otp');
                    exit;
                }

                // Normal Login (preserves existing authentication flow)
                $_SESSION['user_id']       = $user['id'];
                $_SESSION['user_name']     = $user['name'] ?? $user['full_name'];
                $_SESSION['user_username'] = $user['username'];
                $_SESSION['user_email']    = $user['email'] ?? '';
                $_SESSION['user_role']     = $user['role'];
                $_SESSION['user_phone']    = $user['phone'] ?? '';
                $_SESSION['otp_verified']  = false;

                $roleTitle = ucfirst(str_replace('_', ' ', $user['role']));
                Flasher::setFlash('Selamat datang, ' . $_SESSION['user_name'] . '!', 'Berhasil login sebagai ' . $roleTitle, 'success');
                
                header('Location: ' . BASE_URL . '/index.php?url=dashboard');
                exit;
            } else {
                Flasher::setFlash('Kombinasi Username/Email dan Password', 'salah. Silakan periksa kembali!', 'danger');
                header('Location: ' . BASE_URL . '/index.php?url=auth/login');
                exit;
            }
        } else {
            header('Location: ' . BASE_URL . '/index.php?url=auth/login');
            exit;
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        Flasher::setFlash('Sesi berakhir.', 'Anda telah berhasil logout dari sistem.', 'success');
        header('Location: ' . BASE_URL . '/index.php?url=auth/login');
        exit;
    }

    public function forbidden()
    {
        $data = [
            'title' => '403 Forbidden - Akses Ditolak',
            'active_menu' => 'forbidden'
        ];

        $this->view('templates/header', $data);
        $this->view('errors/403', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * AJAX Endpoint: Check account & Request OTP for Forgot Password
     */
    public function forgotRequestOtp()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Metode request tidak diizinkan.']);
            exit;
        }

        $rawInput = file_get_contents('php://input');
        $dataInput = json_decode($rawInput, true) ?: $_POST;
        $identity = trim($dataInput['identity'] ?? '');

        if (empty($identity)) {
            echo json_encode(['success' => false, 'message' => 'Masukkan username atau nomor WhatsApp Anda.']);
            exit;
        }

        $userModel = $this->model('UserModel');
        $user = $userModel->getUserByUsername($identity);

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Akun dengan username atau nomor telepon ' . htmlspecialchars($identity) . ' tidak ditemukan dalam sistem.']);
            exit;
        }

        $phone = trim($user['phone'] ?? '');
        if (empty($phone)) {
            echo json_encode(['success' => false, 'message' => 'Akun tidak memiliki nomor WhatsApp yang terdaftar untuk menerima OTP. Hubungi Operator.']);
            exit;
        }

        $otpModel = $this->model('OtpModel');

        // Check Rate limit (Max per hour)
        if (!$otpModel->checkRateLimit($phone)) {
            echo json_encode([
                'success' => false,
                'message' => 'Batas permintaan OTP terlampaui (' . OTP_RATE_LIMIT_PER_HOUR . 'x per jam). Silakan coba lagi nanti.'
            ]);
            exit;
        }

        // Check Resend Cooldown
        $cooldown = $otpModel->getResendCooldownRemaining($phone);
        if ($cooldown > 0) {
            echo json_encode([
                'success' => false,
                'cooldown' => $cooldown,
                'message' => "Tunggu {$cooldown} detik sebelum meminta ulang kode OTP."
            ]);
            exit;
        }

        // Generate OTP
        $otpCode = $otpModel->generateOtpCode(6);
        $otpModel->createOtp($user['id'], $phone, $otpCode, 'whatsapp');
        $dispatchResult = $otpModel->dispatchOtp($phone, $otpCode, 'whatsapp');

        // Save reset session
        $_SESSION['forgot_pwd_user_id'] = $user['id'];
        $_SESSION['forgot_pwd_phone'] = $phone;
        $_SESSION['forgot_pwd_otp_verified'] = false;

        // Mask phone for security display
        $len = strlen($phone);
        $maskedPhone = ($len > 6) 
            ? substr($phone, 0, 4) . str_repeat('*', max(3, $len - 7)) . substr($phone, -3)
            : $phone;

        echo json_encode([
            'success' => true,
            'message' => $dispatchResult['sent'] 
                ? 'Kode OTP berhasil dikirim ke nomor WhatsApp Anda.' 
                : ($dispatchResult['mode'] === 'simulated' ? 'Mode simulasi aktif (token belum disetel): [' . $otpCode . ']' : $dispatchResult['message']),
            'masked_phone' => $maskedPhone,
            'phone' => $phone,
            'cooldown' => OTP_RESEND_COOLDOWN,
            'mode' => $dispatchResult['mode'],
            'debug_simulated_otp' => ($dispatchResult['mode'] === 'simulated') ? $otpCode : null
        ]);
        exit;
    }

    /**
     * AJAX Endpoint: Verify OTP for Forgot Password
     */
    public function forgotVerifyOtp()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Metode request tidak diizinkan.']);
            exit;
        }

        $rawInput = file_get_contents('php://input');
        $dataInput = json_decode($rawInput, true) ?: $_POST;
        $otpCode = trim($dataInput['otp'] ?? '');
        $phone = trim($dataInput['phone'] ?? ($_SESSION['forgot_pwd_phone'] ?? ''));

        if (empty($phone) || empty($otpCode)) {
            echo json_encode(['success' => false, 'message' => 'Nomor WhatsApp dan 6 digit kode OTP wajib diisi.']);
            exit;
        }

        $otpModel = $this->model('OtpModel');
        $verifyResult = $otpModel->verifyOtp($phone, $otpCode);

        if ($verifyResult['success']) {
            $_SESSION['forgot_pwd_otp_verified'] = true;
            echo json_encode([
                'success' => true,
                'message' => 'Verifikasi OTP berhasil! Silakan masukkan kata sandi baru Anda.'
            ]);
            exit;
        }

        echo json_encode([
            'success' => false,
            'message' => $verifyResult['message'],
            'remaining_attempts' => $verifyResult['remaining_attempts'] ?? 0
        ]);
        exit;
    }

    /**
     * AJAX Endpoint: Reset Password after verified OTP
     */
    public function forgotResetPassword()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Metode request tidak diizinkan.']);
            exit;
        }

        $isVerified = !empty($_SESSION['forgot_pwd_otp_verified']) && $_SESSION['forgot_pwd_otp_verified'] === true;
        $userId = $_SESSION['forgot_pwd_user_id'] ?? null;

        if (!$isVerified || !$userId) {
            echo json_encode(['success' => false, 'message' => 'Sesi verifikasi OTP tidak valid atau telah kedaluwarsa. Silakan ulangi proses.']);
            exit;
        }

        $rawInput = file_get_contents('php://input');
        $dataInput = json_decode($rawInput, true) ?: $_POST;
        $newPassword = trim($dataInput['new_password'] ?? '');
        $confirmPassword = trim($dataInput['confirm_password'] ?? '');

        if (empty($newPassword) || strlen($newPassword) < 6) {
            echo json_encode(['success' => false, 'message' => 'Kata sandi baru minimal harus 6 karakter.']);
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            echo json_encode(['success' => false, 'message' => 'Konfirmasi kata sandi tidak cocok.']);
            exit;
        }

        $userModel = $this->model('UserModel');
        $updated = $userModel->updatePassword($userId, $newPassword);

        if ($updated) {
            // Clear forgot password session
            unset($_SESSION['forgot_pwd_user_id'], $_SESSION['forgot_pwd_phone'], $_SESSION['forgot_pwd_otp_verified']);
            
            Flasher::setFlash('Kata Sandi Diperbarui!', 'Kata sandi berhasil diubah. Silakan masuk menggunakan kata sandi baru Anda.', 'success');
            echo json_encode([
                'success' => true,
                'message' => 'Kata sandi berhasil diperbarui! Mengarahkan ke login...',
                'redirect_url' => BASE_URL . '/index.php?url=auth/login'
            ]);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui kata sandi di database. Silakan coba lagi.']);
            exit;
        }
    }
}
