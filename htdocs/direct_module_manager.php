<?php
// Direct module manager - bypass routing issues
session_start();

// Load config
$config = require __DIR__ . '/app/config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_modules') {
    try {
        require_once __DIR__ . '/modules/admin/controllers/ModuleManagerController.php';
        
        $enabled = $_POST['enabled'] ?? [];
        
        // Get all modules from filesystem scan
        $allModules = [];
        $modulesPath = $_SERVER['DOCUMENT_ROOT'] . '/modules';
        
        if (is_dir($modulesPath)) {
            $moduleDirectories = array_filter(glob($modulesPath . '/*'), 'is_dir');
            
            foreach ($moduleDirectories as $moduleDir) {
                $moduleName = basename($moduleDir);
                $allModules[$moduleName] = [
                    'enabled' => isset($config['modules'][$moduleName]['enabled']) ? $config['modules'][$moduleName]['enabled'] : false,
                    'suitable_as_default' => isset($config['modules'][$moduleName]['suitable_as_default']) ? $config['modules'][$moduleName]['suitable_as_default'] : false
                ];
            }
        }
        
        // Initialize modules array if not exists
        if (!isset($config['modules'])) {
            $config['modules'] = [];
        }
        
        // Update all modules
        foreach ($allModules as $modName => $modInfo) {
            if ($modName === 'admin') {
                $config['modules'][$modName]['enabled'] = true;
            } else {
                $config['modules'][$modName]['enabled'] = in_array($modName, $enabled);
            }
            
            if (!isset($config['modules'][$modName]['suitable_as_default'])) {
                $config['modules'][$modName]['suitable_as_default'] = false;
            }
        }
        
        // Save default module selection
        if (isset($_POST['default_module']) && in_array($_POST['default_module'], $enabled)) {
            $config['default_module'] = $_POST['default_module'];
        }
        
        // Write config file
        $configPath = __DIR__ . '/app/config.php';
        $configExport = var_export($config, true);
        
        if (file_put_contents($configPath, "<?php\nreturn $configExport;")) {
            $success = "Module configuration updated successfully!";
        } else {
            $error = "Failed to write configuration file.";
        }
        
    } catch (Exception $e) {
        $error = "Error updating module configuration: " . $e->getMessage();
    }
}

// Get current modules for display
$modules = [];
$modulesPath = $_SERVER['DOCUMENT_ROOT'] . '/modules';

if (is_dir($modulesPath)) {
    $moduleDirectories = array_filter(glob($modulesPath . '/*'), 'is_dir');
    
    foreach ($moduleDirectories as $moduleDir) {
        $moduleName = basename($moduleDir);
        $modules[$moduleName] = [
            'enabled' => isset($config['modules'][$moduleName]['enabled']) ? $config['modules'][$moduleName]['enabled'] : false,
            'suitable_as_default' => isset($config['modules'][$moduleName]['suitable_as_default']) ? $config['modules'][$moduleName]['suitable_as_default'] : false
        ];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Module Manager - Direct Access</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>Module Manager</h1>
    <p><small>Direct access version - bypassing routing issues</small></p>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="post">
        <input type="hidden" name="action" value="update_modules">
        
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Module</th>
                        <th>Status</th>
                        <th>Enabled</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($modules as $modName => $modInfo): ?>
                        <?php 
                        $isEnabled = !empty($modInfo['enabled']);
                        $isCore = in_array($modName, ['admin', 'home']);
                        ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($modName) ?></strong>
                                <?php if ($isCore): ?>
                                    <span class="badge bg-secondary ms-2">Core</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($isEnabled): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($isCore): ?>
                                    <input type="checkbox" checked disabled>
                                    <input type="hidden" name="enabled[]" value="<?= htmlspecialchars($modName) ?>">
                                    <small class="text-muted ms-2">Required</small>
                                <?php else: ?>
                                    <input type="checkbox" name="enabled[]" value="<?= htmlspecialchars($modName) ?>" <?= $isEnabled ? 'checked' : '' ?>>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mb-3">
            <label for="default_module" class="form-label">Default Module:</label>
            <select name="default_module" id="default_module" class="form-select">
                <?php foreach ($modules as $modName => $modInfo): ?>
                    <?php if (!empty($modInfo['enabled']) && !empty($modInfo['suitable_as_default'])): ?>
                        <option value="<?= htmlspecialchars($modName) ?>" <?= (isset($config['default_module']) && $config['default_module'] === $modName) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($modName) ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" class="btn btn-success">Save Changes</button>
        <a href="/admin" class="btn btn-secondary">Back to Admin</a>
    </form>
</div>
</body>
</html>