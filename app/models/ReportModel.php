<?php
/**
 * ReportModel Class
 * Comprehensive Reporting Engine & Data Aggregator for Kepala Sekolah & Operator
 * Supports 7 core reports, 5 date period filters, and fallback datasets.
 */

class ReportModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Helper: Resolve Date Filtering SQL Conditions & Parameters
     * @param string $filterType 'today', 'this_week', 'this_month', 'this_year', 'custom'
     * @param string $startDate 'YYYY-MM-DD'
     * @param string $endDate 'YYYY-MM-DD'
     * @param string $column SQL column name to filter
     * @return array ['sql' => string, 'params' => array, 'label' => string]
     */
    public function resolveDateFilter($filterType = 'this_month', $startDate = '', $endDate = '', $column = 'created_at')
    {
        $sql = "";
        $params = [];
        $label = "Bulan Ini";

        switch ($filterType) {
            case 'today':
                $sql = " AND DATE({$column}) = CURDATE()";
                $label = "Hari Ini (" . date('d M Y') . ")";
                break;

            case 'this_week':
                $sql = " AND YEARWEEK({$column}, 1) = YEARWEEK(CURDATE(), 1)";
                $startOfWeek = date('d M Y', strtotime('monday this week'));
                $endOfWeek = date('d M Y', strtotime('sunday this week'));
                $label = "Minggu Ini ({$startOfWeek} - {$endOfWeek})";
                break;

            case 'this_month':
                $sql = " AND YEAR({$column}) = YEAR(CURDATE()) AND MONTH({$column}) = MONTH(CURDATE())";
                $label = "Bulan Ini (" . date('F Y') . ")";
                break;

            case 'this_year':
                $sql = " AND YEAR({$column}) = YEAR(CURDATE())";
                $label = "Tahun Ini (" . date('Y') . ")";
                break;

            case 'custom':
                if (!empty($startDate) && !empty($endDate)) {
                    $sql = " AND DATE({$column}) BETWEEN :start_date AND :end_date";
                    $params[':start_date'] = $startDate;
                    $params[':end_date']   = $endDate;
                    $label = "Periode Custom (" . date('d/m/Y', strtotime($startDate)) . " s.d. " . date('d/m/Y', strtotime($endDate)) . ")";
                } else if (!empty($startDate)) {
                    $sql = " AND DATE({$column}) >= :start_date";
                    $params[':start_date'] = $startDate;
                    $label = "Sejak Tanggal " . date('d/m/Y', strtotime($startDate));
                } else if (!empty($endDate)) {
                    $sql = " AND DATE({$column}) <= :end_date";
                    $params[':end_date'] = $endDate;
                    $label = "Sampai Tanggal " . date('d/m/Y', strtotime($endDate));
                } else {
                    $label = "Semua Periode";
                }
                break;

            default:
                $sql = " AND YEAR({$column}) = YEAR(CURDATE()) AND MONTH({$column}) = MONTH(CURDATE())";
                $label = "Bulan Ini (" . date('F Y') . ")";
                break;
        }

        return [
            'sql'    => $sql,
            'params' => $params,
            'label'  => $label
        ];
    }

    /**
     * 1. LAPORAN JUMLAH ORDER
     */
    public function getOrderReportData($filterType = 'this_month', $startDate = '', $endDate = '')
    {
        $filter = $this->resolveDateFilter($filterType, $startDate, $endDate, 'so.created_at');

        if ($this->db->isConnected()) {
            // Overall Stats
            $sqlSummary = "SELECT 
                            COUNT(so.id) as total_orders,
                            SUM(CASE WHEN so.status IN ('pending', 'approved', 'assigned', 'in_progress', 'Diajukan', 'Menunggu Verifikasi', 'Disetujui', 'Teknisi Ditugaskan', 'Dijadwalkan', 'Dalam Pengerjaan') THEN 1 ELSE 0 END) as active_orders,
                            SUM(CASE WHEN so.status IN ('completed', 'Selesai', 'Ditutup') THEN 1 ELSE 0 END) as completed_orders,
                            SUM(CASE WHEN so.status IN ('cancelled', 'Dibatalkan') THEN 1 ELSE 0 END) as cancelled_orders
                           FROM service_orders so
                           WHERE 1=1 " . $filter['sql'];

            $this->db->query($sqlSummary);
            foreach ($filter['params'] as $key => $val) {
                $this->db->bind($key, $val);
            }
            $summary = $this->db->single() ?: [];

            // Breakdown by Status
            $sqlStatus = "SELECT so.status, COUNT(so.id) as count 
                          FROM service_orders so 
                          WHERE 1=1 " . $filter['sql'] . " 
                          GROUP BY so.status";
            $this->db->query($sqlStatus);
            foreach ($filter['params'] as $key => $val) {
                $this->db->bind($key, $val);
            }
            $statusRows = $this->db->resultSet() ?: [];

            // Detailed Orders List
            $sqlList = "SELECT so.*, 
                               u_cust.full_name as customer_name, u_cust.phone as customer_phone,
                               u_tech.full_name as technician_name,
                               s.name as main_service_name,
                               t.grand_total, t.payment_status
                        FROM service_orders so
                        LEFT JOIN customers c ON so.customer_id = c.id
                        LEFT JOIN users u_cust ON c.user_id = u_cust.id
                        LEFT JOIN technicians tec ON so.technician_id = tec.id
                        LEFT JOIN users u_tech ON tec.user_id = u_tech.id
                        LEFT JOIN order_details od ON so.id = od.order_id
                        LEFT JOIN services s ON od.service_id = s.id
                        LEFT JOIN transactions t ON so.id = t.order_id
                        WHERE 1=1 " . $filter['sql'] . "
                        GROUP BY so.id
                        ORDER BY so.id DESC";

            $this->db->query($sqlList);
            foreach ($filter['params'] as $key => $val) {
                $this->db->bind($key, $val);
            }
            $ordersList = $this->db->resultSet() ?: [];

            return [
                'period_label'     => $filter['label'],
                'total_orders'     => intval($summary['total_orders'] ?? 0),
                'active_orders'    => intval($summary['active_orders'] ?? 0),
                'completed_orders' => intval($summary['completed_orders'] ?? 0),
                'cancelled_orders' => intval($summary['cancelled_orders'] ?? 0),
                'status_breakdown' => $statusRows,
                'orders'           => $ordersList
            ];
        }

        return $this->getDemoOrderReportData($filter['label']);
    }

    /**
     * 2. LAPORAN PENDAPATAN
     */
    public function getRevenueReportData($filterType = 'this_month', $startDate = '', $endDate = '')
    {
        $filter = $this->resolveDateFilter($filterType, $startDate, $endDate, 't.created_at');

        if ($this->db->isConnected()) {
            $sqlSummary = "SELECT 
                            COUNT(t.id) as total_transactions,
                            COALESCE(SUM(t.grand_total), 0) as gross_revenue,
                            COALESCE(SUM(CASE WHEN t.payment_status IN ('paid', 'Lunas') THEN t.grand_total ELSE 0 END), 0) as paid_revenue,
                            COALESCE(SUM(CASE WHEN t.payment_status IN ('unpaid', 'partially_paid', 'Belum Dibayar', 'Menunggu Verifikasi') THEN t.grand_total ELSE 0 END), 0) as unpaid_revenue,
                            COALESCE(SUM(t.total_service_fee), 0) as total_service_fee,
                            COALESCE(SUM(t.total_sparepart_fee), 0) as total_sparepart_fee,
                            COALESCE(SUM(t.discount), 0) as total_discount
                           FROM transactions t
                           WHERE 1=1 " . $filter['sql'];

            $this->db->query($sqlSummary);
            foreach ($filter['params'] as $key => $val) {
                $this->db->bind($key, $val);
            }
            $summary = $this->db->single() ?: [];

            // Payment Methods Breakdown
            $sqlMethods = "SELECT p.payment_method, COUNT(p.id) as count, COALESCE(SUM(p.amount), 0) as total
                           FROM payments p
                           JOIN transactions t ON p.transaction_id = t.id
                           WHERE 1=1 " . $filter['sql'] . "
                           GROUP BY p.payment_method";

            $this->db->query($sqlMethods);
            foreach ($filter['params'] as $key => $val) {
                $this->db->bind($key, $val);
            }
            $methods = $this->db->resultSet() ?: [];

            // Detailed Transactions List
            $sqlList = "SELECT t.*, so.order_code, u_cust.full_name as customer_name, c.customer_type
                        FROM transactions t
                        JOIN service_orders so ON t.order_id = so.id
                        JOIN customers c ON so.customer_id = c.id
                        JOIN users u_cust ON c.user_id = u_cust.id
                        WHERE 1=1 " . $filter['sql'] . "
                        ORDER BY t.id DESC";

            $this->db->query($sqlList);
            foreach ($filter['params'] as $key => $val) {
                $this->db->bind($key, $val);
            }
            $transactions = $this->db->resultSet() ?: [];

            return [
                'period_label'        => $filter['label'],
                'total_transactions' => intval($summary['total_transactions'] ?? 0),
                'gross_revenue'      => floatval($summary['gross_revenue'] ?? 0),
                'paid_revenue'       => floatval($summary['paid_revenue'] ?? 0),
                'unpaid_revenue'     => floatval($summary['unpaid_revenue'] ?? 0),
                'total_service_fee'  => floatval($summary['total_service_fee'] ?? 0),
                'total_sparepart_fee'=> floatval($summary['total_sparepart_fee'] ?? 0),
                'total_discount'     => floatval($summary['total_discount'] ?? 0),
                'payment_methods'    => $methods,
                'transactions'       => $transactions
            ];
        }

        return $this->getDemoRevenueReportData($filter['label']);
    }

    /**
     * 3. LAPORAN JASA TERLARIS
     */
    public function getTopServicesReportData($filterType = 'this_month', $startDate = '', $endDate = '')
    {
        $filter = $this->resolveDateFilter($filterType, $startDate, $endDate, 'so.created_at');

        if ($this->db->isConnected()) {
            $sql = "SELECT s.id, s.code, s.name, s.category, s.base_price, s.icon,
                           COUNT(od.id) as total_orders,
                           COALESCE(SUM(od.subtotal), 0) as total_revenue
                    FROM services s
                    JOIN order_details od ON s.id = od.service_id
                    JOIN service_orders so ON od.order_id = so.id
                    WHERE 1=1 " . $filter['sql'] . "
                    GROUP BY s.id, s.code, s.name, s.category, s.base_price, s.icon
                    ORDER BY total_orders DESC, total_revenue DESC";

            $this->db->query($sql);
            foreach ($filter['params'] as $key => $val) {
                $this->db->bind($key, $val);
            }
            $services = $this->db->resultSet() ?: [];

            // Category breakdown
            $sqlCat = "SELECT s.category, COUNT(od.id) as total_orders, COALESCE(SUM(od.subtotal), 0) as total_revenue
                       FROM services s
                       JOIN order_details od ON s.id = od.service_id
                       JOIN service_orders so ON od.order_id = so.id
                       WHERE 1=1 " . $filter['sql'] . "
                       GROUP BY s.category
                       ORDER BY total_orders DESC";
            $this->db->query($sqlCat);
            foreach ($filter['params'] as $key => $val) {
                $this->db->bind($key, $val);
            }
            $categories = $this->db->resultSet() ?: [];

            return [
                'period_label' => $filter['label'],
                'services'     => $services,
                'categories'   => $categories
            ];
        }

        return $this->getDemoTopServicesReportData($filter['label']);
    }

    /**
     * 4. LAPORAN CUSTOMER
     */
    public function getCustomerReportData($filterType = 'this_month', $startDate = '', $endDate = '')
    {
        $filter = $this->resolveDateFilter($filterType, $startDate, $endDate, 'c.created_at');

        if ($this->db->isConnected()) {
            // Customer Type Breakdown
            $sqlType = "SELECT c.customer_type, COUNT(c.id) as count 
                        FROM customers c 
                        GROUP BY c.customer_type";
            $this->db->query($sqlType);
            $typeBreakdown = $this->db->resultSet() ?: [];

            // Customer Leaderboard / Activity in period
            $filterOrder = $this->resolveDateFilter($filterType, $startDate, $endDate, 'so.created_at');
            $sqlList = "SELECT c.id, c.customer_type, c.address, c.city,
                               u.full_name, u.email, u.phone,
                               COUNT(so.id) as total_orders,
                               COALESCE(SUM(t.grand_total), 0) as total_spent,
                               MAX(so.created_at) as last_order_date
                        FROM customers c
                        JOIN users u ON c.user_id = u.id
                        LEFT JOIN service_orders so ON c.id = so.customer_id " . $filterOrder['sql'] . "
                        LEFT JOIN transactions t ON so.id = t.order_id AND t.payment_status IN ('paid', 'Lunas')
                        GROUP BY c.id, c.customer_type, c.address, c.city, u.full_name, u.email, u.phone
                        ORDER BY total_orders DESC, total_spent DESC";

            $this->db->query($sqlList);
            foreach ($filterOrder['params'] as $key => $val) {
                $this->db->bind($key, $val);
            }
            $customers = $this->db->resultSet() ?: [];

            return [
                'period_label'   => $filter['label'],
                'total_customers'=> count($customers),
                'type_breakdown' => $typeBreakdown,
                'customers'      => $customers
            ];
        }

        return $this->getDemoCustomerReportData($filter['label']);
    }

    /**
     * 5. LAPORAN PEKERJAAN TEKNISI
     */
    public function getTechnicianReportData($filterType = 'this_month', $startDate = '', $endDate = '')
    {
        $filter = $this->resolveDateFilter($filterType, $startDate, $endDate, 'so.created_at');

        if ($this->db->isConnected()) {
            $sql = "SELECT tec.id, tec.nis_nip, tec.class_grade, tec.specialization, tec.skill_level, tec.status as current_status,
                           u.full_name, u.phone,
                           COUNT(so.id) as total_assigned,
                           SUM(CASE WHEN so.status IN ('completed', 'Selesai', 'Ditutup') THEN 1 ELSE 0 END) as total_completed,
                           SUM(CASE WHEN so.status IN ('assigned', 'in_progress', 'Teknisi Ditugaskan', 'Dijadwalkan', 'Dalam Pengerjaan') THEN 1 ELSE 0 END) as total_ongoing,
                           COALESCE(AVG(r.stars), 5.0) as avg_rating,
                           COUNT(r.id) as review_count
                    FROM technicians tec
                    JOIN users u ON tec.user_id = u.id
                    LEFT JOIN service_orders so ON tec.id = so.technician_id " . $filter['sql'] . "
                    LEFT JOIN ratings r ON tec.id = r.technician_id
                    GROUP BY tec.id, tec.nis_nip, tec.class_grade, tec.specialization, tec.skill_level, tec.status, u.full_name, u.phone
                    ORDER BY total_completed DESC, avg_rating DESC";

            $this->db->query($sql);
            foreach ($filter['params'] as $key => $val) {
                $this->db->bind($key, $val);
            }
            $technicians = $this->db->resultSet() ?: [];

            return [
                'period_label' => $filter['label'],
                'technicians'  => $technicians
            ];
        }

        return $this->getDemoTechnicianReportData($filter['label']);
    }

    /**
     * 6. LAPORAN RATING LAYANAN
     */
    public function getRatingReportData($filterType = 'this_month', $startDate = '', $endDate = '')
    {
        $filter = $this->resolveDateFilter($filterType, $startDate, $endDate, 'r.created_at');

        if ($this->db->isConnected()) {
            $sqlSummary = "SELECT 
                            COUNT(r.id) as total_reviews,
                            COALESCE(AVG(r.stars), 0) as avg_stars,
                            SUM(CASE WHEN r.stars = 5 THEN 1 ELSE 0 END) as star_5,
                            SUM(CASE WHEN r.stars = 4 THEN 1 ELSE 0 END) as star_4,
                            SUM(CASE WHEN r.stars = 3 THEN 1 ELSE 0 END) as star_3,
                            SUM(CASE WHEN r.stars = 2 THEN 1 ELSE 0 END) as star_2,
                            SUM(CASE WHEN r.stars = 1 THEN 1 ELSE 0 END) as star_1
                           FROM ratings r
                           WHERE 1=1 " . $filter['sql'];

            $this->db->query($sqlSummary);
            foreach ($filter['params'] as $key => $val) {
                $this->db->bind($key, $val);
            }
            $summary = $this->db->single() ?: [];

            $total = intval($summary['total_reviews'] ?? 0);
            $satisfied = intval($summary['star_5'] ?? 0) + intval($summary['star_4'] ?? 0);
            $satisfactionRate = $total > 0 ? round(($satisfied / $total) * 100) : 100;

            // Rating List
            $sqlList = "SELECT r.*, 
                               so.order_code, s.name as service_name,
                               u_cust.full_name as customer_name,
                               u_tech.full_name as technician_name
                        FROM ratings r
                        JOIN service_orders so ON r.order_id = so.id
                        LEFT JOIN order_details od ON so.id = od.order_id
                        LEFT JOIN services s ON od.service_id = s.id
                        JOIN customers c ON r.customer_id = c.id
                        JOIN users u_cust ON c.user_id = u_cust.id
                        LEFT JOIN technicians tec ON r.technician_id = tec.id
                        LEFT JOIN users u_tech ON tec.user_id = u_tech.id
                        WHERE 1=1 " . $filter['sql'] . "
                        GROUP BY r.id
                        ORDER BY r.id DESC";

            $this->db->query($sqlList);
            foreach ($filter['params'] as $key => $val) {
                $this->db->bind($key, $val);
            }
            $ratings = $this->db->resultSet() ?: [];

            return [
                'period_label'      => $filter['label'],
                'total_reviews'     => $total,
                'avg_rating'        => round(floatval($summary['avg_stars'] ?? 5.0), 1),
                'satisfaction_rate' => $satisfactionRate,
                'stars_breakdown'   => [
                    5 => intval($summary['star_5'] ?? 0),
                    4 => intval($summary['star_4'] ?? 0),
                    3 => intval($summary['star_3'] ?? 0),
                    2 => intval($summary['star_2'] ?? 0),
                    1 => intval($summary['star_1'] ?? 0),
                ],
                'ratings'           => $ratings
            ];
        }

        return $this->getDemoRatingReportData($filter['label']);
    }

    /**
     * 7. LAPORAN BERDASARKAN PERIODE (TREND COMPARISON)
     */
    public function getPeriodReportData($filterType = 'this_month', $startDate = '', $endDate = '')
    {
        $filter = $this->resolveDateFilter($filterType, $startDate, $endDate, 'so.created_at');

        if ($this->db->isConnected()) {
            $groupBy = "DATE(so.created_at)";
            $dateFormat = "%d %b %Y";

            if ($filterType === 'this_year') {
                $groupBy = "DATE_FORMAT(so.created_at, '%Y-%m')";
                $dateFormat = "%M %Y";
            }

            $sql = "SELECT DATE_FORMAT(so.created_at, '{$dateFormat}') as period_date,
                           COUNT(so.id) as total_orders,
                           SUM(CASE WHEN so.status IN ('completed', 'Selesai', 'Ditutup') THEN 1 ELSE 0 END) as completed_orders,
                           COALESCE(SUM(t.grand_total), 0) as gross_revenue,
                           COALESCE(SUM(CASE WHEN t.payment_status IN ('paid', 'Lunas') THEN t.grand_total ELSE 0 END), 0) as paid_revenue,
                           COALESCE(AVG(r.stars), 5.0) as avg_rating
                    FROM service_orders so
                    LEFT JOIN transactions t ON so.id = t.order_id
                    LEFT JOIN ratings r ON so.id = r.order_id
                    WHERE 1=1 " . $filter['sql'] . "
                    GROUP BY {$groupBy}
                    ORDER BY MIN(so.created_at) ASC";

            $this->db->query($sql);
            foreach ($filter['params'] as $key => $val) {
                $this->db->bind($key, $val);
            }
            $timeline = $this->db->resultSet() ?: [];

            return [
                'period_label' => $filter['label'],
                'timeline'     => $timeline
            ];
        }

        return $this->getDemoPeriodReportData($filter['label']);
    }

    // =========================================================================
    // FALLBACK DEMO DATASETS (For offline DB / quick testing)
    // =========================================================================

    private function getDemoOrderReportData($label)
    {
        return [
            'period_label'     => $label,
            'total_orders'     => 14,
            'active_orders'    => 5,
            'completed_orders' => 8,
            'cancelled_orders' => 1,
            'status_breakdown' => [
                ['status' => 'Diajukan', 'count' => 2],
                ['status' => 'Teknisi Ditugaskan', 'count' => 2],
                ['status' => 'Dalam Pengerjaan', 'count' => 1],
                ['status' => 'Selesai', 'count' => 8],
                ['status' => 'Dibatalkan', 'count' => 1]
            ],
            'orders' => [
                [
                    'id' => 1,
                    'order_code' => 'ORD-20260829-001',
                    'customer_name' => 'Ahmad Dahlan',
                    'customer_phone' => '081234567890',
                    'technician_name' => 'Budi Santoso',
                    'main_service_name' => 'Instalasi CCTV Analog & DVR 4 Channel',
                    'status' => 'Dalam Pengerjaan',
                    'priority' => 'high',
                    'scheduled_date' => '2026-08-30',
                    'grand_total' => 660000,
                    'payment_status' => 'Belum Dibayar',
                    'created_at' => '2026-08-29 10:15:00'
                ],
                [
                    'id' => 2,
                    'order_code' => 'ORD-20260829-002',
                    'customer_name' => 'Dewi Lestari',
                    'customer_phone' => '081245678901',
                    'technician_name' => 'Rizky Pratama',
                    'main_service_name' => 'Konfigurasi Router MikroTik & Bandwidth',
                    'status' => 'Teknisi Ditugaskan',
                    'priority' => 'medium',
                    'scheduled_date' => '2026-08-31',
                    'grand_total' => 150000,
                    'payment_status' => 'Belum Dibayar',
                    'created_at' => '2026-08-29 11:30:00'
                ],
                [
                    'id' => 3,
                    'order_code' => 'ORD-20260828-003',
                    'customer_name' => 'Budi Utomo (SDN 1 Bandung)',
                    'customer_phone' => '081256789012',
                    'technician_name' => 'Fajar Kurniawan',
                    'main_service_name' => 'Perbaikan PC Desktop & Upgrade SSD/RAM',
                    'status' => 'Selesai',
                    'priority' => 'high',
                    'scheduled_date' => '2026-08-28',
                    'grand_total' => 250000,
                    'payment_status' => 'Lunas',
                    'created_at' => '2026-08-28 09:00:00'
                ]
            ]
        ];
    }

    private function getDemoRevenueReportData($label)
    {
        return [
            'period_label'        => $label,
            'total_transactions' => 12,
            'gross_revenue'      => 4250000,
            'paid_revenue'       => 3440000,
            'unpaid_revenue'     => 810000,
            'total_service_fee'  => 2850000,
            'total_sparepart_fee'=> 1400000,
            'total_discount'     => 50000,
            'payment_methods'    => [
                ['payment_method' => 'transfer_bank', 'count' => 6, 'total' => 2100000],
                ['payment_method' => 'qris', 'count' => 4, 'total' => 940000],
                ['payment_method' => 'cash', 'count' => 2, 'total' => 400000]
            ],
            'transactions' => [
                [
                    'id' => 3,
                    'transaction_code' => 'TRX-20260828-003',
                    'order_code' => 'ORD-20260828-003',
                    'customer_name' => 'Budi Utomo (SDN 1 Bandung)',
                    'customer_type' => 'sekolah',
                    'total_service_fee' => 280000,
                    'total_sparepart_fee' => 0,
                    'discount' => 30000,
                    'tax_amount' => 0,
                    'grand_total' => 250000,
                    'payment_status' => 'Lunas',
                    'created_at' => '2026-08-28 16:30:00'
                ],
                [
                    'id' => 1,
                    'transaction_code' => 'TRX-20260829-001',
                    'order_code' => 'ORD-20260829-001',
                    'customer_name' => 'Ahmad Dahlan',
                    'customer_type' => 'umum',
                    'total_service_fee' => 200000,
                    'total_sparepart_fee' => 480000,
                    'discount' => 20000,
                    'tax_amount' => 0,
                    'grand_total' => 660000,
                    'payment_status' => 'Belum Dibayar',
                    'created_at' => '2026-08-29 10:15:00'
                ]
            ]
        ];
    }

    private function getDemoTopServicesReportData($label)
    {
        return [
            'period_label' => $label,
            'services'     => [
                [
                    'id' => 1,
                    'code' => 'SVC-CCTV-01',
                    'name' => 'Instalasi CCTV Analog & DVR 4 Channel',
                    'category' => 'Keamanan Jaringan',
                    'base_price' => 200000,
                    'icon' => 'bi-camera-video-fill',
                    'total_orders' => 5,
                    'total_revenue' => 1750000
                ],
                [
                    'id' => 7,
                    'code' => 'SVC-RTR-01',
                    'name' => 'Konfigurasi Router MikroTik & Bandwidth Queue',
                    'category' => 'Network Management',
                    'base_price' => 150000,
                    'icon' => 'bi-router-fill',
                    'total_orders' => 4,
                    'total_revenue' => 750000
                ],
                [
                    'id' => 6,
                    'code' => 'SVC-HW-02',
                    'name' => 'Perbaikan PC Desktop & Upgrade SSD/RAM',
                    'category' => 'Maintenance Hardware',
                    'base_price' => 80000,
                    'icon' => 'bi-pc-display',
                    'total_orders' => 3,
                    'total_revenue' => 520000
                ]
            ],
            'categories' => [
                ['category' => 'Keamanan Jaringan', 'total_orders' => 5, 'total_revenue' => 1750000],
                ['category' => 'Network Management', 'total_orders' => 4, 'total_revenue' => 750000],
                ['category' => 'Maintenance Hardware', 'total_orders' => 3, 'total_revenue' => 520000]
            ]
        ];
    }

    private function getDemoCustomerReportData($label)
    {
        return [
            'period_label'    => $label,
            'total_customers' => 12,
            'type_breakdown'  => [
                ['customer_type' => 'umum', 'count' => 6],
                ['customer_type' => 'sekolah', 'count' => 4],
                ['customer_type' => 'instansi', 'count' => 2]
            ],
            'customers' => [
                [
                    'id' => 3,
                    'customer_type' => 'sekolah',
                    'full_name' => 'Budi Utomo (SDN 1 Bandung)',
                    'email' => 'sdn1.budiutomo@sch.id',
                    'phone' => '081256789012',
                    'city' => 'Kota Bandung',
                    'total_orders' => 4,
                    'total_spent' => 1250000,
                    'last_order_date' => '2026-08-28'
                ],
                [
                    'id' => 1,
                    'customer_type' => 'umum',
                    'full_name' => 'Ahmad Dahlan',
                    'email' => 'ahmad.dahlan@gmail.com',
                    'phone' => '081234567890',
                    'city' => 'Kota Bandung',
                    'total_orders' => 3,
                    'total_spent' => 960000,
                    'last_order_date' => '2026-08-29'
                ]
            ]
        ];
    }

    private function getDemoTechnicianReportData($label)
    {
        return [
            'period_label' => $label,
            'technicians'  => [
                [
                    'id' => 1,
                    'nis_nip' => '22231001',
                    'class_grade' => 'XII TKJ 1',
                    'specialization' => 'Instalasi CCTV & Fiber Optic',
                    'skill_level' => 'senior',
                    'current_status' => 'available',
                    'full_name' => 'Budi Santoso',
                    'phone' => '085711223344',
                    'total_assigned' => 6,
                    'total_completed' => 6,
                    'total_ongoing' => 0,
                    'avg_rating' => 4.9,
                    'review_count' => 6
                ],
                [
                    'id' => 2,
                    'nis_nip' => '22231015',
                    'class_grade' => 'XII TKJ 1',
                    'specialization' => 'Konfigurasi Router MikroTik & Firewall',
                    'skill_level' => 'senior',
                    'current_status' => 'on_duty',
                    'full_name' => 'Rizky Pratama',
                    'phone' => '085722334455',
                    'total_assigned' => 5,
                    'total_completed' => 4,
                    'total_ongoing' => 1,
                    'avg_rating' => 4.7,
                    'review_count' => 4
                ],
                [
                    'id' => 3,
                    'nis_nip' => '22231028',
                    'class_grade' => 'XII TKJ 2',
                    'specialization' => 'Hardware PC/Laptop & Troubleshooting OS',
                    'skill_level' => 'junior',
                    'current_status' => 'available',
                    'full_name' => 'Fajar Kurniawan',
                    'phone' => '085733445566',
                    'total_assigned' => 3,
                    'total_completed' => 3,
                    'total_ongoing' => 0,
                    'avg_rating' => 5.0,
                    'review_count' => 3
                ]
            ]
        ];
    }

    private function getDemoRatingReportData($label)
    {
        return [
            'period_label'      => $label,
            'total_reviews'     => 12,
            'avg_rating'        => 4.8,
            'satisfaction_rate' => 95,
            'stars_breakdown'   => [5 => 9, 4 => 2, 3 => 1, 2 => 0, 1 => 0],
            'ratings'           => [
                [
                    'id' => 1,
                    'order_code' => 'ORD-20260828-003',
                    'customer_name' => 'Budi Utomo (SDN 1 Bandung)',
                    'technician_name' => 'Fajar Kurniawan',
                    'service_name' => 'Perbaikan PC Desktop & Upgrade SSD/RAM',
                    'stars' => 5,
                    'comment' => 'Pelayanan teknisi siswa TKJ sangat ramah, rapi, dan PC lab sekolah sekarang kembali cepat!\n\n[FEEDBACK LAYANAN]: Mohon pertahankan respon cepat operator.',
                    'created_at' => '2026-08-28 17:00:00'
                ],
                [
                    'id' => 2,
                    'order_code' => 'ORD-20260829-002',
                    'customer_name' => 'Dewi Lestari',
                    'technician_name' => 'Rizky Pratama',
                    'service_name' => 'Konfigurasi Router MikroTik',
                    'stars' => 5,
                    'comment' => 'Setting router ruko sangat memuaskan, koneksi lancar.',
                    'created_at' => '2026-08-27 14:20:00'
                ]
            ]
        ];
    }

    private function getDemoPeriodReportData($label)
    {
        return [
            'period_label' => $label,
            'timeline'     => [
                ['period_date' => 'Mei 2026', 'total_orders' => 8, 'completed_orders' => 8, 'gross_revenue' => 1800000, 'paid_revenue' => 1800000, 'avg_rating' => 4.8],
                ['period_date' => 'Juni 2026', 'total_orders' => 12, 'completed_orders' => 11, 'gross_revenue' => 2900000, 'paid_revenue' => 2900000, 'avg_rating' => 4.7],
                ['period_date' => 'Juli 2026', 'total_orders' => 10, 'completed_orders' => 10, 'gross_revenue' => 2400000, 'paid_revenue' => 2400000, 'avg_rating' => 4.9],
                ['period_date' => 'Agustus 2026', 'total_orders' => 14, 'completed_orders' => 12, 'gross_revenue' => 4250000, 'paid_revenue' => 3440000, 'avg_rating' => 4.8]
            ]
        ];
    }
}
