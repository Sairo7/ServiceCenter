<?php
/**
 * ReportController Class
 * Executive & Operational Reporting Module for Kepala Sekolah & Operator
 */

class ReportController extends Controller
{
    /**
     * Main Reporting Hub View
     */
    public function index()
    {
        Auth::requireRole(['kepala_sekolah', 'operator']);

        $role = Auth::role();
        $isReadOnly = ($role === 'kepala_sekolah');

        $activeTab  = trim($_GET['tab'] ?? 'orders');
        $filterType = trim($_GET['filter'] ?? 'this_month');
        $startDate  = trim($_GET['start_date'] ?? '');
        $endDate    = trim($_GET['end_date'] ?? '');

        // Valid tabs array
        $validTabs = ['orders', 'revenue', 'services', 'customers', 'technicians', 'ratings', 'period'];
        if (!in_array($activeTab, $validTabs)) {
            $activeTab = 'orders';
        }

        $reportModel = $this->model('ReportModel');

        // Fetch Tab Specific Dataset
        $reportData = [];
        switch ($activeTab) {
            case 'orders':
                $reportData = $reportModel->getOrderReportData($filterType, $startDate, $endDate);
                $tabTitle = 'Laporan Jumlah Order';
                break;
            case 'revenue':
                $reportData = $reportModel->getRevenueReportData($filterType, $startDate, $endDate);
                $tabTitle = 'Laporan Pendapatan TEFA';
                break;
            case 'services':
                $reportData = $reportModel->getTopServicesReportData($filterType, $startDate, $endDate);
                $tabTitle = 'Laporan Jasa Terlaris';
                break;
            case 'customers':
                $reportData = $reportModel->getCustomerReportData($filterType, $startDate, $endDate);
                $tabTitle = 'Laporan Customer';
                break;
            case 'technicians':
                $reportData = $reportModel->getTechnicianReportData($filterType, $startDate, $endDate);
                $tabTitle = 'Laporan Pekerjaan Teknisi';
                break;
            case 'ratings':
                $reportData = $reportModel->getRatingReportData($filterType, $startDate, $endDate);
                $tabTitle = 'Laporan Rating Layanan';
                break;
            case 'period':
                $reportData = $reportModel->getPeriodReportData($filterType, $startDate, $endDate);
                $tabTitle = 'Laporan Berdasarkan Periode';
                break;
        }

        $data = [
            'title'       => $tabTitle . ' - Executive Business Unit TEFA TKJ',
            'active_menu' => 'reports',
            'active_tab'  => $activeTab,
            'filter_type' => $filterType,
            'start_date'  => $startDate,
            'end_date'    => $endDate,
            'is_read_only'=> $isReadOnly,
            'user_role'   => $role,
            'report_data' => $reportData
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('report/index', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Standalone Formal Print/PDF View
     */
    public function printView()
    {
        Auth::requireRole(['kepala_sekolah', 'operator']);

        $role = Auth::role();
        $isReadOnly = ($role === 'kepala_sekolah');

        $activeTab  = trim($_GET['tab'] ?? 'orders');
        $filterType = trim($_GET['filter'] ?? 'this_month');
        $startDate  = trim($_GET['start_date'] ?? '');
        $endDate    = trim($_GET['end_date'] ?? '');

        $reportModel = $this->model('ReportModel');

        $reportData = [];
        $tabTitle = 'Laporan Operasional TEFA';
        switch ($activeTab) {
            case 'orders':
                $reportData = $reportModel->getOrderReportData($filterType, $startDate, $endDate);
                $tabTitle = 'Laporan Jumlah Order';
                break;
            case 'revenue':
                $reportData = $reportModel->getRevenueReportData($filterType, $startDate, $endDate);
                $tabTitle = 'Laporan Pendapatan TEFA';
                break;
            case 'services':
                $reportData = $reportModel->getTopServicesReportData($filterType, $startDate, $endDate);
                $tabTitle = 'Laporan Jasa Terlaris';
                break;
            case 'customers':
                $reportData = $reportModel->getCustomerReportData($filterType, $startDate, $endDate);
                $tabTitle = 'Laporan Customer';
                break;
            case 'technicians':
                $reportData = $reportModel->getTechnicianReportData($filterType, $startDate, $endDate);
                $tabTitle = 'Laporan Pekerjaan Teknisi';
                break;
            case 'ratings':
                $reportData = $reportModel->getRatingReportData($filterType, $startDate, $endDate);
                $tabTitle = 'Laporan Rating Layanan';
                break;
            case 'period':
                $reportData = $reportModel->getPeriodReportData($filterType, $startDate, $endDate);
                $tabTitle = 'Laporan Berdasarkan Periode';
                break;
        }

        $data = [
            'title'       => 'Cetak ' . $tabTitle,
            'active_tab'  => $activeTab,
            'tab_title'   => $tabTitle,
            'filter_type' => $filterType,
            'is_read_only'=> $isReadOnly,
            'user'        => Auth::user(),
            'report_data' => $reportData
        ];

        $this->view('report/print', $data);
    }

    /**
     * Download Excel / CSV Format Export File
     */
    public function exportExcel()
    {
        Auth::requireRole(['kepala_sekolah', 'operator']);

        $activeTab  = trim($_GET['tab'] ?? 'orders');
        $filterType = trim($_GET['filter'] ?? 'this_month');
        $startDate  = trim($_GET['start_date'] ?? '');
        $endDate    = trim($_GET['end_date'] ?? '');

        $reportModel = $this->model('ReportModel');
        $filename = "Laporan_" . ucfirst($activeTab) . "_" . date('Ymd_His') . ".csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        // Add UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        switch ($activeTab) {
            case 'orders':
                $data = $reportModel->getOrderReportData($filterType, $startDate, $endDate);
                fputcsv($output, ['LAPORAN JUMLAH ORDER - BUSINESS UNIT TEFA TKJ']);
                fputcsv($output, ['Periode:', $data['period_label']]);
                fputcsv($output, ['Total Order:', $data['total_orders'], 'Order Aktif:', $data['active_orders'], 'Order Selesai:', $data['completed_orders'], 'Order Dibatalkan:', $data['cancelled_orders']]);
                fputcsv($output, []);
                fputcsv($output, ['No Tiket', 'Customer', 'Telepon', 'Jasa Utama', 'Teknisi', 'Prioritas', 'Status', 'Tanggal Jadwal', 'Total Cost (Rp)']);
                
                if (!empty($data['orders'])) {
                    foreach ($data['orders'] as $row) {
                        fputcsv($output, [
                            $row['order_code'] ?? '',
                            $row['customer_name'] ?? '',
                            $row['customer_phone'] ?? '',
                            $row['main_service_name'] ?? '-',
                            $row['technician_name'] ?? 'Belum Ditugaskan',
                            strtoupper($row['priority'] ?? 'MEDIUM'),
                            $row['status'] ?? '',
                            $row['scheduled_date'] ?? '',
                            number_format($row['grand_total'] ?? 0, 0, ',', '.')
                        ]);
                    }
                }
                break;

            case 'revenue':
                $data = $reportModel->getRevenueReportData($filterType, $startDate, $endDate);
                fputcsv($output, ['LAPORAN PENDAPATAN TEFA - BUSINESS UNIT TEFA TKJ']);
                fputcsv($output, ['Periode:', $data['period_label']]);
                fputcsv($output, ['Total Transaksi:', $data['total_transactions'], 'Total Omset Gross:', $data['gross_revenue'], 'Revenue Lunas:', $data['paid_revenue'], 'Piutang Unpaid:', $data['unpaid_revenue']]);
                fputcsv($output, []);
                fputcsv($output, ['Kode Transaksi', 'Kode Order', 'Customer', 'Tipe Customer', 'Biaya Jasa (Rp)', 'Biaya Sparepart (Rp)', 'Diskon (Rp)', 'Grand Total (Rp)', 'Status Bayar', 'Tanggal']);
                
                if (!empty($data['transactions'])) {
                    foreach ($data['transactions'] as $row) {
                        fputcsv($output, [
                            $row['transaction_code'] ?? '',
                            $row['order_code'] ?? '',
                            $row['customer_name'] ?? '',
                            strtoupper($row['customer_type'] ?? 'UMUM'),
                            number_format($row['total_service_fee'] ?? 0, 0, ',', '.'),
                            number_format($row['total_sparepart_fee'] ?? 0, 0, ',', '.'),
                            number_format($row['discount'] ?? 0, 0, ',', '.'),
                            number_format($row['grand_total'] ?? 0, 0, ',', '.'),
                            $row['payment_status'] ?? '',
                            $row['created_at'] ?? ''
                        ]);
                    }
                }
                break;

            case 'services':
                $data = $reportModel->getTopServicesReportData($filterType, $startDate, $endDate);
                fputcsv($output, ['LAPORAN JASA TERLARIS - BUSINESS UNIT TEFA TKJ']);
                fputcsv($output, ['Periode:', $data['period_label']]);
                fputcsv($output, []);
                fputcsv($output, ['Kode Jasa', 'Nama Layanan', 'Kategori', 'Harga Base (Rp)', 'Total Dipesan (Frekuensi)', 'Total Omset Dihasilkan (Rp)']);
                
                if (!empty($data['services'])) {
                    foreach ($data['services'] as $row) {
                        fputcsv($output, [
                            $row['code'] ?? '',
                            $row['name'] ?? '',
                            $row['category'] ?? '',
                            number_format($row['base_price'] ?? 0, 0, ',', '.'),
                            $row['total_orders'] ?? 0,
                            number_format($row['total_revenue'] ?? 0, 0, ',', '.')
                        ]);
                    }
                }
                break;

            case 'customers':
                $data = $reportModel->getCustomerReportData($filterType, $startDate, $endDate);
                fputcsv($output, ['LAPORAN DEMOGRAFI & TRANSAKSI CUSTOMER']);
                fputcsv($output, ['Periode:', $data['period_label']]);
                fputcsv($output, []);
                fputcsv($output, ['Nama Customer', 'Email', 'Telepon', 'Tipe Customer', 'Kota', 'Total Order', 'Total Spending (Rp)', 'Order Terakhir']);
                
                if (!empty($data['customers'])) {
                    foreach ($data['customers'] as $row) {
                        fputcsv($output, [
                            $row['full_name'] ?? '',
                            $row['email'] ?? '',
                            $row['phone'] ?? '',
                            strtoupper($row['customer_type'] ?? 'UMUM'),
                            $row['city'] ?? '',
                            $row['total_orders'] ?? 0,
                            number_format($row['total_spent'] ?? 0, 0, ',', '.'),
                            $row['last_order_date'] ?? '-'
                        ]);
                    }
                }
                break;

            case 'technicians':
                $data = $reportModel->getTechnicianReportData($filterType, $startDate, $endDate);
                fputcsv($output, ['LAPORAN EVALUASI PERFORMA TEKNISI SISWA TKJ']);
                fputcsv($output, ['Periode:', $data['period_label']]);
                fputcsv($output, []);
                fputcsv($output, ['Nama Teknisi', 'NIS/NIP', 'Kelas', 'Spesialisasi', 'Skill Level', 'Status', 'Total Penugasan', 'Tugas Selesai', 'Pekerjaan Aktif', 'Rating Rata-rata']);
                
                if (!empty($data['technicians'])) {
                    foreach ($data['technicians'] as $row) {
                        fputcsv($output, [
                            $row['full_name'] ?? '',
                            $row['nis_nip'] ?? '',
                            $row['class_grade'] ?? '',
                            $row['specialization'] ?? '',
                            strtoupper($row['skill_level'] ?? 'JUNIOR'),
                            strtoupper($row['current_status'] ?? 'AVAILABLE'),
                            $row['total_assigned'] ?? 0,
                            $row['total_completed'] ?? 0,
                            $row['total_ongoing'] ?? 0,
                            number_format($row['avg_rating'] ?? 5, 1)
                        ]);
                    }
                }
                break;

            case 'ratings':
                $data = $reportModel->getRatingReportData($filterType, $startDate, $endDate);
                fputcsv($output, ['LAPORAN RATING & FEEDBACK LAYANAN PELANGGAN']);
                fputcsv($output, ['Periode:', $data['period_label']]);
                fputcsv($output, ['Total Review:', $data['total_reviews'], 'Rating Rata-rata:', $data['avg_rating'], 'Tingkat Kepuasan (%):', $data['satisfaction_rate'] . '%']);
                fputcsv($output, []);
                fputcsv($output, ['Kode Order', 'Customer', 'Layanan Jasa', 'Teknisi Siswa', 'Rating Bintang', 'Komentar / Feedback', 'Tanggal']);
                
                if (!empty($data['ratings'])) {
                    foreach ($data['ratings'] as $row) {
                        fputcsv($output, [
                            $row['order_code'] ?? '',
                            $row['customer_name'] ?? '',
                            $row['service_name'] ?? '',
                            $row['technician_name'] ?? 'N/A',
                            $row['stars'] . ' Bintang',
                            str_replace("\n", ' ', $row['comment'] ?? ''),
                            $row['created_at'] ?? ''
                        ]);
                    }
                }
                break;

            case 'period':
                $data = $reportModel->getPeriodReportData($filterType, $startDate, $endDate);
                fputcsv($output, ['LAPORAN TREN PERIODE & ANALISIS REKAPITULASI']);
                fputcsv($output, ['Periode:', $data['period_label']]);
                fputcsv($output, []);
                fputcsv($output, ['Periode Waktu', 'Total Order Masuk', 'Order Selesai', 'Gross Omset (Rp)', 'Revenue Lunas (Rp)', 'Rating Rata-rata']);
                
                if (!empty($data['timeline'])) {
                    foreach ($data['timeline'] as $row) {
                        fputcsv($output, [
                            $row['period_date'] ?? '',
                            $row['total_orders'] ?? 0,
                            $row['completed_orders'] ?? 0,
                            number_format($row['gross_revenue'] ?? 0, 0, ',', '.'),
                            number_format($row['paid_revenue'] ?? 0, 0, ',', '.'),
                            number_format($row['avg_rating'] ?? 5, 1)
                        ]);
                    }
                }
                break;
        }

        fclose($output);
        exit;
    }
}
