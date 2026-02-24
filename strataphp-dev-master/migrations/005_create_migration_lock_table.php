<?php
// Migration: Create migration_lock table
return function($db) {
    $db->query("CREATE TABLE IF NOT EXISTS migration_lock (
        id INT PRIMARY KEY DEFAULT 1,
        locked TINYINT(1) NOT NULL DEFAULT 0,
        locked_at TIMESTAMP NULL DEFAULT NULL,
        locked_by VARCHAR(255) DEFAULT NULL
    ) ENGINE=InnoDB;");
    // Ensure a single row exists
    $row = $db->fetchAll("SELECT id FROM migration_lock WHERE id = 1");
    if (!$row) {
        $db->query("INSERT INTO migration_lock (id, locked) VALUES (1, 0)");
    }
};
