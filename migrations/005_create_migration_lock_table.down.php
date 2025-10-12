<?php
// Down migration: Drop migration_lock table
return function($db) {
    $db->query("DROP TABLE IF EXISTS migration_lock");
    echo "✅ Dropped migration_lock table\n";
};