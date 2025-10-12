<?php
use App\App;
use App\Modules\GoogleAnalytics\Controllers\GoogleAnalyticsController;

// Ensure Composer autoloader is loaded for App class
$composerAutoload = __DIR__ . '/../../../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

// GoogleAnalytics module routes
global $router;

if (!empty(App::config('modules')['google_analytics']['enabled'])) {
    
    // Main routes
    $router->get('/google_analytics', [GoogleAnalyticsController::class, 'index']);
    $router->get('/google_analytics/create', [GoogleAnalyticsController::class, 'create']);
    $router->post('/google_analytics/create', [GoogleAnalyticsController::class, 'store']);
    $router->get('/google_analytics/{{id}}', [GoogleAnalyticsController::class, 'show']);
    $router->get('/google_analytics/{{id}}/edit', [GoogleAnalyticsController::class, 'edit']);
    $router->post('/google_analytics/{{id}}/edit', [GoogleAnalyticsController::class, 'update']);
    $router->post('/google_analytics/{{id}}/delete', [GoogleAnalyticsController::class, 'delete']);
    
    // API routes (optional)
    $router->get('/api/google_analytics', [GoogleAnalyticsController::class, 'apiIndex']);
    
    // Register as root if this is the default module
    if (!empty(App::config('default_module')) && App::config('default_module') === 'google_analytics') {
        $router->get('/', [GoogleAnalyticsController::class, 'index']);
    }
}