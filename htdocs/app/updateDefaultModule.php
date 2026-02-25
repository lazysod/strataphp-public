<?php
/**
 * Update the default_module value in modules.php
 * Usage: include and call updateDefaultModule('cms');
 */
function updateDefaultModule($newDefault) {
    $modulesFile = __DIR__ . '/modules.php';
    error_log('updateDefaultModule: modulesFile=' . $modulesFile);
    if (!file_exists($modulesFile)) {
        error_log('modules.php not found');
        throw new \Exception('modules.php not found');
    }
    $modulesConfig = include $modulesFile;
    if (!is_array($modulesConfig)) {
        error_log('modules.php did not return an array');
        throw new \Exception('modules.php did not return an array');
    }
    $modulesConfig['default_module'] = $newDefault;
    // Export PHP array to file
    $export = "<?php\nreturn " . var_export($modulesConfig, true) . ";\n";
    $result = file_put_contents($modulesFile, $export);
    error_log('file_put_contents result: ' . $result);
    if ($result === false) {
        error_log('Failed to write modules.php');
        throw new \Exception('Failed to write modules.php');
    }
    return true;
}
// Example usage:
// updateDefaultModule('cms');
