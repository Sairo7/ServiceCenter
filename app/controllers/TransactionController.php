<?php
/**
 * TransactionController Class
 * Handles Billing, Invoicing, Backend Calculations, Manual Payments, & Revenue Reports
 */

class TransactionController extends Controller
{
    /**
     * Invoice & Cost Breakdown View (For Customer & Operator)
     */
    public function invoice($orderId = null)
    {
        Auth::requireLogin();

        if (!$orderId) {
            header('Location: ' . BASE_URL . '/index.php?url=dashboard');
            exit;
        }

        $orderModel = $this->model('OrderModel');
        $trxModel   = $this->model('TransactionModel');

        $order = $orderModel->getOrderById($orderId);

        if (!$order) {
            Flasher::setFlash('Tiket Order', 'tidak ditemukan!', 'warning');
            header('Location: ' . BASE_URL . '/index.php?url=dashboard');
            exit;
        }

        // Privacy Guard: Customer can ONLY view invoice of their own order!
        if (Auth::role() === 'customer') {
            $user = Auth::user();
            $currentCustomerId = $orderModel->getCustomerIdByUserId($user['id']);
            if ($order['customer_id'] != $currentCustomerId) {
                header('Location: ' . BASE_URL . '/index.php?url=auth/forbidden');
                exit;
            }
        }

        $trx = $trxModel->getTransactionByOrderId($orderId);

        $data = [
            'title' => 'Rincian Tagihan & Invoice - Tiket ' . htmlspecialchars($order['order_code']),
            'active_menu' => 'invoice',
            'order' => $order,
            'trx' => $trx
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('transaction/invoice', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Operator Action: Save Invoice Breakdown with SECURE BACKEND CALCULATION
     */
    public function saveInvoice($orderId = null)
    {
        Auth::requireRole('operator');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $orderId) {
            $serviceFee    = floatval($_POST['total_service_fee'] ?? 0);
            $sparepartFee  = floatval($_POST['total_sparepart_fee'] ?? 0);
            $additionalFee = floatval($_POST['tax_amount'] ?? 0); // Additional fee
            $discount      = floatval($_POST['discount'] ?? 0);

            $trxModel   = $this->model('TransactionModel');
            $workLogModel = $this->model('WorkLogModel');
            $user       = Auth::user();

            // SECURE BACKEND FORMULA EXECUTION
            if ($trxModel->calculateAndSaveTransaction($orderId, $serviceFee, $sparepartFee, $additionalFee, $discount)) {
                $grandTotal = max(0, ($serviceFee + $sparepartFee + $additionalFee) - $discount);
                
                $logMsg = 'Operator (' . $user['name'] . ') menerbitkan Rincian Tagihan Invoice: Jasa Rp ' . number_format($serviceFee, 0, ',', '.') . ' + Sparepart Rp ' . number_format($sparepartFee, 0, ',', '.') . ' + Biaya Tambahan Rp ' . number_format($additionalFee, 0, ',', '.') . ' - Diskon Rp ' . number_format($discount, 0, ',', '.') . ' = Total Rp ' . number_format($grandTotal, 0, ',', '.');
                $workLogModel->logStatusChange($orderId, null, 'checkout', $logMsg);

                // Notifications
                $orderModel = $this->model('OrderModel');
                $notifModel = $this->model('NotificationModel');
                $order      = $orderModel->getOrderById($orderId);
                $customerUserId = $notifModel->getUserIdByCustomerId($order['customer_id'] ?? 0);

                if ($customerUserId) {
                    $notifModel->sendNotification($customerUserId, 'Tagihan Invoice Diterbitkan', 'Rincian tagihan invoice untuk tiket ' . $order['order_code'] . ' sebesar Rp ' . number_format($grandTotal, 0, ',', '.') . ' telah diterbitkan.', 'payment', 'index.php?url=transaction/invoice/' . $orderId);
                }

                Flasher::setFlash('Rincian Tagihan Berhasil Diterbitkan!', 'Total tagihan Rp ' . number_format($grandTotal, 0, ',', '.') . ' dihitung secara aman di backend.', 'success');
            } else {
                Flasher::setFlash('Gagal menerbitkan', 'tagihan transaksi.', 'danger');
            }
        }
        header('Location: ' . BASE_URL . '/index.php?url=transaction/invoice/' . $orderId);
        exit;
    }

    /**
     * Customer Action: Submit Manual Payment Record MVP (Status -> Menunggu Verifikasi)
     */
    public function pay($orderId = null)
    {
        Auth::requireRole('customer');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $orderId) {
            $user = Auth::user();
            $amount        = floatval($_POST['amount'] ?? 0);
            $paymentMethod = trim($_POST['payment_method'] ?? 'transfer_bank');

            if ($amount <= 0) {
                Flasher::setFlash('Nominal Pembayaran', 'wajib lebih besar dari 0!', 'danger');
                header('Location: ' . BASE_URL . '/index.php?url=transaction/invoice/' . $orderId);
                exit;
            }

            // Optional Proof Image Upload
            $proofName = null;
            if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['proof_image'];
                $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                
                if (in_array($ext, $allowed) && $file['size'] <= 5 * 1024 * 1024) {
                    $uploadDir = __DIR__ . '/../../assets/uploads/orders/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    $proofName = 'PROOF_' . date('Ymd_His') . '_' . rand(100, 999) . '.' . $ext;
                    move_uploaded_file($file['tmp_name'], $uploadDir . $proofName);
                }
            }

            $trxModel = $this->model('TransactionModel');
            $workLogModel = $this->model('WorkLogModel');

            if ($trxModel->recordPayment($orderId, $amount, $paymentMethod, $proofName)) {
                $logMsg = 'Customer (' . $user['name'] . ') mencatat konfirmasi pembayaran manual sebesar Rp ' . number_format($amount, 0, ',', '.') . ' via ' . strtoupper(str_replace('_', ' ', $paymentMethod)) . '. (Status: Menunggu Verifikasi)';
                $workLogModel->logStatusChange($orderId, null, 'checkout', $logMsg);

                // Notifications
                $orderModel = $this->model('OrderModel');
                $notifModel = $this->model('NotificationModel');
                $order      = $orderModel->getOrderById($orderId);

                $notifModel->sendNotificationToRole('operator', 'Pembayaran Menunggu Verifikasi', 'Customer (' . $user['name'] . ') mengunggah konfirmasi pembayaran sebesar Rp ' . number_format($amount, 0, ',', '.') . ' untuk order ' . ($order['order_code'] ?? '') . '.', 'payment', 'index.php?url=transaction/invoice/' . $orderId);

                Flasher::setFlash('Konfirmasi Pembayaran Terkirim!', 'Pembayaran Anda sebesar Rp ' . number_format($amount, 0, ',', '.') . ' sedang diverifikasi oleh Operator.', 'success');
            }
        }
        header('Location: ' . BASE_URL . '/index.php?url=transaction/invoice/' . $orderId);
        exit;
    }

    /**
     * Operator Action: Verify Payment (Status -> Lunas)
     */
    public function verifyPayment($orderId = null)
    {
        Auth::requireRole('operator');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $orderId) {
            $user = Auth::user();
            $status = trim($_POST['status'] ?? 'Lunas'); // 'Lunas' or 'Belum Dibayar' or 'Dibatalkan'

            $trxModel = $this->model('TransactionModel');
            $workLogModel = $this->model('WorkLogModel');

            if ($trxModel->verifyPayment($orderId, $status, $user['id'])) {
                $logMsg = 'Operator (' . $user['name'] . ') memverifikasi status pembayaran transaksi menjadi "' . $status . '".';
                $workLogModel->logStatusChange($orderId, null, 'checkout', $logMsg);

                // Notifications
                $orderModel = $this->model('OrderModel');
                $notifModel = $this->model('NotificationModel');
                $order      = $orderModel->getOrderById($orderId);
                $customerUserId = $notifModel->getUserIdByCustomerId($order['customer_id'] ?? 0);

                if ($customerUserId) {
                    $notifModel->sendNotification($customerUserId, 'Status Pembayaran Diverifikasi', 'Pembayaran transaksi untuk tiket ' . $order['order_code'] . ' telah diverifikasi Operator menjadi "' . $status . '".', 'payment', 'index.php?url=transaction/invoice/' . $orderId);
                }

                Flasher::setFlash('Verifikasi Pembayaran Berhasil!', 'Status pembayaran kini bernilai "' . $status . '".', 'success');
            }
        }
        header('Location: ' . BASE_URL . '/index.php?url=transaction/invoice/' . $orderId);
        exit;
    }

    /**
     * Revenue Report View (For Operator & Kepala Sekolah)
     */
    public function report()
    {
        Auth::requireRole(['operator', 'kepala_sekolah']);

        $trxModel = $this->model('TransactionModel');
        $reports  = $trxModel->getRevenueReport();

        // Calculate total stats
        $totalRevenue = 0;
        $paidCount = 0;
        foreach ($reports as $r) {
            if (($r['payment_status'] ?? '') === 'Lunas') {
                $totalRevenue += floatval($r['grand_total'] ?? 0);
                $paidCount++;
            }
        }

        $data = [
            'title' => 'Laporan Pendapatan Unit Bisnis TKJ (TEFA)',
            'active_menu' => 'revenue_report',
            'reports' => $reports,
            'total_revenue' => $totalRevenue,
            'paid_count' => $paidCount
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('transaction/report', $data);
        $this->view('templates/footer', $data);
    }
}
