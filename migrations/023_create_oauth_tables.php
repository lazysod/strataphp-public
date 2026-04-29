<?php
/**
 * Migration: Create OAuth2 tables for clients, codes, and tokens
 */

return [
    'up' => function($db) {
        $db->query("CREATE TABLE IF NOT EXISTS oauth_clients (
            id INT AUTO_INCREMENT PRIMARY KEY,
            client_id VARCHAR(80) NOT NULL UNIQUE,
            client_secret VARCHAR(128) NOT NULL,
            redirect_uri VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            status TINYINT(1) DEFAULT 1,
            created_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $db->query("CREATE TABLE IF NOT EXISTS oauth_codes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(128) NOT NULL UNIQUE,
            client_id VARCHAR(80) NOT NULL,
            user_id INT NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL,
            KEY client_id (client_id),
            KEY user_id (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $db->query("CREATE TABLE IF NOT EXISTS oauth_tokens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            access_token VARCHAR(128) NOT NULL UNIQUE,
            client_id VARCHAR(80) NOT NULL,
            user_id INT NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL,
            revoked TINYINT(1) DEFAULT 0,
            KEY client_id (client_id),
            KEY user_id (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    },
    'down' => function($db) {
        $db->exec("DROP TABLE IF EXISTS oauth_tokens;");
        $db->exec("DROP TABLE IF EXISTS oauth_codes;");
        $db->exec("DROP TABLE IF EXISTS oauth_clients;");
    }
];
