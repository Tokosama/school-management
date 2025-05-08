<?php

require_once '../app/Models/User.php';

class AuthController
{
    public function showRegisterForm()
    {
        require_once '../app/Views/auth/register.php';
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $role = $_POST['role'] ?? '';

            // Validation simple
            if (empty($email) || empty($password) || empty($role) || $password !== $confirmPassword) {
                $_SESSION['error'] = 'Veuillez remplir correctement tous les champs.';
                header('Location: /auth/register');
                exit;
            }

            // Vérifier si l'utilisateur existe déjà
            $userModel = new User();
            if ($userModel->findByEmail($email)) {
                $_SESSION['error'] = 'Un compte avec cet email existe déjà.';
                header('Location: /auth/register');
                exit;
            }

            // Enregistrer l'utilisateur
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $userModel->create($email, $hashedPassword, $role);

            $_SESSION['success'] = 'Inscription réussie. Vous pouvez maintenant vous connecter.';
            header('Location: /auth/login');
            exit;
        }
    }

    public function showLoginForm()
    {
        require_once '../views/auth/login.php';
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $userModel = new User();
            $user = $userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];

                // Redirection en fonction du rôle
                if ($user['role'] === 'etudiant') {
                    header('Location: /etudiant/dashboard');
                } elseif ($user['role'] === 'enseignant') {
                    header('Location: /enseignant/dashboard');
                } else {
                    header('Location: /');
                }
                exit;
            } else {
                $_SESSION['error'] = 'Identifiants incorrects.';
                header('Location: /auth/login');
                exit;
            }
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: /auth/login');
        exit;
    }
}
