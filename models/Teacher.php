<?php
// Teacher.php

class Teacher {
    private $pdo;

    public function __construct($dbPath = 'database.sqlite') {
        $this->pdo = new PDO("sqlite:" . $dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT * FROM users WHERE role = 'teacher'
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByDomain($domain) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM users 
            WHERE role = 'teacher' AND domain LIKE :domain
        ");
        $stmt->execute([':domain' => '%' . $domain . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
