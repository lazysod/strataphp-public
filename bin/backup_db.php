#!/usr/bin/env php
<?php
// Database backup script for Strata Framework
if (!isset($config)) {
    $config = require __DIR__ . '/../htdocs/app/config.php';
}
if (!$config || !isset($config['db'])) {
    echo "Could not load database config.\n";
    exit(1);
}

$dbname = $config['db']['database'];
$user = $config['db']['username'];
$pass = $config['db']['password'];
$host = $config['db']['host'];

// Check if mysqldump is available
exec('which mysqldump', $out, $whichRet);
if ($whichRet !== 0) {
    echo "mysqldump command not found. Please install MySQL client tools.\n";
    exit(1);
}

$date = date('Ymd_His');
$backupDir = __DIR__ . '/../storage/backups/';
if (!is_dir($backupDir)) mkdir($backupDir, 0777, true);
$backupFile = $backupDir . "backup_{$dbname}_{$date}.sql";

$cmd = "mysqldump -h {$host} -u {$user} --password='{$pass}' {$dbname} > {$backupFile}";

system($cmd, $retval);
if ($retval === 0) {
    echo "Backup complete: $backupFile\n";
} else {
    echo "Backup failed.\n";
    exit(1);
}
