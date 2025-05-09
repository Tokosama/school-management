<?php
// app/Models/Notification.php

require_once __DIR__ . '/../config/database.php';

class Notification {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->createTable();
    }

    private function createTable() {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS notifications (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                project_id INTEGER,
                message TEXT NOT NULL,
                recipient_role TEXT NOT NULL CHECK(
                    recipient_role IN ('student', 'teacher', 'admin')
                ),
                is_read BOOLEAN NOT NULL DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (project_id) REFERENCES projects(id)
            );
        ");
    }

    /**
     * Crée une nouvelle notification
     */
    public function create($userId, $message, $recipientRole = 'admin', $projectId = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO notifications (
                user_id, project_id, message, recipient_role
            ) VALUES (
                :user_id, :project_id, :message, :recipient_role
            )
        ");
        
        return $stmt->execute([
            ':user_id' => $userId,
            ':project_id' => $projectId,
            ':message' => $message,
            ':recipient_role' => $recipientRole
        ]);
    }

    /**
     * Marque une notification comme lue
     */
    public function markAsRead($notificationId) {
        $stmt = $this->pdo->prepare("
            UPDATE notifications 
            SET is_read = 1 
            WHERE id = :id
        ");
        return $stmt->execute([':id' => $notificationId]);
    }

    /**
     * Récupère les notifications pour un utilisateur/rôle spécifique
     */
    public function getForUser($userId, $role, $unreadOnly = false) {
        $sql = "
            SELECT n.*, u.username as sender_name
            FROM notifications n
            JOIN users u ON n.user_id = u.id
            WHERE n.recipient_role = :role
        ";
        
        $params = [':role' => $role];
        
        if ($role !== 'admin') {
            $sql .= " AND (n.user_id = :user_id OR n.project_id IN (
                        SELECT id FROM projects WHERE student_id = :user_id
                    ))";
            $params[':user_id'] = $userId;
        }
        
        if ($unreadOnly) {
            $sql .= " AND n.is_read = 0";
        }
        
        $sql .= " ORDER BY n.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les notifications liées à un projet spécifique
     */
    public function getForProject($projectId) {
        $stmt = $this->pdo->prepare("
            SELECT n.*, u.username as sender_name
            FROM notifications n
            JOIN users u ON n.user_id = u.id
            WHERE n.project_id = :project_id
            ORDER BY n.created_at DESC
        ");
        $stmt->execute([':project_id' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Supprime une notification
     */
    public function delete($notificationId) {
        $stmt = $this->pdo->prepare("
            DELETE FROM notifications 
            WHERE id = :id
        ");
        return $stmt->execute([':id' => $notificationId]);
    }

    /**
     * Récupère le nombre de notifications non lues
     */
    public function getUnreadCount($userId, $role) {
        $sql = "
            SELECT COUNT(*) as count
            FROM notifications
            WHERE recipient_role = :role
            AND is_read = 0
        ";
        
        $params = [':role' => $role];
        
        if ($role !== 'admin') {
            $sql .= " AND (user_id = :user_id OR project_id IN (
                        SELECT id FROM projects WHERE student_id = :user_id
                    ))";
            $params[':user_id'] = $userId;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
}