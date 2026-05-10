--
-- REQUIRED TABLES (Core framework, must not remove)
--
-- Generation Time: Aug 21, 2025 at 09:43 AM



-- Table structure for table `login_tracker`
DROP TABLE IF EXISTS `login_tracker`;
CREATE TABLE `login_tracker` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user_id` int(255) NOT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `locked_at` timestamp NULL DEFAULT NULL,
  `locked_by` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `rank`;
CREATE TABLE `rank` (
    `id` int(255) NOT NULL AUTO_INCREMENT,
    `user_id` int(255) NOT NULL,
    `title` varchar(23) NOT NULL,
    `level` int(3) DEFAULT '0',
    `admin` int(1) DEFAULT '0',
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `migration_lock` (
    `id` int(255) NOT NULL AUTO_INCREMENT,
    `user_id` int(255) NOT NULL,
    `key` varchar(255) NOT NULL,
    `created_date` timestamp NULL DEFAULT NULL,
    `expiry_date` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) DEFAULT NULL,
  `second_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `pwd` varchar(128) NOT NULL,
  `security_hash` varchar(255) NOT NULL,
  `avatar` varchar(120) DEFAULT 'public_uploads/blank.png',
  `is_admin` int(1) DEFAULT '0',
  `sys_admin` int(1) DEFAULT NULL,
  `rank` int(1) DEFAULT '0',
  `last_access` datetime DEFAULT NULL,
  `active` int(1) DEFAULT '0',
  `date` date DEFAULT NULL,
  `dead_switch` int(1) DEFAULT '0', 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
DROP TABLE IF EXISTS `reset`;
CREATE TABLE `reset` (
    `id` int(255) NOT NULL AUTO_INCREMENT,
    `user_id` int(255) NOT NULL,
    `activation_key` varchar(255) NOT NULL,
    `entry_date` datetime NOT NULL,
    `expiry_date` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `user_sessions`;
CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `device_id` varchar(128) NOT NULL,
  `device_type` varchar(32) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `device_info` varchar(255) DEFAULT NULL,
  `session_token` varchar(128) NOT NULL,
  `revoked` tinyint(1) DEFAULT '0',
  `last_seen` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `session_token` (`session_token`),
  KEY `session_lookup` (`session_token`, `device_id`, `revoked`, `expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `links`
DROP TABLE IF EXISTS `links`;
CREATE TABLE `links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `order` int(11) NOT NULL DEFAULT '0',
  `nsfw` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO `links` (`id`, `title`, `url`, `icon`, `created_at`, `order`, `nsfw`) VALUES
(1, 'Strata PHP Home Page', 'https://www.strataphp.org', 'fas fa-link', '2025-08-13 08:09:46', 2, 1),
(3, 'B.Smith Home Page!', 'https://barrysmith.dev', 'fas fa-link', '2025-08-13 08:25:14', 1, 0),
(4, 'Lazy Links 2.0', 'https://lazylinks.co.uk', 'fas fa-link', '2025-08-13 08:26:48', 4, 0);

-- Table structure for `google_analytics_settings`
DROP TABLE IF EXISTS `google_analytics_settings`;
CREATE TABLE `google_analytics_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `measurement_id` varchar(32) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `cookie_login`;
CREATE TABLE `cookie_login` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user_id` int(255) NOT NULL,
  `cookie_hash` varchar(255) NOT NULL,
  `date_added` date NOT NULL,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expiry_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- OPTIONAL MODULE: OAuth2 (for API authentication, can be removed if not needed)
--

-- OAuth2 tables
DROP TABLE IF EXISTS `oauth_tokens`;
CREATE TABLE `oauth_tokens` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `access_token` VARCHAR(128) NOT NULL UNIQUE,
  `client_id` VARCHAR(80) NOT NULL,
  `user_id` INT NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `created_at` DATETIME NOT NULL,
  `revoked` TINYINT(1) DEFAULT 0,
  KEY `client_id` (`client_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `oauth_codes`;
CREATE TABLE `oauth_codes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(128) NOT NULL UNIQUE,
  `client_id` VARCHAR(80) NOT NULL,
  `user_id` INT NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `created_at` DATETIME NOT NULL,
  `code_challenge` VARCHAR(128) NULL,
  `code_challenge_method` VARCHAR(10) DEFAULT 'plain',
  `scope` VARCHAR(255) DEFAULT 'basic',
  KEY `client_id` (`client_id`),
  KEY `user_id` (`user_id`),
  KEY `code_lookup` (`code`, `expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `oauth_clients`;
CREATE TABLE `oauth_clients` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `client_id` VARCHAR(80) NOT NULL UNIQUE,
  `client_secret` VARCHAR(128) NOT NULL,
  `redirect_uri` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `status` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `is_public` TINYINT(1) DEFAULT 0,
  `allowed_scopes` VARCHAR(255) DEFAULT 'basic'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- OPTIONAL MODULE: Sites and CMS (for content management, can be removed if not needed)
--

-- Sites table and CMS changes
DROP TABLE IF EXISTS `sites`;
CREATE TABLE `sites` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `api_key` VARCHAR(64) NOT NULL UNIQUE,
  `status` ENUM('active','inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- CMS Pages table
DROP TABLE IF EXISTS `cms_pages`;
CREATE TABLE `cms_pages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `content` LONGTEXT,
  `excerpt` TEXT,
  `meta_title` VARCHAR(255),
  `meta_description` TEXT,
  `meta_keywords` VARCHAR(255),
  `status` ENUM('draft', 'published', 'private') DEFAULT 'draft',
  `template` VARCHAR(100) DEFAULT 'default',
  `featured_image` VARCHAR(255),
  `author_id` INT,
  `parent_id` INT DEFAULT NULL,
  `menu_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `published_at` TIMESTAMP NULL,
  `og_image` VARCHAR(500) DEFAULT NULL,
  `og_type` VARCHAR(50) DEFAULT 'article',
  `twitter_card` VARCHAR(50) DEFAULT 'summary_large_image',
  `canonical_url` VARCHAR(500) DEFAULT NULL,
  `noindex` TINYINT(1) DEFAULT 0,
  `site_id` INT NULL,
  INDEX `idx_slug` (`slug`),
  INDEX `idx_status` (`status`),
  INDEX `idx_author` (`author_id`),
  INDEX `idx_parent` (`parent_id`),
  CONSTRAINT `fk_cms_pages_site_id` FOREIGN KEY (`site_id`) REFERENCES `sites`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- CMS Menus table
DROP TABLE IF EXISTS `cms_menus`;
CREATE TABLE `cms_menus` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- CMS Menu Items table
DROP TABLE IF EXISTS `cms_menu_items`;
CREATE TABLE `cms_menu_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `menu_id` INT NOT NULL,
  `label` VARCHAR(255) NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `parent_id` INT DEFAULT NULL,
  `order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`menu_id`) REFERENCES `cms_menus`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`parent_id`) REFERENCES `cms_menu_items`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add applied_by to migrations table
ALTER TABLE `migrations` ADD COLUMN IF NOT EXISTS `applied_by` VARCHAR(255) DEFAULT NULL AFTER `applied_at`;

--
-- REQUIRED TABLE: Migrations (tracks applied migrations, must not remove)
--
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `migration` VARCHAR(255) NOT NULL,
  `applied_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `applied_by` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB;

--
-- OPTIONAL: Migration tracking enhancement (can be removed if not needed)
--

--
-- NOTE: Blog tables and modules are not included in this install file. Add them only if you enable the blog feature.
--
