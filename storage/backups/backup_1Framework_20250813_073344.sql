-- MySQL dump 10.13  Distrib 9.4.0, for macos15.4 (arm64)
--
-- Host: 127.0.0.1    Database: 1Framework
-- ------------------------------------------------------
-- Server version	5.7.39

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ban_ip`
--

DROP TABLE IF EXISTS `ban_ip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ban_ip` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `address` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ban_ip`
--

LOCK TABLES `ban_ip` WRITE;
/*!40000 ALTER TABLE `ban_ip` DISABLE KEYS */;
/*!40000 ALTER TABLE `ban_ip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cookie_login`
--

DROP TABLE IF EXISTS `cookie_login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cookie_login` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user_id` int(255) NOT NULL,
  `cookie_hash` varchar(255) NOT NULL,
  `date_added` date NOT NULL,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expiry_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cookie_login`
--

LOCK TABLES `cookie_login` WRITE;
/*!40000 ALTER TABLE `cookie_login` DISABLE KEYS */;
/*!40000 ALTER TABLE `cookie_login` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `error_log`
--

DROP TABLE IF EXISTS `error_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `error_log` (
  `log_id` int(255) NOT NULL AUTO_INCREMENT,
  `event_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `event_type` enum('System Error','Failed Login','Admin Login Attempt','Account Suspended','IP Blocked') NOT NULL,
  `event_description` text,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `error_log`
--

LOCK TABLES `error_log` WRITE;
/*!40000 ALTER TABLE `error_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `error_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_replies`
--

DROP TABLE IF EXISTS `forum_replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forum_replies` (
  `reply_id` int(255) NOT NULL AUTO_INCREMENT,
  `topic_id` int(255) NOT NULL,
  `author_id` int(255) NOT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `is_helpful` int(11) DEFAULT NULL,
  `is_visible` int(1) DEFAULT '1',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`reply_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_replies`
--

LOCK TABLES `forum_replies` WRITE;
/*!40000 ALTER TABLE `forum_replies` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_replies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ip_log`
--

DROP TABLE IF EXISTS `ip_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ip_log` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user_id` int(255) NOT NULL,
  `ip_address` varchar(120) NOT NULL,
  `date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ip_log`
--

LOCK TABLES `ip_log` WRITE;
/*!40000 ALTER TABLE `ip_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `ip_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_sessions`
--

DROP TABLE IF EXISTS `login_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_sessions` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user_id` int(255) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `session_set` datetime NOT NULL,
  `session_expire` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_sessions`
--

LOCK TABLES `login_sessions` WRITE;
/*!40000 ALTER TABLE `login_sessions` DISABLE KEYS */;
INSERT INTO `login_sessions` VALUES (1,1,'fvm7pgmv95bbjc0hmogkldfbnu','2024-10-06 21:35:38','2024-10-06 22:35:38');
/*!40000 ALTER TABLE `login_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_tracker`
--

DROP TABLE IF EXISTS `login_tracker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_tracker` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user_id` int(255) NOT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_tracker`
--

LOCK TABLES `login_tracker` WRITE;
/*!40000 ALTER TABLE `login_tracker` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_tracker` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migration_lock`
--

DROP TABLE IF EXISTS `migration_lock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migration_lock` (
  `id` int(11) NOT NULL DEFAULT '1',
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `locked_at` timestamp NULL DEFAULT NULL,
  `locked_by` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration_lock`
--

LOCK TABLES `migration_lock` WRITE;
/*!40000 ALTER TABLE `migration_lock` DISABLE KEYS */;
INSERT INTO `migration_lock` VALUES (1,0,NULL,NULL);
/*!40000 ALTER TABLE `migration_lock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `applied_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'002_create_users_table.php','2025-08-12 15:02:34','2025-08-12 16:51:16'),(3,'003_drop_display_name_from_users.php','2025-08-12 15:58:07','barry@Caledonia-Digital.local'),(4,'004_add_applied_by_to_migrations.php','2025-08-12 15:58:46','barry@Caledonia-Digital.local'),(5,'005_create_migration_lock_table.php','2025-08-12 16:00:23','barry@Caledonia-Digital.local');
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `private_messages`
--

DROP TABLE IF EXISTS `private_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `private_messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `is_system` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `read_at` timestamp NULL DEFAULT NULL,
  `deleted_by_sender` tinyint(1) DEFAULT '0',
  `deleted_by_recipient` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`message_id`),
  KEY `idx_recipient` (`recipient_id`),
  KEY `idx_sender` (`sender_id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `private_messages`
--

LOCK TABLES `private_messages` WRITE;
/*!40000 ALTER TABLE `private_messages` DISABLE KEYS */;
INSERT INTO `private_messages` VALUES (1,5,1,'test','hello world',1,1,'2025-07-18 09:05:53','2025-07-18 11:17:19',0,1),(2,1,5,'test 1?','This is a message Test',1,0,'2025-07-18 12:58:10','2025-07-20 16:34:30',0,0),(3,1,5,'test message 2','this is test message 2\r\n\r\nit\'s ok?',1,0,'2025-07-18 12:59:41','2025-07-20 16:36:01',0,0),(4,1,5,'test','hello',1,0,'2025-07-18 13:26:19','2025-07-20 16:36:04',0,0),(5,5,1,'Re: test 1?','this is a reply???!@?',1,0,'2025-07-20 16:35:55','2025-07-20 16:36:20',0,0);
/*!40000 ALTER TABLE `private_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rank`
--

DROP TABLE IF EXISTS `rank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rank` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user_id` int(255) NOT NULL,
  `title` varchar(23) NOT NULL,
  `level` int(3) DEFAULT '0',
  `admin` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rank`
--

LOCK TABLES `rank` WRITE;
/*!40000 ALTER TABLE `rank` DISABLE KEYS */;
INSERT INTO `rank` VALUES (1,1,'Admin',100,1);
/*!40000 ALTER TABLE `rank` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reset`
--

DROP TABLE IF EXISTS `reset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reset` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user_id` int(255) NOT NULL,
  `key` varchar(255) NOT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reset`
--

LOCK TABLES `reset` WRITE;
/*!40000 ALTER TABLE `reset` DISABLE KEYS */;
INSERT INTO `reset` VALUES (89,1,'69d03fdcf148936790a2f80a2c33f3faf6410cbf4dfd5c19fd716972996c462b',NULL,'2025-08-09 22:03:32'),(90,1,'bc0ffc10e072eed02120a768b58d3d2781ce709278af8e03a0b055212bda684f',NULL,'2025-08-09 22:05:12'),(91,1,'390911f3a582074ad960b1e4311650a14e9351e650e61c4f1c701e938430433d',NULL,'2025-08-09 22:05:28'),(92,1,'7f4989a0a0e32af0282af2612f77aa60adfa32375f98a133c204ef493934b9c7',NULL,'2025-08-09 22:06:14'),(93,1,'d282e5adba5fd7ab5b12af264707fa1fc2ddbe9b40a7cff3d53750e0c3efc43b',NULL,'2025-08-10 11:43:14');
/*!40000 ALTER TABLE `reset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_activation`
--

DROP TABLE IF EXISTS `user_activation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_activation` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user_id` int(255) NOT NULL,
  `activation_key` varchar(255) NOT NULL,
  `entry_date` datetime NOT NULL,
  `expiry_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_activation`
--

LOCK TABLES `user_activation` WRITE;
/*!40000 ALTER TABLE `user_activation` DISABLE KEYS */;
INSERT INTO `user_activation` VALUES (14,16,'7987e1e3fbdc24d97af6892318b2dde2b180aa64c90f231c76d60d08bb2f453b','2025-08-10 12:02:01','2025-08-11 12:02:01'),(15,17,'b2fa7e97d856c4e659ff68f4f1cbd1af9856a47926a92bb4b320fe130abd4ed8','2025-08-10 12:05:46','2025-08-11 12:05:46');
/*!40000 ALTER TABLE `user_activation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) DEFAULT NULL,
  `second_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `pwd` varchar(128) NOT NULL,
  `security_hash` varchar(255) NOT NULL,
  `avatar` varchar(120) DEFAULT 'dist/img/avatar.png',
  `is_admin` int(1) DEFAULT '0',
  `sys_admin` int(1) DEFAULT NULL,
  `rank` int(1) DEFAULT '0',
  `last_access` datetime DEFAULT NULL,
  `active` int(1) DEFAULT '0',
  `date` date DEFAULT NULL,
  `dead_switch` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Barry','Smith','divinorum2001@gmail.com','$2y$12$EmV7wCqatiCqntZw6lFk2Omv52bigQeZBjDKvvgK/J2oP1HEEQBtm','eadee2397845d6a762b373238264fe14','/storage/uploads/users/1/smile.png',0,NULL,1,'2025-08-13 08:15:16',1,'2025-08-10',0),(2,'Dave','test','divinorum2001+test@gmail.com','$2y$12$ZI/iHsbKMWOnCdl2PKB1MuZ8IBbs7kUrrobtjQ29hBtrv3SW1d2te','dbef4e0e4be2488444396fe3b5868c97','dist/img/avatar.png',0,NULL,0,NULL,0,NULL,0),(4,'Alice','Admin','alice@example.com','password_hash','hash1','dist/img/avatar.png',1,NULL,0,NULL,1,NULL,0),(5,'Bob','User','bob@example.com','password_hash','hash2','dist/img/avatar.png',0,NULL,0,NULL,1,NULL,0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-13  8:33:45
