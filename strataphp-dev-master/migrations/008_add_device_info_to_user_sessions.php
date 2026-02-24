<?php
// Migration: Add device_info column to user_sessions table
return [
    'up' => function ($db) {
        $db->query("ALTER TABLE user_sessions ADD COLUMN device_info VARCHAR(255) DEFAULT NULL AFTER device_type;");
    },
    'down' => function ($db) {
        $db->query("ALTER TABLE user_sessions DROP COLUMN device_info;");
    }
];
