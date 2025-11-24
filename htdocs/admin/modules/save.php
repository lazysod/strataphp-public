<?php
// /admin/modules/save.php
session_start();
$siteConfig = file_exists($_SERVER['DOCUMENT_ROOT'] . '/app/config.php') ? include $_SERVER['DOCUMENT_ROOT'] . '/app/config.php' : [];
$sessionPrefix = $siteConfig['session_prefix'] ?? ($siteConfig['prefix'] ?? 'framework');
if (empty($_SESSION[$sessionPrefix . 'admin'])) {
    header('Location: /admin/admin_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enabled_present'])) {
    $modulesConfigPath = $_SERVER['DOCUMENT_ROOT'] . '/app/modules.php';
    $modulesConfig = include $modulesConfigPath;
    $enabledModules = $_POST['enabled'] ?? [];
    foreach ($modulesConfig['modules'] as $modName => &$modInfo) {
        if ($modName === 'admin' || $modName === 'home') {
            $modInfo['enabled'] = true;
        } else {
            $modInfo['enabled'] = in_array($modName, $enabledModules);
        }
    }
    // Save updated config
    $export = var_export($modulesConfig, true);
    file_put_contents($modulesConfigPath, "<?php\nreturn $export;\n");
    $_SESSION['module_update_success'] = 'Module settings saved.';
} else {
    $_SESSION['module_update_error'] = 'Invalid request.';
}
session_write_close();
header('Location: /admin/modules');
exit;
