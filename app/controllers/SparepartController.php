<?php
/**
 * SparepartController Class
 * Operator Hardware & Network Sparepart Inventory Management
 */

class SparepartController extends Controller
{
    /**
     * Operator Sparepart Management View
     */
    public function manage()
    {
        Auth::requireRole('operator');

        $search   = trim($_GET['q'] ?? '');
        $category = trim($_GET['cat'] ?? '');

        $sparepartModel = $this->model('SparepartModel');
        $spareparts = $sparepartModel->getAllSpareparts($search, $category);

        $data = [
            'title' => 'Manajemen Inventaris Sparepart - Operator TKJ',
            'active_menu' => 'manage_spareparts',
            'spareparts' => $spareparts,
            'search' => $search,
            'selected_category' => $category
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('sparepart/manage', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Operator Action: Create Sparepart
     */
    public function create()
    {
        Auth::requireRole('operator');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'part_code'     => trim($_POST['part_code'] ?? 'PRT-' . time()),
                'name'          => trim($_POST['name'] ?? ''),
                'category'      => trim($_POST['category'] ?? 'Komponen IT'),
                'stock_qty'     => intval($_POST['stock_qty'] ?? 0),
                'unit'          => trim($_POST['unit'] ?? 'pcs'),
                'unit_cost'     => floatval($_POST['unit_cost'] ?? 0),
                'selling_price' => floatval($_POST['selling_price'] ?? 0),
                'min_stock'     => intval($_POST['min_stock'] ?? 5)
            ];

            if (empty($data['name']) || empty($data['part_code']) || empty($data['selling_price'])) {
                Flasher::setFlash('Kode, Nama Sparepart, dan Harga Jual', 'wajib diisi!', 'danger');
                header('Location: ' . BASE_URL . '/index.php?url=sparepart/manage');
                exit;
            }

            $sparepartModel = $this->model('SparepartModel');
            if ($sparepartModel->create($data)) {
                Flasher::setFlash('Sparepart Baru Berhasil Ditambahkan!', 'Stok awal: ' . $data['stock_qty'] . ' ' . $data['unit'], 'success');
            } else {
                Flasher::setFlash('Gagal menambahkan', 'sparepart baru.', 'danger');
            }
        }
        header('Location: ' . BASE_URL . '/index.php?url=sparepart/manage');
        exit;
    }

    /**
     * Operator Action: Edit Sparepart
     */
    public function edit($id = null)
    {
        Auth::requireRole('operator');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            $data = [
                'part_code'     => trim($_POST['part_code'] ?? ''),
                'name'          => trim($_POST['name'] ?? ''),
                'category'      => trim($_POST['category'] ?? 'Komponen IT'),
                'stock_qty'     => intval($_POST['stock_qty'] ?? 0),
                'unit'          => trim($_POST['unit'] ?? 'pcs'),
                'unit_cost'     => floatval($_POST['unit_cost'] ?? 0),
                'selling_price' => floatval($_POST['selling_price'] ?? 0),
                'min_stock'     => intval($_POST['min_stock'] ?? 5)
            ];

            $sparepartModel = $this->model('SparepartModel');
            if ($sparepartModel->update($id, $data)) {
                Flasher::setFlash('Data Sparepart Berhasil Diperbarui!', 'Stok: ' . $data['stock_qty'] . ' ' . $data['unit'], 'success');
            } else {
                Flasher::setFlash('Gagal memperbarui', 'data sparepart.', 'danger');
            }
        }
        header('Location: ' . BASE_URL . '/index.php?url=sparepart/manage');
        exit;
    }

    /**
     * Operator Action: Delete Sparepart
     */
    public function delete($id = null)
    {
        Auth::requireRole('operator');

        if ($id) {
            $sparepartModel = $this->model('SparepartModel');
            if ($sparepartModel->delete($id)) {
                Flasher::setFlash('Sparepart', 'berhasil dihapus dari inventaris!', 'success');
            }
        }
        header('Location: ' . BASE_URL . '/index.php?url=sparepart/manage');
        exit;
    }
}
