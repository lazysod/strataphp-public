<?php
/**
 * Migration: Create user_sessions table for device/session management
 */

return [
    'up' => function($db) {
        $db->query("CREATE TABLE IF NOT EXISTS user_sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            device_id VARCHAR(128) NOT NULL,
            device_type VARCHAR(32) DEFAULT NULL,
            session_token VARCHAR(128) NOT NULL,
            revoked TINYINT(1) DEFAULT 0,
            last_seen DATETIME NOT NULL,
            created_at DATETIME NOT NULL,
            INDEX(user_id),
            INDEX(device_id),
            INDEX(session_token)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    },
    'down' => function($db) {
        $db->exec("DROP TABLE IF EXISTS user_sessions;");
    }
];
