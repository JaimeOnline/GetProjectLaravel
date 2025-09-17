-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: localhost    Database: getprojectlaravel
-- ------------------------------------------------------
-- Server version	8.0.41

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
-- Table structure for table `activities`
--

DROP TABLE IF EXISTS `activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `estatus_operacional` text COLLATE utf8mb4_unicode_ci,
  `status` enum('no_iniciada','en_ejecucion','culminada','en_espera_de_insumos','en_certificacion_por_cliente','pases_enviados','pausada','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_ejecucion',
  `caso` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `fecha_recepcion` date DEFAULT NULL,
  `prioridad` int NOT NULL DEFAULT '1',
  `orden_analista` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `activities_user_id_foreign` (`user_id`),
  KEY `activities_parent_id_foreign` (`parent_id`),
  CONSTRAINT `activities_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `activities` (`id`) ON DELETE SET NULL,
  CONSTRAINT `activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activities`
--

LOCK TABLES `activities` WRITE;
/*!40000 ALTER TABLE `activities` DISABLE KEYS */;
INSERT INTO `activities` VALUES (1,'Actividad Principal 1','Descripción de la actividad principal 1',NULL,'pases_enviados','CASO-001',NULL,NULL,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-09-04',1,4),(2,'Subactividad 1 de 1','Descripción de la subactividad 1 de 1',NULL,'cancelada','CASO-1-SUB1',NULL,1,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-05-17',4,2),(3,'Subsubactividad 1 de 1 de 1','Descripción de la subsubactividad 1 de 1 de 1',NULL,'pases_enviados','CASO-1-SUB1-SUB1',NULL,2,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-05-19',2,5),(4,'Subactividad 2 de 1','Descripción de la subactividad 2 de 1',NULL,'culminada','CASO-1-SUB2',NULL,1,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-03-19',5,3),(5,'Subsubactividad 1 de 2 de 1','Descripción de la subsubactividad 1 de 2 de 1',NULL,'cancelada','CASO-1-SUB2-SUB1',NULL,4,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-03-16',2,4),(6,'Subactividad 3 de 1','Descripción de la subactividad 3 de 1',NULL,'en_espera_de_insumos','CASO-1-SUB3',NULL,1,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-06-13',5,1),(7,'Subsubactividad 1 de 3 de 1','Descripción de la subsubactividad 1 de 3 de 1',NULL,'en_certificacion_por_cliente','CASO-1-SUB3-SUB1',NULL,6,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-04-05',4,4),(8,'Subsubactividad 2 de 3 de 1','Descripción de la subsubactividad 2 de 3 de 1',NULL,'en_certificacion_por_cliente','CASO-1-SUB3-SUB2',NULL,6,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-07-28',1,5),(9,'Actividad Principal 2','Descripción de la actividad principal 2',NULL,'no_iniciada','CASO-002',NULL,NULL,'2025-09-13 01:18:30','2025-09-13 01:18:30','2024-11-24',5,3),(10,'Subactividad 1 de 2','Descripción de la subactividad 1 de 2',NULL,'en_espera_de_insumos','CASO-2-SUB1',NULL,9,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-01-30',3,5),(11,'Subsubactividad 1 de 1 de 2','Descripción de la subsubactividad 1 de 1 de 2',NULL,'en_ejecucion','CASO-2-SUB1-SUB1',NULL,10,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-04-17',4,3),(12,'Subsubactividad 2 de 1 de 2','Descripción de la subsubactividad 2 de 1 de 2',NULL,'en_ejecucion','CASO-2-SUB1-SUB2',NULL,10,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-02-08',4,3),(13,'Subactividad 2 de 2','Descripción de la subactividad 2 de 2',NULL,'en_certificacion_por_cliente','CASO-2-SUB2',NULL,9,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-01-04',2,4),(14,'Subsubactividad 1 de 2 de 2','Descripción de la subsubactividad 1 de 2 de 2',NULL,'culminada','CASO-2-SUB2-SUB1',NULL,13,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-07-28',2,1),(15,'Subsubactividad 2 de 2 de 2','Descripción de la subsubactividad 2 de 2 de 2',NULL,'en_certificacion_por_cliente','CASO-2-SUB2-SUB2',NULL,13,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-07-07',5,1),(16,'Actividad Principal 3','Descripción de la actividad principal 3',NULL,'pases_enviados','CASO-003',NULL,NULL,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-03-03',4,3),(17,'Subactividad 1 de 3','Descripción de la subactividad 1 de 3',NULL,'cancelada','CASO-3-SUB1',NULL,16,'2025-09-13 01:18:30','2025-09-13 01:18:30','2024-09-27',5,4),(18,'Subsubactividad 1 de 1 de 3','Descripción de la subsubactividad 1 de 1 de 3',NULL,'culminada','CASO-3-SUB1-SUB1',NULL,17,'2025-09-13 01:18:30','2025-09-13 01:18:30','2024-10-23',2,2),(19,'Subsubactividad 2 de 1 de 3','Descripción de la subsubactividad 2 de 1 de 3',NULL,'cancelada','CASO-3-SUB1-SUB2',NULL,17,'2025-09-13 01:18:30','2025-09-13 01:18:30','2024-09-12',2,4),(20,'Subactividad 2 de 3','Descripción de la subactividad 2 de 3',NULL,'no_iniciada','CASO-3-SUB2',NULL,16,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-01-13',4,5),(21,'Subsubactividad 1 de 2 de 3','Descripción de la subsubactividad 1 de 2 de 3',NULL,'en_ejecucion','CASO-3-SUB2-SUB1',NULL,20,'2025-09-13 01:18:30','2025-09-13 01:18:30','2024-10-12',3,2),(22,'Subsubactividad 2 de 2 de 3','Descripción de la subsubactividad 2 de 2 de 3',NULL,'en_espera_de_insumos','CASO-3-SUB2-SUB2',NULL,20,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-04-06',1,5),(23,'Subactividad 3 de 3','Descripción de la subactividad 3 de 3',NULL,'en_ejecucion','CASO-3-SUB3',NULL,16,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-09-04',1,2),(24,'Subsubactividad 1 de 3 de 3','Descripción de la subsubactividad 1 de 3 de 3',NULL,'pases_enviados','CASO-3-SUB3-SUB1',NULL,23,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-04-14',2,2),(25,'Actividad Principal 4','Descripción de la actividad principal 4',NULL,'pausada','CASO-004',NULL,NULL,'2025-09-13 01:18:30','2025-09-13 01:18:30','2024-12-27',4,3),(26,'Subactividad 1 de 4','Descripción de la subactividad 1 de 4',NULL,'pausada','CASO-4-SUB1',NULL,25,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-03-15',1,4),(27,'Subsubactividad 1 de 1 de 4','Descripción de la subsubactividad 1 de 1 de 4',NULL,'pausada','CASO-4-SUB1-SUB1',NULL,26,'2025-09-13 01:18:30','2025-09-13 01:18:30','2024-12-29',3,2),(28,'Subactividad 2 de 4','Descripción de la subactividad 2 de 4',NULL,'en_ejecucion','CASO-4-SUB2',NULL,25,'2025-09-13 01:18:30','2025-09-13 01:18:30','2024-12-23',5,1),(29,'Subsubactividad 1 de 2 de 4','Descripción de la subsubactividad 1 de 2 de 4',NULL,'en_ejecucion','CASO-4-SUB2-SUB1',NULL,28,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-03-16',2,2),(30,'Subsubactividad 2 de 2 de 4','Descripción de la subsubactividad 2 de 2 de 4',NULL,'en_espera_de_insumos','CASO-4-SUB2-SUB2',NULL,28,'2025-09-13 01:18:30','2025-09-13 01:18:30','2024-12-01',2,1),(31,'Subactividad 3 de 4','Descripción de la subactividad 3 de 4',NULL,'pausada','CASO-4-SUB3',NULL,25,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-03-18',2,4),(32,'Subsubactividad 1 de 3 de 4','Descripción de la subsubactividad 1 de 3 de 4',NULL,'en_ejecucion','CASO-4-SUB3-SUB1',NULL,31,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-03-20',2,2),(33,'Subsubactividad 2 de 3 de 4','Descripción de la subsubactividad 2 de 3 de 4',NULL,'cancelada','CASO-4-SUB3-SUB2',NULL,31,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-06-04',2,1),(34,'Actividad Principal 5','Descripción de la actividad principal 5',NULL,'no_iniciada','CASO-005',NULL,NULL,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-03-03',5,5),(35,'Subactividad 1 de 5','Descripción de la subactividad 1 de 5',NULL,'en_ejecucion','CASO-5-SUB1',NULL,34,'2025-09-13 01:18:30','2025-09-13 01:18:30','2024-10-09',2,1),(36,'Subsubactividad 1 de 1 de 5','Descripción de la subsubactividad 1 de 1 de 5',NULL,'en_certificacion_por_cliente','CASO-5-SUB1-SUB1',NULL,35,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-06-18',2,2),(37,'Subsubactividad 2 de 1 de 5','Descripción de la subsubactividad 2 de 1 de 5',NULL,'en_espera_de_insumos','CASO-5-SUB1-SUB2',NULL,35,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-06-07',3,5),(38,'Subactividad 2 de 5','Descripción de la subactividad 2 de 5',NULL,'en_certificacion_por_cliente','CASO-5-SUB2',NULL,34,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-03-04',4,5),(39,'Subsubactividad 1 de 2 de 5','Descripción de la subsubactividad 1 de 2 de 5',NULL,'pausada','CASO-5-SUB2-SUB1',NULL,38,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-06-21',2,2),(40,'Subsubactividad 2 de 2 de 5','Descripción de la subsubactividad 2 de 2 de 5',NULL,'en_certificacion_por_cliente','CASO-5-SUB2-SUB2',NULL,38,'2025-09-13 01:18:30','2025-09-13 01:18:30','2025-08-14',4,4),(41,'Subactividad 3 de 5','Descripción de la subactividad 3 de 5',NULL,'en_ejecucion','CASO-5-SUB3',NULL,34,'2025-09-13 01:18:31','2025-09-13 01:18:31','2024-09-13',1,1),(42,'Subsubactividad 1 de 3 de 5','Descripción de la subsubactividad 1 de 3 de 5',NULL,'pausada','CASO-5-SUB3-SUB1',NULL,41,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-03-26',2,3),(43,'Subsubactividad 2 de 3 de 5','Descripción de la subsubactividad 2 de 3 de 5',NULL,'no_iniciada','CASO-5-SUB3-SUB2',NULL,41,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-01-14',1,1),(44,'Actividad Principal 6','Descripción de la actividad principal 6',NULL,'en_certificacion_por_cliente','CASO-006',NULL,NULL,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-04-06',4,1),(45,'Subactividad 1 de 6','Descripción de la subactividad 1 de 6',NULL,'culminada','CASO-6-SUB1',NULL,44,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-07-29',3,3),(46,'Subsubactividad 1 de 1 de 6','Descripción de la subsubactividad 1 de 1 de 6',NULL,'cancelada','CASO-6-SUB1-SUB1',NULL,45,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-02-09',2,3),(47,'Subsubactividad 2 de 1 de 6','Descripción de la subsubactividad 2 de 1 de 6',NULL,'en_certificacion_por_cliente','CASO-6-SUB1-SUB2',NULL,45,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-08-21',3,1),(48,'Subactividad 2 de 6','Descripción de la subactividad 2 de 6',NULL,'en_ejecucion','CASO-6-SUB2',NULL,44,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-08-22',1,5),(49,'Subsubactividad 1 de 2 de 6','Descripción de la subsubactividad 1 de 2 de 6',NULL,'en_espera_de_insumos','CASO-6-SUB2-SUB1',NULL,48,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-06-02',5,3),(50,'Subactividad 3 de 6','Descripción de la subactividad 3 de 6',NULL,'no_iniciada','CASO-6-SUB3',NULL,44,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-06-29',5,4),(51,'Subsubactividad 1 de 3 de 6','Descripción de la subsubactividad 1 de 3 de 6',NULL,'en_certificacion_por_cliente','CASO-6-SUB3-SUB1',NULL,50,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-02-18',4,5),(52,'Subsubactividad 2 de 3 de 6','Descripción de la subsubactividad 2 de 3 de 6',NULL,'en_ejecucion','CASO-6-SUB3-SUB2',NULL,50,'2025-09-13 01:18:31','2025-09-13 01:18:31','2024-11-30',4,4),(53,'Subactividad 4 de 6','Descripción de la subactividad 4 de 6',NULL,'pases_enviados','CASO-6-SUB4',NULL,44,'2025-09-13 01:18:31','2025-09-13 01:18:31','2024-09-30',4,2),(54,'Subsubactividad 1 de 4 de 6','Descripción de la subsubactividad 1 de 4 de 6',NULL,'no_iniciada','CASO-6-SUB4-SUB1',NULL,53,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-08-14',1,1),(55,'Actividad Principal 7','Descripción de la actividad principal 7',NULL,'no_iniciada','CASO-007',NULL,NULL,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-01-24',5,5),(56,'Subactividad 1 de 7','Descripción de la subactividad 1 de 7',NULL,'en_espera_de_insumos','CASO-7-SUB1',NULL,55,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-01-07',4,2),(57,'Subsubactividad 1 de 1 de 7','Descripción de la subsubactividad 1 de 1 de 7',NULL,'pases_enviados','CASO-7-SUB1-SUB1',NULL,56,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-05-09',1,5),(58,'Subsubactividad 2 de 1 de 7','Descripción de la subsubactividad 2 de 1 de 7',NULL,'en_espera_de_insumos','CASO-7-SUB1-SUB2',NULL,56,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-07-19',5,5),(59,'Subactividad 2 de 7','Descripción de la subactividad 2 de 7',NULL,'culminada','CASO-7-SUB2',NULL,55,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-09-12',5,1),(60,'Subsubactividad 1 de 2 de 7','Descripción de la subsubactividad 1 de 2 de 7',NULL,'pases_enviados','CASO-7-SUB2-SUB1',NULL,59,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-03-28',2,4),(61,'Actividad Principal 8','Descripción de la actividad principal 8',NULL,'pausada','CASO-008',NULL,NULL,'2025-09-13 01:18:31','2025-09-13 01:18:31','2024-12-18',5,4),(62,'Subactividad 1 de 8','Descripción de la subactividad 1 de 8',NULL,'pases_enviados','CASO-8-SUB1',NULL,61,'2025-09-13 01:18:31','2025-09-13 01:18:31','2024-10-24',3,4),(63,'Subsubactividad 1 de 1 de 8','Descripción de la subsubactividad 1 de 1 de 8',NULL,'culminada','CASO-8-SUB1-SUB1',NULL,62,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-08-13',4,4),(64,'Subactividad 2 de 8','Descripción de la subactividad 2 de 8',NULL,'en_ejecucion','CASO-8-SUB2',NULL,61,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-05-27',1,5),(65,'Subsubactividad 1 de 2 de 8','Descripción de la subsubactividad 1 de 2 de 8',NULL,'pausada','CASO-8-SUB2-SUB1',NULL,64,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-03-16',2,2),(66,'Subsubactividad 2 de 2 de 8','Descripción de la subsubactividad 2 de 2 de 8',NULL,'cancelada','CASO-8-SUB2-SUB2',NULL,64,'2025-09-13 01:18:31','2025-09-13 01:18:31','2024-11-25',5,5),(67,'Subactividad 3 de 8','Descripción de la subactividad 3 de 8',NULL,'no_iniciada','CASO-8-SUB3',NULL,61,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-01-24',3,4),(68,'Subsubactividad 1 de 3 de 8','Descripción de la subsubactividad 1 de 3 de 8',NULL,'culminada','CASO-8-SUB3-SUB1',NULL,67,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-07-18',2,1),(69,'Actividad Principal 9','Descripción de la actividad principal 9',NULL,'en_ejecucion','CASO-009',NULL,NULL,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-05-30',1,2),(70,'Subactividad 1 de 9','Descripción de la subactividad 1 de 9',NULL,'en_certificacion_por_cliente','CASO-9-SUB1',NULL,69,'2025-09-13 01:18:31','2025-09-13 01:18:31','2024-09-20',3,2),(71,'Subsubactividad 1 de 1 de 9','Descripción de la subsubactividad 1 de 1 de 9',NULL,'en_certificacion_por_cliente','CASO-9-SUB1-SUB1',NULL,70,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-08-09',2,3),(72,'Subsubactividad 2 de 1 de 9','Descripción de la subsubactividad 2 de 1 de 9',NULL,'pausada','CASO-9-SUB1-SUB2',NULL,70,'2025-09-13 01:18:31','2025-09-13 01:18:31','2024-10-01',2,2),(73,'Subactividad 2 de 9','Descripción de la subactividad 2 de 9',NULL,'cancelada','CASO-9-SUB2',NULL,69,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-01-06',4,5),(74,'Subsubactividad 1 de 2 de 9','Descripción de la subsubactividad 1 de 2 de 9',NULL,'culminada','CASO-9-SUB2-SUB1',NULL,73,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-05-26',5,1),(75,'Subsubactividad 2 de 2 de 9','Descripción de la subsubactividad 2 de 2 de 9',NULL,'en_ejecucion','CASO-9-SUB2-SUB2',NULL,73,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-02-27',5,1),(76,'Subactividad 3 de 9','Descripción de la subactividad 3 de 9',NULL,'en_ejecucion','CASO-9-SUB3',NULL,69,'2025-09-13 01:18:31','2025-09-13 01:18:31','2024-10-22',4,1),(77,'Subsubactividad 1 de 3 de 9','Descripción de la subsubactividad 1 de 3 de 9',NULL,'en_certificacion_por_cliente','CASO-9-SUB3-SUB1',NULL,76,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-05-07',2,4),(78,'Subsubactividad 2 de 3 de 9','Descripción de la subsubactividad 2 de 3 de 9',NULL,'pases_enviados','CASO-9-SUB3-SUB2',NULL,76,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-04-21',4,4),(79,'Subactividad 4 de 9','Descripción de la subactividad 4 de 9',NULL,'no_iniciada','CASO-9-SUB4',NULL,69,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-01-27',5,2),(80,'Subsubactividad 1 de 4 de 9','Descripción de la subsubactividad 1 de 4 de 9',NULL,'pases_enviados','CASO-9-SUB4-SUB1',NULL,79,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-06-22',2,5),(81,'Actividad Principal 10','Descripción de la actividad principal 10',NULL,'en_ejecucion','CASO-010',NULL,NULL,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-07-24',4,3),(82,'Subactividad 1 de 10','Descripción de la subactividad 1 de 10',NULL,'pases_enviados','CASO-10-SUB1',NULL,81,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-01-30',5,4),(83,'Subsubactividad 1 de 1 de 10','Descripción de la subsubactividad 1 de 1 de 10',NULL,'en_certificacion_por_cliente','CASO-10-SUB1-SUB1',NULL,82,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-06-21',2,3),(84,'Subsubactividad 2 de 1 de 10','Descripción de la subsubactividad 2 de 1 de 10',NULL,'en_espera_de_insumos','CASO-10-SUB1-SUB2',NULL,82,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-02-22',2,1),(85,'Subactividad 2 de 10','Descripción de la subactividad 2 de 10',NULL,'no_iniciada','CASO-10-SUB2',NULL,81,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-03-10',3,1),(86,'Subsubactividad 1 de 2 de 10','Descripción de la subsubactividad 1 de 2 de 10',NULL,'en_certificacion_por_cliente','CASO-10-SUB2-SUB1',NULL,85,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-02-06',5,1),(87,'Subsubactividad 2 de 2 de 10','Descripción de la subsubactividad 2 de 2 de 10',NULL,'pausada','CASO-10-SUB2-SUB2',NULL,85,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-05-18',4,5),(88,'Actividad Principal 11','Descripción de la actividad principal 11',NULL,'pausada','CASO-011',NULL,NULL,'2025-09-13 01:18:31','2025-09-13 01:18:31','2025-05-01',5,5),(89,'Subactividad 1 de 11','Descripción de la subactividad 1 de 11',NULL,'no_iniciada','CASO-11-SUB1',NULL,88,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-01-23',5,3),(90,'Subsubactividad 1 de 1 de 11','Descripción de la subsubactividad 1 de 1 de 11',NULL,'no_iniciada','CASO-11-SUB1-SUB1',NULL,89,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-03-11',5,2),(91,'Subactividad 2 de 11','Descripción de la subactividad 2 de 11',NULL,'en_certificacion_por_cliente','CASO-11-SUB2',NULL,88,'2025-09-13 01:18:32','2025-09-13 01:18:32','2024-11-16',4,2),(92,'Subsubactividad 1 de 2 de 11','Descripción de la subsubactividad 1 de 2 de 11',NULL,'no_iniciada','CASO-11-SUB2-SUB1',NULL,91,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-04-03',3,5),(93,'Subactividad 3 de 11','Descripción de la subactividad 3 de 11',NULL,'culminada','CASO-11-SUB3',NULL,88,'2025-09-13 01:18:32','2025-09-13 01:18:32','2024-11-06',2,3),(94,'Subsubactividad 1 de 3 de 11','Descripción de la subsubactividad 1 de 3 de 11',NULL,'pases_enviados','CASO-11-SUB3-SUB1',NULL,93,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-08-18',1,3),(95,'Actividad Principal 12','Descripción de la actividad principal 12',NULL,'en_espera_de_insumos','CASO-012',NULL,NULL,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-07-02',3,3),(96,'Subactividad 1 de 12','Descripción de la subactividad 1 de 12',NULL,'en_ejecucion','CASO-12-SUB1',NULL,95,'2025-09-13 01:18:32','2025-09-13 01:18:32','2024-11-20',5,1),(97,'Subsubactividad 1 de 1 de 12','Descripción de la subsubactividad 1 de 1 de 12',NULL,'cancelada','CASO-12-SUB1-SUB1',NULL,96,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-01-30',2,1),(98,'Subsubactividad 2 de 1 de 12','Descripción de la subsubactividad 2 de 1 de 12',NULL,'pases_enviados','CASO-12-SUB1-SUB2',NULL,96,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-07-08',5,5),(99,'Subactividad 2 de 12','Descripción de la subactividad 2 de 12',NULL,'en_ejecucion','CASO-12-SUB2',NULL,95,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-04-28',2,5),(100,'Subsubactividad 1 de 2 de 12','Descripción de la subsubactividad 1 de 2 de 12',NULL,'pausada','CASO-12-SUB2-SUB1',NULL,99,'2025-09-13 01:18:32','2025-09-13 01:18:32','2024-10-29',5,2),(101,'Actividad Principal 13','Descripción de la actividad principal 13',NULL,'pases_enviados','CASO-013',NULL,NULL,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-03-26',2,5),(102,'Subactividad 1 de 13','Descripción de la subactividad 1 de 13',NULL,'en_certificacion_por_cliente','CASO-13-SUB1',NULL,101,'2025-09-13 01:18:32','2025-09-13 01:18:32','2024-09-13',2,1),(103,'Subsubactividad 1 de 1 de 13','Descripción de la subsubactividad 1 de 1 de 13',NULL,'en_certificacion_por_cliente','CASO-13-SUB1-SUB1',NULL,102,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-04-30',4,4),(104,'Subactividad 2 de 13','Descripción de la subactividad 2 de 13',NULL,'en_ejecucion','CASO-13-SUB2',NULL,101,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-05-30',4,1),(105,'Subsubactividad 1 de 2 de 13','Descripción de la subsubactividad 1 de 2 de 13',NULL,'no_iniciada','CASO-13-SUB2-SUB1',NULL,104,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-06-16',5,5),(106,'Subsubactividad 2 de 2 de 13','Descripción de la subsubactividad 2 de 2 de 13',NULL,'pases_enviados','CASO-13-SUB2-SUB2',NULL,104,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-05-10',5,5),(107,'Actividad Principal 14','Descripción de la actividad principal 14',NULL,'en_certificacion_por_cliente','CASO-014',NULL,NULL,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-09-09',2,5),(108,'Subactividad 1 de 14','Descripción de la subactividad 1 de 14',NULL,'no_iniciada','CASO-14-SUB1',NULL,107,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-03-16',4,5),(109,'Subsubactividad 1 de 1 de 14','Descripción de la subsubactividad 1 de 1 de 14',NULL,'cancelada','CASO-14-SUB1-SUB1',NULL,108,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-01-22',3,5),(110,'Subsubactividad 2 de 1 de 14','Descripción de la subsubactividad 2 de 1 de 14',NULL,'en_ejecucion','CASO-14-SUB1-SUB2',NULL,108,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-02-19',2,3),(111,'Subactividad 2 de 14','Descripción de la subactividad 2 de 14',NULL,'en_ejecucion','CASO-14-SUB2',NULL,107,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-04-02',1,4),(112,'Subsubactividad 1 de 2 de 14','Descripción de la subsubactividad 1 de 2 de 14',NULL,'no_iniciada','CASO-14-SUB2-SUB1',NULL,111,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-08-18',5,3),(113,'Actividad Principal 15','Descripción de la actividad principal 15',NULL,'culminada','CASO-015',NULL,NULL,'2025-09-13 01:18:32','2025-09-13 01:18:32','2024-09-26',4,4),(114,'Subactividad 1 de 15','Descripción de la subactividad 1 de 15',NULL,'cancelada','CASO-15-SUB1',NULL,113,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-03-31',4,1),(115,'Subsubactividad 1 de 1 de 15','Descripción de la subsubactividad 1 de 1 de 15',NULL,'en_certificacion_por_cliente','CASO-15-SUB1-SUB1',NULL,114,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-08-18',4,1),(116,'Subactividad 2 de 15','Descripción de la subactividad 2 de 15',NULL,'en_certificacion_por_cliente','CASO-15-SUB2',NULL,113,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-02-25',3,5),(117,'Subsubactividad 1 de 2 de 15','Descripción de la subsubactividad 1 de 2 de 15',NULL,'pases_enviados','CASO-15-SUB2-SUB1',NULL,116,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-08-20',2,1),(118,'Subactividad 3 de 15','Descripción de la subactividad 3 de 15',NULL,'pases_enviados','CASO-15-SUB3',NULL,113,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-01-01',3,2),(119,'Subsubactividad 1 de 3 de 15','Descripción de la subsubactividad 1 de 3 de 15',NULL,'cancelada','CASO-15-SUB3-SUB1',NULL,118,'2025-09-13 01:18:32','2025-09-13 01:18:32','2024-10-28',2,4),(120,'Subsubactividad 2 de 3 de 15','Descripción de la subsubactividad 2 de 3 de 15',NULL,'pases_enviados','CASO-15-SUB3-SUB2',NULL,118,'2025-09-13 01:18:32','2025-09-13 01:18:32','2024-12-13',2,5),(121,'Actividad Principal 16','Descripción de la actividad principal 16',NULL,'pausada','CASO-016',NULL,NULL,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-08-12',2,4),(122,'Subactividad 1 de 16','Descripción de la subactividad 1 de 16',NULL,'en_certificacion_por_cliente','CASO-16-SUB1',NULL,121,'2025-09-13 01:18:32','2025-09-13 01:18:32','2024-11-25',5,5),(123,'Subsubactividad 1 de 1 de 16','Descripción de la subsubactividad 1 de 1 de 16',NULL,'en_certificacion_por_cliente','CASO-16-SUB1-SUB1',NULL,122,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-02-27',3,2),(124,'Subsubactividad 2 de 1 de 16','Descripción de la subsubactividad 2 de 1 de 16',NULL,'no_iniciada','CASO-16-SUB1-SUB2',NULL,122,'2025-09-13 01:18:32','2025-09-13 01:18:32','2024-10-23',5,2),(125,'Subactividad 2 de 16','Descripción de la subactividad 2 de 16',NULL,'en_certificacion_por_cliente','CASO-16-SUB2',NULL,121,'2025-09-13 01:18:32','2025-09-13 01:18:32','2024-09-15',4,3),(126,'Subsubactividad 1 de 2 de 16','Descripción de la subsubactividad 1 de 2 de 16',NULL,'pausada','CASO-16-SUB2-SUB1',NULL,125,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-04-20',3,5),(127,'Subsubactividad 2 de 2 de 16','Descripción de la subsubactividad 2 de 2 de 16',NULL,'pases_enviados','CASO-16-SUB2-SUB2',NULL,125,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-02-04',2,2),(128,'Subactividad 3 de 16','Descripción de la subactividad 3 de 16',NULL,'pases_enviados','CASO-16-SUB3',NULL,121,'2025-09-13 01:18:32','2025-09-13 01:18:32','2024-10-17',1,1),(129,'Subsubactividad 1 de 3 de 16','Descripción de la subsubactividad 1 de 3 de 16',NULL,'en_certificacion_por_cliente','CASO-16-SUB3-SUB1',NULL,128,'2025-09-13 01:18:32','2025-09-13 01:18:32','2024-12-26',3,2),(130,'Subsubactividad 2 de 3 de 16','Descripción de la subsubactividad 2 de 3 de 16',NULL,'en_ejecucion','CASO-16-SUB3-SUB2',NULL,128,'2025-09-13 01:18:32','2025-09-13 01:18:32','2024-11-22',1,1),(131,'Actividad Principal 17','Descripción de la actividad principal 17',NULL,'culminada','CASO-017',NULL,NULL,'2025-09-13 01:18:32','2025-09-13 01:18:32','2025-02-21',4,3),(132,'Subactividad 1 de 17','Descripción de la subactividad 1 de 17',NULL,'en_certificacion_por_cliente','CASO-17-SUB1',NULL,131,'2025-09-13 01:18:32','2025-09-13 01:18:32','2024-10-25',5,2),(133,'Subsubactividad 1 de 1 de 17','Descripción de la subsubactividad 1 de 1 de 17',NULL,'pausada','CASO-17-SUB1-SUB1',NULL,132,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-07-14',5,3),(134,'Subactividad 2 de 17','Descripción de la subactividad 2 de 17',NULL,'cancelada','CASO-17-SUB2',NULL,131,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-03-09',4,1),(135,'Subsubactividad 1 de 2 de 17','Descripción de la subsubactividad 1 de 2 de 17',NULL,'en_certificacion_por_cliente','CASO-17-SUB2-SUB1',NULL,134,'2025-09-13 01:18:33','2025-09-13 01:18:33','2024-10-05',5,1),(136,'Subactividad 3 de 17','Descripción de la subactividad 3 de 17',NULL,'cancelada','CASO-17-SUB3',NULL,131,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-03-21',5,3),(137,'Subsubactividad 1 de 3 de 17','Descripción de la subsubactividad 1 de 3 de 17',NULL,'culminada','CASO-17-SUB3-SUB1',NULL,136,'2025-09-13 01:18:33','2025-09-13 01:18:33','2024-10-23',4,2),(138,'Subsubactividad 2 de 3 de 17','Descripción de la subsubactividad 2 de 3 de 17',NULL,'pases_enviados','CASO-17-SUB3-SUB2',NULL,136,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-06-11',1,1),(139,'Subactividad 4 de 17','Descripción de la subactividad 4 de 17',NULL,'culminada','CASO-17-SUB4',NULL,131,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-03-19',5,5),(140,'Subsubactividad 1 de 4 de 17','Descripción de la subsubactividad 1 de 4 de 17',NULL,'no_iniciada','CASO-17-SUB4-SUB1',NULL,139,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-06-26',4,5),(141,'Subsubactividad 2 de 4 de 17','Descripción de la subsubactividad 2 de 4 de 17',NULL,'pases_enviados','CASO-17-SUB4-SUB2',NULL,139,'2025-09-13 01:18:33','2025-09-13 01:18:33','2024-11-15',5,1),(142,'Actividad Principal 18','Descripción de la actividad principal 18',NULL,'en_espera_de_insumos','CASO-018',NULL,NULL,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-01-29',5,3),(143,'Subactividad 1 de 18','Descripción de la subactividad 1 de 18',NULL,'en_ejecucion','CASO-18-SUB1',NULL,142,'2025-09-13 01:18:33','2025-09-13 01:18:33','2024-10-11',2,1),(144,'Subsubactividad 1 de 1 de 18','Descripción de la subsubactividad 1 de 1 de 18',NULL,'pausada','CASO-18-SUB1-SUB1',NULL,143,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-05-14',5,1),(145,'Subactividad 2 de 18','Descripción de la subactividad 2 de 18',NULL,'no_iniciada','CASO-18-SUB2',NULL,142,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-02-12',2,2),(146,'Subsubactividad 1 de 2 de 18','Descripción de la subsubactividad 1 de 2 de 18',NULL,'pausada','CASO-18-SUB2-SUB1',NULL,145,'2025-09-13 01:18:33','2025-09-13 01:18:33','2024-11-20',3,2),(147,'Subactividad 3 de 18','Descripción de la subactividad 3 de 18',NULL,'culminada','CASO-18-SUB3',NULL,142,'2025-09-13 01:18:33','2025-09-13 01:18:33','2024-10-30',4,5),(148,'Subsubactividad 1 de 3 de 18','Descripción de la subsubactividad 1 de 3 de 18',NULL,'no_iniciada','CASO-18-SUB3-SUB1',NULL,147,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-02-17',5,3),(149,'Actividad Principal 19','Descripción de la actividad principal 19',NULL,'culminada','CASO-019',NULL,NULL,'2025-09-13 01:18:33','2025-09-13 01:18:33','2024-12-28',4,5),(150,'Subactividad 1 de 19','Descripción de la subactividad 1 de 19',NULL,'en_certificacion_por_cliente','CASO-19-SUB1',NULL,149,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-05-14',1,4),(151,'Subsubactividad 1 de 1 de 19','Descripción de la subsubactividad 1 de 1 de 19',NULL,'en_certificacion_por_cliente','CASO-19-SUB1-SUB1',NULL,150,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-02-05',5,2),(152,'Subsubactividad 2 de 1 de 19','Descripción de la subsubactividad 2 de 1 de 19',NULL,'no_iniciada','CASO-19-SUB1-SUB2',NULL,150,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-01-31',4,4),(153,'Subactividad 2 de 19','Descripción de la subactividad 2 de 19',NULL,'cancelada','CASO-19-SUB2',NULL,149,'2025-09-13 01:18:33','2025-09-13 01:18:33','2024-10-29',5,3),(154,'Subsubactividad 1 de 2 de 19','Descripción de la subsubactividad 1 de 2 de 19',NULL,'pausada','CASO-19-SUB2-SUB1',NULL,153,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-06-04',2,1),(155,'Subsubactividad 2 de 2 de 19','Descripción de la subsubactividad 2 de 2 de 19',NULL,'en_ejecucion','CASO-19-SUB2-SUB2',NULL,153,'2025-09-13 01:18:33','2025-09-13 01:18:33','2024-12-06',5,5),(156,'Actividad Principal 20','Descripción de la actividad principal 20',NULL,'culminada','CASO-020',NULL,NULL,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-04-24',3,5),(157,'Subactividad 1 de 20','Descripción de la subactividad 1 de 20',NULL,'culminada','CASO-20-SUB1',NULL,156,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-05-29',1,1),(158,'Subsubactividad 1 de 1 de 20','Descripción de la subsubactividad 1 de 1 de 20',NULL,'en_espera_de_insumos','CASO-20-SUB1-SUB1',NULL,157,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-06-09',2,2),(159,'Subsubactividad 2 de 1 de 20','Descripción de la subsubactividad 2 de 1 de 20',NULL,'pases_enviados','CASO-20-SUB1-SUB2',NULL,157,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-03-13',4,2),(160,'Subactividad 2 de 20','Descripción de la subactividad 2 de 20',NULL,'en_espera_de_insumos','CASO-20-SUB2',NULL,156,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-06-09',1,3),(161,'Subsubactividad 1 de 2 de 20','Descripción de la subsubactividad 1 de 2 de 20',NULL,'en_ejecucion','CASO-20-SUB2-SUB1',NULL,160,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-08-11',4,5),(162,'Subsubactividad 2 de 2 de 20','Descripción de la subsubactividad 2 de 2 de 20',NULL,'culminada','CASO-20-SUB2-SUB2',NULL,160,'2025-09-13 01:18:33','2025-09-13 01:18:33','2024-11-03',1,1),(163,'Subactividad 3 de 20','Descripción de la subactividad 3 de 20',NULL,'pausada','CASO-20-SUB3',NULL,156,'2025-09-13 01:18:33','2025-09-13 01:18:33','2024-11-22',1,3),(164,'Subsubactividad 1 de 3 de 20','Descripción de la subsubactividad 1 de 3 de 20',NULL,'en_certificacion_por_cliente','CASO-20-SUB3-SUB1',NULL,163,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-05-06',1,4),(165,'Subsubactividad 2 de 3 de 20','Descripción de la subsubactividad 2 de 3 de 20',NULL,'no_iniciada','CASO-20-SUB3-SUB2',NULL,163,'2025-09-13 01:18:33','2025-09-13 01:18:33','2025-04-27',5,5);
/*!40000 ALTER TABLE `activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_analista`
--

DROP TABLE IF EXISTS `activity_analista`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_analista` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activity_id` bigint unsigned NOT NULL,
  `analista_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_user_activity_id_foreign` (`activity_id`),
  KEY `activity_analista_analista_id_foreign` (`analista_id`),
  CONSTRAINT `activity_analista_analista_id_foreign` FOREIGN KEY (`analista_id`) REFERENCES `analistas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_user_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=260 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_analista`
--

LOCK TABLES `activity_analista` WRITE;
/*!40000 ALTER TABLE `activity_analista` DISABLE KEYS */;
INSERT INTO `activity_analista` VALUES (1,1,4,NULL,NULL),(2,1,5,NULL,NULL),(3,2,2,NULL,NULL),(4,2,3,NULL,NULL),(5,3,1,NULL,NULL),(6,4,4,NULL,NULL),(7,4,5,NULL,NULL),(8,5,1,NULL,NULL),(9,5,2,NULL,NULL),(10,6,4,NULL,NULL),(11,6,5,NULL,NULL),(12,7,4,NULL,NULL),(13,8,4,NULL,NULL),(14,9,3,NULL,NULL),(15,9,5,NULL,NULL),(16,10,1,NULL,NULL),(17,10,3,NULL,NULL),(18,11,2,NULL,NULL),(19,12,3,NULL,NULL),(20,13,1,NULL,NULL),(21,13,5,NULL,NULL),(22,14,5,NULL,NULL),(23,15,6,NULL,NULL),(24,16,1,NULL,NULL),(25,17,1,NULL,NULL),(26,17,2,NULL,NULL),(27,18,4,NULL,NULL),(28,19,6,NULL,NULL),(29,20,5,NULL,NULL),(30,21,3,NULL,NULL),(31,21,6,NULL,NULL),(32,22,2,NULL,NULL),(33,22,4,NULL,NULL),(34,23,1,NULL,NULL),(35,23,2,NULL,NULL),(36,24,1,NULL,NULL),(37,25,4,NULL,NULL),(38,26,2,NULL,NULL),(39,27,1,NULL,NULL),(40,28,2,NULL,NULL),(41,29,4,NULL,NULL),(42,29,6,NULL,NULL),(43,30,1,NULL,NULL),(44,30,4,NULL,NULL),(45,31,3,NULL,NULL),(46,31,5,NULL,NULL),(47,32,2,NULL,NULL),(48,33,3,NULL,NULL),(49,33,5,NULL,NULL),(50,34,5,NULL,NULL),(51,34,6,NULL,NULL),(52,35,4,NULL,NULL),(53,36,1,NULL,NULL),(54,37,3,NULL,NULL),(55,37,4,NULL,NULL),(56,38,6,NULL,NULL),(57,39,5,NULL,NULL),(58,40,2,NULL,NULL),(59,40,3,NULL,NULL),(60,41,6,NULL,NULL),(61,42,1,NULL,NULL),(62,42,5,NULL,NULL),(63,43,1,NULL,NULL),(64,44,4,NULL,NULL),(65,44,5,NULL,NULL),(66,44,6,NULL,NULL),(67,45,4,NULL,NULL),(68,45,5,NULL,NULL),(69,46,3,NULL,NULL),(70,47,3,NULL,NULL),(71,47,5,NULL,NULL),(72,48,1,NULL,NULL),(73,48,2,NULL,NULL),(74,49,2,NULL,NULL),(75,50,2,NULL,NULL),(76,50,5,NULL,NULL),(77,51,3,NULL,NULL),(78,52,2,NULL,NULL),(79,52,4,NULL,NULL),(80,53,4,NULL,NULL),(81,53,6,NULL,NULL),(82,54,2,NULL,NULL),(83,54,4,NULL,NULL),(84,55,4,NULL,NULL),(85,56,3,NULL,NULL),(86,56,4,NULL,NULL),(87,57,1,NULL,NULL),(88,58,1,NULL,NULL),(89,58,4,NULL,NULL),(90,59,1,NULL,NULL),(91,59,3,NULL,NULL),(92,60,6,NULL,NULL),(93,61,1,NULL,NULL),(94,61,3,NULL,NULL),(95,61,6,NULL,NULL),(96,62,1,NULL,NULL),(97,63,6,NULL,NULL),(98,64,2,NULL,NULL),(99,64,4,NULL,NULL),(100,65,1,NULL,NULL),(101,65,4,NULL,NULL),(102,66,3,NULL,NULL),(103,66,4,NULL,NULL),(104,67,6,NULL,NULL),(105,68,2,NULL,NULL),(106,68,4,NULL,NULL),(107,69,1,NULL,NULL),(108,69,2,NULL,NULL),(109,69,4,NULL,NULL),(110,70,6,NULL,NULL),(111,71,3,NULL,NULL),(112,71,4,NULL,NULL),(113,72,1,NULL,NULL),(114,73,5,NULL,NULL),(115,73,6,NULL,NULL),(116,74,5,NULL,NULL),(117,75,5,NULL,NULL),(118,76,4,NULL,NULL),(119,77,3,NULL,NULL),(120,77,5,NULL,NULL),(121,78,3,NULL,NULL),(122,79,1,NULL,NULL),(123,80,1,NULL,NULL),(124,81,1,NULL,NULL),(125,81,5,NULL,NULL),(126,81,6,NULL,NULL),(127,82,1,NULL,NULL),(128,83,5,NULL,NULL),(129,84,1,NULL,NULL),(130,84,2,NULL,NULL),(131,85,6,NULL,NULL),(132,86,4,NULL,NULL),(133,86,6,NULL,NULL),(134,87,4,NULL,NULL),(135,88,3,NULL,NULL),(136,88,4,NULL,NULL),(137,89,3,NULL,NULL),(138,89,6,NULL,NULL),(139,90,6,NULL,NULL),(140,91,1,NULL,NULL),(141,92,1,NULL,NULL),(142,92,2,NULL,NULL),(143,93,1,NULL,NULL),(144,94,3,NULL,NULL),(145,95,2,NULL,NULL),(146,95,5,NULL,NULL),(147,95,6,NULL,NULL),(148,96,4,NULL,NULL),(149,96,5,NULL,NULL),(150,97,1,NULL,NULL),(151,98,3,NULL,NULL),(152,98,4,NULL,NULL),(153,99,3,NULL,NULL),(154,99,6,NULL,NULL),(155,100,1,NULL,NULL),(156,100,2,NULL,NULL),(157,101,4,NULL,NULL),(158,101,6,NULL,NULL),(159,102,4,NULL,NULL),(160,103,4,NULL,NULL),(161,104,1,NULL,NULL),(162,104,5,NULL,NULL),(163,105,3,NULL,NULL),(164,105,6,NULL,NULL),(165,106,2,NULL,NULL),(166,106,5,NULL,NULL),(167,107,2,NULL,NULL),(168,107,5,NULL,NULL),(169,107,6,NULL,NULL),(170,108,1,NULL,NULL),(171,108,5,NULL,NULL),(172,109,2,NULL,NULL),(173,109,4,NULL,NULL),(174,110,6,NULL,NULL),(175,111,1,NULL,NULL),(176,111,6,NULL,NULL),(177,112,3,NULL,NULL),(178,113,2,NULL,NULL),(179,113,4,NULL,NULL),(180,113,5,NULL,NULL),(181,114,2,NULL,NULL),(182,114,4,NULL,NULL),(183,115,5,NULL,NULL),(184,115,6,NULL,NULL),(185,116,3,NULL,NULL),(186,117,5,NULL,NULL),(187,117,6,NULL,NULL),(188,118,1,NULL,NULL),(189,118,2,NULL,NULL),(190,119,4,NULL,NULL),(191,119,5,NULL,NULL),(192,120,1,NULL,NULL),(193,120,3,NULL,NULL),(194,121,1,NULL,NULL),(195,121,2,NULL,NULL),(196,121,4,NULL,NULL),(197,122,4,NULL,NULL),(198,123,3,NULL,NULL),(199,123,4,NULL,NULL),(200,124,4,NULL,NULL),(201,124,6,NULL,NULL),(202,125,3,NULL,NULL),(203,125,6,NULL,NULL),(204,126,4,NULL,NULL),(205,126,5,NULL,NULL),(206,127,3,NULL,NULL),(207,128,4,NULL,NULL),(208,129,4,NULL,NULL),(209,129,5,NULL,NULL),(210,130,4,NULL,NULL),(211,131,6,NULL,NULL),(212,132,5,NULL,NULL),(213,132,6,NULL,NULL),(214,133,4,NULL,NULL),(215,134,1,NULL,NULL),(216,134,4,NULL,NULL),(217,135,5,NULL,NULL),(218,136,4,NULL,NULL),(219,136,6,NULL,NULL),(220,137,4,NULL,NULL),(221,138,1,NULL,NULL),(222,139,5,NULL,NULL),(223,140,2,NULL,NULL),(224,140,3,NULL,NULL),(225,141,6,NULL,NULL),(226,142,6,NULL,NULL),(227,143,6,NULL,NULL),(228,144,6,NULL,NULL),(229,145,1,NULL,NULL),(230,145,2,NULL,NULL),(231,146,6,NULL,NULL),(232,147,1,NULL,NULL),(233,147,3,NULL,NULL),(234,148,4,NULL,NULL),(235,149,4,NULL,NULL),(236,150,1,NULL,NULL),(237,150,4,NULL,NULL),(238,151,2,NULL,NULL),(239,151,3,NULL,NULL),(240,152,6,NULL,NULL),(241,153,4,NULL,NULL),(242,154,1,NULL,NULL),(243,155,1,NULL,NULL),(244,155,4,NULL,NULL),(245,156,1,NULL,NULL),(246,157,1,NULL,NULL),(247,157,6,NULL,NULL),(248,158,3,NULL,NULL),(249,159,5,NULL,NULL),(250,160,4,NULL,NULL),(251,160,5,NULL,NULL),(252,161,5,NULL,NULL),(253,161,6,NULL,NULL),(254,162,4,NULL,NULL),(255,163,1,NULL,NULL),(256,164,2,NULL,NULL),(257,164,3,NULL,NULL),(258,165,2,NULL,NULL),(259,165,6,NULL,NULL);
/*!40000 ALTER TABLE `activity_analista` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_statuses`
--

DROP TABLE IF EXISTS `activity_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activity_id` bigint unsigned NOT NULL,
  `status_id` bigint unsigned NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `activity_statuses_activity_id_status_id_unique` (`activity_id`,`status_id`),
  KEY `activity_statuses_status_id_foreign` (`status_id`),
  CONSTRAINT `activity_statuses_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_statuses_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_statuses`
--

LOCK TABLES `activity_statuses` WRITE;
/*!40000 ALTER TABLE `activity_statuses` DISABLE KEYS */;
INSERT INTO `activity_statuses` VALUES (6,2,8,'2025-09-13 02:30:47','2025-09-13 02:30:47','2025-09-13 02:30:47'),(7,6,6,'2025-09-13 02:31:02','2025-09-13 02:31:02','2025-09-13 02:31:02'),(10,1,6,'2025-09-16 13:58:04','2025-09-16 13:58:04','2025-09-16 13:58:04'),(11,9,6,'2025-09-16 13:58:19','2025-09-16 13:58:19','2025-09-16 13:58:19');
/*!40000 ALTER TABLE `activity_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `analistas`
--

DROP TABLE IF EXISTS `analistas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `analistas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `analistas`
--

LOCK TABLES `analistas` WRITE;
/*!40000 ALTER TABLE `analistas` DISABLE KEYS */;
INSERT INTO `analistas` VALUES (1,'Analista 1','2025-09-13 01:18:30','2025-09-13 01:18:30'),(2,'Analista 2','2025-09-13 01:18:30','2025-09-13 01:18:30'),(3,'Analista 3','2025-09-13 01:18:30','2025-09-13 01:18:30'),(4,'Analista 4','2025-09-13 01:18:30','2025-09-13 01:18:30'),(5,'Analista 5','2025-09-13 01:18:30','2025-09-13 01:18:30'),(6,'Analista 6','2025-09-13 01:18:30','2025-09-13 01:18:30');
/*!40000 ALTER TABLE `analistas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activity_id` bigint unsigned NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comments_activity_id_foreign` (`activity_id`),
  CONSTRAINT `comments_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emails`
--

DROP TABLE IF EXISTS `emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emails` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activity_id` bigint unsigned NOT NULL,
  `type` enum('sent','received') COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_recipient` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachments` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `emails_activity_id_foreign` (`activity_id`),
  CONSTRAINT `emails_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emails`
--

LOCK TABLES `emails` WRITE;
/*!40000 ALTER TABLE `emails` DISABLE KEYS */;
/*!40000 ALTER TABLE `emails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_08_09_133150_create_activities_table',1),(5,'2025_08_09_155348_create_activity_user_table',1),(6,'2025_08_11_182345_create_requirements_table',1),(7,'2025_08_12_175242_add_recepcion_date_to_activities_table',1),(8,'2025_08_13_234309_add_caso_to_activities_table',1),(9,'2025_08_14_134700_add_parent_id_to_activities_table',1),(10,'2025_08_20_155713_create_comments_table',1),(11,'2025_08_20_185625_create_analistas_table',1),(12,'2025_08_20_185646_rename_activity_user_to_activity_analista',1),(13,'2025_08_20_192234_fix_activity_analista_foreign_key',1),(14,'2025_08_21_132634_create_emails_table',1),(15,'2025_08_21_134707_make_sender_recipient_nullable_in_emails_table',1),(16,'2025_08_21_135831_modify_attachments_field_in_emails_table',1),(17,'2025_08_22_193456_add_status_and_dates_to_requirements_table',1),(18,'2025_08_23_195851_create_statuses_table',1),(19,'2025_08_23_195912_create_activity_statuses_table',1),(20,'2025_08_23_200000_seed_statuses_table',1),(21,'2025_08_23_200039_migrate_existing_activity_statuses',1),(22,'2025_08_23_210510_add_new_statuses_and_update_existing',1),(23,'2025_09_01_070302_add_estatus_operacional_to_activities_table',1),(24,'2025_09_12_090004_add_prioridad_orden_to_activities_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `requirements`
--

DROP TABLE IF EXISTS `requirements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `requirements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activity_id` bigint unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pendiente','recibido') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `fecha_recepcion` timestamp NULL DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `requirements_activity_id_foreign` (`activity_id`),
  CONSTRAINT `requirements_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=291 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `requirements`
--

LOCK TABLES `requirements` WRITE;
/*!40000 ALTER TABLE `requirements` DISABLE KEYS */;
INSERT INTO `requirements` VALUES (1,1,'Requerimiento 1 de Actividad 1','pendiente',NULL,NULL,'2025-09-08 01:18:30','2025-09-13 01:18:30'),(2,1,'Requerimiento 2 de Actividad 1','recibido',NULL,NULL,'2025-05-30 01:18:30','2025-09-13 01:18:30'),(3,2,'Requerimiento 1 de Subactividad 1 de 1','pendiente',NULL,NULL,'2025-07-18 01:18:30','2025-09-13 01:18:30'),(4,2,'Requerimiento 2 de Subactividad 1 de 1','recibido',NULL,NULL,'2024-11-05 01:18:30','2025-09-13 01:18:30'),(5,3,'Requerimiento 1 de Subsubactividad 1 de 1 de 1','pendiente',NULL,NULL,'2025-01-01 01:18:30','2025-09-13 01:18:30'),(6,4,'Requerimiento 1 de Subactividad 2 de 1','recibido',NULL,NULL,'2024-10-23 01:18:30','2025-09-13 01:18:30'),(7,5,'Requerimiento 1 de Subsubactividad 1 de 2 de 1','recibido',NULL,NULL,'2025-03-05 01:18:30','2025-09-13 01:18:30'),(8,6,'Requerimiento 1 de Subactividad 3 de 1','recibido',NULL,NULL,'2025-01-15 01:18:30','2025-09-13 01:18:30'),(9,7,'Requerimiento 1 de Subsubactividad 1 de 3 de 1','pendiente',NULL,NULL,'2025-04-03 01:18:30','2025-09-13 01:18:30'),(10,7,'Requerimiento 2 de Subsubactividad 1 de 3 de 1','recibido',NULL,NULL,'2025-02-12 01:18:30','2025-09-13 01:18:30'),(11,8,'Requerimiento 1 de Subsubactividad 2 de 3 de 1','recibido',NULL,NULL,'2025-05-04 01:18:30','2025-09-13 01:18:30'),(12,9,'Requerimiento 1 de Actividad 2','recibido',NULL,NULL,'2025-08-24 01:18:30','2025-09-13 01:18:30'),(13,9,'Requerimiento 2 de Actividad 2','pendiente',NULL,NULL,'2025-03-30 01:18:30','2025-09-13 01:18:30'),(14,10,'Requerimiento 1 de Subactividad 1 de 2','recibido',NULL,NULL,'2025-04-20 01:18:30','2025-09-13 01:18:30'),(15,10,'Requerimiento 2 de Subactividad 1 de 2','recibido',NULL,NULL,'2024-09-29 01:18:30','2025-09-13 01:18:30'),(16,11,'Requerimiento 1 de Subsubactividad 1 de 1 de 2','pendiente',NULL,NULL,'2025-02-17 01:18:30','2025-09-13 01:18:30'),(17,11,'Requerimiento 2 de Subsubactividad 1 de 1 de 2','pendiente',NULL,NULL,'2025-05-30 01:18:30','2025-09-13 01:18:30'),(18,12,'Requerimiento 1 de Subsubactividad 2 de 1 de 2','recibido',NULL,NULL,'2025-06-18 01:18:30','2025-09-13 01:18:30'),(19,13,'Requerimiento 1 de Subactividad 2 de 2','recibido',NULL,NULL,'2025-09-11 01:18:30','2025-09-13 01:18:30'),(20,13,'Requerimiento 2 de Subactividad 2 de 2','recibido',NULL,NULL,'2025-09-04 01:18:30','2025-09-13 01:18:30'),(21,14,'Requerimiento 1 de Subsubactividad 1 de 2 de 2','recibido',NULL,NULL,'2025-08-22 01:18:30','2025-09-13 01:18:30'),(22,14,'Requerimiento 2 de Subsubactividad 1 de 2 de 2','pendiente',NULL,NULL,'2024-09-25 01:18:30','2025-09-13 01:18:30'),(23,15,'Requerimiento 1 de Subsubactividad 2 de 2 de 2','recibido',NULL,NULL,'2025-05-30 01:18:30','2025-09-13 01:18:30'),(24,15,'Requerimiento 2 de Subsubactividad 2 de 2 de 2','pendiente',NULL,NULL,'2025-08-29 01:18:30','2025-09-13 01:18:30'),(25,16,'Requerimiento 1 de Actividad 3','pendiente',NULL,NULL,'2024-09-30 01:18:30','2025-09-13 01:18:30'),(26,16,'Requerimiento 2 de Actividad 3','recibido',NULL,NULL,'2025-01-06 01:18:30','2025-09-13 01:18:30'),(27,16,'Requerimiento 3 de Actividad 3','recibido',NULL,NULL,'2025-06-30 01:18:30','2025-09-13 01:18:30'),(28,17,'Requerimiento 1 de Subactividad 1 de 3','recibido',NULL,NULL,'2025-08-28 01:18:30','2025-09-13 01:18:30'),(29,18,'Requerimiento 1 de Subsubactividad 1 de 1 de 3','recibido',NULL,NULL,'2025-04-10 01:18:30','2025-09-13 01:18:30'),(30,18,'Requerimiento 2 de Subsubactividad 1 de 1 de 3','recibido',NULL,NULL,'2025-08-17 01:18:30','2025-09-13 01:18:30'),(31,19,'Requerimiento 1 de Subsubactividad 2 de 1 de 3','recibido',NULL,NULL,'2025-05-15 01:18:30','2025-09-13 01:18:30'),(32,19,'Requerimiento 2 de Subsubactividad 2 de 1 de 3','pendiente',NULL,NULL,'2025-09-06 01:18:30','2025-09-13 01:18:30'),(33,20,'Requerimiento 1 de Subactividad 2 de 3','recibido',NULL,NULL,'2025-05-13 01:18:30','2025-09-13 01:18:30'),(34,20,'Requerimiento 2 de Subactividad 2 de 3','recibido',NULL,NULL,'2025-06-11 01:18:30','2025-09-13 01:18:30'),(35,21,'Requerimiento 1 de Subsubactividad 1 de 2 de 3','recibido',NULL,NULL,'2024-12-22 01:18:30','2025-09-13 01:18:30'),(36,22,'Requerimiento 1 de Subsubactividad 2 de 2 de 3','recibido',NULL,NULL,'2025-05-08 01:18:30','2025-09-13 01:18:30'),(37,23,'Requerimiento 1 de Subactividad 3 de 3','pendiente',NULL,NULL,'2025-06-29 01:18:30','2025-09-13 01:18:30'),(38,23,'Requerimiento 2 de Subactividad 3 de 3','pendiente',NULL,NULL,'2025-02-23 01:18:30','2025-09-13 01:18:30'),(39,24,'Requerimiento 1 de Subsubactividad 1 de 3 de 3','pendiente',NULL,NULL,'2025-03-17 01:18:30','2025-09-13 01:18:30'),(40,25,'Requerimiento 1 de Actividad 4','recibido',NULL,NULL,'2025-04-07 01:18:30','2025-09-13 01:18:30'),(41,25,'Requerimiento 2 de Actividad 4','recibido',NULL,NULL,'2025-01-08 01:18:30','2025-09-13 01:18:30'),(42,26,'Requerimiento 1 de Subactividad 1 de 4','pendiente',NULL,NULL,'2025-06-11 01:18:30','2025-09-13 01:18:30'),(43,26,'Requerimiento 2 de Subactividad 1 de 4','pendiente',NULL,NULL,'2025-07-27 01:18:30','2025-09-13 01:18:30'),(44,26,'Requerimiento 3 de Subactividad 1 de 4','recibido',NULL,NULL,'2024-10-13 01:18:30','2025-09-13 01:18:30'),(45,27,'Requerimiento 1 de Subsubactividad 1 de 1 de 4','recibido',NULL,NULL,'2025-08-28 01:18:30','2025-09-13 01:18:30'),(46,28,'Requerimiento 1 de Subactividad 2 de 4','pendiente',NULL,NULL,'2025-09-08 01:18:30','2025-09-13 01:18:30'),(47,29,'Requerimiento 1 de Subsubactividad 1 de 2 de 4','recibido',NULL,NULL,'2025-06-28 01:18:30','2025-09-13 01:18:30'),(48,29,'Requerimiento 2 de Subsubactividad 1 de 2 de 4','pendiente',NULL,NULL,'2025-09-05 01:18:30','2025-09-13 01:18:30'),(49,30,'Requerimiento 1 de Subsubactividad 2 de 2 de 4','recibido',NULL,NULL,'2024-11-20 01:18:30','2025-09-13 01:18:30'),(50,31,'Requerimiento 1 de Subactividad 3 de 4','pendiente',NULL,NULL,'2025-05-20 01:18:30','2025-09-13 01:18:30'),(51,32,'Requerimiento 1 de Subsubactividad 1 de 3 de 4','pendiente',NULL,NULL,'2025-05-05 01:18:30','2025-09-13 01:18:30'),(52,33,'Requerimiento 1 de Subsubactividad 2 de 3 de 4','pendiente',NULL,NULL,'2025-01-18 01:18:30','2025-09-13 01:18:30'),(53,33,'Requerimiento 2 de Subsubactividad 2 de 3 de 4','pendiente',NULL,NULL,'2025-04-14 01:18:30','2025-09-13 01:18:30'),(54,34,'Requerimiento 1 de Actividad 5','pendiente',NULL,NULL,'2025-08-23 01:18:30','2025-09-13 01:18:30'),(55,34,'Requerimiento 2 de Actividad 5','pendiente',NULL,NULL,'2025-03-15 01:18:30','2025-09-13 01:18:30'),(56,35,'Requerimiento 1 de Subactividad 1 de 5','pendiente',NULL,NULL,'2024-11-11 01:18:30','2025-09-13 01:18:30'),(57,35,'Requerimiento 2 de Subactividad 1 de 5','pendiente',NULL,NULL,'2025-09-04 01:18:30','2025-09-13 01:18:30'),(58,36,'Requerimiento 1 de Subsubactividad 1 de 1 de 5','pendiente',NULL,NULL,'2025-03-19 01:18:30','2025-09-13 01:18:30'),(59,37,'Requerimiento 1 de Subsubactividad 2 de 1 de 5','pendiente',NULL,NULL,'2024-11-11 01:18:30','2025-09-13 01:18:30'),(60,37,'Requerimiento 2 de Subsubactividad 2 de 1 de 5','pendiente',NULL,NULL,'2025-08-05 01:18:30','2025-09-13 01:18:30'),(61,38,'Requerimiento 1 de Subactividad 2 de 5','pendiente',NULL,NULL,'2025-02-22 01:18:30','2025-09-13 01:18:30'),(62,38,'Requerimiento 2 de Subactividad 2 de 5','pendiente',NULL,NULL,'2024-10-19 01:18:30','2025-09-13 01:18:30'),(63,39,'Requerimiento 1 de Subsubactividad 1 de 2 de 5','recibido',NULL,NULL,'2025-03-08 01:18:30','2025-09-13 01:18:30'),(64,39,'Requerimiento 2 de Subsubactividad 1 de 2 de 5','recibido',NULL,NULL,'2024-09-15 01:18:30','2025-09-13 01:18:30'),(65,40,'Requerimiento 1 de Subsubactividad 2 de 2 de 5','recibido',NULL,NULL,'2024-12-21 01:18:31','2025-09-13 01:18:31'),(66,40,'Requerimiento 2 de Subsubactividad 2 de 2 de 5','recibido',NULL,NULL,'2024-12-15 01:18:31','2025-09-13 01:18:31'),(67,41,'Requerimiento 1 de Subactividad 3 de 5','pendiente',NULL,NULL,'2024-10-19 01:18:31','2025-09-13 01:18:31'),(68,41,'Requerimiento 2 de Subactividad 3 de 5','recibido',NULL,NULL,'2025-06-30 01:18:31','2025-09-13 01:18:31'),(69,42,'Requerimiento 1 de Subsubactividad 1 de 3 de 5','recibido',NULL,NULL,'2025-08-27 01:18:31','2025-09-13 01:18:31'),(70,43,'Requerimiento 1 de Subsubactividad 2 de 3 de 5','recibido',NULL,NULL,'2024-12-20 01:18:31','2025-09-13 01:18:31'),(71,44,'Requerimiento 1 de Actividad 6','pendiente',NULL,NULL,'2025-04-18 01:18:31','2025-09-13 01:18:31'),(72,44,'Requerimiento 2 de Actividad 6','pendiente',NULL,NULL,'2025-04-26 01:18:31','2025-09-13 01:18:31'),(73,45,'Requerimiento 1 de Subactividad 1 de 6','pendiente',NULL,NULL,'2025-05-20 01:18:31','2025-09-13 01:18:31'),(74,45,'Requerimiento 2 de Subactividad 1 de 6','recibido',NULL,NULL,'2024-12-22 01:18:31','2025-09-13 01:18:31'),(75,46,'Requerimiento 1 de Subsubactividad 1 de 1 de 6','pendiente',NULL,NULL,'2025-03-24 01:18:31','2025-09-13 01:18:31'),(76,47,'Requerimiento 1 de Subsubactividad 2 de 1 de 6','recibido',NULL,NULL,'2025-03-22 01:18:31','2025-09-13 01:18:31'),(77,47,'Requerimiento 2 de Subsubactividad 2 de 1 de 6','recibido',NULL,NULL,'2025-05-15 01:18:31','2025-09-13 01:18:31'),(78,48,'Requerimiento 1 de Subactividad 2 de 6','pendiente',NULL,NULL,'2024-12-29 01:18:31','2025-09-13 01:18:31'),(79,48,'Requerimiento 2 de Subactividad 2 de 6','recibido',NULL,NULL,'2024-11-19 01:18:31','2025-09-13 01:18:31'),(80,48,'Requerimiento 3 de Subactividad 2 de 6','pendiente',NULL,NULL,'2025-08-25 01:18:31','2025-09-13 01:18:31'),(81,49,'Requerimiento 1 de Subsubactividad 1 de 2 de 6','recibido',NULL,NULL,'2024-10-21 01:18:31','2025-09-13 01:18:31'),(82,50,'Requerimiento 1 de Subactividad 3 de 6','pendiente',NULL,NULL,'2024-12-28 01:18:31','2025-09-13 01:18:31'),(83,50,'Requerimiento 2 de Subactividad 3 de 6','pendiente',NULL,NULL,'2024-12-11 01:18:31','2025-09-13 01:18:31'),(84,51,'Requerimiento 1 de Subsubactividad 1 de 3 de 6','recibido',NULL,NULL,'2025-06-26 01:18:31','2025-09-13 01:18:31'),(85,52,'Requerimiento 1 de Subsubactividad 2 de 3 de 6','recibido',NULL,NULL,'2024-10-26 01:18:31','2025-09-13 01:18:31'),(86,53,'Requerimiento 1 de Subactividad 4 de 6','recibido',NULL,NULL,'2025-07-20 01:18:31','2025-09-13 01:18:31'),(87,53,'Requerimiento 2 de Subactividad 4 de 6','pendiente',NULL,NULL,'2025-06-14 01:18:31','2025-09-13 01:18:31'),(88,53,'Requerimiento 3 de Subactividad 4 de 6','pendiente',NULL,NULL,'2024-11-04 01:18:31','2025-09-13 01:18:31'),(89,54,'Requerimiento 1 de Subsubactividad 1 de 4 de 6','recibido',NULL,NULL,'2025-04-27 01:18:31','2025-09-13 01:18:31'),(90,55,'Requerimiento 1 de Actividad 7','recibido',NULL,NULL,'2025-03-15 01:18:31','2025-09-13 01:18:31'),(91,56,'Requerimiento 1 de Subactividad 1 de 7','recibido',NULL,NULL,'2024-12-23 01:18:31','2025-09-13 01:18:31'),(92,56,'Requerimiento 2 de Subactividad 1 de 7','pendiente',NULL,NULL,'2025-09-12 01:18:31','2025-09-13 01:18:31'),(93,57,'Requerimiento 1 de Subsubactividad 1 de 1 de 7','recibido',NULL,NULL,'2025-06-25 01:18:31','2025-09-13 01:18:31'),(94,58,'Requerimiento 1 de Subsubactividad 2 de 1 de 7','recibido',NULL,NULL,'2025-02-07 01:18:31','2025-09-13 01:18:31'),(95,59,'Requerimiento 1 de Subactividad 2 de 7','recibido',NULL,NULL,'2025-07-11 01:18:31','2025-09-13 01:18:31'),(96,59,'Requerimiento 2 de Subactividad 2 de 7','pendiente',NULL,NULL,'2024-11-10 01:18:31','2025-09-13 01:18:31'),(97,60,'Requerimiento 1 de Subsubactividad 1 de 2 de 7','pendiente',NULL,NULL,'2025-01-26 01:18:31','2025-09-13 01:18:31'),(98,60,'Requerimiento 2 de Subsubactividad 1 de 2 de 7','pendiente',NULL,NULL,'2025-01-08 01:18:31','2025-09-13 01:18:31'),(99,61,'Requerimiento 1 de Actividad 8','recibido',NULL,NULL,'2025-03-17 01:18:31','2025-09-13 01:18:31'),(100,61,'Requerimiento 2 de Actividad 8','pendiente',NULL,NULL,'2025-04-14 01:18:31','2025-09-13 01:18:31'),(101,61,'Requerimiento 3 de Actividad 8','pendiente',NULL,NULL,'2025-04-12 01:18:31','2025-09-13 01:18:31'),(102,61,'Requerimiento 4 de Actividad 8','pendiente',NULL,NULL,'2025-02-14 01:18:31','2025-09-13 01:18:31'),(103,62,'Requerimiento 1 de Subactividad 1 de 8','recibido',NULL,NULL,'2025-06-02 01:18:31','2025-09-13 01:18:31'),(104,62,'Requerimiento 2 de Subactividad 1 de 8','recibido',NULL,NULL,'2025-03-27 01:18:31','2025-09-13 01:18:31'),(105,62,'Requerimiento 3 de Subactividad 1 de 8','recibido',NULL,NULL,'2025-06-26 01:18:31','2025-09-13 01:18:31'),(106,63,'Requerimiento 1 de Subsubactividad 1 de 1 de 8','recibido',NULL,NULL,'2025-02-07 01:18:31','2025-09-13 01:18:31'),(107,64,'Requerimiento 1 de Subactividad 2 de 8','recibido',NULL,NULL,'2024-10-04 01:18:31','2025-09-13 01:18:31'),(108,65,'Requerimiento 1 de Subsubactividad 1 de 2 de 8','pendiente',NULL,NULL,'2024-10-15 01:18:31','2025-09-13 01:18:31'),(109,65,'Requerimiento 2 de Subsubactividad 1 de 2 de 8','recibido',NULL,NULL,'2024-10-13 01:18:31','2025-09-13 01:18:31'),(110,66,'Requerimiento 1 de Subsubactividad 2 de 2 de 8','pendiente',NULL,NULL,'2025-08-24 01:18:31','2025-09-13 01:18:31'),(111,67,'Requerimiento 1 de Subactividad 3 de 8','recibido',NULL,NULL,'2024-11-21 01:18:31','2025-09-13 01:18:31'),(112,67,'Requerimiento 2 de Subactividad 3 de 8','recibido',NULL,NULL,'2024-09-15 01:18:31','2025-09-13 01:18:31'),(113,67,'Requerimiento 3 de Subactividad 3 de 8','pendiente',NULL,NULL,'2025-01-30 01:18:31','2025-09-13 01:18:31'),(114,68,'Requerimiento 1 de Subsubactividad 1 de 3 de 8','recibido',NULL,NULL,'2025-03-08 01:18:31','2025-09-13 01:18:31'),(115,69,'Requerimiento 1 de Actividad 9','pendiente',NULL,NULL,'2025-09-11 01:18:31','2025-09-13 02:24:35'),(116,69,'Requerimiento 2 de Actividad 9','pendiente',NULL,NULL,'2025-03-24 01:18:31','2025-09-13 01:18:31'),(117,69,'Requerimiento 3 de Actividad 9','pendiente',NULL,NULL,'2025-03-01 01:18:31','2025-09-13 01:18:31'),(118,69,'Requerimiento 4 de Actividad 9','pendiente',NULL,NULL,'2025-04-02 01:18:31','2025-09-13 01:18:31'),(119,69,'Requerimiento 5 de Actividad 9','pendiente',NULL,NULL,'2025-04-14 01:18:31','2025-09-13 01:18:31'),(120,70,'Requerimiento 1 de Subactividad 1 de 9','pendiente',NULL,NULL,'2025-07-27 01:18:31','2025-09-13 01:18:31'),(121,70,'Requerimiento 2 de Subactividad 1 de 9','pendiente',NULL,NULL,'2025-01-13 01:18:31','2025-09-13 01:18:31'),(122,71,'Requerimiento 1 de Subsubactividad 1 de 1 de 9','pendiente',NULL,NULL,'2024-12-13 01:18:31','2025-09-13 01:18:31'),(123,71,'Requerimiento 2 de Subsubactividad 1 de 1 de 9','pendiente',NULL,NULL,'2025-07-28 01:18:31','2025-09-13 01:18:31'),(124,72,'Requerimiento 1 de Subsubactividad 2 de 1 de 9','pendiente',NULL,NULL,'2025-05-03 01:18:31','2025-09-13 01:18:31'),(125,72,'Requerimiento 2 de Subsubactividad 2 de 1 de 9','pendiente',NULL,NULL,'2025-05-08 01:18:31','2025-09-13 01:18:31'),(126,73,'Requerimiento 1 de Subactividad 2 de 9','recibido',NULL,NULL,'2024-10-14 01:18:31','2025-09-13 01:18:31'),(127,74,'Requerimiento 1 de Subsubactividad 1 de 2 de 9','recibido',NULL,NULL,'2025-04-07 01:18:31','2025-09-13 01:18:31'),(128,75,'Requerimiento 1 de Subsubactividad 2 de 2 de 9','recibido',NULL,NULL,'2024-09-19 01:18:31','2025-09-13 01:18:31'),(129,75,'Requerimiento 2 de Subsubactividad 2 de 2 de 9','pendiente',NULL,NULL,'2025-08-31 01:18:31','2025-09-13 01:18:31'),(130,76,'Requerimiento 1 de Subactividad 3 de 9','pendiente',NULL,NULL,'2025-03-31 01:18:31','2025-09-13 01:18:31'),(131,77,'Requerimiento 1 de Subsubactividad 1 de 3 de 9','pendiente',NULL,NULL,'2025-08-17 01:18:31','2025-09-13 01:18:31'),(132,78,'Requerimiento 1 de Subsubactividad 2 de 3 de 9','pendiente',NULL,NULL,'2025-05-06 01:18:31','2025-09-13 01:18:31'),(133,79,'Requerimiento 1 de Subactividad 4 de 9','recibido',NULL,NULL,'2025-03-27 01:18:31','2025-09-13 01:18:31'),(134,79,'Requerimiento 2 de Subactividad 4 de 9','recibido',NULL,NULL,'2025-01-02 01:18:31','2025-09-13 01:18:31'),(135,80,'Requerimiento 1 de Subsubactividad 1 de 4 de 9','pendiente',NULL,NULL,'2025-02-27 01:18:31','2025-09-13 01:18:31'),(136,80,'Requerimiento 2 de Subsubactividad 1 de 4 de 9','recibido',NULL,NULL,'2025-01-21 01:18:31','2025-09-13 01:18:31'),(137,81,'Requerimiento 1 de Actividad 10','pendiente',NULL,NULL,'2025-05-22 01:18:31','2025-09-13 01:18:31'),(138,82,'Requerimiento 1 de Subactividad 1 de 10','recibido',NULL,NULL,'2025-02-15 01:18:31','2025-09-13 01:18:31'),(139,82,'Requerimiento 2 de Subactividad 1 de 10','recibido',NULL,NULL,'2025-02-23 01:18:31','2025-09-13 01:18:31'),(140,82,'Requerimiento 3 de Subactividad 1 de 10','recibido',NULL,NULL,'2024-10-20 01:18:31','2025-09-13 01:18:31'),(141,83,'Requerimiento 1 de Subsubactividad 1 de 1 de 10','recibido',NULL,NULL,'2024-11-04 01:18:31','2025-09-13 01:18:31'),(142,84,'Requerimiento 1 de Subsubactividad 2 de 1 de 10','recibido',NULL,NULL,'2024-09-24 01:18:31','2025-09-13 01:18:31'),(143,85,'Requerimiento 1 de Subactividad 2 de 10','recibido',NULL,NULL,'2025-05-19 01:18:31','2025-09-13 01:18:31'),(144,85,'Requerimiento 2 de Subactividad 2 de 10','recibido',NULL,NULL,'2025-04-25 01:18:31','2025-09-13 01:18:31'),(145,85,'Requerimiento 3 de Subactividad 2 de 10','recibido',NULL,NULL,'2024-11-04 01:18:31','2025-09-13 01:18:31'),(146,86,'Requerimiento 1 de Subsubactividad 1 de 2 de 10','recibido',NULL,NULL,'2024-10-11 01:18:31','2025-09-13 01:18:31'),(147,86,'Requerimiento 2 de Subsubactividad 1 de 2 de 10','pendiente',NULL,NULL,'2025-05-10 01:18:31','2025-09-13 01:18:31'),(148,87,'Requerimiento 1 de Subsubactividad 2 de 2 de 10','pendiente',NULL,NULL,'2024-12-20 01:18:31','2025-09-13 01:18:31'),(149,88,'Requerimiento 1 de Actividad 11','recibido',NULL,NULL,'2024-09-14 01:18:32','2025-09-13 01:18:32'),(150,88,'Requerimiento 2 de Actividad 11','recibido',NULL,NULL,'2025-03-04 01:18:32','2025-09-13 01:18:32'),(151,88,'Requerimiento 3 de Actividad 11','pendiente',NULL,NULL,'2024-12-23 01:18:32','2025-09-13 01:18:32'),(152,89,'Requerimiento 1 de Subactividad 1 de 11','recibido',NULL,NULL,'2024-09-13 01:18:32','2025-09-13 01:18:32'),(153,90,'Requerimiento 1 de Subsubactividad 1 de 1 de 11','pendiente',NULL,NULL,'2025-03-19 01:18:32','2025-09-13 01:18:32'),(154,91,'Requerimiento 1 de Subactividad 2 de 11','pendiente',NULL,NULL,'2025-06-08 01:18:32','2025-09-13 01:18:32'),(155,92,'Requerimiento 1 de Subsubactividad 1 de 2 de 11','pendiente',NULL,NULL,'2025-01-19 01:18:32','2025-09-13 01:18:32'),(156,92,'Requerimiento 2 de Subsubactividad 1 de 2 de 11','recibido',NULL,NULL,'2025-07-10 01:18:32','2025-09-13 01:18:32'),(157,93,'Requerimiento 1 de Subactividad 3 de 11','pendiente',NULL,NULL,'2025-03-18 01:18:32','2025-09-13 01:18:32'),(158,93,'Requerimiento 2 de Subactividad 3 de 11','recibido',NULL,NULL,'2024-11-16 01:18:32','2025-09-13 01:18:32'),(159,94,'Requerimiento 1 de Subsubactividad 1 de 3 de 11','recibido',NULL,NULL,'2024-12-08 01:18:32','2025-09-13 01:18:32'),(160,95,'Requerimiento 1 de Actividad 12','pendiente',NULL,NULL,'2024-09-30 01:18:32','2025-09-13 01:18:32'),(161,96,'Requerimiento 1 de Subactividad 1 de 12','pendiente',NULL,NULL,'2024-09-16 01:18:32','2025-09-13 01:18:32'),(162,96,'Requerimiento 2 de Subactividad 1 de 12','recibido',NULL,NULL,'2025-03-29 01:18:32','2025-09-13 01:18:32'),(163,96,'Requerimiento 3 de Subactividad 1 de 12','recibido',NULL,NULL,'2025-08-01 01:18:32','2025-09-13 01:18:32'),(164,97,'Requerimiento 1 de Subsubactividad 1 de 1 de 12','pendiente',NULL,NULL,'2025-04-20 01:18:32','2025-09-13 01:18:32'),(165,97,'Requerimiento 2 de Subsubactividad 1 de 1 de 12','pendiente',NULL,NULL,'2025-01-10 01:18:32','2025-09-13 01:18:32'),(166,98,'Requerimiento 1 de Subsubactividad 2 de 1 de 12','recibido',NULL,NULL,'2025-03-03 01:18:32','2025-09-13 01:18:32'),(167,98,'Requerimiento 2 de Subsubactividad 2 de 1 de 12','pendiente',NULL,NULL,'2025-01-27 01:18:32','2025-09-13 01:18:32'),(168,99,'Requerimiento 1 de Subactividad 2 de 12','pendiente',NULL,NULL,'2025-01-23 01:18:32','2025-09-13 01:18:32'),(169,100,'Requerimiento 1 de Subsubactividad 1 de 2 de 12','recibido',NULL,NULL,'2024-11-24 01:18:32','2025-09-13 01:18:32'),(170,101,'Requerimiento 1 de Actividad 13','recibido',NULL,NULL,'2025-02-06 01:18:32','2025-09-13 01:18:32'),(171,101,'Requerimiento 2 de Actividad 13','recibido',NULL,NULL,'2024-11-06 01:18:32','2025-09-13 01:18:32'),(172,101,'Requerimiento 3 de Actividad 13','recibido',NULL,NULL,'2024-11-26 01:18:32','2025-09-13 01:18:32'),(173,101,'Requerimiento 4 de Actividad 13','pendiente',NULL,NULL,'2024-11-14 01:18:32','2025-09-13 01:18:32'),(174,102,'Requerimiento 1 de Subactividad 1 de 13','pendiente',NULL,NULL,'2024-12-18 01:18:32','2025-09-13 01:18:32'),(175,102,'Requerimiento 2 de Subactividad 1 de 13','pendiente',NULL,NULL,'2025-01-24 01:18:32','2025-09-13 01:18:32'),(176,102,'Requerimiento 3 de Subactividad 1 de 13','pendiente',NULL,NULL,'2025-09-06 01:18:32','2025-09-13 01:18:32'),(177,103,'Requerimiento 1 de Subsubactividad 1 de 1 de 13','recibido',NULL,NULL,'2025-04-20 01:18:32','2025-09-13 01:18:32'),(178,103,'Requerimiento 2 de Subsubactividad 1 de 1 de 13','pendiente',NULL,NULL,'2025-09-02 01:18:32','2025-09-13 01:18:32'),(179,104,'Requerimiento 1 de Subactividad 2 de 13','recibido',NULL,NULL,'2025-01-16 01:18:32','2025-09-13 01:18:32'),(180,104,'Requerimiento 2 de Subactividad 2 de 13','pendiente',NULL,NULL,'2024-11-22 01:18:32','2025-09-13 01:18:32'),(181,105,'Requerimiento 1 de Subsubactividad 1 de 2 de 13','recibido',NULL,NULL,'2025-04-24 01:18:32','2025-09-13 01:18:32'),(182,105,'Requerimiento 2 de Subsubactividad 1 de 2 de 13','recibido',NULL,NULL,'2024-12-26 01:18:32','2025-09-13 01:18:32'),(183,106,'Requerimiento 1 de Subsubactividad 2 de 2 de 13','recibido',NULL,NULL,'2025-02-25 01:18:32','2025-09-13 01:18:32'),(184,106,'Requerimiento 2 de Subsubactividad 2 de 2 de 13','pendiente',NULL,NULL,'2025-09-08 01:18:32','2025-09-13 01:18:32'),(185,107,'Requerimiento 1 de Actividad 14','pendiente',NULL,NULL,'2025-07-13 01:18:32','2025-09-13 01:18:32'),(186,107,'Requerimiento 2 de Actividad 14','pendiente',NULL,NULL,'2025-01-22 01:18:32','2025-09-13 01:18:32'),(187,107,'Requerimiento 3 de Actividad 14','pendiente',NULL,NULL,'2025-04-24 01:18:32','2025-09-13 01:18:32'),(188,107,'Requerimiento 4 de Actividad 14','pendiente',NULL,NULL,'2025-08-06 01:18:32','2025-09-13 01:18:32'),(189,108,'Requerimiento 1 de Subactividad 1 de 14','recibido',NULL,NULL,'2025-08-30 01:18:32','2025-09-13 01:18:32'),(190,108,'Requerimiento 2 de Subactividad 1 de 14','pendiente',NULL,NULL,'2024-12-31 01:18:32','2025-09-13 01:18:32'),(191,109,'Requerimiento 1 de Subsubactividad 1 de 1 de 14','recibido',NULL,NULL,'2025-01-31 01:18:32','2025-09-13 01:18:32'),(192,109,'Requerimiento 2 de Subsubactividad 1 de 1 de 14','pendiente',NULL,NULL,'2024-10-15 01:18:32','2025-09-13 01:18:32'),(193,110,'Requerimiento 1 de Subsubactividad 2 de 1 de 14','recibido',NULL,NULL,'2025-05-11 01:18:32','2025-09-13 01:18:32'),(194,110,'Requerimiento 2 de Subsubactividad 2 de 1 de 14','pendiente',NULL,NULL,'2025-07-10 01:18:32','2025-09-13 01:18:32'),(195,111,'Requerimiento 1 de Subactividad 2 de 14','pendiente',NULL,NULL,'2025-04-02 01:18:32','2025-09-13 01:18:32'),(196,111,'Requerimiento 2 de Subactividad 2 de 14','recibido',NULL,NULL,'2024-10-05 01:18:32','2025-09-13 01:18:32'),(197,111,'Requerimiento 3 de Subactividad 2 de 14','pendiente',NULL,NULL,'2024-10-26 01:18:32','2025-09-13 01:18:32'),(198,112,'Requerimiento 1 de Subsubactividad 1 de 2 de 14','recibido',NULL,NULL,'2025-04-06 01:18:32','2025-09-13 01:18:32'),(199,113,'Requerimiento 1 de Actividad 15','recibido',NULL,NULL,'2024-10-07 01:18:32','2025-09-13 01:18:32'),(200,113,'Requerimiento 2 de Actividad 15','pendiente',NULL,NULL,'2025-06-21 01:18:32','2025-09-13 01:18:32'),(201,113,'Requerimiento 3 de Actividad 15','pendiente',NULL,NULL,'2025-09-10 01:18:32','2025-09-13 01:18:32'),(202,114,'Requerimiento 1 de Subactividad 1 de 15','recibido',NULL,NULL,'2024-11-14 01:18:32','2025-09-13 01:18:32'),(203,114,'Requerimiento 2 de Subactividad 1 de 15','recibido',NULL,NULL,'2024-09-22 01:18:32','2025-09-13 01:18:32'),(204,115,'Requerimiento 1 de Subsubactividad 1 de 1 de 15','recibido',NULL,NULL,'2024-12-25 01:18:32','2025-09-13 01:18:32'),(205,116,'Requerimiento 1 de Subactividad 2 de 15','recibido',NULL,NULL,'2024-12-30 01:18:32','2025-09-13 01:18:32'),(206,116,'Requerimiento 2 de Subactividad 2 de 15','pendiente',NULL,NULL,'2024-10-18 01:18:32','2025-09-13 01:18:32'),(207,117,'Requerimiento 1 de Subsubactividad 1 de 2 de 15','pendiente',NULL,NULL,'2025-03-15 01:18:32','2025-09-13 01:18:32'),(208,117,'Requerimiento 2 de Subsubactividad 1 de 2 de 15','recibido',NULL,NULL,'2025-04-13 01:18:32','2025-09-13 01:18:32'),(209,118,'Requerimiento 1 de Subactividad 3 de 15','recibido',NULL,NULL,'2025-04-20 01:18:32','2025-09-13 01:18:32'),(210,118,'Requerimiento 2 de Subactividad 3 de 15','pendiente',NULL,NULL,'2024-12-10 01:18:32','2025-09-13 01:18:32'),(211,119,'Requerimiento 1 de Subsubactividad 1 de 3 de 15','recibido',NULL,NULL,'2024-12-27 01:18:32','2025-09-13 01:18:32'),(212,119,'Requerimiento 2 de Subsubactividad 1 de 3 de 15','recibido',NULL,NULL,'2025-02-14 01:18:32','2025-09-13 01:18:32'),(213,120,'Requerimiento 1 de Subsubactividad 2 de 3 de 15','recibido',NULL,NULL,'2025-07-31 01:18:32','2025-09-13 01:18:32'),(214,121,'Requerimiento 1 de Actividad 16','recibido',NULL,NULL,'2024-10-02 01:18:32','2025-09-13 01:18:32'),(215,121,'Requerimiento 2 de Actividad 16','recibido',NULL,NULL,'2025-07-27 01:18:32','2025-09-13 01:18:32'),(216,121,'Requerimiento 3 de Actividad 16','pendiente',NULL,NULL,'2025-03-10 01:18:32','2025-09-13 01:18:32'),(217,121,'Requerimiento 4 de Actividad 16','pendiente',NULL,NULL,'2025-01-18 01:18:32','2025-09-13 01:18:32'),(218,122,'Requerimiento 1 de Subactividad 1 de 16','pendiente',NULL,NULL,'2025-08-04 01:18:32','2025-09-13 01:18:32'),(219,122,'Requerimiento 2 de Subactividad 1 de 16','recibido',NULL,NULL,'2025-03-17 01:18:32','2025-09-13 01:18:32'),(220,122,'Requerimiento 3 de Subactividad 1 de 16','pendiente',NULL,NULL,'2024-11-12 01:18:32','2025-09-13 01:18:32'),(221,123,'Requerimiento 1 de Subsubactividad 1 de 1 de 16','recibido',NULL,NULL,'2025-08-04 01:18:32','2025-09-13 01:18:32'),(222,123,'Requerimiento 2 de Subsubactividad 1 de 1 de 16','pendiente',NULL,NULL,'2025-07-28 01:18:32','2025-09-13 01:18:32'),(223,124,'Requerimiento 1 de Subsubactividad 2 de 1 de 16','pendiente',NULL,NULL,'2024-11-18 01:18:32','2025-09-13 01:18:32'),(224,125,'Requerimiento 1 de Subactividad 2 de 16','recibido',NULL,NULL,'2025-07-01 01:18:32','2025-09-13 01:18:32'),(225,126,'Requerimiento 1 de Subsubactividad 1 de 2 de 16','recibido',NULL,NULL,'2025-02-10 01:18:32','2025-09-13 01:18:32'),(226,126,'Requerimiento 2 de Subsubactividad 1 de 2 de 16','pendiente',NULL,NULL,'2024-10-08 01:18:32','2025-09-13 01:18:32'),(227,127,'Requerimiento 1 de Subsubactividad 2 de 2 de 16','recibido',NULL,NULL,'2024-11-13 01:18:32','2025-09-13 01:18:32'),(228,128,'Requerimiento 1 de Subactividad 3 de 16','recibido',NULL,NULL,'2025-01-31 01:18:32','2025-09-13 01:18:32'),(229,128,'Requerimiento 2 de Subactividad 3 de 16','recibido',NULL,NULL,'2024-12-03 01:18:32','2025-09-13 01:18:32'),(230,128,'Requerimiento 3 de Subactividad 3 de 16','pendiente',NULL,NULL,'2025-06-03 01:18:32','2025-09-13 01:18:32'),(231,129,'Requerimiento 1 de Subsubactividad 1 de 3 de 16','recibido',NULL,NULL,'2024-11-14 01:18:32','2025-09-13 01:18:32'),(232,130,'Requerimiento 1 de Subsubactividad 2 de 3 de 16','pendiente',NULL,NULL,'2025-01-21 01:18:32','2025-09-13 01:18:32'),(233,130,'Requerimiento 2 de Subsubactividad 2 de 3 de 16','pendiente',NULL,NULL,'2025-07-29 01:18:32','2025-09-13 01:18:32'),(234,131,'Requerimiento 1 de Actividad 17','recibido',NULL,NULL,'2025-02-21 01:18:32','2025-09-13 01:18:32'),(235,131,'Requerimiento 2 de Actividad 17','pendiente',NULL,NULL,'2025-06-29 01:18:32','2025-09-13 01:18:32'),(236,131,'Requerimiento 3 de Actividad 17','recibido',NULL,NULL,'2025-08-24 01:18:32','2025-09-13 01:18:32'),(237,131,'Requerimiento 4 de Actividad 17','pendiente',NULL,NULL,'2025-05-19 01:18:32','2025-09-13 01:18:32'),(238,132,'Requerimiento 1 de Subactividad 1 de 17','recibido',NULL,NULL,'2025-06-12 01:18:33','2025-09-13 01:18:33'),(239,132,'Requerimiento 2 de Subactividad 1 de 17','pendiente',NULL,NULL,'2025-05-12 01:18:33','2025-09-13 01:18:33'),(240,133,'Requerimiento 1 de Subsubactividad 1 de 1 de 17','pendiente',NULL,NULL,'2025-03-13 01:18:33','2025-09-13 01:18:33'),(241,133,'Requerimiento 2 de Subsubactividad 1 de 1 de 17','recibido',NULL,NULL,'2025-05-18 01:18:33','2025-09-13 01:18:33'),(242,134,'Requerimiento 1 de Subactividad 2 de 17','pendiente',NULL,NULL,'2024-11-30 01:18:33','2025-09-13 01:18:33'),(243,134,'Requerimiento 2 de Subactividad 2 de 17','recibido',NULL,NULL,'2025-02-14 01:18:33','2025-09-13 01:18:33'),(244,135,'Requerimiento 1 de Subsubactividad 1 de 2 de 17','recibido',NULL,NULL,'2024-11-17 01:18:33','2025-09-13 01:18:33'),(245,136,'Requerimiento 1 de Subactividad 3 de 17','recibido',NULL,NULL,'2025-04-22 01:18:33','2025-09-13 01:18:33'),(246,136,'Requerimiento 2 de Subactividad 3 de 17','recibido',NULL,NULL,'2025-07-14 01:18:33','2025-09-13 01:18:33'),(247,137,'Requerimiento 1 de Subsubactividad 1 de 3 de 17','recibido',NULL,NULL,'2025-05-16 01:18:33','2025-09-13 01:18:33'),(248,138,'Requerimiento 1 de Subsubactividad 2 de 3 de 17','pendiente',NULL,NULL,'2025-01-08 01:18:33','2025-09-13 01:18:33'),(249,139,'Requerimiento 1 de Subactividad 4 de 17','pendiente',NULL,NULL,'2025-06-15 01:18:33','2025-09-13 01:18:33'),(250,140,'Requerimiento 1 de Subsubactividad 1 de 4 de 17','recibido',NULL,NULL,'2025-08-15 01:18:33','2025-09-13 01:18:33'),(251,141,'Requerimiento 1 de Subsubactividad 2 de 4 de 17','recibido',NULL,NULL,'2025-03-02 01:18:33','2025-09-13 01:18:33'),(252,141,'Requerimiento 2 de Subsubactividad 2 de 4 de 17','pendiente',NULL,NULL,'2025-04-27 01:18:33','2025-09-13 01:18:33'),(253,142,'Requerimiento 1 de Actividad 18','recibido',NULL,NULL,'2024-10-19 01:18:33','2025-09-13 01:18:33'),(254,143,'Requerimiento 1 de Subactividad 1 de 18','pendiente',NULL,NULL,'2025-01-21 01:18:33','2025-09-13 01:18:33'),(255,144,'Requerimiento 1 de Subsubactividad 1 de 1 de 18','pendiente',NULL,NULL,'2024-11-28 01:18:33','2025-09-13 01:18:33'),(256,145,'Requerimiento 1 de Subactividad 2 de 18','pendiente',NULL,NULL,'2025-07-31 01:18:33','2025-09-13 01:18:33'),(257,146,'Requerimiento 1 de Subsubactividad 1 de 2 de 18','pendiente',NULL,NULL,'2025-05-25 01:18:33','2025-09-13 01:18:33'),(258,146,'Requerimiento 2 de Subsubactividad 1 de 2 de 18','pendiente',NULL,NULL,'2025-05-14 01:18:33','2025-09-13 01:18:33'),(259,147,'Requerimiento 1 de Subactividad 3 de 18','pendiente',NULL,NULL,'2025-04-26 01:18:33','2025-09-13 01:18:33'),(260,147,'Requerimiento 2 de Subactividad 3 de 18','pendiente',NULL,NULL,'2025-08-19 01:18:33','2025-09-13 01:18:33'),(261,147,'Requerimiento 3 de Subactividad 3 de 18','recibido',NULL,NULL,'2025-08-24 01:18:33','2025-09-13 01:18:33'),(262,148,'Requerimiento 1 de Subsubactividad 1 de 3 de 18','pendiente',NULL,NULL,'2024-12-19 01:18:33','2025-09-13 01:18:33'),(263,149,'Requerimiento 1 de Actividad 19','pendiente',NULL,NULL,'2025-04-12 01:18:33','2025-09-13 01:18:33'),(264,150,'Requerimiento 1 de Subactividad 1 de 19','recibido',NULL,NULL,'2025-06-05 01:18:33','2025-09-13 01:18:33'),(265,151,'Requerimiento 1 de Subsubactividad 1 de 1 de 19','pendiente',NULL,NULL,'2025-01-02 01:18:33','2025-09-13 01:18:33'),(266,151,'Requerimiento 2 de Subsubactividad 1 de 1 de 19','pendiente',NULL,NULL,'2024-12-03 01:18:33','2025-09-13 01:18:33'),(267,152,'Requerimiento 1 de Subsubactividad 2 de 1 de 19','recibido',NULL,NULL,'2025-02-15 01:18:33','2025-09-13 01:18:33'),(268,153,'Requerimiento 1 de Subactividad 2 de 19','recibido',NULL,NULL,'2025-07-01 01:18:33','2025-09-13 01:18:33'),(269,153,'Requerimiento 2 de Subactividad 2 de 19','pendiente',NULL,NULL,'2025-06-12 01:18:33','2025-09-13 01:18:33'),(270,154,'Requerimiento 1 de Subsubactividad 1 de 2 de 19','pendiente',NULL,NULL,'2025-07-02 01:18:33','2025-09-13 01:18:33'),(271,155,'Requerimiento 1 de Subsubactividad 2 de 2 de 19','recibido',NULL,NULL,'2025-09-03 01:18:33','2025-09-13 01:18:33'),(272,156,'Requerimiento 1 de Actividad 20','recibido',NULL,NULL,'2024-09-13 01:18:33','2025-09-13 01:18:33'),(273,156,'Requerimiento 2 de Actividad 20','recibido',NULL,NULL,'2025-01-09 01:18:33','2025-09-13 01:18:33'),(274,156,'Requerimiento 3 de Actividad 20','recibido',NULL,NULL,'2025-02-12 01:18:33','2025-09-13 01:18:33'),(275,156,'Requerimiento 4 de Actividad 20','pendiente',NULL,NULL,'2025-01-17 01:18:33','2025-09-13 01:18:33'),(276,157,'Requerimiento 1 de Subactividad 1 de 20','recibido',NULL,NULL,'2025-04-15 01:18:33','2025-09-13 01:18:33'),(277,157,'Requerimiento 2 de Subactividad 1 de 20','recibido',NULL,NULL,'2024-10-22 01:18:33','2025-09-13 01:18:33'),(278,157,'Requerimiento 3 de Subactividad 1 de 20','pendiente',NULL,NULL,'2025-03-18 01:18:33','2025-09-13 01:18:33'),(279,158,'Requerimiento 1 de Subsubactividad 1 de 1 de 20','pendiente',NULL,NULL,'2025-01-21 01:18:33','2025-09-13 01:18:33'),(280,159,'Requerimiento 1 de Subsubactividad 2 de 1 de 20','recibido',NULL,NULL,'2024-10-08 01:18:33','2025-09-13 01:18:33'),(281,159,'Requerimiento 2 de Subsubactividad 2 de 1 de 20','pendiente',NULL,NULL,'2025-07-01 01:18:33','2025-09-13 01:18:33'),(282,160,'Requerimiento 1 de Subactividad 2 de 20','pendiente',NULL,NULL,'2024-11-07 01:18:33','2025-09-13 01:18:33'),(283,160,'Requerimiento 2 de Subactividad 2 de 20','pendiente',NULL,NULL,'2025-04-10 01:18:33','2025-09-13 01:18:33'),(284,161,'Requerimiento 1 de Subsubactividad 1 de 2 de 20','pendiente',NULL,NULL,'2025-03-29 01:18:33','2025-09-13 01:18:33'),(285,162,'Requerimiento 1 de Subsubactividad 2 de 2 de 20','recibido',NULL,NULL,'2025-05-19 01:18:33','2025-09-13 01:18:33'),(286,163,'Requerimiento 1 de Subactividad 3 de 20','pendiente',NULL,NULL,'2025-08-01 01:18:33','2025-09-13 01:18:33'),(287,163,'Requerimiento 2 de Subactividad 3 de 20','recibido',NULL,NULL,'2025-06-08 01:18:33','2025-09-13 01:18:33'),(288,164,'Requerimiento 1 de Subsubactividad 1 de 3 de 20','recibido',NULL,NULL,'2025-03-03 01:18:33','2025-09-13 01:18:33'),(289,165,'Requerimiento 1 de Subsubactividad 2 de 3 de 20','pendiente',NULL,NULL,'2024-10-21 01:18:33','2025-09-13 01:18:33'),(290,165,'Requerimiento 2 de Subsubactividad 2 de 3 de 20','recibido',NULL,NULL,'2025-08-20 01:18:33','2025-09-13 01:18:33');
/*!40000 ALTER TABLE `requirements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('ElF7TSm64uQcvfdI6M1wG2Lf4t3FEemk84drmPDQ',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ3czM1dtdUVVVWp0d1JEVnlvRUZ3b2RKRlEyTWU0NG9LaWM0eGNtYSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hY3Rpdml0aWVzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1758031115),('g0TCNyruDzpxq9fYe6VW6oEfC9dR8MF3h2OWj5IV',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZGt0dFdkNWNSNkdHUnNKWXZiVjEyck5jZVFuOW5BMW5OQXNtNFVmcSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hY3Rpdml0aWVzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1757730709),('hGBKOHq9c749DAhI5j9hpcvxC5fWaPKGtYL276An',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiV2c1YXFYWHl1Y1J3VE8zM3RXZ29sU0ZPQXh4cVVGNkVOdHlJZHU0ZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hY3Rpdml0aWVzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1757953829);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statuses`
--

DROP TABLE IF EXISTS `statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#007bff',
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `statuses_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statuses`
--

LOCK TABLES `statuses` WRITE;
/*!40000 ALTER TABLE `statuses` DISABLE KEYS */;
INSERT INTO `statuses` VALUES (1,'en_ejecucion','En Ejecución','#17a2b8','fas fa-play-circle',2,1,'2025-09-13 01:18:29','2025-09-13 01:18:29'),(2,'culminada','Culminada','#28a745','fas fa-check-circle',7,1,'2025-09-13 01:18:29','2025-09-13 01:18:29'),(3,'en_espera_de_insumos','En Espera de Insumos','#ffc107','fas fa-pause-circle',3,1,'2025-09-13 01:18:29','2025-09-13 01:18:29'),(4,'pausada','Pausada','#6c757d','fas fa-pause',4,1,'2025-09-13 01:18:29','2025-09-13 01:18:29'),(5,'cancelada','Cancelada','#dc3545','fas fa-times-circle',8,1,'2025-09-13 01:18:29','2025-09-13 01:18:29'),(6,'en_certificacion_por_cliente','En Certificación por Cliente','#fd7e14','fas fa-certificate',5,1,'2025-09-13 01:18:29','2025-09-13 01:18:29'),(7,'no_iniciada','No Iniciada','#6c757d','fas fa-clock',1,1,'2025-09-13 01:18:29','2025-09-13 01:18:29'),(8,'pases_enviados','Pases Enviados','#20c997','fas fa-paper-plane',6,1,'2025-09-13 01:18:29','2025-09-13 01:18:29');
/*!40000 ALTER TABLE `statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
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

-- Dump completed on 2025-09-16 13:28:34
