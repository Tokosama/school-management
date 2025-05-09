<?php
// Teacher.php

class Teacher {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->createTable();
    }

    // Création de la table des enseignants
    private function createTable() {
        $this->pdo->exec("
        CREATE TABLE IF NOT EXISTS teachers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
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
    
    // Création d'un enseignant
    public function create($username, $domains) {
        $stmt = $this->pdo->prepare("
            INSERT INTO teachers (username, domains)
            VALUES (:username, :domains)
        ");
        
        return $stmt->execute([
            ':username' => $username,
            ':domains' => is_array($domains) ? implode('/', $domains) : $domains
        ]);
    }

    // Récupérer tous les enseignants
    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT * FROM teachers
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mise à jour d'un enseignant
    public function update($id, $username, $domains) {
        // Mise à jour des informations de l'enseignant
        $stmt = $this->pdo->prepare("
            UPDATE teachers 
            SET username = :username,
                domains = :domains
            WHERE id = :id
        ");
        
        return $stmt->execute([
            ':id' => $id,
            ':username' => $username,
            ':domains' => is_array($domains) ? implode('/', $domains) : $domains
        ]);
    }

    // Récupérer un enseignant par son ID
    public function getById($id) {
        $stmt = $this->pdo->prepare("
            SELECT id, username, domains
            FROM teachers 
            WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($teacher) {
            // Convertir les domaines en tableau
            $teacher['domains'] = explode('/', $teacher['domains']);
        }
        
        return $teacher ?: null;
    }

    // Compter le nombre total d'enseignants
    public function count(): int {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM teachers");
        return (int)$stmt->fetchColumn();
    }

    // Supprimer un enseignant
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM teachers WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // Récupérer les projets assignés à un enseignant
    public function getAssignedProjects(int $teacherId): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                p.id,
                p.title,
                p.description,
                p.domains, 
                p.status,
                p.file_path,
                p.created_at as project_created_at,
                u.username as student_username,
                u.email as student_email
            FROM 
                projects p
            JOIN 
                students u ON p.student_id = u.id
            WHERE 
                p.assigned_teacher_id = :teacher_id
            ORDER BY 
                p.status ASC, p.created_at DESC
        ");
        
        $stmt->execute([':teacher_id' => $teacherId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
