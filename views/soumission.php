<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Logique pour traiter la soumission du cahier de charge (à adapter)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $theme = htmlspecialchars($_POST['theme']);
    $binome = htmlspecialchars($_POST['binome']);

    echo "<div class='confirmation'>Cahier de charge soumis pour le thème : <strong>$theme</strong>, avec binôme : <strong>$binome</strong></div>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soumission du Cahier de Charge</title>
    <link rel="stylesheet" href="css/soumission.css">

</head>
<body>
    <div class="container">
        <h1>Soumettre un Cahier de Charge</h1>
        <form action="soumission.php" method="POST">
            <label for="theme">Thème :</label>
            <input type="text" name="theme" id="theme" required>

            <label for="binome">Nom du binôme :</label>
            <input type="text" name="binome" id="binome" required>

            <button type="submit">Soumettre</button>
        </form>
    </div>
</body>
</html>
