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
    <title>Connexion</title>
    <style>
        /* Reset des styles par défaut */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Fond de la page */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container pour le formulaire */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        /* Style pour la boîte de connexion */
        .login-box {
            background: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        /* Titre de la page */
        h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        /* Style pour les groupes d'input */
        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
            display: block;
        }

        .input-group input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border 0.3s;
        }

        .input-group input:focus {
            border: 1px solid #007bff;
            outline: none;
        }

        /* Bouton de connexion */
        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-btn:hover {
            background-color: #0056b3;
        }

        /* Lien pour l'inscription */
        .signup-link {
            margin-top: 20px;
            font-size: 14px;
        }

        .signup-link a {
            color: #007bff;
            text-decoration: none;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }
    </style>
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

