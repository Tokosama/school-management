<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Teacher.php';
require_once __DIR__ . '/models/Project.php';
require_once __DIR__ . '/models/Notification.php';

// Crée toutes les tables en instanciant les modèles
new User();
new Teacher();
new Project();
new Notification();

echo "Base de données initialisée avec succès!\n";