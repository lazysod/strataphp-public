<?php
// Migration: Core tables
return [
    'core' => true,
    'up' => function($db) {
        // Users table
        $db->query("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(50) DEFAULT NULL,
            second_name VARCHAR(50) DEFAULT NULL,
            email VARCHAR(255) NOT NULL,
            pwd VARCHAR(128) NOT NULL,
            security_hash VARCHAR(255) NOT NULL,
            avatar VARCHAR(120) DEFAULT 'public_uploads/blank.png',
            is_admin INT(1) DEFAULT '0',
            sys_admin INT(1) DEFAULT NULL,
            rank INT(1) DEFAULT '0',
            last_access DATETIME DEFAULT NULL,
            active INT(1) DEFAULT '0',
            date DATE DEFAULT NULL,
            dead_switch INT(1) DEFAULT '0'
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
        // login_tracker
        $db->query("CREATE TABLE IF NOT EXISTS login_tracker (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            locked_at TIMESTAMP NULL DEFAULT NULL,
            locked_by VARCHAR(255) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        // rank
        $db->query("CREATE TABLE IF NOT EXISTS rank (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(23) NOT NULL,
            level INT(3) DEFAULT '0',
            admin INT(1) DEFAULT '0'
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
        // migration_lock
        $db->query("CREATE TABLE IF NOT EXISTS migration_lock (
            id INT PRIMARY KEY DEFAULT 1,
            locked TINYINT(1) NOT NULL DEFAULT 0,
            locked_at TIMESTAMP NULL DEFAULT NULL,
            locked_by VARCHAR(255) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
        // reset
        $db->query("CREATE TABLE IF NOT EXISTS reset (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            activation_key VARCHAR(255) NOT NULL,
            entry_date DATETIME NOT NULL,
            expiry_date DATETIME DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        // user_activation
        $db->query("CREATE TABLE IF NOT EXISTS user_activation (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            activation_key VARCHAR(255) NOT NULL,
            entry_date DATETIME NOT NULL,
            expiry_date DATETIME DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        // cookie_login
        $db->query("CREATE TABLE IF NOT EXISTS cookie_login (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            cookie_hash VARCHAR(255) NOT NULL,
            date_added DATE NOT NULL,
            last_updated TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            expiry_date DATE NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        echo "✅ Core tables created\n";
    },
    'down' => function($db) {
        $db->query("DROP TABLE IF EXISTS users");
        $db->query("DROP TABLE IF EXISTS login_tracker");
        $db->query("DROP TABLE IF EXISTS rank");
        $db->query("DROP TABLE IF EXISTS migration_lock");
        $db->query("DROP TABLE IF EXISTS reset");
        $db->query("DROP TABLE IF EXISTS user_activation");
        $db->query("DROP TABLE IF EXISTS cookie_login");
        echo "✅ Core tables dropped\n";
    }
];
