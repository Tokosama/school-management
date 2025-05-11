<?php
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Teacher.php';
require_once __DIR__ . '/../models/Project.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/Admin.php';

class AdminController
{
    private $adminModel;

    public function __construct()
    {
        $this->adminModel = new Admin();
    }

    private function checkAdminAuth()
    {
        if (!isset($_SESSION['student_id'])) {
            header('Location: /auth/login');
            exit;
        }
    }

    public function dashboard()
    {
        $this->checkAdminAuth();
        $notifications = $this->adminModel->getAdminNotifications(true);
        require_once __DIR__ . '/../views/admin/adminDashboard.php';
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
    require_once __DIR__ . '/../views/admin/createTeacher.php';
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

            header('Location: index.php?action=createTeacher');
    }

    public function assignProjects()
    {
        $this->checkAdminAuth();
        $pendingProjects = $this->adminModel->getPendingProjects();
        $teachers = $this->adminModel->getAllTeachers();
        var_dump($teachers);
        require_once __DIR__ . '/../views/admin/affectation.php';
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
            var_dump("tesssssssssssssssss");
            $_SESSION['success'] = 'Affectation réussie';
        } catch (Exception $e) {
            var_dump("noooooooooooooooooooooooooooooooo");

            $_SESSION['error'] = $e->getMessage();
        }

     header('Location: index.php?action=affectation');
    }

    public function markNotificationAsRead($notificationId)
    {
        $this->checkAdminAuth();
        $this->adminModel->markNotificationAsRead($notificationId);
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/admin/dashboard'));
    }
}