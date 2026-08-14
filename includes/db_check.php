<?php
// includes/db_check.php
require_once __DIR__ . '/database.php';

header('Content-Type: text/plain');
echo "DATABASE CONNECTION TEST\n";
echo "=======================\n";

try {
    global $db;
    
    // 1. Test basic connection
    echo "Connection test: ";
    $db->query("SELECT 1");
    echo "SUCCESS\n";
    
    // 2. Check levels table
    echo "Levels table exists: ";
    $exists = $db->query("SHOW TABLES LIKE 'levels'")->rowCount() > 0;
    echo $exists ? "YES" : "NO";
    echo "\n";
    
    if ($exists) {
        // 3. Count levels
        echo "Levels count: " . $db->query("SELECT COUNT(*) FROM levels")->fetchColumn() . "\n";
        
        // 4. Test insert
        echo "Test insert: ";
        $db->query("INSERT INTO levels (name, required_points) VALUES ('TEST_LEVEL', 0)");
        echo $db->errorCode() === '00000' ? "SUCCESS" : "FAILED";
        echo "\n";
        
        // 5. Cleanup
        $db->query("DELETE FROM levels WHERE name = 'TEST_LEVEL'");
    }
    
    echo "\nFULL TEST COMPLETE\n";
    
} catch (Exception $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
}