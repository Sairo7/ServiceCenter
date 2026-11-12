<?php
/**
 * ServiceController Class
 * Service Catalog Management for Customers & Operators
 */

class ServiceController extends Controller
{
    /**
     * Customer / Public Service Catalog View
     */
    public function index()
    {
        $search = trim($_GET['q'] ?? '');
        $category = trim($_GET['cat'] ?? '');

        $serviceModel = $this->model('ServiceModel');
        $services = $serviceModel->getAllServices($search, $category, true);
        $categories = $serviceModel->getCategories();

        $data = [
            'title' => 'Katalog Layanan Jasa Teknis TKJ - TKJ Service Center',
            'active_menu' => 'services',
            'services' => $services,
            'categories' => $categories,
            'search' => $search,
            'selected_category' => $category
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('service/index', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Service Detail View
     */
    public function detail($id = null)
    {
        if (!$id) {
            header('Location: ' . BASE_URL . '/index.php?url=service');
            exit;
        }

        $serviceModel = $this->model('ServiceModel');
        $service = $serviceModel->getById($id);

        if (!$service) {
            Flasher::setFlash('Layanan Jasa', 'tidak ditemukan!', 'warning');
            header('Location: ' . BASE_URL . '/index.php?url=service');
            exit;
        }

        $data = [
            'title' => htmlspecialchars($service['title'] ?? $service['name']) . ' - TKJ Service Center',
            'active_menu' => 'services',
            'service' => $service
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('service/detail', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Action when Customer clicks "Ajukan Layanan" -> Redirects to Order Create Form
     */
    public function apply($id = null)
    {
        header('Location: ' . BASE_URL . '/index.php?url=order/create/' . ($id ?? ''));
        exit;
    }

    /**
     * Operator Management View (RBAC Protected: Operator Only)
     */
    public function manage()
    {
        Auth::requireRole('operator');

        $search = trim($_GET['q'] ?? '');
        $category = trim($_GET['cat'] ?? '');

        $serviceModel = $this->model('ServiceModel');
        $services = $serviceModel->getAllServices($search, $category, false); // Include inactive
        $categories = $serviceModel->getCategories();

        $data = [
            'title' => 'Manajemen Katalog Jasa - Operator TKJ',
            'active_menu' => 'manage_services',
            'services' => $services,
            'categories' => $categories,
            'search' => $search,
            'selected_category' => $category
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('service/manage', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Operator Action: Create Service
     */
    public function create()
    {
        Auth::requireRole('operator');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => trim($_POST['code'] ?? 'SVC-' . time()),
                'name' => trim($_POST['name'] ?? ''),
                'category' => trim($_POST['category'] ?? 'Layanan IT'),
                'description' => trim($_POST['description'] ?? ''),
                'base_price' => floatval($_POST['base_price'] ?? 0),
                'estimated_duration_hours' => intval($_POST['estimated_duration_hours'] ?? 2),
                'icon' => trim($_POST['icon'] ?? 'bi-wrench'),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            if (empty($data['name']) || empty($data['description'])) {
                Flasher::setFlash('Nama Jasa dan Deskripsi', 'wajib diisi!', 'danger');
                header('Location: ' . BASE_URL . '/index.php?url=service/manage');
                exit;
            }

            $serviceModel = $this->model('ServiceModel');
            if ($serviceModel->create($data)) {
                Flasher::setFlash('Layanan Jasa Baru', 'berhasil ditambahkan ke katalog!', 'success');
            } else {
                Flasher::setFlash('Gagal menambahkan', 'layanan jasa baru.', 'danger');
            }
        }
        header('Location: ' . BASE_URL . '/index.php?url=service/manage');
        exit;
    }

    /**
     * Operator Action: Edit Service
     */
    public function edit($id = null)
    {
        Auth::requireRole('operator');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            $data = [
                'code' => trim($_POST['code'] ?? ''),
                'name' => trim($_POST['name'] ?? ''),
                'category' => trim($_POST['category'] ?? 'Layanan IT'),
                'description' => trim($_POST['description'] ?? ''),
                'base_price' => floatval($_POST['base_price'] ?? 0),
                'estimated_duration_hours' => intval($_POST['estimated_duration_hours'] ?? 2),
                'icon' => trim($_POST['icon'] ?? 'bi-wrench'),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            $serviceModel = $this->model('ServiceModel');
            if ($serviceModel->update($id, $data)) {
                Flasher::setFlash('Perubahan Layanan Jasa', 'berhasil diperbarui!', 'success');
            } else {
                Flasher::setFlash('Gagal memperbarui', 'layanan jasa.', 'danger');
            }
        }
        header('Location: ' . BASE_URL . '/index.php?url=service/manage');
        exit;
    }

    /**
     * Operator Action: Toggle Active Status
     */
    public function toggle($id = null)
    {
        Auth::requireRole('operator');

        if ($id) {
            $serviceModel = $this->model('ServiceModel');
            if ($serviceModel->toggleStatus($id)) {
                Flasher::setFlash('Status Layanan Jasa', 'berhasil diperbarui (Aktif/Nonaktif)!', 'success');
            }
        }
        header('Location: ' . BASE_URL . '/index.php?url=service/manage');
        exit;
    }

    /**
     * Operator Action: Delete Service
     */
    public function delete($id = null)
    {
        Auth::requireRole('operator');

        if ($id) {
            $serviceModel = $this->model('ServiceModel');
            if ($serviceModel->delete($id)) {
                Flasher::setFlash('Layanan Jasa', 'berhasil dihapus dari database!', 'success');
            }
        }
        header('Location: ' . BASE_URL . '/index.php?url=service/manage');
        exit;
    }
}
