<?php

require_once '../app/Models/Student.php';

class AuthController
{
    public function showRegisterForm()
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
        
        require_once '../app/Views/auth/register.php';
    }

    public function register()
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
                header('Location: /auth/register');
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
                header('Location: /auth/register');
                exit;
            }       
           

            // Enregistrement de l'utilisateur
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $StudentId = $StudentModel->create([
                'Studentname' => $Studentname,
                'password' => $hashedPassword,
                'domains' => $domains,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            if ($StudentId) {
                $_SESSION['success'] = 'Inscription réussie. Vous pouvez maintenant vous connecter.';
                header('Location: /auth/login');
                exit;
            } else {
                $_SESSION['error'] = 'Une erreur est survenue lors de l\'inscription.';
                header('Location: /auth/register');
                exit;
            }
        }
        
        // Si la méthode n'est pas POST
        header('Location: /auth/register');
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
        
        require_once '../app/Views/auth/login.php';
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Nettoyage des entrées
            $Studentname = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);

            $StudentModel = new Student();
            
            // Recherche par email ou username
            if (filter_var($Studentname, FILTER_VALIDATE_EMAIL)) {
                $Student = $StudentModel->findByEmail($Studentname);
            } else {
                $Student = $StudentModel->findByStudentname($Studentname);
            }

            if ($Student && password_verify($password, $Student['password'])) {
                // Régénération de l'ID de session pour prévenir les attaques de fixation
                session_regenerate_id(true);
                
                $_SESSION['Student_id'] = $Student['id'];
                $_SESSION['username'] = $Student['username'];

                // Option "Se souvenir de moi"
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $expires = time() + 60 * 60 * 24 * 30; // 30 jours
                    
                    setcookie('remember_token', $token, $expires, '/', '', true, true);
                    $StudentModel->updateRememberToken($Student['id'], $token, date('Y-m-d H:i:s', $expires));
                }

                // Redirection en fonction du rôle
                switch ($Student['role']) {
                    case 'etudiant':
                        $redirect = '/etudiant/dashboard';
                        break;
                    case 'enseignant':
                        $redirect = '/enseignant/dashboard';
                        break;
                    case 'admin':
                        $redirect = '/admin/dashboard';
                        break;
                    default:
                        $redirect = '/';
                }
                
                header('Location: ' . $redirect);
                exit;
            } else {
                $_SESSION['error'] = 'Identifiants incorrects.';
                header('Location: /auth/login');
                exit;
            }
        }
        
        // Si la méthode n'est pas POST
        header('Location: /auth/login');
        exit;
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