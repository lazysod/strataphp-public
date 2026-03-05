<?php
// OAuth Clients module routes
// Not in use yet, but this is where we would define routes for OAuth client management (e.g. listing providers, adding new ones, etc.)
global $router;
$router->get('/oauth/authorize', [\App\Modules\OAuthClients\Controllers\OAuthAuthorizeController::class, 'index']);
$router->post('/oauth/authorize', [\App\Modules\OAuthClients\Controllers\OAuthAuthorizeController::class, 'handleAuthorization']);