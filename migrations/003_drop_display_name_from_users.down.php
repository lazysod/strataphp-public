<?php
// Down migration: Re-add display_name column to users table
return function($db) {
    $db->query("ALTER TABLE users ADD COLUMN display_name VARCHAR(100) DEFAULT NULL AFTER id;");
};
