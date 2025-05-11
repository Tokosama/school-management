<?php

require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Admin.php';

class AuthController
{
    public function showsignupForm()
    {
        // Vérification si l'utilisateur est déjà connecté
        if (isset($_SESSION['id'])) {
            header('Location: /');
            exit;
        }
        
        // Nettoyage des messages d'erreur/succès de la session
        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);
        
        require_once '../views/auth/signup.php';
    }

    public function studentSignup()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Nettoyage des entrées
            $Studentname = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $domains = $_POST['domains'] ?? '';
         
            // Validation
            $errors = [];
            
            if (empty($Studentname) || strlen($Studentname) < 3) {
                $errors[] = 'Le nom d\'utilisateur doit contenir au moins 3 caractères.';
            }
            
            if (empty($password) || strlen($password) < 6) {
                $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
            }
            
            
            
            if (empty($domains)) {
                $errors[] = 'La filiere est requis.';
            }
            

            if (!empty($errors)) {
                $_SESSION['error'] = implode('<br>', $errors);
                $_SESSION['old'] = [
                    'username' => $Studentname,
                    'password' => $password,
                    'domains' => $domains
                ];
                header('Location: /auth/signup');
                exit;
            }

            // Vérification de l'unicité de l'utilisateur
            $StudentModel = new Student();
            if ($StudentModel->findByStudentname($Studentname)) {
                $_SESSION['error'] = 'Un compte avec ce nom d\'utilisateur existe déjà.';
                $_SESSION['old'] = [
                    'username' => $Studentname,
                    'password' => $password,
                    'domains' => $domains
                ];
                header('Location: /auth/signup');
                exit;
            }       
           

            // Enregistrement de l'utilisateur
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $StudentId = $StudentModel->create([
                'username' => $Studentname,
                'password' => $hashedPassword,
                'domains' => $domains,
                'created_at' => date('Y-m-d H:i:s')
            ]);
var_dump($StudentId);
            if ($StudentId) {
                $_SESSION['success'] = 'Inscription réussie. Vous pouvez maintenant vous connecter.';
                header('Location: /student/auth/login');
                exit;
            } else {
                $_SESSION['error'] = 'Une erreur est survenue lors de l\'inscription.';
                header('Location: /auth/signup');
                exit;
            }
        }
        
        // Si la méthode n'est pas POST
require_once __DIR__ . '/../views/student/auth/signup.php';
        exit;
    }
public function adminSignup()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Nettoyage des entrées
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validation
        $errors = [];

        if (empty($username) || strlen($username) < 3) {
            $errors[] = 'Le nom d\'utilisateur doit contenir au moins 3 caractères.';
        }

        if (empty($password) || strlen($password) < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
        }
var_dump("teeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee");

        // Si des erreurs existent
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $_SESSION['old'] = [
                'username' => $username,
                'password' => $password,
            ];
            header('Location: /auth/admin/signup');
            exit;
        }

        // Vérification de l'unicité du nom d'utilisateur
        $AdminModel = new Admin();  // Assure-toi que tu as une classe Admin qui gère les interactions avec la base de données
        if ($AdminModel->findByUsername($username)) {
            $_SESSION['error'] = 'Un compte avec ce nom d\'utilisateur existe déjà.';
            $_SESSION['old'] = [
                'username' => $username,
                'password' => $password,
            ];
            header('Location: /auth/admin/signup');
            exit;
        }
        // Enregistrement de l'administrateur

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $adminId = $AdminModel->createAdmin( $username,$hashedPassword,
        );
        if ($adminId) {
            $_SESSION['success'] = 'Inscription réussie. Vous pouvez maintenant vous connecter.';
            header('Location: index.php?action=admin/login');
            exit;
        } else {
            $_SESSION['error'] = 'Une erreur est survenue lors de l\'inscription.';
            header('Location: /auth/admin/signup');
            exit;
        }
    }

    // Si la méthode n'est pas POST
    require_once __DIR__ . '/../views/admin/auth/signup.php';
    exit;
}

    public function showLoginForm()
    {
        // Vérification si l'utilisateur est déjà connecté
        if (isset($_SESSION['Student_id'])) {
            header('Location: /');
            exit;
        }
        
        // Nettoyage des messages d'erreur/succès de la session
        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);
        
        require_once '../views/auth/login.php';
    }

    public function studentLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Nettoyage des entrées
            $Studentname = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);

            $StudentModel = new Student();
            
            // Recherche par username
            
                $Student = $StudentModel->findByStudentname($Studentname);
            
                var_dump($Student);

            if ($Student && password_verify($password, $Student['password'])) {
                // Régénération de l'ID de session pour prévenir les attaques de fixation
                session_regenerate_id(true);
                
                $_SESSION['student_id'] = $Student['id'];
                $_SESSION['username'] = $Student['username'];

                // Redirection en fonction du rôle
              
                    header('Location: index.php?action=dashboard-etudiant');
                exit;
            } else {
                var_dump("incoreectttt noooo");

                $_SESSION['error'] = 'Identifiants incorrects.';
                require_once __DIR__ . '/../views/auth/login.php';
                exit;
            }
            
        }
        
        // Si la méthode n'est pas POST
        require_once __DIR__ . '/../views/student/auth/login.php';
    }
public function adminLogin()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Nettoyage des entrées
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']); // "Se souvenir de moi"

        // Modèle Admin
        $AdminModel = new Admin();
        
        // Recherche par username
        $admin = $AdminModel->findByUsername($username);

        if ($admin && password_verify($password, $admin['password'])) {
            
            // Régénération de l'ID de session pour prévenir les attaques de fixation
            session_regenerate_id(true);
            
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];

            // Option "Se souvenir de moi"
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expires = time() + 60 * 60 * 24 * 30; // 30 jours
                
                setcookie('remember_token', $token, $expires, '/', '', true, true);
              //  $AdminModel->updateRememberToken($admin['id'], $token, date('Y-m-d H:i:s', $expires));
            }
                   
                header('Location: index.php?action=affectation');
                exit;
            // Redirection vers la page d'administration après la connexion
        } else {
            $_SESSION['error'] = 'Identifiants incorrects.';
            require_once __DIR__ . '/../views/admin/auth/login.php'; // Charger la vue pour la page de connexion admin
            exit;
        }
    }

    // Si la méthode n'est pas POST
    require_once __DIR__ . '/../views/admin/auth/login.php'; // Charger la vue de la page de connexion admin
}

    public function logout()
    {
        // Suppression du token "Se souvenir de moi" si existant
        if (isset($_COOKIE['remember_token'])) {
            $StudentModel = new Student();
            $StudentModel->updateRememberToken($_SESSION['Student_id'], null, null);
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        // Destruction complète de la session
        $_SESSION = array();
        session_destroy();
        
        header('Location: /auth/login');
        exit;
    }
}