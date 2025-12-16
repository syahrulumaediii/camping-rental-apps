<?php
require_once __DIR__ . '/../config/database.php';


class Item
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = [])
    {
        $sql = "SELECT * FROM items WHERE 1=1";
        $params = [];

        if (!empty($filters['category'])) {
            $sql .= " AND category = ?";
            $params[] = $filters['category'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (name LIKE ? OR description LIKE ?)";
            $search = "%{$filters['search']}%";
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($filters['min_price'])) {
            $sql .= " AND price_per_day >= ?";
            $params[] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $sql .= " AND price_per_day <= ?";
            $params[] = $filters['max_price'];
        }

        $sql .= " ORDER BY created_at DESC";

        return $this->db->fetchAll($sql, $params);
    }

    public function getById($id)
    {
        return $this->db->fetchOne("SELECT * FROM items WHERE id = ?", [$id]);
    }

    public function getAvailableItems($startDate = null, $endDate = null)
    {
        if ($startDate && $endDate) {
            return $this->db->fetchAll("
                SELECT i.*, 
                    (i.quantity_total - COALESCE(SUM(b.quantity), 0)) as available_quantity
                FROM items i
                LEFT JOIN bookings b ON i.id = b.item_id
                    AND b.status IN ('pending', 'confirmed')
                    AND (
                        (b.start_date <= ? AND b.end_date >= ?) OR
                        (b.start_date <= ? AND b.end_date >= ?) OR
                        (b.start_date >= ? AND b.end_date <= ?)
                    )
                WHERE i.status = 'available'
                GROUP BY i.id
                HAVING available_quantity > 0
                ORDER BY i.name
            ", [$startDate, $startDate, $endDate, $endDate, $startDate, $endDate]);
        }

        return $this->db->fetchAll("
            SELECT * FROM items 
            WHERE status = 'available' AND quantity_available > 0
            ORDER BY name
        ");
    }

    public function create($data)
    {
        return $this->db->insert('items', $data);
    }

    // models/Item.php
    public function update($id, $data)
    {
        return $this->db->update('items', $data, 'id = :id', ['id' => $id]);
    }

    public function delete($id)
    {
        return $this->db->delete('items', 'id = ?', [$id]);
    }

    public function updateQuantity($id, $quantity, $action = 'manual')
    {
        $item = $this->getById($id);
        if (!$item) return false;

        // Log history
        $this->db->insert('inventory_history', [
            'item_id' => $id,
            'quantity_before' => $item['quantity_available'],
            'quantity_after' => $quantity,
            'action' => $action,
            'notes' => "Quantity updated via $action"
        ]);

        return $this->db->update('items', ['quantity_available' => $quantity], 'id = ?', [$id]);
    }

    public function getCategories()
    {
        return $this->db->fetchAll("SELECT DISTINCT category FROM items WHERE category IS NOT NULL ORDER BY category");
    }

    public function getItemWithReviews($id)
    {
        $item = $this->getById($id);
        if (!$item) return null;

        $reviews = $this->db->fetchAll("
            SELECT r.*, u.username, u.full_name
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.item_id = ?
            ORDER BY r.created_at DESC
        ", [$id]);

        $avgRating = $this->db->fetchOne("
            SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews
            FROM reviews
            WHERE item_id = ?
        ", [$id]);

        $item['reviews'] = $reviews;
        $item['avg_rating'] = $avgRating['avg_rating'] ?? 0;
        $item['total_reviews'] = $avgRating['total_reviews'] ?? 0;

        return $item;
    }
}
