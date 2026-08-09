<?php
// Migration: Add order column to links table
return [
    'up' => function($db) {
        $column = $db->fetch("SHOW COLUMNS FROM links LIKE 'order'");
        if (!$column) {
            $db->query("ALTER TABLE links ADD COLUMN `order` INT NOT NULL DEFAULT 0 AFTER nsfw");
            $db->query("UPDATE links SET `order` = id WHERE `order` = 0");
            echo "✅ Added order column to links table\n";
        } else {
            echo "ℹ️ links.order already exists\n";
        }
    },
    'down' => function($db) {
        $column = $db->fetch("SHOW COLUMNS FROM links LIKE 'order'");
        if ($column) {
            $db->query("ALTER TABLE links DROP COLUMN `order`");
            echo "✅ Dropped order column from links table\n";
        } else {
            echo "ℹ️ links.order does not exist\n";
        }
    }
];
