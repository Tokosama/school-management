<?php
session_start();

$action = $_GET['action'] ?? 'login'; // Page par défaut : login

// Début du HTML global
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Application PHP</title>

    <?php
    // Inclure le bon CSS selon la page
    switch ($action) {
        case 'login':
            echo '<link rel="stylesheet" href="views/css/login.css">';
            break;
        case 'signup':
            echo '<link rel="stylesheet" href="views/css/signup.css">';
            break;
        // Ajouter les autres au besoin
    }
    ?>
</head>
<body>

<?php include 'views/header.php'; ?>

<?php
// Inclusion de la vue demandée
switch ($action) {
    case 'login':
        include 'views/login.php';
        break;
    case 'signup':
        include 'views/signup.php';
        break;
    case 'soumission':
        include 'views/soumission.php';
        break;
    case 'relance':
        include 'views/relance.php';
        break;
    case 'dashboard':
        include 'views/dashboard.php';
        break;
    default:
        echo "<h1>Page non trouvée</h1>";
}
?>

</body>
</html>