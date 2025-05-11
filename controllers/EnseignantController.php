<?php
require_once __DIR__ . '/../models/Teacher.php';
require_once __DIR__ . '/../models/Notification.php';

class EnseignantController
{
    private $teacherModel;
    private $notificationModel;

    public function __construct()
    {
        $this->teacherModel = new Teacher();
        $this->notificationModel = new Notification();
    }

    public function dashboard()
    {
        $this->checkTeacherAuth();

        $teacherId = $_SESSION['teacher_id'];
        $projects = $this->teacherModel->getAssignedProjects($teacherId);

        $notifications = $this->notificationModel->getForUser(
            $teacherId,
            'teacher',
             // Unread only
        );

        require_once __DIR__ . '/../Views/enseignant/dashboard.php';
    }

    public function showProfile()
    {
        $this->checkTeacherAuth();

        $teacher = $this->teacherModel->getById($_SESSION['teacher_id']);
        $domainOptions = ['AL', 'SRC', 'SI'];

        require_once __DIR__ . '/../Views/enseignant/profile.php';
    }

    public function updateProfile()
    {
        $this->checkTeacherAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /enseignant/profile');
            exit;
        }

        $teacherId = $_SESSION['teacher_id'];
        $username = trim($_POST['username'] ?? '');
        $domains = $_POST['domains'] ?? [];

        try {
            $success = $this->teacherModel->update(
                $teacherId,
                $username,
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

    public function showProjects()
    {
        $this->checkTeacherAuth();

        $projects = $this->teacherModel->getAssignedProjects($_SESSION['teacher_id']);
        require_once __DIR__ . '/../Views/enseignant/projects.php';
    }

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

    private function checkTeacherAuth()
    {
        if (!isset($_SESSION['teacher_id']) ) {
            header('Location: /auth/login');
            exit;
        }
    }
}