<?php
// htdocs/index.php - new entry point for the fresh framework
require_once __DIR__ . '/app/start.php'; // config, autoload, etc.
$config = require __DIR__ . '/app/config.php';
$db = new DB($config);
$user = new User($db, $config);
$user->cookie_check();
$requestPath = '/' . trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
if ($requestPath === '//') { $requestPath = '/';
}
$method = $_SERVER['REQUEST_METHOD'];

global $router;
ob_start();
$dispatched = false;
if (isset($router) && $router instanceof Router) {
    // Try to dispatch using the modular router
    // If not found, Router will output 404 and exit
    $router->dispatch($method, $requestPath);
    $dispatched = true;
}
ob_end_flush();

// Fallback: legacy controller/action loader (should only run if router not used)
if (!$dispatched) {
    $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $segments = explode('/', $uri);
    $controllerName = !empty($segments[0]) ? ucfirst($segments[0]) . 'Controller' : 'HomeController';
    $action = isset($segments[1]) ? $segments[1] : 'index';
    $controllerFile = __DIR__ . '/controllers/' . $controllerName . '.php';
    if (file_exists($controllerFile)) {
        include_once $controllerFile;
        $controller = new $controllerName();
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            include_once __DIR__ . '/controllers/NotFoundController.php';
            $controller = new NotFoundController();
            $controller->index();
        }
    } else {
        include_once __DIR__ . '/controllers/NotFoundController.php';
        $controller = new NotFoundController();
        $controller->index();
    }
}
