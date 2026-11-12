<?php
/**
 * RatingModel Class
 * Manages Customer Ratings, Comments, Service Feedback, and Statistical Analytics
 */

class RatingModel
{
    private $db;
    private $table = 'ratings';

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Create/Store a new rating from customer (Strict 1 rating per order)
     */
    public function createRating($data)
    {
        if (!$this->db->isConnected()) {
            return false;
        }

        // Prevent duplicate rating
        if ($this->hasCustomerRatedOrder($data['order_id'])) {
            return false;
        }

        $sql = "INSERT INTO " . $this->table . " (order_id, customer_id, technician_id, stars, comment, created_at)
                VALUES (:order_id, :customer_id, :technician_id, :stars, :comment, NOW())";

        $commentCombined = trim($data['comment'] ?? '');
        $feedback = trim($data['service_feedback'] ?? '');
        if (!empty($feedback)) {
            $commentCombined .= "\n\n[FEEDBACK LAYANAN]: " . $feedback;
        }

        $this->db->query($sql);
        $this->db->bind(':order_id', $data['order_id']);
        $this->db->bind(':customer_id', $data['customer_id']);
        $this->db->bind(':technician_id', $data['technician_id'] ?? null);
        $this->db->bind(':stars', intval($data['stars']));
        $this->db->bind(':comment', $commentCombined);

        return $this->db->execute();
    }

    /**
     * Check if an order has already been rated by customer
     */
    public function hasCustomerRatedOrder($orderId)
    {
        if (!$this->db->isConnected()) {
            return false;
        }

        $sql = "SELECT id FROM " . $this->table . " WHERE order_id = :order_id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':order_id', $orderId);
        $row = $this->db->single();

        return !empty($row);
    }

    /**
     * Get rating details by Order ID
     */
    public function getRatingByOrderId($orderId)
    {
        if (!$this->db->isConnected()) {
            return null;
        }

        $sql = "SELECT r.*, 
                       u_cust.full_name as customer_name, u_cust.avatar as customer_avatar,
                       u_tech.full_name as technician_name,
                       so.order_code, s.name as service_name
                FROM " . $this->table . " r
                LEFT JOIN customers c ON r.customer_id = c.id
                LEFT JOIN users u_cust ON c.user_id = u_cust.id
                LEFT JOIN technicians t ON r.technician_id = t.id
                LEFT JOIN users u_tech ON t.user_id = u_tech.id
                LEFT JOIN service_orders so ON r.order_id = so.id
                LEFT JOIN order_details od ON so.id = od.order_id
                LEFT JOIN services s ON od.service_id = s.id
                WHERE r.order_id = :order_id
                ORDER BY r.id DESC LIMIT 1";

        $this->db->query($sql);
        $this->db->bind(':order_id', $orderId);
        $rating = $this->db->single();

        if ($rating) {
            return $this->parseRatingFields($rating);
        }

        return null;
    }

    /**
     * Helper to split comment and service_feedback
     */
    private function parseRatingFields($rating)
    {
        $rawComment = $rating['comment'] ?? '';
        $comment = $rawComment;
        $feedback = '';

        if (strpos($rawComment, '[FEEDBACK LAYANAN]:') !== false) {
            $parts = explode('[FEEDBACK LAYANAN]:', $rawComment);
            $comment = trim($parts[0] ?? '');
            $feedback = trim($parts[1] ?? '');
        }

        $rating['user_comment'] = $comment;
        $rating['service_feedback'] = $feedback;

        return $rating;
    }

    /**
     * Get all ratings with optional filtering for Operator & Kepsek
     */
    public function getAllRatings($search = '', $stars = '', $techId = '', $serviceId = '')
    {
        if (!$this->db->isConnected()) {
            return $this->getDemoRatings();
        }

        $sql = "SELECT r.*, 
                       u_cust.full_name as customer_name, u_cust.phone as customer_phone,
                       u_tech.full_name as technician_name,
                       so.order_code, s.name as service_name, s.category as service_category
                FROM " . $this->table . " r
                LEFT JOIN customers c ON r.customer_id = c.id
                LEFT JOIN users u_cust ON c.user_id = u_cust.id
                LEFT JOIN technicians t ON r.technician_id = t.id
                LEFT JOIN users u_tech ON t.user_id = u_tech.id
                LEFT JOIN service_orders so ON r.order_id = so.id
                LEFT JOIN order_details od ON so.id = od.order_id
                LEFT JOIN services s ON od.service_id = s.id
                WHERE 1=1";

        if (!empty($search)) {
            $sql .= " AND (so.order_code LIKE :search OR u_cust.full_name LIKE :search OR r.comment LIKE :search)";
        }
        if (!empty($stars)) {
            $sql .= " AND r.stars = :stars";
        }
        if (!empty($techId)) {
            $sql .= " AND r.technician_id = :techId";
        }
        if (!empty($serviceId)) {
            $sql .= " AND s.id = :serviceId";
        }

        $sql .= " ORDER BY r.id DESC";

        $this->db->query($sql);

        if (!empty($search)) $this->db->bind(':search', "%$search%");
        if (!empty($stars)) $this->db->bind(':stars', intval($stars));
        if (!empty($techId)) $this->db->bind(':techId', intval($techId));
        if (!empty($serviceId)) $this->db->bind(':serviceId', intval($serviceId));

        $results = $this->db->resultSet();
        if ($results !== false && !empty($results)) {
            $parsed = [];
            foreach ($results as $row) {
                $parsed[] = $this->parseRatingFields($row);
            }
            return $parsed;
        }

        return $this->getDemoRatings();
    }

    /**
     * Get overall average rating summary
     */
    public function getOverallStats()
    {
        if (!$this->db->isConnected()) {
            return [
                'avg_rating' => 4.8,
                'total_reviews' => 12,
                'satisfaction_percentage' => 95,
                'stars_breakdown' => [5 => 9, 4 => 2, 3 => 1, 2 => 0, 1 => 0]
            ];
        }

        $sql = "SELECT AVG(stars) as avg_stars, COUNT(id) as total_count FROM " . $this->table;
        $this->db->query($sql);
        $summary = $this->db->single();

        $avgStars = round($summary['avg_stars'] ?? 0, 1);
        $totalCount = intval($summary['total_count'] ?? 0);

        // Calculate breakdown by stars
        $sqlBreakdown = "SELECT stars, COUNT(id) as cnt FROM " . $this->table . " GROUP BY stars";
        $this->db->query($sqlBreakdown);
        $rows = $this->db->resultSet();

        $breakdown = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        $satisfiedCount = 0;

        if ($rows) {
            foreach ($rows as $r) {
                $st = intval($r['stars']);
                $cnt = intval($r['cnt']);
                $breakdown[$st] = $cnt;
                if ($st >= 4) {
                    $satisfiedCount += $cnt;
                }
            }
        }

        $satisfaction = $totalCount > 0 ? round(($satisfiedCount / $totalCount) * 100) : 100;

        return [
            'avg_rating' => $avgStars ?: 5.0,
            'total_reviews' => $totalCount,
            'satisfaction_percentage' => $satisfaction,
            'stars_breakdown' => $breakdown
        ];
    }

    /**
     * Get rating statistics broken down by technician
     */
    public function getRatingStatsByTechnician()
    {
        if (!$this->db->isConnected()) {
            return [
                [
                    'technician_id' => 1,
                    'technician_name' => 'Budi Santoso',
                    'specialization' => 'Instalasi CCTV & Fiber Optic',
                    'total_reviews' => 6,
                    'avg_rating' => 4.9,
                    'five_star_count' => 5
                ],
                [
                    'technician_id' => 2,
                    'technician_name' => 'Rizky Pratama',
                    'specialization' => 'Router MikroTik & Network',
                    'total_reviews' => 4,
                    'avg_rating' => 4.7,
                    'five_star_count' => 3
                ],
                [
                    'technician_id' => 3,
                    'technician_name' => 'Fajar Kurniawan',
                    'specialization' => 'Hardware PC & Laptop',
                    'total_reviews' => 2,
                    'avg_rating' => 5.0,
                    'five_star_count' => 2
                ]
            ];
        }

        $sql = "SELECT t.id as technician_id, u.full_name as technician_name, t.specialization,
                       COUNT(r.id) as total_reviews,
                       ROUND(AVG(r.stars), 1) as avg_rating,
                       SUM(CASE WHEN r.stars = 5 THEN 1 ELSE 0 END) as five_star_count
                FROM technicians t
                JOIN users u ON t.user_id = u.id
                LEFT JOIN ratings r ON t.id = r.technician_id
                GROUP BY t.id, u.full_name, t.specialization
                ORDER BY avg_rating DESC, total_reviews DESC";

        $this->db->query($sql);
        $results = $this->db->resultSet();

        return $results ?: [];
    }

    /**
     * Get rating statistics broken down by service category
     */
    public function getRatingStatsByService()
    {
        if (!$this->db->isConnected()) {
            return [
                [
                    'service_id' => 1,
                    'service_name' => 'Instalasi CCTV Analog & DVR',
                    'category' => 'Keamanan Jaringan',
                    'total_reviews' => 5,
                    'avg_rating' => 4.8
                ],
                [
                    'service_id' => 7,
                    'service_name' => 'Konfigurasi Router MikroTik',
                    'category' => 'Network Management',
                    'total_reviews' => 4,
                    'avg_rating' => 4.7
                ],
                [
                    'service_id' => 6,
                    'service_name' => 'Perbaikan PC Desktop & Upgrade',
                    'category' => 'Maintenance Hardware',
                    'total_reviews' => 3,
                    'avg_rating' => 5.0
                ]
            ];
        }

        $sql = "SELECT s.id as service_id, s.name as service_name, s.category,
                       COUNT(r.id) as total_reviews,
                       ROUND(AVG(r.stars), 1) as avg_rating
                FROM services s
                LEFT JOIN order_details od ON s.id = od.service_id
                LEFT JOIN service_orders so ON od.order_id = so.id
                LEFT JOIN ratings r ON so.id = r.order_id
                GROUP BY s.id, s.name, s.category
                ORDER BY avg_rating DESC, total_reviews DESC";

        $this->db->query($sql);
        $results = $this->db->resultSet();

        return $results ?: [];
    }

    /**
     * Demo ratings fallback
     */
    private function getDemoRatings()
    {
        return [
            [
                'id' => 1,
                'order_id' => 3,
                'order_code' => 'ORD-20260829-003',
                'customer_name' => 'Budi Utomo (SD Negeri 1)',
                'technician_name' => 'Fajar Kurniawan',
                'service_name' => 'Perbaikan PC Desktop & Upgrade SSD/RAM',
                'stars' => 5,
                'user_comment' => 'Pelayanan teknisi siswa TKJ sangat ramah, rapi, dan PC lab sekolah sekarang kembali cepat!',
                'service_feedback' => 'Mohon pertahankan respon cepat operator dan kerapihan penataan kabel.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ],
            [
                'id' => 2,
                'order_id' => 2,
                'order_code' => 'ORD-20260829-002',
                'customer_name' => 'Dewi Lestari',
                'technician_name' => 'Rizky Pratama',
                'service_name' => 'Konfigurasi Router MikroTik & Bandwidth Queue',
                'stars' => 5,
                'user_comment' => 'Setting router ruko sangat memuaskan, koneksi lancar tidak ada lag lagi.',
                'service_feedback' => 'Teknisi menjelaskannya dengan sopan dan detail.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
            ],
            [
                'id' => 3,
                'order_id' => 1,
                'order_code' => 'ORD-20260829-001',
                'customer_name' => 'Ahmad Dahlan',
                'technician_name' => 'Budi Santoso',
                'service_name' => 'Instalasi CCTV Analog & DVR 4 Channel',
                'stars' => 4,
                'user_comment' => 'Kamera jernih 1080p dan aplikasi HP langsung terpasang.',
                'service_feedback' => 'Waktu pengerjaan sedikit lebih lama dari estimasi namun hasil sangat rapi.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
            ]
        ];
    }
}
