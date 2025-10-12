<?php
// Google Analytics Settings Admin View
session_start();
$settingsPath = __DIR__ . '/../settings.json';
$measurementId = '';
// Path is constructed using __DIR__ and is not user-controlled, ensuring safety.
if (file_exists($settingsPath)) {
    // Extra safety: ensure settings path is within allowed directory
    if (strpos(realpath(dirname($settingsPath)), realpath(__DIR__ . '/..')) !== 0) {
        throw new Exception('Settings path is outside allowed directory.');
    }
    // Safe: $settingsPath is a fixed path, not user-controlled
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
