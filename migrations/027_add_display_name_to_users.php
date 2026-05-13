<?php
// Migration: Re-add display_name column to users table for backward compatibility
return [
    'up' => function($db) {
        $col = $db->fetchAll("SHOW COLUMNS FROM users LIKE 'display_name'");
        if (!$col) {
            $db->query("ALTER TABLE users ADD COLUMN display_name VARCHAR(100) DEFAULT NULL AFTER id;");
            echo "Added display_name column to users table\n";
        } else {
            echo "display_name column already exists on users table\n";
        }
    },
    'down' => function($db) {
        $col = $db->fetchAll("SHOW COLUMNS FROM users LIKE 'display_name'");
        if ($col) {
            $db->query("ALTER TABLE users DROP COLUMN display_name;");
            echo "Dropped display_name column from users table\n";
        } else {
            echo "display_name column not present on users table\n";
        }
    }
];
