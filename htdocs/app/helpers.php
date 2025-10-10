<?php
/**
 * Global config() helper for StrataPHP
 * Usage: config('key') or config() for full array
 */
function config($key = null) {
    static $config = null;
    if ($config === null) {
        $config = include __DIR__ . '/config.php';
    }
    if ($key === null) return $config;
    return $config[$key] ?? null;
}

// Returns the base URL of the app
function base_url($path = '') {
    $base = config('base_url') ?? '';
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

// Returns the full URL to an asset
function asset($path) {
    return base_url('assets/' . ltrim($path, '/'));
}

// Checks if the current user is an admin (adjust as needed)
function is_admin() {
    $session = $_SESSION ?? [];
    return !empty($session['is_admin']);
}

// Performs a safe HTTP redirect
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

// Returns or generates a CSRF token
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// HTML-escapes a string (shortcut for htmlspecialchars)
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Returns the current user array/object if logged in
function current_user() {
    return $_SESSION['user'] ?? null;
}
