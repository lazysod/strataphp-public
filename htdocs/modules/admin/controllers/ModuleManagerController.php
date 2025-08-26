<?php
namespace App\Modules\Admin\Controllers;
// Admin UI for module management
class ModuleManagerController {
    public function index() {
        global $config;
        // Use config['modules'] directly for suitability and enabled flags
        $modules = isset($config['modules']) ? $config['modules'] : [];
        include __DIR__ . '/../views/module_manager.php';
    }

    public function update() {
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
            file_put_contents($configPath, "<?php\nreturn $configExport;");
        }
        header('Location: /admin/modules');
        exit;
    }
}
