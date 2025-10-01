<?php
// Ensure $config and $modules are always available
if (!isset($config)) {
    $config = file_exists($_SERVER['DOCUMENT_ROOT'] . '/app/config.php') ? include $_SERVER['DOCUMENT_ROOT'] . '/app/config.php' : [];
}
if (!isset($modules)) {
    $modules = isset($config['modules']) ? $config['modules'] : [];
}

// Admin session check
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/config.php';
$sessionPrefix = $config['session_prefix'] ?? ($config['prefix'] ?? 'framework');
if (!isset($_SESSION[$sessionPrefix . 'admin']) || $_SESSION[$sessionPrefix . 'admin'] < 1) {
    header('Location: /admin/admin_login.php');
    exit;
}

require $_SERVER['DOCUMENT_ROOT'] . '/views/partials/admin_header.php'; ?>
<section class="py-5">
    <div class="container px-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-cubes me-2"></i>Module Manager</h1>
            <div>
                <a href="/admin/module-installer" class="btn btn-primary">
                    <i class="fas fa-download me-2"></i>Install New Module
                </a>
            </div>
        </div>
        
        <!-- Module Installation Info Box -->
        <div class="alert alert-info mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h6 class="mb-1"><i class="fas fa-lightbulb me-2"></i>Need More Modules?</h6>
                    <p class="mb-0">Install modules from GitHub, ZIP files, or generate new ones using our module installer.</p>
                </div>
                <div class="col-md-4 text-md-end mt-2 mt-md-0">
                    <a href="/admin/module-installer" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Get Modules
                    </a>
                </div>
            </div>
        </div>
        
        <form method="post" action="/admin/modules/update">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th><i class="fas fa-cube me-2"></i>Module</th>
                        <th><i class="fas fa-toggle-on me-2"></i>Enabled</th>
                        <th><i class="fas fa-info-circle me-2"></i>Status</th>
                        <th><i class="fas fa-check-circle me-2"></i>Validation</th>
                        <th><i class="fas fa-cog me-2"></i>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($modules)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-box-open fa-2x mb-3"></i>
                                    <h6>No modules installed</h6>
                                    <p class="mb-0">Get started by installing your first module!</p>
                                    <a href="/admin/module-installer" class="btn btn-primary btn-sm mt-2">
                                        <i class="fas fa-download me-1"></i>Install Module
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        // Initialize module validator for validation checks
                        require_once $_SERVER['DOCUMENT_ROOT'] . '/app/Services/ModuleValidator.php';
                        $moduleValidator = class_exists('App\\Services\\ModuleValidator') ? new \App\Services\ModuleValidator() : null;
                        ?>
                        <?php foreach ($modules as $modName => $modInfo): ?>
                            <?php
                            // Get validation status for each module
                            $validationStatus = null;
                            if ($moduleValidator) {
                                // Construct proper module path
                                $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
                                if (empty($documentRoot)) {
                                    // Fallback for CLI context
                                    $modulePath = dirname(__FILE__, 4) . '/modules/' . $modName;
                                } else {
                                    $modulePath = $documentRoot . '/modules/' . $modName;
                                }
                                
                                if (is_dir($modulePath)) {
                                    $validationResults = $moduleValidator->validateModule($modulePath);
                                    $validationStatus = $validationResults['valid'];
                                }
                            }
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($modName); ?></strong>
                                    <?php if ($modName === 'admin' || $modName === 'home'): ?>
                                        <span class="badge bg-secondary ms-2">Core</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($modName === 'admin'): ?>
                                        <input type="checkbox" checked disabled>
                                        <input type="hidden" name="enabled[]" value="admin">
                                        <small class="text-muted ms-2">Required</small>
                                    <?php elseif ($modName === 'home'): ?>
                                        <input type="checkbox" checked disabled>
                                        <input type="hidden" name="enabled[]" value="home">
                                        <small class="text-muted ms-2">Required</small>
                                    <?php else: ?>
                                        <input type="checkbox" name="enabled[]" value="<?php echo htmlspecialchars($modName); ?>" <?php if (!empty($modInfo['enabled'])) echo 'checked'; ?>>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($modInfo['enabled'])): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($modInfo['suitable_as_default'])): ?>
                                        <span class="badge bg-info ms-1">Can be Default</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($validationStatus === true): ?>
                                        <span class="badge bg-success" title="All validations passed">
                                            <i class="fas fa-check-circle"></i> Valid
                                        </span>
                                    <?php elseif ($validationStatus === false): ?>
                                        <span class="badge bg-warning" title="Some validation issues found">
                                            <i class="fas fa-exclamation-triangle"></i> Issues
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary" title="Validation not available">
                                            <i class="fas fa-question-circle"></i> Unknown
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="/admin/modules/details/<?php echo urlencode($modName); ?>" 
                                           class="btn btn-outline-primary btn-sm" 
                                           title="View Details">
                                            <i class="fas fa-info-circle"></i>
                                        </a>
                                        <?php if ($moduleValidator): ?>
                                            <button type="button" 
                                                    class="btn btn-outline-secondary btn-sm" 
                                                    onclick="validateModule('<?php echo htmlspecialchars($modName); ?>')"
                                                    title="Validate Module">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="mb-3">
                <label for="default_module" class="form-label"><strong>Default Module (Root Page)</strong></label>
                <select id="default_module" name="default_module" class="form-select">
                    <?php
                    // Only show modules that are enabled AND suitable_as_default in config
                    foreach ($modules as $modName => $modInfo):
                        if (!empty($modInfo['enabled']) && !empty($modInfo['suitable_as_default'])):
                    ?>
                        <option value="<?php echo htmlspecialchars($modName); ?>" <?php if (isset($config['default_module']) && $config['default_module'] === $modName) echo 'selected'; ?>><?php echo htmlspecialchars($modName); ?></option>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Save Changes</button>
        </form>
        
        <!-- Bulk Actions -->
        <div class="mt-4">
            <div class="row">
                <div class="col-md-6">
                    <a href="/admin/modules/validate-all" class="btn btn-outline-info">
                        <i class="fas fa-check-double me-2"></i>Validate All Modules
                    </a>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="/admin/module-installer" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Install New Module
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function validateModule(moduleName) {
    // Find the validation badge for this module
    const row = event.target.closest('tr');
    const validationCell = row.querySelector('td:nth-child(4)');
    const originalContent = validationCell.innerHTML;
    
    // Show loading state
    validationCell.innerHTML = '<span class="badge bg-secondary"><i class="fas fa-spinner fa-spin"></i> Validating...</span>';
    
    fetch(`/admin/modules/validate/${moduleName}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update validation status based on results
            if (data.valid) {
                validationCell.innerHTML = '<span class="badge bg-success" title="All validations passed"><i class="fas fa-check-circle"></i> Valid</span>';
            } else {
                validationCell.innerHTML = '<span class="badge bg-warning" title="Some validation issues found"><i class="fas fa-exclamation-triangle"></i> Issues</span>';
            }
        } else {
            // Restore original content on error
            validationCell.innerHTML = originalContent;
            alert('Validation failed: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        // Restore original content on error
        validationCell.innerHTML = originalContent;
        alert('Error validating module: ' + error.message);
    });
}
</script>

<?php require $_SERVER['DOCUMENT_ROOT'] . '/views/partials/footer.php'; ?>
