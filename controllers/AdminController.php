<?php

class AdminController
{
    public function dashboard()
    {
        $cahierModel = new CahierCharge();
        $etudiantsEnAttente = $cahierModel->getEtudiantsSansAffectation();

        $enseignantModel = new Enseignant();
        $enseignants = $enseignantModel->getAll();

        require_once '../app/Views/admin/dashboard.php';
    }

    public function affecter()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $etudiantId = $_POST['etudiant_id'] ?? null;
            $enseignantId = $_POST['enseignant_id'] ?? null;

            if (!$etudiantId || !$enseignantId) {
                $_SESSION['error'] = 'Veuillez sélectionner un étudiant et un enseignant.';
                header('Location: /admin/dashboard');
                exit;
            }

            $affectationModel = new Affectation();
            $affectationModel->create($etudiantId, $enseignantId);

            $_SESSION['success'] = 'Affectation réalisée avec succès.';
            header('Location: /admin/dashboard');
            exit;
        }
    }

    public function notifications()
    {
        $notificationModel = new Notification();
        $notifications = $notificationModel->getAll();

        require_once '../app/Views/admin/notifications.php';
    }
}
