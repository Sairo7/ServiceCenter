<?php
/**
 * ProfileController Class
 * User Profile Management
 */

class ProfileController extends Controller
{
    public function index()
    {
        Auth::requireLogin();
        $user = Auth::user();

        $userModel = $this->model('UserModel');
        $fullUser = $userModel->getUserById($user['id']) ?? $userModel->getUserByUsername($user['username']);

        $data = [
            'title' => 'Profil Saya - TKJ Service Center',
            'active_menu' => 'profile',
            'user' => $fullUser ?? $user
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('profile/index', $data);
        $this->view('templates/footer', $data);
    }

    public function update()
    {
        Auth::requireLogin();
        $currentUser = Auth::user();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php?url=profile');
            exit;
        }

        $fullName = trim($_POST['full_name'] ?? ($_POST['name'] ?? ''));
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        // 1. Validasi Input tidak boleh kosong
        if (empty($fullName) || empty($email) || empty($phone)) {
            Flasher::setFlash('Gagal memperbarui profil!', 'Nama lengkap, alamat email, dan nomor telepon/WhatsApp wajib diisi.', 'danger');
            header('Location: ' . BASE_URL . '/index.php?url=profile');
            exit;
        }

        $userModel = $this->model('UserModel');
        $currentRecord = $userModel->getUserById($currentUser['id']) ?? $userModel->getUserByUsername($currentUser['username']);

        // 2. Validasi Username (jika diubah)
        $targetUsername = $currentRecord['username'] ?? $currentUser['username'];
        if (!empty($username) && strtolower($username) !== strtolower($targetUsername)) {
            // Validasi karakter username (alfanumerik, underscore, titik, minus, panjang 3-30 karakter)
            if (!preg_match('/^[a-zA-Z0-9_.-]{3,30}$/', $username)) {
                Flasher::setFlash('Username tidak valid!', 'Username hanya boleh terdiri dari 3-30 karakter huruf, angka, underscore, atau titik.', 'danger');
                header('Location: ' . BASE_URL . '/index.php?url=profile');
                exit;
            }
            if ($userModel->isUsernameTakenByOther($username, $currentUser['id'])) {
                Flasher::setFlash('Username sudah digunakan!', 'Username tersebut telah digunakan oleh pengguna lain. Silakan pilih username lain.', 'danger');
                header('Location: ' . BASE_URL . '/index.php?url=profile');
                exit;
            }
            $targetUsername = $username;
        }

        // 3. Validasi Format Email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flasher::setFlash('Format email tidak valid!', 'Harap masukkan alamat email yang benar (contoh: nama@domain.com).', 'danger');
            header('Location: ' . BASE_URL . '/index.php?url=profile');
            exit;
        }

        // 4. Validasi Format Nomor WhatsApp / Telepon Indonesia
        // Mendukung: 08..., 628..., +628... dengan panjang 9-16 digit angka
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (!preg_match('/^(\+62|62|0)8[1-9][0-9]{6,12}$/', $phone) && !preg_match('/^(08|628)[0-9]{7,13}$/', $cleanPhone)) {
            Flasher::setFlash('Format nomor WhatsApp tidak valid!', 'Nomor telepon/WhatsApp harus berupa nomor seluler aktif Indonesia (contoh: 081234567890 atau 6281234567890).', 'danger');
            header('Location: ' . BASE_URL . '/index.php?url=profile');
            exit;
        }

        // 5. Cek Keunikan Email (tidak boleh digunakan oleh akun lain)
        if ($userModel->isEmailTakenByOther($email, $currentUser['id'])) {
            Flasher::setFlash('Email sudah terdaftar!', 'Alamat email ini sudah digunakan oleh akun pengguna lain.', 'danger');
            header('Location: ' . BASE_URL . '/index.php?url=profile');
            exit;
        }

        $emailChanged = (strtolower($email) !== strtolower($currentRecord['email'] ?? ''));
        $phoneChanged = ($cleanPhone !== preg_replace('/[^0-9]/', '', $currentRecord['phone'] ?? ''));

        // 6. Simpan perubahan profil lengkap ke database
        $saved = $userModel->updateProfile($currentUser['id'], $fullName, $email, $phone, $targetUsername);

        if ($saved) {
            // Update data session user
            $_SESSION['user_name'] = $fullName;
            $_SESSION['user_username'] = $targetUsername;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_phone'] = $phone;

            // 7. Tandai kontak yang berubah sebagai belum terverifikasi jika nomor/email diganti
            if ($phoneChanged || $emailChanged) {
                $_SESSION['otp_verified'] = false;
                // Bersihkan sesi aktif OTP sebelumnya agar meminta OTP baru untuk nomor baru
                unset($_SESSION['otp_active_identifier']);
            }

            $infoMsg = 'Profil pengguna berhasil diperbarui.';
            if ($phoneChanged) {
                $infoMsg .= ' Nomor WhatsApp Anda berubah, silakan lakukan verifikasi OTP.';
            }

            Flasher::setFlash('Profil Berhasil Diperbarui!', $infoMsg, 'success');
        } else {
            Flasher::setFlash('Gagal memperbarui profil!', 'Terjadi kendala saat menyimpan data ke database.', 'danger');
        }

        header('Location: ' . BASE_URL . '/index.php?url=profile');
        exit;
    }
}
