/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.13-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: escuela_presente_demo
-- ------------------------------------------------------
-- Server version	10.11.13-MariaDB-0ubuntu0.24.04.1

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
-- Table structure for table `academic_groups`
--

DROP TABLE IF EXISTS `academic_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `grade_id` bigint(20) unsigned NOT NULL,
  `section_id` bigint(20) unsigned NOT NULL,
  `school_cycle_id` bigint(20) unsigned NOT NULL,
  `student_limit` int(11) NOT NULL DEFAULT 10,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `academic_groups_grade_id_foreign` (`grade_id`),
  KEY `academic_groups_section_id_foreign` (`section_id`),
  KEY `academic_groups_school_cycle_id_foreign` (`school_cycle_id`),
  CONSTRAINT `academic_groups_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`),
  CONSTRAINT `academic_groups_school_cycle_id_foreign` FOREIGN KEY (`school_cycle_id`) REFERENCES `school_cycles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `academic_groups_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_groups`
--

LOCK TABLES `academic_groups` WRITE;
/*!40000 ALTER TABLE `academic_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `account_configurations`
--

DROP TABLE IF EXISTS `account_configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_configurations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `country` varchar(191) NOT NULL,
  `timezone` varchar(191) NOT NULL,
  `city` varchar(191) NOT NULL,
  `language` varchar(191) NOT NULL,
  `files_location` varchar(191) NOT NULL DEFAULT 'local',
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_configurations_user_id_foreign` (`user_id`),
  CONSTRAINT `account_configurations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_configurations`
--

LOCK TABLES `account_configurations` WRITE;
/*!40000 ALTER TABLE `account_configurations` DISABLE KEYS */;
/*!40000 ALTER TABLE `account_configurations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assists`
--

DROP TABLE IF EXISTS `assists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `assists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `observation` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assists_student_id_foreign` (`student_id`),
  CONSTRAINT `assists_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assists`
--

LOCK TABLES `assists` WRITE;
/*!40000 ALTER TABLE `assists` DISABLE KEYS */;
/*!40000 ALTER TABLE `assists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `balances`
--

DROP TABLE IF EXISTS `balances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `balances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `amount` int(11) NOT NULL,
  `type` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `balances_student_id_foreign` (`student_id`),
  CONSTRAINT `balances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `balances`
--

LOCK TABLES `balances` WRITE;
/*!40000 ALTER TABLE `balances` DISABLE KEYS */;
/*!40000 ALTER TABLE `balances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `files` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `path` varchar(191) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `files_user_id_foreign` (`user_id`),
  CONSTRAINT `files_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `files`
--

LOCK TABLES `files` WRITE;
/*!40000 ALTER TABLE `files` DISABLE KEYS */;
/*!40000 ALTER TABLE `files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `general_configuration`
--

DROP TABLE IF EXISTS `general_configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `general_configuration` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `cct` varchar(191) NOT NULL,
  `modality` varchar(191) NOT NULL,
  `address` varchar(191) NOT NULL,
  `coordinates` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`coordinates`)),
  `email` varchar(191) NOT NULL,
  `phone` varchar(191) NOT NULL,
  `website` varchar(191) NOT NULL,
  `fiscal_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`fiscal_data`)),
  `logo` varchar(191) NOT NULL DEFAULT '/image/no-image.jpg',
  `last_enrollment` varchar(191) NOT NULL DEFAULT '0',
  `plan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`plan`)),
  `prices` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`prices`)),
  `custom_messages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`custom_messages`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `general_configuration`
--

LOCK TABLES `general_configuration` WRITE;
/*!40000 ALTER TABLE `general_configuration` DISABLE KEYS */;
INSERT INTO `general_configuration` VALUES
(1,'Demo','1234567890','MATUTINA','Dirección','{\"lat\":\"19.5096103\",\"lng\":\"-99.1593793\"}','hola@hola.com','987654321','https://www.google.com.pe/','{\"billing_name\":\"DAVID HERRERA ALMERAYA\",\"rfc\":\"HEAD840903M63\",\"tax_regime\":\"Incorporaci\\u00f3n Fiscal\",\"postal_code\":\"07969\",\"billing_address\":\"CALZADA VALLEJO #2421, CIUDAD DE M\\u00c9XICO\"}','/image/no-image.jpg','1000000','{\"name\":\"Gratis\",\"limit\":50}','{\"reentry\":50,\"credentials\":50,\"replacement\":50}',NULL,'2025-04-07 19:04:05','2025-04-07 19:04:05');
/*!40000 ALTER TABLE `general_configuration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grades`
--

DROP TABLE IF EXISTS `grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `grades` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(191) NOT NULL,
  `order` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grades`
--

LOCK TABLES `grades` WRITE;
/*!40000 ALTER TABLE `grades` DISABLE KEYS */;
/*!40000 ALTER TABLE `grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `incidents`
--

DROP TABLE IF EXISTS `incidents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `incidents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incidents`
--

LOCK TABLES `incidents` WRITE;
/*!40000 ALTER TABLE `incidents` DISABLE KEYS */;
INSERT INTO `incidents` VALUES
(1,'EL ALUMNO(A) NO TRAJO MATERIAL',NULL,NULL),
(2,'EL ALUMNO(A) NO TRABAJA EN CLASE',NULL,NULL),
(3,'EL ALUMNO(A) NO HACE TAREAS',NULL,NULL),
(4,'EL ALUMNO(A) LLEGA TARDE',NULL,NULL),
(5,'EL ALUMNO(A) NO CUMPLE CON LOS MATERIALES',NULL,NULL),
(6,'EL ALUMNO(A) DISCUTE EN EL SALON DE CLASES',NULL,NULL),
(7,'EL ALUMNO(A) NO OBEDECE A LAS INSTRUCCIONES DEL PROFESOR',NULL,NULL),
(8,'EL ALUMNO(A) SALIO DEL SALON SIN AUTORIZACION',NULL,NULL),
(9,'EL ALUMNO(A) NO TRAE BATA DE LABORATORIO',NULL,NULL),
(10,'EL ALUMNO(A) NO TRAE EL UNIFORME COMPLETO',NULL,NULL),
(11,'EL ALUMNO(A) UTILIZA PALABRAS ANTISONANTES EN EL AULA',NULL,NULL),
(12,'EL ALUMNO(A) GOLPEA A SUS COMPAÑEROS',NULL,NULL),
(13,'EL ALUMNO(A) COME EN CLASE',NULL,NULL),
(14,'EL ALUMNO(A) NO RESPETA AL PERSONAL DOCENTE',NULL,NULL);
/*!40000 ALTER TABLE `incidents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `incidents_report`
--

DROP TABLE IF EXISTS `incidents_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `incidents_report` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `incident_id` bigint(20) unsigned NOT NULL,
  `teacher_id` bigint(20) unsigned NOT NULL,
  `specialty_id` bigint(20) unsigned NOT NULL,
  `photo` text DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `incidents_report_student_id_foreign` (`student_id`),
  KEY `incidents_report_incident_id_foreign` (`incident_id`),
  KEY `incidents_report_teacher_id_foreign` (`teacher_id`),
  KEY `incidents_report_specialty_id_foreign` (`specialty_id`),
  CONSTRAINT `incidents_report_incident_id_foreign` FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`),
  CONSTRAINT `incidents_report_specialty_id_foreign` FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`id`),
  CONSTRAINT `incidents_report_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  CONSTRAINT `incidents_report_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incidents_report`
--

LOCK TABLES `incidents_report` WRITE;
/*!40000 ALTER TABLE `incidents_report` DISABLE KEYS */;
/*!40000 ALTER TABLE `incidents_report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'2014_10_12_000000_create_users_table',1),
(2,'2014_10_12_100000_create_password_resets_table',1),
(3,'2016_06_01_000001_create_oauth_auth_codes_table',1),
(4,'2016_06_01_000002_create_oauth_access_tokens_table',1),
(5,'2016_06_01_000003_create_oauth_refresh_tokens_table',1),
(6,'2016_06_01_000004_create_oauth_clients_table',1),
(7,'2016_06_01_000005_create_oauth_personal_access_clients_table',1),
(8,'2019_08_19_000000_create_failed_jobs_table',1),
(9,'2019_12_14_000001_create_personal_access_tokens_table',1),
(10,'2022_07_07_164836_create_permission_tables',1),
(11,'2022_07_31_223855_create_account_configurations_table',1),
(12,'2022_08_01_033043_create_files_table',1),
(13,'2022_08_08_213503_create_general_configuration_table',1),
(14,'2022_10_21_000000_create_students_table',1),
(15,'2022_10_21_000001_create_relatives_data_table',1),
(16,'2022_10_21_000002_create_academic_data_table',1),
(17,'2022_10_21_000003_create_socioeconomic_data_table',1),
(18,'2022_10_21_000004_create_healt_data_table',1),
(19,'2022_10_21_231752_create_balances_table',1),
(20,'2022_10_21_235251_create_assists_table',1),
(21,'2022_10_21_235727_create_specialties_table',1),
(22,'2022_10_21_235728_create_teachers_table',1),
(23,'2022_10_21_235805_create_incidents_table',1),
(24,'2022_10_22_012408_create_incidents_report_table',1),
(25,'2022_11_14_024339_create_payments_table',1),
(26,'2022_11_24_043318_add_active_column_in_students_table',1),
(27,'2022_11_25_041038_create_grades_table',1),
(28,'2022_11_25_041051_create_sections_table',1),
(29,'2022_11_25_041112_create_school_cycles_table',1),
(30,'2022_11_25_041136_create_payment_prices_table',1),
(31,'2022_11_25_041155_create_academic_groups_table',1),
(32,'2022_11_25_042441_add_academic_group_column_in_students_table',1),
(33,'2022_11_25_042442_add_conekta_id_column_in_students_table',1),
(34,'2022_11_25_042443_add_prices_column_in_general_configuration_table',1),
(35,'2022_11_29_001846_add_order_column_in_grades_table',1),
(36,'2022_12_01_174853_update_student_id_column_students_table',1),
(37,'2022_12_11_034108_update_columns_payments_table',1),
(38,'2022_12_17_021725_add_custom_messages_column_in_general_configuration_table',1),
(39,'2023_02_28_150833_add_column_student_id_users_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES
(1,'App\\Models\\Tenants\\User',1);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_access_tokens`
--

DROP TABLE IF EXISTS `oauth_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) DEFAULT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_access_tokens`
--

LOCK TABLES `oauth_access_tokens` WRITE;
/*!40000 ALTER TABLE `oauth_access_tokens` DISABLE KEYS */;
INSERT INTO `oauth_access_tokens` VALUES
('00f7aae482bd5f889960b652d4fb70795e7b1ba65ecd8d7302736a54a2d8aa2816132b865912c677',1,2,'authToken','[]',0,'2025-04-07 19:15:27','2025-04-07 19:15:27','2026-04-07 19:15:27');
/*!40000 ALTER TABLE `oauth_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_auth_codes`
--

DROP TABLE IF EXISTS `oauth_auth_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_auth_codes`
--

LOCK TABLES `oauth_auth_codes` WRITE;
/*!40000 ALTER TABLE `oauth_auth_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_auth_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_clients`
--

DROP TABLE IF EXISTS `oauth_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `secret` varchar(100) DEFAULT NULL,
  `provider` varchar(191) DEFAULT NULL,
  `redirect` text NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_clients`
--

LOCK TABLES `oauth_clients` WRITE;
/*!40000 ALTER TABLE `oauth_clients` DISABLE KEYS */;
INSERT INTO `oauth_clients` VALUES
(1,NULL,'Default password grant client','jb4uV1XTQ8W8NEm2N1QXpIq2SntJcjVz2BCUkL2r',NULL,'http://your.redirect.path',0,1,0,'2025-04-07 19:04:04','2025-04-07 19:04:04'),
(2,NULL,'Default personal access client','dxiI8DeIykEMXKL0qC0EWo6Pj2a2N9nPmVI7RopE',NULL,'http://your.redirect.path',1,0,0,'2025-04-07 19:04:04','2025-04-07 19:04:04');
/*!40000 ALTER TABLE `oauth_clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_personal_access_clients`
--

DROP TABLE IF EXISTS `oauth_personal_access_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_personal_access_clients`
--

LOCK TABLES `oauth_personal_access_clients` WRITE;
/*!40000 ALTER TABLE `oauth_personal_access_clients` DISABLE KEYS */;
INSERT INTO `oauth_personal_access_clients` VALUES
(1,2,'2025-04-07 19:04:04','2025-04-07 19:04:04');
/*!40000 ALTER TABLE `oauth_personal_access_clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_refresh_tokens`
--

DROP TABLE IF EXISTS `oauth_refresh_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) NOT NULL,
  `access_token_id` varchar(100) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_refresh_tokens`
--

LOCK TABLES `oauth_refresh_tokens` WRITE;
/*!40000 ALTER TABLE `oauth_refresh_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_refresh_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_prices`
--

DROP TABLE IF EXISTS `payment_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_prices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(191) NOT NULL,
  `amount` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_prices`
--

LOCK TABLES `payment_prices` WRITE;
/*!40000 ALTER TABLE `payment_prices` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_prices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `preference_id` varchar(191) DEFAULT NULL,
  `status` varchar(191) DEFAULT NULL,
  `payment_id` varchar(191) DEFAULT NULL,
  `merchant_order_id` varchar(191) DEFAULT NULL,
  `payment_type` varchar(191) DEFAULT NULL,
  `payment_method` varchar(191) DEFAULT NULL,
  `amount` int(11) NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_student_id_foreign` (`student_id`),
  CONSTRAINT `payments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES
(1,'create assist','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(2,'read assist','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(3,'update assist','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(4,'delete assist','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(5,'create balance','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(6,'read balance','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(7,'update balance','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(8,'delete balance','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(9,'create dashboard','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(10,'read dashboard','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(11,'update dashboard','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(12,'delete dashboard','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(13,'create dinner','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(14,'read dinner','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(15,'update dinner','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(16,'delete dinner','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(17,'create general configuration','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(18,'read general configuration','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(19,'update general configuration','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(20,'delete general configuration','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(21,'create incident','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(22,'read incident','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(23,'update incident','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(24,'delete incident','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(25,'create incident report','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(26,'read incident report','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(27,'update incident report','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(28,'delete incident report','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(29,'create role','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(30,'read role','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(31,'update role','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(32,'delete role','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(33,'create specialty','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(34,'read specialty','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(35,'update specialty','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(36,'delete specialty','api','2025-04-07 19:04:05','2025-04-07 19:04:05'),
(37,'create student','api','2025-04-07 19:04:05','2025-04-07 19:04:05'),
(38,'read student','api','2025-04-07 19:04:05','2025-04-07 19:04:05'),
(39,'update student','api','2025-04-07 19:04:05','2025-04-07 19:04:05'),
(40,'delete student','api','2025-04-07 19:04:05','2025-04-07 19:04:05'),
(41,'create teacher','api','2025-04-07 19:04:05','2025-04-07 19:04:05'),
(42,'read teacher','api','2025-04-07 19:04:05','2025-04-07 19:04:05'),
(43,'update teacher','api','2025-04-07 19:04:05','2025-04-07 19:04:05'),
(44,'delete teacher','api','2025-04-07 19:04:05','2025-04-07 19:04:05'),
(45,'create user','api','2025-04-07 19:04:05','2025-04-07 19:04:05'),
(46,'read user','api','2025-04-07 19:04:05','2025-04-07 19:04:05'),
(47,'update user','api','2025-04-07 19:04:05','2025-04-07 19:04:05'),
(48,'delete user','api','2025-04-07 19:04:05','2025-04-07 19:04:05');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES
(1,2),
(2,2),
(3,2),
(4,2),
(5,2),
(6,2),
(7,2),
(8,2),
(9,2),
(10,2),
(11,2),
(12,2),
(13,2),
(14,2),
(15,2),
(16,2),
(17,2),
(18,2),
(19,2),
(20,2),
(21,2),
(22,2),
(23,2),
(24,2),
(25,2),
(26,2),
(27,2),
(28,2),
(29,2),
(30,2),
(31,2),
(32,2),
(33,2),
(34,2),
(35,2),
(36,2),
(37,2),
(38,2),
(39,2),
(40,2),
(41,2),
(42,2),
(43,2),
(44,2),
(45,2),
(46,2),
(47,2),
(48,2);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'Super Admin','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(2,'Admin','api','2025-04-07 19:04:04','2025-04-07 19:04:04'),
(3,'Usuario','api','2025-04-07 19:04:04','2025-04-07 19:04:04');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_cycles`
--

DROP TABLE IF EXISTS `school_cycles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `school_cycles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_cycles`
--

LOCK TABLES `school_cycles` WRITE;
/*!40000 ALTER TABLE `school_cycles` DISABLE KEYS */;
/*!40000 ALTER TABLE `school_cycles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `specialties`
--

DROP TABLE IF EXISTS `specialties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `specialties` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `specialties`
--

LOCK TABLES `specialties` WRITE;
/*!40000 ALTER TABLE `specialties` DISABLE KEYS */;
INSERT INTO `specialties` VALUES
(1,'SUB. ACADEMICA',NULL,NULL),
(2,'DPTO. DE ORIENTACION',NULL,NULL),
(3,'ESPAÑOL',NULL,NULL),
(4,'MATEMATICAS',NULL,NULL),
(5,'HISTORIA',NULL,NULL),
(6,'GEOGRAFIA',NULL,NULL),
(7,'BIOLOGIA',NULL,NULL),
(8,'FISICA',NULL,NULL),
(9,'INGLES',NULL,NULL),
(10,'FORMACION CIVICA',NULL,NULL),
(11,'MUSICA',NULL,NULL),
(12,'TEATRO',NULL,NULL),
(13,'ARTES VISUALES',NULL,NULL);
/*!40000 ALTER TABLE `specialties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_academics`
--

DROP TABLE IF EXISTS `student_academics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_academics` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `udeei` varchar(191) DEFAULT NULL,
  `origin_school` varchar(191) DEFAULT NULL,
  `federal_entity_school` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_academics_student_id_foreign` (`student_id`),
  CONSTRAINT `student_academics_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_academics`
--

LOCK TABLES `student_academics` WRITE;
/*!40000 ALTER TABLE `student_academics` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_academics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_healths`
--

DROP TABLE IF EXISTS `student_healths`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_healths` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `current_general_status` varchar(191) DEFAULT NULL,
  `blood_type` varchar(191) DEFAULT NULL,
  `chronic_disease` varchar(191) DEFAULT NULL,
  `has_medical_service` tinyint(1) NOT NULL DEFAULT 1,
  `medical_service_number` varchar(191) DEFAULT NULL,
  `medical_service_name` varchar(191) DEFAULT NULL,
  `familiar_affection` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`familiar_affection`)),
  `medical_care` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`medical_care`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_healths_student_id_foreign` (`student_id`),
  CONSTRAINT `student_healths_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_healths`
--

LOCK TABLES `student_healths` WRITE;
/*!40000 ALTER TABLE `student_healths` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_healths` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_relatives`
--

DROP TABLE IF EXISTS `student_relatives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_relatives` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `father_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`father_data`)),
  `mother_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`mother_data`)),
  `authorized_persons` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`authorized_persons`)),
  `roommates` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`roommates`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_relatives_student_id_foreign` (`student_id`),
  CONSTRAINT `student_relatives_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_relatives`
--

LOCK TABLES `student_relatives` WRITE;
/*!40000 ALTER TABLE `student_relatives` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_relatives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_socioeconomics`
--

DROP TABLE IF EXISTS `student_socioeconomics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_socioeconomics` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `general` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`general`)),
  `ownerships` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ownerships`)),
  `nutrition` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`nutrition`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_socioeconomics_student_id_foreign` (`student_id`),
  CONSTRAINT `student_socioeconomics_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_socioeconomics`
--

LOCK TABLES `student_socioeconomics` WRITE;
/*!40000 ALTER TABLE `student_socioeconomics` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_socioeconomics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `students` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `enrollment` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `last_name_father` varchar(191) NOT NULL,
  `last_name_mother` varchar(191) NOT NULL,
  `nationality` enum('MEXICANA','EXTRANJERA') NOT NULL,
  `curp` varchar(191) NOT NULL,
  `date_birth` date NOT NULL,
  `place_birth` varchar(191) NOT NULL,
  `sex` enum('MASCULINO','FEMENINO') NOT NULL,
  `weight` varchar(191) NOT NULL,
  `height` varchar(191) NOT NULL,
  `is_migrant` tinyint(1) NOT NULL DEFAULT 0,
  `indigenous_group` varchar(191) NOT NULL,
  `indigenous_language` varchar(191) NOT NULL,
  `disability` varchar(191) NOT NULL,
  `health_insurance` varchar(191) NOT NULL,
  `scholarship` varchar(191) NOT NULL,
  `address` varchar(191) NOT NULL,
  `colony` varchar(191) NOT NULL,
  `postal_code` varchar(191) NOT NULL,
  `municipality` varchar(191) NOT NULL,
  `federal_entity` varchar(191) NOT NULL,
  `home_phone` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `photo` varchar(191) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `academic_group_id` bigint(20) unsigned DEFAULT NULL,
  `mercado_pago_id` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_enrollment_unique` (`enrollment`),
  KEY `students_academic_group_id_foreign` (`academic_group_id`),
  CONSTRAINT `students_academic_group_id_foreign` FOREIGN KEY (`academic_group_id`) REFERENCES `academic_groups` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `teachers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `last_name` varchar(191) DEFAULT NULL,
  `date_birth` date DEFAULT NULL,
  `sex` enum('MASCULINO','FEMENINO') DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `address` varchar(191) DEFAULT NULL,
  `specialty_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `teachers_specialty_id_foreign` (`specialty_id`),
  CONSTRAINT `teachers_specialty_id_foreign` FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teachers`
--

LOCK TABLES `teachers` WRITE;
/*!40000 ALTER TABLE `teachers` DISABLE KEYS */;
INSERT INTO `teachers` VALUES
(1,'VICTOR MANUEL GARCIA ORDAZ',NULL,NULL,NULL,NULL,NULL,NULL,7,NULL,NULL),
(2,'BENITEZ ORTIZ JUANA',NULL,NULL,NULL,NULL,NULL,NULL,5,NULL,NULL),
(3,'ISLAS JIMENEZ MAGALI',NULL,NULL,NULL,NULL,NULL,NULL,7,NULL,NULL),
(4,'MEJIA GARCIA NAYELY SUGEY',NULL,NULL,NULL,NULL,NULL,NULL,9,NULL,NULL),
(5,'MONTERO REYES ELIZABETH',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL),
(6,'BUENDIA GONZALEZ IVAN HECTOR',NULL,NULL,NULL,NULL,NULL,NULL,8,NULL,NULL),
(7,'GUTIERREZ NORMAN JOSE ANTONIO',NULL,NULL,NULL,NULL,NULL,NULL,13,NULL,NULL),
(8,'RODRIGUEZ JIMENEZ LEONARDO',NULL,NULL,NULL,NULL,NULL,NULL,13,NULL,NULL),
(9,'SERRANO CASTRO VIVIANA',NULL,NULL,NULL,NULL,NULL,NULL,13,NULL,NULL),
(10,'BENITEZ POLANCO ADRIANA GEORGINA',NULL,NULL,NULL,NULL,NULL,NULL,7,NULL,NULL),
(11,'SANCHEZ ALMARAZ IRENE',NULL,NULL,NULL,NULL,NULL,NULL,7,NULL,NULL),
(12,'CIPRIANO REYES BESALEL DAFNE',NULL,NULL,NULL,NULL,NULL,NULL,7,NULL,NULL),
(13,'TREJO CASTAÑEDA ALEJANDRA BRISEIDA',NULL,NULL,NULL,NULL,NULL,NULL,8,NULL,NULL),
(14,'VAZQUEZ RAMIREZ GUADALUPE',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL),
(15,'ESPINOSA ROMERO MARIA MARTHA',NULL,NULL,NULL,NULL,NULL,NULL,13,NULL,NULL),
(16,'GODINEZ REYES ZAFIRO BIBIANA',NULL,NULL,NULL,NULL,NULL,NULL,6,NULL,NULL),
(17,'GONZALEZ MUÑOZ LUISA SILVIA',NULL,NULL,NULL,NULL,NULL,NULL,9,NULL,NULL),
(18,'PEREZ MAZA FRANCISCO',NULL,NULL,NULL,NULL,NULL,NULL,10,NULL,NULL),
(19,'MARQUEZ GARCIA PERLA SUSANA',NULL,NULL,NULL,NULL,NULL,NULL,10,NULL,NULL),
(20,'MARTINEZ CANO JOCELYN',NULL,NULL,NULL,NULL,NULL,NULL,6,NULL,NULL),
(21,'GARCIA LUGO MARGARITA',NULL,NULL,NULL,NULL,NULL,NULL,5,NULL,NULL),
(22,'GUZMAN RODRIGUEZ MARINA',NULL,NULL,NULL,NULL,NULL,NULL,4,NULL,NULL),
(23,'VILLARREAL LINARES JUDITH',NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL),
(24,'DE LA LONGA CABRERA LUZ AUDELLI',NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL),
(25,'WALLES TORRES SOLEDAD IRMA',NULL,NULL,NULL,NULL,NULL,NULL,11,NULL,NULL),
(26,'JIMENEZ HERRERA IRMA ',NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL),
(27,'ROJAS JACINTO LETICIA',NULL,NULL,NULL,NULL,NULL,NULL,6,NULL,NULL),
(28,'SOTO MUÑOZ VANESSA SASHENKA',NULL,NULL,NULL,NULL,NULL,NULL,8,NULL,NULL),
(29,'ESCALONA MENDEZ MARIA ANTONIETA',NULL,NULL,NULL,NULL,NULL,NULL,4,NULL,NULL);
/*!40000 ALTER TABLE `teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `student_id` bigint(20) unsigned DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_student_id_foreign` (`student_id`),
  CONSTRAINT `users_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Super Admin','demo@gmail.com',NULL,'$2y$10$d5lzA0VuRCzfaSTlR73Nx.fUo1dTcMTjANl0k2QbV27Ywba9jXvo2',1,NULL,NULL,'2025-04-07 19:04:05','2025-04-07 19:04:05');
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

-- Dump completed on 2026-02-06 18:59:13
