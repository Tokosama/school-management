<?php
// Project.php

class Project {
    private $pdo;

    public function __construct($dbPath = 'database.sqlite') {
        $this->pdo = new PDO("sqlite:" . $dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function submit($student_id, $partner_name, $theme, $file_path) {
        $stmt = $this->pdo->prepare("
            INSERT INTO projects (student_id, partner_name, theme, file_path)
            VALUES (:student_id, :partner_name, :theme, :file_path)
        ");
        return $stmt->execute([
            ':student_id' => $student_id,
            ':partner_name' => $partner_name,
            ':theme' => $theme,
            ':file_path' => $file_path
        ]);
    }

    public function assign($project_id, $teacher_id) {
        $stmt = $this->pdo->prepare("
            UPDATE projects
            SET assigned_teacher_id = :teacher_id, status = 'assigned'
            WHERE id = :project_id
        ");
        return $stmt->execute([
            ':teacher_id' => $teacher_id,
            ':project_id' => $project_id
        ]);
    }

    public function getByStudent($student_id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM projects WHERE student_id = :student_id
        ");
        $stmt->execute([':student_id' => $student_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPending() {
        $stmt = $this->pdo->query("
            SELECT * FROM projects WHERE status = 'pending'
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM projects");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
