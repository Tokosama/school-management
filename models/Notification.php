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
                student_id INTEGER NOT NULL,
                project_id INTEGER NOT NULL,
                message TEXT NOT NULL,
                is_read BOOLEAN NOT NULL DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (student_id) REFERENCES students(id),
                FOREIGN KEY (project_id) REFERENCES projects(id)
            );
        ");
    }

    /**
     * Crée une nouvelle notification
     */
    public function create($studentId, $projectId, $message) {
        $stmt = $this->pdo->prepare("
            INSERT INTO notifications (
                student_id, project_id, message
            ) VALUES (
                :student_id, :project_id, :message
            )
        ");
        
        return $stmt->execute([
            ':student_id' => $studentId,
            ':project_id' => $projectId,
            ':message' => $message
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
     * Récupère les notifications liées à un projet spécifique
     */
    public function getForProject($projectId) {
        $stmt = $this->pdo->prepare("
            SELECT n.*, s.Studentname AS sender_name
            FROM notifications n
            JOIN students s ON n.student_id = s.id
            WHERE n.project_id = :project_id
            ORDER BY n.created_at DESC
        ");
        $stmt->execute([':project_id' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les notifications liées à un étudiant
     */
    public function getForStudent($studentId) {
        $stmt = $this->pdo->prepare("
            SELECT n.*, p.name AS project_name
            FROM notifications n
            JOIN projects p ON n.project_id = p.id
            WHERE n.student_id = :student_id
            ORDER BY n.created_at DESC
        ");
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Supprime une notification
     */
    public function getAll() {
    $stmt = $this->pdo->prepare("
        SELECT n.*, s.username AS student_name, p.theme AS project_title
        FROM notifications n
        LEFT JOIN students s ON n.student_id = s.id
        LEFT JOIN projects p ON n.project_id = p.id
        ORDER BY n.created_at DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function delete($notificationId) {
        $stmt = $this->pdo->prepare("
            DELETE FROM notifications 
            WHERE id = :id
        ");
        return $stmt->execute([':id' => $notificationId]);
    }

    /**
     * Récupère le nombre de notifications non lues d’un étudiant
     */
    public function getUnreadCount($studentId) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) AS count
            FROM notifications
            WHERE student_id = :student_id
            AND is_read = 0
        ");
        $stmt->execute([':student_id' => $studentId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
}
