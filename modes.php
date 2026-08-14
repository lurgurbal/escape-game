<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once __DIR__ . 'logs/log_config.php';

// Récupérer tous les modes de jeu
$stmt = $pdo->query("SELECT * FROM game_modes");
$gameModes = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<section class="game-modes">
    <h2>Choisissez votre mode de jeu</h2>
    <link rel="stylesheet" href="assets/css/style.css">
    <div class="mode-grid">
        <?php foreach ($gameModes as $mode): ?>
        <div class="mode-card" style="background-image: url('assets/images/<?= $mode['theme_image'] ?>')">
            <div class="mode-content">
                <h3><?= htmlspecialchars($mode['name']) ?></h3>
                <div class="difficulty <?= $mode['difficulty'] ?>">
                    <?= ucfirst($mode['difficulty']) ?>
                </div>
                <p><?= htmlspecialchars($mode['description']) ?></p>
                
                <?php if ($mode['duration']): ?>
                <div class="duration">
                    ⏱ <?= $mode['duration'] ?> minutes
                </div>
                <?php endif; ?>
                
                <a href="start_game.php?mode=<?= $mode['id'] ?>" class="btn">
                    <?= (Auth::getUserLevel() >= 3) ? 'Commencer' : 'Débloquer à niveau 3' ?>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>