<?php
session_start();
include 'header.php'; // Inclure le header commun

if (!isset($_SESSION['user_id'])) {
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
    <form id="form-cahier-charges" class="space-y-4" enctype="multipart/form-data" method="POST">
        <div class="form-group">
            <label for="titre-projet" class="form-label">Titre du thème du projet <span class="text-red-500">*</span></label>
            <input type="text" id="titre-projet" name="titre-projet" class="form-input" placeholder="Entrez le titre du projet" required>
        </div>
        <div class="form-group">
            <label for="nom-binome" class="form-label">Nom du binôme <span class="text-red-500">*</span></label>
            <input type="text" id="nom-binome" name="nom-binome" class="form-input" placeholder="Entrez le nom du binôme" required>
        </div>
        <div class="form-group">
            <label for="description-projet" class="form-label">Description du projet</label>
            <textarea id="description-projet" name="description-projet" class="form-textarea" placeholder="Entrez une description du projet (optionnel)"></textarea>
        </div>
        <div class="form-group">
            <label for="fichier-cahier" class="form-label">Fichier du cahier des charges <span class="text-red-500">*</span></label>
            <input type="file" id="fichier-cahier" name="fichier-cahier" class="form-input" accept=".pdf,.doc,.docx" required>
            <p class="text-muted mt-1">Formats acceptés : .pdf, .doc, .docx (Max 5Mo)</p>
            <p id="fichier-error" class="text-red-500 text-sm mt-1" style="display: none;">Veuillez sélectionner un fichier valide (max 5Mo).</p>
        </div>
        <div class="form-group">
            <button type="submit" class="form-button form-button-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="form-button-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75v-2.25m-9-5.25v-4.5m0 0l-3-3m3 3l3-3m-3 10.5v4.5m0-4.5l3 3m-3-3l-3 3" />
                </svg>
                Soumettre le cahier des charges
            </button>
            <button type="reset" class="form-button form-button-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" class="form-button-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6.75 6.75 0 100-13.5 6.75 6.75 0 000 13.5zM12 16.5c-1.654 0-3-1.346-3-3s1.346-3 3-3 3 1.346 3 3-1.346 3-3 3z" />
                </svg>
                Effacer
            </button>
        </div>
    </form>
    <div id="message-cahier" class="mt-4"></div>
</section>

</body>
</html>
