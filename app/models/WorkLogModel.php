<?php
/**
 * WorkLogModel Class
 * Handles audit log trail & detailed technician work reports
 */

class WorkLogModel
{
    private $db;
    private $table = 'work_logs';

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Log status change automatically
     */
    public function logStatusChange($orderId, $technicianId, $logType, $description, $photoEvidence = null)
    {
        if ($this->db->isConnected()) {
            $sql = "INSERT INTO " . $this->table . " (order_id, technician_id, log_type, description, photo_evidence, logged_at, created_at)
                    VALUES (:order_id, :technician_id, :log_type, :description, :photo_evidence, NOW(), NOW())";
            
            $this->db->query($sql);
            $this->db->bind(':order_id', $orderId);
            $this->db->bind(':technician_id', $technicianId ?? 1);
            $this->db->bind(':log_type', $logType);
            $this->db->bind(':description', $description);
            $this->db->bind(':photo_evidence', $photoEvidence);

            return $this->db->execute();
        }
        return true;
    }

    /**
     * Save Detailed Work Report from Technician
     */
    public function saveDetailedReport($orderId, $technicianId, $reportData)
    {
        $aiFullReport = $reportData['ai_report_full'] ?? null;
        $recommendation = $reportData['recommendation'] ?? ($reportData['notes'] ?? 'Telah diserahterimakan kepada customer.');

        if (!empty($aiFullReport)) {
            $descriptionFormatted = "[LAPORAN KERJA TEKNISI PRO]\n" .
                "• Diagnosa: " . $reportData['diagnosis'] . "\n" .
                "• Pemeriksaan: " . $reportData['inspection'] . "\n" .
                "• Tindakan/Perbaikan: " . $reportData['action_taken'] . "\n" .
                "• Sparepart Terpakai: " . $reportData['spareparts_used'] . "\n" .
                "• Hasil Pekerjaan: " . $reportData['execution_result'] . "\n" .
                "• Catatan Teknisi: " . $reportData['notes'] . "\n" .
                "• Rekomendasi AI: " . $recommendation . "\n" .
                "--- DOKUMEN LAPORAN RESMI 7 BAGIAN ---\n" . $aiFullReport;
        } else {
            $descriptionFormatted = "[LAPORAN KERJA TEKNISI]\n" .
                "• Diagnosa: " . $reportData['diagnosis'] . "\n" .
                "• Pemeriksaan: " . $reportData['inspection'] . "\n" .
                "• Tindakan/Perbaikan: " . $reportData['action_taken'] . "\n" .
                "• Sparepart Terpakai: " . $reportData['spareparts_used'] . "\n" .
                "• Hasil Pekerjaan: " . $reportData['execution_result'] . "\n" .
                "• Catatan Teknisi: " . $reportData['notes'];
        }

        $photoEvidenceCombo = null;
        if (!empty($reportData['photo_before']) || !empty($reportData['photo_after'])) {
            $photoEvidenceCombo = ($reportData['photo_before'] ?? '') . '|' . ($reportData['photo_after'] ?? '');
        }

        return $this->logStatusChange($orderId, $technicianId, 'repair', $descriptionFormatted, $photoEvidenceCombo);
    }

    /**
     * Check if detailed work report exists for an order
     */
    public function hasDetailedReport($orderId)
    {
        if ($this->db->isConnected()) {
            $sql = "SELECT id FROM " . $this->table . " WHERE order_id = :order_id AND (description LIKE '[LAPORAN KERJA TEKNISI]%' OR description LIKE '[LAPORAN KERJA TEKNISI PRO]%') LIMIT 1";
            $this->db->query($sql);
            $this->db->bind(':order_id', $orderId);
            $row = $this->db->single();
            if ($row) return true;
        }
        return false;
    }

    /**
     * Get detailed work report data by Order ID
     */
    public function getReportByOrderId($orderId)
    {
        if ($this->db->isConnected()) {
            $sql = "SELECT wl.*, u.full_name as technician_name, u.phone as technician_phone
                    FROM " . $this->table . " wl
                    LEFT JOIN technicians t ON wl.technician_id = t.id
                    LEFT JOIN users u ON t.user_id = u.id
                    WHERE wl.order_id = :order_id AND (wl.description LIKE '[LAPORAN KERJA TEKNISI]%' OR wl.description LIKE '[LAPORAN KERJA TEKNISI PRO]%')
                    ORDER BY wl.id DESC LIMIT 1";

            $this->db->query($sql);
            $this->db->bind(':order_id', $orderId);
            $report = $this->db->single();
            if ($report) {
                return $this->parseReportFields($report);
            }
        }

        // Demo fallback report
        return [
            'id' => 99,
            'order_id' => $orderId,
            'technician_name' => 'Budi Santoso (Senior)',
            'diagnosis' => 'Kamera CCTV channel 2 & 3 mati akibat kabel coaxial tertekuk & power supply drop.',
            'inspection' => 'Pengecekan voltase power supply 12V dan pengujian kontinuitas sinyal kabel BNC.',
            'action_taken' => 'Penggantian konektor BNC jack, perbaikan isolasi kabel, dan penataan ulang jalur kabel garasi.',
            'spareparts_used' => '2 pcs BNC Connector Cat6, 5 meter Kabel Coaxial, 1 Roll Isolasi Listrik 3M.',
            'execution_result' => 'Semua 4 Kamera CCTV berfungsi normal, gambar jernih 1080p, dan remote view smartphone aktif.',
            'notes' => 'Disarankan tidak menumpuk barang berat di dekat jalur kabel garasi.',
            'recommendation' => 'Pastikan stopkontak daya listrik stabil dan terlindung dari barang berat.',
            'ai_report_full' => null,
            'photo_before' => 'sample_before.jpg',
            'photo_after' => 'sample_after.jpg',
            'logged_at' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Helper parser for report text
     */
    private function parseReportFields($report)
    {
        $text = $report['description'] ?? '';
        
        preg_match('/• Diagnosa: (.*?)(?=\n•|\n---|$)/s', $text, $d);
        preg_match('/• Pemeriksaan: (.*?)(?=\n•|\n---|$)/s', $text, $i);
        preg_match('/• Tindakan\/Perbaikan: (.*?)(?=\n•|\n---|$)/s', $text, $a);
        preg_match('/• Sparepart Terpakai: (.*?)(?=\n•|\n---|$)/s', $text, $sp);
        preg_match('/• Hasil Pekerjaan: (.*?)(?=\n•|\n---|$)/s', $text, $res);
        preg_match('/• Catatan Teknisi: (.*?)(?=\n•|\n---|$)/s', $text, $n);
        preg_match('/• Rekomendasi AI: (.*?)(?=\n---|$)/s', $text, $rec);

        $aiFull = null;
        if (strpos($text, '--- DOKUMEN LAPORAN RESMI 7 BAGIAN ---') !== false) {
            $parts = explode('--- DOKUMEN LAPORAN RESMI 7 BAGIAN ---', $text);
            $aiFull = trim($parts[1] ?? '');
        }

        $photos = explode('|', $report['photo_evidence'] ?? '');

        return [
            'id' => $report['id'],
            'order_id' => $report['order_id'],
            'technician_name' => $report['technician_name'] ?? 'Teknisi Siswa',
            'diagnosis' => trim($d[1] ?? 'Pemeriksaan awal komponen perangkat IT.'),
            'inspection' => trim($i[1] ?? 'Pengujian sinyal dan kelistrikan.'),
            'action_taken' => trim($a[1] ?? 'Perbaikan komponen dan optimasi.'),
            'spareparts_used' => trim($sp[1] ?? 'Tidak menggunakan sparepart tambahan.'),
            'execution_result' => trim($res[1] ?? 'Pekerjaan selesai dan teruji.'),
            'notes' => trim($n[1] ?? 'Telah diserahterimakan kepada customer.'),
            'recommendation' => trim($rec[1] ?? 'Disarankan untuk melakukan pemeliharaan berkala.'),
            'ai_report_full' => $aiFull,
            'photo_before' => $photos[0] ?? null,
            'photo_after' => $photos[1] ?? null,
            'logged_at' => $report['logged_at'] ?? $report['created_at']
        ];
    }

    /**
     * Get logs history for a specific order
     */
    public function getLogsByOrderId($orderId)
    {
        if ($this->db->isConnected()) {
            $sql = "SELECT wl.*, u.full_name as user_name, u.role as user_role
                    FROM " . $this->table . " wl
                    LEFT JOIN technicians t ON wl.technician_id = t.id
                    LEFT JOIN users u ON t.user_id = u.id
                    WHERE wl.order_id = :order_id
                    ORDER BY wl.id DESC";

            $this->db->query($sql);
            $this->db->bind(':order_id', $orderId);
            $results = $this->db->resultSet();
            if ($results !== false) {
                return $results;
            }
        }

        return [
            [
                'id' => 1,
                'order_id' => $orderId,
                'user_name' => 'Siti Rahmawati, S.Kom',
                'user_role' => 'operator',
                'log_type' => 'diagnosis',
                'description' => 'Operator memverifikasi pengajuan order dan menyetujui pengerjaan.',
                'logged_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
            ],
            [
                'id' => 2,
                'order_id' => $orderId,
                'user_name' => 'Budi Santoso',
                'user_role' => 'teknisi',
                'log_type' => 'checkin',
                'description' => 'Teknisi menerima tugas lapangan dan mengonfirmasi jadwal.',
                'logged_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))
            ]
        ];
    }
}
