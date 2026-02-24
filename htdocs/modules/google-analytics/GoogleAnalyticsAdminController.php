<?php
/**
 * Google Analytics Admin Controller
 * Provides a simple admin interface for updating the Measurement ID
 * Includes error handling and documentation comments.
 */
class GoogleAnalyticsAdminController
{
    private $settingsPath;

    public function __construct()
    {
        $this->settingsPath = dirname(__DIR__, 3) . '/storage/settings/google_analytics.json';
    }

    /**
     * Show the settings form for Google Analytics Measurement ID
     */
    public function showSettings()
    {
        session_start();
        $measurementId = '';
        try {
            if (file_exists($this->settingsPath)) {
                $json = @file_get_contents($this->settingsPath);
                if ($json !== false) {
                    $data = json_decode($json, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $measurementId = $data['measurement_id'] ?? '';
                    }
                }
            }
        } catch (Throwable $e) {
            $_SESSION['ga_settings_error'] = 'Error loading settings: ' . htmlspecialchars($e->getMessage());
        }
        include __DIR__ . '/views/analytics-settings.php';
    }

    /**
     * Save the Measurement ID from the settings form
     */
    public function saveSettings()
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $measurementId = trim($_POST['measurement_id'] ?? '');
            if ($measurementId !== '') {
                try {
                    $json = json_encode(['measurement_id' => $measurementId], JSON_PRETTY_PRINT);
                    if ($json === false) {
                        throw new \Exception('Failed to encode JSON.');
                    }
                    $result = @file_put_contents($this->settingsPath, $json);
                    if ($result === false) {
                        throw new \Exception('Failed to write settings file.');
                    }
                    $_SESSION['ga_settings_success'] = 'Measurement ID saved.';
                } catch (\Throwable $e) {
                    $_SESSION['ga_settings_error'] = 'Error saving settings: ' . htmlspecialchars($e->getMessage());
                }
            } else {
                $_SESSION['ga_settings_error'] = 'Measurement ID cannot be empty.';
            }
            header('Location: /admin/google-analytics-settings');
            exit;
        }
    }
}
