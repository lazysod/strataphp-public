<?php
use App\App;
// Ensure Composer autoloader is loaded for App class
$composerAutoload = __DIR__ . '/../../../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}
// ...existing code...
use App\Modules\Links\Controllers\LinksController;
global $router;
if (!empty(App::config('modules')['links']['enabled'])) {
    // Register / as root if links is the default module
    if (!empty(App::config('default_module')) && App::config('default_module') === 'links') {
        $router->get('/', [LinksController::class, 'index']);
    }
    $router->get('/links', [LinksController::class, 'index']);
    $router->get('/links/about', [LinksController::class, 'about']);
}
    // Additional context lines can be added here if necessary
