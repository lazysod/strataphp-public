<?php
// 1. Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// 2. Load environment variables (if using vlucas/phpdotenv)
$dotenvPath = dirname(__DIR__);
if (file_exists($dotenvPath . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($dotenvPath);
    $dotenv->load();
}

// 3. Load global config
$configFile = __DIR__ . '/app/config.php';
$config = file_exists($configFile) ? require $configFile : [];

// 4. Set up error/exception handling (simple example)
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});
set_exception_handler(function ($exception) {
    http_response_code(500);
    echo '<h1>Application Error</h1>';
    echo '<pre>' . htmlspecialchars($exception) . '</pre>';
    // Optionally log error here
});

// 5. Start session (if needed)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 6. Make config available globally (optional)
global $config;
