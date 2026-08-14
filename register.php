<?php
// register.php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug mode - set to false in production
define('DEBUG', true);

// Includes
require_once 'includes/config.php';
require_once 'includes/GameFunctions.php';
require_once 'includes/auth.php';
require_once __DIR__ . '/logs/log_config.php';

// Redirect if already logged in
if (Auth::isLoggedIn()) {
    log_message("User already logged in, redirecting from registration", "INFO");
    header('Location: index.php');
    exit();
}

$error = '';
$username = $password = $confirm_password = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get and sanitize inputs
        $username = clean_input($_POST['username'] ?? '');
        $password = clean_input($_POST['password'] ?? '');
        $confirm_password = clean_input($_POST['confirm_password'] ?? '');
       
        log_message("Registration attempt for username: $username", "INFO");

        // Validation
        if (empty($username) || empty($password) || empty($confirm_password)) {
            throw new Exception("Tous les champs obligatoires sont requis.");
        }

        if ($password !== $confirm_password) {
            log_message("Password mismatch for user: $username", "WARNING");
            throw new Exception("Les mots de passe ne correspondent pas.");
        }

        if (strlen($password) < 8) {
            log_message("Password too short for user: $username", "WARNING");
            throw new Exception("Le mot de passe doit contenir au moins 8 caractères.");
        }

        // Attempt registration
        $result = $auth->register($username, $password); 

        if ($result === true) {
            log_message("Successful registration for user: $username", "INFO");
            $_SESSION['registration_success'] = "Inscription réussie! Vous pouvez maintenant vous connecter.";

            // Attempt login
            $login_result = $auth->login($username, $password);

            if ($login_result === true) {
                log_message("Auto-login successful after registration for user: $username", "INFO");
                $_SESSION['login_success'] = "Inscription et connexion réussies!";
                header('Location: index.php');
                exit();
            } else {
                log_message("Auto-login failed after registration for user: $username", "WARNING");
                header('Location: login.php');
                exit();
            }
        } else {
            log_message("Registration failed for user $username: $result", "ERROR");
            throw new Exception($result);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
        log_message("Registration error: " . $e->getMessage(), "ERROR");
        
        if (DEBUG) {
            $error .= " (Debug: Check error logs for details)";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Escape Game Scientifique</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .debug-info {
            background-color: #f8f9fa;
            padding: 15px;
            margin: 20px 0;
            border: 1px solid #ddd;
            font-family: monospace;
        }
        .alert {
            padding: 10px;
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            margin-bottom: 20px;
        }
        .alert.success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .login-container {
            max-width: 400px;
            margin: auto;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Inscription</h1>
        <p>Créez un compte pour accéder au laboratoire</p>

        <?php if (isset($_SESSION['registration_success'])): ?>
            <div class="alert success">
                <?= htmlspecialchars($_SESSION['registration_success']) ?>
                <?php unset($_SESSION['registration_success']); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (DEBUG && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="debug-info">
                <h3>Debug Information:</h3>
                <p>Username: <?= htmlspecialchars($username) ?></p>
                <p>Password Length: <?= strlen($password) ?></p>
                <p>Confirm Password Length: <?= strlen($confirm_password) ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" required 
                       value="<?= htmlspecialchars($username) ?>">
            </div>
            <div class="form-group">
                <label for="password">Mot de passe (8 caractères minimum)</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn">S'inscrire</button>
        </form>

        <p class="register-link">Déjà un compte ? <a href="login.php">Se connecter</a></p>
    </div>
</body>
</html>