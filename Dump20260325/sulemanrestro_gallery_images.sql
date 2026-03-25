-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: sulemanrestro
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `gallery_images`
--

DROP TABLE IF EXISTS `gallery_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gallery_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_path` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `display_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gallery_images`
--

LOCK TABLES `gallery_images` WRITE;
/*!40000 ALTER TABLE `gallery_images` DISABLE KEYS */;
INSERT INTO `gallery_images` VALUES (5,'restro_img/gallery/1774367956_7014.jpg','Lakeside Fine Dining','Peaceful waterfront setting with elegant seating, perfect for a romantic or relaxing dining experience.','2026-03-24 15:59:16',0),(6,'restro_img/gallery/1774368018_3895.jpg','Crispy Veg Cutlet Stack','Golden-fried veg cutlets layered with onions and spices, served fresh on a traditional leaf base.','2026-03-24 16:00:18',0),(7,'restro_img/gallery/1774368052_8188.jpg','Sunny Side Egg Toast','Perfectly fried egg on toasted bread with creamy spread and a sprinkle of herbs.','2026-03-24 16:00:52',0),(8,'restro_img/gallery/1774368165_9157.jpg','Classic Pancake Stack','Fluffy pancakes topped with blueberries and honey syrup, a perfect sweet start to your day.','2026-03-24 16:02:45',0),(9,'restro_img/gallery/1774368222_9767.jpg','Grilled Salmon Delight','Juicy grilled salmon served with fresh sautéed vegetables for a healthy, premium meal.','2026-03-24 16:03:42',0),(10,'restro_img/gallery/1774368256_5550.jpg','Yogurt Granola Bowl','Creamy yogurt topped with crunchy granola and fresh berries — light, nutritious, and refreshing.','2026-03-24 16:04:16',0),(11,'restro_img/gallery/1774368301_1477.jpg','Professional Service Experience','Staff ensuring smooth operations and top-quality customer service inside the restaurant.','2026-03-24 16:05:01',0),(12,'restro_img/gallery/1774368336_5022.jpg','Reserved Table Setup','Elegant table arrangement with a cozy ambiance, reserved for special guests.','2026-03-24 16:05:36',0),(13,'restro_img/gallery/1774368397_9930.jpg','Luxury Dining Experience','Fine dining setup with premium plating, wine, and candlelight atmosphere.','2026-03-24 16:06:37',0),(14,'restro_img/gallery/1774368427_9335.jpg','Gourmet Plated Dish','Beautifully presented dish served in a high-end restaurant environment.','2026-03-24 16:07:07',0),(15,'restro_img/gallery/1774368498_1477.jpg','Chef’s Special Platter','A complete meal with grilled meat, vegetables, and sides crafted with precision.','2026-03-24 16:08:18',0),(16,'restro_img/gallery/1774368562_3713.jpg','Kitchen Team in Action','Professional chefs preparing dishes with focus and teamwork in a live kitchen.','2026-03-24 16:09:22',0);
/*!40000 ALTER TABLE `gallery_images` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-25  8:44:00
