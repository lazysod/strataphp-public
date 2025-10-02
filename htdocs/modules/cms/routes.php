<?php
use App\App;
use App\Modules\Cms\Controllers\CmsController;

// Ensure Composer autoloader is loaded for App class
$composerAutoload = __DIR__ . '/../../../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

// Cms module routes
global $router;

if (!empty(App::config('modules')['cms']['enabled'])) {
    
    // Main routes
    $router->get('/cms', [CmsController::class, 'index']);
    $router->get('/cms/create', [CmsController::class, 'create']);
    $router->post('/cms/create', [CmsController::class, 'store']);
    $router->get('/cms/{{id}}', [CmsController::class, 'show']);
    $router->get('/cms/{{id}}/edit', [CmsController::class, 'edit']);
    $router->post('/cms/{{id}}/edit', [CmsController::class, 'update']);
    $router->post('/cms/{{id}}/delete', [CmsController::class, 'delete']);
    
    // API routes (optional)
    $router->get('/api/cms', [CmsController::class, 'apiIndex']);
    
    // Register as root if this is the default module
    if (!empty(App::config('default_module')) && App::config('default_module') === 'cms') {
        $router->get('/', [CmsController::class, 'index']);
    }
}