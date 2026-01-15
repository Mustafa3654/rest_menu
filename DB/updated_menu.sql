-- Updated SQL Schema for Restaurant Menu Redesign

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_name` text NOT NULL,
  `cat_picture` text NOT NULL,
  `cat_icon` varchar(255) DEFAULT NULL,
  `Order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE IF NOT EXISTS `items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` text NOT NULL,
  `item_category` text NOT NULL,
  `item_pricelbp` int(11) NOT NULL,
  `Ingredients` text NOT NULL,
  `item_pic` text NOT NULL,
  `Order` int(11) NOT NULL DEFAULT 0,
  `item_priceusd` double NOT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `userpassword` varchar(20) NOT NULL,
  `isAdmin` int(11) DEFAULT 0,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `restaurant_name` varchar(255) NOT NULL,
  `restaurant_logo` varchar(255) DEFAULT NULL,
  `restaurant_email` varchar(255) DEFAULT NULL,
  `restaurant_phone` varchar(50) DEFAULT NULL,
  `restaurant_address` text DEFAULT NULL,
  `restaurant_description` text DEFAULT NULL,
  `opening_hours` varchar(255) DEFAULT NULL,
  `whatsapp_number` varchar(50) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`restaurant_name`, `restaurant_logo`, `restaurant_email`, `restaurant_phone`, `restaurant_address`, `restaurant_description`, `opening_hours`, `whatsapp_number`, `instagram_url`, `facebook_url`) VALUES
('Ethel Catering', 'bgs/logoo.jfif', 'info@ethelcatering.com', '03 495 894', 'Lebanon', 'Experience the rich flavors of traditional Lebanese street food, crafted with love and tradition.', '12:00 PM - 10:00 PM', '+961 81 160 368', 'https://www.instagram.com/ethel_catering?igsh=MWRpeG4zazY0dWJkdg==', 'https://www.facebook.com/ethelcatering?mibextid=JRoKGi');

-- --------------------------------------------------------

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `userpassword`, `isAdmin`) VALUES
(1, 'user1', '123456', 1);

-- --------------------------------------------------------

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_id`, `cat_name`, `cat_picture`, `Order`) VALUES
(37, 'HOT DRINKS', 'items/6841b6994d20c_IMG_1937.jpeg', 0),
(38, 'SAJ', 'items/6841b6dd5109e_94ABFA68-7651-4080-A12F-36A83EBA446A.png', 0),
(39, 'FRESH DRINKS', 'items/6841b745bcef1_IMG_1939.jpeg', 0),
(40, 'ENERGY DRINKS', 'items/6841b7809eaa2_IMG_1940.jpeg', 0),
(41, 'SHISHA', 'items/6841b7e105987_IMG_1941.jpeg', 0),
(42, 'COLD COFFE', 'items/6841b81a1c054_IMG_1943.webp', 0),
(43, 'DESSERT', 'items/6841bb2a9fe8c_IMG_1944.webp', 0),
(44, 'ARABAY', 'items/6841bbb33a900_IMG_1945.jpeg', 0),
(45, 'NUTS', 'items/6841bc018fb20_IMG_1946.webp', 0),
(46, 'Soft Drinks', 'items/6841bc38c5405_IMG_1947.webp', 0),
(47, 'MILK SHAKE', 'items/6841bca8294e9_IMG_1949.webp', 0),
(50, 'ICECREAM', 'items/6841be1043bb9_IMG_1950.jpeg', 0),
(51, 'CAKES', 'items/6841be48ddecf_IMG_1951.jpeg', 0),
(52, 'Smoke', 'items/6841be8751582_IMG_1952.png', 0),
(53, 'CHIPS', 'items/6841bec074190_IMG_1953.jpeg', 0),
(54, 'FRISCO', 'items/6841befab2af7_IMG_1954.jpeg', 0),
(55, 'KAAK', 'items/6841bf2feb0c1_IMG_1955.jpeg', 0);

-- --------------------------------------------------------

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `item_name`, `item_category`, `item_pricelbp`, `Ingredients`, `item_pic`, `Order`, `item_priceusd`) VALUES
(322, 'Chocolate', 'SAJ', 150000, '0', 'items/6841980519d4a_IMG_1925.jpeg', 1, 0),
(323, 'Labneh', 'SAJ', 100000, '0', '', 0, 0),
(324, 'Cocktail', 'SAJ', 150000, '0', '', 0, 0),
(325, 'Mortadella & Cheese', 'SAJ', 200000, '0', '', 0, 0),
(326, 'Four Cheese', 'SAJ', 250000, '0', '', 0, 0),
(327, 'Kafta & Cheese', 'SAJ', 300000, '0', 'items/6841e6117ff3c_D9AB56FD-B47F-4D3E-942F-FE78B794C9CF.png', 0, 0),
(328, 'Sojouk & Cheese', 'SAJ', 300000, '0', 'items/6841e6228d783_27CF9FE0-35B5-4A3A-BEF4-8737ADF96810.png', 0, 0),
(329, 'Turkish Cheese', 'SAJ', 200000, '0', '', 0, 0),
(330, 'Kishk', 'SAJ', 120000, '0', '', 0, 0),
(331, 'Kashkawan', 'SAJ', 200000, '0', 'items/68419c35da0a1_55ED0EB9-9072-4200-A075-FA0F8C0B9FE7.png', 0, 0),
(332, 'Halloum', 'SAJ', 175000, '0', '', 0, 0),
(334, 'Zaatar', 'SAJ', 100000, '0', 'items/IMG-684199052a0a01.35325359.png', 0, 0),
(335, 'Tea', 'HOT DRINKS', 100000, '0', 'items/IMG-6842eee4361825.09702240.webp', 0, 0),
(336, 'Green Tea', 'HOT DRINKS', 100000, '0', 'items/IMG-6842ef1fd28f67.85871431.webp', 0, 0),
(337, 'Ginger Tea', 'HOT DRINKS', 100000, '0', 'items/IMG-6842efaab93b13.87693235.jpeg', 0, 0),
(338, 'Zhuraat', 'HOT DRINKS', 100000, '0', 'items/IMG-6842f009319415.17255802.jpeg', 0, 0),
(339, 'Espresso', 'HOT DRINKS', 100000, '0', 'items/IMG-6842f038c59c47.73116220.webp', 0, 0),
(341, 'Nestle Nescaffe', 'HOT DRINKS', 150000, '0', 'items/IMG-6842f08617fc54.31820992.webp', 0, 0),
(342, 'Nescafe 3 in 1', 'HOT DRINKS', 80000, '0', 'items/IMG-6842f104a7c573.04967031.jpeg', 0, 0),
(343, 'Hot Chocolate', 'HOT DRINKS', 175000, '0', 'items/IMG-6842f147ad4ea3.18463255.jpeg', 0, 0),
(344, 'Cafe Latte', 'HOT DRINKS', 175000, '0', 'items/IMG-6842f17c370533.35444248.webp', 0, 0),
(345, 'Americano', 'HOT DRINKS', 150000, '0', 'items/IMG-6842f1be4df279.36409402.webp', 0, 0),
(346, 'Turkish Coffee', 'HOT DRINKS', 150000, '0', 'items/IMG-6842f1f2bc5b88.51581602.webp', 0, 0),
(347, 'Nestle Espresso', 'HOT DRINKS', 120000, '0', 'items/IMG-6842f24c3aa441.20195339.jpeg', 0, 0),
(348, 'Capuccino', 'HOT DRINKS', 175000, '0', 'items/IMG-6842f2ae52ccb8.90502562.webp', 0, 0),
(350, 'Orange', 'FRESH DRINKS', 150000, '0', 'items/IMG-6842f3b595da16.05426299.jpeg', 0, 0),
(351, 'Lemonade', 'FRESH DRINKS', 150000, '0', 'items/IMG-6842f430f1beb3.90929834.jpeg', 0, 0),
(352, 'Minted Lemonade', 'FRESH DRINKS', 200000, '0', 'items/IMG-6842f4686ae822.91121770.webp', 0, 0),
(353, 'Strawberry', 'FRESH DRINKS', 200000, '0', 'items/IMG-6842f4d37ed570.24215411.jpeg', 0, 0),
(354, 'Mango', 'FRESH DRINKS', 200000, '0', 'items/IMG-6842f515de3e74.28230389.jpeg', 0, 0),
(355, 'Cocktail', 'FRESH DRINKS', 250000, '0', 'items/IMG-6842f55cef56c6.22203660.jpeg', 0, 0),
(356, 'Cocktail Shu2af', 'FRESH DRINKS', 350000, '0', 'items/IMG-6842f5adceec70.18420150.jpeg', 0, 0),
(357, 'Mix.Juice', 'FRESH DRINKS', 200000, '0', '', 0, 0),
(358, 'BomBom', 'ENERGY DRINKS', 150000, '0', 'items/IMG-6842f65d421340.62784704.jpeg', 0, 0),
(359, 'RedBull', 'ENERGY DRINKS', 200000, '0', 'items/IMG-6842f68fce8cc5.92899492.jpeg', 0, 0),
(360, 'DarkBlue', 'ENERGY DRINKS', 100000, '0', 'items/IMG-6842f7a7f126e0.72046378.jpeg', 0, 0),
(361, 'Apple Wezara', 'SHISHA', 350000, '0', '', 0, 0),
(362, 'Lemon & Mint', 'SHISHA', 300000, '0', '', 0, 0),
(363, '3elke & Mint', 'SHISHA', 350000, '0', '', 0, 0),
(364, 'Special', 'SHISHA', 450000, '0', '', 0, 0),
(365, 'Grape & Mint', 'SHISHA', 300000, '0', '', 0, 0),
(366, 'Ice Coffe', 'COLD COFFE', 200000, '0', 'items/IMG-6842f8bbd53174.52287432.webp', 0, 0),
(367, 'Nescafe Frappe', 'COLD COFFE', 250000, '0', 'items/IMG-6842f925750063.45089477.webp', 0, 0),
(368, 'Mocka Frappe', 'COLD COFFE', 250000, '0', 'items/IMG-6842f973549278.86482725.webp', 0, 0),
(369, 'Jalo', 'DESSERT', 100000, '0', 'items/IMG-6842fa11d75f66.18144611.jpeg', 0, 0),
(370, 'Kastar', 'DESSERT', 100000, '0', 'items/IMG-6842fa2d987092.51169689.jpeg', 0, 0),
(371, 'Rez b7leeb', 'DESSERT', 150000, '0', 'items/IMG-6842fa47179538.81330441.jpeg', 0, 0),
(372, 'Small Foul', 'ARABAY', 150000, '0', '', 0, 0),
(373, 'Medium Foul', 'ARABAY', 200000, '0', '', 0, 0),
(374, 'Big Foul', 'ARABAY', 250000, '0', '', 0, 0),
(375, 'Small Tourmos', 'ARABAY', 100000, '0', '', 0, 0),
(376, 'Medium Tourmos', 'ARABAY', 150000, '0', '', 0, 0),
(377, 'Big Tourmos', 'ARABAY', 200000, '0', '', 0, 0),
(378, 'Corn', 'ARABAY', 100000, '0', '', 0, 0),
(379, 'Corn With Hamed', 'ARABAY', 150000, '0', '', 0, 0),
(380, 'Corn Mix', 'ARABAY', 250000, '0', '', 0, 0),
(381, 'Carrot', 'ARABAY', 100000, '0', '', 0, 0),
(382, 'Mix Plate Medium', 'ARABAY', 350000, '0', '', 0, 0),
(383, 'Mix Plate Big', 'ARABAY', 450000, '0', '', 0, 0),
(384, 'Extra plate', 'NUTS', 150000, '0', '', 0, 0),
(385, 'Mini Plate', 'NUTS', 100000, '0', '', 0, 0),
(386, 'Water', 'Soft Drinks', 50000, '0', '', 0, 0),
(387, 'Pepsi , Diet', 'Soft Drinks', 100000, '0', '', 0, 0),
(388, '7 UP , Diet', 'Soft Drinks', 100000, '0', '', 0, 0),
(389, 'Mirinda', 'Soft Drinks', 100000, '0', '', 0, 0),
(390, 'Extra Juice', 'Soft Drinks', 75000, '0', '', 0, 0),
(391, 'MR.Juice', 'Soft Drinks', 35000, '0', '', 0, 0),
(392, 'Reem Water', 'Soft Drinks', 100000, '0', '', 0, 0),
(393, 'Laziza', 'Soft Drinks', 150000, '0', '', 0, 0),
(394, 'Ice Tea', 'Soft Drinks', 150000, '0', '', 0, 0),
(395, 'Strawberry', 'MILK SHAKE', 200000, '0', '', 0, 0),
(396, 'Chocolate', 'MILK SHAKE', 200000, '0', '', 0, 0),
(397, 'Oreo', 'MILK SHAKE', 200000, '0', '', 0, 0),
(398, 'Vanilla', 'MILK SHAKE', 200000, '0', '', 0, 0),
(399, 'Kinder', 'MILK SHAKE', 200000, '0', '', 0, 0),
(400, 'Big Cup', 'ICECREAM', 100000, '0', '', 0, 0),
(401, 'Korneh', 'ICECREAM', 150000, '0', '', 0, 0),
(402, 'Rimas Brownie', 'CAKES', 40000, '0', '', 0, 0),
(403, 'Rimas Taweel', 'CAKES', 35000, '0', '', 0, 0),
(404, 'Rimas 3ade', 'CAKES', 25000, '0', '', 0, 0),
(405, 'Rimas Me7she', 'CAKES', 35000, '0', '', 0, 0),
(406, 'Mars', 'CAKES', 60000, '0', '', 0, 0),
(407, 'Kitkat', 'CAKES', 80000, '0', '', 0, 0),
(408, 'Snickers', 'CAKES', 60000, '0', '', 0, 0),
(409, 'Albeni', 'CAKES', 40000, '0', '', 0, 0),
(410, 'Biskream Big', 'CAKES', 70000, '0', '', 0, 0),
(411, 'Hali', 'CAKES', 60000, '0', '', 0, 0),
(412, 'Sandwich', 'CAKES', 35000, '0', '', 0, 0),
(413, 'Biskato', 'CAKES', 25000, '0', '', 0, 0),
(414, 'Unika', 'CAKES', 20000, '0', '', 0, 0),
(415, 'Babli', 'CAKES', 50000, '0', '', 0, 0),
(416, 'Break', 'CAKES', 20000, '0', '', 0, 0),
(417, 'Metro', 'CAKES', 40000, '0', '', 0, 0),
(418, 'Oreo', 'CAKES', 50000, '0', '', 0, 0),
(419, 'Milka', 'CAKES', 50000, '0', '', 0, 0),
(420, 'Twix', 'CAKES', 70000, '0', '', 0, 0),
(421, 'All Seders', 'Smoke', 80000, '0', '', 0, 0),
(422, 'Winston Blue', 'Smoke', 175000, '0', '', 0, 0),
(423, 'Winston Red', 'Smoke', 205000, '0', '', 0, 0),
(424, 'All Kint', 'Smoke', 155000, '0', '', 0, 0),
(425, 'Gauliose Red', 'Smoke', 75000, '0', '', 0, 0),
(426, 'Pringles', 'CHIPS', 90000, '0', '', 0, 0),
(427, 'Big', 'FRISCO', 100000, '0', '', 0, 0),
(428, 'Big With IceCream', 'FRISCO', 150000, '0', '', 0, 0),
(429, 'Akawi', 'KAAK', 150000, '0', '', 0, 0),
(430, 'Halloum', 'KAAK', 175000, '0', '', 0, 0),
(431, 'Kashkwan', 'KAAK', 200000, '0', '', 0, 0),
(432, 'Mortadella Cheese', 'KAAK', 200000, '0', '', 0, 0),
190	(433, 'Turkish Cheese', 'KAAK', 300000, '0', '', 0, 0),
191	(434, 'Chocolate', 'KAAK', 150000, '0', '', 0, 0),
192	(435, 'Add (Banana And Strawberry )', 'KAAK', 50000, '0', '', 0, 0),
193	(436, 'Add (Banana and Strawberry)', 'SAJ', 50000, '0', '', 0, 0),
194	(437, 'Master Salt', 'CHIPS', 60000, '0', '', 0, 0),
195	(438, 'Master BBQ', 'CHIPS', 60000, '0', '', 0, 0),
196	(439, 'Master Vinegar', 'CHIPS', 60000, '0', '', 0, 0);

COMMIT;
