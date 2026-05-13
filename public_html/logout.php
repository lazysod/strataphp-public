<?php
// Global logout: destroy session and redirect to main site
session_destroy();

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/app/SessionManager.php';
use App\SessionManager;
use App\DB;
// Instantiate SessionManager and revoke the session
$db = new DB($config);
$sessionManager = new SessionManager($db, $config);
$sessionManager->destroySession();

// Remove the legacy remember me cookie (if still needed)
$sessionPrefix = $config['session_prefix'] ?? '';
setcookie($sessionPrefix . 'cookie_login', '', time() - 42000, '/', '', isset($_SERVER['HTTPS']), true);

header('Location: /');
exit;
