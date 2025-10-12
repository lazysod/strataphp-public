<?php
/**
 * ModuleAutoDiscovery
 *
 * Scans the modules directory for modules with index.php metadata and returns an array of discovered modules.
 */
class ModuleAutoDiscovery
{
    /**
     * Discover modules in the given directory.
     * @param string $modulesDir
     * @return array
     */
    public static function discover($modulesDir)
    {
        $modules = [];
        if (!is_dir($modulesDir)) {
            error_log('[ModuleAutoDiscovery] Directory not found: ' . $modulesDir);
            return $modules;
        }
        foreach (scandir($modulesDir) as $moduleName) {
            if ($moduleName === '.' || $moduleName === '..') continue;
            $modulePath = $modulesDir . DIRECTORY_SEPARATOR . $moduleName;
            $indexFile = $modulePath . DIRECTORY_SEPARATOR . 'index.php';
            if (is_dir($modulePath) && file_exists($indexFile)) {
                $meta = include $indexFile;
                if (!is_array($meta)) {
                    error_log('[ModuleAutoDiscovery] Invalid metadata for module: ' . $moduleName);
                    continue;
                }
                $modules[$moduleName] = [
                    'enabled' => isset($meta['enabled']) ? (bool)$meta['enabled'] : false,
                    'suitable_as_default' => isset($meta['suitable_as_default']) ? (bool)$meta['suitable_as_default'] : false,
                ];
            }
        }
        if (empty($modules)) {
            error_log('[ModuleAutoDiscovery] No modules discovered in: ' . $modulesDir);
        }
        return $modules;
    $modules = [];
        if (!is_dir($modulesDir)) return $modules;
        foreach (scandir($modulesDir) as $moduleName) {
            if ($moduleName === '.' || $moduleName === '..') continue;
            $modulePath = $modulesDir . DIRECTORY_SEPARATOR . $moduleName;
            $indexFile = $modulePath . DIRECTORY_SEPARATOR . 'index.php';
            if (is_dir($modulePath) && file_exists($indexFile)) {
                $meta = include $indexFile;
                // Debug: log the raw metadata for each module
                file_put_contents(
                    __DIR__ . '/../../storage/logs/module_autodiscovery_debug.log',
                    date('c') . " [$moduleName] " . var_export($meta, true) . "\n",
                    FILE_APPEND
                );
                if (is_array($meta)) {
                    $modules[$moduleName] = [
                        'enabled' => isset($meta['enabled']) ? (bool)$meta['enabled'] : false,
                        'suitable_as_default' => isset($meta['suitable_as_default']) ? (bool)$meta['suitable_as_default'] : false,
                    ];
                }
            }
        }
        return $modules;
    }
}
