<?php
// Script to clean up old/revoked sessions for all users
// Usage: php bin/cleanup_sessions.php

require_once __DIR__ . '/../public_html/bootstrap.php';
use App\DB;

$config = include __DIR__ . '/../public_html/app/config.php';
$db = new DB($config);

// Delete revoked sessions older than 7 days
$deleted = $db->query("DELETE FROM user_sessions WHERE revoked = 1 AND last_seen < DATE_SUB(NOW(), INTERVAL 7 DAY)");

// Optionally, delete all revoked sessions (uncomment below)
// $deleted = $db->query("DELETE FROM user_sessions WHERE revoked = 1");

echo "Cleanup complete. Old revoked sessions deleted.\n";
