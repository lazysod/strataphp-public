<?php
// Down migration: Remove social media and SEO fields from cms_pages table
return function($db) {
    $columnsToRemove = [
        'noindex',
        'canonical_url',
        'twitter_card',
        'og_type',
        'og_image'
    ];
    
    foreach ($columnsToRemove as $columnName) {
        // Check if column exists before dropping
        $columns = $db->fetchAll("SHOW COLUMNS FROM cms_pages LIKE '{$columnName}'");
        
        if (!empty($columns)) {
            $db->query("ALTER TABLE cms_pages DROP COLUMN {$columnName}");
            echo "✅ Removed {$columnName} column\n";
        } else {
            echo "ℹ️  {$columnName} column does not exist\n";
        }
    }
    
    echo "✅ Social media and SEO fields rollback completed successfully\n";
};