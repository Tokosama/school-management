<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Enseignants</title>
    <link rel="stylesheet" href="views/css/liste_enseignants.css">
</head>
<body>
    <div class="container">
        <h1>Liste des Enseignants</h1>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom Complet</th>
                    <th>Filière</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enseignants as $enseignant): ?>
                    <tr>
                        <td><?= htmlspecialchars($enseignant['id']) ?></td>
                        <td><?= htmlspecialchars($enseignant['username']) ?></td>
                        <td><?= htmlspecialchars($enseignant['domains']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="/admin">← Retour au tableau de bord</a>
    </div>
</body>
</html>
