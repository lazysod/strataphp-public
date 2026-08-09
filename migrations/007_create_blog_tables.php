<?php
return [
    'up' => function($db) {
        $db->query("CREATE TABLE IF NOT EXISTS blog_posts (
            post_id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            content MEDIUMTEXT NOT NULL,
            author_id INT NOT NULL,
            category_id INT DEFAULT NULL,
            published_at DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            status ENUM('draft','published','archived') DEFAULT 'draft',
            featured_image VARCHAR(255) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        $db->query("CREATE TABLE IF NOT EXISTS blog_categories (
            category_id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL,
            slug VARCHAR(120) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        $db->query("CREATE TABLE IF NOT EXISTS blog_tags (
            tag_id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL,
            slug VARCHAR(120) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        $db->query("CREATE TABLE IF NOT EXISTS blog_post_tags (
            post_id INT NOT NULL,
            tag_id INT NOT NULL,
            PRIMARY KEY (post_id, tag_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    },
    'down' => function($db) {
        $db->query("DROP TABLE IF EXISTS blog_post_tags;");
        $db->query("DROP TABLE IF EXISTS blog_tags;");
        $db->query("DROP TABLE IF EXISTS blog_categories;");
        $db->query("DROP TABLE IF EXISTS blog_posts;");
    }
];
