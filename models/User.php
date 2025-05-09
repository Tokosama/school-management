<?php
// User.php

class User {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->createTable();
    }

    // Initialisation des tables si elles n'existent pas
    private function createTable() {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                user_id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                role TEXT NOT NULL CHECK(role IN ('student')),
                domain TEXT
            );
        ");

        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS projects (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                student_id INTEGER NOT NULL,
                binome TEXT NOT NULL,
                theme TEXT NOT NULL,
                file_path TEXT NOT NULL,
                teacher_id INTEGER ,
                status TEXT NOT NULL DEFAULT 'pending',
                FOREIGN KEY(student_id) REFERENCES users(id),
                FOREIGN KEY(teacher_id) REFERENCES users(id)
            );
        ");

        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS notifications (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                student_id INTEGER NOT NULL,
                message TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(student_id) REFERENCES users(id)
            );
        ");
    }

    // Ajout des nouvelles méthodes nécessaires pour AuthController
    public function findByUsername($username) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (username, email, password, role, filiere, created_at)
            VALUES (:username, :email, :password, :role, :filiere, :created_at)
        ");
        return $stmt->execute([
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':password' => $data['password'],
            ':role' => $data['role'],
            ':filiere' => $data['filiere'],
            ':created_at' => $data['created_at']
        ]);
    }

    public function updateRememberToken($userId, $token, $expires) {
        $stmt = $this->pdo->prepare("
            UPDATE users 
            SET remember_token = :token, remember_token_expires = :expires 
            WHERE id = :id
        ");
        return $stmt->execute([
            ':token' => $token,
            ':expires' => $expires,
            ':id' => $userId
        ]);
    }

    // Enregistrement d'un nouvel utilisateur
    public function register($username, $password, $role, $filiere = null) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("
            INSERT INTO users (username, password, role, filiere)
            VALUES (:username, :password, :role, :filiere)
        ");
        return $stmt->execute([
            ':username' => $username,
            ':password' => $hashedPassword,
            ':role' => $role,
            ':filiere' => $filiere
        ]);
    }

    // Connexion de l'utilisateur
    public function login($username, $password) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM users WHERE username = :username
        ");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // Récupérer un utilisateur par son ID
    public function getUserById($id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM users WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer tous les enseignants
    public function getAllTeachers() {
        $stmt = $this->pdo->query("
            SELECT * FROM users WHERE role = 'teacher'
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
            ':partner_name' => $binome,
            ':theme' => $theme,
            ':file_path' => $file_path
        ]);
    }

    // Affectation d'un enseignant à un projet
    public function assignTeacher($project_id, $teacher_id) {
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
