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
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(100) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
INSERT INTO `menu_items` VALUES (4,'PIZZA','A perfectly baked pizza topped with flavorful sauce, gooey cheese, and handpicked fresh ingredients.',300.00,'Appetizers','suleman _menu/pizza.jpg',1,'2026-03-24 11:42:58'),(5,'BURGER','A delicious burger stacked with a juicy patty, fresh ingredients, and rich sauces in a soft toasted bun.',150.00,'Appetizers','suleman _menu/burger.jpg',1,'2026-03-24 11:44:30'),(7,'COFFEE','Rich, smooth, and energizing — your perfect cup of coffee.',50.00,'Beverages','suleman _menu/coffee.jpg',1,'2026-03-24 12:18:04'),(9,'EGGS','Boiled eggs coated with spicy masala, delivering a simple yet flavorful kick in every bite.',40.00,'Appetizers','suleman _menu/eggs.jpg',1,'2026-03-24 15:02:14'),(10,'PASTA','Freshly cooked pasta tossed in a rich, flavorful sauce, finished with herbs for a comforting classic.',60.00,'Appetizers','suleman _menu/pasta.jpg',1,'2026-03-24 15:03:19'),(11,'SANDWICH','Loaded sandwich with tasty fillings and chutney, giving a perfect street-style flavor.',70.00,'Appetizers','suleman _menu/sandwiches.jpg',1,'2026-03-24 15:05:05'),(13,'ICED TEA','Cool iced tea infused with fresh lemon for a crisp and zesty refreshment.',40.00,'Beverages','suleman _menu/icedtea.jpg',1,'2026-03-24 15:13:25'),(14,'CHOCOLATE SHAKE','Rich and creamy chocolate shake blended to perfection for a smooth, refreshing treat.',120.00,'Beverages','suleman _menu/chocoshake.jpg',1,'2026-03-24 15:15:16'),(15,'ORANGE JUICE','Freshly squeezed orange juice, naturally sweet and packed with refreshing citrus flavor.',80.00,'Beverages','suleman _menu/orangejuice.jpg',1,'2026-03-24 15:17:04'),(16,'PASTRY','Decadent chocolate pastry with creamy layers, delivering a rich and indulgent taste.',50.00,'Desserts','suleman _menu/pastery.jpg',1,'2026-03-24 15:20:57'),(17,'SMOOTHIE','A refreshing, thick, and creamy blend of fresh fruits, milk or yogurt, perfectly chilled and naturally sweet—packed with flavor and nutrition in every sip.',60.00,'Beverages','suleman _menu/smoothie.jpg',1,'2026-03-24 15:25:11'),(18,'MATCHA','A finely ground Japanese green tea with a rich, earthy flavor and vibrant color—smooth, creamy, and packed with antioxidants for a calm, sustained energy boost.',70.00,'Beverages','suleman _menu/Matcha.jpg',1,'2026-03-24 15:27:08'),(19,'ICE CREAM','A rich, creamy frozen dessert made with milk, cream, and sweet flavors—smooth, indulgent, and perfectly chilled to satisfy every sweet craving.',50.00,'Desserts','suleman _menu/icecream.jpg',1,'2026-03-24 15:33:41'),(20,'CHEESECAKE','A rich and creamy dessert with a smooth cheese filling on a buttery biscuit base—perfectly balanced in sweetness with a soft, melt-in-the-mouth texture.',100.00,'Desserts','suleman _menu/cheesecake.jpg',1,'2026-03-24 15:40:15'),(21,'FRENCH FRIES','Crispy, golden potato sticks fried to perfection—lightly salted and served hot for the ultimate crunchy and satisfying snack.',100.00,'Appetizers','suleman _menu/frenchfries.jpg',1,'2026-03-24 15:41:33'),(22,'BUTTER CHICKEN','Tender chicken cooked in a rich, creamy tomato-based gravy infused with butter and aromatic spices—smooth, mildly spiced, and full of indulgent flavor.',400.00,'Main Course','suleman _menu/butterchicken.jpg',1,'2026-03-24 15:44:39'),(24,'PANNER BUTTER MASALA','Soft paneer cubes simmered in a rich, buttery tomato gravy infused with aromatic spices—creamy, mildly sweet, and irresistibly flavorful.',350.00,'Main Course','suleman _menu/paneer.jpg',1,'2026-03-24 15:47:27');
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
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
