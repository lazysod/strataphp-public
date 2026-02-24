<?php
// Utility to load theme config JSON from a theme folder
function load_theme_config($themeName = 'default')
{
    $themeConfigPath = __DIR__ . "/../themes/{$themeName}/theme.json";
    if (file_exists($themeConfigPath)) {
        $json = file_get_contents($themeConfigPath);
        $config = json_decode($json, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $config;
        }
    }
    return null;
}
