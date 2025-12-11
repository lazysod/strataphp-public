<?php
require dirname(__DIR__, 2) . '/vendor/autoload.php';

use App\Token;
use App\Logger;
use App\Modules\Admin\Controllers\ModuleManagerController;
use App\Modules\Admin\Controllers\AdminLinksController;
use App\Modules\Admin\Controllers\UserAdminController;
use App\Controllers\AdminController;
// Load config early for session prefix
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..'); // if start.php is in htdocs/app/
$dotenv->load();
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    include_once __DIR__ . '/../vendor/autoload.php';
    if (class_exists('Dotenv\\Dotenv')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
        error_log('TINYMCE_API_KEY after Dotenv: ' . getenv('TINYMCE_API_KEY'));
    }
}
$config = require __DIR__ . '/config.php';
if (function_exists('opcache_invalidate')) {
    opcache_invalidate(__DIR__ . '/config.php', true);
}
$config = isset($config) ? $config : (file_exists(__DIR__ . '/config.php') ? require __DIR__ . '/config.php' : []);

// Set timezone based on config entry
date_default_timezone_set($config['timezone'] ?? 'Europe/London');

// set session prefix for security
if (!defined('SESSION_PREFIX')) {
    define('PREFIX', $config['session_prefix'] ?? 'app_');
}

// Load Composer autoloader first (if present)
$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    include_once $composerAutoload;
}

// Start session for authentication and tokens
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Set up session prefix for middleware
$sessionPrefix = $config['session_prefix'] ?? 'app_';

// Set up global router
require_once __DIR__ . '/Router.php';
global $router;
if (!isset($router)) {
    $router = new Router();
}

// Register core admin routes
$router->get('/admin/dashboard/profile', [AdminController::class, 'profile']);
$router->get('/admin', [AdminController::class, 'index']);
$router->get('/admin/dashboard', [AdminController::class, 'dashboard']);
$router->get('/admin/reset-password', [AdminController::class, 'resetRequest']);
$router->post('/admin/reset-password', [AdminController::class, 'resetRequest']);
$router->get('/admin/reset-password/confirm', [AdminController::class, 'resetPassword']);
$router->post('/admin/reset-password/confirm', [AdminController::class, 'resetPassword']);
$router->post('/admin/dashboard/profile', [AdminController::class, 'profile']);

// Register admin links routes
if (isset($router) && $router instanceof Router) {
    // Admin authentication middleware
    $router->middleware(function ($request, $next) use ($sessionPrefix) {
        $path = $request['path'];
        $isAdminRoute = strpos($path, '/admin') === 0;
        $isLoginPage = $path === '/admin' || $path === '/admin/reset-password';
        
        if ($isAdminRoute && !$isLoginPage && empty($_SESSION[$sessionPrefix . 'admin'])) {
            // Check if this is an AJAX request or API call
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                     strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            $isJsonRequest = isset($_SERVER['CONTENT_TYPE']) && 
                           strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false;
            $isApiCall = strpos($path, '/admin/modules/') === 0;
            
            header('Location: /admin/admin_login.php');
            exit;
        }
        return $next($request);
    });

    // Admin routes are now loaded from modules/admin/routes.php
}

// Modular system loader: load routes for all enabled modules

$modulesDir = __DIR__ . '/../modules/';
if (is_dir($modulesDir)) {
    // Auto-detect modules
    $foundModules = [];
    foreach (scandir($modulesDir) as $modName) {
        if ($modName === '.' || $modName === '..' || !is_dir($modulesDir . $modName)) continue;
        $foundModules[$modName] = true;
    }
    // Sync with config['modules']
    if (!isset($config['modules'])) {
        $config['modules'] = $foundModules;
    } else {
        foreach ($foundModules as $modName => $enabled) {
            if (!array_key_exists($modName, $config['modules'])) {
                $config['modules'][$modName] = true;
            }
        }
        // Optionally, remove modules from config that no longer exist
        foreach ($config['modules'] as $modName => $enabled) {
            if (!isset($foundModules[$modName])) {
                unset($config['modules'][$modName]);
            }
        }
    }
    // Load routes for enabled modules
    global $router;
    
    // Always load admin routes (core functionality)
    $adminRouteFile = $modulesDir . 'admin/routes.php';
    if (file_exists($adminRouteFile)) {
        include $adminRouteFile;
    }
    
    foreach ($config['modules'] as $modName => $modInfo) {
        // Skip admin since we already loaded it
        if ($modName === 'admin') {
            continue;
        }
        if (is_array($modInfo) && !$modInfo['enabled']) {
            continue;
        }
        $routeFile = $modulesDir . $modName . '/routes.php';
        if (file_exists($routeFile)) {
            include $routeFile;
        }
    }
}

// Dependency Injection Container setup
require_once __DIR__ . '/Container.php';
global $container;
$container = new Container();

// Register Logger service
$container->factory('logger', function ($c) use ($config) {
    return new Logger($config);
});

// Register DB service
$container->factory('db', function ($c) use ($config) {
    return new App\DB($config['db']);
});

// Example: define app constants
define('APP_VERSION', '0.1.0');
// define('BASE_URL', '/htdocs/');

// Auto-generate a CSRF token for every user session
$sessionPrefix = $config['session_prefix'] ?? 'app_';
if (empty($_SESSION[$sessionPrefix . 'csrf_token'])) {
    $_SESSION[$sessionPrefix . 'csrf_token'] = Token::generate();
}

