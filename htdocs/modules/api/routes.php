<?php
// API v1 routes for jokes
// These routes will be loaded by app/start.php if the module is enabled

global $router;

// Error out if no endpoint given
$router->get('/api/v1/jokes/', ['JokesApiController', 'index'], 'api.jokes.index');

// GET /api/v1/jokes/random
$router->get('/api/v1/jokes/random', ['JokesApiController', 'random'], 'api.jokes.random');

// GET /api/v1/jokes/{id}
$router->get('/api/v1/jokes/{id}', ['JokesApiController', 'get'], 'api.jokes.get');
