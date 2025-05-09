<?php
// app/Models/Project.php

require_once __DIR__ . '/../config/database.php';

class Project {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->createTable();
    }

    private function createTable() {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS projects (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                student_id INTEGER NOT NULL,
                Nom_binome TEXT NOT NULL,
                title TEXT NOT NULL,
                description TEXT,
                domains TEXT NOT NULL CHECK(
                    domains IN (
                        'AL', 'SRC', 'SI',
                        'AL/SI', 'AL/SRC', 'SI/SRC',
                        'AL/SI/SRC'
                    )
                ),
                file_path TEXT NOT NULL,
                status TEXT NOT NULL DEFAULT 'pending' CHECK(
                    status IN ('en cours', 'assigné', 'completé')
                ),
                assigned_teacher_id INTEGER,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (student_id) REFERENCES users(id),
                FOREIGN KEY (teacher_id) REFERENCES teachers(id)
            );
        ");
    }

    /**
     * Valide les domaines comme dans Teacher.php
     */
    private function validateDomains($domains) {
        $allowed = [
            'AL', 'SRC', 'SI',
            'AL/SI', 'AL/SRC', 'SI/SRC',
            'AL/SI/SRC'
        ];
        
        if (is_array($domains)) {
            $domains = implode('/', array_unique($domains));
        }
        
        $domains = strtoupper($domains);
        
        if (!in_array($domains, $allowed)) {
            throw new InvalidArgumentException("Combinaison de domaines invalide. Les combinaisons autorisées sont: " . implode(', ', $allowed));
        }
        
        return $domains;
    }

    /**
     * Crée un nouveau projet
     */
    public function create($studentId, $Nom_binome, $title, $description, $domains, $filePath) {
        $domains = $this->validateDomains($domains);
        
        $stmt = $this->pdo->prepare("
            INSERT INTO projects (
                student_id, Nom_binome, title, 
                description, domains, file_path
            ) VALUES (
                :student_id, :Nom_binome, :title,
                :description, :domains, :file_path
            )
        ");
        
        return $stmt->execute([
            ':student_id' => $studentId,
            ':partner_name' => $Nom_binome,
            ':title' => $title,
            ':description' => $description,
            ':domains' => $domains,
            ':file_path' => $filePath
        ]);
    }

    /**
     * Affecte un enseignant à un projet
     */
    public function assignTeacher($projectId, $teacherId) {
        $stmt = $this->pdo->prepare("
            UPDATE projects 
            SET assigned_teacher_id = :teacher_id,
                status = 'assigned',
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :project_id
        ");
        
        return $stmt->execute([
            ':teacher_id' => $teacherId,
            ':project_id' => $projectId
        ]);
    }

    /**
     * Récupère un projet par son ID
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, 
                   u.username as student_name,
                   t.first_name || ' ' || t.last_name as teacher_name
            FROM projects p
            LEFT JOIN users u ON p.student_id = u.id
            LEFT JOIN teachers t ON p.assigned_teacher_id = t.id
            WHERE p.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les projets d'un étudiant
     */
    public function getByStudent($studentId) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, 
                   t.first_name || ' ' || t.last_name as teacher_name
            FROM projects p
            LEFT JOIN teachers t ON p.assigned_teacher_id = t.id
            WHERE p.student_id = :student_id
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les projets en attente d'affectation
     */
    public function getPendingProjects() {
        $stmt = $this->pdo->query("
            SELECT p.*, 
                   u.username as student_name
            FROM projects p
            JOIN users u ON p.student_id = u.id
            WHERE p.status = 'pending'
            ORDER BY p.created_at
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les projets par domaine
     */
    public function getByDomain($domain) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, 
                   u.username as student_name,
                   t.first_name || ' ' || t.last_name as teacher_name
            FROM projects p
            JOIN users u ON p.student_id = u.id
            LEFT JOIN teachers t ON p.assigned_teacher_id = t.id
            WHERE p.domains LIKE :domain
            ORDER BY p.status, p.created_at
        ");
        $stmt->execute([':domain' => '%' . $domain . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Met à jour le statut d'un projet
     */
    public function updateStatus($projectId, $status) {
        $stmt = $this->pdo->prepare("
            UPDATE projects 
            SET status = :status,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :project_id
        ");
        
        return $stmt->execute([
            ':status' => $status,
            ':project_id' => $projectId
        ]);
    }

    /**
     * Supprime un projet
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM projects WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}