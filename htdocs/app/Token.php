<?php
namespace App;
class Token
{
    // Generate a secure random token
    public static function generate($length = 32)
    {
        return bin2hex(random_bytes($length));
    }

    // Store a token in session (for CSRF or login)
    public static function set($key, $token)
    {
        $_SESSION[$key] = $token;
    }

    // Get a token from session
    public static function get($key)
    {
        return $_SESSION[$key] ?? null;
    }

    // Verify a token from session
    public static function verify($key, $token)
    {
        return isset($_SESSION[$key]) && hash_equals($_SESSION[$key], $token);
    }

    // Remove a token from session
    public static function remove($key)
    {
        unset($_SESSION[$key]);
    }
}
