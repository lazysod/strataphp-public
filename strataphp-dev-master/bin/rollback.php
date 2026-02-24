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
    // Look for a corresponding down migration: e.g. 003_drop_display_name_from_users.down.php
    $downFile = preg_replace('/\.php$/', '.down.php', $rollbackFile);
    if (!file_exists($downFile)) {
        echo "No rollback (down) migration found for $last.\n";
        continue;
    }
    $down = include $downFile;
    $down($db);
    $db->query('DELETE FROM migrations WHERE migration = ?', [$last]);
    echo "Rolled back: $last\n";
    $rolledBack++;
}
if ($rolledBack === 0) {
    echo "No migrations rolled back.\n";
} else {
    echo "Total rolled back: $rolledBack\n";
}
