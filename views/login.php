<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Supposons que l'authentification soit déjà gérée côté backend
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Exemple basique d'authentification (à ajuster avec ta logique)
    if ($username === 'etudiant' && $password === 'password') {
        $_SESSION['user_id'] = $username;  // Le nom d'utilisateur peut être un ID unique dans une base de données
        header('Location: dashboard.php');
        exit();
    } elseif ($username === 'enseignant' && $password === 'password') {
        $_SESSION['user_id'] = $username;
        header('Location: dashboard.php');
        exit();
    } else {
        $error_message = 'Nom d\'utilisateur ou mot de passe incorrect.';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <title>Connexion</title>
    
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Se connecter</h2>
            <form method="POST" action="index.php?action=login">
                <div class="input-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" placeholder="Entrez votre nom d'utilisateur" required>
                </div>
                <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>
                </div>
                <button type="submit" class="login-btn">Se connecter</button>
                <div class="signup-link">
                    <p>Pas encore de compte ? <a href="signup.php">Inscrivez-vous</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

