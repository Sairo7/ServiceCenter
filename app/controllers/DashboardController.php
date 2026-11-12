<?php
/**
 * DashboardController Class
 * Handles role-based access control and executive analytics routing
 */

class DashboardController extends Controller
{
    /**
     * Default index: Redirects user to their role-specific dashboard
     */
    public function index()
    {
        Auth::requireLogin();

        $role = Auth::role();
        header('Location: ' . BASE_URL . '/index.php?url=dashboard/' . $role);
        exit;
    }

    /**
     * Customer Dashboard
     */
    public function customer()
    {
        Auth::requireRole('customer');

        $user = Auth::user();
        $dashModel = $this->model('DashboardModel');
        $stats = $dashModel->getCustomerStats($user['id']);

        $data = [
            'title' => 'Dashboard Customer - TKJ Service Business Web App',
            'active_menu' => 'dashboard',
            'user' => $user,
            'stats' => $stats
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('dashboard/customer', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Operator Dashboard
     */
    public function operator()
    {
        Auth::requireRole('operator');

        $user = Auth::user();
        $dashModel = $this->model('DashboardModel');
        $stats = $dashModel->getOperatorStats();

        $data = [
            'title' => 'Dashboard Operator - TKJ Service Business Web App',
            'active_menu' => 'dashboard',
            'user' => $user,
            'stats' => $stats
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('dashboard/operator', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Teknisi Siswa Dashboard
     */
    public function teknisi()
    {
        Auth::requireRole('teknisi');

        $user = Auth::user();
        $dashModel = $this->model('DashboardModel');
        $stats = $dashModel->getTechnicianStats($user['id']);

        $data = [
            'title' => 'Dashboard Teknisi Siswa - TKJ Service Business Web App',
            'active_menu' => 'dashboard',
            'user' => $user,
            'stats' => $stats
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('dashboard/teknisi', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Kepala Sekolah Executive Dashboard (Read-Only Monitoring)
     */
    public function kepala_sekolah()
    {
        Auth::requireRole('kepala_sekolah');

        $user = Auth::user();
        $dashModel = $this->model('DashboardModel');
        $stats = $dashModel->getKepalaSekolahStats();

        $data = [
            'title' => 'Dashboard Monitoring Kepala Sekolah - TKJ Service Business Web App',
            'active_menu' => 'dashboard',
            'user' => $user,
            'stats' => $stats
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('dashboard/kepala_sekolah', $data);
        $this->view('templates/footer', $data);
    }
}
