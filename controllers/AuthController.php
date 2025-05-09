<?php

require_once '../app/Models/User.php';

class AuthController
{
    public function showRegisterForm()
    {
        // Vérification si l'utilisateur est déjà connecté
        if (isset($_SESSION['student_id'])) {
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
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? '';
            $filiere = $_POST['filiere'] ?? '';
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);

            // Validation
            $errors = [];
            
            if (empty($username) || strlen($username) < 3) {
                $errors[] = 'Le nom d\'utilisateur doit contenir au moins 3 caractères.';
            }
            
            if (empty($password) || strlen($password) < 6) {
                $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
            }
            
            if (!in_array($role, ['etudiant', 'enseignant', 'admin'])) {
                $errors[] = 'Rôle invalide.';
            }
            
            if (empty($filiere)) {
                $errors[] = 'La filiere est requis.';
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Adresse email invalide.';
            }

            if (!empty($errors)) {
                $_SESSION['error'] = implode('<br>', $errors);
                $_SESSION['old'] = [
                    'username' => $username,
                    'email' => $email,
                    'role' => $role,
                    'filiere' => $filiere
                ];
                header('Location: /auth/register');
                exit;
            }

            // Vérification de l'unicité de l'utilisateur
            $userModel = new User();
            if ($userModel->findByUsername($username)) {
                $_SESSION['error'] = 'Un compte avec ce nom d\'utilisateur existe déjà.';
                $_SESSION['old'] = [
                    'username' => $username,
                    'email' => $email,
                    'role' => $role,
                    'filiere' => $filiere
                ];
                header('Location: /auth/register');
                exit;
            }
            
            if ($userModel->findByEmail($email)) {
                $_SESSION['error'] = 'Un compte avec cette adresse email existe déjà.';
                $_SESSION['old'] = [
                    'username' => $username,
                    'email' => $email,
                    'role' => $role,
                    'filiere' => $filiere
                ];
                header('Location: /auth/register');
                exit;
            }

            // Enregistrement de l'utilisateur
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $userId = $userModel->create([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => $role,
                'filiere' => $filiere,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            if ($userId) {
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
        if (isset($_SESSION['user_id'])) {
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
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);

            $userModel = new User();
            
            // Recherche par email ou username
            if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
                $user = $userModel->findByEmail($username);
            } else {
                $user = $userModel->findByUsername($username);
            }

            if ($user && password_verify($password, $user['password'])) {
                // Régénération de l'ID de session pour prévenir les attaques de fixation
                session_regenerate_id(true);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_username'] = $user['username'];

                // Option "Se souvenir de moi"
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $expires = time() + 60 * 60 * 24 * 30; // 30 jours
                    
                    setcookie('remember_token', $token, $expires, '/', '', true, true);
                    $userModel->updateRememberToken($user['id'], $token, date('Y-m-d H:i:s', $expires));
                }

                // Redirection en fonction du rôle
                switch ($user['role']) {
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
            $userModel = new User();
            $userModel->updateRememberToken($_SESSION['user_id'], null, null);
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        // Destruction complète de la session
        $_SESSION = array();
        session_destroy();
        
        header('Location: /auth/login');
        exit;
    }
}