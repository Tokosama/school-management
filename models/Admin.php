<?php
// app/Models/Admin.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Teacher.php';
require_once __DIR__ . '/Project.php';
require_once __DIR__ . '/Notification.php';

class Admin {
    private $pdo;
    private $userModel;
    private $teacherModel;
    private $projectModel;
    private $notificationModel;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->userModel = new User();
        $this->teacherModel = new Teacher();
        $this->projectModel = new Project();
        $this->notificationModel = new Notification();
    }

    /**
     * Gestion des enseignants
     */
    public function createTeacher($Nom, $prenom, $email,$role, $domains) {
        return $this->teacherModel->create($Nom, $prenom, $email,$role, $domains);
    }

    public function updateTeacher( $Nom, $prenom, $email,$role, $domains) {
        return $this->teacherModel->update( $Nom, $prenom, $email,$role, $domains);
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
                $_SESSION['user_id'],
                "Votre projet '{$project['title']}' a été affecté à {$teacher['Nom']} {$teacher['prenom']}",
                'student',
                $projectId
            );
            
            // Notifier l'enseignant
            $this->notificationModel->create(
                $_SESSION['user_id'],
                "Vous avez été assigné au projet '{$project['title']}'",
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
    public function createUser($username, $email, $password, $role, $domain = null) {
        return $this->userModel->create([
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
            'domain' => $domain,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function deactivateUser($userId) {
        // Implémentez selon votre logique métier
        // Ex: marquer l'utilisateur comme inactif plutôt que suppression
    }

    /**
     * Statistiques et rapports
     */
    public function getDashboardStats() {
        return [
            'students' => $this->countUsersByRole('student'),
            'teachers' => $this->countUsersByRole('teacher'),
            'projects_pending' => $this->projectModel->countByStatus('en cours'),
            'projects_assigned' => $this->projectModel->countByStatus('assigné'),
            'notifications_unread' => $this->notificationModel->getUnreadCount(null, 'admin')
        ];
    }

    private function countUsersByRole($role) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE role = ?");
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
        return $this->notificationModel->getForUser($_SESSION['user_id'], 'admin', $unreadOnly);
    }
}