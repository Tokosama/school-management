<?php
// Si l'utilisateur est déjà connecté, on redirige vers le dashboard
session_start();
if (isset($_SESSION['Student_id'])) {
    header('Location: index.php?action=dashboard');
    exit();
}
?>
<?php include 'views/header.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="views/css/login.css">
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <h2>Connexion</h2>
        <form action="index.php?action=login" method="POST">
            <div class="input-group">
                <label for="Studentname">Nom d'utilisateur</label>
                <input type="text" name="Studentname" required>
            </div>
            <div class="input-group">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">Se connecter</button>
        </form>

        <div class="signup-link">
            <p>Vous n'avez pas de compte ?
                <a href="index.php?action=signup">Inscrivez-vous</a>
            </p>
        </div>
    </div>
</div>

</body>
</html>
