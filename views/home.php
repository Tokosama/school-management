<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Projets Étudiants</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!--Styles-->
    <style>
        .form-container {
            background-color: white;
            border-radius: 0.5rem;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .form-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #1e293b;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        .form-input, .form-textarea, .form-select {
            display: block;
            width: 100%;
            padding: 0.75rem;
            border-radius: 0.375rem;
            border: 1px solid #d1d5db;
            font-size: 1rem;
            line-height: 1.5rem;
            color: #4b5563;
            background-color: #f9fafb;
            transition: border-color 0.15s ease-in-out, shadow-sm 0.15s ease-in-out;
        }
        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        .form-textarea {
            min-height: 6rem;
            resize: vertical;
        }
        .form-select {
            appearance: none; /* Supprime la flèche par défaut du select */
            background-image: url("data:image/svg+xml,%3Csvgxmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor'%3E%3Cpath fill-rule='evenodd' d='M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 011.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z' clip-rule='evenodd' /%3E%3C/svg%3E"); /* Flèche personnalisée en SVG */
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1rem;
            padding-right: 2.5rem;
        }
        .form-select:focus {
            padding-right: 2.5rem;
        }

        .form-checkbox-group {
            margin-top: 0.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .form-checkbox-label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #4b5563;
        }
        .form-checkbox-input {
            appearance: none;
            width: 1rem;
            height: 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.25rem;
            background-color: #f9fafb;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: border-color 0.15s ease-in-out, background-color 0.15s ease-in-out;
        }
        .form-checkbox-input:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }
        .form-checkbox-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        .form-checkbox-input:checked::before {
            content: "";
            display: block;
            width: 0.6rem;
            height: 0.6rem;
            border-width: 0.15rem;
            border-style: solid;
            border-color: white;
            border-top-width: 0;
            border-left-width: 0;
            transform: rotate(45deg);
        }


        .form-button {
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.15s ease-in-out, transform 0.15s ease-in-out;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .form-button-primary {
            background-color: #3b82f6;
            color: white;
        }
        .form-button-primary:hover {
            background-color: #2563eb;
            transform: translateY(-0.0625rem);
        }
        .form-button-secondary {
            background-color: #e5e7eb;
            color: #374151;
        }
        .form-button-secondary:hover {
            background-color: #d1d5db;
            transform: translateY(-0.0625rem);
        }
        .form-button-icon {
            width: 1.25rem;
            height: 1.25rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
        }
        .alert-success {
            background-color: #f0fdf4;
            color: #15803d;
            border-color: #dcfce7;
        }
        .alert-error {
            background-color: #fee2e2;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .text-muted {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .space-y-4 > * + * {
            margin-top: 1rem;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-semibold text-gray-800 mb-6 text-center">Gestion des Projets Étudiants</h1>

        <section id="enregistrement-enseignants" class="form-container">
            <h2 class="form-title">Enregistrer un Enseignant</h2>
            <form id="form-enseignant" class="space-y-4">
                <div class="form-group">
                    <label for="nom-enseignant" class="form-label">Nom de l'enseignant <span class="text-red-500">*</span></label>
                    <input type="text" id="nom-enseignant" name="nom-enseignant" class="form-input" placeholder="Entrez le nom de l'enseignant" required>
                </div>
                <div class="form-group">
                    <label for="domaines-expertise" class="form-label">Domaine(s) d'expertise <span class="text-red-500">*</span></label>
                    <div id="domaines-expertise" class="form-checkbox-group">
                        <label class="form-checkbox-label">
                            <input type="checkbox" name="domaines[]" value="AL" class="form-checkbox-input">
                            AL (Architecture Logicielle)
                        </label>
                        <label class="form-checkbox-label">
                            <input type="checkbox" name="domaines[]" value="SRC" class="form-checkbox-input">
                            SRC (Systèmes et Réseaux de Communication)
                        </label>
                        <label class="form-checkbox-label">
                            <input type="checkbox" name="domaines[]" value="SI" class="form-checkbox-input">
                            SI (Systèmes d'Information)
                        </label>
                    </div>
                    <p id="domaines-error" class="text-red-500 text-sm mt-1" style="display: none;">Veuillez sélectionner au moins un domaine d'expertise.</p>
                </div>
                <div class="form-group">
                    <button type="submit" class="form-button form-button-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="form-button-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6m12-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Enregistrer l'enseignant
                    </button>
                    <button type="reset" class="form-button form-button-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="form-button-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6.75 6.75 0 100-13.5 6.75 6.75 0 000 13.5zM12 16.5c-1.654 0-3-1.346-3-3s1.346-3 3-3 3 1.346 3 3-1.346 3-3 3z" />
                        </svg>
                        Effacer
                    </button>
                </div>
            </form>
            <div id="message-enseignant" class="mt-4"></div>
        </section>

        <section id="soumission-cahier-charges" class="form-container">
            <h2 class="form-title">Soumettre un Cahier des Charges</h2>
            <form id="form-cahier-charges" class="space-y-4" enctype="multipart/form-data">
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

        <section id="consultation-affectation" class="form-container">
            <h2 class="form-title">Votre Affectation</h2>
            <div id="info-affectation" class="bg-gray-50 border border-gray-200 rounded-md p-4 mb-4">
                <p><strong>Étudiant :</strong> <span id="nom-etudiant">Non disponible</span></p>
                <p><strong>Enseignant affecté :</strong> <span id="nom-enseignant-affecte">En attente d'affectation</span></p>
            </div>
            <button id="bouton-relance" class="form-button form-button-primary" style="display: none;">
                <svg xmlns="http://www.w3.org/2000/svg" class="form-button-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H10.5A3 3 0 007.5 9v6a3 3 0 003 3h3a3 3 0 003-3V9a3 3 0 00-3-3zM15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Relancer l'affectation
            </button>
            <div id="message-relance" class="mt-4"></div>
        </section>

        <section id="liste-etudiants-attente" class="form-container">
            <h2 class="form-title">Étudiants en Attente d'Affectation</h2>
            <div id="liste-etudiants" class="space-y-4">
                <p class="text-muted">Aucun étudiant en attente d'affectation pour le moment.</p>
            </div>
        </section>
    </div>

    <script>
        //Enrregistrement de l'enseignant
    </script>
</body>
</html>
