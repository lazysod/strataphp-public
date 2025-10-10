<?php
/**
 * Migration: Create sites table and add site_id to cms_pages
 * Up:   Adds multi-site support for headless CMS/API
 * Down: Removes all multi-site changes
 */

use App\DB;

return [
    'up' => function(DB $db) {
        // 1. Create sites table
        $db->query("CREATE TABLE IF NOT EXISTS sites (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            api_key VARCHAR(64) NOT NULL UNIQUE,
            status ENUM('active','inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // 2. Add site_id to cms_pages
        $db->query("ALTER TABLE cms_pages ADD COLUMN site_id INT NULL AFTER id;");
        // 3. Add FK constraint (optional, safe to skip if issues)
        $db->query("ALTER TABLE cms_pages ADD CONSTRAINT fk_cms_pages_site_id FOREIGN KEY (site_id) REFERENCES sites(id) ON DELETE SET NULL;");
    },
    'down' => function(DB $db) {
        // Remove FK constraint first (if exists)
        $db->query("ALTER TABLE cms_pages DROP FOREIGN KEY IF EXISTS fk_cms_pages_site_id;");
        // Remove site_id column
        $db->query("ALTER TABLE cms_pages DROP COLUMN IF EXISTS site_id;");
        // Drop sites table
        $db->query("DROP TABLE IF EXISTS sites;");
    }
];
