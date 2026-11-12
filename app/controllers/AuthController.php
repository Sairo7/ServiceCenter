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
}
