#!/usr/bin/env php
<?php
// Migration status tool for Strata Framework
require_once __DIR__ . '/../htdocs/app/config.php';
require_once __DIR__ . '/../htdocs/app/DB.php';

use App\DB;
$config = $config ?? require __DIR__ . '/../htdocs/app/config.php';
$db = new DB($config);

$migrationsDir = __DIR__ . '/../migrations/';
$migrationFiles = glob($migrationsDir . '*.php');
sort($migrationFiles);

// Get applied migrations
$rows = $db->fetchAll('SELECT migration FROM migrations');
$applied = array_column($rows, 'migration');

// List all migrations and their status
printf("%-45s | %-10s\n", 'Migration', 'Status');
echo str_repeat('-', 60) . "\n";
foreach ($migrationFiles as $file) {
    $name = basename($file);
    if ($name === '001_create_migrations_table.php') continue;
    $status = in_array($name, $applied) ? 'applied' : 'pending';
    printf("%-45s | %-10s\n", $name, $status);
}
