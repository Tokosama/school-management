<?php
// app/Models/Admin.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Teacher.php';
require_once __DIR__ . '/Project.php';
require_once __DIR__ . '/Notification.php';

class Admin {
    private $pdo;
    private $teacherModel;
    private $projectModel;
    private $notificationModel;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->teacherModel = new Teacher();
        $this->projectModel = new Project();
        $this->notificationModel = new Notification();

        $this->createTable(); // Assure la création de la table admin
    }

    /**
     * Création de la table admin si elle n'existe pas
     */
    private function createTable() {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS admins (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL
            );
        ");
    }

    /**
     * Création d'un compte admin
     */
    public function createAdmin($username, $password) {
        $stmt = $this->pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        return $stmt->execute([
            $username,
            password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    /**
     * Gestion des enseignants
     */
    public function createTeacher($username, $domains) {
        return $this->teacherModel->create($username, $domains);
    }

    public function updateTeacher($username, $domains) {
        return $this->teacherModel->update($username, $domains);
    }

    public function deleteTeacher($id) {
        // Vérifier que l'enseignant n'est pas affecté à des projets
        $projects = $this->projectModel->getByTeacher($id);
        if (!empty($projects)) {
            throw new Exception("Impossible de supprimer : l'enseignant est affecté à des projets");
        }
        
        return $this->teacherModel->delete($id);
    }

    public function getAllTeachers() {
        return $this->teacherModel->getAll();
    }

    /**
     * Gestion des projets
     */
    public function assignTeacherToProject($projectId, $teacherId) {
        $project = $this->projectModel->getById($projectId);
        if (!$project) {
            throw new Exception("Projet introuvable");
        }

        $teacher = $this->teacherModel->getById($teacherId);
        if (!$teacher) {
            throw new Exception("Enseignant introuvable");
        }

        // Vérifier la compatibilité des domaines
        $teacherDomains = explode('/', $teacher['domains']);
        $projectDomains = explode('/', $project['domains']);
        
        if (empty(array_intersect($teacherDomains, $projectDomains))) {
            throw new Exception("L'enseignant n'a pas les compétences requises pour ce projet");
        }

        // Effectuer l'affectation
        $success = $this->projectModel->assignTeacher($projectId, $teacherId);
        
        if ($success) {
            // Notifier l'étudiant
            $this->notificationModel->create(
                $_SESSION['Student_id'],
                "Votre projet '{$project['theme']}' a été affecté à {$teacher['username']}",
                'student',
                $projectId
            );
            
            // Notifier l'enseignant
            $this->notificationModel->create(
                $_SESSION['Student_id'],
                "Vous avez été assigné au projet '{$project['theme']}'",
                'teacher',
                $projectId
            );
        }
        
        return $success;
    }

    public function getPendingProjects() {
        return $this->projectModel->getPendingProjects();
    }

    public function getProjectsByStatus($status) {
        $validStatuses = ['en cours', 'assigné', 'completé'];
        if (!in_array($status, $validStatuses)) {
            throw new InvalidArgumentException("Statut de projet invalide");
        }
        
        return $this->projectModel->getByStatus($status);
    }

    /**
     * Gestion des utilisateurs
     */
    public function deactivateStudent($studentId) {
        // Exemple : marquer comme inactif
        $stmt = $this->pdo->prepare("UPDATE students SET active = 0 WHERE id = ?");
        return $stmt->execute([$studentId]);
    }

    /**
     * Statistiques et rapports
     */
    public function getDashboardStats() {
        return [
            'students' => $this->countStudentsByRole('student'),
            'teachers' => $this->countStudentsByRole('teacher'),
            'projects_pending' => $this->projectModel->countByStatus('en cours'),
            'projects_assigned' => $this->projectModel->countByStatus('assigné'),
            'notifications_unread' => $this->notificationModel->getUnreadCount(null, 'admin')
        ];
    }

    private function countStudentsByRole($role) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM students WHERE role = ?");
        $stmt->execute([$role]);
        return $stmt->fetchColumn();
    }

    /**
     * Gestion des notifications
     */
    public function markNotificationAsRead($notificationId) {
        return $this->notificationModel->markAsRead($notificationId);
    }

    public function getAdminNotifications($unreadOnly = false) {
        return $this->notificationModel->getForStudent($_SESSION['admin_id'], 'admin', $unreadOnly);
    }
}
