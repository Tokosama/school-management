<?php
session_start();

// Liste des actions autorisées
$action = $_GET['action'] ?? 'student/login';

// Inclure les contrôleurs nécessaires
require_once 'config/database.php'; // définit $pdo

require_once 'controllers/AuthController.php';
require_once 'controllers/EtudiantController.php';
require_once 'controllers/AdminController.php';

//require_once 'controllers/DashboardController.php';
// Ajoute ici d'autres contrôleurs au besoin

// Charger le header
include 'views/header.php';

// Début HTML
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Application PHP</title>

    <?php
    // CSS par action (si nécessaire)
    if (in_array($action, ['login', 'signup'])) {
        echo "<link rel='stylesheet' href='/views/css/{$action}.css'>";
    }
    ?>
</head>
<body>

<?php
// Routeur simple basé sur l'action
switch ($action) {
    case 'student/login':
        $controller = new AuthController();
        $controller->studentLogin();
        break;

    case 'student/signup':
        $controller = new AuthController();
        $controller->studentSignup();
        break;

    
    case 'admin/login':
        $controller = new AuthController();
        $controller->adminLogin();
        break;
    case 'admin/signup':
        $controller = new AuthController();
        $controller->adminSignup();
        break;
    case 'soumission':
        $controller = new EtudiantController();
        $controller->showSubmissionForm();
        break;
    case 'affectation':
        $controller = new AdminController();
        $controller->assignProjects();
        break;
    case 'ajouterEnseignant':
        $controller = new AdminController();
        $controller->createTeacher();
        break;
    case 'processAssignment':
        $controller = new AdminController();
        $controller->processAssignment();
        break;
    case 'soumettre-projet':
            $controller = new EtudiantController();
            $controller->submitCahier();
            break;
    case 'dashboard-etudiant':
        $controller = new EtudiantController();
        $controller->dashboard();
        break;

 case 'relanceProjet':
        $controller = new EtudiantController();
        $controller->relancer();
        break;

    case 'dashboard-admin':
        $controller = new AdminController();
        $controller->dashboard();

    break;


   

    default:
        echo "<h1>Page non trouvée</h1>";
}
?>

</body>
</html>
