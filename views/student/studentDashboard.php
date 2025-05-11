<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header('Location: index.php?action=login');
    exit();
}

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

<div class="dashboard-container">
    <div class="welcome-box">
        <h1>Bienvenue, <?php echo htmlspecialchars($studentName); ?> !</h1>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert-success">
                <?php echo htmlspecialchars($_SESSION['success']); ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
    </div>

    <div class="project-section">
        <?php if ($submittedProject): ?>
            <div class="project-card">
                <h2>Votre projet soumis</h2>
                <p><strong>Thème :</strong> <?php echo htmlspecialchars($submittedProject['theme']); ?></p>
                <p><strong>Statut :</strong> 
                    <span class="status <?php echo $submittedProject['status']; ?>">
                        <?php echo htmlspecialchars($submittedProject['status']); ?>
                    </span>
                </p>

                <?php if ($submittedProject['status'] === 'assigned' && isset($teacher)): ?>
                    <p><strong>Professeur assigné :</strong> <?php echo htmlspecialchars($teacher['username']); ?></p>
                <?php else: ?>
                    <form action="index.php?action=relanceProjet" method="POST">
                        <input type="hidden" name="project_id" value="<?php echo $submittedProject['id']; ?>">
                        <button type="submit" class="btn relancer">Relancer</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="no-project">Vous n'avez pas encore soumis de projet.</p>
            <a href="index.php?action=soumission" class="btn soumettre">Soumettre un cahier de charge</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
