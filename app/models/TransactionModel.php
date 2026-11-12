<?php
/**
 * TransactionModel Class
 * Handles Backend Billing Calculation, Invoicing, Manual Payments, & Revenue Reports
 */

class TransactionModel
{
    private $db;
    private $table = 'transactions';

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * BACKEND FORMULA CALCULATION & SAVING:
     * Grand Total = Service Fee + Sparepart Fee + Additional Fee - Discount
     */
    public function calculateAndSaveTransaction($orderId, $serviceFee, $sparepartFee = 0, $additionalFee = 0, $discount = 0)
    {
        // Sanitize numeric values
        $serviceFee    = max(0, floatval($serviceFee));
        $sparepartFee  = max(0, floatval($sparepartFee));
        $additionalFee = max(0, floatval($additionalFee));
        $discount      = max(0, floatval($discount));

        // BACKEND SECURE FORMULA EXECUTION
        $grandTotal = ($serviceFee + $sparepartFee + $additionalFee) - $discount;
        $grandTotal = max(0, $grandTotal); // Ensure non-negative

        $trxCode = 'TRX-' . date('Ymd') . '-' . str_pad($orderId, 3, '0', STR_PAD_LEFT);

        if ($this->db->isConnected()) {
            // Check if transaction row already exists
            $this->db->query("SELECT id FROM " . $this->table . " WHERE order_id = :order_id");
            $this->db->bind(':order_id', $orderId);
            $existing = $this->db->single();

            if ($existing) {
                $sql = "UPDATE " . $this->table . " 
                        SET total_service_fee = :service_fee, total_sparepart_fee = :sparepart_fee, 
                            tax_amount = :additional_fee, discount = :discount, grand_total = :grand_total 
                        WHERE id = :id";
                $this->db->query($sql);
                $this->db->bind(':id', $existing['id']);
                $this->db->bind(':service_fee', $serviceFee);
                $this->db->bind(':sparepart_fee', $sparepartFee);
                $this->db->bind(':additional_fee', $additionalFee);
                $this->db->bind(':discount', $discount);
                $this->db->bind(':grand_total', $grandTotal);

                return $this->db->execute();
            } else {
                $sql = "INSERT INTO " . $this->table . " 
                        (transaction_code, order_id, total_service_fee, total_sparepart_fee, tax_amount, discount, grand_total, payment_status, created_at)
                        VALUES (:code, :order_id, :service_fee, :sparepart_fee, :additional_fee, :discount, :grand_total, 'Belum Dibayar', NOW())";
                $this->db->query($sql);
                $this->db->bind(':code', $trxCode);
                $this->db->bind(':order_id', $orderId);
                $this->db->bind(':service_fee', $serviceFee);
                $this->db->bind(':sparepart_fee', $sparepartFee);
                $this->db->bind(':additional_fee', $additionalFee);
                $this->db->bind(':discount', $discount);
                $this->db->bind(':grand_total', $grandTotal);

                return $this->db->execute();
            }
        }
        return true;
    }

    /**
     * Get transaction & invoice details by Order ID
     */
    public function getTransactionByOrderId($orderId)
    {
        if ($this->db->isConnected()) {
            $sql = "SELECT t.*, o.order_code, o.status as order_status, COALESCE(s.name, 'Layanan Teknis IT') as service_name,
                           u.full_name as customer_name, u.email as customer_email, u.phone as customer_phone
                    FROM " . $this->table . " t
                    JOIN service_orders o ON t.order_id = o.id
                    LEFT JOIN services s ON o.service_id = s.id
                    JOIN customers c ON o.customer_id = c.id
                    JOIN users u ON c.user_id = u.id
                    WHERE t.order_id = :order_id";

            $this->db->query($sql);
            $this->db->bind(':order_id', $orderId);
            $trx = $this->db->single();
            if ($trx) {
                return $trx;
            }
        }

        // Demo fallback invoice data
        return [
            'id' => 1,
            'transaction_code' => 'TRX-20260829-001',
            'order_id' => $orderId,
            'order_code' => 'TKJ-20260829-A7F2',
            'service_name' => 'Instalasi CCTV Analog & DVR 4 Channel',
            'customer_name' => 'Ahmad Dahlan',
            'customer_email' => 'ahmad.dahlan@gmail.com',
            'customer_phone' => '081234567890',
            'total_service_fee' => 200000,
            'total_sparepart_fee' => 480000,
            'tax_amount' => 0, // Additional fee
            'discount' => 20000,
            'grand_total' => 660000,
            'payment_status' => 'Belum Dibayar',
            'created_at' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Customer Manual Payment Record Submission (Status: Menunggu Verifikasi)
     */
    public function recordPayment($orderId, $amount, $paymentMethod, $proofImage = null)
    {
        $trx = $this->getTransactionByOrderId($orderId);
        $trxId = $trx['id'] ?? 1;

        if ($this->db->isConnected()) {
            // Update transaction payment status to 'Menunggu Verifikasi'
            $sqlTrx = "UPDATE " . $this->table . " SET payment_status = 'Menunggu Verifikasi' WHERE id = :id";
            $this->db->query($sqlTrx);
            $this->db->bind(':id', $trxId);
            $this->db->execute();

            // Insert into payments table
            $payCode = 'PAY-' . date('Ymd') . '-' . rand(100, 999);
            $sqlPay = "INSERT INTO payments (payment_code, transaction_id, amount, payment_method, proof_image, payment_date, status, created_at)
                       VALUES (:code, :trx_id, :amount, :method, :proof, NOW(), 'pending', NOW())";
            $this->db->query($sqlPay);
            $this->db->bind(':code', $payCode);
            $this->db->bind(':trx_id', $trxId);
            $this->db->bind(':amount', $amount);
            $this->db->bind(':method', $paymentMethod);
            $this->db->bind(':proof', $proofImage);

            return $this->db->execute();
        }
        return true;
    }

    /**
     * Operator Verify Payment (Status: Lunas / Dibatalkan)
     */
    public function verifyPayment($orderId, $status = 'Lunas', $verifierUserId = 2)
    {
        $trx = $this->getTransactionByOrderId($orderId);
        $trxId = $trx['id'] ?? 1;

        if ($this->db->isConnected()) {
            $sqlTrx = "UPDATE " . $this->table . " SET payment_status = :status WHERE id = :id";
            $this->db->query($sqlTrx);
            $this->db->bind(':status', $status);
            $this->db->bind(':id', $trxId);
            $this->db->execute();

            $sqlPay = "UPDATE payments SET status = 'verified', verified_by_user_id = :verifier WHERE transaction_id = :trx_id";
            $this->db->query($sqlPay);
            $this->db->bind(':verifier', $verifierUserId);
            $this->db->bind(':trx_id', $trxId);

            return $this->db->execute();
        }
        return true;
    }

    /**
     * Revenue Report Data for Operator & Kepala Sekolah
     */
    public function getRevenueReport()
    {
        if ($this->db->isConnected()) {
            $sql = "SELECT t.*, o.order_code, COALESCE(s.name, 'Layanan Teknis IT') as service_name, u.full_name as customer_name
                    FROM " . $this->table . " t
                    JOIN service_orders o ON t.order_id = o.id
                    LEFT JOIN services s ON o.service_id = s.id
                    JOIN customers c ON o.customer_id = c.id
                    JOIN users u ON c.user_id = u.id
                    ORDER BY t.id DESC";

            $this->db->query($sql);
            $results = $this->db->resultSet();
            if ($results !== false) {
                return $results;
            }
        }

        return [
            [
                'id' => 1,
                'transaction_code' => 'TRX-20260828-003',
                'order_code' => 'TKJ-20260828-C9D4',
                'service_name' => 'Perbaikan PC Desktop & Upgrade SSD/RAM',
                'customer_name' => 'Budi Utomo (SDN 1)',
                'total_service_fee' => 280000,
                'total_sparepart_fee' => 0,
                'tax_amount' => 0,
                'discount' => 30000,
                'grand_total' => 250000,
                'payment_status' => 'Lunas',
                'created_at' => '2026-08-28 16:30:00'
            ]
        ];
    }
}
