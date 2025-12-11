<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/app/start.php'; // config, autoload, etc.
use App\DB;
use App\User;

$config = require __DIR__ . '/app/config.php';

// Ensure session prefix and PREFIX constant are set before use
if (!defined('SESSION_PREFIX')) {
    $config = require __DIR__ . '/app/config.php';
    define('SESSION_PREFIX', $config['session_prefix'] ?? 'app_');
}
$sessionPrefix = defined('SESSION_PREFIX') ? SESSION_PREFIX : 'app_';
if (!defined('PREFIX')) {
    define('PREFIX', $sessionPrefix);
}

// Global error and exception handlers

set_error_handler(function($errno, $errstr, $errfile, $errline) use ($config) {
    if ($config['debug']) {
        echo '<div style="margin:2em auto;max-width:600px;padding:1em;border:1px solid #e74c3c;background:#fff3f3;color:#c0392b;font-family:sans-serif;text-align:center;">';
        echo '<strong>Oops! An error occurred:</strong><br>';
        echo htmlspecialchars($errstr) . '<br><small>(' . htmlspecialchars($errfile) . ' line ' . $errline . ')</small>';
        echo '</div>';
    } else {
        include $config['system_pages'][500];
    }
    exit;
});

set_exception_handler(function($exception) use ($config) {
    error_log("[EXCEPTION] " . $exception->getMessage() . "\n", 3, $config['log_path']);
    if ($config['debug']) {
        echo '<div style="margin:2em auto;max-width:600px;padding:1em;border:1px solid #e74c3c;background:#fff3f3;color:#c0392b;font-family:sans-serif;text-align:center;">';
        echo '<strong>Oops! An unexpected error occurred:</strong><br>';
        echo htmlspecialchars($exception->getMessage()) . '<br><small>(' . htmlspecialchars($exception->getFile()) . ' line ' . $exception->getLine() . ')</small>';
        echo '</div>';
    } else {
        include $config['system_pages'][500];
    }
    exit;
});


$db = new DB($config);
$user = new App\User($db, $config);

$requestPath = '/' . trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
if ($requestPath === '//') { $requestPath = '/'; }

$isLoginPage = ($requestPath === '/user/login' || $requestPath === '/admin/admin_login.php');
$isAdminRoute = (strpos($requestPath, '/admin') === 0);
if (!$isLoginPage) {
    require_once __DIR__ . '/app/SessionManager.php';
    $sessionManager = new App\SessionManager($db, $config);
    $sessionPrefix = $config['session_prefix'] ?? 'app_';
    // Validate session and get current session info
    $session_token = $_COOKIE[PREFIX . 'session_token'] ?? null;
    $device_id = $_COOKIE[PREFIX . 'device_id'] ?? null;
    if ($session_token && $device_id) {
        $sessionRow = $db->fetch("SELECT * FROM user_sessions WHERE session_token = ? AND device_id = ? AND revoked = 0", [$session_token, $device_id]);
        if ($sessionRow) {
            $_SESSION[$sessionPrefix . 'session_id'] = $sessionRow['id'];
            $validated_user_id = $sessionRow['user_id'];
            // Restore session variables if missing
            if (!isset($_SESSION[$sessionPrefix . 'user_id'])) {
                $userData = $db->fetch("SELECT * FROM users WHERE id = ?", [$validated_user_id]);
                if ($userData) {
                    $rank = (new App\User($db, $config))->get_rank($userData['id']);
                    $_SESSION[$sessionPrefix . 'rank_title'] = $rank['title'];
                    $_SESSION[$sessionPrefix . 'rank_level'] = $rank['level'];
                    if ($rank['admin'] > 0) {
                        $_SESSION[$sessionPrefix . 'admin'] = $rank['admin'];
                    }
                    $_SESSION[$sessionPrefix . 'email'] = $userData['email'];
                    $_SESSION[$sessionPrefix . 'user_id'] = $userData['id'];
                    $_SESSION[$sessionPrefix . 'sec_hash'] = $userData['security_hash'];
                    $_SESSION[$sessionPrefix . 'first_name'] = $userData['first_name'];
                    $_SESSION[$sessionPrefix . 'second_name'] = $userData['second_name'];
                    $_SESSION[$sessionPrefix . 'last_log'] = $userData['last_access'];
                    $_SESSION[$sessionPrefix . 'avatar'] = $userData['avatar'];
                    $_SESSION[$sessionPrefix . 'user'] = [
                        'id' => $userData['id'],
                        'email' => $userData['email'],
                        'is_admin' => ($rank['admin'] > 0 ? 1 : 0),
                        'rank_title' => $rank['title'],
                        'rank_level' => $rank['level'],
                        'avatar' => (new App\User($db, $config))->gravatar($userData['email'])
                    ];
                }
            }
        } else {
            // Session not found or revoked - clear session and redirect to login
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(), '', time() - 42000,
                    $params['path'], $params['domain'],
                    $params['secure'], $params['httponly']
                );
            }
            
            // Remove session management cookies
            setcookie(PREFIX . 'session_token', '', time() - 42000, '/', '', isset($_SERVER['HTTPS']), true);
            setcookie(PREFIX . 'device_id', '', time() - 42000, '/', '', isset($_SERVER['HTTPS']), true);
            
            session_destroy();
            header('Location: /user/login');
            exit;
        }
    } elseif (isset($_SESSION[$sessionPrefix . 'user_id'])) {
        // User has session variables but no session cookies - session was revoked
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
        header('Location: /user/login');
        exit;
    }
}

$method = $_SERVER['REQUEST_METHOD'];

$requestPath = '/' . trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
if ($requestPath === '//') { $requestPath = '/'; }

// Centralized routing: all requests are dispatched via the modular router
if (isset($router) && $router instanceof Router) {
    $router->dispatch($method, $requestPath);
} else {
    // If router is not available, show 404
    include_once __DIR__ . '/controllers/NotFoundController.php';
    $controller = new NotFoundController();
    $controller->index();
}

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("[ERROR] $errstr in $errfile on line $errline\n", 3, LOG_PATH);
    // Optionally show a friendly error page
    if ($errno === E_USER_ERROR) {
        include BASE_PATH . '/htdocs/views/errors/500.php';
        exit;
    }
});

set_exception_handler(function($exception) {
    error_log("[EXCEPTION] " . $exception->getMessage() . "\n", 3, LOG_PATH);
    include BASE_PATH . '/htdocs/views/errors/500.php';
    exit;
});