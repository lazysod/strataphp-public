# Routing

StrataPHP uses modular routing. There is no central `routes.php` file.

## How It Works

1. `public_html/index.php` boots the router
2. The framework loads `routes.php` from each enabled module in `public_html/modules/`
3. Each module defines its own routes using the global `$router` instance

## Adding Routes to a Module

Create `public_html/modules/your-module/routes.php`:

```php
<?php
// public_html/modules/blog/routes.php

global $router;

$router->get('/blog', 'BlogController@index');
$router->get('/blog/{slug}', 'BlogController@show');
$router->post('/blog', 'BlogController@store');