<?php
require_once '../app/Models/Student.php';
require_once '../app/Models/Project.php';
require_once '../app/Models/Notification.php';

class EtudiantController
{
    private $studentModel;
    private $projectModel;
    private $notificationModel;

    public function __construct()
    {
        $this->studentModel = new Student();
        $this->projectModel = new Project();
        $this->notificationModel = new Notification();
    }

    public function dashboard()
    {
        if (!isset($_SESSION['student_id']) || $_SESSION['student_role'] !== 'student') {
            header('Location: /auth/login');
            exit;
        }

        $student = $this->studentModel->getStudentById($_SESSION['student_id']);
        $projects = $this->projectModel->getByStudent($_SESSION['student_id']);

        require_once '../app/Views/etudiant/dashboard.php';
    }

    public function showSubmissionForm()
    {
        if (!isset($_SESSION['student_id']) || $_SESSION['student_role'] !== 'student') {
            header('Location: /auth/login');
            exit;
        }

        $existingProjects = $this->projectModel->getByStudent($_SESSION['student_id']);
        if (!empty($existingProjects)) {
            $_SESSION['error'] = 'Vous avez déjà soumis un projet.';
            header('Location: /etudiant/dashboard');
            exit;
        }

        require_once '../app/Views/etudiant/submit_cahier.php';
    }

    public function submitCahier()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['student_id']) 
        || $_SESSION['student_role'] !== 'student') {
            header('Location: /auth/login');
            exit;
        }

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'partner_name' => trim($_POST['partner_name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'domains' => $_POST['domains'] ?? [],
            'file' => $_FILES['file'] ?? null
        ];


        $filePath = $this->handleFileUpload($data['file']);
        if (!$filePath) {
            $_SESSION['error'] = 'Erreur lors du téléchargement du fichier';
            header('Location: /etudiant/submit');
            exit;
        }

        try {
            $this->createProject($_SESSION['student_id'], $data, $filePath);
            $_SESSION['success'] = 'Projet soumis avec succès';
            header('Location: /etudiant/dashboard');
        } catch (Exception $e) {
            unlink($filePath);
            $_SESSION['error'] = $e->getMessage();
            header('Location: /etudiant/submit');
        }
    }

    private function validateProjectData($data)
    {
        $errors = [];
        if (empty($data['title'])) $errors[] = 'Titre requis';
        if (empty($data['partner_name'])) $errors[] = 'Nom du binôme requis';
        if (empty($data['domains'])) $errors[] = 'Domaines requis';
        if (!$data['file'] || $data['file']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Fichier invalide';
        }
        return $errors;
    }

    private function handleFileUpload($file)
    {
        $uploadDir = '../uploads/';
        $filename = uniqid() . '_' . basename($file['name']);
        $filePath = $uploadDir . $filename;
        
        return move_uploaded_file($file['tmp_name'], $filePath) ? $filename : false;
    }

    private function createProject($studentId, $data, $filename)
    {
        return $this->projectModel->create(
            $studentId,
            $data['title'],
            $data['partner_name'],
            $data['description'],
            $data['domains'],
            $filename
        );
    }

    public function relancer()
    {
        if (!isset($_SESSION['student_id']) || $_SESSION['student_role'] !== 'student') {
            header('Location: /auth/login');
            exit;
        }

        if (!$this->hasPendingProject($_SESSION['student_id'])) {
            $_SESSION['error'] = 'Aucun projet en attente';
            header('Location: /etudiant/dashboard');
            exit;
        }

        $this->sendReminderNotification($_SESSION['student_id']);
        $_SESSION['success'] = 'Relance envoyée';
        header('Location: /etudiant/dashboard');
    }

    private function hasPendingProject($studentId)
    {
        $projects = $this->projectModel->getByStudent($studentId);
        foreach ($projects as $project) {
            if ($project['status'] === 'pending') return true;
        }
        return false;
    }

    private function sendReminderNotification($studentId)
    {
        $this->notificationModel->create(
            $studentId,
            'Relance pour affectation de projet',
            'admin'
        );
    }
}