<?php
require_once __DIR__ . '/../Models/Teacher.php';
require_once __DIR__ . '/../Models/Notification.php';

class EnseignantController
{
    private $teacherModel;
    private $notificationModel;

    public function __construct()
    {
        $this->teacherModel = new Teacher();
        $this->notificationModel = new Notification();
    }

    /**
     * Affiche le tableau de bord enseignant
     */
    public function dashboard()
    {
        // Vérification de l'authentification et du rôle
        if (!isset($_SESSION['Student_id']) || $_SESSION['Student_role'] !== 'teacher') {
            header('Location: /auth/login');
            exit;
        }

        // Récupération des projets assignés
        $teacherId = $_SESSION['Student_id'];
        $projects = $this->teacherModel->getAssignedProjects($teacherId);

        // Récupération des notifications
        $notifications = $this->notificationModel->getForStudent(
            $teacherId,
            'teacher',
            true // Unread only
        );

        require_once __DIR__ . '/../Views/enseignant/dashboard.php';
    }

    /**
     * Affiche le formulaire de profil
     */
    public function showProfile()
    {
        $this->checkTeacherAuth();

        $teacher = $this->teacherModel->getById($_SESSION['Student_id']);
        $domainOptions = ['AL', 'SRC', 'SI']; // Options pour le formulaire

        require_once __DIR__ . '/../Views/enseignant/profile.php';
    }

    /**
     * Met à jour le profil enseignant
     */
    public function updateProfile()
    {
        $this->checkTeacherAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /enseignant/profile');
            exit;
        }

        $teacherId = $_SESSION['Student_id'];
        $Nom = trim($_POST['Nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $domains = $_POST['domains'] ?? [];

        try {
            $success = $this->teacherModel->update(
                $teacherId,
                $Nom,
                $prenom,
                $email,
                $domains
            );

            if ($success) {
                $_SESSION['success'] = 'Profil mis à jour avec succès';
            } else {
                $_SESSION['error'] = 'Aucune modification détectée';
            }
        } catch (InvalidArgumentException $e) {
            $_SESSION['error'] = $e->getMessage();
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Erreur de base de données';
        }

        header('Location: /enseignant/profile');
        exit;
    }

    /**
     * Affiche les projets assignés
     */
    public function showProjects()
    {
        $this->checkTeacherAuth();

        $projects = $this->teacherModel->getAssignedProjects($_SESSION['Student_id']);
        require_once __DIR__ . '/../Views/enseignant/projects.php';
    }

    /**
     * Marque une notification comme lue
     */
    public function markNotificationAsRead($notificationId)
    {
        $this->checkTeacherAuth();

        if ($this->notificationModel->markAsRead($notificationId)) {
            $_SESSION['success'] = 'Notification marquée comme lue';
        } else {
            $_SESSION['error'] = 'Erreur lors de la mise à jour';
        }

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/enseignant/dashboard'));
        exit;
    }

    /**
     * Vérifie l'authentification et le rôle enseignant
     */
    private function checkTeacherAuth()
    {
        if (!isset($_SESSION['Student_id']) || $_SESSION['Student_role'] !== 'teacher') {
            header('Location: /auth/login');
            exit;
        }
    }
}