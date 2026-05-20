-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2026 at 03:17 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `menu`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `action` varchar(50) NOT NULL COMMENT 'create, update, delete, login',
  `entity` varchar(50) NOT NULL COMMENT 'item, category, settings, etc.',
  `entity_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `cat_id` int(11) NOT NULL,
  `cat_name` text NOT NULL,
  `cat_picture` text NOT NULL,
  `cat_icon` text DEFAULT NULL,
  `Order` int(11) NOT NULL,
  `cat_footer` text DEFAULT NULL,
  `cat_footer_bottom` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_id`, `cat_name`, `cat_picture`, `cat_icon`, `Order`, `cat_footer`, `cat_footer_bottom`) VALUES
(1, 'OUR FAMOUS PIES', '', '', 0, '', NULL),
(2, 'A LA CARTE KABOBS', '', '', 0, NULL, NULL),
(3, 'COMBOS', '', '', 0, 'ALL COMBOS COME WITH PITA BREAD & GARLIC SAUCE!', 'You must order at least 1 hour earlier!'),
(4, 'WRAPS', '', '', 0, 'Wrapped in Pita', NULL),
(5, 'DIPS & APPETIZERS', '', '', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contact_submissions`
--

CREATE TABLE `contact_submissions` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phonenumber` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `photo_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `photo_path`, `created_at`) VALUES
(10, 'admin/pics/VIBE-6a097bdee405e3.18468677.jpeg', '2026-05-17 08:27:10'),
(11, 'admin/pics/VIBE-6a097bdee5e3b5.07149715.jpeg', '2026-05-17 08:27:10'),
(12, 'admin/pics/VIBE-6a097bdee738e4.20778092.jpeg', '2026-05-17 08:27:10'),
(13, 'admin/pics/VIBE-6a097bdee89521.88826768.jpeg', '2026-05-17 08:27:10'),
(14, 'admin/pics/VIBE-6a097bdee9c529.17942774.jpeg', '2026-05-17 08:27:10'),
(15, 'admin/pics/VIBE-6a097bdeeae615.71194129.jpeg', '2026-05-17 08:27:10'),
(16, 'admin/pics/VIBE-6a097bdeec25b5.78933111.jpeg', '2026-05-17 08:27:10'),
(17, 'admin/pics/VIBE-6a097bdeed6b57.68382703.jpeg', '2026-05-17 08:27:10'),
(18, 'admin/pics/VIBE-6a097bdeee86c6.40744973.jpeg', '2026-05-17 08:27:10'),
(19, 'admin/pics/VIBE-6a097bdef018a5.16114756.jpeg', '2026-05-17 08:27:10');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `item_name` text NOT NULL,
  `item_category` text NOT NULL,
  `Ingredients` text NOT NULL,
  `item_pic` text NOT NULL,
  `Order` int(11) NOT NULL DEFAULT 0,
  `item_priceusd` double NOT NULL,
  `price_suffix` varchar(20) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `item_name`, `item_category`, `Ingredients`, `item_pic`, `Order`, `item_priceusd`, `price_suffix`) VALUES
(1, 'Meat Pie', 'Our Famous Pies', '', '', 0, 3.5, ''),
(2, 'Cheese Pie', 'Our Famous Pies', '', '', 0, 3.5, ''),
(3, 'Spinach Pie', 'Our Famous Pies', '', '', 0, 3, ''),
(4, 'Spinach & Feta Pie', 'Our Famous Pies', '', '', 0, 3.99, ''),
(5, 'Sujuk Pie', 'Our Famous Pies', '', '', 0, 5, ''),
(6, '1/2 Zaatar - 1/2 Cheese Bread', 'Our Famous Pies', '', '', 0, 3, ''),
(7, 'Zaatar Bread', 'Our Famous Pies', '', '', 0, 2.5, ''),
(8, 'Kishek Bread', 'Our Famous Pies', '', '', 0, 3.5, ''),
(9, 'Chicken Tawouk Skewer', 'A LA CARTE KABOBS', '', '', 0, 4.5, ''),
(10, 'Kafta Kabob Skewer', 'A LA CARTE KABOBS', '', '', 0, 4.5, ''),
(11, 'Beef Kabob Skewer', 'A LA CARTE KABOBS', '', '', 0, 5.5, ''),
(12, 'Lamb Kabob Skewer', 'A LA CARTE KABOBS', '', '', 0, 5.5, ''),
(13, 'Chicken', 'WRAPS', 'Strips of marinated chicken breast with lettuce, tomatoes,\r\npickles, pickled turnips, and garlic sauce', '', 0, 10, ''),
(14, 'Beef Shawarma', 'WRAPS', 'Strips of marinated beef with parsley, onions, tomatoes,\r\npickles, pickled turnips, and tahini sauce', '', 0, 11, ''),
(15, 'Falafel Wrap', 'WRAPS', 'Falafel patties with lettuce, tomatoes, pickles, pickled turnips, parsley, and tahini sauce', '', 0, 7, ''),
(16, 'Kafta Wrap', 'WRAPS', 'Grilled ground beef with onions, parsley, spices, tomatoes,\r\nonions, pickles, pickled turnips, parsley, and tahini sauce', '', 0, 11, ''),
(17, 'Shish Tawook Wrap', 'WRAPS', 'Grilled chicken cubes with lettuce, tomatoes, pickles, pickled turnips, and garlic sauce', '', 0, 11, ''),
(18, 'Shish Kabob Wrap', 'WRAPS', 'Grilled beef tenderloin cubes with tomatoes, onions, parsley, pickles, pickled turnips, and tahini sauce', '', 0, 11, ''),
(19, 'Lamb Kabob Wrap', 'WRAPS', 'Grilled lamb cubes with tomatoes, onions, parsley, pickles, pickled turnips, and tahini sauce', '', 0, 12, ''),
(20, 'Hummus Wrap', 'WRAPS', 'Hummus with lettuce, tomatoes, and pickles', '', 0, 7, ''),
(21, 'Baba Ghanouj Wrap', 'WRAPS', 'Baba ghanouj with lettuce, tomatoes, and pickles', '', 0, 7, ''),
(22, '4 PEOPLE', 'COMBOS', '2 beef kabobs, 2 chicken kabobs, 2 kafta kabobs, 6 pcs of falafel, 4 kibbeh balls, salad, hummus, rice, and pita', '', 0, 85, ''),
(23, '6 PEOPLE', 'COMBOS', '4 beef kabobs, 4 chicken kabobs, 4 kafta kabobs, 12 pcs of falafel, 6 kibbeh balls, salad, hummus, rice, and pita', '', 0, 120, ''),
(24, '10 PEOPLE', 'COMBOS', '6 beef kabobs, 6 chicken kabobs, 6 kafta kabobs, 24 pcs of falafel, 10 kibbeh balls, salad, hummus, rice, and pita', '', 0, 180, ''),
(25, 'Hummus', 'DIPS & APPETIZERS', '', '', 0, 8.5, '/lb'),
(26, 'Baba Ghanouj', 'DIPS & APPETIZERS', '', '', 0, 8.5, '/lb'),
(27, 'Tabbouli', 'DIPS & APPETIZERS', '', '', 0, 9, '/lb'),
(28, 'Fattoush', 'DIPS & APPETIZERS', '', '', 0, 9, ''),
(29, 'Shankleesh', 'DIPS & APPETIZERS', '', '', 0, 10, '/lb'),
(30, 'Grape Leaves', 'DIPS & APPETIZERS', '', '', 0, 8, '/lb'),
(31, 'Moujadara', 'DIPS & APPETIZERS', '', '', 0, 8, '/lb'),
(32, 'Labneh', 'DIPS & APPETIZERS', '', '', 0, 7.5, '/lb'),
(33, 'Labneh with Garlic', 'DIPS & APPETIZERS', '', '', 0, 8.5, '/lb'),
(34, 'Garlic Sauce', 'DIPS & APPETIZERS', '', '', 0, 10, '/lb'),
(35, 'Tzatziki (Gyro) Sauce', 'DIPS & APPETIZERS', '', '', 0, 7, '/lb'),
(36, 'Falafel', 'DIPS & APPETIZERS', '', '', 0, 8, '/doz'),
(37, 'Kibbeh Balls', 'DIPS & APPETIZERS', '', '', 0, 3, 'ea'),
(38, 'French Fries', 'DIPS & APPETIZERS', '', '', 0, 6, 'LG'),
(39, 'French Fries', 'DIPS & APPETIZERS', '', '', 0, 3, 'SM');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `total_lbp` bigint(20) DEFAULT 0,
  `total_usd` decimal(10,2) DEFAULT 0.00,
  `whatsapp_number` varchar(20) DEFAULT NULL,
  `items_json` text NOT NULL COMMENT 'JSON array of order items',
  `status` enum('pending','sent','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `restaurant_name` varchar(255) NOT NULL,
  `restaurant_logo` varchar(255) DEFAULT NULL,
  `restaurant_email` varchar(255) DEFAULT NULL,
  `restaurant_phone` varchar(50) DEFAULT NULL,
  `restaurant_address` text DEFAULT NULL,
  `restaurant_maps` text DEFAULT NULL,
  `restaurant_description` text DEFAULT NULL,
  `opening_hours` varchar(255) DEFAULT NULL,
  `opening_title` varchar(255) DEFAULT 'Open Daily',
  `home_bg` varchar(255) DEFAULT 'bgs/home-bg.jpg',
  `menu_bg` varchar(255) DEFAULT 'bgs/menu-bg.jpg',
  `contact_bg` varchar(255) DEFAULT NULL,
  `whatsapp_number` varchar(50) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `chat_id` bigint(255) NOT NULL,
  `bot_token` text NOT NULL,
  `country_code` varchar(10) NOT NULL,
  `order_method` varchar(50) DEFAULT 'whatsapp',
  `banner1_t1` varchar(255) DEFAULT 'THANK YOU FOR SUPPORTING LOCAL',
  `banner1_t2` varchar(255) DEFAULT 'Made with fresh ingredients & lots of love',
  `banner1_t3` varchar(255) DEFAULT 'AUTHENTIC MEDITERRANEAN FLAVOR',
  `banner2_t1` varchar(255) DEFAULT 'FRESH INGREDIENTS',
  `banner2_t2` varchar(255) DEFAULT 'MADE DAILY',
  `banner2_t3` varchar(255) DEFAULT 'AUTHENTIC RECIPES',
  `banner2_t4` varchar(255) DEFAULT 'MADE WITH LOVE',
  `about_title` varchar(255) DEFAULT 'Flavors Crafted With Heritage & Love',
  `about_subtitle` varchar(255) DEFAULT 'Our Legacy',
  `about_desc1` text DEFAULT NULL,
  `about_desc2` text DEFAULT NULL,
  `about_image` varchar(255) DEFAULT 'admin/bgs/about_story.png',
  `about_chef_image` varchar(255) DEFAULT 'admin/bgs/about_chef.png',
  `about_chef_title` varchar(255) DEFAULT 'The Passion Behind the Plate',
  `about_chef_subtitle` varchar(255) DEFAULT 'Handcrafted Culinary Artistry',
  `about_chef_name` varchar(255) DEFAULT 'Nabil',
  `about_chef_bio1` text DEFAULT NULL,
  `about_chef_bio2` text DEFAULT NULL,
  `about_years` varchar(50) DEFAULT '15+',
  `about_years_label` varchar(255) DEFAULT 'Years of Tradition',
  `about_bg` varchar(255) DEFAULT 'admin/bgs/hero-bg.jpg',
  `values_title` varchar(255) DEFAULT 'What We Stand For',
  `values_subtitle` varchar(255) DEFAULT 'Our Principles',
  `values_desc` text DEFAULT NULL,
  `value1_icon` varchar(100) DEFAULT 'fas fa-seedling',
  `value1_title` varchar(255) DEFAULT '100% Fresh Daily',
  `value1_desc` text DEFAULT NULL,
  `value2_icon` varchar(100) DEFAULT 'fas fa-scroll',
  `value2_title` varchar(255) DEFAULT 'Authentic Recipes',
  `value2_desc` text DEFAULT NULL,
  `value3_icon` varchar(100) DEFAULT 'fas fa-heart',
  `value3_title` varchar(255) DEFAULT 'Prepared With Love',
  `value3_desc` text DEFAULT NULL,
  `value4_icon` varchar(100) DEFAULT 'fas fa-hands-helping',
  `value4_title` varchar(255) DEFAULT 'Warm Hospitality',
  `value4_desc` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `restaurant_name`, `restaurant_logo`, `restaurant_email`, `restaurant_phone`, `restaurant_address`, `restaurant_maps`, `restaurant_description`, `opening_hours`, `opening_title`, `home_bg`, `menu_bg`, `contact_bg`, `whatsapp_number`, `instagram_url`, `facebook_url`, `chat_id`, `bot_token`, `country_code`, `order_method`, `banner1_t1`, `banner1_t2`, `banner1_t3`, `banner2_t1`, `banner2_t2`, `banner2_t3`, `banner2_t4`) VALUES
(1, 'Nabil Mediterranean Food', 'admin/bgs/logo_1779008183_logo.png', 'nabilskitchen@outlook.com', '(216) 376-9591', '4640 Richmond Road - 200 Warrensville, OH 44128', '', '', '9:00AM - 6:00PM', 'Monday - Saturday', 'admin/bgs/home_1779008244_WhatsApp Image 2026-05-16 at 5.21.26 PM.jpeg', 'admin/bgs/menu_1779008244_WhatsApp Image 2026-05-16 at 5.16.51 PMmm.jpeg', 'admin/bgs/contact_1779008244_WhatsApp Image 2026-05-16 at 5.16.51 PMm.jpeg', '(216) 376-9591', '', '', 0, '', '+1', 'sms', 'THANK YOU FOR SUPPORTING LOCAL BUSINESS', 'Made with fresh ingredients & lots of love', 'AUTHENTIC MEDITERRANEAN FLAVOR', 'FRESH INGREDIENTS', 'MADE DAILY', 'AUTHENTIC RECIPES', 'MADE WITH LOTS OF LOVE ❤️');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `userpassword` varchar(20) NOT NULL,
  `isAdmin` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `userpassword`, `isAdmin`) VALUES
(1, 'admin', 'admin2', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_entity` (`entity`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`),
  ADD KEY `idx_categories_order` (`Order`);

--
-- Indexes for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_items_category` (`item_category`(768)),
  ADD KEY `idx_items_name` (`item_name`(768));

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
