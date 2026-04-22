<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 2));
}
if (!defined('LOG_PATH')) {
    define('LOG_PATH', BASE_PATH . '/storage/logs/app.log');
}
require_once __DIR__ . '/App/ThemeConfigException.php';
$modulesConfig = include __DIR__ . '/modules.php';
//  get badwords from config and add to modulesConfig for use in module metadata

$badWords = file_exists(__DIR__ . '/bad_words.php') ? include __DIR__ . '/bad_words.php' : [];
$modulesConfig['bad_words'] = $badWords;
return array(
    'api_key' => 'changeme123',
    'site_name' => 'StrataPHP',
    'php_path' => '/usr/bin/php',
    'site_description' => 'A simple PHP framework',
    'admin_email' => getenv('ADMIN_EMAIL') ?: 'your-admin@example.com',
    'form_email' => getenv('FORM_EMAIL') ?: 'your-form@example.com',
    'base_url' => 'http://localhost:8888',
    'dashboard_url' => '/admin/dashboard',
    'logo_small' => '/assets/images/logo_small.png',
    'db' => array(
        'host' => getenv('DB_HOST') ?: 'localhost',
        'username' => getenv('DB_USERNAME') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: 'root',
        'database' => getenv('DB_DATABASE') ?: 'db_name',
    ),
    'mail' => array(
        'host' => getenv('MAIL_HOST') ?: 'smtp.example.com',
        'port' => getenv('MAIL_PORT') ?: 587,
        'username' => getenv('MAIL_USERNAME') ?: 'your-smtp-user@example.com',
        'password' => getenv('MAIL_PASSWORD') ?: 'your_smtp_password',
        'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
        'from_email' => getenv('MAIL_FROM_ADDRESS') ?: 'your-smtp-user@example.com',
        'from_name' => getenv('MAIL_FROM_NAME') ?: 'StrataPHP',
    ),
    'version' => '1.0.0',
    'debug' => true,
    'timezone' => 'Europe/London',
    'session_lifetime' => 3600,
    'session_heartbeat_interval' => 300,
    'maintenance_mode' => false,
    'allowed_ips' => array('127.0.0.1'),
    'salt' => 'b7f8c2e1a9d42a3e2c1b8d7f5e4c3a2',
    'base_path' => BASE_PATH,
    'theme' => 'default',
    'theme_path' => '/themes/default',
    'theme_config' => array(
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
    'modules' => $modulesConfig['modules'],
    'session_prefix' => 'app_',
    'csrf_token' => true,
    'login_redirect' => '/',
    'system_pages' => array(
        404 => '/views/errors/404.php',
        500 => '/views/errors/500.php',
    ),
    'custom_pages' => array(
        'privacy' => '/views/privacy.php',
        'terms' => '/views/terms.php',
    ),
    'default_module' => $modulesConfig['default_module'],
    'update_url' => '',
    'registration_enabled' => true,
    'tinymceApiKey' => $_ENV['TINYMCE_API_KEY'] ?? '',
    'bad_words' => $modulesConfig['bad_words'] ?? [],
    'cms_upload_dir' => __DIR__ . '/../storage/uploads/cms/',
    'media_upload_dir' => __DIR__ . '/../storage/uploads/media/',
);
