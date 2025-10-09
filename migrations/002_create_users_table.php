<?php
// Migration: Create users table
return [
    'up' => function($db) {
        $db->query("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            display_name VARCHAR(100) DEFAULT NULL,
            first_name VARCHAR(100) NOT NULL,
            second_name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            pwd VARCHAR(255) NOT NULL,
            is_admin TINYINT(1) DEFAULT 0,
            active TINYINT(1) DEFAULT 1,
            security_hash VARCHAR(64) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;");
        echo "✅ Created users table\n";
    },
    'down' => function($db) {
        $db->query("DROP TABLE IF EXISTS users");
        echo "✅ Dropped users table\n";
    }
];
