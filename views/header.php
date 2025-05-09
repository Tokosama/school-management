<?php
$current_action = $_GET['action'] ?? 'login';
?>

<header>
    <nav>
        <ul>
            <?php if (!isset($_SESSION['user_id']) && $current_action !== 'login' && $current_action !== 'signup'): ?>
                <!-- Si l'utilisateur n'est pas connecté et n'est pas sur la page connexion ou inscription, afficher les liens -->
                <li><a href="index.php?action=login" class="<?php echo ($current_action == 'login') ? 'active' : ''; ?>">Connexion</a></li>
                <li><a href="index.php?action=signup" class="<?php echo ($current_action == 'signup') ? 'active' : ''; ?>">Inscription</a></li>
            <?php elseif (isset($_SESSION['user_id'])): ?>
                <!-- Si l'utilisateur est connecté, afficher Dashboard et déconnexion -->
                <li><a href="index.php?action=dashboard" class="<?php echo ($current_action == 'dashboard') ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="index.php?action=logout" class="<?php echo ($current_action == 'logout') ? 'active' : ''; ?>">Se déconnecter</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
