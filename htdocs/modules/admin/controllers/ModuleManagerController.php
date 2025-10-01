<?php
namespace App\Modules\Admin\Controllers;

/**
 * Module Manager Controller
 * 
 * Handles module management interface including enabling/disabling modules
 * and setting the default module
 */
class ModuleManagerController {
    
    /**
     * Display module management interface
     * 
     * @return void
     */
    public function index() {
        try {
            global $config;
            // Use config['modules'] directly for suitability and enabled flags
            $modules = isset($config['modules']) ? $config['modules'] : [];
            include __DIR__ . '/../views/module_manager.php';
        } catch (\Exception $e) {
            error_log("Error loading module manager: " . $e->getMessage());
            http_response_code(500);
            echo '<h1>Error loading module manager</h1>';
        }
    }

    /**
     * Update module configuration (enable/disable modules, set default)
     * 
     * @return void
     */
    public function update() {
        try {
            global $config;
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $enabled = $_POST['enabled'] ?? [];
                foreach ($config['modules'] as $modName => $modInfo) {
                    if (is_array($modInfo)) {
                        if ($modName === 'admin') {
                            $config['modules'][$modName]['enabled'] = true; // Always enabled
                        } else {
                            $config['modules'][$modName]['enabled'] = in_array($modName, $enabled);
                        }
                    }
                }
                // Save default module selection
                if (isset($_POST['default_module']) && in_array($_POST['default_module'], $enabled)) {
                    $config['default_module'] = $_POST['default_module'];
                }
                $configPath = __DIR__ . '/../../../app/config.php';
                $configExport = var_export($config, true);
                
                // Security: Validate config path and backup original
                if (!$this->isSecureConfigPath($configPath)) {
                    throw new \Exception("Invalid configuration path");
                }
                
                if (!$this->secureConfigWrite($configPath, "<?php\nreturn $configExport;")) {
                    throw new \Exception("Failed to write configuration file");
                }
            }
            header('Location: /admin/modules');
            exit;
        } catch (\Exception $e) {
            error_log("Error updating module configuration: " . $e->getMessage());
            $_SESSION['error'] = 'Error updating module configuration. Please try again.';
            header('Location: /admin/modules');
            exit;
        }
    }
    
    /**
     * Security: Validate configuration file path
     */
    private function isSecureConfigPath($configPath)
    {
        $realPath = realpath(dirname($configPath));
        $expectedPath = realpath(__DIR__ . '/../../../app');
        
        if ($realPath === false || $expectedPath === false) {
            return false;
        }
        
        // Ensure we're only writing to the app directory
        return $realPath === $expectedPath && basename($configPath) === 'config.php';
    }
    
    /**
     * Security: Safe configuration file writing with backup
     */
    private function secureConfigWrite($configPath, $content)
    {
        // Validate content is PHP
        if (!str_starts_with($content, '<?php')) {
            return false;
        }
        
        // Create backup
        $backupPath = $configPath . '.backup.' . time();
        if (file_exists($configPath)) {
            copy($configPath, $backupPath);
        }
        
        // Write new config
        $result = file_put_contents($configPath, $content, LOCK_EX);
        
        // Clean up old backups (keep only last 5)
        $this->cleanupConfigBackups(dirname($configPath));
        
        return $result !== false;
    }
    
    /**
     * Clean up old configuration backups
     */
    private function cleanupConfigBackups($configDir)
    {
        $backups = glob($configDir . '/config.php.backup.*');
        if (count($backups) > 5) {
            rsort($backups); // Sort by filename (timestamp)
            $toDelete = array_slice($backups, 5);
            foreach ($toDelete as $backup) {
                unlink($backup);
            }
        }
    }
}
