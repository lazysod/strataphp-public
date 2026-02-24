#!/usr/bin/env php
<?php
// Migration generator for Strata Framework
// Usage: php create_migration.php MigrationName
if ($argc < 2) {
    echo "Usage: php create_migration.php MigrationName\n";
    exit(1);
}
$name = preg_replace('/[^a-zA-Z0-9_]/', '', $argv[1]);
$date = date('Ymd_His');
$migration = $date . '_' . $name . '.php';
$down = $date . '_' . $name . '.down.php';
$dir = __DIR__ . '/../migrations/';

$migrationTemplate = <<<PHP
<?php
// Migration: $name
return function(\$db) {
    // TODO: Write migration logic here
};
PHP;

$downTemplate = <<<PHP
<?php
// Down migration: $name
return function(\$db) {
    // TODO: Write rollback logic here
};
PHP;

file_put_contents($dir . $migration, $migrationTemplate);
file_put_contents($dir . $down, $downTemplate);
echo "Created: $migration\nCreated: $down\n";
