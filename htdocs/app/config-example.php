<?php
if (!defined('BASE_PATH')) {
  define('BASE_PATH', dirname(__DIR__, 2));
}

// Define the log file path
if (!defined('LOG_PATH')) {
    define('LOG_PATH', BASE_PATH . '/storage/logs');
}
// Example config file for Strata Framework
require_once __DIR__ . '/theme.php';

// date_default_timezone_set('Europe/London');
return array(
    'site_name' => 'StrataPHP',
    'site_description' => 'A simple PHP framework',
    'admin_email' => 'your-admin@example.com',
    'form_email' => 'your-form@example.com',
    'base_url' => 'http://localhost:8888',
    'dashboard_url' => '/admin/dashboard',
    'logo_small' => '/assets/images/logo_small.png',
    'db' =>
    array(
        'host' => '127.0.0.1',
        'username' => 'your_db_user',
        'password' => 'your_db_password',
        'database' => 'your_db_name',
    ),
    'mail' =>
    array(
        'host' => 'smtp.example.com',
        'username' => 'your-smtp-user@example.com',
        'password' => 'your_smtp_password',
        'port' => 587,
        'encryption' => 'tls',
        'from_email' => 'your-smtp-user@example.com',
    ),
    'debug' => true,
    'timezone' => 'Europe/London',
    'session_lifetime' => 3600,
    'version' => '1.0.0',
    'maintenance_mode' => false,
    'allowed_ips' =>
    array(
        0 => '127.0.0.1',
    ),
    'base_path' => BASE_PATH,
    'theme' => 'default',
    'theme_path' => '/themes/default',
    'theme_config' =>
    array(
        'name' => 'Default Theme',
        'author' => 'Strata Team',
        'version' => '1.0',
        'logo' => '/assets/images/logo_small.png',
        'favicon' => '/assets/images/favicon.ico',
        'css' => '/css/styles.css',
        'js' => '/js/scripts.js',
    ),
    'logo_url' => '/themes/default/assets/images/logo_small.png',
    'partials_path' => '/views/partials',
    'admin_views_path' => '/views/admin',
    'log_path' => LOG_PATH,
    'js_path' => '/js',
    'assets_path' => '/assets',
    'uploads_path' => '/storage/uploads',
    'prefix' => 'framework',
    'token_expiry' => 3600,
    'modules' =>
    array(
        'home' =>
        array(
            'enabled' => true,
            'suitable_as_default' => true,
        ),
        'user' =>
        array(
            'enabled' => true,
            'suitable_as_default' => false,
        ),
        'contact' =>
        array(
            'enabled' => true,
            'suitable_as_default' => true,
        ),
        'links' =>
        array(
            'enabled' => true,
            'suitable_as_default' => true,
        ),
        'admin' =>
        array(
            'enabled' => true, // Always enabled, cannot be disabled
            'suitable_as_default' => false,
        ),
        'helloworld' =>
        array(
            'enabled' => true,
            'suitable_as_default' => false,
        ),
    ),
    'session_prefix' => 'app_',
    'csrf_token' => true,
    'login_redirect' => '/',
    'system_pages' =>
    array(
        404 => '/views/errors/404.php',
        500 => '/views/errors/500.php',
    ),
    'custom_pages' =>
    array(
        'privacy' => '/views/privacy.php',
        'terms' => '/views/terms.php',
    ),
    'default_module' => 'home',
    // Enable or disable user registration
    'registration_enabled' => true,
);
