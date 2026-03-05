<?php

require_once dirname(__DIR__, 2) . '/bootstrap.php';
$sessionPrefix = $config['session_prefix'] ?? ($config['prefix'] ?? 'framework');
if (empty($_SESSION[$sessionPrefix . 'admin'])) {
    header('Location: /admin/admin_login.php');
    exit;
}
$modulesConfig = include dirname(__DIR__, 2) . '/app/modules.php';
$modules = $modulesConfig['modules'];
$siteConfig = include dirname(__DIR__, 2) . '/app/config.php';
require dirname(__DIR__, 2) . '/modules/admin/views/module_manager.php';
