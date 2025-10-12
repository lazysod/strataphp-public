<?php
namespace App\Modules\GoogleAnalytics\Controllers;

/**
 * Google Analytics Settings Controller (File-based)
 *
 * Handles the display and saving of Google Analytics Measurement ID using a JSON config file.
 * No database required.
 *
 * @author StrataPHP Framework
 */
class GoogleAnalyticsSettingsController
{
    /**
     * Show the settings form
     *
     * @return void
     */
    public function index()
    {
        try {
            $settingsPath = dirname(__DIR__, 3) . '/storage/settings/google_analytics.json';
            $measurementId = '';
            if (file_exists($settingsPath)) {
                $data = json_decode(file_get_contents($settingsPath), true);
                $measurementId = $data['measurement_id'] ?? '';
            }
            include __DIR__ . '/../views/settings.php';
        } catch (\Throwable $e) {
            echo '<div class="alert alert-danger">An error occurred: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }

    /**
     * Handle form submission to save Measurement ID
     *
     * @return void
     */
    public function save()
    {
        try {
            $measurementId = $_POST['measurement_id'] ?? '';
            $settingsPath = dirname(__DIR__, 3) . '/storage/settings/google_analytics.json';
            if ($measurementId) {
                $json = json_encode(['measurement_id' => $measurementId], JSON_PRETTY_PRINT);
                if ($json === false) {
                    throw new \Exception('Failed to encode JSON.');
                }
                $result = @file_put_contents($settingsPath, $json);
                if ($result === false) {
                    throw new \Exception('Failed to write settings file.');
                }
            }
            header('Location: /admin/google-analytics-settings');
            exit;
        } catch (\Throwable $e) {
            echo '<div class="alert alert-danger">An error occurred: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}
