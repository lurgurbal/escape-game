<?php
// quiz_results.php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/logs/log_config.php';
// Retirez require_once pour functions.php qui n'existe pas

if (!Auth::isLoggedIn()) {
    header('Location: login.php');
    exit();
}

if (!isset($_SESSION['quiz_score'])) {
    header('Location: quiz.php');
    exit();
}

// Définissez le nombre total de questions manuellement ou incluez le fichier des questions
$total_questions = 3; // Remplacez par le vrai nombre ou utilisez la solution 1 ci-dessus
$score = $_SESSION['quiz_score'];
$percentage = round(($score / $total_questions) * 100);

// Réinitialisation du quiz
unset($_SESSION['quiz_level']);
unset($_SESSION['quiz_score']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats du Quiz</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .results-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .results-score {
            font-size: 2em;
            color: #28a745;
            margin: 20px 0;
        }
        .results-percentage {
            font-size: 1.5em;
            margin: 20px 0;
        }
        .progress-circle {
            width: 150px;
            height: 150px;
            margin: 0 auto 30px;
            position: relative;
        }
        .progress-circle svg {
            transform: rotate(-90deg);
        }
        .progress-circle circle {
            fill: none;
            stroke-width: 10;
            stroke-linecap: round;
        }
        .progress-circle .bg {
            stroke: #f3f3f3;
        }
        .progress-circle .progress {
            stroke: #28a745;
            stroke-dasharray: 440;
            stroke-dashoffset: <?= 440 - (440 * $percentage / 100) ?>;
            transition: stroke-dashoffset 1s ease;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Résultats du Quiz</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Retour à l'accueil</a></li>
                    <li><a href="quiz.php">Recommencer le quiz</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="results-container">
            <h2>Votre performance</h2>
            
            <div class="progress-circle">
                <svg viewBox="0 0 160 160">
                    <circle class="bg" cx="80" cy="80" r="70"></circle>
                    <circle class="progress" cx="80" cy="80" r="70"></circle>
                </svg>
            </div>
            
            <div class="results-score">
                <?= $score ?> / <?= $total_questions ?>
            </div>
            
            <div class="results-percentage">
                <?= $percentage ?>%
            </div>
            
            <p>
                <?php
                if ($percentage >= 80) {
                    echo "Excellent travail! Vous maîtrisez parfaitement ces concepts.";
                } elseif ($percentage >= 60) {
                    echo "Bon score! Vous avez une bonne compréhension du sujet.";
                } elseif ($percentage >= 40) {
                    echo "Pas mal! Quelques révisions pourraient aider.";
                } else {
                    echo "Continuez à pratiquer! Vous vous améliorerez avec le temps.";
                }
                ?>
            </p>
            
            <a href="quiz.php" class="btn">Recommencer le quiz</a>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>Laboratoire de Physique Quantique</p>
        </div>
    </footer>
</body>
</html>