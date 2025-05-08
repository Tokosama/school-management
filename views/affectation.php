<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== 'enseignant') {
    header('Location: login.php');
    exit();
}

// Logique pour afficher les étudiants non affectés et les affecter
// Exemple statique (à remplacer par une vraie logique avec la base de données)
$students = [
    ['name' => 'Etudiant 1', 'assigned' => false],
    ['name' => 'Etudiant 2', 'assigned' => false],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = $_POST['student_name'];
    // Logique pour affecter l'étudiant
    echo "<p>L'étudiant $student_name a été affecté.</p>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affectations des Étudiants</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f2f5f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: 60px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #333;
        }

        h2 {
            color: #555;
            margin-bottom: 20px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            background-color: #f9f9f9;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        button {
            padding: 8px 15px;
            font-size: 1em;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        .notification {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #c3e6cb;
            font-weight: bold;
            margin-top: 20px;
        }

        a {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 20px;
            text-decoration: none;
            background-color: #007BFF;
            color: white;
            border-radius: 6px;
            font-weight: bold;
        }

        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Affectations des Étudiants</h1>

        <h2>Étudiants en attente d'affectation</h2>
        <ul>
            <?php foreach ($students as $student): ?>
                <?php if (!$student['assigned']): ?>
                    <li>
                        <?php echo $student['name']; ?>
                        <form action="teacher_assignment.php" method="POST" style="display:inline;">
                            <input type="hidden" name="student_name" value="<?php echo $student['name']; ?>">
                            <button type="submit">Affecter</button>
                        </form>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>

        <div class="notification">
            <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    echo "L'affectation a bien été effectuée !";
                }
            ?>
        </div>

        <a href="dashboard.php">Retour au Tableau de Bord</a>
    </div>
</body>
</html>