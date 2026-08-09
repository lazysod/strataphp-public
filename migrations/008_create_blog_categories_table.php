<?php
// Migration: Create blog categories table and add category_id to posts
return function($db) {
    // Create categories table
    $db->query("CREATE TABLE IF NOT EXISTS blog_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL,
        description TEXT DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Add category_id to blog_posts if not exists
    $columns = $db->fetchAll("SHOW COLUMNS FROM blog_posts LIKE 'category_id'");
    if (empty($columns)) {
        $db->query("ALTER TABLE blog_posts ADD COLUMN category_id INT DEFAULT NULL AFTER id;");
        $db->query("ALTER TABLE blog_posts ADD FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL;");
    }
};
