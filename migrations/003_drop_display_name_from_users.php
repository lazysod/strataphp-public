<?php
// Migration: Drop display_name column from users table
return function($db) {
    // Check if column exists before dropping
    $col = $db->fetchAll("SHOW COLUMNS FROM users LIKE 'display_name'");
    if ($col) {
        $db->query("ALTER TABLE users DROP COLUMN display_name;");
    }
};