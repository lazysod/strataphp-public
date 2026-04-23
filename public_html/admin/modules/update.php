<?php
// /admin/modules/update.php
require_once dirname(__DIR__, 2) . '/bootstrap.php';
use App\ModuleUpdater;
$sessionPrefix = $config['session_prefix'] ?? ($config['prefix'] ?? 'framework');
if (empty($_SESSION[$sessionPrefix . 'admin'])) {
    header('Location: /admin/admin_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['module'])) {
    $moduleName = $_POST['module'];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/app/ModuleUpdater.php';
    $coreModules = include $_SERVER['DOCUMENT_ROOT'] . '/app/core_modules.php';
    $coreModulesLower = array_change_key_case($coreModules, CASE_LOWER);
    $modulesDir = $_SERVER['DOCUMENT_ROOT'] . '/modules';
    $moduleKey = strtolower($moduleName);
    if (isset($coreModulesLower[$moduleKey])) {
        $zipUrl = $coreModulesLower[$moduleKey]['zip'];
        $success = \App\ModuleUpdater::updateModule($moduleKey, $zipUrl, $modulesDir);
        if ($success) {
            $_SESSION['module_update_success'] = "$moduleName updated successfully.";
        } else {
            $_SESSION['module_update_error'] = "[UPDATE FAIL] Failed to update $moduleName.";
        }
    } else {
        $_SESSION['module_update_error'] = "[UPDATE FAIL] Module not found in core modules list.";
    }
} else {
    $_SESSION['module_update_error'] = "[UPDATE FAIL] Invalid request.";
}
session_write_close();
header('Location: /admin/modules');
exit;
