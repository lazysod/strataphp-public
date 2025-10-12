<?php
// Migration: Create links table
return [
    'up' => function($db) {
        $db->query("CREATE TABLE IF NOT EXISTS links (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            url VARCHAR(255) NOT NULL,
            icon VARCHAR(64) DEFAULT NULL,
            nsfw TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;");
        echo "✅ Created links table\n";
    },
    'down' => function($db) {
        $db->query("DROP TABLE IF EXISTS links");
        echo "✅ Dropped links table\n";
    }
];
