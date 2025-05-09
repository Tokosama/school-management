<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Exemple simple de rôle étudiant (à ajuster avec ta logique)
$is_student = $user_id === 'etudiant'; 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/dashboard.css">
    <title>Tableau de bord</title>

</head>
<body>
    <div class="container">
        <h1>Bienvenue, <?php echo htmlspecialchars($user_id); ?> !</h1>

        <?php if ($is_student): ?>
            <p>Soumettez votre cahier de charge.</p>
            <a href="soumission.php">Soumettre le cahier de charge</a>
        <?php else: ?>
            <p>Gestion des affectations.</p>
            <a href="teacher_assignment.php">Voir les affectations</a>
        <?php endif; ?>

        <a href="logout.php">Se déconnecter</a>
    </div>
</body>
</html>
