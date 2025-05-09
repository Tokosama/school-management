<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/signup.css">
    <title>Inscription</title>

</head>
<body>
    <div class="signup-container">
        <div class="signup-box">
            <h2>Créer un compte</h2>
            <form method="POST" action="index.php?action=signup">
                <div class="input-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" placeholder="Choisissez un nom d'utilisateur" required>
                </div>
                <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="Choisissez un mot de passe" required>
                </div>
                <div class="input-group">
                    <label for="confirm_password">Confirmer le mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmez votre mot de passe" required>
                </div>
                <button type="submit" class="signup-btn">S'inscrire</button>
                <div class="login-link">
                    <p>Vous avez déjà un compte ? <a href="login.php">Se connecter</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
