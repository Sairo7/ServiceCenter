<?php
/**
 * OrderController Class
 * Service Order Submission, Status Tracking, Privacy Enforcement, & Operator Management
 */

class OrderController extends Controller
{
    /**
     * Customer: Order Submission Form
     */
    public function create($serviceId = null)
    {
        Auth::requireRole('customer');

        $serviceModel = $this->model('ServiceModel');
        $services = $serviceModel->getAllServices('', '', true);
        $selectedService = $serviceId ? $serviceModel->getById($serviceId) : null;

        $user = Auth::user();

        $data = [
            'title' => 'Pengajuan Layanan Jasa - TKJ Service Center',
            'active_menu' => 'create_order',
            'services' => $services,
            'selected_service' => $selectedService,
            'user' => $user
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('order/create', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Customer: Process Order POST Submission
     */
    public function store()
    {
        Auth::requireRole('customer');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = Auth::user();
            $orderModel = $this->model('OrderModel');
            $customerId = $orderModel->getCustomerIdByUserId($user['id']);

            $serviceId          = intval($_POST['service_id'] ?? 0);
            $subject            = trim($_POST['subject'] ?? '');
            $problemDescription = trim($_POST['problem_description'] ?? '');
            $location           = trim($_POST['service_address'] ?? '');
            $phone              = trim($_POST['phone'] ?? '');
            $scheduledDate      = trim($_POST['scheduled_date'] ?? '');
            $priority           = trim($_POST['priority'] ?? 'medium');

            if (empty($serviceId) || empty($subject) || empty($problemDescription) || empty($location) || empty($phone) || empty($scheduledDate)) {
                Flasher::setFlash('Formulir Pengajuan', 'wajib diisi dengan lengkap!', 'danger');
                header('Location: ' . BASE_URL . '/index.php?url=order/create/' . $serviceId);
                exit;
            }

            // File Upload Validation (Photo evidence)
            $photoName = null;
            if (isset($_FILES['photo_evidence']) && $_FILES['photo_evidence']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['photo_evidence'];
                $fileName = $file['name'];
                $fileTmp  = $file['tmp_name'];
                $fileSize = $file['size'];
                $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
                $maxSize = 5 * 1024 * 1024; // 5MB

                if (!in_array($fileExt, $allowedExts)) {
                    Flasher::setFlash('Format foto tidak valid!', 'Gunakan ekstensi JPG, PNG, atau WEBP.', 'danger');
                    header('Location: ' . BASE_URL . '/index.php?url=order/create/' . $serviceId);
                    exit;
                }

                if ($fileSize > $maxSize) {
                    Flasher::setFlash('Ukuran foto terlalu besar!', 'Maksimal ukuran foto adalah 5MB.', 'danger');
                    header('Location: ' . BASE_URL . '/index.php?url=order/create/' . $serviceId);
                    exit;
                }

                $uploadDir = __DIR__ . '/../../assets/uploads/orders/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $photoName = 'ORD_IMG_' . date('Ymd_His') . '_' . rand(100, 999) . '.' . $fileExt;
                move_uploaded_file($fileTmp, $uploadDir . $photoName);
            }

            $ticketCode = $orderModel->generateTicketCode();

            $orderData = [
                'order_code'          => $ticketCode,
                'customer_id'         => $customerId,
                'service_id'          => $serviceId,
                'status'              => 'Diajukan',
                'priority'            => $priority,
                'service_address'     => $location,
                'problem_description' => "[Subjek: " . $subject . "] " . $problemDescription,
                'scheduled_date'      => $scheduledDate,
                'photo_evidence'      => $photoName
            ];

            $orderId = $orderModel->createOrder($orderData);

            if ($orderId) {
                $workLogModel = $this->model('WorkLogModel');
                $notifModel   = $this->model('NotificationModel');

                $workLogModel->logStatusChange($orderId, null, 'diagnosis', 'Customer (' . $user['name'] . ') mengajukan permintaan layanan jasa baru dengan Nomor Tiket: ' . $ticketCode);

                // Notifications
                $notifModel->sendNotification($user['id'], 'Pesanan Berhasil Diajukan', 'Pesanan Anda dengan Nomor Tiket ' . $ticketCode . ' telah berhasil diajukan dan sedang menunggu verifikasi Operator.', 'order_status', 'index.php?url=order/detail/' . $orderId);
                $notifModel->sendNotificationToRole('operator', 'Pengajuan Order Baru', 'Customer ' . $user['name'] . ' mengajukan order baru (Tiket: ' . $ticketCode . ').', 'order_status', 'index.php?url=order/assign/' . $orderId);

                Flasher::setFlash('Permintaan Layanan Berhasil Diajukan!', 'Kode Tiket Anda: ' . $ticketCode . ' (Status: Diajukan)', 'success');
                header('Location: ' . BASE_URL . '/index.php?url=order/my_orders');
                exit;
            } else {
                Flasher::setFlash('Gagal mengajukan', 'layanan jasa.', 'danger');
                header('Location: ' . BASE_URL . '/index.php?url=order/create');
                exit;
            }
        } else {
            header('Location: ' . BASE_URL . '/index.php?url=order/create');
            exit;
        }
    }

    /**
     * Customer: View My Orders List & History
     */
    public function my_orders()
    {
        Auth::requireRole('customer');

        $user = Auth::user();
        $orderModel = $this->model('OrderModel');
        $customerId = $orderModel->getCustomerIdByUserId($user['id']);

        $search = trim($_GET['q'] ?? '');
        $status = trim($_GET['status'] ?? '');

        $orders = $orderModel->getOrdersByCustomer($customerId, $search, $status);

        $data = [
            'title' => 'Status & Riwayat Pesanan Saya - TKJ Service Center',
            'active_menu' => 'my_orders',
            'orders' => $orders,
            'search' => $search,
            'selected_status' => $status
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('order/my_orders', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * View Order Detail Page with Privacy Authorization Check & Technician Report Card
     */
    public function detail($id = null)
    {
        Auth::requireLogin();

        if (!$id) {
            header('Location: ' . BASE_URL . '/index.php?url=dashboard');
            exit;
        }

        $orderModel = $this->model('OrderModel');
        $workLogModel = $this->model('WorkLogModel');
        $ratingModel = $this->model('RatingModel');

        $order = $orderModel->getOrderById($id);
        $logs = $workLogModel->getLogsByOrderId($id);
        $report = $workLogModel->getReportByOrderId($id);
        $hasReport = $workLogModel->hasDetailedReport($id);

        $rating = $ratingModel->getRatingByOrderId($id);
        $hasRated = $ratingModel->hasCustomerRatedOrder($id);

        if (!$order) {
            Flasher::setFlash('Tiket Pesanan', 'tidak ditemukan!', 'warning');
            header('Location: ' . BASE_URL . '/index.php?url=dashboard');
            exit;
        }

        // PRIVACY ENFORCEMENT: Customer can ONLY view their own orders!
        if (Auth::role() === 'customer') {
            $user = Auth::user();
            $currentCustomerId = $orderModel->getCustomerIdByUserId($user['id']);
            if ($order['customer_id'] != $currentCustomerId) {
                header('Location: ' . BASE_URL . '/index.php?url=auth/forbidden');
                exit;
            }
        }

        $data = [
            'title' => 'Detail Tiket Order ' . htmlspecialchars($order['order_code']) . ' - TKJ Service Center',
            'active_menu' => 'order_detail',
            'order' => $order,
            'logs' => $logs,
            'report' => $report,
            'has_report' => $hasReport,
            'rating' => $rating,
            'has_rated' => $hasRated
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('order/detail', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Customer: Submit Rating and Feedback for Completed/Closed Order
     */
    public function storeRating($id = null)
    {
        Auth::requireRole('customer');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            $user = Auth::user();
            $orderModel = $this->model('OrderModel');
            $ratingModel = $this->model('RatingModel');
            $workLogModel = $this->model('WorkLogModel');

            $order = $orderModel->getOrderById($id);
            if (!$order) {
                Flasher::setFlash('Tiket Order', 'tidak ditemukan!', 'warning');
                header('Location: ' . BASE_URL . '/index.php?url=order/my_orders');
                exit;
            }

            // Check customer ownership
            $customerId = $orderModel->getCustomerIdByUserId($user['id']);
            if ($order['customer_id'] != $customerId) {
                header('Location: ' . BASE_URL . '/index.php?url=auth/forbidden');
                exit;
            }

            // ORDER STATUS RULE: Order MUST be 'Selesai' or 'Ditutup'
            $allowedStatus = ['Selesai', 'Ditutup'];
            if (!in_array($order['status'], $allowedStatus)) {
                Flasher::setFlash('Rating Tidak Dapat Diberikan!', 'Rating hanya dapat diberikan setelah pengerjaan selesai atau ditutup.', 'warning');
                header('Location: ' . BASE_URL . '/index.php?url=order/detail/' . $id);
                exit;
            }

            // ONE RATING PER ORDER RULE
            if ($ratingModel->hasCustomerRatedOrder($id)) {
                Flasher::setFlash('Rating Sudah Ada!', 'Anda sudah memberikan ulasan untuk pesanan ini sebelumnya.', 'info');
                header('Location: ' . BASE_URL . '/index.php?url=order/detail/' . $id);
                exit;
            }

            $stars = intval($_POST['stars'] ?? 5);
            if ($stars < 1 || $stars > 5) {
                $stars = 5;
            }

            $comment = trim($_POST['comment'] ?? '');
            $serviceFeedback = trim($_POST['service_feedback'] ?? '');

            if (empty($comment)) {
                Flasher::setFlash('Komentar Ulasan', 'wajib diisi!', 'danger');
                header('Location: ' . BASE_URL . '/index.php?url=order/detail/' . $id);
                exit;
            }

            $ratingData = [
                'order_id' => $id,
                'customer_id' => $customerId,
                'technician_id' => $order['technician_id'] ?? null,
                'stars' => $stars,
                'comment' => $comment,
                'service_feedback' => $serviceFeedback
            ];

            if ($ratingModel->createRating($ratingData)) {
                $logMsg = 'Customer (' . $user['name'] . ') memberikan Rating ' . $stars . ' Bintang ⭐ & Feedback Ulasan.';
                $workLogModel->logStatusChange($id, $order['technician_id'] ?? null, 'checkout', $logMsg);

                Flasher::setFlash('Terima Kasih Atas Ulasan Anda!', 'Rating ' . $stars . ' Bintang & feedback berhasil disimpan.', 'success');
            } else {
                Flasher::setFlash('Gagal menyimpan rating', 'silakan coba kembali.', 'danger');
            }
        }

        header('Location: ' . BASE_URL . '/index.php?url=order/detail/' . $id);
        exit;
    }

    /**
     * Operator & Kepala Sekolah: Rating & Feedback Monitoring Analytics View
     */
    public function ratings()
    {
        Auth::requireLogin();
        $role = Auth::role();
        if ($role !== 'operator' && $role !== 'kepala_sekolah') {
            header('Location: ' . BASE_URL . '/index.php?url=auth/forbidden');
            exit;
        }

        $search    = trim($_GET['q'] ?? '');
        $stars     = trim($_GET['stars'] ?? '');
        $techId    = trim($_GET['tech_id'] ?? '');
        $serviceId = trim($_GET['service_id'] ?? '');

        $ratingModel = $this->model('RatingModel');
        $techModel   = $this->model('TechnicianModel');
        $serviceModel= $this->model('ServiceModel');

        $ratings         = $ratingModel->getAllRatings($search, $stars, $techId, $serviceId);
        $overallStats    = $ratingModel->getOverallStats();
        $techStats       = $ratingModel->getRatingStatsByTechnician();
        $serviceStats    = $ratingModel->getRatingStatsByService();
        $allTechnicians  = $techModel->getAllTechnicians();
        $allServices     = $serviceModel->getAllServices('', '', false);

        $data = [
            'title' => 'Monitoring Rating & Feedback Pelanggan - TKJ Service Center',
            'active_menu' => 'ratings',
            'user' => Auth::user(),
            'ratings' => $ratings,
            'overall_stats' => $overallStats,
            'tech_stats' => $techStats,
            'service_stats' => $serviceStats,
            'technicians' => $allTechnicians,
            'services' => $allServices,
            'search' => $search,
            'selected_stars' => $stars,
            'selected_tech' => $techId,
            'selected_service' => $serviceId
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('order/ratings', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Customer: Cancel Order Action
     */
    public function cancel($id = null)
    {
        Auth::requireRole('customer');

        if ($id) {
            $user = Auth::user();
            $orderModel = $this->model('OrderModel');
            $workLogModel = $this->model('WorkLogModel');
            $customerId = $orderModel->getCustomerIdByUserId($user['id']);

            if ($orderModel->cancelOrder($id, $customerId)) {
                $workLogModel->logStatusChange($id, null, 'checkout', 'Customer (' . $user['name'] . ') membatalkan pengajuan tiket order ini.');
                Flasher::setFlash('Pesanan Layanan Jasa', 'berhasil dibatalkan.', 'success');
            } else {
                Flasher::setFlash('Pesanan tidak dapat dibatalkan', 'karena pengerjaan telah berlangsung.', 'warning');
            }
        }

        header('Location: ' . BASE_URL . '/index.php?url=order/my_orders');
        exit;
    }

    /**
     * Operator: Manage All Orders View
     */
    public function manage()
    {
        Auth::requireRole('operator');

        $search    = trim($_GET['q'] ?? '');
        $status    = trim($_GET['status'] ?? '');
        $serviceId = trim($_GET['service_id'] ?? '');

        $orderModel   = $this->model('OrderModel');
        $serviceModel = $this->model('ServiceModel');

        $orders   = $orderModel->getAllOrders($search, $status, $serviceId);
        $services = $serviceModel->getAllServices('', '', false);

        $data = [
            'title' => 'Kelola Order Masuk - Operator TKJ',
            'active_menu' => 'manage_orders',
            'orders' => $orders,
            'services' => $services,
            'search' => $search,
            'selected_status' => $status,
            'selected_service' => $serviceId
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('order/manage', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Operator: Form Assign Technician & Schedule
     */
    public function assign($id = null)
    {
        Auth::requireRole('operator');

        if (!$id) {
            header('Location: ' . BASE_URL . '/index.php?url=order/manage');
            exit;
        }

        $orderModel = $this->model('OrderModel');
        $techModel = $this->model('TechnicianModel');

        $order = $orderModel->getOrderById($id);
        $technicians = $techModel->getAllTechnicians();

        if (!$order) {
            Flasher::setFlash('Tiket Order', 'tidak ditemukan!', 'warning');
            header('Location: ' . BASE_URL . '/index.php?url=order/manage');
            exit;
        }

        $data = [
            'title' => 'Penugasan Teknisi - Tiket ' . htmlspecialchars($order['order_code']),
            'active_menu' => 'manage_orders',
            'order' => $order,
            'technicians' => $technicians
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('order/assign', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Operator: Process Assign Technician with SCHEDULE CONFLICT CHECK
     */
    public function processAssign($id = null)
    {
        Auth::requireRole('operator');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            $user = Auth::user();
            $technicianId = intval($_POST['technician_id'] ?? 0);
            $workDate     = trim($_POST['work_date'] ?? '');
            $startTime    = trim($_POST['start_time'] ?? '09:00');
            $endTime      = trim($_POST['end_time'] ?? '12:00');
            $notes        = trim($_POST['notes'] ?? '');

            if (empty($technicianId) || empty($workDate) || empty($startTime) || empty($endTime)) {
                Flasher::setFlash('Form Penugasan', 'wajib diisi dengan lengkap!', 'danger');
                header('Location: ' . BASE_URL . '/index.php?url=order/assign/' . $id);
                exit;
            }

            $techModel = $this->model('TechnicianModel');
            $orderModel = $this->model('OrderModel');
            $workLogModel = $this->model('WorkLogModel');

            $conflict = $techModel->checkScheduleConflict($technicianId, $workDate, $startTime, $endTime, $id);

            if ($conflict) {
                Flasher::setFlash(
                    'PERINGATAN TABRAKAN JADWAL TEKNISI!', 
                    'Teknisi yang Anda pilih sudah memiliki jadwal pekerjaan lain pada tanggal ' . date('d M Y', strtotime($workDate)) . ' (Jam ' . substr($conflict['start_time'], 0, 5) . ' - ' . substr($conflict['end_time'], 0, 5) . ') untuk Order Tiket: ' . $conflict['order_code'] . '. Silakan pilih teknisi lain atau ubah jam kerja.', 
                    'danger'
                );
                header('Location: ' . BASE_URL . '/index.php?url=order/assign/' . $id);
                exit;
            }

            if ($techModel->assignTechnicianSchedule($id, $technicianId, $workDate, $startTime, $endTime, $notes)) {
                $allTechs = $techModel->getAllTechnicians();
                $techName = 'Teknisi Siswa';
                foreach ($allTechs as $t) {
                    if ($t['id'] == $technicianId) {
                        $techName = $t['name'];
                        break;
                    }
                }

                $logMsg = 'Operator (' . $user['name'] . ') menugaskan ' . $techName . ' pada tanggal ' . date('d M Y', strtotime($workDate)) . ' (' . $startTime . ' - ' . $endTime . '). Instruksi: ' . ($notes ?: 'Tidak ada catatan tambahan.');
                $workLogModel->logStatusChange($id, $technicianId, 'diagnosis', $logMsg);

                // Notifications
                $notifModel = $this->model('NotificationModel');
                $order = $orderModel->getOrderById($id);
                $customerUserId = $notifModel->getUserIdByCustomerId($order['customer_id'] ?? 0);
                $techUserId     = $notifModel->getUserIdByTechnicianId($technicianId);

                if ($customerUserId) {
                    $notifModel->sendNotification($customerUserId, 'Teknisi & Jadwal Ditentukan', 'Teknisi ' . $techName . ' telah ditugaskan untuk order ' . $order['order_code'] . ' pada tanggal ' . date('d M Y', strtotime($workDate)) . ' (' . $startTime . ' - ' . $endTime . ').', 'assignment', 'index.php?url=order/detail/' . $id);
                }
                if ($techUserId) {
                    $notifModel->sendNotification($techUserId, 'Tugas Pekerjaan Lapangan Baru', 'Anda ditugaskan menangani tiket order ' . $order['order_code'] . ' pada tanggal ' . date('d M Y', strtotime($workDate)) . ' (' . $startTime . ' - ' . $endTime . ').', 'assignment', 'index.php?url=technician/detail/' . $id);
                }

                Flasher::setFlash('Penugasan Teknisi Berhasil!', 'Teknisi ' . $techName . ' telah ditugaskan dan tidak ada tabrakan jadwal.', 'success');
                header('Location: ' . BASE_URL . '/index.php?url=order/manage');
                exit;
            }
        }
        header('Location: ' . BASE_URL . '/index.php?url=order/manage');
        exit;
    }

    /**
     * Operator: Verify Order (Approve / Reject)
     */
    public function verify($id = null)
    {
        Auth::requireRole('operator');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            $user = Auth::user();
            $action = trim($_POST['action'] ?? 'approve');
            $notes  = trim($_POST['notes'] ?? '');

            $orderModel = $this->model('OrderModel');
            $workLogModel = $this->model('WorkLogModel');
            $notifModel = $this->model('NotificationModel');

            $order = $orderModel->getOrderById($id);

            if ($action === 'approve') {
                $newStatus = 'Disetujui';
                $orderModel->updateStatus($id, $newStatus);
                $logMsg = 'Operator (' . $user['name'] . ') memverifikasi dan MENYETUJUI pengajuan order. Catatan: ' . ($notes ?: 'Disetujui untuk penugasan teknisi.');
                $workLogModel->logStatusChange($id, null, 'diagnosis', $logMsg);

                // Notification
                $customerUserId = $notifModel->getUserIdByCustomerId($order['customer_id'] ?? 0);
                if ($customerUserId) {
                    $notifModel->sendNotification($customerUserId, 'Pesanan Diverifikasi Operator', 'Order ' . $order['order_code'] . ' telah diverifikasi & Disetujui oleh Operator. Menunggu penugasan teknisi.', 'order_status', 'index.php?url=order/detail/' . $id);
                }

                Flasher::setFlash('Order Berhasil Disetujui!', 'Status order kini "Disetujui". Anda dapat menugaskan teknisi.', 'success');
            } else {
                $newStatus = 'Dibatalkan';
                $orderModel->updateStatus($id, $newStatus);
                $logMsg = 'Operator (' . $user['name'] . ') MENOLAK/MEMBATALKAN pengajuan order. Alasan: ' . ($notes ?: 'Tidak memenuhi kriteria pengajuan.');
                $workLogModel->logStatusChange($id, null, 'checkout', $logMsg);

                // Notification
                $customerUserId = $notifModel->getUserIdByCustomerId($order['customer_id'] ?? 0);
                if ($customerUserId) {
                    $notifModel->sendNotification($customerUserId, 'Pesanan Ditolak/Dibatalkan', 'Order ' . $order['order_code'] . ' dibatalkan oleh Operator. Alasan: ' . ($notes ?: '-'), 'order_status', 'index.php?url=order/detail/' . $id);
                }

                Flasher::setFlash('Order Ditolak', 'Status order diubah menjadi Dibatalkan.', 'warning');
            }
        }
        header('Location: ' . BASE_URL . '/index.php?url=order/detail/' . $id);
        exit;
    }

    /**
     * Operator: Verify or Return Technician Work Report for Revision
     */
    public function verifyReport($id = null)
    {
        Auth::requireRole('operator');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            $user = Auth::user();
            $action = trim($_POST['action'] ?? 'verify'); // 'verify' or 'revise'
            $notes  = trim($_POST['notes'] ?? '');

            $orderModel = $this->model('OrderModel');
            $workLogModel = $this->model('WorkLogModel');
            $notifModel = $this->model('NotificationModel');

            $order = $orderModel->getOrderById($id);
            $customerUserId = $notifModel->getUserIdByCustomerId($order['customer_id'] ?? 0);
            $techUserId     = $notifModel->getUserIdByTechnicianId($order['technician_id'] ?? 0);

            if ($action === 'verify') {
                $orderModel->updateStatus($id, 'Ditutup');
                $logMsg = 'Operator (' . $user['name'] . ') MEMVERIFIKASI LAPORAN KERJA TEKNISI. Pekerjaan dinyatakan rampung, teruji, & tiket resmi Ditutup.';
                $workLogModel->logStatusChange($id, null, 'checkout', $logMsg);

                if ($customerUserId) {
                    $notifModel->sendNotification($customerUserId, 'Pekerjaan Selesai & Tiket Ditutup', 'Order ' . $order['order_code'] . ' telah diverifikasi selesai dan tiket resmi Ditutup. Silakan berikan ulasan & rating.', 'order_status', 'index.php?url=order/detail/' . $id);
                }
                if ($techUserId) {
                    $notifModel->sendNotification($techUserId, 'Laporan Pekerjaan Disetujui', 'Laporan pengerjaan tiket ' . $order['order_code'] . ' telah diverifikasi & disetujui Operator.', 'order_status', 'index.php?url=technician/detail/' . $id);
                }

                Flasher::setFlash('Laporan Teknisi Terverifikasi!', 'Pekerjaan dinyatakan selesai penuh dan tiket resmi Ditutup.', 'success');
            } else {
                // Return report to technician for revision
                $orderModel->updateStatus($id, 'Dalam Pengerjaan');
                $logMsg = 'Operator (' . $user['name'] . ') MENGEMBALIKAN LAPORAN KE TEKNISI UNTUK REVISI. Alasan/Kekurangan: ' . ($notes ?: 'Foto sebelum/sesudah atau rincian tindakan kurang lengkap.');
                $workLogModel->logStatusChange($id, null, 'repair', $logMsg);

                if ($techUserId) {
                    $notifModel->sendNotification($techUserId, 'Revisi Laporan Pekerjaan', 'Operator mengembalikan laporan tiket ' . $order['order_code'] . ' untuk revisi. Catatan: ' . ($notes ?: 'Lengkapi laporan.'), 'assignment', 'index.php?url=technician/report/' . $id);
                }

                Flasher::setFlash('Laporan Dikembalikan ke Teknisi', 'Status order dikembalikan menjadi "Dalam Pengerjaan" untuk perbaikan laporan.', 'warning');
            }
        }
        header('Location: ' . BASE_URL . '/index.php?url=order/detail/' . $id);
        exit;
    }

    /**
     * Operator: Update Order Status
     */
    public function updateStatus($id = null)
    {
        Auth::requireRole('operator');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            $user = Auth::user();
            $newStatus = trim($_POST['status'] ?? '');
            
            $allowedStatuses = [
                'Diajukan', 'Menunggu Verifikasi', 'Disetujui', 'Teknisi Ditugaskan', 
                'Dijadwalkan', 'Dalam Pengerjaan', 'Menunggu Customer/Sparepart', 
                'Selesai', 'Ditutup', 'Dibatalkan'
            ];

            if (in_array($newStatus, $allowedStatuses)) {
                $orderModel = $this->model('OrderModel');
                $workLogModel = $this->model('WorkLogModel');

                $order = $orderModel->getOrderById($id);
                $oldStatus = $order['status'] ?? 'Diajukan';

                if ($orderModel->updateStatus($id, $newStatus)) {
                    $logMsg = 'Operator (' . $user['name'] . ') memperbarui status dari "' . $oldStatus . '" menjadi "' . $newStatus . '".';
                    $workLogModel->logStatusChange($id, null, 'diagnosis', $logMsg);

                    Flasher::setFlash('Status Tiket Order', 'berhasil diperbarui menjadi "' . $newStatus . '"!', 'success');
                }
            }
        }
        header('Location: ' . BASE_URL . '/index.php?url=order/manage');
        exit;
    }
}
