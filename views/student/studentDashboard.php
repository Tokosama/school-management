<?php
session_start();

// Vérifie si l'étudiant est connecté
if (!isset($_SESSION['student_id'])) {
    header('Location: index.php?action=login');
    exit();
}

// Affichage du nom de l'étudiant (présumé que tu as une variable $studentId)
$studentId = $_SESSION['student_id'];
$studentName = $_SESSION['username'];

?>

<!-- Inclusion du header -->
<?php include 'views/header.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="views/css/dashboard.css">
    <title>Tableau de bord étudiant</title>
</head>
<body>
   <div class="container">
    <h1>Bienvenue, <?php echo htmlspecialchars($studentName); ?> !</h1>

    <?php if ($submittedProject): ?>
        <h2>Votre projet soumis</h2>
        <p><strong>Thème :</strong> <?php echo htmlspecialchars($submittedProject['theme']); ?></p>
        <p><strong>Statut :</strong> <?php echo htmlspecialchars($submittedProject['status']); ?></p>

        <?php if ($submittedProject['status'] === 'assigned' && isset($teacher)): ?>
            <h3>Professeur assigné : <?php echo htmlspecialchars($teacher['username']); ?></h3>
        <?php else: ?>
            <!-- Bouton de relance si le statut n'est pas 'assigned' -->
            <form action="index.php?action=relanceProjet" method="POST">
                <input type="hidden" name="project_id" value="<?php echo $submittedProject['id']; ?>">
                <button type="submit">Relancer</button>
            </form>
        <?php endif; ?>
    <?php else: ?>
        <p>Vous n'avez pas encore soumis de projet.</p>

        <!-- Bouton pour soumettre un projet uniquement si aucun projet n'est soumis -->
        <div style="margin-top: 20px;">
            <a href="index.php?action=soumission" class="btn">Soumettre un cahier de charge</a>
        </div>
    <?php endif; ?>

    <a href="index.php?action=logout">Se déconnecter</a>
</div>

</body>
</html>
