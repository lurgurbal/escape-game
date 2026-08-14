<?php
// quiz.php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/GameFunctions.php';

if (!Auth::isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Define quiz questions first
$quiz_questions = [
    1 => [
        'question' => 'Un radiateur consomme 1000 W pendant 3 heures. Quel est le coût de cette énergie à 0,18 €/kWh?',
        'correct' => '0.54 €',
        'incorrect' => ['1.80 €', '0.18 €', '3.60 €']
    ],
    2 => [
        'question' => 'Quelle est l\'unité de mesure de la résistance électrique?',
        'correct' => 'Ohm',
        'incorrect' => ['Watt', 'Volt', 'Ampère']
    ],
    3 => [
        'question' => 'Quel scientifique est connu pour ses lois sur le mouvement?',
        'correct' => 'Newton',
        'incorrect' => ['Einstein', 'Galilée', 'Tesla']
    ]
];

// Initialize session variables for the quiz
if (!isset($_SESSION['quiz_level'])) {
    $_SESSION['quiz_level'] = 1;
    $_SESSION['quiz_score'] = 0;
}

$current_level = $_SESSION['quiz_level'];

// Check if the level exists in the quiz
if (!isset($quiz_questions[$current_level])) {
    die('Niveau de quiz non trouvé');
}

$questionData = $quiz_questions[$current_level];

// Prepare random answers
$all_answers = array_merge(
    [$quiz_questions[$current_level]['correct']],
    $quiz_questions[$current_level]['incorrect']
);
shuffle($all_answers);

// Process the answer
$feedback = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'])) {
    $selected_answer = clean_input($_POST['answer']);
    $correct_answer = $quiz_questions[$current_level]['correct'];
    
    if ($selected_answer === $correct_answer) {
        $feedback = '<div class="alert alert-success">Bonne réponse! +1 point</div>';
        $_SESSION['quiz_score']++;
        $_SESSION['quiz_level']++;
        
        // Redirection if all levels are completed
        if ($_SESSION['quiz_level'] > count($quiz_questions)) {
            header('Location: quiz_results.php');
            exit();
        }
    } else {
        $feedback = '<div class="alert alert-danger">Incorrect! La bonne réponse était: '.htmlspecialchars($correct_answer).'</div>';
        $_SESSION['quiz_level']++;
        
        // Redirection if all levels are completed
        if ($_SESSION['quiz_level'] > count($quiz_questions)) {
            header('Location: quiz_results.php');
            exit();
        }
    }
    
    // Reload the page for the new question
    header('Location: quiz.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mode Quiz - Niveau <?= htmlspecialchars($current_level) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .quiz-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .quiz-question {
            font-size: 1.2em;
            margin-bottom: 20px;
            color: #333;
        }
        .quiz-option {
            display: block;
            margin: 10px 0;
            padding: 12px 15px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .quiz-option:hover {
            background: #e9ecef;
        }
        .quiz-option input[type="radio"] {
            margin-right: 10px;
        }
        .quiz-progress {
            margin: 20px 0;
            font-weight: bold;
        }
        .quiz-score {
            float: right;
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Quiz Scientifique</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Retour au jeu principal</a></li>
                    <li><a href="profile.php">Profil</a></li>
                    <li><a href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="quiz-container">
            <div class="quiz-progress">
                Question <?= $current_level ?> sur <?= count($quiz_questions) ?>
                <div class="quiz-score">Score: <?= $_SESSION['quiz_score'] ?></div>
            </div>
            
            <?= $feedback ?>
            
            <div class="quiz-question">
                <?= htmlspecialchars($quiz_questions[$current_level]['question']) ?>
            </div>
            
            <form method="POST" action="quiz.php">
                <?php foreach ($all_answers as $index => $answer): ?>
                    <label class="quiz-option">
                        <input type="radio" name="answer" value="<?= htmlspecialchars($answer) ?>" required>
                        <?= htmlspecialchars($answer) ?>
                    </label>
                <?php endforeach; ?>
                
                <button type="submit" class="btn">Valider</button>
            </form>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>Quiz Scientifique - Laboratoire de Physique Quantique</p>
        </div>
    </footer>
</body>
</html>