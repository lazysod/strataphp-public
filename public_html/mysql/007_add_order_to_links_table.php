<?php
// Migration: Add 'order' column to links table
return [
    'up' => function ($db) {
        $db->query("ALTER TABLE links ADD COLUMN `order` INT NOT NULL DEFAULT 0 AFTER id;");
    },
    'down' => function ($db) {
        $db->query("ALTER TABLE links DROP COLUMN `order`;");
    }
];
