<?php
// StrataPHP CMS Module Routes

use App\App;
use App\Modules\Cms\Controllers\CmsController;
use App\Modules\Cms\Controllers\PageController;
use App\Modules\Cms\Controllers\AdminController;
use App\Modules\Cms\Controllers\ImageController;

// Ensure Composer autoloader is loaded for App class
$composerAutoload = __DIR__ . '/../../../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

global $router;

if (!empty(App::config('modules')['cms']['enabled'])) {
    
    // Public page routes - Dynamic routing for CMS pages
    $router->get('/', [PageController::class, 'home']);
    $router->get('/page/{slug}', [PageController::class, 'show']);
    
    // Admin CMS management routes
    $router->get('/admin/cms', [AdminController::class, 'dashboard']);
    $router->get('/admin/cms/dashboard', [AdminController::class, 'dashboard']);
    $router->get('/admin/cms/pages', [AdminController::class, 'pages']);
    $router->get('/admin/cms/pages/create', [AdminController::class, 'createPage']);
    $router->post('/admin/cms/pages/create', [AdminController::class, 'storePage']);
    $router->get('/admin/cms/pages/{id}/edit', [AdminController::class, 'editPage']);
    $router->post('/admin/cms/pages/{id}/edit', [AdminController::class, 'updatePage']);
    $router->post('/admin/cms/pages/{id}/delete', [AdminController::class, 'deletePage']);
    
    // Image upload routes
    $router->post('/admin/cms/upload/image', [ImageController::class, 'upload']);
    $router->get('/admin/cms/media', [AdminController::class, 'mediaLibrary']);
    
    // API routes for headless usage
    $router->get('/api/cms/pages', [CmsController::class, 'apiPages']);
    $router->get('/api/cms/pages/{slug}', [CmsController::class, 'apiPage']);
    
    // Fallback route for dynamic pages (must be last)
    $router->get('/{slug}', [PageController::class, 'dynamicPage']);
}