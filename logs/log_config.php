
<?php
// Configuration des logs
define('LOG_MAX_FILES', 30);
define('LOG_RETENTION_DAYS', 30);

// Niveaux de log
const LOG_LEVELS = [
    'DEBUG'     => 0,
    'INFO'      => 1,
    'NOTICE'    => 2,
    'WARNING'   => 3,
    'ERROR'     => 4,
    'CRITICAL'  => 5,
    'ALERT'     => 6,
    'EMERGENCY' => 7
];

// Créer le répertoire logs sécurisé
if (!file_exists(LOG_DIR)) {
    mkdir(LOG_DIR, 0755, true);
    file_put_contents(LOG_DIR . '.htaccess', "Order deny,allow\nDeny from all");
}

// Rotation et nettoyage des logs
function manage_logs() {
    // Rotation par taille
    $files = glob(LOG_DIR . 'app_*.log');
    
    // Nettoyage par ancienneté
    $cutoff = strtotime('-' . LOG_RETENTION_DAYS . ' days');
    array_filter($files, function($file) use ($cutoff) {
        if (filemtime($file) < $cutoff) {
            unlink($file);
        }
    });
}

// Fonction de logging optimisée
function log_message(string $message, string $level = 'INFO', array $context = []) {
    $level = strtoupper($level);
    if (!isset(LOG_LEVELS[$level])) {
        $level = 'INFO';
    }

    $log_entry = sprintf(
        "[%s] %-9s %s %s\n",
        date('Y-m-d H:i:s.v'), // Ajout des millisecondes
        "[$level]",
        $message,
        $context ? json_encode($context, JSON_UNESCAPED_SLASHES) : ''
    );

    $log_file = LOG_DIR . 'app_' . date('Y-m-d') . '.log';
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    
    // Gestion des logs en arrière-plan
    register_shutdown_function('manage_logs');
}

// Logger spécial pour la sécurité
function log_security(string $event, array $details = []) {
    $log_entry = sprintf(
        "[%s] [SECURITY] %s %s\n",
        date('Y-m-d H:i:s.v'),
        $event,
        json_encode(array_merge([
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ], $details))
    );

    $sec_file = LOG_DIR . 'security_' . date('Y-m-d') . '.log';
    file_put_contents($sec_file, $log_entry, FILE_APPEND | LOCK_EX);
}
