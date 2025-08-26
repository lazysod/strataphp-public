<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/app/start.php'; // config, autoload, etc.
use App\DB;
use App\User;
$config = require __DIR__ . '/app/config.php';

// Global error and exception handlers

set_error_handler(function($errno, $errstr, $errfile, $errline) use ($config) {
    error_log("[ERROR] $errstr in $errfile on line $errline", 3, $config['log_path']);
    if ($config['debug']) {
        echo '<div style="margin:2em auto;max-width:600px;padding:1em;border:1px solid #e74c3c;background:#fff3f3;color:#c0392b;font-family:sans-serif;text-align:center;">';
        echo '<strong>Oops! An error occurred:</strong><br>';
        echo htmlspecialchars($errstr) . '<br><small>(' . htmlspecialchars($errfile) . ' line ' . $errline . ')</small>';
    // ...existing code...
        echo '</div>';
    } else {
        include $config['system_pages'][500];
    }
    exit;
});

set_exception_handler(function($exception) use ($config) {
    error_log("[EXCEPTION] " . $exception->getMessage(), 3, $config['log_path']);
    if ($config['debug']) {
        echo '<div style="margin:2em auto;max-width:600px;padding:1em;border:1px solid #e74c3c;background:#fff3f3;color:#c0392b;font-family:sans-serif;text-align:center;">';
        echo '<strong>Oops! An unexpected error occurred:</strong><br>';
        echo htmlspecialchars($exception->getMessage()) . '<br><small>(' . htmlspecialchars($exception->getFile()) . ' line ' . $exception->getLine() . ')</small>';
    // ...existing code...
        echo '</div>';
    } else {
        include $config['system_pages'][500];
    }
    exit;
});

$db = new DB($config);
$user = new App\User($db, $config);
$user->cookie_check();
$requestPath = '/' . trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
if ($requestPath === '//') { $requestPath = '/';
}
$method = $_SERVER['REQUEST_METHOD'];

global $router;
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
    error_log("[ERROR] $errstr in $errfile on line $errline", 3, LOG_PATH);
    // Optionally show a friendly error page
    if ($errno === E_USER_ERROR) {
        include BASE_PATH . '/htdocs/views/errors/500.php';
        exit;
    }
});

set_exception_handler(function($exception) {
    error_log("[EXCEPTION] " . $exception->getMessage(), 3, LOG_PATH);
    include BASE_PATH . '/htdocs/views/errors/500.php';
    exit;
});




