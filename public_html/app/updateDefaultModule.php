<?php
/**
 * Update the default_module value in modules.php
 * Usage: include and call updateDefaultModule('cms');
 */
use App\Logger;
function updateDefaultModule($newDefault)
{
    $modulesFile = __DIR__ . '/modules.php';
    $logger = Logger::getInstance();
    $logger->info('Updating default module to: ' . $newDefault);
    if (!file_exists($modulesFile)) {
        $logger->error('modules.php not found');
        throw new \Exception('modules.php not found');
    }
    $modulesConfig = include $modulesFile;
    if (!is_array($modulesConfig)) {
        $logger->error('modules.php did not return an array');
        throw new \Exception('modules.php did not return an array');
    }
    $modulesConfig['default_module'] = $newDefault;
    // Export PHP array to file
    $export = "<?php\nreturn " . var_export($modulesConfig, true) . ";\n";
    $result = file_put_contents($modulesFile, $export);
    $logger->info('file_put_contents result: ' . $result);
    if ($result === false) {
        $logger->error('Failed to write modules.php');
        throw new \Exception('Failed to write modules.php');
    }
    return true;
}
// Example usage:
// updateDefaultModule('cms');
