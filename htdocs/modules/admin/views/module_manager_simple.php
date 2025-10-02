<?php
// Simple module manager - no duplicate forms
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
                <a href="/nuclear_modules.php" class="btn btn-info">
                    <i class="fas fa-toggle-on me-2"></i>Individual Toggles
                </a>
            </div>
        </div>

        <form method="post" action="/admin/modules">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Module Configuration</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($modules as $modName => $modInfo): ?>
                            <?php 
                            $isEnabled = !empty($modInfo['enabled']);
                            $isCore = in_array($modName, ['admin', 'home']);
                            ?>
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <?php if ($isCore): ?>
                                        <input class="form-check-input" type="checkbox" checked disabled>
                                        <input type="hidden" name="enabled[]" value="<?= htmlspecialchars($modName) ?>">
                                        <label class="form-check-label">
                                            <strong><?= htmlspecialchars($modName) ?></strong>
                                            <span class="badge bg-secondary ms-2">Core</span>
                                        </label>
                                    <?php else: ?>
                                        <input class="form-check-input" type="checkbox" name="enabled[]" value="<?= htmlspecialchars($modName) ?>" <?= $isEnabled ? 'checked' : '' ?> id="mod_<?= $modName ?>">
                                        <label class="form-check-label" for="mod_<?= $modName ?>">
                                            <strong><?= htmlspecialchars($modName) ?></strong>
                                            <span class="badge <?= $isEnabled ? 'bg-success' : 'bg-secondary' ?> ms-2">
                                                <?= $isEnabled ? 'Enabled' : 'Disabled' ?>
                                            </span>
                                        </label>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="mt-3">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</section>

<?php require $_SERVER['DOCUMENT_ROOT'] . '/views/partials/footer.php'; ?>