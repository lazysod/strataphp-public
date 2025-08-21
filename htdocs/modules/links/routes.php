<?php
global $router;
if (!empty(App::config('modules')['links']['enabled'])) {
    // Register / as root if links is the default module
    if (!empty(App::config('default_module')) && App::config('default_module') === 'links') {
        $router->get('/', [LinksController::class, 'index']);
    }
    $router->get('/links', [LinksController::class, 'index']);
    $router->get('/links/about', [LinksController::class, 'about']);
}
