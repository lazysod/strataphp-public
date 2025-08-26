<?php
namespace App;

// api class
class Api
{
    public static function init()
    {
        // Load routes if module is enabled
        $config = require __DIR__ . '/../../app/config.php';
        if (isset($config['modules']['api']) && $config['modules']['api']['enabled']) {
            require __DIR__ . '/routes.php';
        }
    }
}