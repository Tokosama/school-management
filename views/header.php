<?php
session_start();
?>
<link rel="stylesheet" href="views/css/header.css">

<header class="navbar">
    <nav class="navbar-nav">
        <ul class="navbar-list">
            <?php if (isset($_SESSION['student_id'])): ?>
                <!-- Navigation pour étudiant -->
                <li class="navbar-item"><a class="navbar-link" href="index.php?action=dashboard-etudiant">Dashboard</a></li>
                <li class="navbar-item"><a class="navbar-link" href="index.php?action=soumission">Soumission</a></li>
                <li class="navbar-item"><a class="navbar-link" href="index.php?action=logout">Se déconnecter</a></li>

            <?php elseif (isset($_SESSION['admin_id'])): ?>
                <!-- Navigation pour admin -->
                <li class="navbar-item"><a class="navbar-link" href="index.php?action=dashboard-admin">Dashboard</a></li>
                <li class="navbar-item"><a class="navbar-link" href="index.php?action=ajouterEnseignant">Créer Enseignant</a></li>
                <li class="navbar-item"><a class="navbar-link" href="index.php?action=listerEnseignant">Liste des Enseignants</a></li>

                <li class="navbar-item"><a class="navbar-link" href="index.php?action=affectation">Affectation</a></li>
                <li class="navbar-item"><a class="navbar-link" href="index.php?action=logout">Se déconnecter</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
