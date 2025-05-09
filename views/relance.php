<?php
session_start();
include 'header.php'; // Inclure le header commun

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$notification_message = "Relance : Un étudiant n'a pas encore été affecté.";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification de Relance</title>
    <link rel="stylesheet" href="views/css/relance.css"> <!-- Veille à ce que le chemin soit correct -->
</head>
<body>
    <div class="container">
        <h1>Notification</h1>
        <p><?php echo $notification_message; ?></p>

        <!-- Retour au Dashboard -->
        <a href="index.php?action=dashboard" class="btn-back">Retour au Tableau de Bord</a>
    </div>
</body>
</html>
