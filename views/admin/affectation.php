<?php
session_start();

// if (!isset($_SESSION['admin_id']) ) {
//     header('Location: index.php?action=login');
//     exit();
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = $_POST['project_id'];
    $project_theme = $_POST['project_theme'];
    $teacher_id = $_POST['teacher_id'];
    echo "<p>Le projet \"$project_theme\" a été affecté à l'enseignant ID: $teacher_id.</p>";
}
?>

<!-- Inclusion du header -->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="views/css/affectation.css">
    <title>Affectations des Projets</title>
</head>
<body>
    <div class="container">
        <h1>Affectations des Projets Étudiants</h1>
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_SESSION['success']); ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
        <?php if (isset($_SESSION['login-error'])): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($_SESSION['login-error']); ?>
    </div>
    <?php unset($_SESSION['login-error']); ?>
<?php endif; ?>
        <h2>Projets en attente d'affectation</h2>

        <?php if (!empty($pendingProjects)): ?>
            <ul>
                <?php foreach ($pendingProjects as $project): ?>
                    <li class="affectation-li">
                        <strong>Thème :</strong> <?php echo htmlspecialchars($project['theme']); ?><br>
                        <strong>Étudiant :</strong> <?php echo htmlspecialchars($project['student_name']); ?><br>
                        <strong>Binôme :</strong> <?php echo htmlspecialchars($project['binome_name']); ?><br>
                        <strong>Domaine :</strong> <?php echo htmlspecialchars($project['domains']); ?><br>

                        <form action="index.php?action=processAssignment" method="POST" style="margin-top: 5px;">
    <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
    <label for="teacher_<?php echo $project['id']; ?>">Enseignant :</label>
    <select name="teacher_id" id="teacher_<?php echo $project['id']; ?>" required>
        <option value="">-- Choisir un enseignant --</option>
        <?php foreach ($teachers as $teacher): ?>
            <option value="<?php echo $teacher['id']; ?>">
                <?php echo htmlspecialchars($teacher['username']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Affecter</button>
</form>

                    </li>
                    <hr>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucun projet en attente.</p>
        <?php endif; ?>

        <div class="notification">
            <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    echo "L'affectation a bien été enregistrée.";
                }
            ?>
        </div>

        <a href="index.php?action=dashboard-admin">Retour au Tableau de Bord</a>
    </div>
</body>
</html>
