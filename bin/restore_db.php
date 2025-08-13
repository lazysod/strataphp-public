#!/usr/bin/env php
<?php
// Database restore script for Strata Framework
require_once __DIR__ . '/../htdocs/app/config.php';

$dbname = $config['db']['database'];
$user = $config['db']['username'];
$pass = $config['db']['password'];
$host = $config['db']['host'];

if ($argc < 2) {
    echo "Usage: php bin/restore_db.php /path/to/backup.sql\n";
    exit(1);
}
$backupFile = $argv[1];
if (!file_exists($backupFile)) {
    echo "Backup file not found: $backupFile\n";
    exit(1);
}

$cmd = "mysql -h {$host} -u {$user} --password='{$pass}' {$dbname} < {$backupFile}";

system($cmd, $retval);
if ($retval === 0) {
    echo "Restore complete from: $backupFile\n";
} else {
    echo "Restore failed.\n";
    exit(1);
}
