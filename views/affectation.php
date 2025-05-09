<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== 'enseignant') {
    header('Location: login.php');
    exit();
}

// Logique pour afficher les étudiants non affectés et les affecter
// Exemple statique (à remplacer par une vraie logique avec la base de données)
$students = [
    ['name' => 'Etudiant 1', 'assigned' => false],
    ['name' => 'Etudiant 2', 'assigned' => false],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = $_POST['student_name'];
    // Logique pour affecter l'étudiant
    echo "<p>L'étudiant $student_name a été affecté.</p>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/affectation.css">
    <title>Affectations des Étudiants</title>
    
</head>
<body>
    <div class="container">
        <h1>Affectations des Étudiants</h1>

        <h2>Étudiants en attente d'affectation</h2>
        <ul>
            <?php foreach ($students as $student): ?>
                <?php if (!$student['assigned']): ?>
                    <li>
                        <?php echo $student['name']; ?>
                        <form action="teacher_assignment.php" method="POST" style="display:inline;">
                            <input type="hidden" name="student_name" value="<?php echo $student['name']; ?>">
                            <button type="submit">Affecter</button>
                        </form>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>

        <div class="notification">
            <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    echo "L'affectation a bien été effectuée !";
                }
            ?>
        </div>

        <a href="dashboard.php">Retour au Tableau de Bord</a>
    </div>
</body>
</html>