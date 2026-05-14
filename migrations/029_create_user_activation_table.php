<?php
// Migration: Create user_activation table for account activation flows
return [
    'up' => function($db) {
        $db->query("CREATE TABLE IF NOT EXISTS user_activation (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            activation_key VARCHAR(255) NOT NULL,
            entry_date DATETIME NOT NULL,
            expiry_date DATETIME DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        echo "✅ user_activation table created\n";
    },
    'down' => function($db) {
        $db->query("DROP TABLE IF EXISTS user_activation");
        echo "✅ user_activation table dropped\n";
    }
];
