<?php
// enigmes/enigme3.php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

if (!Auth::isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

if ($_SESSION['level'] != 3) {
    header('Location: ../../index.php');
    exit();
}

// Vérifie la provenance
if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = basename(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH));
    if ($referer === 'game-modes.php' && !isset($_GET['mode'])) {
        header('Location: game-modes.php');
        exit();
    }
}

$enigma = $gameFunctions->getCurrentEnigma(3);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'])) {
    $answer = clean_input($_POST['answer']);
    $correct = $gameFunctions->checkAnswer($_SESSION['user_id'], 3, $answer);
    
    if ($correct) {
        header('Location: ../../index.php');
        exit();
    } else {
        $feedback = '<div class="alert alert-danger">Réponse incorrecte. Essayez encore!</div>';
    }
}

$mode = $_GET['mode'] ?? 'classic'; // Default to classic

// Initialize game session if not exists
if (!isset($_SESSION['game_session'])) {
    $_SESSION['game_session'] = [
        'mode' => $mode,
        'attempts' => 0,
        'start_time' => time(),
        'current_enigma' => 3
    ];
    
    // Mode-specific initialization
    if ($mode === 'timer') {
        $_SESSION['game_session']['end_time'] = time() + 1800; // 30 minutes
    }
}

// Get enigmas based on mode
if ($_SESSION['game_session']['mode'] === 'classic') {
    $order = "WHERE niveau IN (1, 2, 3, 4, 5) ORDER BY niveau ASC";
} elseif ($_SESSION['game_session']['mode'] === 'timer') {
    $order = "WHERE niveau IN (1, 2, 3, 4, 5) ORDER BY RAND() LIMIT 5"; // 5 random enigmas
} elseif ($_SESSION['game_session']['mode'] === 'expert') {
    $order = "WHERE niveau IN (3, 4, 5) ORDER BY RAND()"; // Only harder enigmas
}

$stmt = $pdo->prepare("SELECT * FROM enigmes $order");
$stmt->execute();
$enigmes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Énigme 3 - Chute libre</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
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
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Laboratoire de Physique Quantique</h1>
            <nav>
                <ul>
                    <li><a href="../../index.php">Accueil</a></li>
                    <li><a href="../../profile.php">Profil</a></li>
                    <li><a href="../../logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="game-container">
            <h2>Niveau 3: Physique - Chute libre</h2>
            <div class="progress-container">
                <div class="progress-bar" style="width: 60%"></div>
            </div>
            
            <?php if (isset($_SESSION['game_session']['start_time'])): ?>
                <div class="timer">
                    <span>Temps écoulé: <span id="timeElapsed">00:00:00</span></span>
                    <?php if ($_SESSION['game_session']['mode'] === 'timer'): ?>
                        <span> / Temps restant: <span id="timeRemaining">30:00</span></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?= $feedback ?? '' ?>
            
            <div class="enigma-card">
                <div class="enigma-description">
                    <p>Pour stabiliser le système de refroidissement du laboratoire, vous devez résoudre ce problème de physique.</p>
                    <p><strong>Énoncé:</strong> <?= nl2br(htmlspecialchars($enigma['consigne'])) ?></p>
                    <p>Formule utile: v = √(2gh) où v est la vitesse, g l'accélération gravitationnelle et h la hauteur</p>
                </div>
                
                <form method="POST" action="enigme3.php?mode=<?= htmlspecialchars($mode) ?>">
                    <div class="form-group">
                        <label for="answer">Vitesse (en m/s):</label>
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

    <script src="../../assets/js/script.js"></script>
    <script>
        // Timer functionality
        function updateTimer() {
            const startTime = <?= $_SESSION['game_session']['start_time'] ?? 0 ?>;
            const now = Math.floor(Date.now() / 1000);
            const elapsed = now - startTime;
            
            // Update elapsed time
            const hours = Math.floor(elapsed / 3600);
            const minutes = Math.floor((elapsed % 3600) / 60);
            const seconds = elapsed % 60;
            
            document.getElementById('timeElapsed').textContent = 
                `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            // Update remaining time if in timer mode
            <?php if (isset($_SESSION['game_session']['mode']) && $_SESSION['game_session']['mode'] === 'timer'): ?>
                const endTime = <?= $_SESSION['game_session']['end_time'] ?? 0 ?>;
                const remaining = endTime - now;
                
                if (remaining <= 0) {
                    document.getElementById('timeRemaining').textContent = '00:00';
                    document.getElementById('timeRemaining').classList.add('time-warning');
                    alert('Temps écoulé !');
                    window.location.href = '../../index.php?timeout=1';
                } else {
                    const remMinutes = Math.floor(remaining / 60);
                    const remSeconds = remaining % 60;
                    const timeRemainingElement = document.getElementById('timeRemaining');
                    timeRemainingElement.textContent = 
                        `${remMinutes.toString().padStart(2, '0')}:${remSeconds.toString().padStart(2, '0')}`;
                    
                    // Add warning class when less than 5 minutes remain
                    if (remaining < 300) { // 5 minutes
                        timeRemainingElement.classList.add('time-warning');
                    }
                }
            <?php endif; ?>
        }
        
        // Initialize timer
        if (document.getElementById('timeElapsed')) {
            updateTimer();
            setInterval(updateTimer, 1000);
        }
    </script>
</body>
</html>