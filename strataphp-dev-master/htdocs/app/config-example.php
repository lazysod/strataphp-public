<?php
if (!defined('BASE_PATH')) {
  define('BASE_PATH', dirname(__DIR__, 2));
}

// Define the log file path
if (!defined('LOG_PATH')) {
    define('LOG_PATH', BASE_PATH . '/storage/logs/app.log');
}
// Example config file for Strata Framework
require_once __DIR__ . '/theme.php';

// date_default_timezone_set('Europe/London');
return array(
    'site_name' => 'StrataPHP',
    'site_description' => 'A simple PHP framework',
    'admin_email' => getenv('ADMIN_EMAIL') ?: 'your-admin@example.com',
    'form_email' => getenv('FORM_EMAIL') ?: 'your-form@example.com',
    'base_url' => 'http://localhost:8888',
    'dashboard_url' => '/admin/dashboard',
    'logo_small' => '/assets/images/logo_small.png',
    'db' =>
    array(
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'username' => getenv('DB_USERNAME') ?: 'your_db_user',
        'password' => getenv('DB_PASSWORD') ?: 'your_db_password',
        'database' => getenv('DB_DATABASE') ?: 'your_db_name',
    ),
    'mail' =>
    array(
        'host' => getenv('MAIL_HOST') ?: 'smtp.example.com',
        'port' => getenv('MAIL_PORT') ?: 587,
        'username' => getenv('MAIL_USERNAME') ?: 'your-smtp-user@example.com',
        'password' => getenv('MAIL_PASSWORD') ?: 'your_smtp_password',
        'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
        'from_email' => getenv('MAIL_FROM_ADDRESS') ?: 'your-smtp-user@example.com',
        'from_name' => getenv('MAIL_FROM_NAME') ?: 'StrataPHP',
    ),
    'debug' => true,
    'timezone' => 'Europe/London',
    'session_lifetime' => 3600,
    'session_heartbeat_interval' => 300, // seconds (default 5 minutes)
    'version' => '1.0.0',
    'maintenance_mode' => false,
    'allowed_ips' =>
    array(
        0 => '127.0.0.1',
    ),
    'salt' => 'b7f8c2e1a9d4f6a3e2c1b8d7f5e4c3a2',
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
