<?php
// Destroy admin session and redirect to main site
session_start();
// Unset all session variables

$_SESSION = [];

// Destroy session cookie if exists
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// Destroy the session
session_destroy();

header('Location: /');
exit;
