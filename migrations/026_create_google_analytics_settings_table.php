<?php
// Migration: Add Google Analytics settings table (optional)
return [
    'up' => function($db) {
        $db->query("CREATE TABLE IF NOT EXISTS google_analytics_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            measurement_id VARCHAR(32) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        echo "✅ Created google_analytics_settings table\n";
    },
    'down' => function($db) {
        $db->query("DROP TABLE IF EXISTS google_analytics_settings;");
        echo "✅ Dropped google_analytics_settings table\n";
    }
];
