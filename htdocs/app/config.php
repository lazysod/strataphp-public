<?php
return array (
  'site_name' => 'StrataPHP',
  'site_description' => 'A simple PHP framework',
  'admin_email' => 'noreply@albaweb.net',
  'form_email' => 'divinorum2001@gmail.com',
  'base_url' => 'http://localhost:8888',
  'dashboard_url' => '/admin/dashboard',
  'logo_small' => '/assets/images/logo_small.png',
  'db' => 
  array (
    'host' => '127.0.0.1',
    'username' => 'root',
    'password' => 'root',
    'database' => '1Framework',
  ),
  'mail' => 
  array (
    'host' => 'mail.albaweb.net',
    'username' => 'noreply@albaweb.net',
    'password' => 'divinorum2001#',
    'port' => 587,
    'encryption' => 'tls',
    'from_email' => 'noreply@albaweb.net',
  ),
  'debug' => true,
  'timezone' => 'Europe/London',
  'session_lifetime' => 3600,
  'version' => '1.0.0',
  'maintenance_mode' => false,
  'allowed_ips' => 
  array (
    0 => '127.0.0.1',
  ),
  'base_path' => '/Users/barry/MAMP_SITES/strata-framework/htdocs/app/../',
  'theme' => 'default',
  'theme_path' => '/themes/default',
  'theme_config' => 
  array (
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
  'log_path' => '/Users/barry/MAMP_SITES/strata-framework/htdocs/app/../storage/logs',
  'js_path' => '/js',
  'assets_path' => '/assets',
  'uploads_path' => '/storage/uploads',
  'prefix' => 'framework',
  'token_expiry' => 3600,
  'modules' => 
  array (
    'home' => 
    array (
      'enabled' => true,
      'suitable_as_default' => true,
    ),
    'user' => 
    array (
      'enabled' => true,
      'suitable_as_default' => false,
    ),
    'contact' => 
    array (
      'enabled' => true,
      'suitable_as_default' => true,
    ),
    'links' => 
    array (
      'enabled' => true,
      'suitable_as_default' => true,
    ),
    'admin' => 
    array (
      'enabled' => true,
      'suitable_as_default' => false,
    ),
    'helloworld' => 
    array (
      'enabled' => true,
      'suitable_as_default' => false,
    ),
  ),
  'session_prefix' => 'app_',
  'csrf_token' => true,
  'login_redirect' => '/',
  'system_pages' => 
  array (
    404 => '/views/errors/404.php',
    500 => '/views/errors/500.php',
  ),
  'custom_pages' => 
  array (
    'privacy' => '/views/privacy.php',
    'terms' => '/views/terms.php',
  ),
  'default_module' => 'home',
);