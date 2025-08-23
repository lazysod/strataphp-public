<?php
require_once __DIR__ . '/../app/config.php';
session_start();
$sessionPrefix = $config['session_prefix'] ?? ($config['prefix'] ?? 'framework');
if (isset($_SESSION[$sessionPrefix . 'admin']) && $_SESSION[$sessionPrefix . 'admin'] > 0) {
    header('Location: /admin/dashboard');
    exit;
}
header('Location: /admin/admin_login.php');
exit;
