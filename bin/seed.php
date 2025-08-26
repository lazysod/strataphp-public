#!/usr/bin/env php
<?php
// Seeder tool for Strata Framework
require_once __DIR__ . '/../htdocs/app/config.php';
require_once __DIR__ . '/../htdocs/app/class/DB.php';

$config = $config ?? require __DIR__ . '/../htdocs/app/config.php';
$db = new DB($config);

$seedsDir = __DIR__ . '/../seeds/';

// Check for rollback/de-seed argument
$isDown = false;
if ($argc > 1 && ($argv[1] === '--down' || $argv[1] === 'rollback')) {
    $isDown = true;
}

if ($isDown) {
    $seedFiles = glob($seedsDir . '*.down.php');
    rsort($seedFiles); // reverse order
    foreach ($seedFiles as $file) {
        $name = basename($file);
        $seed = include $file;
        $seed($db);
        echo "De-seeded: $name\n";
    }
    echo "All de-seeds complete.\n";
    // ...existing code...
}

// Run all seeds or a specific one
if ($argc > 1) {
    $seedFile = $seedsDir . $argv[1];
    if (!file_exists($seedFile)) {
        echo "Seed file not found: $argv[1]\n";
    // ...existing code...
    }
    $seed = include $seedFile;
    $seed($db);
    echo "Seeded: $argv[1]\n";
    // ...existing code...
}

$seedFiles = glob($seedsDir . '*.php');
sort($seedFiles);
foreach ($seedFiles as $file) {
    if (substr($file, -9) === '.down.php') continue; // skip down files
    $name = basename($file);
    $seed = include $file;
    $seed($db);
    echo "Seeded: $name\n";
}
echo "All seeds complete.\n";
