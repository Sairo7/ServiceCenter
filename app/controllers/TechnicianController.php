<?php
/**
 * TechnicianController Class
 * Technician Field Workspace, Job Acceptance, Progress Updates, Work Reports, & Ownership Guard
 */

class TechnicianController extends Controller
{
    /**
     * SECURITY GUARD: Ensures technician can ONLY manage jobs explicitly assigned to them
     */
    private function checkTechnicianOwnership($order)
    {
        $user = Auth::user();
        $techModel = $this->model('TechnicianModel');
        $currentTech = $techModel->getTechnicianByUserId($user['id']);

        if (!$order || !isset($order['technician_id']) || $order['technician_id'] != $currentTech['id']) {
            // Ownership mismatch -> HTTP 403 Forbidden!
            header('Location: ' . BASE_URL . '/index.php?url=auth/forbidden');
            exit;
        }
    }

    /**
     * Technician Jobs List View
     */
    public function jobs()
    {
        Auth::requireRole('teknisi');

        $user = Auth::user();
        $techModel = $this->model('TechnicianModel');
        $jobs = $techModel->getAssignedJobsByTechnician($user['id']);

        $data = [
            'title' => 'Daftar Pekerjaan Lapangan Saya - Teknisi TKJ',
            'active_menu' => 'tech_jobs',
            'user' => $user,
            'jobs' => $jobs
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('technician/jobs', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Technician Job Detail View
     */
    public function detail($id = null)
    {
        Auth::requireRole('teknisi');

        if (!$id) {
            header('Location: ' . BASE_URL . '/index.php?url=technician/jobs');
            exit;
        }

        $orderModel = $this->model('OrderModel');
        $workLogModel = $this->model('WorkLogModel');

        $order = $orderModel->getOrderById($id);
        
        // Ownership Authorization Guard Check
        $this->checkTechnicianOwnership($order);

        $logs = $workLogModel->getLogsByOrderId($id);
        $report = $workLogModel->getReportByOrderId($id);
        $hasReport = $workLogModel->hasDetailedReport($id);

        $data = [
            'title' => 'Detail Tugas ' . htmlspecialchars($order['order_code']) . ' - Teknisi TKJ',
            'active_menu' => 'tech_jobs',
            'order' => $order,
            'logs' => $logs,
            'report' => $report,
            'has_report' => $hasReport
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('technician/detail', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Technician Action: Accept Job (Change status to "Dijadwalkan")
     */
    public function acceptJob($id = null)
    {
        Auth::requireRole('teknisi');

        if ($id) {
            $user = Auth::user();
            $orderModel = $this->model('OrderModel');
            $workLogModel = $this->model('WorkLogModel');
            $techModel = $this->model('TechnicianModel');

            $order = $orderModel->getOrderById($id);
            $this->checkTechnicianOwnership($order);

            $tech = $techModel->getTechnicianByUserId($user['id']);
            $newStatus = 'Dijadwalkan';

            if ($orderModel->updateStatus($id, $newStatus)) {
                $description = 'Teknisi (' . $user['name'] . ') menerima tugas lapangan dan mengonfirmasi kesiapan jadwal.';
                $workLogModel->logStatusChange($id, $tech['id'] ?? 1, 'checkin', $description);

                Flasher::setFlash('Tugas Berhasil Diterima!', 'Status pekerjaan kini dalam tahap Dijadwalkan.', 'success');
            }
        }

        header('Location: ' . BASE_URL . '/index.php?url=technician/detail/' . $id);
        exit;
    }

    /**
     * Technician Action: Update Job Status with MANDATORY REPORT RULE for "Selesai"
     */
    public function updateStatus($id = null)
    {
        Auth::requireRole('teknisi');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            $user = Auth::user();
            $newStatus = trim($_POST['status'] ?? '');
            $notes = trim($_POST['notes'] ?? '');

            $orderModel = $this->model('OrderModel');
            $workLogModel = $this->model('WorkLogModel');
            $techModel = $this->model('TechnicianModel');

            $order = $orderModel->getOrderById($id);
            $this->checkTechnicianOwnership($order);

            $tech = $techModel->getTechnicianByUserId($user['id']);
            $oldStatus = $order['status'] ?? 'Dijadwalkan';

            // MANDATORY RULE: If status is set to "Selesai", detailed report MUST be filled!
            if ($newStatus === 'Selesai' && !$workLogModel->hasDetailedReport($id)) {
                Flasher::setFlash(
                    'Laporan Pekerjaan Belum Diisi!', 
                    'Untuk mengubah status menjadi "Selesai", Anda WAJIB mengisi Laporan Pekerjaan Lapangan (Diagnosa, Tindakan, Hasil, & Foto Sebelum/Sesudah) terlebih dahulu.', 
                    'warning'
                );
                header('Location: ' . BASE_URL . '/index.php?url=technician/report/' . $id);
                exit;
            }

            $allowedStatuses = ['Teknisi Ditugaskan', 'Dijadwalkan', 'Dalam Pengerjaan', 'Menunggu Customer/Sparepart', 'Selesai'];

            if (in_array($newStatus, $allowedStatuses)) {
                if ($orderModel->updateStatus($id, $newStatus)) {
                    $logType = $newStatus === 'Selesai' ? 'checkout' : ($newStatus === 'Dalam Pengerjaan' ? 'repair' : 'part_replacement');
                    $description = 'Status diubah oleh Teknisi (' . $user['name'] . ') dari "' . $oldStatus . '" menjadi "' . $newStatus . '". Catatan: ' . ($notes ?: 'Tidak ada catatan tambahan.');

                    $workLogModel->logStatusChange($id, $tech['id'] ?? 1, $logType, $description);

                    Flasher::setFlash('Status Pekerjaan Berhasil Diperbarui!', 'Status kini bernilai "' . $newStatus . '".', 'success');
                }
            }
        }

        header('Location: ' . BASE_URL . '/index.php?url=technician/detail/' . $id);
        exit;
    }

    /**
     * Technician View & Fill Work Log Report Form
     */
    public function report($id = null)
    {
        Auth::requireRole('teknisi');

        if (!$id) {
            header('Location: ' . BASE_URL . '/index.php?url=technician/jobs');
            exit;
        }

        $orderModel = $this->model('OrderModel');
        $workLogModel = $this->model('WorkLogModel');

        $order = $orderModel->getOrderById($id);
        $this->checkTechnicianOwnership($order);

        $existingReport = $workLogModel->getReportByOrderId($id);

        $data = [
            'title' => 'Laporan Pekerjaan Lapangan - Tiket ' . htmlspecialchars($order['order_code']),
            'active_menu' => 'tech_jobs',
            'order' => $order,
            'report' => $existingReport
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('technician/report', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Technician POST Process: Store Detailed Work Log Report
     */
    public function storeReport($id = null)
    {
        Auth::requireRole('teknisi');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            $user = Auth::user();
            $orderModel = $this->model('OrderModel');
            $workLogModel = $this->model('WorkLogModel');
            $techModel = $this->model('TechnicianModel');

            $order = $orderModel->getOrderById($id);
            $this->checkTechnicianOwnership($order);

            $tech = $techModel->getTechnicianByUserId($user['id']);

            $diagnosis       = trim($_POST['diagnosis'] ?? '');
            $inspection      = trim($_POST['inspection'] ?? '');
            $actionTaken     = trim($_POST['action_taken'] ?? '');
            $sparepartsUsed  = trim($_POST['spareparts_used'] ?? 'Tidak menggunakan sparepart.');
            $executionResult = trim($_POST['execution_result'] ?? '');
            $notes           = trim($_POST['notes'] ?? '');
            $markCompleted   = isset($_POST['mark_completed']) ? true : false;

            if (empty($diagnosis) || empty($inspection) || empty($actionTaken) || empty($executionResult)) {
                Flasher::setFlash('Form Laporan Pekerjaan', 'wajib diisi lengkap (Diagnosa, Pemeriksaan, Tindakan, & Hasil)!', 'danger');
                header('Location: ' . BASE_URL . '/index.php?url=technician/report/' . $id);
                exit;
            }

            // File Upload Validations for Before & After Photos
            $uploadDir = __DIR__ . '/../../assets/uploads/orders/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            $photoBefore = null;
            if (isset($_FILES['photo_before']) && $_FILES['photo_before']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['photo_before']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, $allowedExts) && $_FILES['photo_before']['size'] <= $maxSize) {
                    $photoBefore = 'BEFORE_' . date('Ymd_His') . '_' . rand(100, 999) . '.' . $ext;
                    move_uploaded_file($_FILES['photo_before']['tmp_name'], $uploadDir . $photoBefore);
                }
            }

            $photoAfter = null;
            if (isset($_FILES['photo_after']) && $_FILES['photo_after']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['photo_after']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, $allowedExts) && $_FILES['photo_after']['size'] <= $maxSize) {
                    $photoAfter = 'AFTER_' . date('Ymd_His') . '_' . rand(100, 999) . '.' . $ext;
                    move_uploaded_file($_FILES['photo_after']['tmp_name'], $uploadDir . $photoAfter);
                }
            }

            $recommendation = trim($_POST['recommendation'] ?? '');
            $aiReportFull   = trim($_POST['ai_report_full'] ?? '');

            $reportData = [
                'diagnosis'        => $diagnosis,
                'inspection'       => $inspection,
                'action_taken'     => $actionTaken,
                'spareparts_used'  => $sparepartsUsed,
                'execution_result' => $executionResult,
                'notes'            => $notes,
                'recommendation'   => $recommendation,
                'ai_report_full'   => $aiReportFull,
                'photo_before'     => $photoBefore,
                'photo_after'      => $photoAfter
            ];

            if ($workLogModel->saveDetailedReport($id, $tech['id'] ?? 1, $reportData)) {
                $notifModel = $this->model('NotificationModel');
                $customerUserId = $notifModel->getUserIdByCustomerId($order['customer_id'] ?? 0);

                if ($markCompleted) {
                    $orderModel->updateStatus($id, 'Selesai');
                    $workLogModel->logStatusChange($id, $tech['id'] ?? 1, 'checkout', 'Teknisi menyelesai pengerjaan dan mengirimkan laporan akhir.');

                    // Notifications
                    if ($customerUserId) {
                        $notifModel->sendNotification($customerUserId, 'Pekerjaan Lapangan Selesai', 'Teknisi telah menyelesaikan pengerjaan tiket ' . $order['order_code'] . '. Silakan tinjau rincian & berikan ulasan.', 'order_status', 'index.php?url=order/detail/' . $id);
                    }
                    $notifModel->sendNotificationToRole('operator', 'Laporan Pekerjaan Selesai Terisi', 'Teknisi (' . $user['name'] . ') menyelesai order ' . $order['order_code'] . ' dan mengirimkan Laporan Pekerjaan. Menunggu verifikasi Operator.', 'order_status', 'index.php?url=order/detail/' . $id);

                    Flasher::setFlash('Laporan Disimpan & Pekerjaan Selesai!', 'Status order kini menjadi "Selesai". Menunggu verifikasi operator.', 'success');
                } else {
                    if ($customerUserId) {
                        $notifModel->sendNotification($customerUserId, 'Draft Laporan Ditambahkan', 'Teknisi mengunggah/memperbarui draft laporan pengerjaan tiket ' . $order['order_code'] . '.', 'order_status', 'index.php?url=order/detail/' . $id);
                    }

                    Flasher::setFlash('Draft Laporan Pekerjaan Berhasil Disimpan!', 'Rincian laporan dapat diedit kembali jika diperlukan.', 'success');
                }
            }
        }

        header('Location: ' . BASE_URL . '/index.php?url=technician/detail/' . $id);
        exit;
    }

    /**
     * Technician Work Schedule Agenda View
     */
    public function schedule()
    {
        Auth::requireRole('teknisi');

        $user = Auth::user();
        $techModel = $this->model('TechnicianModel');
        $schedules = $techModel->getSchedulesByTechnician($user['id']);

        $data = [
            'title' => 'Agenda Jadwal Kerja Saya - Teknisi TKJ',
            'active_menu' => 'tech_schedule',
            'user' => $user,
            'schedules' => $schedules
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('technician/schedule', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * AI Technical Troubleshooting Assistant Page for Technicians
     */
    public function troubleshoot($orderId = null)
    {
        Auth::requireRole('teknisi');

        $user = Auth::user();
        $order = null;

        if ($orderId) {
            $orderModel = $this->model('OrderModel');
            $order = $orderModel->getOrderById($orderId);
        }

        $data = [
            'title' => 'AI Troubleshooting Assistant - Teknisi TKJ',
            'active_menu' => 'tech_ai_assistant',
            'user' => $user,
            'order' => $order
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('technician/troubleshoot', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * SECURE BACKEND PROXY AJAX POST: Process Technician AI Troubleshooting Query
     */
    public function ai_troubleshoot_api()
    {
        header('Content-Type: application/json');

        if (!Auth::check() || Auth::role() !== 'teknisi') {
            echo json_encode(['success' => false, 'error' => 'Akses khusus untuk Teknisi Siswa.']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rawInput = file_get_contents('php://input');
            $dataInput = json_decode($rawInput, true) ?: $_POST;

            $deviceType      = trim($dataInput['device_type'] ?? '');
            $symptom         = trim($dataInput['symptom'] ?? '');
            $deviceCondition = trim($dataInput['device_condition'] ?? '');
            $initialCheck    = trim($dataInput['initial_check'] ?? '');

            if (empty($deviceType) || empty($symptom)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Jenis Perangkat dan Gejala Masalah wajib diisi.'
                ]);
                exit;
            }

            $aiModel = $this->model('AiModel');
            $user = Auth::user();

            $result = $aiModel->generateTechnicianTroubleshootResponse($deviceType, $symptom, $deviceCondition, $initialCheck);

            // Log diagnostic action
            $aiModel->saveConversation(
                $user['id'] ?? 1,
                "[DIAGNOSIS TEKNISI] Perangkat: {$deviceType} | Gejala: {$symptom}",
                json_encode($result, JSON_UNESCAPED_UNICODE)
            );

            echo json_encode([
                'success' => true,
                'result' => $result
            ]);
            exit;
        }

        echo json_encode([
            'success' => false,
            'error' => 'Metode HTTP Tidak Sesuai (Gunakan POST).'
        ]);
        exit;
    }

    /**
     * SECURE BACKEND PROXY AJAX POST: AI Work Report Generator for Technicians
     * Transforms technician raw inputs into a formal 7-section professional report.
     */
    public function ai_generate_report_api()
    {
        header('Content-Type: application/json');

        if (!Auth::check() || Auth::role() !== 'teknisi') {
            echo json_encode(['success' => false, 'error' => 'Akses khusus untuk Teknisi Siswa.']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rawInput = file_get_contents('php://input');
            $dataInput = json_decode($rawInput, true) ?: $_POST;

            $orderId = intval($dataInput['order_id'] ?? 0);
            if (!$orderId) {
                echo json_encode(['success' => false, 'error' => 'Order ID tidak valid.']);
                exit;
            }

            $orderModel = $this->model('OrderModel');
            $order = $orderModel->getOrderById($orderId);
            if (!$order) {
                echo json_encode(['success' => false, 'error' => 'Data order tidak ditemukan.']);
                exit;
            }

            // Verify technician ownership
            $user = Auth::user();
            $techModel = $this->model('TechnicianModel');
            $currentTech = $techModel->getTechnicianByUserId($user['id']);
            if (!$order || !isset($order['technician_id']) || $order['technician_id'] != $currentTech['id']) {
                echo json_encode(['success' => false, 'error' => 'Anda tidak memiliki hak akses untuk tiket ini.']);
                exit;
            }

            $inputs = [
                'masalah'   => trim($dataInput['masalah'] ?? ''),
                'diagnosa'  => trim($dataInput['diagnosa'] ?? ''),
                'tindakan'  => trim($dataInput['tindakan'] ?? ''),
                'hasil'     => trim($dataInput['hasil'] ?? ''),
                'sparepart' => trim($dataInput['sparepart'] ?? ''),
                'catatan'   => trim($dataInput['catatan'] ?? '')
            ];

            if (empty($inputs['diagnosa']) && empty($inputs['tindakan']) && empty($inputs['hasil']) && empty($inputs['masalah'])) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Harap isi setidaknya Masalah, Diagnosa, Tindakan, atau Hasil untuk membuat laporan AI.'
                ]);
                exit;
            }

            $aiModel = $this->model('AiModel');
            $result = $aiModel->generateWorkReportResponse($order, $inputs);

            // Log AI generation action
            $aiModel->saveConversation(
                $user['id'] ?? 1,
                "[AI REPORT GENERATOR] Tiket: {$order['order_code']}",
                json_encode($result, JSON_UNESCAPED_UNICODE)
            );

            echo json_encode($result);
            exit;
        }

        echo json_encode(['success' => false, 'error' => 'Method invalid (POST required).']);
        exit;
    }
}

