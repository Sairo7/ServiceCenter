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
}
