<?php
// profile.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/GameFunctions.php';
require_once __DIR__ . '/logs/log_config.php';

if (!Auth::isLoggedIn()) {
    header('Location: login.php');
    exit();
}

if (!isset($_SESSION['user_id'], $_SESSION['username'])) {
    die("Erreur : Données de session incomplètes");
}

$gameFunctions = new GameFunctions($db);
$errorMessage = '';

try {
    $userProgress = $gameFunctions->getUserProgress($_SESSION['user_id']);
    $currentLevel = $gameFunctions->getCurrentLevel($_SESSION['user_id']);
    $history = $gameFunctions->getAnswerHistory($_SESSION['user_id']);
} catch (Exception $e) {
    $errorMessage = "Erreur de chargement des données";
    error_log("Profile error: " . $e->getMessage());
}

$feedbackMessages = [
    'level_up' => isset($_GET['level_up']) ? 'Félicitations! Vous avez atteint un nouveau niveau!' : '',
    'badge_earned' => isset($_GET['badge_earned']) ? 'Nouveau badge débloqué!' : ''
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .progress-section {
            margin-bottom: 30px;
        }
        .current-level {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .progress-bar {
            height: 20px;
            background: #e9ecef;
            border-radius: 10px;
            margin: 10px 0;
            overflow: hidden;
        }
        .progress {
            height: 100%;
            background: #28a745;
            transition: width 0.3s ease;
        }
        .levels-progress {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }
        .level-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .level-card.achieved {
            border-left: 4px solid #28a745;
        }
        .badge {
            display: inline-block;
            background: #e9ffe9;
            color: #28a745;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8em;
            margin-top: 10px;
        }
        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .history-table th, .history-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .history-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .history-table tr:hover {
            background: #f8f9fa;
        }
        .feedback {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .feedback.success {
            background: #d4edda;
            color: #155724;
        }
        .feedback.info {
            background: #d1ecf1;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Laboratoire de Physique Quantique</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="profile.php">Profil</a></li>
                    <?php if ($_SESSION['username'] === 'admin'): ?>
                        <li><a href="admin/dashboard.php">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="profile-container">
            <h2>Profil de <?= htmlspecialchars($_SESSION['username']) ?></h2>
            
            <?php if ($errorMessage): ?>
                <div class="error-message"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>
            
            <div class="progress-section">
                <div class="current-level">
                    <h3>Niveau actuel: <?= htmlspecialchars($currentLevel['name']) ?></h3>
                    <div class="progress-bar">
                        <div class="progress" style="width: <?= min(100, ($currentLevel['progress'] / $currentLevel['required_points']) * 100) ?>%"></div>
                    </div>
                    <p><?= $currentLevel['progress'] ?> / <?= $currentLevel['required_points'] ?> points</p>
                </div>
                
                <div class="levels-progress">
                    <?php foreach ($userProgress as $level): ?>
                        <div class="level-card <?= $level['achieved'] ? 'achieved' : '' ?>">
                            <h4><?= htmlspecialchars($level['name']) ?></h4>
                            <p>Points requis: <?= $level['required_points'] ?></p>
                            <?php if ($level['achieved'] && isset($level['achieved_at']) && $level['achieved_at']): ?>
                            <div class="badge">🎉 Validé le <?= date('d/m/Y', strtotime($level['achieved_at'])) ?></div>
                        <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <h3>Historique des réponses</h3>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Énigme</th>
                        <th>Réponse donnée</th>
                        <th>Résultat</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $entry): ?>
                        <tr>
                            <td><?= htmlspecialchars($entry['titre']) ?></td>
                            <td><?= htmlspecialchars($entry['reponse_donnee']) ?></td>
                            <td><?= $entry['resultat'] ? '✅ Correct' : '❌ Incorrect' ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($entry['date'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($history)): ?>
                        <tr>
                            <td colspan="4">Aucune réponse enregistrée</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>
    <main class="container">
    <?= $feedback ?>
    <div class="feedback-section">
    <?php foreach($feedbackMessages as $message): ?>
        <?php if(!empty($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
    <?php endforeach; ?>
    </div>
</main>
    <footer>
        <div class="container">
            <p>Escape Game Scientifique - Sauver le laboratoire piraté</p>
        </div>>
</body>
</html>