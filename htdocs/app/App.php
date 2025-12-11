<?php
namespace App;
use App\Logger;
class App
{
    // Dump a variable in a readable format
    public static function dump($var)
    {
        echo '<pre>';
        print_r($var);
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
        $logger = new Logger($config);
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
        }
    }


    // Theme-aware view loader
    public static function loadView($view, $vars = [])
    {
        $configFile = __DIR__ . '/config.php';
        $config = file_exists($configFile) ? include $configFile : [];
        $themePath = $config['theme_path'] ?? '/themes/default';
        $themeView = $_SERVER['DOCUMENT_ROOT'] . $themePath . '/views/' . $view . '.php';
        $coreView = $_SERVER['DOCUMENT_ROOT'] . '/views/' . $view . '.php';

        // Extract variables for use in view
        if (!empty($vars)) {
            extract($vars);
        }

        if (file_exists($themeView)) {
            include $themeView;
        } elseif (file_exists($coreView)) {
            include $coreView;
        } else {
            echo "<div style='color:red'>View not found: $view</div>";
        }
    }
}
