<?php
// Down migration: Drop migrations table (WARNING: This will remove migration history)
return function($db) {
    $db->query("DROP TABLE IF EXISTS migrations");
    echo "⚠️  Dropped migrations table (migration history lost)\n";
};