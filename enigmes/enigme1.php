<?php
// enigmes/enigme1.php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/GameFunctions.php';

// 2. Vérification de la connexion PDO
if (!isset($pdo) || !($pdo instanceof PDO)) {
    die("Erreur: Connexion à la base de données non initialisée");
}

// 3. Vérification de l'authentification
if (!Auth::isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// 4. Niveau actuel et vérification
$currentLevel = 1; // À modifier pour chaque fichier (1-5)

if ($_SESSION['level'] != $currentLevel) {
    header('Location: ../index.php');
    exit();
}

// 5. Initialisation des fonctions du jeu
if (!isset($gameFunctions)) {
    $gameFunctions = new GameFunctions($pdo);
}

// 6. Récupération de l'énigme actuelle
try {
    $enigma = $gameFunctions->getCurrentEnigma($currentLevel);
    if (!$enigma) {
        throw new Exception("Énigme non trouvée pour le niveau $currentLevel");
    }
} catch (Exception $e) {
    die("Erreur lors du chargement de l'énigme: " . $e->getMessage());
}

// 7. Traitement de la réponse
$feedback = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'])) {
    $answer = clean_input($_POST['answer']);
    
    try {
        $correct = $gameFunctions->checkAnswer($_SESSION['user_id'], $currentLevel, $answer);
        
        if ($correct) {
            // Mise à jour du niveau si ce n'est pas le dernier
            if ($currentLevel < MAX_LEVEL) {
                $stmt = $pdo->prepare("UPDATE users SET level = level + 1 WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $_SESSION['level']++;
            }
            
            header('Location: ../index.php' . ($currentLevel === MAX_LEVEL ? '?completed=1' : ''));
            exit();
        } else {
            $feedback = '<div class="alert alert-danger">Réponse incorrecte. Essayez encore!</div>';
        }
    } catch (Exception $e) {
        $feedback = '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

// 8. Gestion des modes de jeu
$mode = $_GET['mode'] ?? 'classic';
$validModes = ['classic', 'timer', 'expert'];
if (!in_array($mode, $validModes)) {
    $mode = 'classic';
}

// 9. Initialisation de la session de jeu
if (!isset($_SESSION['game_session'])) {
    $_SESSION['game_session'] = [
        'mode' => $mode,
        'attempts' => 0,
        'start_time' => time(),
        'current_enigma' => $currentLevel
    ];
    
    if ($mode === 'timer') {
        $_SESSION['game_session']['end_time'] = time() + 1800; // 30 minutes
    }
}

// 10. Récupération des énigmes selon le mode
try {
    switch ($_SESSION['game_session']['mode']) {
        case 'timer':
            $order = "WHERE niveau IN (1, 2, 3, 4, 5) ORDER BY RAND() LIMIT 5";
            break;
        case 'expert':
            $order = "WHERE niveau IN (3, 4, 5) ORDER BY RAND()";
            break;
        case 'classic':
        default:
            $order = "WHERE niveau IN (1, 2, 3, 4, 5) ORDER BY niveau ASC";
    }

    $stmt = $pdo->prepare("SELECT * FROM enigmes $order");
    $stmt->execute();
    $enigmes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($enigmes)) {
        throw new Exception("Aucune énigme trouvée dans la base de données");
    }
} catch (PDOException $e) {
    die("Erreur de base de données: " . htmlspecialchars($e->getMessage()));
}

// 11. Calcul de la progression
$progressPercentage = ($currentLevel / MAX_LEVEL) * 100;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Énigme <?= htmlspecialchars($currentLevel) ?> - <?= htmlspecialchars($enigma['title'] ?? 'Enigma') ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .timer {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }
        .time-warning {
            color: #dc3545;
            font-weight: bold;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        .progress-container {
            width: 100%;
            background-color: #e0e0e0;
            border-radius: 5px;
            margin: 20px 0;
        }
        .progress-bar {
            height: 20px;
            border-radius: 5px;
            background-color: #4CAF50;
            text-align: center;
            line-height: 20px;
            color: white;
        }
        .enigma-card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            background-color: #5cb85c;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Laboratoire de Physique Quantique</h1>
            <nav>
                <ul>
                    <li><a href="../index.php">Accueil</a></li>
                    <li><a href="../profile.php">Profil</a></li>
                    <li><a href="../logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="game-container">
            <h2>Niveau <?= htmlspecialchars($currentLevel) ?>: <?= htmlspecialchars($enigma['category'] ?? 'Général') ?> - <?= htmlspecialchars($enigma['title'] ?? 'Enigma') ?></h2>
            <div class="progress-container">
                <div class="progress-bar" style="width: <?= $progressPercentage ?>%"><?= round($progressPercentage) ?>%</div>
            </div>
            
            <?php if (isset($_SESSION['game_session']['start_time'])): ?>
                <div class="timer">
                    <span>Temps écoulé: <span id="timeElapsed">00:00:00</span></span>
                    <?php if ($_SESSION['game_session']['mode'] === 'timer'): ?>
                        <span> / Temps restant: <span id="timeRemaining">30:00</span></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?= $feedback ?>
            
            <div class="enigma-card">
                <div class="enigma-description">
                    <p><?= nl2br(htmlspecialchars($enigma['description'] ?? '')) ?></p>
                    <?php if (!empty($enigma['hint'])): ?>
                        <p><strong>Indice:</strong> <?= htmlspecialchars($enigma['hint']) ?></p>
                    <?php endif; ?>
                    <p><strong>Consigne:</strong> <?= nl2br(htmlspecialchars($enigma['consigne'] ?? '')) ?></p>
                </div>
                
                <form method="POST" action="enigme<?= $currentLevel ?>.php?mode=<?= htmlspecialchars($mode) ?>">
                    <div class="form-group">
                        <label for="answer">Votre réponse:</label>
                        <input type="text" id="answer" name="answer" required class="form-control">
                    </div>
                    <button type="submit" class="btn">Soumettre</button>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>Escape Game Scientifique - Sauver le laboratoire piraté &copy; <?= date('Y') ?></p>
        </div>
    </footer>

    <script>
        // Fonctionnalité du timer
        function updateTimer() {
            const startTime = <?= $_SESSION['game_session']['start_time'] ?? 0 ?>;
            const now = Math.floor(Date.now() / 1000);
            const elapsed = now - startTime;
            
            // Mise à jour du temps écoulé
            const hours = Math.floor(elapsed / 3600);
            const minutes = Math.floor((elapsed % 3600) / 60);
            const seconds = elapsed % 60;
            
            document.getElementById('timeElapsed').textContent = 
                `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            // Mise à jour du temps restant (mode timer)
            <?php if (isset($_SESSION['game_session']['mode']) && $_SESSION['game_session']['mode'] === 'timer'): ?>
                const endTime = <?= $_SESSION['game_session']['end_time'] ?? 0 ?>;
                const remaining = endTime - now;
                
                if (remaining <= 0) {
                    document.getElementById('timeRemaining').textContent = '00:00';
                    document.getElementById('timeRemaining').classList.add('time-warning');
                    alert('Temps écoulé !');
                    window.location.href = '../index.php?timeout=1';
                } else {
                    const remMinutes = Math.floor(remaining / 60);
                    const remSeconds = remaining % 60;
                    const timeRemainingElement = document.getElementById('timeRemaining');
                    timeRemainingElement.textContent = 
                        `${remMinutes.toString().padStart(2, '0')}:${remSeconds.toString().padStart(2, '0')}`;
                    
                    if (remaining < 300) { // 5 minutes
                        timeRemainingElement.classList.add('time-warning');
                    }
                }
            <?php endif; ?>
        }
        
        // Initialisation du timer
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('timeElapsed')) {
                updateTimer();
                setInterval(updateTimer, 1000);
            }
        });
    </script>
</body>
</html>