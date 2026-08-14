<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once __DIR__ . 'logs/log_config.php';

$gameModeId = intval($_GET['mode'] ?? 0);

// Vérifier si le mode existe
$stmt = $pdo->prepare("SELECT * FROM game_modes WHERE id = ?");
$stmt->execute([$gameModeId]);
$gameMode = $stmt->fetch();

if (!$gameMode) {
    header('Location: modes.php');
    exit();
}

// Initialiser la session de jeu
$_SESSION['current_game'] = [
    'mode_id' => $gameModeId,
    'start_time' => time(),
    'attempts' => 0,
    'score' => 0,
    'current_enigma' => 1
];

// Mettre à jour le profil utilisateur
$stmt = $pdo->prepare("UPDATE utilisateurs SET current_game_mode = ? WHERE id = ?");
$stmt->execute([$gameModeId, $_SESSION['user_id']]);

header('Location: enigma.php');
exit();