<?php
// Student.php

class Student {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->createTable();
    }

    // Initialisation des tables si elles n'existent pas
    private function createTable() {
        $this->pdo->exec("
        CREATE TABLE IF NOT EXISTS students (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            domains TEXT NOT NULL CHECK(
                domains IN (
                    'AL', 'SRC', 'SI',
                    'AL/SI', 'AL/SRC', 'SI/SRC',
                    'AL/SI/SRC'
                )
            )
        );
    ");
    }

    // Ajout des nouvelles méthodes nécessaires pour AuthController
    public function findByUsername($username) {
        $stmt = $this->pdo->prepare("SELECT * FROM students WHERE username = :username");
        $stmt->execute([':username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function create(array $data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO students (username, password, domains)
            VALUES (:username, :password, :domains)
        ");
        return $stmt->execute([
            ':username' => $data['username'],
            ':password' => $data['password'],
            ':domains' => $data['domains']
        ]);
    }

    public function updateRememberToken($studentId, $token, $expires) {
        $stmt = $this->pdo->prepare("
            UPDATE students 
            SET remember_token = :token, remember_token_expires = :expires 
            WHERE id = :id
        ");
        return $stmt->execute([
            ':token' => $token,
            ':expires' => $expires,
            ':id' => $studentId
        ]);
    }

    // Enregistrement d'un nouvel utilisateur
    public function register($username, $password, $domains) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("
            INSERT INTO students (username, password, domains)
            VALUES (:username, :password, :domains)
        ");
        return $stmt->execute([
            ':username' => $username,
            ':password' => $hashedPassword,
            ':domains' => $domains
        ]);
    }

    // Connexion de l'utilisateur
    public function login($username, $password) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM students WHERE username = :username
        ");
        $stmt->execute([':username' => $username]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($student && password_verify($password, $student['password'])) {
            return $student;
        }
        return false;
    }

    // Récupérer un utilisateur par son ID
    public function getStudentById($id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM students WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer tous les enseignants
    public function getAllTeachers() {
        $stmt = $this->pdo->query("
            SELECT * FROM students WHERE role = 'teacher'
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Soumission d'un nouveau projet
    public function submitProject($student_id, $binome, $theme, $file_path) {
        $stmt = $this->pdo->prepare("
            INSERT INTO projects (student_id, binome, theme, file_path)
            VALUES (:student_id, :binome, :theme, :file_path)
        ");
        return $stmt->execute([
            ':student_id' => $student_id,
            ':binome' => $binome,
            ':theme' => $theme,
            ':file_path' => $file_path
        ]);
    }

    // Affectation d'un enseignant à un projet
    public function assignTeacher($project_id, $teacher_id) {
        $stmt = $this->pdo->prepare("
            UPDATE projects
            SET teacher_id = :teacher_id, status = 'assigned'
            WHERE id = :project_id
        ");
        return $stmt->execute([
            ':teacher_id' => $teacher_id,
            ':project_id' => $project_id
        ]);
    }

    // Récupérer les projets en attente d'affectation
    public function getPendingProjects() {
        $stmt = $this->pdo->query("
            SELECT * FROM projects WHERE status = 'pending'
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer le projet d'un étudiant
    public function getProjectByStudentId($student_id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM projects WHERE student_id = :student_id
        ");
        $stmt->execute([':student_id' => $student_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Envoi d'une notification de relance
    public function sendReminder($student_id, $message) {
        $stmt = $this->pdo->prepare("
            INSERT INTO notifications (student_id, message)
            VALUES (:student_id, :message)
        ");
        return $stmt->execute([
            ':student_id' => $student_id,
            ':message' => $message
        ]);
    }

    // Récupérer toutes les notifications
    public function getAllNotifications() {
        $stmt = $this->pdo->query("
            SELECT * FROM notifications ORDER BY created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
