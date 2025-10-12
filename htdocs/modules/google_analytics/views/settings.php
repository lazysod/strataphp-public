<?php
// Google Analytics Settings Admin View
session_start();
$settingsPath = __DIR__ . '/../settings.json';
$measurementId = '';
if (file_exists($settingsPath)) {
    $data = json_decode(file_get_contents($settingsPath), true);
    $measurementId = $data['measurement_id'] ?? '';
}
?>
<h2>Google Analytics Settings</h2>
<?php if (!empty($_SESSION['ga_settings_success'])): ?>
    <div style="color: green;"> <?= htmlspecialchars($_SESSION['ga_settings_success']) ?> </div>
    <?php unset($_SESSION['ga_settings_success']); ?>
<?php endif; ?>
<?php if (!empty($_SESSION['ga_settings_error'])): ?>
    <div style="color: red;"> <?= htmlspecialchars($_SESSION['ga_settings_error']) ?> </div>
    <?php unset($_SESSION['ga_settings_error']); ?>
<?php endif; ?>
<form method="post" action="/modules/google_analytics/save_settings.php">
    <label for="measurement_id">Measurement ID (e.g., G-XXXXXXXXXX):</label><br>
    <input type="text" id="measurement_id" name="measurement_id" value="<?= htmlspecialchars($measurementId) ?>" required style="width:300px;">
    <br><br>
    <button type="submit">Save</button>
</form>
