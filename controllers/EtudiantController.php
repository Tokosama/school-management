<?php
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Teacher.php';

require_once __DIR__ . '/../models/Project.php';
require_once __DIR__ . '/../models/Notification.php';


class EtudiantController
{
    private $studentModel;
    private $projectModel;
    private $notificationModel;
    private $teacherModel;


    public function __construct()
    {
        $this->studentModel = new Student();
        $this->teacherModel = new Teacher();

        $this->projectModel = new Project();
        $this->notificationModel = new Notification();
    }

   

    public function showSubmissionForm()
    {

        
        if (!isset($_SESSION['student_id']) ) {
            header('Location: /auth/login');
            exit;
        }

        $existingProjects = $this->projectModel->getByStudent($_SESSION['student_id']);
        if (!empty($existingProjects)) {
            $_SESSION['error'] = 'Vous avez déjà soumis un projet.';
            // header('Location: /etudiant/dashboard');
            // exit;
        }

        require_once __DIR__ . '/../views/student/soumission.php';
    }
private function handleFileUpload($file)
{
    $uploadDir = __DIR__ . '/../uploads/';
    
    // Créer le dossier s’il n’existe pas
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filename = uniqid() . '_' . basename($file['name']);
    $filePath = $uploadDir . $filename;

    return move_uploaded_file($file['tmp_name'], $filePath) ? $filename : false;
}

    public function submitCahier()
{


    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['student_id']) ) {
        header('Location: /auth/login');
        exit;
    }

    // Récupérer l'étudiant via son ID
    $studentModel = new Student(); // Assure-toi que Student.php est bien inclus
    $student = $studentModel->findById($_SESSION['student_id']);
    if (!$student) {
        $_SESSION['error'] = "Étudiant introuvable.";
        header('Location: /etudiant/submit');
        exit;
    }

    $data = [
        'student_name' => $_SESSION['username'], 
        'binome_name' => trim($_POST['binome_name'] ?? ''),
        'theme' => trim($_POST['theme'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'domains' => trim($_POST['domains'] ?? ''),
        'file' => $_FILES['file_path'] ?? null
    ];

    if (empty($data['binome_name']) || empty($data['theme']) || empty($data['domains']) || !$data['file']) {
        $_SESSION['error'] = 'Tous les champs obligatoires doivent être remplis.';
        header('Location: /etudiant/submit');
        exit;
    }

    $filePath = $this->handleFileUpload($data['file']);
    if (!$filePath) {
        $_SESSION['error'] = 'Erreur lors du téléchargement du fichier.';
        header('Location: /etudiant/submit');
        exit;
    }
    
   


    try {
        $this->createProject($_SESSION['student_id'], $data, $filePath);
        $_SESSION['success'] = 'Projet soumis avec succès.';
        header('Location: index.php?action=dashboard-etudiant');
        
    } catch (Exception $e) {
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $_SESSION['error'] = 'Erreur : ' . $e->getMessage();
  
    }
 
}
   private function createProject($studentId, $data, $filename)
{
    return $this->projectModel->create(
        $studentId,               // student_id
        $data['student_name'],      // teacher_id
        $data['binome_name'],    // student_name (réutilise l'information déjà présente dans la session)
        $data['theme'],     // binome_name
        $data['description'],           // theme
        $data['domains'],     // description
        $filename                 // file_path
    );
}


public function dashboard()
{
    //$this->checkStudentAuth();
    $studentId = $_SESSION['student_id'];
    $studentModel = new Student(); // Assure-toi que Student.php est bien inclus

    $submittedProject = $studentModel->getProjectByStudentId($studentId);
 if ($submittedProject && $submittedProject['status'] == 'assigned') {
        $teacher = $this->teacherModel->getById($submittedProject['teacher_id']);
    }
    // Passe les données à la vue
    require_once __DIR__ . '/../views/student/studentDashboard.php';
}
    private function validateProjectData($data)
    {
        $errors = [];
        if (empty($data['theme'])) $errors[] = 'Theme requis';
        if (empty($data['binome_name'])) $errors[] = 'Nom du binôme requis';
        if (empty($data['domains'])) $errors[] = 'Domaines requis';
        if (!$data['file'] || $data['file']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Fichier invalide';
        }
        return $errors;
    }




    public function relancer()
{
    if (!isset($_SESSION['student_id'])) {
        header('Location: /auth/login');
        exit;
    }

    $studentId = $_SESSION['student_id'];

    // Récupérer le projet en attente
    $project = $this->projectModel->getPendingProject($studentId);

    if (!$project) {
        $_SESSION['error'] = 'Aucun projet en attente';
        header('Location: /etudiant/dashboard');
        exit;
    }

    // Appel à la méthode de notification avec l'ID du projet
    $this->sendReminderNotification($studentId, $project['id']);

    $_SESSION['success'] = 'Relance envoyée';
    header('Location: /index.php?action=dashboard-etudiant');
}


    private function hasPendingProject($studentId)
    {
        $projects = $this->projectModel->getByStudent($studentId);
        foreach ($projects as $project) {
            if ($project['status'] === 'pending') return true;
        }
        return false;
    }

    private function sendReminderNotification($studentId,$projectId)
    {
        $this->notificationModel->create(
            $studentId,
            $projectId,
            "Relance pur l'affectation au cahier de charge"
        );
    }
}