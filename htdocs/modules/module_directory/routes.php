<?php
use App\App;
use App\Modules\ModuleDirectory\Controllers\ModuleDirectoryController;

// Ensure Composer autoloader is loaded for App class
$composerAutoload = __DIR__ . '/../../../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

// ModuleDirectory module routes
global $router;

if (!empty(App::config('modules')['module_directory']['enabled'])) {
    
    // Main routes
    $router->get('/module_directory', [ModuleDirectoryController::class, 'index']);
    $router->get('/module_directory/create', [ModuleDirectoryController::class, 'create']);
    $router->post('/module_directory/create', [ModuleDirectoryController::class, 'store']);
    $router->get('/module_directory/{{id}}', [ModuleDirectoryController::class, 'show']);
    $router->get('/module_directory/{{id}}/edit', [ModuleDirectoryController::class, 'edit']);
    $router->post('/module_directory/{{id}}/edit', [ModuleDirectoryController::class, 'update']);
    $router->post('/module_directory/{{id}}/delete', [ModuleDirectoryController::class, 'delete']);
    
    // API routes (optional)
    $router->get('/api/module_directory', [ModuleDirectoryController::class, 'apiIndex']);
    
    // Register as root if this is the default module
    if (!empty(App::config('default_module')) && App::config('default_module') === 'module_directory') {
        $router->get('/', [ModuleDirectoryController::class, 'index']);
    }
}