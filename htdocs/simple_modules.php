<?php
// Simple working module manager
$config = include $_SERVER['DOCUMENT_ROOT'] . '/app/config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enabled = $_POST['enabled'] ?? [];
    
    // Update each module
    foreach ($config['modules'] as $modName => $modInfo) {
        $config['modules'][$modName]['enabled'] = in_array($modName, $enabled);
    }
    
    // Save config
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/app/config.php', '<?php' . "\nreturn " . var_export($config, true) . ';');
    
    echo "Modules updated successfully!<br><a href='/admin/modules'>Back to Module Manager</a>";
    exit;
}

$modules = $config['modules'] ?? [];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple Module Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Module Manager</h2>
        
        <form method="post">
            <?php foreach ($modules as $modName => $modInfo): ?>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="enabled[]" value="<?= $modName ?>" 
                           <?= $modInfo['enabled'] ? 'checked' : '' ?> id="mod_<?= $modName ?>">
                    <label class="form-check-label" for="mod_<?= $modName ?>">
                        <?= $modName ?> (<?= $modInfo['enabled'] ? 'Enabled' : 'Disabled' ?>)
                    </label>
                </div>
            <?php endforeach; ?>
            
            <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
        </form>
        
        <hr>
        <a href="/nuclear_modules.php" class="btn btn-secondary">Nuclear Option</a>
        <a href="/admin/modules" class="btn btn-info">Back to Full Manager</a>
    </div>
</body>
</html>