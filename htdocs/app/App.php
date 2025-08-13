<?php
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

    // Example: log a message (expand as needed)
    public static function log($message)
    {
        $logFile = __DIR__ . '/../storage/app.log';
        file_put_contents($logFile, date('Y-m-d H:i:s') . ' ' . $message . "\n", FILE_APPEND);
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
            $output = print_r($var, true);
            self::log('[DEBUG] ' . $output);
        }
    }

}
