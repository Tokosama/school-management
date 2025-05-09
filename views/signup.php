<?php
session_start();
include 'views/header.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="views/css/signup.css">
</head>
<body>
<div class="signup-container">
    <div class="signup-box">
        <h2>Inscription</h2>
        <form action="index.php?action=signup" method="POST">

            <div class="input-group">
                <label for="last_name">Nom</label>
                <input type="text" name="last_name" required>
            </div>

            <div class="input-group">
                <label for="first_name">Prénom</label>
                <input type="text" name="first_name" required>
            </div>

            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="input-group">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" required>
            </div>

            <div class="input-group">
                <label for="filiere">Filière</label>
                <select name="filiere" id="filiere" required>
                    <option value="" selected disabled>-- Veuillez choisir une option --</option>
                    <option value="AL">AL (Algorithmique Logicielle)</option>
                    <option value="SRC">SRC (Systèmes, Réseaux et Cloud)</option>
                    <option value="SI">SI (Système d'Information)</option>
                </select>
            </div>

            <button type="submit" class="signup-btn">S'inscrire</button>
        </form>

        <div class="signup-link">
            <p>Déjà un compte ? <a href="index.php?action=login">Se connecter</a></p>
        </div>
    </div>
</div>
</body>
</html>
