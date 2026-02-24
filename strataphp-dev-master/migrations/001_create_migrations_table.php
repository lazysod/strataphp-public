<?php
// Migration: Create migrations table
return function($db) {
    $db->query("CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL,
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        applied_by VARCHAR(255) DEFAULT NULL
    ) ENGINE=InnoDB;");
};
