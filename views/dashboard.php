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
    <title>Tableau de bord</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f2f5f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 60px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #333;
        }

        p {
            color: #555;
            font-size: 1.1em;
            margin-bottom: 20px;
        }

        a {
            display: inline-block;
            margin: 10px 5px;
            padding: 12px 20px;
            text-decoration: none;
            background-color: #007BFF;
            color: white;
            border-radius: 6px;
            font-weight: bold;
        }

        a:hover {
            background-color: #0056b3;
        }
    </style>
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
