<?php
namespace App\Modules\GoogleAnalytics\Controllers;

use App\DB;

class GoogleAnalyticsSettingsController
{
    protected $db;

    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    // Show the settings form
    public function index()
    {
        $stmt = $this->db->getPdo()->query("SELECT * FROM google_analytics_settings ORDER BY id DESC LIMIT 1");
        $row = $stmt->fetch();
        $measurementId = $row['measurement_id'] ?? '';
        include __DIR__ . '/../views/settings.php';
    }

    // Handle form submission
    public function save()
    {
        $measurementId = $_POST['measurement_id'] ?? '';
        if ($measurementId) {
            // Upsert logic: update if exists, else insert
            $pdo = $this->db->getPdo();
            $row = $pdo->query("SELECT id FROM google_analytics_settings LIMIT 1")->fetch();
            if ($row) {
                $stmt = $pdo->prepare("UPDATE google_analytics_settings SET measurement_id = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$measurementId, $row['id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO google_analytics_settings (measurement_id) VALUES (?)");
                $stmt->execute([$measurementId]);
            }
        }
        header('Location: /admin/google-analytics-settings');
        exit;
    }
}
