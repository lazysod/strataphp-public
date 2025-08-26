#!/usr/bin/env php
<?php
require_once __DIR__ . '/../htdocs/app/config.php';
require_once __DIR__ . '/../htdocs/app/class/DB.php';

if ($argc < 2) {
    echo "Usage: php bin/run_down_migration.php <migration_name>\n";
    // ...existing code...
}

$migrationName = $argv[1];
$migrationFile = __DIR__ . '/../migrations/' . $migrationName . '.down.php';

if (!file_exists($migrationFile)) {
    echo "Down migration file not found: $migrationFile\n";
    // ...existing code...
}

$config = $config ?? require __DIR__ . '/../htdocs/app/config.php';
$db = new DB($config);
$migration = include $migrationFile;
if (is_callable($migration)) {
    $migration($db);
    echo "Down migration executed: $migrationName\n";
} else {
    echo "Migration file did not return a callable.\n";
}
