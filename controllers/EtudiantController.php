<?php

class EtudiantController
{
    public function dashboard()
    {
        $etudiantModel = new Etudiant();
        $etudiant = $etudiantModel->findById($_SESSION['etudiant_id']);

        $cahierModel = new CahierCharge();
        $cahier = $cahierModel->findByEtudiantId($etudiant['id']);

        $affectationModel = new Affectation();
        $affectation = $affectationModel->findByEtudiantId($etudiant['id']);

        require_once '../app/Views/etudiant/dashboard.php';
    }

    public function showSubmissionForm()
    {
        require_once '../app/Views/etudiant/submit_cahier.php';
    }

    public function submitCahier()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $theme = $_POST['theme'] ?? '';
            $binome = $_POST['binome'] ?? '';
            $file = $_FILES['cahier'] ?? null;

            if (empty($theme) || empty($binome) || !$file) {
                $_SESSION['error'] = 'Veuillez remplir tous les champs et sélectionner un fichier.';
                header('Location: /etudiant/submit');
                exit;
            }

            // Enregistrer le fichier
            $uploadDir = '../uploads/';
            $filename = uniqid() . '_' . basename($file['name']);
            $filePath = $uploadDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                $cahierModel = new CahierCharge();
                $cahierModel->create($_SESSION['etudiant_id'], $theme, $binome, $filename);

                $_SESSION['success'] = 'Cahier des charges soumis avec succès.';
                header('Location: /etudiant/dashboard');
                exit;
            } else {
                $_SESSION['error'] = 'Erreur lors du téléchargement du fichier.';
                header('Location: /etudiant/submit');
                exit;
            }
        }
    }

    public function relancer()
    {
        $etudiantId = $_SESSION['etudiant_id'];

        $notificationModel = new Notification();
        $notificationModel->create($etudiantId, 'Relance pour affectation');

        $_SESSION['success'] = 'Relance envoyée à l\'administrateur.';
        header('Location: /etudiant/dashboard');
        exit;
    }
}
