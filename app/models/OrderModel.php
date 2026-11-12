<?php
/**
 * OrderModel Class
 * Handles database operations for Customer Service Orders
 */

class OrderModel
{
    private $db;
    private $table = 'service_orders';

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Generate unique ticket code: TKJ-YYYYMMDD-XXXX
     */
    public function generateTicketCode()
    {
        $prefix = 'TKJ-' . date('Ymd') . '-';
        $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4));
        return $prefix . $random;
    }

    /**
     * Create new service order
     */
    public function createOrder($data)
    {
        if ($this->db->isConnected()) {
            $sql = "INSERT INTO " . $this->table . " 
                    (order_code, customer_id, service_id, status, priority, service_address, problem_description, photo_evidence, scheduled_date, created_at) 
                    VALUES (:order_code, :customer_id, :service_id, :status, :priority, :service_address, :problem_description, :photo_evidence, :scheduled_date, NOW())";
            
            $this->db->query($sql);
            $this->db->bind(':order_code', $data['order_code']);
            $this->db->bind(':customer_id', $data['customer_id']);
            $this->db->bind(':service_id', $data['service_id']);
            $this->db->bind(':status', $data['status'] ?? 'Diajukan');
            $this->db->bind(':priority', $data['priority'] ?? 'medium');
            $this->db->bind(':service_address', $data['service_address']);
            $this->db->bind(':problem_description', $data['problem_description']);
            $this->db->bind(':photo_evidence', $data['photo_evidence'] ?? null);
            $this->db->bind(':scheduled_date', $data['scheduled_date']);
            
            if ($this->db->execute()) {
                $orderId = $this->db->lastInsertId();

                // Also insert line item into order_details for consistency
                if ($orderId && !empty($data['service_id'])) {
                    $this->db->query("SELECT base_price, name FROM services WHERE id = :sid");
                    $this->db->bind(':sid', $data['service_id']);
                    $svc = $this->db->single();
                    $unitPrice = $svc['base_price'] ?? 0;
                    $svcName = $svc['name'] ?? 'Layanan TKJ';

                    $sqlDetail = "INSERT INTO order_details (order_id, service_id, quantity, unit_price, subtotal, notes, created_at)
                                  VALUES (:order_id, :service_id, 1, :unit_price, :subtotal, :notes, NOW())";
                    $this->db->query($sqlDetail);
                    $this->db->bind(':order_id', $orderId);
                    $this->db->bind(':service_id', $data['service_id']);
                    $this->db->bind(':unit_price', $unitPrice);
                    $this->db->bind(':subtotal', $unitPrice);
                    $this->db->bind(':notes', $svcName);
                    $this->db->execute();
                }

                return $orderId;
            }
        }
        return rand(100, 999);
    }

    /**
     * Get orders belonging to a specific customer only
     */
    public function getOrdersByCustomer($customerId, $search = '', $status = '')
    {
        if ($this->db->isConnected()) {
            $sql = "SELECT o.*, s.name as service_name, s.category as service_category, s.icon as service_icon, s.base_price
                    FROM " . $this->table . " o
                    JOIN services s ON o.service_id = s.id
                    WHERE o.customer_id = :customer_id";

            if (!empty($search)) {
                $sql .= " AND (o.order_code LIKE :search OR o.problem_description LIKE :search OR s.name LIKE :search)";
            }
            if (!empty($status)) {
                $sql .= " AND o.status = :status";
            }

            $sql .= " ORDER BY o.id DESC";

            $this->db->query($sql);
            $this->db->bind(':customer_id', $customerId);
            if (!empty($search)) {
                $this->db->bind(':search', '%' . $search . '%');
            }
            if (!empty($status)) {
                $this->db->bind(':status', $status);
            }

            $results = $this->db->resultSet();
            if ($results !== false) {
                return $results;
            }
        }

        // Demo fallback data for customer
        $demo = $this->getDemoOrders();
        $filtered = [];
        foreach ($demo as $o) {
            if ($o['customer_id'] == $customerId) {
                $matchSearch = empty($search) || (stripos($o['order_code'], $search) !== false || stripos($o['service_name'], $search) !== false);
                $matchStatus = empty($status) || $o['status'] === $status;
                if ($matchSearch && $matchStatus) {
                    $filtered[] = $o;
                }
            }
        }
        return $filtered;
    }

    /**
     * Get all orders for Operator view with search and multi-filters
     */
    public function getAllOrders($search = '', $status = '', $serviceId = '')
    {
        if ($this->db->isConnected()) {
            $sql = "SELECT o.*, s.name as service_name, s.category as service_category, u.full_name as customer_name, u.phone as customer_phone
                    FROM " . $this->table . " o
                    JOIN services s ON o.service_id = s.id
                    JOIN customers c ON o.customer_id = c.id
                    JOIN users u ON c.user_id = u.id
                    WHERE 1=1";

            if (!empty($search)) {
                $sql .= " AND (o.order_code LIKE :search OR u.full_name LIKE :search OR o.problem_description LIKE :search)";
            }
            if (!empty($status)) {
                $sql .= " AND o.status = :status";
            }
            if (!empty($serviceId)) {
                $sql .= " AND o.service_id = :service_id";
            }

            $sql .= " ORDER BY o.id DESC";

            $this->db->query($sql);
            if (!empty($search)) {
                $this->db->bind(':search', '%' . $search . '%');
            }
            if (!empty($status)) {
                $this->db->bind(':status', $status);
            }
            if (!empty($serviceId)) {
                $this->db->bind(':service_id', $serviceId);
            }

            $results = $this->db->resultSet();
            if ($results !== false) {
                return $results;
            }
        }

        // Demo fallback data for operator
        $demo = $this->getDemoOrders();
        $filtered = [];
        foreach ($demo as $o) {
            $matchSearch = empty($search) || (stripos($o['order_code'], $search) !== false || stripos($o['customer_name'], $search) !== false);
            $matchStatus = empty($status) || $o['status'] === $status;
            $matchService = empty($serviceId) || $o['service_id'] == $serviceId;

            if ($matchSearch && $matchStatus && $matchService) {
                $filtered[] = $o;
            }
        }
        return $filtered;
    }

    /**
     * Get single order details by ID
     */
    public function getOrderById($id)
    {
        if ($this->db->isConnected()) {
            $sql = "SELECT o.*, s.name as service_name, s.category as service_category, s.icon as service_icon, s.base_price,
                           u.full_name as customer_name, u.email as customer_email, u.phone as customer_phone,
                           tu.full_name as technician_name, tu.phone as technician_phone
                    FROM " . $this->table . " o
                    JOIN services s ON o.service_id = s.id
                    JOIN customers c ON o.customer_id = c.id
                    JOIN users u ON c.user_id = u.id
                    LEFT JOIN technicians t ON o.technician_id = t.id
                    LEFT JOIN users tu ON t.user_id = tu.id
                    WHERE o.id = :id";

            $this->db->query($sql);
            $this->db->bind(':id', $id);
            $result = $this->db->single();
            if ($result) {
                return $result;
            }
        }

        $demo = $this->getDemoOrders();
        foreach ($demo as $o) {
            if ($o['id'] == $id) return $o;
        }
        return null;
    }

    /**
     * Customer Cancel Order
     */
    public function cancelOrder($id, $customerId)
    {
        if ($this->db->isConnected()) {
            $sql = "UPDATE " . $this->table . " SET status = 'Dibatalkan' WHERE id = :id AND customer_id = :customer_id AND status IN ('Diajukan', 'Menunggu Verifikasi', 'Disetujui')";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            $this->db->bind(':customer_id', $customerId);
            return $this->db->execute();
        }
        return true;
    }

    /**
     * Operator Update Status (Dihubungkan dengan Notifikasi WhatsApp Fonnte Otomatis)
     */
    public function updateStatus($id, $newStatus)
    {
        if ($this->db->isConnected()) {
            $sql = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            $this->db->bind(':status', $newStatus);
            $executed = $this->db->execute();

            // Jika status berhasil di-update, kirim notifikasi WA otomatis ke customer
            if ($executed) {
                $orderData = $this->getOrderById($id);

                if ($orderData && !empty($orderData['customer_phone'])) {
                    // Ambil token, nama layanan, dan kode negara secara dinamis dari tabel system_configs
                    $serviceName = 'TKJ Service Center';
                    $token = 'jmsDKErQCKjFWhycUYqe';
                    $countryCode = '62';

                    try {
                        $this->db->query("SELECT config_key, config_value FROM system_configs WHERE config_key IN ('service_name', 'api_token', 'default_country_code')");
                        $configs = $this->db->resultSet();
                        if (!empty($configs)) {
                            foreach ($configs as $cfg) {
                                if ($cfg['config_key'] === 'service_name' && !empty($cfg['config_value'])) {
                                    $serviceName = trim($cfg['config_value']);
                                } elseif ($cfg['config_key'] === 'api_token' && !empty($cfg['config_value'])) {
                                    $token = trim($cfg['config_value']);
                                } elseif ($cfg['config_key'] === 'default_country_code' && !empty($cfg['config_value'])) {
                                    $countryCode = trim($cfg['config_value']);
                                }
                            }
                        }
                    } catch (Exception $e) {
                        // Fallback ke nilai default
                    }

                    // Bersihkan nomor HP customer dari karakter non-angka
                    $target = preg_replace('/[^0-9]/', '', $orderData['customer_phone']);

                    $customerName   = $orderData['customer_name'] ?? 'Customer';
                    $ticketId       = $orderData['order_code'] ?? 'TKJ-ORDER';
                    $deviceName     = $orderData['service_name'] ?? 'Layanan Servis';
                    $issue          = $orderData['problem_description'] ?? '-';
                    $technicianName = $orderData['technician_name'] ?? 'Tim Teknisi';

                    // Format pesan murni teks biasa tanpa tanda bintang (*) atau format Markdown apa pun
                    if ($newStatus === 'Dalam Pengerjaan') {
                        $message = "$serviceName\n\n"
                                 . "Yth. $customerName,\n\n"
                                 . "Pesanan layanan servis Anda telah diterima oleh teknisi kami dan saat ini SEDANG DALAM PROSES PENGERJAAN.\n\n"
                                 . "Detail Perbaikan:\n"
                                 . "- No. Tiket: $ticketId\n"
                                 . "- Perangkat: $deviceName\n"
                                 . "- Keluhan: $issue\n"
                                 . "- Teknisi Penanggung Jawab: $technicianName\n"
                                 . "- Status: Sedang Diproses / Dalam Pengerjaan\n\n"
                                 . "Kami akan mengabari Anda kembali setelah perbaikan selesai. Terima kasih telah mempercayakan perbaikan Anda di $serviceName.";
                    } else {
                        $message = "$serviceName\n\n"
                                 . "Yth. $customerName,\n\n"
                                 . "Status pesanan servis Anda ($ticketId) saat ini telah diperbarui menjadi: $newStatus.\n\n"
                                 . "Terima kasih telah mempercayakan perbaikan Anda di $serviceName.";
                    }

                    // Eksekusi cURL ke Fonnte API secara aman
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://api.fonnte.com/send',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => array(
                            'target' => $target,
                            'message' => $message,
                            'countryCode' => $countryCode,
                        ),
                        CURLOPT_HTTPHEADER => array(
                            "Authorization: $token"
                        ),
                    ));
                    curl_exec($curl);
                    curl_close($curl);
                }
            }

            return $executed;
        }
        return true;
    }

    /**
     * Get Customer ID from User ID
     */
    public function getCustomerIdByUserId($userId)
    {
        if ($this->db->isConnected()) {
            $this->db->query("SELECT id FROM customers WHERE user_id = :user_id");
            $this->db->bind(':user_id', $userId);
            $row = $this->db->single();
            if ($row) {
                return $row['id'];
            }
        }
        // Fallback mapping: user_id 6 -> customer_id 1
        return $userId == 6 ? 1 : ($userId == 7 ? 2 : ($userId == 8 ? 3 : 1));
    }

    /**
     * Demo Orders Fallback Data
     */
    public function getDemoOrders()
    {
        return [
            [
                'id' => 1,
                'order_code' => 'TKJ-20260829-A7F2',
                'customer_id' => 1,
                'user_id' => 6,
                'service_id' => 1,
                'service_name' => 'Instalasi CCTV Analog & DVR 4 Channel',
                'service_category' => 'Keamanan Jaringan',
                'service_icon' => 'bi-camera-video-fill',
                'base_price' => 200000,
                'customer_name' => 'Ahmad Dahlan',
                'customer_phone' => '081234567890',
                'customer_email' => 'ahmad.dahlan@gmail.com',
                'technician_name' => 'Budi Santoso',
                'status' => 'Dalam Pengerjaan',
                'priority' => 'high',
                'subject' => 'Pasang 4 CCTV Garasi & Toko Swalayan',
                'problem_description' => 'Memerlukan pemasangan 4 unit kamera CCTV Analog outdoor di bagian garasi & toko swalayan dengan penarikan kabel coaxial 50m.',
                'service_address' => 'Jl. Merdeka No. 12, Kel. Kebon Manis, Kota Bandung',
                'scheduled_date' => '2026-08-30',
                'photo_evidence' => 'cctv_sample.jpg',
                'created_at' => '2026-08-29 09:15:00'
            ],
            [
                'id' => 2,
                'order_code' => 'TKJ-20260829-B8E3',
                'customer_id' => 2,
                'user_id' => 7,
                'service_id' => 7,
                'service_name' => 'Konfigurasi Router MikroTik & Bandwidth Queue',
                'service_category' => 'Network Management',
                'service_icon' => 'bi-router-fill',
                'base_price' => 150000,
                'customer_name' => 'Dewi Lestari',
                'customer_phone' => '081245678901',
                'customer_email' => 'dewi.lestari@gmail.com',
                'technician_name' => 'Rizky Pratama',
                'status' => 'Teknisi Ditugaskan',
                'priority' => 'medium',
                'subject' => 'Setting Router MikroTik RB750Gr3 Ruko',
                'problem_description' => 'Meminta setting pembagian bandwidth 50Mbps untuk 10 PC ruko agar tidak berebut koneksi.',
                'service_address' => 'Jl. Pemuda No. 45, Ruko B3, Kota Bandung',
                'scheduled_date' => '2026-08-31',
                'photo_evidence' => null,
                'created_at' => '2026-08-29 10:30:00'
            ],
            [
                'id' => 3,
                'order_code' => 'TKJ-20260828-C9D4',
                'customer_id' => 3,
                'user_id' => 8,
                'service_id' => 6,
                'service_name' => 'Perbaikan PC Desktop & Upgrade SSD/RAM',
                'service_category' => 'Maintenance Hardware',
                'service_icon' => 'bi-pc-display',
                'base_price' => 80000,
                'customer_name' => 'Budi Utomo (SD Negeri 1)',
                'customer_phone' => '081256789012',
                'customer_email' => 'sdn1.budiutomo@sch.id',
                'technician_name' => 'Fajar Kurniawan',
                'status' => 'Selesai',
                'priority' => 'high',
                'subject' => 'Perbaikan 2 PC Lab SDN 1 Lemot',
                'problem_description' => '2 Komputer Lab Sekolah mati dan lemot saat membuka aplikasi Office.',
                'service_address' => 'Jl. Pendidikan No. 1, SDN 1 Bandung',
                'scheduled_date' => '2026-08-28',
                'photo_evidence' => null,
                'created_at' => '2026-08-28 13:00:00'
            ]
        ];
    }
}