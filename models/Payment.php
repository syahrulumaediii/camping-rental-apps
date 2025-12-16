<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/functions.php';

class Payment
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = [])
    {
        $sql = "
            SELECT p.*, 
                b.start_date, b.end_date,
                u.username, u.full_name, u.email,
                i.name as item_name
            FROM payments p
            JOIN bookings b ON p.booking_id = b.id
            JOIN users u ON p.user_id = u.id
            JOIN items i ON b.item_id = i.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['user_id'])) {
            $sql .= " AND p.user_id = ?";
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND p.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['booking_id'])) {
            $sql .= " AND p.booking_id = ?";
            $params[] = $filters['booking_id'];
        }

        $sql .= " ORDER BY p.created_at DESC";

        return $this->db->fetchAll($sql, $params);
    }

    public function getById($id)
    {
        return $this->db->fetchOne("
            SELECT p.*, 
                b.start_date, b.end_date, b.quantity, b.total_price as booking_total,
                u.username, u.full_name, u.email, u.phone,
                i.name as item_name, i.category, i.image_url
            FROM payments p
            JOIN bookings b ON p.booking_id = b.id
            JOIN users u ON p.user_id = u.id
            JOIN items i ON b.item_id = i.id
            WHERE p.id = ?
        ", [$id]);
    }

    public function getByBookingId($bookingId)
    {
        return $this->db->fetchOne("SELECT * FROM payments WHERE booking_id = ?", [$bookingId]);
    }

    public function create($data)
    {
        try {
            $this->db->beginTransaction();

            // Generate transaction ID if not provided
            if (empty($data['transaction_id'])) {
                $data['transaction_id'] = generateTransactionId();
            }

            // Create payment
            $paymentId = $this->db->insert('payments', $data);

            if (!$paymentId) {
                throw new Exception("Failed to create payment");
            }

            // Update booking status if payment is completed
            if ($data['status'] === 'completed') {
                $this->db->update(
                    'bookings',
                    ['status' => 'confirmed'],
                    'id = ?',
                    [$data['booking_id']]
                );
            }

            $this->db->commit();
            return $paymentId;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Payment creation error: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data)
    {
        return $this->db->update('payments', $data, 'id = ?', [$id]);
    }
    public function updateStatus($id, $status)
    {
        try {
            $this->db->beginTransaction();

            $payment = $this->getById($id);
            if (!$payment) {
                throw new Exception("Payment not found");
            }

            // ✅ Update payment status — simpan return value ke $updated
            $updated = $this->db->update(
                'payments',
                ['status' => $status],
                'id = :id',   // ← named placeholder
                ['id' => $id] // ← named param
            );

            if (!$updated) {
                throw new Exception("Failed to update payment status");
            }

            // ✅ Update booking status — ganti ? jadi :id
            if ($status === 'completed') {
                $this->db->update(
                    'bookings',
                    ['status' => 'confirmed'],
                    'id = :id',   // ← named placeholder
                    ['id' => $payment['booking_id']] // ← named param
                );
            } elseif ($status === 'failed') {
                $this->db->update(
                    'bookings',
                    ['status' => 'cancelled'],
                    'id = :id',   // ← named placeholder
                    ['id' => $payment['booking_id']] // ← named param
                );
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Payment status update error: " . $e->getMessage());
            return false;
        }
    }

    public function getPaymentStats($userId = null)
    {
        $sql = "
            SELECT 
                COUNT(*) as total_payments,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed,
                COALESCE(SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END), 0) as total_revenue
            FROM payments
        ";
        $params = [];

        if ($userId) {
            $sql .= " WHERE user_id = ?";
            $params[] = $userId;
        }

        return $this->db->fetchOne($sql, $params);
    }

    public function getRevenueByDate($startDate = null, $endDate = null)
    {
        $sql = "
            SELECT 
                DATE(payment_date) as date,
                COUNT(*) as total_transactions,
                SUM(amount) as total_revenue
            FROM payments
            WHERE status = 'completed'
        ";
        $params = [];

        if ($startDate) {
            $sql .= " AND DATE(payment_date) >= ?";
            $params[] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND DATE(payment_date) <= ?";
            $params[] = $endDate;
        }

        $sql .= " GROUP BY DATE(payment_date) ORDER BY date DESC";

        return $this->db->fetchAll($sql, $params);
    }
}
