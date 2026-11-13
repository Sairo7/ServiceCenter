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

        // Check live connection status via OtpModel
        $otpModel = $this->model('OtpModel');
        $connStatus = $otpModel->checkConnection();

        $data = [
            'title'       => 'Konfigurasi Sistem - Operator TKJ',
            'active_menu' => 'system_config',
            'configs'     => $configs,
            'conn_status' => $connStatus
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

        // Sync to .env file if it exists so environment variables stay in sync
        $envFile = __DIR__ . '/../../.env';
        if (file_exists($envFile)) {
            $envContent = file_get_contents($envFile);
            $envContent = preg_replace('/^OTP_PROVIDER=.*/m', 'OTP_PROVIDER=' . strtolower($gatewayProvider), $envContent);
            $envContent = preg_replace('/^OTP_API_TOKEN=.*/m', 'OTP_API_TOKEN=' . $apiToken, $envContent);
            $envContent = preg_replace('/^DEFAULT_COUNTRY_CODE=.*/m', 'DEFAULT_COUNTRY_CODE=' . $defaultCountryCode, $envContent);
            file_put_contents($envFile, $envContent);
        }

        if ($saved1 && $saved2 && $saved3 && $saved4) {
            Flasher::setFlash('Pengaturan sistem', 'berhasil disimpan dan diperbarui!', 'success');
        } else {
            Flasher::setFlash('Gagal menyimpan', 'beberapa pengaturan sistem ke database.', 'danger');
        }

        header('Location: ' . BASE_URL . '/index.php?url=config');
        exit;
    }
}

