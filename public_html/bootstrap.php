<?php
declare(strict_types=1);

// 1. Composer
require_once dirname(__DIR__) . '/vendor/autoload.php';

// 2. Load .env first so config can see environment overrides
$rootPath = dirname(__DIR__);
if (file_exists($rootPath . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($rootPath);
    $dotenv->load();
}

// 3. Load config
$configFile = __DIR__ . '/app/config.php';
$config = file_exists($configFile) ? require $configFile : [];
// --- BACKWARD COMPAT: remove after upgrade ---
// TODO [remove after 2026-09]: $GLOBALS['config'] kept for legacy code
$GLOBALS['config'] = $config;

// 4. Error handling settings
$isCli = php_sapi_name() === 'cli';
$isProd = ($_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? $config['app_env'] ?? 'production') === 'production';

ini_set('display_errors', $isProd ? '0' : '1');
ini_set('log_errors', '1');
error_reporting(E_ALL);
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? $config['timezone'] ?? 'Europe/London');

// 5. Logger - create ONCE and set as singleton instance
$logger = new App\Logger($config);
App\Logger::setInstance($logger);

// 6. Global error/exception handler -> goes to logger, not screen
set_exception_handler(function (Throwable $e) use ($logger, $isCli, $isProd) {
    $logger->error('Uncaught exception', [
        'message' => $e->getMessage(),
        'file' => $e->getFile() . ':' . $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);

    if ($isCli) {
        fwrite(STDERR, "Fatal error: " . $e->getMessage() . PHP_EOL);
        exit(1);
    }

    if (!$isProd) {
        // Show details in dev
        http_response_code(500);
        echo "<pre>" . htmlspecialchars($e->getMessage()) . "\n" . $e->getTraceAsString() . "</pre>";
        exit;
    }

    http_response_code(500);
    $errorPage = __DIR__ . '/../../views/errors/500.php';
    if (file_exists($errorPage)) {
        require $errorPage;
    } else {
        echo "Internal Server Error";
    }
    exit;
});

set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) return false;
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// 7. Session - ONLY for web, not CLI / API
if (!$isCli && session_status() === PHP_SESSION_NONE) {
    $sessionName = $config['session_name'] ?? 'RATEMYMPSESSID';
    session_name($sessionName);
    session_set_cookie_params([
        'lifetime' => $config['session_lifetime'] ?? 86400,
        'path' => '/',
        'domain' => $config['session_domain'] ?? '',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($config['cookie_secure'] ?? false),
        'httponly' => true,
        'samesite' => $config['session_samesite'] ?? 'Lax'
    ]);
    session_start();

    // CSRF only when we have a session
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

// Return config so index.php can use it without $GLOBALS
return $config;