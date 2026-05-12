<?php
// 1. Error handling: log only, never echo
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// 2. Composer autoloader
require_once dirname(__DIR__). '/vendor/autoload.php';

// 3. Load .env
$dotenvPath = dirname(__DIR__);
if (file_exists($dotenvPath. '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($dotenvPath);
    $dotenv->load();
}

// 4. Load global config
$configFile = __DIR__. '/app/config.php';
$config = file_exists($configFile) ? require $configFile : [];
global $config;

// 5. SESSION START - before any possible output
$sessionName = $config['session_name']?? 'STRATASESSID';
$sessionSecure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

// SET ALL SESSION PARAMS BEFORE session_start()
session_name($config['session_name'] ?? 'STRATASESSID');
session_set_cookie_params([
    'lifetime' => $config['session_lifetime'] ?? 86400,
    'path' => '/',
    'domain' => '',
    'secure' => $config['cookie_secure'] ?? false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// ADD THIS BLOCK
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 6. CSRF token for all requests
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 7. define logger instance globally
$logger = App\Logger::getInstance($config); // instantiates once