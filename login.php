<?php
// login.php

// Enable debug mode
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/logs/log_config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect logged-in users
if (Auth::isLoggedIn()) {
    log_message("User already logged in, redirecting to index", "INFO");
    header('Location: index.php');
    exit();
}

$error = '';
$username = '';
$success = '';

// Check for registration success message
if (isset($_SESSION['registration_success'])) {
    $success = $_SESSION['registration_success'];
    unset($_SESSION['registration_success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    log_message("Login attempt for username: $username", "INFO");
    
    try {
        // Call login() on the $auth instance, not statically
        $user = $auth->login($username, $password);
        
        log_message("Successful login for user: $username", "INFO");
        
        // Login successful - session is already set by Auth class
        $redirect = $_SESSION['redirect_url'] ?? 'welcome.php';
        unset($_SESSION['redirect_url']);
        header("Location: $redirect");
        exit();
        
    } catch (Exception $e) {
        $error = $e->getMessage();
        log_message("Failed login attempt for $username: " . $e->getMessage(), "WARNING");
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Escape Game Scientifique</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-container {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
            transition: transform 0.3s;
        }

        .login-container:hover {
            transform: translateY(-3px);
        }

        h1 {
            color: #1a237e;
            text-align: center;
            margin-bottom: 2.5rem;
            font-size: 2.2rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .alert-danger {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fca5a5;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            color: #4a5568;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .btn-login {
            background: #4f46e5;
            color: white;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        .btn-login:hover {
            background: #4338ca;
            transform: translateY(-1px);
        }

        .links {
            text-align: center;
            margin-top: 1.5rem;
            color: #64748b;
        }

        .links a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .links a:hover {
            color: #4338ca;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 2rem;
                margin: 1rem;
            }
            
            h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Accès au Laboratoire</h1>
    
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Identifiant scientifique</label>
                <input type="text" name="username" required>
            </div>
            
            <div class="form-group">
                <label>Mot de passe sécurisé</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn-login">Authentification</button>
        </form>

        <div class="links">
            <p>Nouveau chercheur ? <a href="register.php">Créer un compte</a></p>
        </div>
    </div>
</body>
</html>