<?php
// /admin/modules/update.php
session_start();
$siteConfig = file_exists($_SERVER['DOCUMENT_ROOT'] . '/app/config.php') ? include $_SERVER['DOCUMENT_ROOT'] . '/app/config.php' : [];
$sessionPrefix = $siteConfig['session_prefix'] ?? ($siteConfig['prefix'] ?? 'framework');
if (empty($_SESSION[$sessionPrefix . 'admin'])) {
    header('Location: /admin/admin_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['module'])) {
    $moduleName = $_POST['module'];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/app/ModuleUpdater.php';
    $coreModules = include $_SERVER['DOCUMENT_ROOT'] . '/app/core_modules.php';
    $modulesDir = $_SERVER['DOCUMENT_ROOT'] . '/modules';
    if (isset($coreModules[$moduleName])) {
        $zipUrl = $coreModules[$moduleName]['zip'];
        $success = ModuleUpdater::updateModule($moduleName, $zipUrl, $modulesDir);
        if ($success) {
            $_SESSION['module_update_success'] = "$moduleName updated successfully.";
        } else {
            $_SESSION['module_update_error'] = "Failed to update $moduleName.";
        }
    } else {
        $_SESSION['module_update_error'] = "Module not found in core modules list.";
    }
} else {
    $_SESSION['module_update_error'] = "Invalid request.";
}
session_write_close();
header('Location: /admin/modules');
exit;
