<?php

require_once __DIR__ . '/htdocs/app/config.php';
require_once __DIR__ . '/htdocs/app/DB.php';

$config = include __DIR__ . '/htdocs/app/config.php';
$db = new \App\DB($config);

// Generate a secure random API key
$apiKey = bin2hex(random_bytes(32));
$siteName = 'My New Site';

// Insert the site and get the ID
$db->query("INSERT INTO sites (name, api_key, status) VALUES (?, ?, 'active')", [$siteName, $apiKey]);
$siteId = $db->insertId();

echo "Site created! ID: $siteId\nAPI Key: $apiKey\n";
