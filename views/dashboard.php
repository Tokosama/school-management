<?php
session_start();

if (!isset($_SESSION['Student_id'])) {
    header('Location: index.php?action=login');
    exit();
}

$Student_id = $_SESSION['Student_id'];

// Exemple simple de rôle étudiant (à ajuster avec ta logique)
$is_student = $Student_id === 'etudiant'; 
?>

<!-- Inclusion du header -->
<?php include 'views/header.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="views/css/dashboard.css">
    <title>Tableau de bord</title>
</head>
<body>
    <div class="container">
        <h1>Bienvenue, <?php echo htmlspecialchars($Student_id); ?> !</h1>

        <?php if ($is_student): ?>
            <p>Soumettez votre cahier de charge.</p>
            <a href="index.php?action=soumission">Soumettre le cahier de charge</a>
        <?php else: ?>
            <p>Gestion des affectations.</p>
            <a href="index.php?action=affectation">Voir les affectations</a>
        <?php endif; ?>

        <a href="index.php?action=logout">Se déconnecter</a>
    </div>
</body>
</html>