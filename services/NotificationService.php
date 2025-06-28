<?php
require_once __DIR__ . '/../Db_Connect.php';

class NotificationService {
    private $db;

    public function __construct() {
        global $host, $user, $pass, $dbname;
        try {
            $this->db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    // Add a notification for a user
    public function sendToUser($userId, $message) {
        $stmt = $this->db->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
        return $stmt->execute([$userId, $message]);
    }

    // Create a notification with more details
    public function createNotification($userId, $title, $message, $type = 'order') {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO notifications (
                    user_id, 
                    message, 
                    is_read, 
                    type, 
                    created_at
                ) VALUES (?, ?, 0, ?, NOW())
            ");
            return $stmt->execute([$userId, $message, $type]);
        } catch (Exception $e) {
            error_log('Error creating notification: ' . $e->getMessage());
            return false;
        }
    }

    // Fetch notifications for a user (most recent first)
    public function getUserNotifications($userId, $limit = 10) {
        $limit = (int)$limit; // Ensure it's an integer
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT $limit");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mark a notification as read
    public function markAsRead($notificationId) {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ?");
        return $stmt->execute([$notificationId]);
    }
} 