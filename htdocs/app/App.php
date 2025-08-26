<?php
namespace App;

class App
{
    // Dump a variable in a readable format
    public static function dump($var)
    {
        echo '<pre>';
    // ...existing code...
        echo '</pre>';
    }

    // Example: get config value (expand as needed)
    public static function config($key, $default = null)
    {
        static $config = null;
        if ($config === null) {
            $configFile = __DIR__ . '/config.php';
            $config = file_exists($configFile) ? include $configFile : [];
        }
        return $config[$key] ?? $default;
    }

    // Unified log method using Logger class
    public static function log($message, $level = 'INFO', $context = [])
    {
        $configFile = __DIR__ . '/config.php';
        $config = file_exists($configFile) ? include $configFile : [];
        // Use Logger from app/class/Logger.php
        require_once __DIR__ . '/class/Logger.php';
        $logger = new \Logger($config);
        $logger->log($level, $message, $context);
    }

    public static function stripSpaces($string)
    {
        // Remove all spaces from the string
        return preg_replace('/\s+/', '', $string);
    }

        // Enable debug mode and optionally dump a variable
    public static function debug($var = null)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        if (func_num_args() > 0) {
            self::dump($var);
            // Log debug output
            // ...existing code...
        }
    }

}
