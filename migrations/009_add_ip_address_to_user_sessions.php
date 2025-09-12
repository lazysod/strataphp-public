<?php
// Migration: Add ip_address column to user_sessions table
return [
    'up' => function ($db) {
        $db->query("ALTER TABLE user_sessions ADD COLUMN ip_address VARCHAR(45) DEFAULT NULL AFTER device_type;");
    },
    'down' => function ($db) {
        $db->query("ALTER TABLE user_sessions DROP COLUMN ip_address;");
    }
];
