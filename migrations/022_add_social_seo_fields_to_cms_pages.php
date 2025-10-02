<?php<?php

// Migration: Add social media and SEO fields to cms_pages table// Migration: Add social media and SEO fields to cms_pages table

return function($db) {return function($db) {

    // List of new columns to add    // List of new columns to add

    $newColumns = [    $newColumns = [

        'og_image' => "VARCHAR(500) DEFAULT NULL AFTER meta_description",        'og_image' => "VARCHAR(500) DEFAULT NULL AFTER meta_description",

        'og_type' => "VARCHAR(50) DEFAULT 'article' AFTER og_image",        'og_type' => "VARCHAR(50) DEFAULT 'article' AFTER og_image",

        'twitter_card' => "VARCHAR(50) DEFAULT 'summary_large_image' AFTER og_type",        'twitter_card' => "VARCHAR(50) DEFAULT 'summary_large_image' AFTER og_type",

        'canonical_url' => "VARCHAR(500) DEFAULT NULL AFTER twitter_card",        'canonical_url' => "VARCHAR(500) DEFAULT NULL AFTER twitter_card",

        'noindex' => "TINYINT(1) DEFAULT 0 AFTER canonical_url"        'noindex' => "TINYINT(1) DEFAULT 0 AFTER canonical_url"

    ];    ];

        

    foreach ($newColumns as $columnName => $columnDefinition) {    foreach ($newColumns as $columnName => $columnDefinition) {

        // Check if column already exists        // Check if column already exists

        $columns = $db->fetchAll("SHOW COLUMNS FROM cms_pages LIKE '{$columnName}'");        $columns = $db->fetchAll("SHOW COLUMNS FROM cms_pages LIKE '{$columnName}'");

                

        if (empty($columns)) {        if (empty($columns)) {

            $db->query("ALTER TABLE cms_pages ADD COLUMN {$columnName} {$columnDefinition}");            $db->query("ALTER TABLE cms_pages ADD COLUMN {$columnName} {$columnDefinition}");

            echo "✅ Added {$columnName} column\n";            echo "✅ Added {$columnName} column\n";

        } else {        } else {

            echo "ℹ️  {$columnName} column already exists\n";            echo "ℹ️  {$columnName} column already exists\n";

        }        }

    }    }

        

    echo "✅ Social media and SEO fields migration completed successfully\n";    echo "✅ Social media and SEO fields migration completed successfully\n";

};};
        'og_type' => "VARCHAR(50) DEFAULT 'article' AFTER og_image",
        'twitter_card' => "VARCHAR(50) DEFAULT 'summary_large_image' AFTER og_type",
        'canonical_url' => "VARCHAR(500) DEFAULT NULL AFTER twitter_card",
        'noindex' => "TINYINT(1) DEFAULT 0 AFTER canonical_url"
    ];
    
    $results = [];
    
    foreach ($newColumns as $columnName => $columnDefinition) {
        $columns = $db->fetchAll("SHOW COLUMNS FROM cms_pages LIKE '{$columnName}'");
        
        if (empty($columns)) {
            $db->query("ALTER TABLE cms_pages ADD COLUMN {$columnName} {$columnDefinition}");
            $results[] = "Added {$columnName} column";
        } else {
            $results[] = "{$columnName} column already exists";
        }
    }
    
    return implode(", ", $results);
}

function down($db) {
    $columnsToRemove = [
        'meta_keywords',
        'og_image', 
        'og_type',
        'twitter_card',
        'canonical_url',
        'noindex'
    ];
    
    foreach ($columnsToRemove as $column) {
        $db->query("ALTER TABLE cms_pages DROP COLUMN IF EXISTS {$column}");
    }
    
    return "Removed social media and SEO columns from cms_pages table";
}