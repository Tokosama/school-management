<?php
// app/config/database.php

function initializeDatabase($dbPath = 'database.sqlite') {
    try {
        // Création de la connexion et de la base si elle n'existe pas
        $pdo = new PDO("sqlite:" . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de création de la base de données : " . $e->getMessage());
    }
}

// Créer la connexion globale
$pdo = initializeDatabase();