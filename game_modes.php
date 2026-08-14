<?php

require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once __DIR__ . '/logs/log_config.php';

if (!Auth::isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Reset game mode when accessing modes page
unset($_SESSION['game_mode']);

$modes = [
    [
        'id' => 1,
        'name' => "Mode Classique",
        'desc' => "Résolvez les énigmes dans l'ordre",
        'rules' => "3 tentatives par énigme",
        'link' => "index.php?mode=classic"
    ],
    [
        'id' => 2,
        'name' => "Contre-la-montre",
        'desc' => "Défi chronométré (60min)",
        'rules' => "Temps limité",
        'link' => "index.php?mode=timer"
    ],
    [
        'id' => 3,
        'name' => "Mode Quiz",
        'desc' => "Testez vos connaissances scientifiques",
        'rules' => "4 réponses aléatoires",
        'link' => "quiz.php"
    ]
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modes de Jeu</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
            padding: 2rem;
        }

        h1 {
            text-align: center;
            color: #1a237e;
            margin-bottom: 3rem;
            font-size: 2.5rem;
        }

        .modes-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .mode-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            border: 2px solid transparent;
        }

        .mode-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .mode-card h2 {
            color: #1a237e;
            margin-bottom: 1rem;
            font-size: 1.8rem;
        }

        .mode-card p {
            color: #4a5568;
            line-height: 1.6;
            min-height: 60px;
        }

        .mode-card small {
            display: block;
            color: #718096;
            margin: 1rem 0;
            font-style: italic;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #4f46e5;
            color: white !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: background 0.3s;
            text-align: center;
            width: 100%;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background: #4338ca;
        }

        /* Style spécifique pour le mode quiz */
        .mode-card:nth-child(3) {
            border-color: #10b981;
        }
        .mode-card:nth-child(3) h2 {
            color: #10b981;
        }

        @media (max-width: 768px) {
            .modes-container {
                grid-template-columns: 1fr;
                padding: 1rem;
            }
            
            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <h1>Choisissez votre mode de jeu</h1>
    
    <div class="modes-container">
        <?php foreach ($modes as $mode): ?>
            <div class="mode-card">
                <h2><?= htmlspecialchars($mode['name']) ?></h2>
                <p><?= htmlspecialchars($mode['desc']) ?></p>
                <small><?= htmlspecialchars($mode['rules']) ?></small>
                <a href="<?= htmlspecialchars($mode['link']) ?>" class="btn">Jouer</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>