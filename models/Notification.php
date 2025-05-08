<?php
// Notification.php

class Notification {
    private $pdo;

    public function __construct($dbPath = 'database.sqlite') {
        $this->pdo = new PDO("sqlite:" . $dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function send($student_id, $message) {
        $stmt = $this->pdo->prepare("
            INSERT INTO notifications (student_id, message)
            VALUES (:student_id, :message)
        ");
        return $stmt->execute([
            ':student_id' => $student_id,
            ':message' => $message
        ]);
    }

    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT n.*, u.username as student_username
            FROM notifications n
            JOIN users u ON n.student_id = u.id
            ORDER BY created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByStudent($student_id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM notifications WHERE student_id = :student_id
        ");
        $stmt->execute([':student_id' => $student_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
