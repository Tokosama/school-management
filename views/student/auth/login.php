<?php
// Si l'utilisateur est déjà connecté, on redirige vers le dashboard
// session_start();
// if (isset($_SESSION['Student_id'])) {
//     header('Location: index.php?action=dashboard');
//     exit();
// }
// ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="/views/css/login.css">
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <h2>Connexion</h2>
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

        <form action="/index.php?action=student/login" method="POST">
            <div class="input-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">Se connecter</button>
        </form>

        <div class="signup-link">
            <p>Vous n'avez pas de compte ?
                <a href="index.php?action=student/signup">Inscrivez-vous</a>
            </p>
        </div>
    </div>
</div>

</body>
</html>
