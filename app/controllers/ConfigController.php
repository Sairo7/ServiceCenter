<?php
/**
 * ConfigController Class
 * Operator & Admin System Configuration Management
 */

class ConfigController extends Controller
{
    /**
     * Display System Configuration Page
     */
    public function index()
    {
        Auth::requireRole('operator');

        $configModel = $this->model('ConfigModel');
        $configs = $configModel->getAllConfigs();

        $data = [
            'title'       => 'Konfigurasi Sistem - Operator TKJ',
            'active_menu' => 'system_config',
            'configs'     => $configs
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('config/index', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Process Save/Update Configurations (Upsert)
     */
    public function update()
    {
        Auth::requireRole('operator');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php?url=config');
            exit;
        }

        $serviceName        = trim($_POST['service_name'] ?? '');
        $gatewayProvider    = trim($_POST['gateway_provider'] ?? '');
        $apiToken           = trim($_POST['api_token'] ?? '');
        $defaultCountryCode = trim($_POST['default_country_code'] ?? '');

        // Validasi input wajib
        if (empty($serviceName) || empty($gatewayProvider) || empty($apiToken) || empty($defaultCountryCode)) {
            Flasher::setFlash('Semua kolom konfigurasi', 'wajib diisi dengan lengkap!', 'danger');
            header('Location: ' . BASE_URL . '/index.php?url=config');
            exit;
        }

        $configModel = $this->model('ConfigModel');

        $saved1 = $configModel->set('service_name', $serviceName, 'Nama Layanan Aplikasi');
        $saved2 = $configModel->set('gateway_provider', $gatewayProvider, 'Penyedia Layanan Gateway WhatsApp');
        $saved3 = $configModel->set('api_token', $apiToken, 'API Token Gateway');
        $saved4 = $configModel->set('default_country_code', $defaultCountryCode, 'Kode Negara Default Pengiriman Pesan');

        if ($saved1 && $saved2 && $saved3 && $saved4) {
            Flasher::setFlash('Pengaturan sistem', 'berhasil disimpan dan diperbarui!', 'success');
        } else {
            Flasher::setFlash('Gagal menyimpan', 'beberapa pengaturan sistem ke database.', 'danger');
        }

        header('Location: ' . BASE_URL . '/index.php?url=config');
        exit;
    }
}

