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
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 450px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            color: #555;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            border: none;
            color: white;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .confirmation {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #c3e6cb;
            margin: 20px auto;
            width: 90%;
            text-align: center;
        }
    </style>
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
