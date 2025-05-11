
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="views/css/dashboard.css">
    <style>
        .dashboard {
            display: flex;
            gap: 2rem;
            padding: 2rem;
        }
        .notifications, .affectation {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1rem;
            background-color: #f9f9f9;
        }
        .notifications h2, .affectation h2 {
            margin-bottom: 1rem;
        }
        .notif-item {
            padding: 0.5rem;
            border-bottom: 1px solid #ccc;
        }
        .btn-affectation {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.5rem 1rem;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn-affectation:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="dashboard">
    <!-- Partie notifications -->
    <div class="notifications">
        <h2>Notifications</h2>
        <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $notif): ?>
                <div class="notif-item">
                    <p><strong>Étudiant ID:</strong> <?= htmlspecialchars($notif['student_id']) ?></p>
                    <p><?= htmlspecialchars($notif['message']) ?></p>
                    <small>Créée le : <?= htmlspecialchars($notif['created_at']) ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucune notification trouvée.</p>
        <?php endif; ?>
    </div>

    <!-- Partie affectation -->
    <div class="affectation">
        <h2>Affectations</h2>
        <p>Gérez les projets à affecter aux enseignants.</p>
        <a class="btn-affectation" href="index.php?action=affectation">Aller à la page d'affectation</a>
    </div>
</div>

</body>
</html>
