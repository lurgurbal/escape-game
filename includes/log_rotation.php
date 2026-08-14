<?php
function rotate_logs($log_dir, $max_files = 7) {
    $log_files = glob($log_dir . '/auth*.log');
    
    if (count($log_files) >= $max_files) {
        // Sort by date (oldest first)
        usort($log_files, function($a, $b) {
            return filemtime($a) - filemtime($b);
        });
        
        // Delete the oldest files beyond our max limit
        for ($i = 0; $i < count($log_files) - $max_files + 1; $i++) {
            unlink($log_files[$i]);
        }
    }
    
    // Rotate current log if it's too big (>5MB)
    $current_log = $log_dir . '/auth.log';
    if (file_exists($current_log) && filesize($current_log) > 5 * 1024 * 1024) {
        $backup_name = $log_dir . '/auth_' . date('Y-m-d_His') . '.log';
        rename($current_log, $backup_name);
    }
}
?>
