<?php
/**
 * SparepartModel Class
 * Handles database operations for Hardware & Network Sparepart Inventory
 */

class SparepartModel
{
    private $db;
    private $table = 'spareparts';

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all spareparts with search and category filter
     */
    public function getAllSpareparts($search = '', $category = '')
    {
        if ($this->db->isConnected()) {
            $sql = "SELECT * FROM " . $this->table . " WHERE 1=1";
            if (!empty($search)) {
                $sql .= " AND (part_code LIKE :search OR name LIKE :search OR category LIKE :search)";
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

        // Demo fallback data
        $demo = $this->getDemoSpareparts();
        $filtered = [];
        foreach ($demo as $p) {
            $matchSearch = empty($search) || (stripos($p['part_code'], $search) !== false || stripos($p['name'], $search) !== false);
            $matchCategory = empty($category) || $p['category'] === $category;
            if ($matchSearch && $matchCategory) {
                $filtered[] = $p;
            }
        }
        return $filtered;
    }

    /**
     * Get single sparepart by ID
     */
    public function getById($id)
    {
        if ($this->db->isConnected()) {
            $this->db->query("SELECT * FROM " . $this->table . " WHERE id = :id");
            $this->db->bind(':id', $id);
            $result = $this->db->single();
            if ($result) {
                return $result;
            }
        }

        $demo = $this->getDemoSpareparts();
        foreach ($demo as $p) {
            if ($p['id'] == $id) return $p;
        }
        return null;
    }

    /**
     * Create sparepart
     */
    public function create($data)
    {
        if ($this->db->isConnected()) {
            $sql = "INSERT INTO " . $this->table . " (part_code, name, category, stock_qty, unit, unit_cost, selling_price, min_stock)
                    VALUES (:part_code, :name, :category, :stock_qty, :unit, :unit_cost, :selling_price, :min_stock)";
            $this->db->query($sql);
            $this->db->bind(':part_code', $data['part_code']);
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':category', $data['category']);
            $this->db->bind(':stock_qty', $data['stock_qty']);
            $this->db->bind(':unit', $data['unit'] ?? 'pcs');
            $this->db->bind(':unit_cost', $data['unit_cost'] ?? 0);
            $this->db->bind(':selling_price', $data['selling_price']);
            $this->db->bind(':min_stock', $data['min_stock'] ?? 5);

            return $this->db->execute();
        }
        return true;
    }

    /**
     * Update sparepart
     */
    public function update($id, $data)
    {
        if ($this->db->isConnected()) {
            $sql = "UPDATE " . $this->table . " 
                    SET part_code = :part_code, name = :name, category = :category, stock_qty = :stock_qty, 
                        unit = :unit, unit_cost = :unit_cost, selling_price = :selling_price, min_stock = :min_stock 
                    WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            $this->db->bind(':part_code', $data['part_code']);
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':category', $data['category']);
            $this->db->bind(':stock_qty', $data['stock_qty']);
            $this->db->bind(':unit', $data['unit'] ?? 'pcs');
            $this->db->bind(':unit_cost', $data['unit_cost'] ?? 0);
            $this->db->bind(':selling_price', $data['selling_price']);
            $this->db->bind(':min_stock', $data['min_stock'] ?? 5);

            return $this->db->execute();
        }
        return true;
    }

    /**
     * Delete sparepart
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
     * Demo Spareparts Seed Data
     */
    public function getDemoSpareparts()
    {
        return [
            [
                'id' => 1,
                'part_code' => 'PRT-CBL-CAT6',
                'name' => 'Kabel UTP Belden Cat6 High Grade',
                'category' => 'Kabel & Connector',
                'stock_qty' => 300,
                'unit' => 'meter',
                'unit_cost' => 4500,
                'selling_price' => 7000,
                'min_stock' => 50,
                'status' => 'Tersedia'
            ],
            [
                'id' => 2,
                'part_code' => 'PRT-RJ45-CAT6',
                'name' => 'Konektor RJ45 Cat6 Pass Through',
                'category' => 'Kabel & Connector',
                'stock_qty' => 200,
                'unit' => 'pcs',
                'unit_cost' => 1500,
                'selling_price' => 3000,
                'min_stock' => 20,
                'status' => 'Tersedia'
            ],
            [
                'id' => 3,
                'part_code' => 'PRT-SSD-256G',
                'name' => 'SSD NVMe V-Gen M.2 256GB',
                'category' => 'Storage SSD',
                'stock_qty' => 10,
                'unit' => 'unit',
                'unit_cost' => 260000,
                'selling_price' => 320000,
                'min_stock' => 3,
                'status' => 'Tersedia'
            ],
            [
                'id' => 4,
                'part_code' => 'PRT-CCTV-CAM',
                'name' => 'Kamera CCTV Hikvision 2MP Outdoor',
                'category' => 'Kamera CCTV',
                'stock_qty' => 8,
                'unit' => 'unit',
                'unit_cost' => 180000,
                'selling_price' => 240000,
                'min_stock' => 2,
                'status' => 'Tersedia'
            ],
            [
                'id' => 5,
                'part_code' => 'PRT-RTR-GR3',
                'name' => 'MikroTik RouterBOARD RB750Gr3 hEX',
                'category' => 'Router',
                'stock_qty' => 4,
                'unit' => 'unit',
                'unit_cost' => 650000,
                'selling_price' => 780000,
                'min_stock' => 1,
                'status' => 'Tersedia'
            ]
        ];
    }
}
