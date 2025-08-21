<?php
// Ensure $config and $modules are always available
if (!isset($config)) {
    $config = file_exists($_SERVER['DOCUMENT_ROOT'] . '/app/config.php') ? include $_SERVER['DOCUMENT_ROOT'] . '/app/config.php' : [];
}
if (!isset($modules)) {
    $modules = isset($config['modules']) ? $config['modules'] : [];
}
require $_SERVER['DOCUMENT_ROOT'] . '/views/partials/admin_header.php'; ?>
<section class="py-5">
    <div class="container px-5">
        <h1>Module Manager</h1>
        <form method="post" action="/admin/modules/update">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Module</th>
                        <th>Enabled</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($modules as $modName => $modInfo): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($modName); ?></td>
                            <td>
                                <?php if ($modName === 'home'): ?>
                                    <input type="checkbox" checked disabled>
                                    <input type="hidden" name="enabled[]" value="home">
                                <?php else: ?>
                                    <input type="checkbox" name="enabled[]" value="<?php echo htmlspecialchars($modName); ?>" <?php if (!empty($modInfo['enabled'])) echo 'checked'; ?>>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
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
    </div>
</section>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/views/partials/footer.php'; ?>
