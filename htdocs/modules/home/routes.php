<?php
// Home module routes (core route for /)
global $router;
if (!empty(App::config('default_module')) && App::config('default_module') === 'home') {
    $router->get(
        '/', function () {
            if (class_exists('App')) {
                // App::log('DEBUG: / route dispatched');
            }
            if (class_exists('HomeController')) {
                // App::log('DEBUG: HomeController found');
                $controller = new HomeController();
                if (method_exists($controller, 'index')) {
                    // App::log('DEBUG: HomeController::index() exists');
                    return $controller->index();
                } else {
                    // App::log('DEBUG: HomeController::index() missing');
                }
            } else {
                // App::log('DEBUG: HomeController missing');
            }
            echo 'Home route error: controller or method missing.';
        }
    );
}
$router->get('/about', [AboutController::class, 'index']);
$router->get('/admin', [AdminController::class, 'index']);
$router->post('/admin', [AdminController::class, 'index']);
$router->get('/admin/dashboard', [AdminController::class, 'dashboard']);
