<?php
// Google Analytics Settings Save Handler
session_start();

$settingsPath = dirname(__DIR__, 3) . '/storage/settings/google_analytics.json';

// Debug: log POST and file write attempts
$debugLog = dirname(__DIR__, 3) . '/storage/logs/ga_settings_debug.log';
file_put_contents($debugLog, "==== POST DATA ====".PHP_EOL.print_r($_POST, true), FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $measurementId = isset($_POST['measurement_id']) ? trim($_POST['measurement_id']) : '';
    file_put_contents($debugLog, "Measurement ID: $measurementId\n", FILE_APPEND);
    if ($measurementId !== '') {
        $result = file_put_contents($settingsPath, json_encode(['measurement_id' => $measurementId], JSON_PRETTY_PRINT));
        file_put_contents($debugLog, "Write result: $result\n", FILE_APPEND);
        $_SESSION['ga_settings_success'] = 'Google Analytics Measurement ID saved.';
    } else {
        $_SESSION['ga_settings_error'] = 'Measurement ID cannot be empty.';
    }
    header('Location: /admin/google-analytics-settings');
    exit;
}
