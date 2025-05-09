<?php
session_start();

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
    <link rel="stylesheet" href="css/relance.css">
    <title>Notification de Relance</title>

</head>
<body>
    <div class="container">
        <h1>Notification de Relance</h1>

        <div class="notification">
            <?php echo $notification_message; ?>
        </div>

        <a href="dashboard.php">Retour au Tableau de Bord</a>
    </div>
</body>
</html>