<?php include 'views/header.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un Enseignant</title>
    <link rel="stylesheet" href="/views/css/style.css"> <!-- Ajoute ton style si nécessaire -->
</head>
<body>
    <div class="container">
        <h1>Ajouter un nouvel enseignant</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error">
                <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="success">
                <?php
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <form action="/index.php?action=ajouterEnseignant" method="POST">
            <div class="form-group">
                <label for="username">Nom complet :</label>
                <input type="text" name="username" id="username" required>
            </div>

            <div class="input-group">
                <label for="filiere">Filière</label>
                <select name="domains" id="filiere" required>
                    <option value="" selected disabled>-- Veuillez choisir une option --</option>
                    <option value="AL">AL (Algorithmique Logicielle)</option>
                    <option value="SRC">SRC (Systèmes, Réseaux et Cloud)</option>
                    <option value="SI">SI (Système d'Information)</option>
                </select>
            </div>>

            <button type="submit">Créer l'enseignant</button>
        </form>

        <a href="/admin/teachers">← Retour à la liste des enseignants</a>
    </div>
</body>
</html>
