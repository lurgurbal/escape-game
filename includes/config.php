<?php
// includes/config.php

// 1. Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'escape_game');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// 2. Game Configuration
define('MAX_LEVEL', 3); // Corresponds to levels table
define('BASE_PATH', realpath(__DIR__.'/..'));
define('BASE_URL', 'http://'.($_SERVER['HTTP_HOST'] ?? 'localhost').'/escapegame/');
define('LOG_DIR', BASE_PATH.'/logs');

// 3. Security Functions
function clean_input($data) {
    if (is_array($data)) {
        return array_map('clean_input', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

// 4. Session Configuration
$cookieParams = [
    'lifetime' => 3600,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'] ?? 'localhost',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
];

// 5. Initialize session
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params($cookieParams);
    session_start([
        'cookie_lifetime' => $cookieParams['lifetime'],
        'cookie_secure' => $cookieParams['secure'],
        'cookie_httponly' => $cookieParams['httponly'],
        'cookie_samesite' => $cookieParams['samesite']
    ]);
}

// 6. Environment Configuration
define('ENVIRONMENT', 'development');

// 7. Error reporting based on environment
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', LOG_DIR.'/php_errors.log');
}

// 8. Database connection
try {
    $db = new PDO(
        'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log("Database connection failed: ".$e->getMessage());
    die("Database connection error. Please try again later.");
}

// 9. Initialize required classes
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/GameFunctions.php';

$auth = new Auth($db);
$gameFunctions = new GameFunctions($db);