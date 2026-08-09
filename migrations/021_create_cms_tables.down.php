<?php
// Down migration: Drop all CMS tables
return function($db) {
    $tables = [
        'cms_menu_items',
        'cms_menus', 
        'cms_categories',
        'cms_posts',
        'cms_pages'
    ];
    
    foreach ($tables as $table) {
        $db->query("DROP TABLE IF EXISTS {$table}");
        echo "✅ Dropped {$table} table\n";
    }
    
    echo "✅ All CMS tables dropped successfully\n";
};