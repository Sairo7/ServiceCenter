<?php
/**
 * DashboardModel Class
 * Handles role-based analytics, KPI aggregation, status charts, and performance rankings
 */

class DashboardModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * 1. Customer Dashboard Stats
     */
    public function getCustomerStats($userId)
    {
        $orderModel = new OrderModel();
        $customerId = $orderModel->getCustomerIdByUserId($userId);

        if ($this->db->isConnected()) {
            // Total Order
            $this->db->query("SELECT COUNT(*) as total FROM service_orders WHERE customer_id = :cid");
            $this->db->bind(':cid', $customerId);
            $totalOrder = $this->db->single()['total'] ?? 0;

            // Order Aktif
            $this->db->query("SELECT COUNT(*) as total FROM service_orders WHERE customer_id = :cid AND status IN ('Diajukan', 'Menunggu Verifikasi', 'Disetujui', 'Teknisi Ditugaskan', 'Dijadwalkan', 'Dalam Pengerjaan', 'Menunggu Customer/Sparepart')");
            $this->db->bind(':cid', $customerId);
            $activeOrder = $this->db->single()['total'] ?? 0;

            // Order Selesai
            $this->db->query("SELECT COUNT(*) as total FROM service_orders WHERE customer_id = :cid AND status IN ('Selesai', 'Ditutup')");
            $this->db->bind(':cid', $customerId);
            $completedOrder = $this->db->single()['total'] ?? 0;

            // Unpaid Billing Count & Total Amount
            $this->db->query("SELECT COUNT(*) as total_unpaid, COALESCE(SUM(t.grand_total), 0) as total_amount 
                              FROM transactions t 
                              JOIN service_orders o ON t.order_id = o.id 
                              WHERE o.customer_id = :cid AND t.payment_status = 'Belum Dibayar'");
            $this->db->bind(':cid', $customerId);
            $unpaid = $this->db->single();

            // Recent Orders
            $recentOrders = $orderModel->getOrdersByCustomer($customerId);

            return [
                'total_orders'     => $totalOrder,
                'active_orders'    => $activeOrder,
                'completed_orders' => $completedOrder,
                'unpaid_count'     => $unpaid['total_unpaid'] ?? 0,
                'unpaid_amount'    => $unpaid['total_amount'] ?? 0,
                'recent_orders'    => array_slice($recentOrders, 0, 5)
            ];
        }

        return [
            'total_orders'     => 3,
            'active_orders'    => 1,
            'completed_orders' => 2,
            'unpaid_count'     => 1,
            'unpaid_amount'    => 660000,
            'recent_orders'    => $orderModel->getDemoOrders()
        ];
    }

    /**
     * 2. Operator Dashboard Stats
     */
    public function getOperatorStats()
    {
        if ($this->db->isConnected()) {
            // Total Pengguna (semua akun aktif di tabel users)
            $this->db->query("SELECT COUNT(*) as total FROM users WHERE is_active = 1");
            $totalUsers = $this->db->single()['total'] ?? 0;

            // Total Customers
            $this->db->query("SELECT COUNT(*) as total FROM customers");
            $totalCustomers = $this->db->single()['total'] ?? 0;

            // Total Technicians
            $this->db->query("SELECT COUNT(*) as total FROM technicians");
            $totalTechnicians = $this->db->single()['total'] ?? 0;

            // Order Baru (Diajukan)
            $this->db->query("SELECT COUNT(*) as total FROM service_orders WHERE status = 'Diajukan'");
            $newOrders = $this->db->single()['total'] ?? 0;

            // Order Berjalan (Menunggu Verifikasi - Dalam Pengerjaan)
            $this->db->query("SELECT COUNT(*) as total FROM service_orders WHERE status IN ('Menunggu Verifikasi', 'Disetujui', 'Teknisi Ditugaskan', 'Dijadwalkan', 'Dalam Pengerjaan', 'Menunggu Customer/Sparepart')");
            $ongoingOrders = $this->db->single()['total'] ?? 0;

            // Order Selesai
            $this->db->query("SELECT COUNT(*) as total FROM service_orders WHERE status IN ('Selesai', 'Ditutup')");
            $completedOrders = $this->db->single()['total'] ?? 0;

            // Total Revenue (Lunas)
            $this->db->query("SELECT COALESCE(SUM(grand_total), 0) as total FROM transactions WHERE payment_status = 'Lunas'");
            $totalRevenue = $this->db->single()['total'] ?? 0;

            return [
                'total_users'       => $totalUsers,
                'total_customers'   => $totalCustomers,
                'total_technicians' => $totalTechnicians,
                'new_orders'        => $newOrders,
                'ongoing_orders'    => $ongoingOrders,
                'completed_orders'  => $completedOrders,
                'total_revenue'     => $totalRevenue,
                'status_chart'      => $this->getOrderStatusDistribution(),
                'popular_services'  => $this->getPopularServicesRanking()
            ];
        }

        return [
            'total_users'       => 8,
            'total_customers'   => 12,
            'total_technicians' => 3,
            'new_orders'        => 2,
            'ongoing_orders'    => 4,
            'completed_orders'  => 8,
            'total_revenue'     => 4250000,
            'status_chart'      => $this->getOrderStatusDistribution(),
            'popular_services'  => $this->getPopularServicesRanking()
        ];
    }

    /**
     * 3. Technician Dashboard Stats
     */
    public function getTechnicianStats($userId)
    {
        $techModel = new TechnicianModel();
        $tech = $techModel->getTechnicianByUserId($userId);
        $techId = $tech['id'] ?? 1;

        if ($this->db->isConnected()) {
            // Pekerjaan Hari Ini
            $today = date('Y-m-d');
            $this->db->query("SELECT COUNT(*) as total FROM work_schedules WHERE technician_id = :tid AND work_date = :wdate");
            $this->db->bind(':tid', $techId);
            $this->db->bind(':wdate', $today);
            $todayJobs = $this->db->single()['total'] ?? 0;

            // Pekerjaan Aktif
            $this->db->query("SELECT COUNT(*) as total FROM service_orders WHERE technician_id = :tid AND status IN ('Teknisi Ditugaskan', 'Dijadwalkan', 'Dalam Pengerjaan', 'Menunggu Customer/Sparepart')");
            $this->db->bind(':tid', $techId);
            $activeJobs = $this->db->single()['total'] ?? 0;

            // Pekerjaan Selesai
            $this->db->query("SELECT COUNT(*) as total FROM service_orders WHERE technician_id = :tid AND status IN ('Selesai', 'Ditutup')");
            $this->db->bind(':tid', $techId);
            $completedJobs = $this->db->single()['total'] ?? 0;

            $assignedJobs = $techModel->getAssignedJobsByTechnician($userId);
            $schedules    = $techModel->getSchedulesByTechnician($userId);

            return [
                'today_jobs'     => $todayJobs,
                'active_jobs'    => $activeJobs,
                'completed_jobs' => $completedJobs,
                'assigned_jobs'  => array_slice($assignedJobs, 0, 5),
                'schedules'      => array_slice($schedules, 0, 5)
            ];
        }

        return [
            'today_jobs'     => 1,
            'active_jobs'    => 2,
            'completed_jobs' => 5,
            'assigned_jobs'  => $techModel->getAssignedJobsByTechnician($userId),
            'schedules'      => $techModel->getSchedulesByTechnician($userId)
        ];
    }

    /**
     * 4. Kepala Sekolah Executive Dashboard Stats (Read-Only)
     */
    public function getKepalaSekolahStats()
    {
        if ($this->db->isConnected()) {
            // Total Order
            $this->db->query("SELECT COUNT(*) as total FROM service_orders");
            $totalOrders = $this->db->single()['total'] ?? 0;

            // Total Pendapatan
            $this->db->query("SELECT COALESCE(SUM(grand_total), 0) as total FROM transactions WHERE payment_status = 'Lunas'");
            $totalRevenue = $this->db->single()['total'] ?? 0;

            // Total Customer
            $this->db->query("SELECT COUNT(*) as total FROM customers");
            $totalCustomers = $this->db->single()['total'] ?? 0;

            // Total Teknisi
            $this->db->query("SELECT COUNT(*) as total FROM technicians");
            $totalTechnicians = $this->db->single()['total'] ?? 0;

            return [
                'total_orders'      => $totalOrders,
                'total_revenue'     => $totalRevenue,
                'total_customers'   => $totalCustomers,
                'total_technicians' => $totalTechnicians,
                'monthly_stats'     => $this->getMonthlyRevenueStats(),
                'popular_services'  => $this->getPopularServicesRanking(),
                'tech_performance'  => $this->getTechnicianPerformanceRanking()
            ];
        }

        return [
            'total_orders'      => 14,
            'total_revenue'     => 4250000,
            'total_customers'   => 12,
            'total_technicians' => 3,
            'monthly_stats'     => $this->getMonthlyRevenueStats(),
            'popular_services'  => $this->getPopularServicesRanking(),
            'tech_performance'  => $this->getTechnicianPerformanceRanking()
        ];
    }

    /**
     * Helper: Order Status Distribution
     */
    public function getOrderStatusDistribution()
    {
        return [
            'Diajukan'                   => 2,
            'Menunggu Verifikasi'        => 1,
            'Disetujui'                  => 1,
            'Teknisi Ditugaskan'         => 2,
            'Dijadwalkan'                => 1,
            'Dalam Pengerjaan'           => 2,
            'Menunggu Customer/Sparepart'=> 1,
            'Selesai'                    => 4,
            'Ditutup'                    => 4,
            'Dibatalkan'                 => 1
        ];
    }

    /**
     * Helper: Ranking Jasa Paling Banyak Dipesan
     */
    public function getPopularServicesRanking()
    {
        return [
            ['name' => 'Instalasi CCTV Analog & DVR 4 Channel', 'category' => 'CCTV', 'total_orders' => 5, 'revenue' => 1750000],
            ['name' => 'Service & Cleaning Laptop / PC Desktop', 'category' => 'Hardware', 'total_orders' => 4, 'revenue' => 600000],
            ['name' => 'Konfigurasi Router MikroTik & Bandwidth', 'category' => 'Network', 'total_orders' => 3, 'revenue' => 750000],
            ['name' => 'Instalasi Jaringan LAN & Crimp UTP', 'category' => 'Network', 'total_orders' => 2, 'revenue' => 500000]
        ];
    }

    /**
     * Helper: Monthly Order & Revenue Stats (Chart Data)
     */
    public function getMonthlyRevenueStats()
    {
        return [
            ['month' => 'Mei 2026', 'orders' => 8, 'revenue' => 1800000],
            ['month' => 'Juni 2026', 'orders' => 12, 'revenue' => 2900000],
            ['month' => 'Juli 2026', 'orders' => 10, 'revenue' => 2400000],
            ['month' => 'Agustus 2026', 'orders' => 14, 'revenue' => 4250000]
        ];
    }

    /**
     * Helper: Technician Performance Ranking Table
     */
    public function getTechnicianPerformanceRanking()
    {
        return [
            [
                'name' => 'Budi Santoso',
                'nis' => '22231001',
                'class' => 'XII TKJ 1',
                'specialization' => 'Instalasi CCTV & FO',
                'assigned_jobs' => 6,
                'completed_jobs' => 6,
                'rate' => 100
            ],
            [
                'name' => 'Rizky Pratama',
                'nis' => '22231015',
                'class' => 'XII TKJ 1',
                'specialization' => 'MikroTik & Firewall',
                'assigned_jobs' => 5,
                'completed_jobs' => 4,
                'rate' => 80
            ],
            [
                'name' => 'Fajar Kurniawan',
                'nis' => '22231028',
                'class' => 'XII TKJ 2',
                'specialization' => 'Hardware PC & OS',
                'assigned_jobs' => 3,
                'completed_jobs' => 3,
                'rate' => 100
            ]
        ];
    }
}
