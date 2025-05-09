<?php
// test_Student.php

require_once __DIR__ . '/models/Student.php';
require_once __DIR__ . '/models/Project.php';
require_once __DIR__ . '/models/Notification.php';
require_once __DIR__ . '/models/Teacher.php';

// Initialisation des modèles
$StudentModel = new Student();
$projectModel = new Project();
$notificationModel = new Notification();
$teacherModel = new Teacher();

// Enregistrement d'un enseignant
echo "Enregistrement d'un enseignant...\n";
if ($StudentModel->register('enseignant1', 'motdepasse123', 'teacher', 'AL,SI')) {
    echo "✅ Enseignant enregistré avec succès.\n";
} else {
    echo "❌ Échec de l'enregistrement de l'enseignant.\n";
}

// Enregistrement d'un étudiant
echo "Enregistrement d'un étudiant...\n";
if ($StudentModel->register('etudiant1', 'motdepasse123', 'student')) {
    echo "✅ Étudiant enregistré avec succès.\n";
} else {
    echo "❌ Échec de l'enregistrement de l'étudiant.\n";
}

// Connexion de l'étudiant
echo "Connexion de l'étudiant...\n";
$student = $StudentModel->login('etudiant1', 'motdepasse123');
if ($student) {
    echo "✅ Connexion réussie. ID de l'étudiant : " . $student['id'] . "\n";

    // Soumission d'un projet
    echo "Soumission d'un projet...\n";
    if ($projectModel->submit($student['id'], 'Binome Nom', 'Thème du projet', '/chemin/vers/fichier.pdf')) {
        echo "✅ Projet soumis avec succès.\n";
    } else {
        echo "❌ Échec de la soumission du projet.\n";
    }

    // Envoi d'une relance
    echo "Envoi d'une relance...\n";
    if ($notificationModel->send($student['id'], 'Veuillez affecter un enseignant à mon projet.')) {
        echo "✅ Relance envoyée avec succès.\n";
    } else {
        echo "❌ Échec de l'envoi de la relance.\n";
    }

} else {
    echo "❌ Échec de la connexion de l'étudiant.\n";
}

// Récupération des projets en attente
echo "Récupération des projets en attente...\n";
$pendingProjects = $projectModel->getPending();
if (!empty($pendingProjects)) {
    foreach ($pendingProjects as $project) {
        echo "📝 Projet ID : " . $project['id'] . ", Thème : " . $project['theme'] . "\n";
    }
} else {
    echo "Aucun projet en attente.\n";
}

// Récupération des notifications
echo "Récupération des notifications...\n";
$notifications = $notificationModel->getAll();
if (!empty($notifications)) {
    foreach ($notifications as $notification) {
        echo "🔔 Notification ID : " . $notification['id'] . ", Message : " . $notification['message'] . "\n";
    }
} else {
    echo "Aucune notification.\n";
}

// Récupération des enseignants
echo "Récupération de la liste des enseignants...\n";
$teachers = $teacherModel->getAll();
if (!empty($teachers)) {
    foreach ($teachers as $teacher) {
        echo "👨‍🏫 Enseignant : " . $teacher['Studentname'] . " | Domaine : " . $teacher['domain'] . "\n";
    }
} else {
    echo "Aucun enseignant trouvé.\n";
}
?>
