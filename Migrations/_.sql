-- MariaDB dump 10.19  Distrib 10.6.11-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: 127.0.0.1    Database: framework
-- ------------------------------------------------------
-- Server version	10.6.11-MariaDB-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `mt_authorization`
--

DROP TABLE IF EXISTS `mt_authorization`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mt_authorization` (
                                    `id` int(11) NOT NULL,
                                    `uuid` char(38) NOT NULL,
                                    `u_id` int(11) NOT NULL,
                                    `key` varchar(128) NOT NULL,
                                    `status` bit(1) NOT NULL DEFAULT b'1',
                                    `expired_at` datetime NOT NULL,
                                    `created_at` datetime DEFAULT current_timestamp(),
                                    `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
                                    PRIMARY KEY (`id`),
                                    KEY `mt_authorization_mt_member_null_fk` (`u_id`),
                                    CONSTRAINT `mt_authorization_mt_member_null_fk` FOREIGN KEY (`u_id`) REFERENCES `mt_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mt_language`
--

DROP TABLE IF EXISTS `mt_language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mt_language` (
                               `id` int(11) NOT NULL AUTO_INCREMENT,
                               `name` char(64) NOT NULL,
                               `locale` char(6) NOT NULL,
                               `flag` char(64) NOT NULL,
                               `is_default` bit(1) NOT NULL DEFAULT b'0',
                               PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mt_module`
--

DROP TABLE IF EXISTS `mt_module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mt_module` (
                             `id` int(11) NOT NULL AUTO_INCREMENT,
                             `route` char(32) DEFAULT NULL,
                             `name` char(64) NOT NULL,
                             `translate_tag` char(64) NOT NULL,
                             `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                             `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
                             `is_hidden` bit(1) DEFAULT b'0',
                             PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mt_page`
--

DROP TABLE IF EXISTS `mt_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mt_page` (
                           `id` int(11) NOT NULL AUTO_INCREMENT,
                           `m_id` int(11) NOT NULL,
                           `s_id` int(11) DEFAULT NULL,
                           `route` char(32) NOT NULL,
                           `name` char(64) NOT NULL,
                           `icon` varchar(128) DEFAULT NULL,
                           `translate_tag` char(64) NOT NULL,
                           `meta_data` text NOT NULL,
                           `is_hidden` bit(1) DEFAULT b'0',
                           PRIMARY KEY (`id`),
                           KEY `foreign_key_name` (`m_id`),
                           KEY `mt_page_mt_page_id_fk` (`s_id`),
                           CONSTRAINT `mt_page_ibfk_1` FOREIGN KEY (`m_id`) REFERENCES `mt_module` (`id`),
                           CONSTRAINT `mt_page_mt_page_id_fk` FOREIGN KEY (`s_id`) REFERENCES `mt_page` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mt_role_m_access`
--

DROP TABLE IF EXISTS `mt_role_m_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mt_role_m_access` (
                                    `id` int(11) NOT NULL AUTO_INCREMENT,
                                    `r_id` int(11) NOT NULL,
                                    `m_id` int(11) NOT NULL,
                                    `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                                    `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
                                    PRIMARY KEY (`id`),
                                    KEY `mt_role_m_access_mt_roles_null_fk` (`r_id`),
                                    KEY `mt_role_m_access_mt_module_null_fk` (`m_id`),
                                    CONSTRAINT `mt_role_m_access_mt_module_null_fk` FOREIGN KEY (`m_id`) REFERENCES `mt_module` (`id`),
                                    CONSTRAINT `mt_role_m_access_mt_roles_null_fk` FOREIGN KEY (`r_id`) REFERENCES `mt_roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mt_role_p_access`
--

DROP TABLE IF EXISTS `mt_role_p_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mt_role_p_access` (
                                    `id` int(11) NOT NULL AUTO_INCREMENT,
                                    `r_id` int(11) NOT NULL,
                                    `p_id` int(11) NOT NULL,
                                    `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                                    `updated_at` datetime DEFAULT current_timestamp(),
                                    PRIMARY KEY (`id`),
                                    KEY `mt_role_p_access_mt_page_null_fk` (`p_id`),
                                    KEY `mt_role_p_access_mt_roles_null_fk` (`r_id`),
                                    CONSTRAINT `mt_role_p_access_mt_page_null_fk` FOREIGN KEY (`p_id`) REFERENCES `mt_page` (`id`),
                                    CONSTRAINT `mt_role_p_access_mt_roles_null_fk` FOREIGN KEY (`r_id`) REFERENCES `mt_roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mt_roles`
--

DROP TABLE IF EXISTS `mt_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mt_roles` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `name` char(64) NOT NULL,
                            PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mt_user`
--

DROP TABLE IF EXISTS `mt_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mt_user` (
                           `id` int(11) NOT NULL AUTO_INCREMENT,
                           `uuid` char(38) DEFAULT NULL,
                           `first_name` char(32) NOT NULL,
                           `last_name` char(64) NOT NULL,
                           `avatar` text DEFAULT NULL,
                           `username` char(16) NOT NULL,
                           `password` varchar(128) NOT NULL,
                           `email` char(128) NOT NULL,
                           `phone` char(10) DEFAULT NULL,
                           `dateofbirth` date NOT NULL,
                           `role` int(11) DEFAULT 1,
                           `locale` int(11) NOT NULL DEFAULT 1,
                           `is_verified` bit(1) NOT NULL DEFAULT b'0',
                           `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                           `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
                           PRIMARY KEY (`id`),
                           KEY `mt_user_mt_roles_null_fk` (`role`),
                           KEY `mt_user_mt_language_null_fk` (`locale`),
                           CONSTRAINT `mt_user_mt_language_null_fk` FOREIGN KEY (`locale`) REFERENCES `mt_language` (`id`),
                           CONSTRAINT `mt_user_mt_roles_null_fk` FOREIGN KEY (`role`) REFERENCES `mt_roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mt_user_m_block`
--

DROP TABLE IF EXISTS `mt_user_m_block`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mt_user_m_block` (
                                   `id` int(11) NOT NULL AUTO_INCREMENT,
                                   `u_id` int(11) NOT NULL,
                                   `m_id` int(11) NOT NULL,
                                   `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                                   `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
                                   PRIMARY KEY (`id`),
                                   KEY `mt_user_m_block_mt_module_null_fk` (`m_id`),
                                   KEY `mt_user_m_block_mt_roles_null_fk` (`u_id`),
                                   CONSTRAINT `mt_user_m_block_mt_module_null_fk` FOREIGN KEY (`m_id`) REFERENCES `mt_module` (`id`),
                                   CONSTRAINT `mt_user_m_block_mt_roles_null_fk` FOREIGN KEY (`u_id`) REFERENCES `mt_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mt_user_p_block`
--

DROP TABLE IF EXISTS `mt_user_p_block`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mt_user_p_block` (
                                   `id` int(11) NOT NULL AUTO_INCREMENT,
                                   `u_id` int(11) NOT NULL,
                                   `p_id` int(11) NOT NULL,
                                   `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                                   `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
                                   PRIMARY KEY (`id`),
                                   KEY `mt_user_p_block_mt_page_null_fk` (`p_id`),
                                   KEY `mt_user_p_block_mt_user_null_fk` (`u_id`),
                                   CONSTRAINT `mt_user_p_block_mt_page_null_fk` FOREIGN KEY (`p_id`) REFERENCES `mt_page` (`id`),
                                   CONSTRAINT `mt_user_p_block_mt_user_null_fk` FOREIGN KEY (`u_id`) REFERENCES `mt_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mt_user_reset_password`
--

DROP TABLE IF EXISTS `mt_user_reset_password`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mt_user_reset_password` (
                                          `id` int(11) NOT NULL AUTO_INCREMENT,
                                          `uuid` char(38) NOT NULL,
                                          `u_id` int(11) DEFAULT NULL,
                                          `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                                          `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
                                          PRIMARY KEY (`id`),
                                          KEY `mt_user_reset_password_mt_user_id_fk` (`u_id`),
                                          CONSTRAINT `mt_user_reset_password_mt_user_id_fk` FOREIGN KEY (`u_id`) REFERENCES `mt_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mt_user_sessions`
--

DROP TABLE IF EXISTS `mt_user_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mt_user_sessions` (
                                    `id` int(11) NOT NULL AUTO_INCREMENT,
                                    `uuid` char(38) NOT NULL,
                                    `u_id` int(11) NOT NULL,
                                    `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                                    `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
                                    PRIMARY KEY (`id`),
                                    UNIQUE KEY `mt_member_sessions_pk` (`uuid`),
                                    KEY `mt_member_sessions_mt_member_null_fk` (`u_id`),
                                    CONSTRAINT `mt_member_sessions_mt_member_null_fk` FOREIGN KEY (`u_id`) REFERENCES `mt_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mt_user_verification`
--

DROP TABLE IF EXISTS `mt_user_verification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mt_user_verification` (
                                        `id` int(11) NOT NULL AUTO_INCREMENT,
                                        `uuid` char(38) NOT NULL,
                                        `u_id` int(11) NOT NULL,
                                        `status` bit(1) NOT NULL DEFAULT b'0',
                                        `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                                        `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
                                        PRIMARY KEY (`id`),
                                        KEY `mt_member_verification_mt_member_null_fk` (`u_id`),
                                        CONSTRAINT `mt_member_verification_mt_member_null_fk` FOREIGN KEY (`u_id`) REFERENCES `mt_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-01-15  1:06:24
