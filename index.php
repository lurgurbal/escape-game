<?php
// index.php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/logs/log_config.php';
require_once __DIR__ . '/includes/GameFunctions.php';

if (!Auth::isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Initialisation des variables de session
if (!isset($_SESSION['current_enigma_id'])) {
    $_SESSION['current_enigma_id'] = null;
    $_SESSION['attempts'] = 0;
}

$gameFunctions = new GameFunctions($db);
$currentLevel = $_SESSION['user_level'] ?? 1;
$enigma = $gameFunctions->getCurrentEnigma($currentLevel);

// Traitement de la réponse
$feedback = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'])) {
    $answer = clean_input($_POST['answer']);
    $isCorrect = $gameFunctions->checkAnswer($_SESSION['user_id'], $enigma['id'], $answer);
    
    if ($isCorrect) {
        $feedback = '<div class="alert alert-success">Bonne réponse! Vous avez gagné des points.</div>';
        // La progression est mise à jour automatiquement par checkAnswer()
    } else {
        $_SESSION['attempts']++;
        $feedback = '<div class="alert alert-danger">Réponse incorrecte. Essayez encore!</div>';
    }
    
    // Recharger une nouvelle énigme après la réponse
    $enigma = $gameFunctions->getCurrentEnigma($currentLevel);
    $_SESSION['current_enigma_id'] = $enigma['id'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escape Game Scientifique</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .game-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .enigma-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin: 20px 0;
        }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Laboratoire de Physique Quantique</h1>
            <nav>
                <ul>
                    <li><a href="game_modes.php">Modes de jeu</a></li>
                    <li><a href="profile.php">Profil</a></li>
                    <?php if (isset($_SESSION['username']) && $_SESSION['username'] === 'admin'): ?>
                        <li><a href="admin/dashboard.php">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="game-container">
            <h2>Énigme Scientifique</h2>
            
            <?= $feedback ?>
            
            <div class="enigma-card">
                <h3><?= htmlspecialchars($enigma['titre']) ?></h3>
                <div class="enigma-description">
                    <?= nl2br(htmlspecialchars($enigma['consigne'])) ?>
                </div>
                
                <?php if ($enigma['indice']): ?>
                    <div class="hint">
                        <small>Indice: <?= htmlspecialchars($enigma['indice']) ?></small>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="index.php">
                    <div class="form-group">
                        <label for="answer">Votre réponse:</label>
                        <input type="text" id="answer" name="answer" required>
                    </div>
                    <button type="submit" class="btn">Soumettre</button>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>Escape Game Scientifique - Sauver le laboratoire piraté</p>
        </div>
    </footer>
</body>
</html>