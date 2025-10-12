<?php
// Down migration: Remove applied_by column from migrations table
return function($db) {
    $columns = $db->fetchAll("SHOW COLUMNS FROM migrations LIKE 'applied_by'");
    
    if (!empty($columns)) {
        $db->query("ALTER TABLE migrations DROP COLUMN applied_by");
        echo "✅ Removed applied_by column from migrations table\n";
    } else {
        echo "ℹ️  applied_by column does not exist\n";
    }
};