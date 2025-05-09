<?php
require_once '../app/Models/Student.php';
require_once '../app/Models/Project.php';
require_once '../app/Models/Notification.php';

class EtudiantController
{
    private $StudentModel;
    private $projectModel;
    private $notificationModel;

    public function __construct()
    {
        $this->StudentModel = new Student();
        $this->projectModel = new Project();
        $this->notificationModel = new Notification();
    }

    public function dashboard()
    {
        // Vérification de l'authentification
        if (!isset($_SESSION['Student_id']) || $_SESSION['Student_role'] !== 'student') {
            header('Location: /auth/login');
            exit;
        }

        // Récupération des données
        $student = $this->StudentModel->getStudentById($_SESSION['Student_id']);
        $projects = $this->projectModel->getByStudent($_SESSION['Student_id']);

        require_once '../app/Views/etudiant/dashboard.php';
    }

    public function showSubmissionForm()
    {
        // Vérification de l'authentification
        if (!isset($_SESSION['Student_id']) || $_SESSION['Student_role'] !== 'student') {
            header('Location: /auth/login');
            exit;
        }

        // Vérifier si l'étudiant a déjà soumis un projet
        $existingProjects = $this->projectModel->getByStudent($_SESSION['Student_id']);
        if (!empty($existingProjects)) {
            $_SESSION['error'] = 'Vous avez déjà soumis un projet.';
            header('Location: /etudiant/dashboard');
            exit;
        }

        require_once '../app/Views/etudiant/submit_cahier.php';
    }

    public function submitCahier()
    {
        // Vérification de l'authentification et méthode POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['Student_id']) || $_SESSION['Student_role'] !== 'student') {
            header('Location: /auth/login');
            exit;
        }

        // Validation des données
        $title = trim($_POST['title'] ?? '');
        $partnerName = trim($_POST['partner_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $domains = $_POST['domains'] ?? [];
        $file = $_FILES['file'] ?? null;

        $errors = [];
        if (empty($title)) $errors[] = 'Le titre du projet est requis.';
        if (empty($partnerName)) $errors[] = 'Le nom du binôme est requis.';
        if (empty($domains)) $errors[] = 'Veuillez sélectionner au moins un domaine.';
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) $errors[] = 'Veuillez sélectionner un fichier valide.';

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $_SESSION['old'] = [
                'title' => $title,
                'partner_name' => $partnerName,
                'description' => $description,
                'domains' => $domains
            ];
            header('Location: /etudiant/submit');
            exit;
        }

        // Traitement du fichier
        $uploadDir = '../uploads/';
        $filename = uniqid() . '_' . basename($file['name']);
        $filePath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            try {
                // Création du projet
                $this->projectModel->create(
                    studentId: $_SESSION['Student_id'],
                    Nom_binome: $partnerName,
                    title: $title,
                    description: $description,
                    domains: $domains,
                    filePath: $filename
                );

                $_SESSION['success'] = 'Votre projet a été soumis avec succès.';
                header('Location: /etudiant/dashboard');
                exit;
            } catch (InvalidArgumentException $e) {
                // Suppression du fichier en cas d'erreur
                unlink($filePath);
                $_SESSION['error'] = $e->getMessage();
                header('Location: /etudiant/submit');
                exit;
            }
        } else {
            $_SESSION['error'] = 'Une erreur est survenue lors du téléchargement du fichier.';
            header('Location: /etudiant/submit');
            exit;
        }
    }

    public function relancer()
    {
        // Vérification de l'authentification
        if (!isset($_SESSION['Student_id']) || $_SESSION['Student_role'] !== 'student') {
            header('Location: /auth/login');
            exit;
        }

        // Vérifier si l'étudiant a un projet en attente
        $projects = $this->projectModel->getByStudent($_SESSION['Student_id']);
        $hasPendingProject = false;

        foreach ($projects as $project) {
            if ($project['status'] === 'pending') {
                $hasPendingProject = true;
                break;
            }
        }

        if (!$hasPendingProject) {
            $_SESSION['error'] = 'Vous n\'avez pas de projet en attente d\'affectation.';
            header('Location: /etudiant/dashboard');
            exit;
        }

        // Création de la notification
        $this->notificationModel->create(
            $_SESSION['Student_id'],
            'Relance pour affectation de projet',
            'admin'
        );

        $_SESSION['success'] = 'Votre relance a été envoyée à l\'administrateur.';
        header('Location: /etudiant/dashboard');
        exit;
    }
}