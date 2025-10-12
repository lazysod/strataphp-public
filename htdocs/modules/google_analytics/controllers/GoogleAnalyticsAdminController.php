<?php
namespace App\Modules\GoogleAnalytics\Controllers;

class GoogleAnalyticsAdminController {
    public function settings() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Admin session check
        $siteConfig = file_exists($_SERVER['DOCUMENT_ROOT'] . '/app/config.php') ? include $_SERVER['DOCUMENT_ROOT'] . '/app/config.php' : [];
        $sessionPrefix = $siteConfig['session_prefix'] ?? ($siteConfig['prefix'] ?? 'framework');
        if (empty($_SESSION[$sessionPrefix . 'admin'])) {
            header('Location: /admin/admin_login.php');
            exit;
        }
        // Load settings
        $settingsPath = dirname(__DIR__, 4) . '/storage/settings/google_analytics.json';
        $measurementId = '';
        if (file_exists($settingsPath)) {
            $data = json_decode(file_get_contents($settingsPath), true);
            $measurementId = $data['measurement_id'] ?? '';
        }
        // Show admin header
        require $_SERVER['DOCUMENT_ROOT'] . '/views/partials/admin_header.php';
        echo '<section class="py-5"><div class="container px-5">';
        echo '<h2><i class="fab fa-google me-2"></i>Google Analytics Settings</h2>';
        if ($measurementId !== '') {
            echo '<div class="mb-3"><strong>Current Measurement ID:</strong> ' . htmlspecialchars($measurementId) . '</div>';
        } else {
            echo '<div class="mb-3 text-muted"><em>No Measurement ID is currently set.</em></div>';
        }
        if (!empty($_SESSION['ga_settings_success'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['ga_settings_success']) . '</div>';
            unset($_SESSION['ga_settings_success']);
        }
        if (!empty($_SESSION['ga_settings_error'])) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['ga_settings_error']) . '</div>';
            unset($_SESSION['ga_settings_error']);
        }
        echo '<form method="post" action="/modules/google_analytics/save_settings.php" class="mb-4">';
        echo '<div class="mb-3">';
        echo '<label for="measurement_id" class="form-label">Measurement ID (e.g., G-XXXXXXXXXX):</label>';
        echo '<input type="text" id="measurement_id" name="measurement_id" class="form-control" value="' . htmlspecialchars($measurementId) . '" required style="max-width:400px;">';
        echo '</div>';
        echo '<button type="submit" class="btn btn-primary">Save</button>';
        echo '</form>';
        echo '</div></section>';
    }
}