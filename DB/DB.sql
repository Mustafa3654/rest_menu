-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 07, 2026 at 12:11 PM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u907225650_ashtouta`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `cat_id` int(11) NOT NULL,
  `cat_name` text NOT NULL,
  `cat_picture` text NOT NULL,
  `cat_icon` text DEFAULT NULL,
  `Order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_id`, `cat_name`, `cat_picture`, `cat_icon`, `Order`) VALUES
(2, 'Ashtouta', 'items/CAT-6984c018ca4a49.04268052.jpeg', '', 0),
(3, 'Crepe', '', '', 0),
(4, 'Waffle', '', '', 0),
(5, 'Mini pancakes', '', '', 0),
(12, 'bubble waffle', '', '', 0),
(13, 'Cookies', '', '', 0),
(14, 'Milkshake', '', '', 0),
(15, 'Hot Drinks', '', '', 0),
(16, 'Cold drinks', '', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `contact_submissions`
--

CREATE TABLE `contact_submissions` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `item_name` text NOT NULL,
  `item_category` text NOT NULL,
  `item_pricelbp` int(11) NOT NULL,
  `Ingredients` text NOT NULL,
  `item_pic` text NOT NULL,
  `Order` int(11) NOT NULL DEFAULT 0,
  `item_priceusd` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `item_name`, `item_category`, `item_pricelbp`, `Ingredients`, `item_pic`, `Order`, `item_priceusd`) VALUES
(3, 'Ashtouta Pistachio', 'Ashtouta', 0, 'Ashtouta . Pistachio spread . Pistachio . Rice milk pudding', '', 0, 5),
(4, 'Ashtouta Lutos', 'Ashtouta', 0, 'Ashtouta . Lutos biscuits . Lutos spread . Rice milk pudding', '', 0, 5),
(5, 'Ashtouta Nutella', 'Ashtouta', 0, 'Ashtouta . Nutella . White chocolate .  Strawberry . Rice milk pudding', '', 0, 5),
(6, 'Ashtouta Oreo', 'Ashtouta', 0, 'Ashtouta . Oreo biscuits . Nutella . Oreo spread . White chocolate. Rice milk pudding', '', 0, 5),
(7, 'Ashtouta Mango', 'Ashtouta', 0, 'Ashtouta . Mango . Nutella . Rice milk pudding', '', 0, 7),
(8, 'Ashtouta Fruits', 'Ashtouta', 0, 'Ashtouta . Banana . Strawberry. Mango . Nutella . White chocolate. Rice milk pudding', '', 0, 6),
(9, 'Ashtouta Chocolate Dubai', 'Ashtouta', 0, 'Ashtouta . Kunafa dubai . Nutella . Pistachio. White chocolate. Rice milk pudding', '', 0, 6),
(10, 'Ashtouta Mix 2', 'Ashtouta', 0, '', '', 0, 6),
(11, 'Ashtouta Mix 3', 'Ashtouta', 0, 'Oreo . Lutos . Pistachio', '', 0, 6),
(12, 'Ashtouta Kinder', 'Ashtouta', 0, 'Ashtouta . Kinder . Nutella . White chocolate . Kinder spread. Rice milk pudding', '', 0, 5),
(13, 'Crepe nutella', 'Crepe', 0, 'Nutella . White chocolate. Strawberry', '', 0, 5),
(14, 'Crepe  Lutos', 'Crepe', 0, 'Lutos spread . Lutos biscuits . Nutella', '', 0, 5),
(15, 'Crepe kinder', 'Crepe', 0, 'Kinder . Nutella . White chocolate . Kinder fingers', '', 0, 5),
(16, 'Crepe Chocolate Dubai', 'Crepe', 0, 'Chocolate Dubai . Nutella . White chocolate. Pistachio', '', 0, 6),
(17, 'Crepe fruits', 'Crepe', 0, 'Mango . Banana . Strawberry . Nutella. White chocolate', '', 0, 6),
(18, 'Crepe mix fruits', 'Crepe', 0, 'Banana . Mango . Strawberry . Pineapple . Kiwi . Nutella . White chocolate', '', 0, 7),
(19, 'Crepe Oreo', 'Crepe', 0, 'Oreo spread. Oreo biscuits . Nutella . White chocolate', '', 0, 5),
(20, 'Crepe Brownies', 'Crepe', 0, 'Brownies . Nutella . White chocolate. Strawberry', '', 0, 6),
(21, 'Crepe pistachio white chocolate', 'Crepe', 0, 'Pistachio spread . White chocolate . Pistachio', '', 0, 5),
(22, 'Waffle Nutella', 'Waffle', 0, 'Nutella . White chocolate', '', 0, 4),
(23, 'Waffle fruity', 'Waffle', 0, 'Strawberry . Mango . Banana . Nutella . White chocolate', '', 0, 6),
(24, 'Waffle fruity mix', 'Waffle', 0, 'Strawberry. Mango . Banana . Kiwi . Pine apple . Nutella . White chocolate', '', 0, 7),
(25, 'Waffle strawberry white chocolate', 'Waffle', 0, 'Strawberry sauce . Strawberry fruit. White chocolate', '', 0, 5),
(26, 'Waffle Lutos', 'Waffle', 0, 'Lotus spread . Lotus biscuits', '', 0, 5),
(27, 'Waffle Oreo', 'Waffle', 0, 'Oreo biscuits. Oreo spread. Nutella . White chocolate', '', 0, 5),
(28, 'Waffle Kinder', 'Waffle', 0, 'Kinder fingers . Nutella . White chocolate. Kinder spread', '', 0, 5),
(29, 'Waffle chocolate Dubai', 'Waffle', 0, 'Kunafa . Chocolate Dubai', '', 0, 6),
(30, 'Mini pancakes 12 pcs', 'Mini pancakes', 0, 'Nutella . White chocolate', '', 0, 4),
(31, 'Mini pancakes lotus 12 pcs', 'Mini pancakes', 0, 'Lotus biscuits . Lotus spread', '', 0, 4),
(32, 'Mini pancakes Oreo 12 pcs', 'Mini pancakes', 0, 'Oreo biscuits . Oreo spread . Nutella . White chocolate', '', 0, 4),
(33, 'Mini pancakes Pistachio 12 pcs', 'Mini pancakes', 0, 'Pistachio . Pistachio spread . White chocolate', '', 0, 5),
(34, 'Mini pancakes fruity 12 pcs', 'Mini pancakes', 0, 'Strawberry . Mango . Banana . Nutella . White chocolate', '', 0, 6),
(35, 'Mini pancakes fruity mix 12 pcs', 'Mini pancakes', 0, 'Strawberry. Banana . Mango . Pineapple . Kiwi . Nutella. White chocolate', '', 0, 7),
(36, 'Mini pancakes nutella 24 pcs', 'Mini pancakes', 0, 'Nutella . White chocolate', '', 0, 7),
(37, 'Mini pancakes lotus 24 pcs', 'Mini pancakes', 0, 'Lutos biscuits. Lutos spread', '', 0, 7),
(38, 'Mini pancakes Oreo 24 pcs', 'Mini pancakes', 0, 'Oreo biscuits . Oreo spread. Nutella . White chocolate', '', 0, 7),
(39, 'Mini pancakes fruity 24 pcs', 'Mini pancakes', 0, 'Strawberry. Banana . Mango . Nutella . White chocolate', '', 0, 10),
(40, 'Mini pancakes mix fruity 24 pcs', 'Mini pancakes', 0, 'Strawberry . Mango . Banana . Pineapple. Kiwi . Nutella . White chocolate', '', 0, 12),
(41, 'bubble waffle Nutella 12 pcs', 'bubble waffle', 0, 'Nutella . White chocolate', '', 0, 4),
(42, 'Bubble waffle lutos 12 pcs', 'bubble waffle', 0, 'Lutos spread . Lutos biscuits', '', 0, 4),
(43, 'Bubble waffle Oreo 12 pcs', 'bubble waffle', 0, 'Oreo biscuits . Nutella . Oreo spread', '', 0, 4),
(44, 'Bubble waffle mix 12 pcs', 'bubble waffle', 0, 'Pistachio . Lotus . Oreo', 'items/IMG-6985e7f8066b24.09141417.jpeg', 0, 5.5),
(45, 'Bubble waffle fruits 12 pcs', 'bubble waffle', 0, 'Mango . Strawberry. Banana . Nutella . White chocolate', '', 0, 6),
(46, 'Bubble waffle P.W', 'bubble waffle', 0, 'Pistachio. White chocolate', '', 0, 5);

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
  `exchange_rate` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `restaurant_name`, `restaurant_logo`, `restaurant_email`, `restaurant_phone`, `restaurant_address`, `restaurant_maps`, `restaurant_description`, `opening_hours`, `opening_title`, `home_bg`, `menu_bg`, `contact_bg`, `whatsapp_number`, `instagram_url`, `facebook_url`, `chat_id`, `bot_token`, `exchange_rate`) VALUES
(1, 'Ashtouta & More', 'bgs/logo_1770205351_logo.png', '', '81 559 332', 'Al-Marj', '', '', '2:00PM - 1:00AM', 'Open Daily', '', '', '', '81 559 332', 'https://www.instagram.com/ashtouta.a', '', 0, '', 0);

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
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

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
