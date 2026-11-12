<?php
/**
 * ServiceModel Class
 * Handles database operations for TKJ Technical Services Catalog
 */

class ServiceModel
{
    private $db;
    private $table = 'services';

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Fetch services with optional search, category filter, and active status restriction
     */
    public function getAllServices($search = '', $category = '', $activeOnly = true)
    {
        if ($this->db->isConnected()) {
            $sql = "SELECT *, name as title, base_price as price_estimate FROM " . $this->table . " WHERE 1=1";
            
            if ($activeOnly) {
                $sql .= " AND is_active = 1";
            }
            if (!empty($search)) {
                $sql .= " AND (name LIKE :search OR description LIKE :search OR code LIKE :search)";
            }
            if (!empty($category)) {
                $sql .= " AND category = :category";
            }
            
            $sql .= " ORDER BY id DESC";

            $this->db->query($sql);

            if (!empty($search)) {
                $this->db->bind(':search', '%' . $search . '%');
            }
            if (!empty($category)) {
                $this->db->bind(':category', $category);
            }

            $results = $this->db->resultSet();
            if ($results !== false) {
                return $results;
            }
        }

        // Demo fallback data filter
        $demo = $this->getDemoServices();
        $filtered = [];

        foreach ($demo as $s) {
            $matchActive = $activeOnly ? ($s['is_active'] ?? 1) == 1 : true;
            $matchSearch = empty($search) || (stripos($s['title'], $search) !== false || stripos($s['description'], $search) !== false);
            $matchCategory = empty($category) || $s['category'] === $category;

            if ($matchActive && $matchSearch && $matchCategory) {
                $filtered[] = $s;
            }
        }

        return $filtered;
    }

    /**
     * Get unique categories
     */
    public function getCategories()
    {
        if ($this->db->isConnected()) {
            $this->db->query("SELECT DISTINCT category FROM " . $this->table . " WHERE is_active = 1 ORDER BY category ASC");
            $rows = $this->db->resultSet();
            if (!empty($rows)) {
                return array_column($rows, 'category');
            }
        }

        return ['Keamanan Jaringan', 'Infrastruktur Network', 'Maintenance Hardware', 'Network Management', 'Software & OS'];
    }

    /**
     * Get single service by ID
     */
    public function getById($id)
    {
        if ($this->db->isConnected()) {
            $this->db->query("SELECT *, name as title, base_price as price_estimate FROM " . $this->table . " WHERE id = :id");
            $this->db->bind(':id', $id);
            $result = $this->db->single();
            if ($result) {
                return $result;
            }
        }

        $demo = $this->getDemoServices();
        foreach ($demo as $s) {
            if ($s['id'] == $id) return $s;
        }
        return null;
    }

    /**
     * Create new service
     */
    public function create($data)
    {
        if ($this->db->isConnected()) {
            $sql = "INSERT INTO " . $this->table . " (code, name, category, description, base_price, estimated_duration_hours, icon, is_active) 
                    VALUES (:code, :name, :category, :description, :base_price, :estimated_duration_hours, :icon, :is_active)";
            $this->db->query($sql);
            $this->db->bind(':code', $data['code']);
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':category', $data['category']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':base_price', $data['base_price']);
            $this->db->bind(':estimated_duration_hours', $data['estimated_duration_hours'] ?? 2);
            $this->db->bind(':icon', $data['icon'] ?? 'bi-wrench');
            $this->db->bind(':is_active', $data['is_active'] ?? 1);
            
            return $this->db->execute();
        }
        return true;
    }

    /**
     * Update service
     */
    public function update($id, $data)
    {
        if ($this->db->isConnected()) {
            $sql = "UPDATE " . $this->table . " 
                    SET code = :code, name = :name, category = :category, description = :description, 
                        base_price = :base_price, estimated_duration_hours = :estimated_duration_hours, 
                        icon = :icon, is_active = :is_active 
                    WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            $this->db->bind(':code', $data['code']);
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':category', $data['category']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':base_price', $data['base_price']);
            $this->db->bind(':estimated_duration_hours', $data['estimated_duration_hours'] ?? 2);
            $this->db->bind(':icon', $data['icon'] ?? 'bi-wrench');
            $this->db->bind(':is_active', $data['is_active'] ?? 1);

            return $this->db->execute();
        }
        return true;
    }

    /**
     * Toggle active/inactive status
     */
    public function toggleStatus($id)
    {
        if ($this->db->isConnected()) {
            $sql = "UPDATE " . $this->table . " SET is_active = IF(is_active=1, 0, 1) WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            return $this->db->execute();
        }
        return true;
    }

    /**
     * Delete service
     */
    public function delete($id)
    {
        if ($this->db->isConnected()) {
            $sql = "DELETE FROM " . $this->table . " WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            return $this->db->execute();
        }
        return true;
    }

    /**
     * Demo Services Seed Data
     */
    public function getDemoServices()
    {
        return [
            [
                'id' => 1,
                'code' => 'SVC-CCTV-01',
                'title' => 'Instalasi CCTV Analog & DVR 4 Channel',
                'name' => 'Instalasi CCTV Analog & DVR 4 Channel',
                'category' => 'Keamanan Jaringan',
                'description' => 'Pemasangan 4 unit kamera CCTV Analog, penarikan kabel coaxial, setting DVR, dan konfigurasi monitoring smartphone.',
                'price_estimate' => 200000,
                'base_price' => 200000,
                'estimated_duration_hours' => 4,
                'icon' => 'bi-camera-video-fill',
                'is_active' => 1
            ],
            [
                'id' => 2,
                'code' => 'SVC-CCTV-02',
                'title' => 'Instalasi IP Camera & NVR 8 Channel',
                'name' => 'Instalasi IP Camera & NVR 8 Channel',
                'category' => 'Keamanan Jaringan',
                'description' => 'Pemasangan IP Camera High Definition dengan Switch PoE, penarikan kabel UTP Cat6, NVR, & cloud setup.',
                'price_estimate' => 350000,
                'base_price' => 350000,
                'estimated_duration_hours' => 6,
                'icon' => 'bi-camera-reels-fill',
                'is_active' => 1
            ],
            [
                'id' => 3,
                'code' => 'SVC-NET-01',
                'title' => 'Instalasi & Cabling Jaringan LAN UTP/STP',
                'name' => 'Instalasi & Cabling Jaringan LAN UTP/STP',
                'category' => 'Infrastruktur Network',
                'description' => 'Pemasangan kabel UTP Cat6, crimping RJ45, penataan kabel trunking, faceplate wall, & pengujian cable tester.',
                'price_estimate' => 100000,
                'base_price' => 100000,
                'estimated_duration_hours' => 3,
                'icon' => 'bi-diagram-3-fill',
                'is_active' => 1
            ],
            [
                'id' => 4,
                'code' => 'SVC-NET-02',
                'title' => 'Pemasangan & Setting Access Point Wi-Fi',
                'name' => 'Pemasangan & Setting Access Point Wi-Fi',
                'category' => 'Infrastruktur Network',
                'description' => 'Instalasi Access Point ceiling/wall, konfigurasi SSID dual-band, roaming setup, & keamanan WPA3.',
                'price_estimate' => 120000,
                'base_price' => 120000,
                'estimated_duration_hours' => 2,
                'icon' => 'bi-wifi',
                'is_active' => 1
            ],
            [
                'id' => 5,
                'code' => 'SVC-HW-01',
                'title' => 'Service Laptop Mati Total & Overheat',
                'name' => 'Service Laptop Mati Total & Overheat',
                'category' => 'Maintenance Hardware',
                'description' => 'Pembongkaran laptop, pembersihan fan heatsink, ganti pasta thermal high-conductive, & repair motherboard ringan.',
                'price_estimate' => 150000,
                'base_price' => 150000,
                'estimated_duration_hours' => 3,
                'icon' => 'bi-laptop-fill',
                'is_active' => 1
            ],
            [
                'id' => 6,
                'code' => 'SVC-HW-02',
                'title' => 'Perbaikan PC Desktop & Upgrade SSD/RAM',
                'name' => 'Perbaikan PC Desktop & Upgrade SSD/RAM',
                'category' => 'Maintenance Hardware',
                'description' => 'Perbaikan PC mati/bluescreen, instalasi SSD NVMe/SATA, cloning sistem operasi, & upgrade RAM.',
                'price_estimate' => 80000,
                'base_price' => 80000,
                'estimated_duration_hours' => 2,
                'icon' => 'bi-pc-display',
                'is_active' => 1
            ],
            [
                'id' => 7,
                'code' => 'SVC-RTR-01',
                'title' => 'Konfigurasi Router MikroTik & Bandwidth Queue',
                'name' => 'Konfigurasi Router MikroTik & Bandwidth Queue',
                'category' => 'Network Management',
                'description' => 'Setting dasar MikroTik (WAN/LAN), Firewall Filter, NAT, Bandwidth Simple Queue, & DNS Server.',
                'price_estimate' => 150000,
                'base_price' => 150000,
                'estimated_duration_hours' => 2,
                'icon' => 'bi-router-fill',
                'is_active' => 1
            ],
            [
                'id' => 8,
                'code' => 'SVC-RTR-02',
                'title' => 'Setting Hotspot Voucher & Radius Server MikroTik',
                'name' => 'Setting Hotspot Voucher & Radius Server MikroTik',
                'category' => 'Network Management',
                'description' => 'Konfigurasi portal Wi-Fi Hotspot voucher, template login custom, user manager radius, & limiting paket jam/quota.',
                'price_estimate' => 250000,
                'base_price' => 250000,
                'estimated_duration_hours' => 3,
                'icon' => 'bi-ticket-detailed-fill',
                'is_active' => 1
            ],
            [
                'id' => 9,
                'code' => 'SVC-SW-01',
                'title' => 'Instalasi Software Office, OS Windows/Linux & Antivirus',
                'name' => 'Instalasi Software Office, OS Windows/Linux & Antivirus',
                'category' => 'Software & OS',
                'description' => 'Install ulang OS Windows 11 / Linux Ubuntu LTS, driver resmi, MS Office, PDF Reader, & antivirus terupdate.',
                'price_estimate' => 60000,
                'base_price' => 60000,
                'estimated_duration_hours' => 2,
                'icon' => 'bi-cpu-fill',
                'is_active' => 1
            ]
        ];
    }
}
