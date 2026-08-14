<?php
// admin/dashboard.php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/GameFunctions.php';


$gameFunctions = new GameFunctions($db);
$message = '';

// Gestion des niveaux
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_level'])) {
        if ($gameFunctions->addLevel($_POST['name'], (int)$_POST['required_points'], $_POST['description'])) {
            $message = '<div class="alert success">Niveau ajouté avec succès!</div>';
        } else {
            $message = '<div class="alert error">Erreur lors de l\'ajout du niveau</div>';
        }
    } elseif (isset($_POST['update_user'])) {
        $stmt = $db->prepare("UPDATE utilisateurs SET role = ? WHERE id = ?");
        if ($stmt->execute([$_POST['role'], $_POST['user_id']])) {
            $message = '<div class="alert success">Utilisateur mis à jour!</div>';
        }
    }
}

// Gestion suppression niveau
if (isset($_GET['delete_level'])) {
    if ($gameFunctions->deleteLevel((int)$_GET['delete_level'])) {
        $message = '<div class="alert success">Niveau supprimé!</div>';
    }
}

// Données pour le dashboard
$users = $db->query("SELECT id, username, role, created_at FROM utilisateurs ORDER BY created_at DESC")->fetchAll();
$levels = $gameFunctions->getAllLevels();
$stats = $gameFunctions->getGlobalStats();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1>Tableau de Bord Admin</h1>
            <nav>
                <a href="../index.php">Accueil</a>
                <a href="../profile.php">Profil</a>
                <a href="../logout.php">Déconnexion</a>
            </nav>
        </header>

        <main>
            <?= $message ?>

            <section class="stats-section">
                <h2>Statistiques Globales</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Utilisateurs</h3>
                        <p><?= $stats['total_users'] ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Niveau Moyen</h3>
                        <p><?= $stats['avg_level'] ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Réponses</h3>
                        <p><?= $stats['total_answers'] ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Taux de Réussite</h3>
                        <p><?= $stats['success_rate'] ?>%</p>
                    </div>
                </div>
            </section>

            <section class="users-section">
                <h2>Gestion des Utilisateurs</h2>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Rôle</th>
                            <th>Inscription</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>s
                            <td>
                                <form method="POST" class="role-form">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <select name="role" onchange="this.form.submit()">
                                        <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <input type="hidden" name="update_user" value="1">
                                </form>
                            </td>
                            <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <a href="user_details.php?id=<?= $user['id'] ?>" class="btn">Détails</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <section class="levels-section">
                <h2>Gestion des Niveaux</h2>
                
                <form method="POST" class="level-form">
                    <h3>Ajouter un Niveau</h3>
                    <div class="form-group">
                        <label>Nom du Niveau</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Points Requis</label>
                        <input type="number" name="required_points" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description"></textarea>
                    </div>
                    <button type="submit" name="add_level">Ajouter</button>
                </form>

                <div class="levels-list">
                    <?php foreach ($levels as $level): ?>
                    <div class="level-card">
                        <h3><?= htmlspecialchars($level['name']) ?></h3>
                        <p><strong>Points:</strong> <?= $level['required_points'] ?></p>
                        <?php if (!empty($level['description'])): ?>
                            <p><?= htmlspecialchars($level['description']) ?></p>
                        <?php endif; ?>
                        <div class="level-actions">
                            <a href="?delete_level=<?= $level['id'] ?>" 
                               class="btn delete-btn"
                               onclick="return confirm('Supprimer ce niveau?')">Supprimer</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>

        <footer class="admin-footer">
            <p>Système d'administration - <?= date('Y') ?></p>
        </footer>
    </div>
</body>
</html>