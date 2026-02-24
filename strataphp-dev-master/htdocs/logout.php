<?php
// Global logout: destroy session and redirect to main site
require_once __DIR__ . '/app/start.php'; // config, autoload, etc.
$config = require __DIR__ . '/app/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// Remove the new session management cookies
setcookie(PREFIX . 'session_token', '', time() - 42000, '/', '', isset($_SERVER['HTTPS']), true);
setcookie(PREFIX . 'device_id', '', time() - 42000, '/', '', isset($_SERVER['HTTPS']), true);
// Remove the legacy remember me cookie
setcookie(PREFIX . 'cookie_login', '', time() - 42000, '/', '', isset($_SERVER['HTTPS']), true);

session_destroy();
header('Location: /');
exit;
