<?php
// Google Analytics Settings Admin View
?>
<h2>Google Analytics Settings</h2>
<form method="post" action="/admin/google-analytics-settings/save">
    <label for="measurement_id">Measurement ID (e.g., G-XXXXXXXXXX):</label><br>
    <input type="text" id="measurement_id" name="measurement_id" value="<?= htmlspecialchars($measurementId) ?>" required style="width:300px;">
    <br><br>
    <button type="submit">Save</button>
</form>
