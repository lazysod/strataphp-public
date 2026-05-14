#!/usr/bin/env php
<?php
// Database backup script for Strata Framework
if (!isset($config)) {
    $config = require __DIR__ . '/../public_html/app/config.php';
}
if (!$config || !isset($config['db'])) {
    echo "Could not load database config.\n";
    exit(1);
}

$dbname = $config['db']['database'];
$user = $config['db']['username'];
$pass = $config['db']['password'];
$host = $config['db']['host'];
$port = isset($config['db']['port']) ? $config['db']['port'] : '3306';

if ($argc < 2) {
    echo "Usage: php bin/restore_db.php /path/to/backup.sql [port]\n";
    exit(1);
}
$backupFile = $argv[1];
$port = isset($argv[2]) ? $argv[2] : (isset($config['db']['port']) ? $config['db']['port'] : '3306');
if (!file_exists($backupFile)) {
    echo "Backup file not found: $backupFile\n";
    exit(1);
}

$cmd = "mysql --protocol=TCP -h {$host} -P {$port} -u {$user} --password='{$pass}' {$dbname} < {$backupFile}";

system($cmd, $retval);
if ($retval === 0) {
    echo "Restore complete from: $backupFile\n";
} else {
    echo "Restore failed.\n";
    exit(1);
}
