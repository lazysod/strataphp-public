<?php
if (file_exists(dirname(__DIR__, 2) . '/vendor/autoload.php')) {
    require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
    Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2))->safeLoad();
}
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
// Log levels: 
/*
         Dev: 'log_level' => 'DEBUG' - DEBUG, INFO, WARNING, ERROR
     Staging: 'log_level' => 'INFO' - INFO, WARNING, ERROR
  Production: 'log_level' => 'WARNING' - WARNING, ERROR only
*/
return array(
    'session_expiry_days' => 1,
    'log_level' => 'DEBUG',
    'cookie_secure' => false,
    'api_key' => 'changeme123',
    'site_name' => 'StrataPHP',
    'php_path' => '/usr/bin/php',
    'site_description' => 'A simple PHP framework',
    'admin_email' => $_ENV['ADMIN_EMAIL'] ?? 'your-admin@example.com',
    'form_email' => $_ENV['FORM_EMAIL'] ?? 'your-form@example.com',
    'base_url' => 'http://localhost:8888',
    'dashboard_url' => '/admin/dashboard',
    'logo_small' => '/assets/images/logo_small.png',
    'users' => array(
        'registration_enabled' => true,
        'require_email_verify' => false,
    ),
    'db' => array(
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'username' => $_ENV['DB_USERNAME'] ?? 'root',
        'password' => $_ENV['DB_PASSWORD'] ?? 'root',
        'database' => $_ENV['DB_DATABASE'] ?? 'db_name',
        'port' => $_ENV['DB_PORT'] ?? 3306,
    ),
    'mail' => array(
        'host' => $_ENV['MAIL_HOST'] ?? 'smtp.example.com',
        'port' => $_ENV['MAIL_PORT'] ?? 587,
        'username' => $_ENV['MAIL_USERNAME'] ?? 'your-smtp-user@example.com',
        'password' => $_ENV['MAIL_PASSWORD'] ?? 'your_smtp_password',
        'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
        'from_email' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'your-smtp-user@example.com',
        'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'StrataPHP',
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
