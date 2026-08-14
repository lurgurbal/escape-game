<?php
// enigmes/enigme2.php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

if (!Auth::isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

if ($_SESSION['level'] != 2) {
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
$enigma = $gameFunctions->getCurrentEnigma(2);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'])) {
    $answer = clean_input($_POST['answer']);
    $correct = $gameFunctions->checkAnswer($_SESSION['user_id'], 2, $answer);
    
    if ($correct) {
        header('Location: ../../index.php');
        exit();
    } else {
        $feedback = '<div class="alert alert-danger">Réponse incorrecte. Essayez encore!</div>';
    }
}
$mode = $_GET['mode'] ?? 1; // Mode par défaut

switch($mode) {
    case 1: // Classique avec timer
        $order = "WHERE niveau IN (1, 2) ORDER BY niveau ASC"; 
        $_SESSION['start_time'] = time(); // Start timer for classic mode
        break;
    case 2: // Contre-la-montre
        $_SESSION['start_time'] = time();
        $_SESSION['end_time'] = time() + 1800; // 30 minutes
        $order = "WHERE niveau IN (1, 2) ORDER BY RAND() LIMIT 2"; // Only enigmas 1 and 2
        break;
    case 3: // Expert
        $_SESSION['start_time'] = time();
        $order = "WHERE niveau IN (1, 2) AND niveau >= 1 ORDER BY RAND()"; // Only enigmas 1 and 2
        break;
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
    <title>Énigme 2 - Mot de passe caché</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <!-- Mot de passe caché dans un commentaire HTML : quantum2023 -->
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
            <h2>Niveau 2: Cybersécurité - Mot de passe caché</h2>
            <div class="progress-container">
                <div class="progress-bar" style="width: 40%"></div>
            </div>
            
            <?php if (isset($_SESSION['start_time'])): ?>
                <div class="timer">
                    Temps écoulé: <span id="timeElapsed">00:00:00</span>
                </div>
            <?php endif; ?>
            
            <?= $feedback ?? '' ?>
            
            <div class="enigma-card">
                <div class="enigma-description">
                    <p>Le système a été compromis et les pirates ont caché un mot de passe quelque part dans cette page web. Trouvez-le pour accéder au niveau suivant.</p>
                    <p><strong>Indice:</strong> Les développeurs laissent parfois des informations sensibles dans le code source des pages web.</p>
                    <p><strong>Consigne:</strong> <?= nl2br(htmlspecialchars($enigma['consigne'])) ?></p>
                </div>
                
                <form method="POST" action="enigme2.php?mode=<?= $mode ?>">
                    <div class="form-group">
                        <label for="answer">Mot de passe caché:</label>
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
    <?php if (isset($_SESSION['start_time'])): ?>
    <script>
        // Timer functionality
        function updateTimer() {
            const startTime = <?= $_SESSION['start_time'] ?>;
            const now = Math.floor(Date.now() / 1000);
            const elapsed = now - startTime;
            
            const hours = Math.floor(elapsed / 3600);
            const minutes = Math.floor((elapsed % 3600) / 60);
            const seconds = elapsed % 60;
            
            document.getElementById('timeElapsed').textContent = 
                `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }
        
        updateTimer();
        setInterval(updateTimer, 1000);
    </script>
    <?php endif; ?>
</body>
</html>