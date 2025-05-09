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
                teacher_id INTEGER,
                binome_name TEXT NOT NULL,
                theme TEXT NOT NULL,
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
                    status IN ('pending', 'assigned')
                ),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (student_id) REFERENCES students(id),
                FOREIGN KEY (teacher_id) REFERENCES teachers(id)
            );
        ");
    }

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
            throw new InvalidArgumentException("Invalid domain combination. Allowed combinations are: " . implode(', ', $allowed));
        }

        return $domains;
    }

    public function create($studentId, $binomeName, $theme, $description, $domains, $filePath) {
        try {
            $domains = $this->validateDomains($domains);

            $stmt = $this->pdo->prepare("
                INSERT INTO projects (
                    student_id, binome_name, theme, description, domains, file_path
                ) VALUES (
                    :student_id, :binome_name, :theme, :description, :domains, :file_path
                )
            ");
            return $stmt->execute([
                ':student_id' => $studentId,
                ':binome_name' => $binomeName,
                ':theme' => $theme,
                ':description' => $description,
                ':domains' => $domains,
                ':file_path' => $filePath
            ]);
        } catch (PDOException $e) {
            // Handle PDO error here
            return false;
        }
    }

    public function assignTeacher($projectId, $teacherId) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE projects 
                SET teacher_id = :teacher_id,
                    status = 'assigned',
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :project_id
            ");

            return $stmt->execute([
                ':teacher_id' => $teacherId,
                ':project_id' => $projectId
            ]);
        } catch (PDOException $e) {
            // Handle PDO error here
            return false;
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, 
                       u.username as student_name,
                       p.binome_name,
                       t.username as teacher_name
                FROM projects p
                LEFT JOIN students u ON p.student_id = u.id
                LEFT JOIN teachers t ON p.teacher_id = t.id
                WHERE p.id = :id
            ");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle PDO error here
            return false;
        }
    }

    public function getByStudent($studentId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, 
                       u.username as student_name,
                       p.binome_name,
                       t.username as teacher_name
                FROM projects p
                LEFT JOIN students u ON p.student_id = u.id
                LEFT JOIN teachers t ON p.teacher_id = t.id
                WHERE p.student_id = :student_id
            ");
            $stmt->execute([':student_id' => $studentId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle PDO error here
            return false;
        }
    }

    public function getByDomain($domain) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, 
                       u.username as student_name,
                       p.binome_name,
                       t.username as teacher_name
                FROM projects p
                LEFT JOIN students u ON p.student_id = u.id
                LEFT JOIN teachers t ON p.teacher_id = t.id
                WHERE p.domains LIKE :domain
            ");
            $stmt->execute([':domain' => '%' . $domain . '%']);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle PDO error here
            return false;
        }
    }
}
