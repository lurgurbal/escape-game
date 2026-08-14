<?php
// welcome.php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'logs/log_config.php';

if (!Auth::isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Vérifier si l'utilisateur a déjà vu l'intro
if (isset($_SESSION['intro_viewed'])) {
    header('Location: game_modes.php');
    exit();
}

// Marquer l'intro comme vue
$_SESSION['intro_viewed'] = true;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue au Laboratoire</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero-section {
            background: linear-gradient(rgba(44, 62, 80, 0.9), rgba(52, 152, 219, 0.8)),
                        url('assets/images/lab-bg.jpg') center/cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding: 2rem;
        }

        .story-card {
            max-width: 800px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            transform: translateY(20px);
            opacity: 0;
            animation: storyEntrance 1s 0.5s forwards;
        }

        @keyframes storyEntrance {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .story-text {
            font-size: 1.1rem;
            line-height: 1.8;
            margin: 2rem 0;
            color :black
        }

        .start-btn {
            font-size: 1.2rem;
            padding: 1rem 3rem;
            border-radius: 50px;
            transition: all 0.3s;
        }

        .typewriter {
            border-right: 3px solid white;
            white-space: nowrap;
            overflow: hidden;
            font-size: 2.5rem;
            margin: 0 auto;
            animation: typing 3s steps(30), blink-caret 0.5s step-end infinite;
            color :black
        }

        @keyframes typing {
            from { width: 0 }
            to { width: 100% }
        }

        @keyframes blink-caret {
            from, to { border-color: transparent }
            50% { border-color: white }
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="story-card fade-in">
            <h1 class="typewriter">Laboratoire de Physique Quantique</h1>
            
            <div class="story-text">
                <p>🔬 23h47 - Alerte Sécurité Niveau 4</p>
                <p>Un silence inquiétant règne dans les couloirs du laboratoire. Les systèmes de confinement viennent de tomber en panne, et une mystérieuse énergie quantique se répand dans le complexe.</p>
                
                <p>💻 Dernier journal du Dr. Schrödinger :<br>
                <em>"Les particules exotiques montrent un comportement imprévisible... Les équations de Maxwell ne s'appliquent plus... Il faut absolument..."</em></p>

                <p>⚠️ Votre mission :<br>
                Résoudre les énigmes scientifiques avant que l'anomalie quantique n'atteigne le cœur du réacteur. Utilisez vos connaissances en physique pour rétablir les systèmes !</p>
            </div>

            <button onclick="startAdventure()" class="btn start-btn">
                🚀 Activer le Protocole d'Urgence
            </button>
        </div>
    </div>

    <script>
        function startAdventure() {
            // Ajouter un effet de transition
            document.querySelector('.story-card').style.animation = 'storyExit 1s forwards';
            
            setTimeout(() => {
                window.location.href = 'game_modes.php';
            }, 1000);
        }

        // Ajouter l'animation de sortie
        const style = document.createElement('style');
        style.textContent = `
            @keyframes storyExit {
                from { transform: translateY(0); opacity: 1 }
                to { transform: translateY(-100px); opacity: 0 }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>