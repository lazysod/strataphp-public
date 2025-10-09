#!/usr/bin/env php
<?php
// Rollback migration tool for Strata Framework
require_once __DIR__ . '/../htdocs/app/config.php';
require_once __DIR__ . '/../htdocs/app/DB.php';
use App\DB;
$config = $config ?? require __DIR__ . '/../htdocs/app/config.php';
$db = new DB($config);

$migrationsDir = __DIR__ . '/../migrations/';

// Get number of steps to rollback (default 1)
$steps = 1;
if ($argc > 1 && is_numeric($argv[1]) && (int)$argv[1] > 0) {
    $steps = (int)$argv[1];
}

// Get applied migrations (latest last)
$rows = $db->fetchAll('SELECT migration FROM migrations ORDER BY id DESC');
$applied = array_column($rows, 'migration');

if (count($applied) === 0) {
    echo "No migrations to rollback.\n";
    exit(0);
}

$rolledBack = 0;
foreach ($applied as $i => $last) {
    if ($rolledBack >= $steps) break;
    
    $rollbackFile = $migrationsDir . $last;
    if (!file_exists($rollbackFile)) {
        echo "Migration file not found: $last\n";
        continue;
    }
    
    // Load the migration to check its format
    $migration = include $rollbackFile;
    $downExecuted = false;
    
    // Check for array format with 'down' function
    if (is_array($migration) && isset($migration['down']) && is_callable($migration['down'])) {
        echo "Rolling back (array format): $last... ";
        $migration['down']($db);
        $downExecuted = true;
    } else {
        // Look for a corresponding down migration: e.g. 003_drop_display_name_from_users.down.php
        $downFile = preg_replace('/\.php$/', '.down.php', $rollbackFile);
        if (file_exists($downFile)) {
            echo "Rolling back (separate file): $last... ";
            $down = include $downFile;
            if (is_callable($down)) {
                $down($db);
                $downExecuted = true;
            } else {
                echo "Invalid down migration format in $downFile\n";
                continue;
            }
        }
    }
    
    if (!$downExecuted) {
        echo "No rollback migration found for $last (checked both array format and .down.php file)\n";
        continue;
    }
    
    // Remove from migrations table
    $db->query('DELETE FROM migrations WHERE migration = ?', [$last]);
    echo "done.\n";
    $rolledBack++;
}
if ($rolledBack === 0) {
    echo "No migrations rolled back.\n";
} else {
    echo "Total rolled back: $rolledBack\n";
}
