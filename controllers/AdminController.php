<?php
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Teacher.php';
require_once __DIR__ . '/../Models/Project.php';
require_once __DIR__ . '/../Models/Notification.php';

class AdminController
{
    private $userModel;
    private $teacherModel;
    private $projectModel;
    private $notificationModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->teacherModel = new Teacher();
        $this->projectModel = new Project();
        $this->notificationModel = new Notification();
    }

    /**
     * Vérifie les permissions admin
     */
    private function checkAdminAuth()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /auth/login');
            exit;
        }
    }

    /**
     * Tableau de bord admin
     */
    public function dashboard()
    {
        $this->checkAdminAuth();

        $stats = [
            'pending_projects' => $this->projectModel->countByStatus('pending'),
            'assigned_projects' => $this->projectModel->countByStatus('assigned'),
            'teachers' => $this->teacherModel->count(),
            'unread_notifications' => $this->notificationModel->getUnreadCount(null, 'admin')
        ];

        $recentNotifications = $this->notificationModel->getForUser($_SESSION['user_id'], 'admin', true, 5);

        require_once __DIR__ . '/../Views/admin/dashboard.php';
    }

    /**
     * Gestion des enseignants
     */
    public function manageTeachers()
    {
        $this->checkAdminAuth();

        $teachers = $this->teacherModel->getAll();
        $domainOptions = ['AL', 'SRC', 'SI']; // Pour le formulaire

        require_once __DIR__ . '/../Views/admin/teachers.php';
    }

    /**
     * Crée un nouvel enseignant
     */
    public function createTeacher()
    {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/teachers');
            exit;
        }

        $data = [
            'Nom' => trim($_POST['Nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'email' => filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL),
            'role' => trim($_POST['role'] ?? ''),
            'domains' => $_POST['domains'] ?? []
        ];

        try {
            $this->teacherModel->create(
                Nom: $data['Nom'],
                prenom: $data['prenom'],
                email: $data['email'],
                role: $data['role'],
                domains: $data['domains']
            );

            $_SESSION['success'] = 'Enseignant créé avec succès';
        } catch (InvalidArgumentException $e) {
            $_SESSION['error'] = $e->getMessage();
            $_SESSION['old'] = $data;
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Erreur de base de données : ' . $e->getMessage();
            $_SESSION['old'] = $data;
        }

        header('Location: /admin/teachers');
        exit;
    }

    /**
     * Affectation des projets
     */
    public function assignProjects()
    {
        $this->checkAdminAuth();

        $pendingProjects = $this->projectModel->getPendingProjects();
        $teachers = $this->teacherModel->getAll();

        require_once __DIR__ . '/../Views/admin/assign_projects.php';
    }

    /**
     * Traite l'affectation d'un projet
     */
    public function processAssignment()
    {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/assign-projects');
            exit;
        }

        $projectId = (int)($_POST['project_id'] ?? 0);
        $teacherId = (int)($_POST['teacher_id'] ?? 0);

        try {
            $success = $this->projectModel->assignTeacher($projectId, $teacherId);

            if ($success) {
                $project = $this->projectModel->getById($projectId);
                $teacher = $this->teacherModel->getById($teacherId);

                // Notification à l'étudiant
                $this->notificationModel->create(
                    $_SESSION['user_id'],
                    "Votre projet '{$project['title']}' a été affecté à {$teacher['first_name']} {$teacher['last_name']}",
                    'student',
                    $projectId
                );

                // Notification à l'enseignant
                $this->notificationModel->create(
                    $_SESSION['user_id'],
                    "Nouvelle affectation : Projet '{$project['title']}'",
                    'teacher',
                    $projectId
                );

                $_SESSION['success'] = 'Affectation réussie';
            } else {
                $_SESSION['error'] = 'Erreur lors de l\'affectation';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: /admin/assign-projects');
        exit;
    }

    /**
     * Gestion des notifications
     */
    public function manageNotifications()
    {
        $this->checkAdminAuth();

        $notifications = $this->notificationModel->getForUser(
            $_SESSION['user_id'],
            'admin',
            false // Toutes les notifications
        );

        require_once __DIR__ . '/../Views/admin/notifications.php';
    }

    /**
     * Marque une notification comme lue
     */
    public function markNotificationAsRead($notificationId)
    {
        $this->checkAdminAuth();

        if ($this->notificationModel->markAsRead($notificationId)) {
            $_SESSION['success'] = 'Notification marquée comme lue';
        } else {
            $_SESSION['error'] = 'Erreur lors de la mise à jour';
        }

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/admin/dashboard'));
        exit;
    }
}