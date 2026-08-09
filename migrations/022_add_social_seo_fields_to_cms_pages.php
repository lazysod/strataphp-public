<?php

// Migration: Add social media and SEO fields to cms_pages table

return function($db) {
    // List of new columns to add
    $newColumns = [
        'og_image' => "VARCHAR(500) DEFAULT NULL AFTER meta_description",
        'og_type' => "VARCHAR(50) DEFAULT 'article' AFTER og_image",
        'twitter_card' => "VARCHAR(50) DEFAULT 'summary_large_image' AFTER og_type",
        'canonical_url' => "VARCHAR(500) DEFAULT NULL AFTER twitter_card",
        'noindex' => "TINYINT(1) DEFAULT 0 AFTER canonical_url"
    ];
    
    foreach ($newColumns as $columnName => $columnDefinition) {
        // Check if column already exists
        $columns = $db->fetchAll("SHOW COLUMNS FROM cms_pages LIKE '{$columnName}'");
        
        if (empty($columns)) {
            $db->query("ALTER TABLE cms_pages ADD COLUMN {$columnName} {$columnDefinition}");
            echo "✅ Added {$columnName} column\n";
        } else {
            echo "ℹ️  {$columnName} column already exists\n";
        }
    }
    
    echo "✅ Social media and SEO fields migration completed successfully\n";
};