<?php
/**
 * TechnicianModel Class
 * Handles Technician profiles, scheduling, and conflict prevention
 */

class TechnicianModel
{
    private $db;
    private $table = 'technicians';

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all technicians with user details
     */
    public function getAllTechnicians()
    {
        if ($this->db->isConnected()) {
            $sql = "SELECT t.*, u.full_name as name, u.email, u.phone, u.username
                    FROM " . $this->table . " t
                    JOIN users u ON t.user_id = u.id
                    ORDER BY t.id ASC";

            $this->db->query($sql);
            $results = $this->db->resultSet();
            if (!empty($results)) {
                return $results;
            }
        }

        return $this->getDemoTechnicians();
    }

    /**
     * Get single technician by User ID
     */
    public function getTechnicianByUserId($userId)
    {
        if ($this->db->isConnected()) {
            $sql = "SELECT t.*, u.full_name as name, u.email, u.phone
                    FROM " . $this->table . " t
                    JOIN users u ON t.user_id = u.id
                    WHERE t.user_id = :user_id";
            $this->db->query($sql);
            $this->db->bind(':user_id', $userId);
            $result = $this->db->single();
            if ($result) {
                return $result;
            }
        }

        $demo = $this->getDemoTechnicians();
        foreach ($demo as $tech) {
            if ($tech['user_id'] == $userId) return $tech;
        }
        return $demo[0];
    }

    /**
     * CONFLICT SCHEDULE CHECK: Check if technician already has a job on workDate with overlapping time [startTime, endTime]
     */
    public function checkScheduleConflict($technicianId, $workDate, $startTime, $endTime, $ignoreOrderId = null)
    {
        if ($this->db->isConnected()) {
            $sql = "SELECT ws.*, o.order_code
                    FROM work_schedules ws
                    JOIN service_orders o ON ws.order_id = o.id
                    WHERE ws.technician_id = :technician_id
                      AND ws.work_date = :work_date
                      AND ws.status IN ('scheduled', 'ongoing')
                      AND ((ws.start_time < :end_time) AND (ws.end_time > :start_time))";

            if ($ignoreOrderId) {
                $sql .= " AND ws.order_id != :ignore_order_id";
            }

            $this->db->query($sql);
            $this->db->bind(':technician_id', $technicianId);
            $this->db->bind(':work_date', $workDate);
            $this->db->bind(':start_time', $startTime);
            $this->db->bind(':end_time', $endTime);
            if ($ignoreOrderId) {
                $this->db->bind(':ignore_order_id', $ignoreOrderId);
            }

            $conflicts = $this->db->resultSet();
            if (!empty($conflicts)) {
                return $conflicts[0]; // Conflict detected!
            }
        }
        return false; // No conflict
    }

    /**
     * Assign technician & create schedule
     */
    public function assignTechnicianSchedule($orderId, $technicianId, $workDate, $startTime, $endTime, $notes = '')
    {
        if ($this->db->isConnected()) {
            // 1. Update service_orders table
            $sqlOrder = "UPDATE service_orders 
                         SET technician_id = :technician_id, scheduled_date = :scheduled_date, status = 'Teknisi Ditugaskan' 
                         WHERE id = :order_id";
            $this->db->query($sqlOrder);
            $this->db->bind(':technician_id', $technicianId);
            $this->db->bind(':scheduled_date', $workDate);
            $this->db->bind(':order_id', $orderId);
            $this->db->execute();

            // 2. Insert into work_schedules table
            $sqlSchedule = "INSERT INTO work_schedules (technician_id, order_id, work_date, start_time, end_time, status, notes, created_at)
                            VALUES (:technician_id, :order_id, :work_date, :start_time, :end_time, 'scheduled', :notes, NOW())";
            $this->db->query($sqlSchedule);
            $this->db->bind(':technician_id', $technicianId);
            $this->db->bind(':order_id', $orderId);
            $this->db->bind(':work_date', $workDate);
            $this->db->bind(':start_time', $startTime);
            $this->db->bind(':end_time', $endTime);
            $this->db->bind(':notes', $notes);
            return $this->db->execute();
        }
        return true;
    }

    /**
     * Get assigned jobs for a technician by User ID
     */
    public function getAssignedJobsByTechnician($userId)
    {
        $tech = $this->getTechnicianByUserId($userId);
        $techId = $tech['id'] ?? 1;

        if ($this->db->isConnected()) {
            $sql = "SELECT o.*, COALESCE(s.name, 'Layanan Technical IT') as service_name, 
                           COALESCE(s.category, 'IT Service') as service_category, 
                           COALESCE(s.icon, 'bi-wrench') as service_icon,
                           u.full_name as customer_name, u.phone as customer_phone,
                           ws.start_time, ws.end_time, ws.notes as operator_notes
                    FROM service_orders o
                    LEFT JOIN order_details od ON o.id = od.order_id
                    LEFT JOIN services s ON od.service_id = s.id
                    JOIN customers c ON o.customer_id = c.id
                    JOIN users u ON c.user_id = u.id
                    LEFT JOIN work_schedules ws ON o.id = ws.order_id
                    WHERE o.technician_id = :technician_id
                    GROUP BY o.id
                    ORDER BY o.id DESC";

            $this->db->query($sql);
            $this->db->bind(':technician_id', $techId);
            $results = $this->db->resultSet();
            if ($results !== false) {
                return $results;
            }
        }

        // Fallback demo jobs for technician
        $orderModel = new OrderModel();
        return $orderModel->getDemoOrders();
    }

    /**
     * Get work schedules agenda for a technician
     */
    public function getSchedulesByTechnician($userId)
    {
        $tech = $this->getTechnicianByUserId($userId);
        $techId = $tech['id'] ?? 1;

        if ($this->db->isConnected()) {
            $sql = "SELECT ws.*, o.order_code, COALESCE(s.name, 'Layanan Technical IT') as service_name, u.full_name as customer_name
                    FROM work_schedules ws
                    JOIN service_orders o ON ws.order_id = o.id
                    LEFT JOIN order_details od ON o.id = od.order_id
                    LEFT JOIN services s ON od.service_id = s.id
                    JOIN customers c ON o.customer_id = c.id
                    JOIN users u ON c.user_id = u.id
                    WHERE ws.technician_id = :technician_id
                    GROUP BY ws.id
                    ORDER BY ws.work_date ASC, ws.start_time ASC";

            $this->db->query($sql);
            $this->db->bind(':technician_id', $techId);
            $results = $this->db->resultSet();
            if ($results !== false) {
                return $results;
            }
        }

        return [
            [
                'id' => 1,
                'technician_id' => $techId,
                'order_id' => 1,
                'order_code' => 'TKJ-20260829-A7F2',
                'service_name' => 'Instalasi CCTV Analog & DVR 4 Channel',
                'customer_name' => 'Ahmad Dahlan',
                'work_date' => '2026-08-30',
                'start_time' => '09:00:00',
                'end_time' => '13:00:00',
                'status' => 'scheduled',
                'notes' => 'Bawa bor listrik, kabel coaxial 50m, & BNC connector'
            ]
        ];
    }

    /**
     * Demo Technicians Data
     */
    public function getDemoTechnicians()
    {
        return [
            [
                'id' => 1,
                'user_id' => 3,
                'name' => 'Budi Santoso',
                'username' => 'teknisi',
                'email' => 'budi.teknisi@tkj.sch.id',
                'phone' => '085711223344',
                'nis_nip' => '22231001',
                'class_grade' => 'XII TKJ 1',
                'specialization' => 'Instalasi CCTV & Fiber Optic',
                'skill_level' => 'senior',
                'status' => 'available'
            ],
            [
                'id' => 2,
                'user_id' => 4,
                'name' => 'Rizky Pratama',
                'username' => 'teknisi2',
                'email' => 'rizky.teknisi@tkj.sch.id',
                'phone' => '085722334455',
                'nis_nip' => '22231015',
                'class_grade' => 'XII TKJ 1',
                'specialization' => 'Konfigurasi Router MikroTik & Firewall',
                'skill_level' => 'senior',
                'status' => 'on_duty'
            ],
            [
                'id' => 3,
                'user_id' => 5,
                'name' => 'Fajar Kurniawan',
                'username' => 'teknisi3',
                'email' => 'fajar.teknisi@tkj.sch.id',
                'phone' => '085733445566',
                'nis_nip' => '22231028',
                'class_grade' => 'XII TKJ 2',
                'specialization' => 'Hardware PC/Laptop & Troubleshooting OS',
                'skill_level' => 'junior',
                'status' => 'available'
            ]
        ];
    }
}
