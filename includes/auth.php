<?php
// includes/auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 1800,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict',
        'use_strict_mode' => true
    ]);
}

require_once 'database.php';

class Auth {
    private $db;
    const MAX_LOGIN_ATTEMPTS = 5;
    const LOCKOUT_TIME = 1800;
    const SESSION_TIMEOUT = 1800;

    public function __construct($db) {
        $this->db = $db;
        $this->createLoginAttemptsTable();
    }

    private function createLoginAttemptsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS login_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip VARCHAR(45) NOT NULL,
            username VARCHAR(255) NOT NULL,
            attempts INT NOT NULL DEFAULT 0,
            last_attempt DATETIME NOT NULL,
            INDEX idx_ip (ip),
            INDEX idx_username (username)
        ) ENGINE=InnoDB";
        
        $this->db->exec($sql);
    }

    // User registration
    public function register($username, $password, $email = null) {
        // Input validation
        if (empty($username)) {
            throw new Exception("Username required");
        }
        
        if (empty($password)) {
            throw new Exception("Password required");
        }

        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
            throw new Exception("Username must contain 3-20 characters (letters, numbers, underscores)");
        }

        if (!$this->isPasswordStrong($password)) {
            throw new Exception("Password must contain at least 8 characters with uppercase, lowercase and numbers");
        }

        // Check if user exists
        $stmt = $this->db->prepare("SELECT id FROM utilisateurs WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->fetch()) {
            throw new Exception("Username already taken");
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert new user
        $stmt = $this->db->prepare("INSERT INTO utilisateurs (username, password_hash, current_level, created_at) VALUES (?, ?, 1, NOW())");
        $stmt->execute([$username, $hashedPassword]);

        return $this->db->lastInsertId();
    }

    // User login
    public function login($username, $password) {
        // Check if blocked
        if ($this->isBlocked($username)) {
            throw new Exception("Too many attempts. Please try again later.");
        }

        // Get user from database
        $stmt = $this->db->prepare("SELECT id, username, password_hash, current_level FROM utilisateurs WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $this->resetAttempts($username);
            $this->initUserSession($user);
            return true;
        }

        $this->recordAttempt($username);
        throw new Exception("Invalid username or password");
    }

    private function initUserSession($user) {
        session_regenerate_id(true);
        $_SESSION = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'level' => $user['current_level'],
            'role' => $user['role'] ?? 'user', // Ajout du rôle
            'logged_in' => true,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'last_activity' => time()
        ];
    }

    public static function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    public static function requireAdmin() {
        if (!self::isAdmin()) {
            header('Location: /');
            exit();
        }
    }

    // Check if user is blocked
    private function isBlocked($username) {
        $stmt = $this->db->prepare("SELECT attempts, last_attempt FROM login_attempts 
                                  WHERE username = ? OR ip = ?");
        $stmt->execute([$username, $_SERVER['REMOTE_ADDR']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && $result['attempts'] >= self::MAX_LOGIN_ATTEMPTS) {
            $lastAttempt = strtotime($result['last_attempt']);
            return (time() - $lastAttempt) < self::LOCKOUT_TIME;
        }
        return false;
    }

    // Record failed attempt
    private function recordAttempt($username) {
        $ip = $_SERVER['REMOTE_ADDR'];
        
        $stmt = $this->db->prepare("SELECT id, attempts FROM login_attempts 
                                  WHERE ip = ? OR username = ? LIMIT 1");
        $stmt->execute([$ip, $username]);
        $attempt = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($attempt) {
            $stmt = $this->db->prepare("UPDATE login_attempts 
                                      SET attempts = attempts + 1, 
                                          last_attempt = NOW()
                                      WHERE id = ?");
            $stmt->execute([$attempt['id']]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO login_attempts 
                                      (ip, username, attempts, last_attempt) 
                                      VALUES (?, ?, 1, NOW())");
            $stmt->execute([$ip, $username]);
        }
    }

    // Reset login attempts
    private function resetAttempts($username) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $this->db->prepare("DELETE FROM login_attempts WHERE ip = ? OR username = ?")
             ->execute([$ip, $username]);
    }

    // Check if user is logged in
    public static function isLoggedIn() {
        return isset($_SESSION['logged_in']) && 
               $_SESSION['logged_in'] === true &&
               $_SESSION['ip'] === $_SERVER['REMOTE_ADDR'] &&
               $_SESSION['user_agent'] === $_SERVER['HTTP_USER_AGENT'] &&
               (time() - ($_SESSION['last_activity'] ?? 0)) < self::SESSION_TIMEOUT;
    }

    // Update session activity
    public static function updateSessionActivity() {
        if (self::isLoggedIn()) {
            $_SESSION['last_activity'] = time();
        }
    }

    // Get user ID
    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    // Get user level
    public static function getUserLevel() {
        return $_SESSION['level'] ?? 1;
    }

    // Generate CSRF token
    public function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        return $_SESSION['csrf_token'];
    }

    // Validate CSRF token
    public function validateCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $token);
    }

    // Logout
    public function logout() {
        $_SESSION = [];
        session_destroy();
        setcookie(session_name(), '', time()-3600, '/');
    }

    // Check password strength
    private function isPasswordStrong($password) {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password);
    }

    // Update user level
    public function updateUserLevel($userId, $newLevel) {
        $stmt = $this->db->prepare("UPDATE utilisateurs SET current_level = ? WHERE id = ?");
        $stmt->execute([$newLevel, $userId]);
        
        if (self::isLoggedIn() && $_SESSION['user_id'] == $userId) {
            $_SESSION['level'] = $newLevel;
        }
    }
}

// Initialize auth system
try {
    global $db;
    $auth = new Auth($db);
} catch (Exception $e) {
    error_log("Auth initialization failed: " . $e->getMessage());
    die("Authentication system error. Please try again later.");
}
?>