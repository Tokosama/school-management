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
        // Validation des domaines (identique à create)
        $domains = $this->validateDomains($domains);
        
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
}
?>
