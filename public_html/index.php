<?php
require_once __DIR__. '/bootstrap.php';
use App\DB;
use App\User;
use App\Router;
use App\Logger;
use App\SessionManager;
$config = $GLOBALS['config'];

// Initialize the router
$router = new Router();

// Load all enabled module route files
if (!empty($config['modules']) && is_array($config['modules'])) {
    foreach ($config['modules'] as $moduleName => $moduleInfo) {
        if (!empty($moduleInfo['enabled'])) {
            $routeFile = __DIR__. "/modules/{$moduleName}/routes.php";
            if (file_exists($routeFile)) {
                require_once $routeFile;
            }
        }
    }
}

// Ensure session prefix and PREFIX constant are set before use
if (!defined('SESSION_PREFIX')) {
    $config = require __DIR__. '/app/config.php';
    define('SESSION_PREFIX', $config['session_prefix']?? 'app_');
}
$sessionPrefix = defined('SESSION_PREFIX')? SESSION_PREFIX : 'app_';
if (!defined('PREFIX')) {
    define('PREFIX', $sessionPrefix);
}

// Global error and exception handlers
set_error_handler(function($errno, $errstr, $errfile, $errline) use ($config) {
    if ($config['debug']) {
        echo '<div style="margin:2em auto;max-width:600px;padding:1em;border:1px solid #e74c3c;background:#fff3f3;color:#c0392b;font-family:sans-serif;text-align:center;">';
        echo '<strong>Oops! An error occurred:</strong><br>';
        echo htmlspecialchars($errstr). '<br><small>('. htmlspecialchars($errfile). ' line '. $errline. ')</small>';
        echo '</div>';
    } else {
        include $config['system_pages'][500];
    }
    exit;
});

set_exception_handler(function($exception) use ($config) {
    $logger = Logger::getInstance();
    $logger->error('[EXCEPTION] ' . $exception->getMessage() . ' in ' . $exception->getFile() . ' on line ' . $exception->getLine());
    if ($config['debug']) {
        echo '<div style="margin:2em auto;max-width:600px;padding:1em;border:1px solid #e74c3c;background:#fff3f3;color:#c0392b;font-family:sans-serif;text-align:center;">';
        echo '<strong>Oops! An unexpected error occurred:</strong><br>';
        echo htmlspecialchars($exception->getMessage()). '<br><small>('. htmlspecialchars($exception->getFile()). ' line '. $exception->getLine(). ')</small>';
        echo '</div>';
    } else {
        include $config['system_pages'][500];
    }
    exit;
});

$db = new DB($config);
$user = new App\User($db, $config);
$sessionManager = new SessionManager($db, $config);

$method = $_SERVER['REQUEST_METHOD'];
$requestPath = '/'. trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
if ($requestPath === '//') { $requestPath = '/'; }

$isAdminRoute = (strpos($requestPath, '/admin') === 0);
$adminPublicAuthRoutes = [
    '/admin',
    '/admin/admin_login.php',
    '/admin/reset-request',
    '/admin/reset-password',
];
$isAdminPublicAuthRoute = in_array($requestPath, $adminPublicAuthRoutes, true);

$sessionPrefix = $config['session_prefix'] ?? 'app_';
$currentUserId = $_SESSION[$sessionPrefix . 'user_id'] ?? null;

// Validate the DB-backed session on each authenticated request.
if (!empty($currentUserId)) {
    $validatedUserId = $sessionManager->validateSession();
    if (!$validatedUserId || (int)$validatedUserId !== (int)$currentUserId) {
        $sessionManager->destroySession();
        if ($isAdminRoute) {
            header('Location: /admin/admin_login.php?session=revoked');
        } else {
            header('Location: /user/login?session=revoked');
        }
        exit;
    }
}

// Only enforce auth for /admin routes
if ($isAdminRoute && !$isAdminPublicAuthRoute) {
    if (empty($_SESSION[$sessionPrefix. 'user_id'])) {
        header('Location: /admin/admin_login.php?redirect='. urlencode($requestPath));
        exit;
    }

    if (empty($_SESSION[$sessionPrefix. 'admin']) || $_SESSION[$sessionPrefix. 'admin'] < 1) {
        header('Location: /');
        exit;
    }
}

// Centralized routing: all requests are dispatched via the modular router
if (isset($router) && $router instanceof Router) {
    $router->dispatch($method, $requestPath);
} else {
    // If router is not available, show 404
    include_once __DIR__. '/controllers/NotFoundController.php';
    $controller = new App\Controllers\NotFoundController();
    $controller->index();
}