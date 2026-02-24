<?php
session_start();
$settingsPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/settings/google_analytics.json';
$measurementId = '';
if (file_exists($settingsPath)) {
    $data = json_decode(file_get_contents($settingsPath), true);
    $measurementId = $data['measurement_id'] ?? '';
}
?>
<h2><i class="fab fa-google me-2"></i>Google Analytics Settings</h2>
<?php if (!empty($_SESSION['ga_settings_success'])): ?>
    <div class="alert alert-success"> <?= htmlspecialchars($_SESSION['ga_settings_success']) ?> </div>
    <?php unset($_SESSION['ga_settings_success']); ?>
<?php endif; ?>
<?php if (!empty($_SESSION['ga_settings_error'])): ?>
    <div class="alert alert-danger"> <?= htmlspecialchars($_SESSION['ga_settings_error']) ?> </div>
    <?php unset($_SESSION['ga_settings_error']); ?>
<?php endif; ?>
<form method="post" action="/modules/google-analytics/save_settings.php" class="mb-4">
    <label for="measurement_id" class="form-label">Measurement ID (e.g., G-XXXXXXXXXX):</label><br>
    <input type="text" id="measurement_id" name="measurement_id" class="form-control" value="<?= htmlspecialchars($measurementId) ?>" required style="max-width:400px;">
    <br>
    <button type="submit" class="btn btn-primary">Save</button>
</form>
