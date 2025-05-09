<?php
// Teacher.php

class Teacher {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->createTable();
    }

    private function createTable() {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS teachers (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                Nom TEXT NOT NULL,
                prenom TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                role TEXT NOT NULL CHECK(role IN ('teacher')),
                domains TEXT NOT NULL CHECK(
                    domains IN (
                        'AL', 'SRC', 'SI',
                        'AL/SI', 'AL/SRC', 'SI/SRC',
                        'AL/SI/SRC'
                    )
                ),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }
    
    public function create($Nom, $prenom, $email,$role, $domains) {
        $stmt = $this->pdo->prepare("
            INSERT INTO teachers (Nom, prenom, email,role, domains)
            VALUES (:Nom, :prenom, :email,:role, :domains)
        ");
        
        return $stmt->execute([
            ':Nom' => $Nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':role' => $role,
            ':domains' => is_array($domains) ? implode(',', $domains) : $domains
        ]);
    }

    

    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT * FROM users WHERE role = 'teacher'
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $Nom, $prenom, $email, $domains) {
        
        // Vérification unicité email (excluant l'enseignant actuel)
        $stmt = $this->pdo->prepare("
            SELECT id FROM teachers 
            WHERE email = :email AND id != :id
        ");
        $stmt->execute([':email' => $email, ':id' => $id]);
        
        if ($stmt->fetch()) {
            throw new InvalidArgumentException("Un enseignant avec cet email existe déjà");
        }
    
        // Mise à jour avec gestion des erreurs
        $stmt = $this->pdo->prepare("
            UPDATE teachers 
            SET Nom = :Nom,
                prenom = :prenom,
                email = :email,
                domains = :domains,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        
        return $stmt->execute([
            ':id' => $id,
            ':Nom' => $Nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':domains' => $domains
        ]);
    }

    public function getByDomain($domain) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM users 
            WHERE role = 'teacher' AND domains LIKE :domains
        ");
        $stmt->execute([':domains' => '%' . $domain . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM teachers WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("
            SELECT 
                id,
                Nom,
                prenom,
                email,
                domains,
                created_at,
                updated_at
            FROM teachers 
            WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($teacher) {
            // Convertit les domaines en tableau si nécessaire
            $teacher['domains'] = explode('/', $teacher['domains']);
        }
        
        return $teacher ?: null;
    }

    public function count(): int
{
    $stmt = $this->pdo->query("SELECT COUNT(*) FROM teachers");
    return (int)$stmt->fetchColumn();
}

public function getAssignedProjects(int $teacherId): array
{
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
            users u ON p.student_id = u.id
        WHERE 
            p.assigned_teacher_id = :teacher_id
        ORDER BY 
            p.status ASC, p.created_at DESC
    ");
    
    $stmt->execute([':teacher_id' => $teacherId]);
    
    // Retourne les résultats bruts (sans conversion des domaines)
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
?>
