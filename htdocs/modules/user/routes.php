<?php
// modules/user/routes.php
// Register user module routes using the router and modules['user'] config

global $router;
if (!empty(App::config('modules')['user'])) {
    // Register / as root if user is the default module
    if (!empty(App::config('default_module')) && App::config('default_module') === 'user') {
        $router->get('/', [UserLoginController::class, 'index']);
    }
    $router->get('/user/login', [UserLoginController::class, 'index']);
    $router->post('/user/login', [UserLoginController::class, 'index']);
    $router->get('/user/register', [UserRegisterController::class, 'index']);
    $router->post('/user/register', [UserRegisterController::class, 'index']);
    $router->get('/user/profile', [UserProfileController::class, 'index']);
    $router->post('/user/profile', [UserProfileController::class, 'index']);
    $router->get('/user/reset-request', [UserResetRequestController::class, 'index']);
    $router->post('/user/reset-request', [UserResetRequestController::class, 'index']);
    $router->get('/user/reset', [UserResetController::class, 'index']);
    $router->post('/user/reset', [UserResetController::class, 'index']);
    $router->get('/user/email-test', [EmailTestController::class, 'index']);
    $router->post('/user/email-test', [EmailTestController::class, 'index']);
    $router->get('/user/activate', ['UserActivateController', 'index']);
    // Add more user routes as needed
}
