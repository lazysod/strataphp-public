<?php
// Migration: Add applied_by column to migrations table
return function($db) {
    $col = $db->fetchAll("SHOW COLUMNS FROM migrations LIKE 'applied_by'");
    if (!$col) {
        $db->query("ALTER TABLE migrations ADD COLUMN applied_by VARCHAR(255) DEFAULT NULL AFTER applied_at;");
    }
};
