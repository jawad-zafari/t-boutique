-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 26, 2026 at 08:43 PM
-- Server version: 8.4.3
-- PHP Version: 8.5.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `digi_mvc`
--

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE `attributes` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int NOT NULL,
  `parent_id` int NOT NULL DEFAULT '0',
  `is_filter` tinyint(1) NOT NULL DEFAULT '0',
  `is_right_filter` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attributes`
--

INSERT INTO `attributes` (`id`, `title`, `category_id`, `parent_id`, `is_filter`, `is_right_filter`) VALUES
(1, 'iphone', 13, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `attribute_values`
--

CREATE TABLE `attribute_values` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `attribute_id` int NOT NULL,
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int NOT NULL,
  `session_cookie` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `color_id` int NOT NULL DEFAULT '0',
  `guarantee_id` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `session_cookie`, `product_id`, `quantity`, `color_id`, `guarantee_id`) VALUES
(1, '1783770881', 26, 1, 0, 0),
(110, '1784404340', 6, 2, 0, 0),
(111, '1784404340', 10, 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int NOT NULL DEFAULT '0',
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_brand` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `title`, `parent_id`, `image_path`, `is_brand`) VALUES
(1, 'Smartphone', 0, NULL, 0),
(2, 'Ordinateurs Portables', 0, NULL, 0),
(3, 'Accessoire', 0, NULL, 0),
(4, 'Audio & Écouteurs', 0, NULL, 0),
(7, 'Apple', 0, 'public/images/brands/apple.png', 1),
(8, 'Samsung', 0, 'public/images/brands/samsung.png', 1),
(9, 'Sony', 0, 'public/images/brands/sony.png', 1),
(10, 'Xiaomi', 0, 'public/images/brands/xiaomi.png', 1),
(11, 'Apple', 1, NULL, 0),
(12, 'samsung', 1, NULL, 0),
(13, 'iphone', 11, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `colors`
--

CREATE TABLE `colors` (
  `id` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `color_hex` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `colors`
--

INSERT INTO `colors` (`id`, `title`, `color_hex`) VALUES
(1, 'Noir', '#000000'),
(2, 'Blanc', '#ffffff'),
(3, 'Bleu', '#3b82f6');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `positive_points` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `negative_points` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `likes` int NOT NULL DEFAULT '0',
  `dislikes` int NOT NULL DEFAULT '0',
  `product_id` int NOT NULL,
  `parameters` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `title`, `content`, `created_at`, `positive_points`, `negative_points`, `likes`, `dislikes`, `product_id`, `parameters`, `user_id`, `is_approved`) VALUES
(1, 'dlgjdfjgk', 'dgldfkjg', '2026/07/15', 'dglkjfd', 'dglfdjg', 0, 0, 4, 'a:0:{}', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `comment_scores`
--

CREATE TABLE `comment_scores` (
  `id` int NOT NULL,
  `comment_id` int NOT NULL,
  `parameter_id` int NOT NULL,
  `score` float NOT NULL DEFAULT '5'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comment_scores`
--

INSERT INTO `comment_scores` (`id`, `comment_id`, `parameter_id`, `score`) VALUES
(1, 1, 1, 5),
(2, 1, 2, 4),
(3, 1, 3, 4.5);

-- --------------------------------------------------------

--
-- Table structure for table `discount_codes`
--

CREATE TABLE `discount_codes` (
  `id` int NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT '0',
  `discount_percent` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `max_usage` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `user_id` int NOT NULL,
  `folder_id` int NOT NULL DEFAULT '0',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `product_id`, `user_id`, `folder_id`, `title`) VALUES
(43, 24, 2, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `guarantees`
--

CREATE TABLE `guarantees` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guarantees`
--

INSERT INTO `guarantees` (`id`, `title`) VALUES
(1, 'Garantie constructeur 1 an'),
(2, 'Garantie étendue 24 mois');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int NOT NULL,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `short_desc`, `image_path`, `created_at`) VALUES
(1, 'Nouvelle collection d\'été disponible', 'Découvrez les dernières tendances pour la saison estivale avec nos offres exclusives.', 'public/images/news/1.jpg', '2026-07-15'),
(2, 'Les meilleurs smartphones en 2026', 'Notre guide complet pour bien choisir votre prochain téléphone portable.', 'public/images/news/2.jpg', '2026-07-10');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `transaction_id_before` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `transaction_id_after` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `total_amount` int NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_reversed` tinyint(1) NOT NULL DEFAULT '0',
  `province` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_method_id` int NOT NULL,
  `cart_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_price` int NOT NULL,
  `user_id` int NOT NULL,
  `status_id` int NOT NULL DEFAULT '1',
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `payment_method_id` int NOT NULL,
  `pay_day` int NOT NULL DEFAULT '0',
  `pay_month` int NOT NULL DEFAULT '0',
  `pay_year` int NOT NULL DEFAULT '0',
  `pay_card_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pay_bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_timestamp` int NOT NULL,
  `created_date` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `barcode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `tracking_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `admin_note` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `transaction_id_before`, `transaction_id_after`, `total_amount`, `last_name`, `is_reversed`, `province`, `city`, `postal_code`, `mobile`, `phone`, `shipping_method_id`, `cart_data`, `address_data`, `shipping_price`, `user_id`, `status_id`, `is_paid`, `payment_method_id`, `pay_day`, `pay_month`, `pay_year`, `pay_card_number`, `pay_bank_name`, `created_timestamp`, `created_date`, `barcode`, `tracking_code`, `admin_note`) VALUES
(1, 'cs_test_f3a0be4a3146706571e7b67823c849fd', 'pi_test_aef53565903ceedb', 405, '', 0, '', '', '', '', '', 1, 'a:1:{i:0;a:20:{s:5:\"tedad\";i:1;s:7:\"cartRow\";i:1;s:2:\"id\";i:26;s:5:\"title\";s:18:\"Samsung Galaxy A55\";s:5:\"price\";i:450;s:11:\"category_id\";i:1;s:11:\"description\";s:27:\"Le milieu de gamme parfait.\";s:14:\"stock_quantity\";i:150;s:16:\"discount_percent\";i:10;s:16:\"is_special_offer\";i:0;s:24:\"special_offer_expires_at\";i:0;s:12:\"is_exclusive\";i:0;s:5:\"views\";i:3400;s:9:\"color_ids\";s:5:\"1,2,4\";s:13:\"guarantee_ids\";s:1:\"1\";s:21:\"secondary_category_id\";i:0;s:6:\"weight\";i:202;s:10:\"colorTitle\";N;s:13:\"garanteeTitle\";N;s:13:\"discountTotal\";i:45;}}', '', 0, 1, 5, 1, 1, 0, 0, 0, '', '', 1783803525, '1405/04/21', 'ORD-1783803525-7195', '', NULL),
(2, 'cs_test_b7217d995d070ebb6efb0c6351d22fd6', 'pi_test_128c29ebdc33fdea', 405, '', 0, '', '', '', '', '', 1, 'a:1:{i:0;a:20:{s:5:\"tedad\";i:1;s:7:\"cartRow\";i:1;s:2:\"id\";i:26;s:5:\"title\";s:18:\"Samsung Galaxy A55\";s:5:\"price\";i:450;s:11:\"category_id\";i:1;s:11:\"description\";s:27:\"Le milieu de gamme parfait.\";s:14:\"stock_quantity\";i:150;s:16:\"discount_percent\";i:10;s:16:\"is_special_offer\";i:0;s:24:\"special_offer_expires_at\";i:0;s:12:\"is_exclusive\";i:0;s:5:\"views\";i:3400;s:9:\"color_ids\";s:5:\"1,2,4\";s:13:\"guarantee_ids\";s:1:\"1\";s:21:\"secondary_category_id\";i:0;s:6:\"weight\";i:202;s:10:\"colorTitle\";N;s:13:\"garanteeTitle\";N;s:13:\"discountTotal\";i:45;}}', '', 0, 1, 5, 1, 1, 0, 0, 0, '', '', 1783803959, '1405/04/21', 'ORD-1783803959-5465', '', NULL),
(3, 'cs_test_5ec490a0c4df773dae3253e762388326', 'pi_test_7e1ece5f1134b0c1', 4643, '', 0, '', '', '', '', '', 1, 'a:3:{i:0;a:20:{s:5:\"tedad\";i:1;s:7:\"cartRow\";i:2;s:2:\"id\";i:11;s:5:\"title\";s:11:\"Dell XPS 15\";s:5:\"price\";i:2400;s:11:\"category_id\";i:2;s:11:\"description\";s:38:\"L\'ordinateur portable Windows premium.\";s:14:\"stock_quantity\";i:25;s:16:\"discount_percent\";i:0;s:16:\"is_special_offer\";i:0;s:24:\"special_offer_expires_at\";i:0;s:12:\"is_exclusive\";i:1;s:5:\"views\";i:1200;s:9:\"color_ids\";s:1:\"1\";s:13:\"guarantee_ids\";s:1:\"2\";s:21:\"secondary_category_id\";i:0;s:6:\"weight\";i:1800;s:10:\"colorTitle\";N;s:13:\"garanteeTitle\";N;s:13:\"discountTotal\";i:0;}i:1;a:20:{s:5:\"tedad\";i:1;s:7:\"cartRow\";i:3;s:2:\"id\";i:15;s:5:\"title\";s:23:\"Samsung Galaxy Z Fold 5\";s:5:\"price\";i:1800;s:11:\"category_id\";i:1;s:11:\"description\";s:30:\"Le smartphone pliable premium.\";s:14:\"stock_quantity\";i:10;s:16:\"discount_percent\";i:15;s:16:\"is_special_offer\";i:1;s:24:\"special_offer_expires_at\";i:1893456000;s:12:\"is_exclusive\";i:1;s:5:\"views\";i:2800;s:9:\"color_ids\";s:3:\"1,5\";s:13:\"guarantee_ids\";s:3:\"1,2\";s:21:\"secondary_category_id\";i:0;s:6:\"weight\";i:253;s:10:\"colorTitle\";N;s:13:\"garanteeTitle\";N;s:13:\"discountTotal\";i:270;}i:2;a:20:{s:5:\"tedad\";i:1;s:7:\"cartRow\";i:4;s:2:\"id\";i:8;s:5:\"title\";s:17:\"iPad Air 5 - 64Go\";s:5:\"price\";i:750;s:11:\"category_id\";i:2;s:11:\"description\";s:27:\"Tablette Apple polyvalente.\";s:14:\"stock_quantity\";i:60;s:16:\"discount_percent\";i:5;s:16:\"is_special_offer\";i:0;s:24:\"special_offer_expires_at\";i:0;s:12:\"is_exclusive\";i:1;s:5:\"views\";i:1800;s:9:\"color_ids\";s:3:\"2,5\";s:13:\"guarantee_ids\";s:1:\"1\";s:21:\"secondary_category_id\";i:0;s:6:\"weight\";i:460;s:10:\"colorTitle\";N;s:13:\"garanteeTitle\";N;s:13:\"discountTotal\";d:37.5;}}', '', 0, 1, 5, 1, 1, 0, 0, 0, '', '', 1783893872, '1405/04/22', 'ORD-1783893872-9793', '', NULL),
(4, 'cs_test_e307a89aedaa01539daa3fe327eb5a3f', 'pi_test_607fb4602d2a51d1', 298, '', 0, '', '', '', '', '', 1, 'a:1:{i:0;a:20:{s:5:\"tedad\";i:1;s:7:\"cartRow\";i:68;s:2:\"id\";i:4;s:5:\"title\";s:15:\"Sony WH-1000XM5\";s:5:\"price\";i:350;s:11:\"category_id\";i:4;s:11:\"description\";s:37:\"Casque à réduction de bruit active.\";s:14:\"stock_quantity\";i:100;s:16:\"discount_percent\";i:15;s:16:\"is_special_offer\";i:1;s:24:\"special_offer_expires_at\";i:1893456000;s:12:\"is_exclusive\";i:0;s:5:\"views\";i:2900;s:9:\"color_ids\";s:3:\"1,4\";s:13:\"guarantee_ids\";s:1:\"1\";s:21:\"secondary_category_id\";i:0;s:6:\"weight\";i:250;s:10:\"colorTitle\";N;s:13:\"garanteeTitle\";N;s:13:\"discountTotal\";d:52.5;}}', '', 0, 1, 5, 1, 1, 0, 0, 0, '', '', 1783953509, '1405/04/22', 'ORD-1783953509-8086', '', NULL),
(5, 'cs_test_0ba6c640d369eac72f710482e126263d', 'pi_test_4e9c95733b3ff759', 1450, '', 0, '', '', '', '', '', 1, 'a:1:{i:0;a:20:{s:5:\"tedad\";i:1;s:7:\"cartRow\";i:72;s:2:\"id\";i:1;s:5:\"title\";s:25:\"iPhone 15 Pro Max - 256Go\";s:5:\"price\";i:1450;s:11:\"category_id\";i:1;s:11:\"description\";s:0:\"\";s:14:\"stock_quantity\";i:1;s:16:\"discount_percent\";i:0;s:16:\"is_special_offer\";i:0;s:24:\"special_offer_expires_at\";i:0;s:12:\"is_exclusive\";i:1;s:5:\"views\";i:4506;s:9:\"color_ids\";s:0:\"\";s:13:\"guarantee_ids\";s:0:\"\";s:21:\"secondary_category_id\";i:0;s:6:\"weight\";i:221;s:10:\"colorTitle\";N;s:13:\"garanteeTitle\";N;s:13:\"discountTotal\";i:0;}}', '', 0, 1, 1, 1, 1, 0, 0, 0, '', '', 1784072549, '1405/04/24', 'ORD-1784072549-9824', '', NULL),
(6, 'cs_test_e5ea57035e42d802795d847b8333a579', 'pi_test_604f5156cbfb2fe1', 1465, 'abbas', 0, 'Paris', 'paris', '75018', '0602234983', '0602238983', 1, 'a:1:{i:0;a:20:{s:5:\"tedad\";i:1;s:7:\"cartRow\";i:72;s:2:\"id\";i:1;s:5:\"title\";s:25:\"iPhone 15 Pro Max - 256Go\";s:5:\"price\";i:1450;s:11:\"category_id\";i:1;s:11:\"description\";s:0:\"\";s:14:\"stock_quantity\";i:1;s:16:\"discount_percent\";i:0;s:16:\"is_special_offer\";i:0;s:24:\"special_offer_expires_at\";i:0;s:12:\"is_exclusive\";i:1;s:5:\"views\";i:4506;s:9:\"color_ids\";s:0:\"\";s:13:\"guarantee_ids\";s:0:\"\";s:21:\"secondary_category_id\";i:0;s:6:\"weight\";i:221;s:10:\"colorTitle\";N;s:13:\"garanteeTitle\";N;s:13:\"discountTotal\";i:0;}}', '106 boulevard ney', 15, 1, 1, 1, 1, 0, 0, 0, '', '', 1784073793, '2026-07-15 00:03:13', 'ORD-1784073793-9772', '', NULL),
(7, 'cs_test_5d35d0398ee937391dba8a7f4a755421', 'pi_test_d1ad4ab8e29e3725', 1863, 'njknkj', 0, 'paris', 'paris', '75018', '0602238983', '0602238983', 1, 'a:1:{i:0;a:20:{s:5:\"tedad\";i:1;s:7:\"cartRow\";i:73;s:2:\"id\";i:6;s:5:\"title\";s:21:\"Asus ROG Zephyrus G14\";s:5:\"price\";i:2100;s:11:\"category_id\";i:2;s:11:\"description\";s:24:\"PC Gamer ultra-portable.\";s:14:\"stock_quantity\";i:20;s:16:\"discount_percent\";i:12;s:16:\"is_special_offer\";i:1;s:24:\"special_offer_expires_at\";i:1893456000;s:12:\"is_exclusive\";i:0;s:5:\"views\";i:1500;s:9:\"color_ids\";s:3:\"1,3\";s:13:\"guarantee_ids\";s:1:\"2\";s:21:\"secondary_category_id\";i:0;s:6:\"weight\";i:1600;s:10:\"colorTitle\";N;s:13:\"garanteeTitle\";N;s:13:\"discountTotal\";i:252;}}', '106 boulevard ney', 15, 1, 1, 1, 1, 0, 0, 0, '', '', 1784099646, '2026-07-15 07:14:06', 'ORD-1784099646-6635', '', NULL),
(8, 'cs_test_d516cf7a6372cb899d8cffb0a72d4fce', 'pi_test_1e602136ac6d2066', 1863, 'njknkj', 0, 'paris', 'paris', '75018', '0602238983', '0602238983', 1, 'a:1:{i:0;a:20:{s:5:\"\";i:1;s:7:\"cartRow\";i:74;s:2:\"id\";i:6;s:5:\"title\";s:21:\"Asus ROG Zephyrus G14\";s:5:\"price\";i:2100;s:11:\"category_id\";i:2;s:11:\"description\";s:24:\"PC Gamer ultra-portable.\";s:14:\"stock_quantity\";i:20;s:16:\"discount_percent\";i:12;s:16:\"is_special_offer\";i:1;s:24:\"special_offer_expires_at\";i:1893456000;s:12:\"is_exclusive\";i:0;s:5:\"views\";i:1500;s:9:\"color_ids\";s:3:\"1,3\";s:13:\"guarantee_ids\";s:1:\"2\";s:21:\"secondary_category_id\";i:0;s:6:\"weight\";i:1600;s:10:\"colorTitle\";N;s:13:\"garanteeTitle\";N;s:13:\"discountTotal\";i:252;}}', '106 boulevard ney', 15, 1, 1, 1, 1, 0, 0, 0, '', '', 1784103108, '2026-07-15 08:11:48', 'ORD-1784103108-9162', '', NULL),
(9, 'cs_test_4367dfe866f779e177a82e88b2c3bc26', 'pi_test_74be38c4d005519f', 313, 'njknkj', 0, 'paris', 'paris', '75018', '0602238983', '0602238983', 1, 'a:1:{i:0;a:20:{s:5:\"tedad\";i:1;s:7:\"cartRow\";i:75;s:2:\"id\";i:4;s:5:\"title\";s:15:\"sony wh-1000xm6\";s:5:\"price\";i:350;s:11:\"category_id\";i:4;s:11:\"description\";s:37:\"Casque à réduction de bruit active.\";s:14:\"stock_quantity\";i:100;s:16:\"discount_percent\";i:15;s:16:\"is_special_offer\";i:1;s:24:\"special_offer_expires_at\";i:1893456000;s:12:\"is_exclusive\";i:0;s:5:\"views\";i:2901;s:9:\"color_ids\";s:0:\"\";s:13:\"guarantee_ids\";s:0:\"\";s:21:\"secondary_category_id\";i:0;s:6:\"weight\";i:250;s:10:\"colorTitle\";N;s:13:\"garanteeTitle\";N;s:13:\"discountTotal\";d:52.5;}}', '106 boulevard ney', 15, 1, 2, 1, 1, 0, 0, 0, '', '', 1784126463, '2026-07-15 14:41:03', 'ORD-1784126463-9007', '', NULL),
(10, '', '', 3140, 'jawad zafari', 0, 'sfdghj', 'paris', '75018', '1234567890', '', 2, 'a:2:{i:0;a:20:{s:8:\"quantity\";i:4;s:7:\"cartRow\";i:170;s:2:\"id\";i:24;s:5:\"title\";s:21:\"Logitech MX Master 3S\";s:5:\"price\";i:110;s:11:\"category_id\";i:3;s:11:\"description\";s:37:\"La meilleure souris de productivité.\";s:14:\"stock_quantity\";i:95;s:16:\"discount_percent\";i:5;s:16:\"is_special_offer\";i:1;s:24:\"special_offer_expires_at\";i:1893456000;s:12:\"is_exclusive\";i:1;s:5:\"views\";i:2606;s:9:\"color_ids\";s:0:\"\";s:13:\"guarantee_ids\";s:0:\"\";s:21:\"secondary_category_id\";i:0;s:6:\"weight\";i:141;s:10:\"colorTitle\";N;s:13:\"garanteeTitle\";N;s:13:\"discountTotal\";d:22;}i:1;a:20:{s:8:\"quantity\";i:2;s:7:\"cartRow\";i:171;s:2:\"id\";i:2;s:5:\"title\";s:24:\"Samsung Galaxy S25 Ultra\";s:5:\"price\";i:1350;s:11:\"category_id\";i:1;s:11:\"description\";s:36:\"Le smartphone IA ultime par Samsung.\";s:14:\"stock_quantity\";i:40;s:16:\"discount_percent\";i:10;s:16:\"is_special_offer\";i:1;s:24:\"special_offer_expires_at\";i:1893456000;s:12:\"is_exclusive\";i:0;s:5:\"views\";i:5202;s:9:\"color_ids\";s:0:\"\";s:13:\"guarantee_ids\";s:0:\"\";s:21:\"secondary_category_id\";i:0;s:6:\"weight\";i:232;s:10:\"colorTitle\";N;s:13:\"garanteeTitle\";N;s:13:\"discountTotal\";i:270;}}', '106 boulevard ney', 0, 2, 1, 0, 1, 0, 0, 0, '', '', 1784841566, '2026-07-23 23:19:26', 'ORD-1784841566-5932', '', NULL),
(11, '', '', 0, 'jawad zafari', 0, 'sfdghj', 'paris', '75018', '1234567890', '', 2, 'a:0:{}', '106 boulevard ney', 0, 2, 1, 0, 1, 0, 0, 0, '', '', 1784841615, '2026-07-23 23:20:15', 'ORD-1784841615-4810', '', NULL),
(12, '', '', 110, 'jawad zafari', 0, 'sfdghj', 'paris', '75018', '1234567890', '', 2, 'a:1:{i:0;a:20:{s:8:\"quantity\";i:1;s:7:\"cartRow\";i:172;s:2:\"id\";i:24;s:5:\"title\";s:21:\"Logitech MX Master 3S\";s:5:\"price\";i:110;s:11:\"category_id\";i:3;s:11:\"description\";s:37:\"La meilleure souris de productivité.\";s:14:\"stock_quantity\";i:95;s:16:\"discount_percent\";i:5;s:16:\"is_special_offer\";i:1;s:24:\"special_offer_expires_at\";i:1893456000;s:12:\"is_exclusive\";i:1;s:5:\"views\";i:2606;s:9:\"color_ids\";s:0:\"\";s:13:\"guarantee_ids\";s:0:\"\";s:21:\"secondary_category_id\";i:0;s:6:\"weight\";i:141;s:10:\"colorTitle\";N;s:13:\"garanteeTitle\";N;s:13:\"discountTotal\";d:5.5;}}', '106 boulevard ney', 0, 2, 1, 0, 1, 0, 0, 0, '', '', 1784841677, '2026-07-23 23:21:17', 'ORD-1784841677-2557', '', NULL),
(13, 'cs_test_d83a32ff07921a864dc61e9ebfce4caf', 'pi_test_a22bd472eaf40112', 2100, 'jawad zafari', 0, 'sfdghj', 'paris', '75018', '1234567890', '', 2, 'a:1:{i:0;a:20:{s:8:\"quantity\";i:1;s:7:\"cartRow\";i:173;s:2:\"id\";i:6;s:5:\"title\";s:21:\"Asus ROG Zephyrus G16\";s:5:\"price\";i:2100;s:11:\"category_id\";i:2;s:11:\"description\";s:24:\"PC Gamer ultra-portable.\";s:14:\"stock_quantity\";i:20;s:16:\"discount_percent\";i:12;s:16:\"is_special_offer\";i:1;s:24:\"special_offer_expires_at\";i:1893456000;s:12:\"is_exclusive\";i:0;s:5:\"views\";i:1533;s:9:\"color_ids\";s:0:\"\";s:13:\"guarantee_ids\";s:0:\"\";s:21:\"secondary_category_id\";i:0;s:6:\"weight\";i:1600;s:10:\"colorTitle\";N;s:13:\"garanteeTitle\";N;s:13:\"discountTotal\";i:252;}}', '106 boulevard ney', 0, 2, 4, 1, 1, 0, 0, 0, '', '', 1784842583, '2026-07-23 23:36:23', 'ORD-1784842583-4156', '', NULL),
(14, 'cs_test_b0fb6822ee132973b483cd60443eb816', 'pi_test_0ebb0eee60194e52', 0, 'jawad zafari', 0, 'sfdghj', 'paris', '75018', '1234567890', '', 2, 'a:0:{}', '106 boulevard ney', 0, 2, 4, 1, 1, 0, 0, 0, '', '', 1784842860, '2026-07-23 23:41:00', 'ORD-1784842860-1363', '', NULL),
(15, 'cs_test_4398d633c5fb89a626f5718b1e979042', 'pi_test_516bcdd1bb890e6c', 350, 'jawad zafari', 0, 'sfdghj', 'paris', '75018', '1234567890', '', 2, 'a:1:{i:0;a:20:{s:8:\"quantity\";i:1;s:7:\"cartRow\";i:175;s:2:\"id\";i:10;s:5:\"title\";s:21:\"Samsung Galaxy Watch8\";s:5:\"price\";i:350;s:11:\"category_id\";i:3;s:11:\"description\";s:224:\"Aperçu du produit : 2025 Couleur du corps Anthracite Anthracite Taille d’écran 1,3 pouces Résolution d\'écran 438 x 438 pixels Réseau sans GPS Unisexe Compatible Android Boîtier Aluminium Bracelet Silicone Écran OLED\";s:14:\"stock_quantity\";i:75;s:16:\"discount_percent\";i:10;s:16:\"is_special_offer\";i:1;s:24:\"special_offer_expires_at\";i:1893456000;s:12:\"is_exclusive\";i:0;s:5:\"views\";i:1904;s:9:\"color_ids\";s:0:\"\";s:13:\"guarantee_ids\";s:0:\"\";s:21:\"secondary_category_id\";i:0;s:6:\"weight\";i:50;s:10:\"colorTitle\";N;s:13:\"garanteeTitle\";N;s:13:\"discountTotal\";i:35;}}', '106 boulevard ney', 0, 2, 4, 1, 1, 0, 0, 0, '', '', 1784843283, '2026-07-23 23:48:03', 'ORD-1784843283-4502', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_statuses`
--

CREATE TABLE `order_statuses` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_statuses`
--

INSERT INTO `order_statuses` (`id`, `title`) VALUES
(1, 'En attente de confirmation'),
(2, 'Confirmée'),
(3, 'En attente de paiement'),
(4, 'Payée'),
(5, 'En cours de préparation'),
(6, 'Prête pour l\'expédition'),
(7, 'Expédiée');

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `title`) VALUES
(1, 'Passerelle de paiement (En ligne)'),
(2, 'Virement Bancaire'),
(3, 'Paiement à la livraison');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int NOT NULL,
  `category_id` int NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock_quantity` int NOT NULL DEFAULT '0',
  `discount_percent` int NOT NULL DEFAULT '0',
  `is_special_offer` tinyint(1) NOT NULL DEFAULT '0',
  `special_offer_expires_at` int NOT NULL DEFAULT '0',
  `is_exclusive` tinyint(1) NOT NULL DEFAULT '0',
  `views` int NOT NULL DEFAULT '0',
  `color_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guarantee_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `secondary_category_id` int NOT NULL DEFAULT '0',
  `weight` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `title`, `price`, `category_id`, `description`, `stock_quantity`, `discount_percent`, `is_special_offer`, `special_offer_expires_at`, `is_exclusive`, `views`, `color_ids`, `guarantee_ids`, `secondary_category_id`, `weight`) VALUES
(1, 'iPhone 17 Pro Max - 256Go', 1450, 1, '\r\nTéléphone Apple iPhone 17 Pro Max - Apple\r\nFabricant : Apple\r\nRéférence fabricant : MFYN4QL/A - MFYN4F/A\r\n\r\nProduit soumis à la Rémunération Pour Copie Privée. En savoir plus\r\nLa puissance fournie par le chargeur doit être entre, au minimum, 4,5 Watts requis par l\'équipement radioélectrique et, au maximum, 30 Watts pour atteindre la vitesse de chargement maximale.', 1, 0, 0, 0, 1, 4515, '', '', 0, 221),
(2, 'Samsung Galaxy S25 Ultra', 1350, 1, 'Le smartphone IA ultime par Samsung.', 40, 10, 1, 1893456000, 0, 5202, '', '', 0, 232),
(3, 'MacBook Pro 16', 3200, 2, 'Puissance extrême pour les pros.', 15, 0, 0, 0, 1, 3805, '', '', 0, 2100),
(4, 'sony wh-1000xm6', 350, 4, 'Casque à réduction de bruit active.', 100, 15, 1, 1893456000, 0, 2967, '', '', 0, 250),
(5, 'AirPods Pro 4', 160, 4, 'Écouteurs sans fil Apple haute fidélité.', 80, 0, 0, 0, 0, 3101, '', '', 0, 50),
(6, 'Asus ROG Zephyrus G16', 2100, 2, 'PC Gamer ultra-portable.', 20, 12, 1, 1893456000, 0, 1535, '', '', 0, 1600),
(7, 'Google Pixel 10 Pro', 1100, 1, 'Le meilleur photophone Android.', 30, 8, 0, 0, 0, 2100, '', '', 0, 213),
(8, 'iPad Air 13', 750, 2, 'Tablette Apple polyvalente.', 60, 5, 0, 0, 1, 1803, '', '', 0, 460),
(9, 'Apple Watch Series 12', 450, 3, 'Montre connectée santé et sport.', 90, 0, 0, 0, 0, 4102, '', '', 0, 45),
(10, 'Samsung Galaxy Watch8', 350, 3, 'Aperçu du produit : 2025 Couleur du corps Anthracite Anthracite Taille d’écran 1,3 pouces Résolution d\'écran 438 x 438 pixels Réseau sans GPS Unisexe Compatible Android Boîtier Aluminium Bracelet Silicone Écran OLED', 75, 10, 1, 1893456000, 0, 1904, '', '', 0, 50),
(11, 'Dell XPS 17', 2400, 2, 'L\'ordinateur portable Windows premium.', 25, 0, 0, 0, 1, 1200, '', '', 0, 1800),
(12, 'Xiaomi Pixel Buds 5 Pro', 200, 4, 'Écouteurs intelligents.', 120, 20, 1, 1893456000, 0, 800, '', '', 0, 40),
(13, 'Anker_Chargeur Rapide 100W', 45, 3, 'Chargeur ultra-rapide multi-ports.', 300, 0, 0, 0, 0, 3500, '', '', 0, 100),
(14, 'Câble USB-C Tressé 2M', 20, 3, 'Câble ultra-résistant.', 500, 0, 0, 0, 0, 1101, '', '', 0, 50),
(15, 'Samsung Galaxy Z Fold 8', 1800, 1, 'Le smartphone pliable premium.', 10, 15, 1, 1893456000, 1, 2802, '', '', 0, 253),
(16, 'Lenovo ThinkPad T 490', 2600, 2, 'L\'ultime PC portable professionnel.', 18, 0, 0, 0, 0, 950, '', '', 0, 1100),
(17, 'JBL Flip 6', 300, 4, 'Casque sans fil abordable et puissant.', 85, 10, 0, 0, 0, 1703, '', '', 0, 275),
(18, 'Batterie externe Anker Prime (20 000 mAh, 200 W)', 60, 3, 'Powerbank haute capacité.', 150, 5, 0, 0, 0, 2300, '', '', 0, 400),
(19, 'Coque Silicone iPhone 17', 35, 3, 'Protection souple et résistante.', 200, 0, 0, 0, 0, 3201, '', '', 0, 30),
(20, 'OnePlus 13 Pro', 950, 1, 'Le flagship killer de l\'année.', 45, 8, 1, 1893456000, 0, 1602, '', '', 0, 205),
(21, 'Bose Charge 5', 140, 4, 'Enceinte Bluetooth étanche.', 110, 12, 0, 0, 1, 1451, '', '', 0, 960),
(22, 'Ecran Dell UltraSharp 27\"', 600, 3, 'Moniteur 4K pour professionnels.', 35, 0, 0, 0, 0, 851, '', '', 0, 5500),
(23, 'Microphone Blue Yeti', 120, 4, 'Micro USB pour streaming et podcasts.', 65, 0, 0, 0, 0, 1150, '', '', 0, 1000),
(24, 'Logitech MX Master 3S', 110, 3, 'La meilleure souris de productivité.', 95, 5, 1, 1893456000, 1, 2606, '', '', 0, 141),
(25, 'Clavier Mécanique Keychron', 150, 3, 'Clavier sans fil pour développeurs.', 55, 0, 0, 0, 0, 901, '', '', 0, 850),
(26, 'Samsung Galaxy A55', 450, 1, 'Le milieu de gamme parfait.', 150, 10, 0, 0, 0, 3401, '', '', 0, 202),
(27, 'HP Envy x360', 1400, 2, 'PC portable convertible 2-en-1.', 25, 0, 0, 0, 0, 781, '', '', 0, 1700),
(28, 'AirTag Apple', 35, 3, 'Traceur Bluetooth intelligent.', 300, 0, 0, 0, 1, 4208, '', '', 0, 11),
(29, 'Support Ordinateur Alu1', 45, 3, 'Support ergonomique et ventilé.', 120, 15, 1, 1893456000, 0, 1365, '', '', 0, 600),
(30, 'Console PlayStation 5 Pro', 150, 4, 'Casque gaming sans perte.', 60, 0, 0, 0, 0, 2201, '', '', 0, 347);

-- --------------------------------------------------------

--
-- Table structure for table `product_attributes`
--

CREATE TABLE `product_attributes` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `attribute_id` int NOT NULL,
  `attribute_value_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_colors`
--

CREATE TABLE `product_colors` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `color_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_galleries`
--

CREATE TABLE `product_galleries` (
  `id` int NOT NULL,
  `image_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` int NOT NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT '0',
  `is_3d` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_galleries`
--

INSERT INTO `product_galleries` (`id`, `image_name`, `product_id`, `is_main`, `is_3d`) VALUES
(40, '1784125674_935.jpg', 30, 0, 0),
(41, '1784125848_543.jpg', 29, 0, 0),
(42, '1784125917_820.jpg', 28, 0, 0),
(43, '1784125967_551.jpg', 27, 0, 0),
(44, '1784126022_476.jpg', 26, 0, 0),
(45, '1784126071_309.jpg', 25, 0, 0),
(47, '1784126164_282.jpg', 23, 0, 0),
(48, '1784126214_728.jpg', 22, 0, 0),
(49, '1784126257_659.jpg', 21, 0, 0),
(50, '1784126311_861.jpg', 20, 0, 0),
(51, '1784143788_891.jpg', 19, 0, 0),
(52, '1784143921_870.webp', 18, 0, 0),
(53, '1784143972_662.jpg', 17, 0, 0),
(54, '1784144035_548.jpg', 16, 0, 0),
(55, '1784144093_993.jpg', 15, 0, 0),
(56, '1784144142_623.webp', 14, 0, 0),
(57, '1784144235_119.webp', 13, 0, 0),
(58, '1784144287_568.webp', 12, 0, 0),
(59, '1784144344_665.jpg', 11, 0, 0),
(60, '1784144408_522.jpg', 9, 0, 0),
(61, '1784144470_667.jpg', 8, 0, 0),
(62, '1784144528_681.jpg', 7, 0, 0),
(63, '1784144606_249.jpg', 6, 0, 0),
(64, '1784144649_439.jpg', 4, 0, 0),
(66, '1784201305_595.webp', 24, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_guarantees`
--

CREATE TABLE `product_guarantees` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `guarantee_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_reviews`
--

INSERT INTO `product_reviews` (`id`, `title`, `description`, `product_id`) VALUES
(1, 'Design et Performances', 'Ce produit offre des performances exceptionnelles avec un design très soigné. Idéal pour les professionnels.', 6),
(2, 'Autonomie', 'La batterie dure toute la journée sans aucun problème, même avec une utilisation intensive.', 6);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `parent_id` int NOT NULL DEFAULT '0',
  `product_id` int NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `content`, `created_at`, `user_id`, `parent_id`, `product_id`, `is_approved`) VALUES
(1, 'kqhcoqchsqocsqhcsochsiocshc', '2026-07-16 07:19:04', 1, 0, 4, 1),
(2, 'kdsckdcndkcndckjdncdkjcnncdcndncdcncjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjj', '2026-07-16 07:19:28', 1, 1, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `review_parameters`
--

CREATE TABLE `review_parameters` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `review_parameters`
--

INSERT INTO `review_parameters` (`id`, `title`, `category_id`) VALUES
(1, 'Qualité du son', 4),
(2, 'Confort et ergonomie', 4),
(3, 'Rapport qualité/prix', 4);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `setting_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'special_time', '172800'),
(2, 'limit_slider', '10'),
(3, 'tel', '+33602234983'),
(4, 'email', 'contact@maboutique.fr'),
(5, 'root', 'http://localhost/MaBoutique/'),
(6, 'banner_active', '1'),
(7, 'banner_text', 'Vente flash dvjhvhjvjjjjjjjvhhjvu week-end : plus que'),
(8, 'banner_code', 'Code : WEEK'),
(9, 'banner_endtime', '2026-07-20T23:59:59'),
(10, 'maintenance_mode', '0'),
(11, 'store_address', '123 Avenue des Champs-Élysées, 75008 Paris'),
(12, 'tax_percent', '20'),
(13, 'shipping_cost', '5.00'),
(14, 'social_instagram', ''),
(15, 'social_facebook', ''),
(16, 'tv_video_link', 'https://youtube.com/shorts/Vi5hIyCq1m4?si=Lp4bRqaW4dwLekZO'),
(17, 'tv_cover_image', 'public/images/tv_cover.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_methods`
--

CREATE TABLE `shipping_methods` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shipping_methods`
--

INSERT INTO `shipping_methods` (`id`, `title`, `description`, `price`) VALUES
(1, 'Livraison Express (24h)', 'Livraison rapide entre 24h et 72h max.', 5),
(2, 'Livraison Standard', 'Livraison normale entre 3 et 7 jours ouvrés.', 0);

-- --------------------------------------------------------

--
-- Table structure for table `sliders`
--

CREATE TABLE `sliders` (
  `id` int NOT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `button_text` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Découvrir',
  `text_color` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '#ffffff'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sliders`
--

INSERT INTO `sliders` (`id`, `image_path`, `link`, `title`, `description`, `button_text`, `text_color`) VALUES
(4, 'public/images/slider/slide_6a5950c16ac2e_1784238273.jpg', 'http://localhost/MaBoutique/Collection/index/category/3/1', ' La nouvelle collection est là', 'Découvrez nos meilleurs produits depuis la base de données.', 'Voir la collection', '#ffffff'),
(5, 'public/images/slider/slide_6a59509c31bfb_1784238236.jpg', 'http://localhost/MaBoutique/Collection/index/category/3/1', 'Offres Spéciales', 'Profitez de réductions allant jusqu\'à -50%.', 'En profiter', '#1b1bde');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `national_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_date` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` tinyint(1) NOT NULL DEFAULT '1',
  `newsletter` tinyint(1) NOT NULL DEFAULT '0',
  `role_id` int NOT NULL DEFAULT '3',
  `created_at` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `password`, `last_name`, `national_id`, `phone`, `mobile`, `birth_date`, `address`, `city`, `postal_code`, `gender`, `newsletter`, `role_id`, `created_at`) VALUES
(2, 'jzafari100@gmail.com', '', '$2y$12$i1c8QPefJgJU2oS2Zk5SzuxhwPFaQVGSU0/YDAlI4cl4vvahgrYHu', 'jawad zafari', '', '', '0602234983', '', '', '', '', 1, 0, 1, '2026/07/23');

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `province_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `city_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `neighborhood` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `province_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `city_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_addresses`
--

INSERT INTO `user_addresses` (`id`, `user_id`, `last_name`, `mobile`, `phone`, `province_id`, `city_id`, `neighborhood`, `address`, `postal_code`, `province_name`, `city_name`) VALUES
(2, 1, 'njknkj', '0602238983', '0602238983', '1', '1', '', '106 boulevard ney', '75018', 'paris', 'paris'),
(3, 2, 'Cinetech', '0602238983', '', '', '', '', '106 boulevard ney', '75018', 'Paris', 'paris'),
(4, 2, 'Cinetech', '0602238983', '', '', '', '', '106 boulevard ney', '75018', 'ouiouio', 'paris'),
(5, 2, 'jawad zafari', '1234567890', '', '', '', '', '106 boulevard ney', '75018', 'sfdghj', 'paris');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `title`) VALUES
(1, 'Administrateur'),
(2, 'Employé'),
(3, 'Client');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attributes`
--
ALTER TABLE `attributes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attribute_values`
--
ALTER TABLE `attribute_values`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comment_scores`
--
ALTER TABLE `comment_scores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discount_codes`
--
ALTER TABLE `discount_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guarantees`
--
ALTER TABLE `guarantees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_statuses`
--
ALTER TABLE `order_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_attributes`
--
ALTER TABLE `product_attributes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_colors`
--
ALTER TABLE `product_colors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_galleries`
--
ALTER TABLE `product_galleries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_guarantees`
--
ALTER TABLE `product_guarantees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `review_parameters`
--
ALTER TABLE `review_parameters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shipping_methods`
--
ALTER TABLE `shipping_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attributes`
--
ALTER TABLE `attributes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attribute_values`
--
ALTER TABLE `attribute_values`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=178;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `colors`
--
ALTER TABLE `colors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `comment_scores`
--
ALTER TABLE `comment_scores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `discount_codes`
--
ALTER TABLE `discount_codes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `guarantees`
--
ALTER TABLE `guarantees`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `order_statuses`
--
ALTER TABLE `order_statuses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `product_attributes`
--
ALTER TABLE `product_attributes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_colors`
--
ALTER TABLE `product_colors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_galleries`
--
ALTER TABLE `product_galleries`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `product_guarantees`
--
ALTER TABLE `product_guarantees`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `review_parameters`
--
ALTER TABLE `review_parameters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `shipping_methods`
--
ALTER TABLE `shipping_methods`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
