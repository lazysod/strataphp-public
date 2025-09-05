#!/usr/bin/env php
<?php
// Simple migration runner for Strata Framework
require_once __DIR__ . '/../htdocs/app/config.php';
require_once __DIR__ . '/../htdocs/app/DB.php';
use App\DB;

$config = $config ?? require __DIR__ . '/../htdocs/app/config.php';
$db = new DB($config);

$migrationsDir = __DIR__ . '/../migrations/';
$migrationFiles = glob($migrationsDir . '*.php');
sort($migrationFiles);

// Ensure migrations table exists
$init = include $migrationsDir . '001_create_migrations_table.php';
$init($db);
// Ensure migration_lock table exists
if (file_exists($migrationsDir . '005_create_migration_lock_table.php')) {
    $lockInit = include $migrationsDir . '005_create_migration_lock_table.php';
    $lockInit($db);
}
// Check for lock
$lock = $db->fetchAll('SELECT locked, locked_by, locked_at FROM migration_lock WHERE id = 1');
if ($lock && $lock[0]['locked']) {
    echo "Migrations are locked by {$lock[0]['locked_by']} at {$lock[0]['locked_at']}.\n";
    exit(1);
}
// Set lock
$locked_by = get_current_user() . '@' . gethostname();
$db->query('UPDATE migration_lock SET locked = 1, locked_at = NOW(), locked_by = ? WHERE id = 1', [$locked_by]);

// Get applied migrations
$rows = $db->fetchAll('SELECT migration FROM migrations');
$applied = array_column($rows, 'migration');

try {
    foreach ($migrationFiles as $file) {
        $name = basename($file);
        if ($name === '001_create_migrations_table.php') continue;
        if (substr($name, -9) === '.down.php') continue; // skip down migrations
        if (in_array($name, $applied)) {
            echo "Already applied: $name\n";
            continue;
        }
        echo "Applying: $name... ";
        $migration = include $file;
        $migration($db);
        $applied_by = get_current_user() . '@' . gethostname();
        $db->query('INSERT INTO migrations (migration, applied_by) VALUES (?, ?)', [$name, $applied_by]);
        echo "done.\n";
    }
    echo "All migrations complete.\n";
} finally {
    // Always clear lock
    $db->query('UPDATE migration_lock SET locked = 0, locked_at = NULL, locked_by = NULL WHERE id = 1');
}
