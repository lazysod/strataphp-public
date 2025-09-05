#!/usr/bin/env php
<?php
// Initial install script for StrataPHP Framework
// Usage: php bin/install.php

require_once __DIR__ . '/../htdocs/app/config.php';

$schemaFile = __DIR__ . '/../mysql/db_instal.sql';
if (!file_exists($schemaFile)) {
    echo "Schema file not found: $schemaFile\n";
    exit(1);
}

$config = $config ?? require __DIR__ . '/../htdocs/app/config.php';
$dbConfig = $config['db'];

try {
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = file_get_contents($schemaFile);
    $pdo->exec($sql);
    echo "Database schema imported successfully.\n";
} catch (Exception $e) {
    echo "Error importing schema: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nNext step: Run the create_admin script to set up your admin account.\n";
echo "php bin/create_admin.php\n";
