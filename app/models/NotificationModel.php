<?php
/**
 * NotificationModel Class
 * Handles creation, fetching, unread counting, and mark-as-read for user notifications
 */

class NotificationModel
{
    private $db;
    private $table = 'notifications';

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Send notification to a specific user_id
     */
    public function sendNotification($userId, $title, $message, $type = 'system', $linkUrl = null)
    {
        if (!$this->db->isConnected() || empty($userId)) {
            return false;
        }

        $sql = "INSERT INTO " . $this->table . " (user_id, title, message, type, is_read, link_url, created_at)
                VALUES (:user_id, :title, :message, :type, 0, :link_url, NOW())";

        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':title', $title);
        $this->db->bind(':message', $message);
        $this->db->bind(':type', $type);
        $this->db->bind(':link_url', $linkUrl);

        return $this->db->execute();
    }

    /**
     * Send notification to all users belonging to a specific role (e.g. 'operator')
     */
    public function sendNotificationToRole($roleName, $title, $message, $type = 'system', $linkUrl = null)
    {
        if (!$this->db->isConnected()) {
            return false;
        }

        $sqlUser = "SELECT u.id FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = :role_name AND u.is_active = 1";
        $this->db->query($sqlUser);
        $this->db->bind(':role_name', $roleName);
        $users = $this->db->resultSet();

        if ($users) {
            foreach ($users as $u) {
                $this->sendNotification($u['id'], $title, $message, $type, $linkUrl);
            }
            return true;
        }

        return false;
    }

    /**
     * Get unread notifications for a user
     */
    public function getUnreadNotifications($userId, $limit = 10)
    {
        if (!$this->db->isConnected()) {
            return [];
        }

        $sql = "SELECT * FROM " . $this->table . " 
                WHERE user_id = :user_id AND is_read = 0 
                ORDER BY id DESC LIMIT :lim";

        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':lim', $limit);

        $results = $this->db->resultSet();
        return $results ?: [];
    }

    /**
     * Get all notifications for a user (history)
     */
    public function getAllNotifications($userId, $limit = 30)
    {
        if (!$this->db->isConnected()) {
            return [];
        }

        $sql = "SELECT * FROM " . $this->table . " 
                WHERE user_id = :user_id 
                ORDER BY id DESC LIMIT :lim";

        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':lim', $limit);

        $results = $this->db->resultSet();
        return $results ?: [];
    }

    /**
     * Get unread notifications count for a user (for navbar badge)
     */
    public function getUnreadCount($userId)
    {
        if (!$this->db->isConnected()) {
            return 0;
        }

        $sql = "SELECT COUNT(id) as total FROM " . $this->table . " 
                WHERE user_id = :user_id AND is_read = 0";

        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        $row = $this->db->single();

        return intval($row['total'] ?? 0);
    }

    /**
     * Mark a single notification as read
     */
    public function markAsRead($notificationId, $userId)
    {
        if (!$this->db->isConnected()) {
            return false;
        }

        $sql = "UPDATE " . $this->table . " SET is_read = 1 
                WHERE id = :id AND user_id = :user_id";

        $this->db->query($sql);
        $this->db->bind(':id', $notificationId);
        $this->db->bind(':user_id', $userId);

        return $this->db->execute();
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($userId)
    {
        if (!$this->db->isConnected()) {
            return false;
        }

        $sql = "UPDATE " . $this->table . " SET is_read = 1 
                WHERE user_id = :user_id AND is_read = 0";

        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);

        return $this->db->execute();
    }

    /**
     * Helper to get user_id from customer_id
     */
    public function getUserIdByCustomerId($customerId)
    {
        if (!$this->db->isConnected()) {
            return null;
        }

        $sql = "SELECT user_id FROM customers WHERE id = :cid LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':cid', $customerId);
        $row = $this->db->single();

        return $row['user_id'] ?? null;
    }

    /**
     * Helper to get user_id from technician_id
     */
    public function getUserIdByTechnicianId($technicianId)
    {
        if (!$this->db->isConnected()) {
            return null;
        }

        $sql = "SELECT user_id FROM technicians WHERE id = :tid LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':tid', $technicianId);
        $row = $this->db->single();

        return $row['user_id'] ?? null;
    }
}
