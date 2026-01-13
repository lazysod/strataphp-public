<?php
session_start();
$siteConfig = file_exists($_SERVER['DOCUMENT_ROOT'] . '/app/config.php') ? include $_SERVER['DOCUMENT_ROOT'] . '/app/config.php' : [];
$sessionPrefix = $siteConfig['session_prefix'] ?? ($siteConfig['prefix'] ?? 'framework');
if (empty($_SESSION[$sessionPrefix . 'admin'])) {
    header('Location: /admin/admin_login.php');
    exit;
}
$modulesConfig = include $_SERVER['DOCUMENT_ROOT'] . '/app/modules.php';
$modules = $modulesConfig['modules'];
require $_SERVER['DOCUMENT_ROOT'] . '/modules/admin/views/module_manager.php';
