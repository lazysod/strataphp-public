<?php
// Contact module routes
global $router;
if (!empty(App::config('modules')['contact'])) {
    // Register / as root if contact is the default module
    if (!empty(App::config('default_module')) && App::config('default_module') === 'contact') {
        $router->get('/', [ContactFormController::class, 'index']);
    }
    $router->get('/contact', [ContactFormController::class, 'index']);
    $router->post('/contact', [ContactFormController::class, 'submit']);
}
