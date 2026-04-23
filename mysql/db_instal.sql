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

  -- Table structure for `migration_lock`
DROP TABLE IF EXISTS `rank`;
CREATE TABLE `rank` (
    `id` int(255) NOT NULL AUTO_INCREMENT,
    `user_id` int(255) NOT NULL,
    `title` varchar(23) NOT NULL,
    `level` int(3) DEFAULT '0',
    `admin` int(1) DEFAULT '0',
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
DROP TABLE IF EXISTS `migration_lock`;
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
  `display_name` varchar(100) DEFAULT NULL,
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
  KEY `device_id` (`device_id`),
  KEY `session_token` (`session_token`)
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
