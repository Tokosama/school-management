<?php
session_start();
include 'header.php'; // Inclure le header commun

if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

// Traitement du formulaire de soumission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre_projet = $_POST['titre-projet'];
    $nom_binome = $_POST['nom-binome'];
    $description_projet = $_POST['description-projet'];
    $fichier_cahier = $_FILES['fichier-cahier'];

    // Logique pour enregistrer ou traiter la soumission (par exemple, enregistrer dans la base de données)
    // Vérifier si le fichier est valide
    if ($fichier_cahier['size'] <= 5242880) { // 5 Mo max
        // Enregistrement du fichier (par exemple, dans un répertoire spécifique)
        move_uploaded_file($fichier_cahier['tmp_name'], 'uploads/' . $fichier_cahier['name']);
        echo "<p>Cahier des charges soumis avec succès pour le projet : $titre_projet</p>";
    } else {
        echo "<p>Erreur : le fichier dépasse la taille maximale de 5 Mo.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soumission du Cahier des Charges</title>
    <link rel="stylesheet" href="views/css/soumission.css"> <!-- Ajouter le lien vers le fichier CSS -->
</head>
<body>

<section id="soumission-cahier-charges" class="form-container">
    <h2 class="form-title">Soumettre un Cahier des Charges</h2>
    <form id="form-cahier-charges" class="space-y-4" enctype="multipart/form-data" method="POST" action="index.php?action=soumettre-projet">
    <div class="form-group">
            <label for="theme" class="form-label">Thème du projet <span class="text-red-500">*</span></label>
            <input type="text" id="theme" name="theme" class="form-input" placeholder="Titre du thème" required>
        </div>

        <div class="form-group">
            <label for="binome_name" class="form-label">Nom du binôme <span class="text-red-500">*</span></label>
            <input type="text" id="binome_name" name="binome_name" class="form-input" placeholder="Nom du binôme" required>
        </div>

    

        <div class="form-group">
            <label for="description" class="form-label">Description du projet</label>
            <textarea id="description" name="description" class="form-textarea" placeholder="Description du projet (facultatif)"></textarea>
        </div>

        <div class="form-group">
            <label for="domains" class="form-label">Domaine <span class="text-red-500">*</span></label>
            <select id="domains" name="domains" class="form-input" required>
                <option value="">-- Sélectionnez un domaine --</option>
                <option value="AL">AL</option>
                <option value="SRC">SRC</option>
                <option value="SI">SI</option>
                <option value="AL/SI">AL/SI</option>
                <option value="AL/SRC">AL/SRC</option>
                <option value="SI/SRC">SI/SRC</option>
                <option value="AL/SI/SRC">AL/SI/SRC</option>
            </select>
        </div>

        <div class="form-group">
            <label for="file_path" class="form-label">Fichier du cahier des charges <span class="text-red-500">*</span></label>
            <input type="file" id="file_path" name="file_path" class="form-input" accept=".pdf,.doc,.docx" required>
            <p class="text-muted mt-1">Formats acceptés : .pdf, .doc, .docx (Max 5Mo)</p>
        </div>

        <div class="form-group">
            <button type="submit" class="form-button form-button-primary">
                Soumettre le cahier des charges
            </button>
            <button type="reset" class="form-button form-button-secondary">
                Effacer
            </button>
        </div>
    </form>

    <div id="message-cahier" class="mt-4"></div>
</section>

</body>
</html>
