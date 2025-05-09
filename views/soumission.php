<?php
session_start();
include 'header.php'; // Inclure le header commun

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Logique pour traiter la soumission du cahier de charge
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $theme = $_POST['theme'];
    $binome = $_POST['binome'];

    // Enregistrer la soumission dans la base de données ou un fichier
    echo "<p>Cahier de charge soumis pour le thème : $theme, avec binôme : $binome</p>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soumission du Cahier de Charge</title>
    <link rel="stylesheet" href="views/css/soumission.css"> <!-- Chemin du CSS -->
</head>
<body>
    <div class="container">
        <h1>Soumettre un Cahier de Charge</h1>

        <form action="soumission.php" method="POST"> <!-- Changement ici -->
            <div class="input-group">
                <label for="theme">Thème :</label>
                <input type="text" name="theme" id="theme" required><br>
            </div>

            <div class="input-group">
                <label for="binome">Nom du binôme :</label>
                <input type="text" name="binome" id="binome" required><br>
            </div>

            <button type="submit">Soumettre</button>
        </form>

        <div class="notification">
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <p style="color: green;">Votre cahier de charge a été soumis avec succès !</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
