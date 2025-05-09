<?php
require_once __DIR__ . '/../Models/Student.php';
require_once __DIR__ . '/../Models/Teacher.php';
require_once __DIR__ . '/../Models/Project.php';
require_once __DIR__ . '/../Models/Notification.php';

class AdminController
{
    private $adminModel;

    public function __construct()
    {
        $this->adminModel = new Admin();
    }

    private function checkAdminAuth()
    {
        if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
            header('Location: /auth/login');
            exit;
        }
    }

    public function dashboard()
    {
        $this->checkAdminAuth();
        $stats = $this->adminModel->getDashboardStats();
        $notifications = $this->adminModel->getAdminNotifications(true);
        require_once __DIR__ . '/../Views/admin/dashboard.php';
    }

    public function manageTeachers()
    {
        $this->checkAdminAuth();
        $teachers = $this->adminModel->getAllTeachers();
        $domainOptions = ['AL', 'SRC', 'SI'];
        require_once __DIR__ . '/../Views/admin/teachers.php';
    }

    public function createTeacher()
    {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/teachers');
            exit;
        }

        try {
            $this->adminModel->createTeacher(
                $_POST['username'],
                $_POST['domains']
            );
            $_SESSION['success'] = 'Enseignant créé avec succès';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: /admin/teachers');
    }

    public function assignProjects()
    {
        $this->checkAdminAuth();
        $pendingProjects = $this->adminModel->getPendingProjects();
        $teachers = $this->adminModel->getAllTeachers();
        require_once __DIR__ . '/../Views/admin/assign_projects.php';
    }

    public function processAssignment()
    {
        $this->checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/assign-projects');
            exit;
        }

        try {
            $this->adminModel->assignTeacherToProject(
                $_POST['project_id'],
                $_POST['teacher_id']
            );
            $_SESSION['success'] = 'Affectation réussie';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: /admin/assign-projects');
    }

    public function markNotificationAsRead($notificationId)
    {
        $this->checkAdminAuth();
        $this->adminModel->markNotificationAsRead($notificationId);
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/admin/dashboard'));
    }
}