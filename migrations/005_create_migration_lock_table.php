<?php
// Migration: Create migration_lock table
return function($db) {
    $db->query("CREATE TABLE IF NOT EXISTS migration_lock (
        id INT PRIMARY KEY DEFAULT 1,
        locked TINYINT(1) NOT NULL DEFAULT 0,
        locked_at TIMESTAMP NULL DEFAULT NULL,
        locked_by VARCHAR(255) DEFAULT NULL
    ) ENGINE=InnoDB;");

    // Upgrade legacy migration_lock schemas in-place.
    $lockedCol = $db->fetchAll("SHOW COLUMNS FROM migration_lock LIKE 'locked'");
    if (!$lockedCol) {
        $db->query("ALTER TABLE migration_lock ADD COLUMN locked TINYINT(1) NOT NULL DEFAULT 0");
    }
    $lockedAtCol = $db->fetchAll("SHOW COLUMNS FROM migration_lock LIKE 'locked_at'");
    if (!$lockedAtCol) {
        $db->query("ALTER TABLE migration_lock ADD COLUMN locked_at TIMESTAMP NULL DEFAULT NULL");
    }
    $lockedByCol = $db->fetchAll("SHOW COLUMNS FROM migration_lock LIKE 'locked_by'");
    if (!$lockedByCol) {
        $db->query("ALTER TABLE migration_lock ADD COLUMN locked_by VARCHAR(255) DEFAULT NULL");
    }

    // Ensure a single row exists
    $row = $db->fetchAll("SELECT id FROM migration_lock WHERE id = 1");
    if (!$row) {
        $db->query("INSERT INTO migration_lock (id, locked) VALUES (1, 0)");
    }
};
