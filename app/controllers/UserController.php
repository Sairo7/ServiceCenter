<?php
/**
 * UserController Class
 * Operator User Management & Directory Controller
 */

class UserController extends Controller
{
    /**
     * Operator View: User Directory & Account List
     */
    public function index()
    {
        Auth::requireRole('operator');

        $search     = trim($_GET['q'] ?? '');
        $roleFilter = trim($_GET['role'] ?? '');

        $userModel = $this->model('UserModel');
        $users = $userModel->getAllUsersForOperator($search, $roleFilter);
        $totalActive = $userModel->countAllActiveUsers();

        // Calculate count per role from the returned users or overall
        $roleCounts = [
            'all'            => count($users),
            'customer'       => 0,
            'teknisi'        => 0,
            'operator'       => 0,
            'kepala_sekolah' => 0
        ];

        // Overall counts without filter
        $allUsers = $userModel->getAllUsersForOperator('', '');
        foreach ($allUsers as $u) {
            $r = $u['role'] ?? '';
            if (isset($roleCounts[$r])) {
                $roleCounts[$r]++;
            }
        }

        $data = [
            'title'         => 'Total Pengguna & Data Akun - Operator TKJ',
            'active_menu'   => 'total_users',
            'users'         => $users,
            'search'        => $search,
            'selected_role' => $roleFilter,
            'total_active'  => $totalActive,
            'role_counts'   => $roleCounts
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('user/index', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Operator Action: Store New User
     */
    public function store()
    {
        Auth::requireRole('operator');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = $this->model('UserModel');

            $fullName       = trim($_POST['full_name'] ?? '');
            $username       = trim($_POST['username'] ?? '');
            $email          = trim($_POST['email'] ?? '');
            $phone          = trim($_POST['phone'] ?? '');
            $roleId         = intval($_POST['role_id'] ?? 0);
            $password       = trim($_POST['password'] ?? '');
            $specialization = trim($_POST['specialization'] ?? '');

            // Basic validation
            if (empty($fullName) || empty($username) || empty($email) || empty($roleId) || empty($password)) {
                Flasher::setFlash('Gagal menambahkan pengguna.', 'Semua kolom wajib diisi lengkap.', 'danger');
                header('Location: ' . BASE_URL . '/index.php?url=user');
                exit;
            }

            $res = $userModel->createUser([
                'full_name'      => $fullName,
                'username'       => $username,
                'email'          => $email,
                'phone'          => $phone,
                'role_id'        => $roleId,
                'password'       => $password,
                'specialization' => $specialization
            ]);

            if ($res['status']) {
                Flasher::setFlash('Berhasil!', $res['message'], 'success');
            } else {
                Flasher::setFlash('Gagal menambahkan pengguna.', $res['message'], 'danger');
            }

            header('Location: ' . BASE_URL . '/index.php?url=user');
            exit;
        }

        header('Location: ' . BASE_URL . '/index.php?url=user');
        exit;
    }
}
