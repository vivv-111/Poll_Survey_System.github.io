-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： localhost
-- 產生時間： 2025 年 04 月 21 日 10:35
-- 伺服器版本： 10.4.28-MariaDB
-- PHP 版本： 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `restaurant_menu`
--

-- --------------------------------------------------------

--
-- 資料表結構 `admin_info`
--

CREATE TABLE `admin_info` (
  `admin_id` int(10) UNSIGNED NOT NULL,
  `admin_name` varchar(50) NOT NULL,
  `admin_email` varchar(100) NOT NULL,
  `admin_password` varchar(255) NOT NULL,
  `admin_phone` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `admin_info`
--

INSERT INTO `admin_info` (`admin_id`, `admin_name`, `admin_email`, `admin_password`, `admin_phone`, `created_at`, `updated_at`) VALUES
(1104, 'Admin', 'admin@qq.com', '$2y$10$eoTkdtILn1hdakctoPzt6.rAjdebj9s.TwGuxI1Ef/V057UfEcvVm', '123456', '2025-03-02 23:59:39', '2025-03-02 23:59:39');

-- --------------------------------------------------------

--
-- 資料表結構 `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `last_attempt` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `ip_address`, `attempts`, `last_attempt`, `created_at`) VALUES
(6, '::1', 1, '2025-04-21 16:31:57', '2025-04-21 08:31:57');

-- --------------------------------------------------------

--
-- 資料表結構 `menu_items`
--

CREATE TABLE `menu_items` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Type` varchar(255) NOT NULL,
  `Ingredients` text DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Price` decimal(10,2) NOT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `menu_items`
--

INSERT INTO `menu_items` (`ID`, `Name`, `Type`, `Ingredients`, `Description`, `Price`, `photo`) VALUES
(5, 'Shoyu Chāomian', 'fried_noodles', 'Noodles, Soy Sauce, Vegetables, Pork', 'Classic Chinese soy sauce noodles with savory pork and veggies', 18.00, 'img_file/Shoyu Chāomian.jpeg'),
(6, 'Korean Zha Jiang Mein', 'fried_noodles', 'Noodles, Fermented Bean Paste, Cabbage, Pickled Radish', 'Rich Korean-style bean paste noodles with tangy cabbage', 28.00, 'img_file/Korean Zha Jiang Mein.jpg'),
(7, 'Pad Thai', 'fried_noodles', 'Rice Noodles, Shrimp, Fish Sauce, Lime Juice', 'Sour-sweet Thai street food with shrimp and lime', 20.00, 'img_file/Pad Thai.jpg'),
(8, 'Vegetable Fried Rice', 'fried_rice', 'Rice, Carrots, Peas, Corn', 'Colorful veggie fried rice with aromatic spices', 15.00, 'img_file/Vegetable Fried Rice.jpg'),
(9, 'Chicken Fried Rice', 'fried_rice', 'Rice, Chicken Breast, Scrambled Eggs, Soy Sauce', 'Buttery chicken fried rice with golden eggs', 18.00, 'img_file/Chicken Fried Rice.jpeg'),
(10, 'Pad Thai Fried Rice', 'fried_rice', 'Rice Noodles, Shrimp, Coconut Milk, Tamarind', 'Thai-inspired coconut rice with shrimp and tangy sauce', 22.00, 'img_file/Pad Thai Fried Rice.jpg'),
(11, 'Tonkotsu Ramen', 'soup_noodles', 'Ramen, Pork Bone Broth, Char Siu, Egg', 'Rich Japanese pork bone broth ramen with melt-in-your-mouth pork', 25.00, 'img_file/Tonkotsu Ramen.jpeg'),
(12, 'Kimchi Jjajang Myeon', 'soup_noodles', 'Noodles, Kimchi, Tofu, Beef', 'Spicy Korean kimchi stew noodles with chewy tofu', 22.00, 'img_file/Kimchi Jjajang Myeon.jpg'),
(13, 'Beef Shoyu Ramen', 'soup_noodles', 'Ramen, Beef Shank, Soy Sauce, Green Onion', 'Clear beef broth ramen with tender meat slices', 20.00, 'img_file/Beef Shoyu Ramen.jpg'),
(14, 'Indian Butter Chicken Curry', 'curry', 'Chicken, Coconut Milk, Spices', 'Creamy butter chicken curry with aromatic Indian spices', 28.00, 'img_file/Indian Butter Chicken Curry.jpg'),
(15, 'Thai Green Curry', 'curry', 'Chicken, Green Paste, Coconut Milk, Eggplant', 'Zesty green curry with creamy coconut texture', 25.00, 'img_file/Thai Green Curry.jpg'),
(16, 'Mushroom Stew Curry', 'curry', 'Mushrooms, Cream, Puff Pastry', 'Velvety mushroom and cream curry with buttery pastry', 22.00, 'img_file/Mushroom Stew Curry.jpeg'),
(17, 'Butter Jam Toast', 'toast', 'Sliced Bread, Butter, Strawberry Jam', 'Classic English breakfast with rich butter and jam', 12.00, 'img_file/Butter Jam Toast.jpg'),
(18, 'Avocado Toast', 'toast', 'Sliced Bread, Avocado, Poached Egg', 'Modern brunch toast with creamy avocado and egg', 15.00, 'img_file/Avocado Toast.jpg'),
(19, 'Croissant with Chocolate', 'toast', 'Croissant, Dark Chocolate, Almond Slivers', 'Luxurious chocolate croissant with crunchy almonds', 20.00, 'img_file/Croissant with Chocolate.jpg'),
(20, 'Classic Beef Burger', 'hamburger', 'Beef Patty, Hamburger Bun, Lettuce, Tomato', 'Premium beef burger with fresh toppings', 30.00, 'img_file/Classic Beef Burger.jpeg'),
(21, 'Chicken Cheese Burger', 'hamburger', 'Grilled Chicken, Mozzarella, Pickles', 'Low-fat cheeseburger with crispy lettuce and tomato', 28.00, 'img_file/Chicken Cheese Burger.jpeg'),
(22, 'Vegan Burger', 'hamburger', 'Tempeh Patty, Avocado, Sprouts', 'Plant-based patty with creamy avocado and veggies', 25.00, 'img_file/Vegan Burger.jpeg'),
(23, 'Ham & Egg Sandwich', 'sandwich', 'Toast, Ham, Fried Egg, Lettuce', 'Classic breakfast sandwich with perfectly cooked egg', 15.00, 'img_file/Ham & Egg Sandwich.jpeg'),
(24, 'Smoked Salmon Sandwich', 'sandwich', 'Toast, Salmon, Cream Cheese, Herbs', 'Elegant smoked salmon tea sandwich', 28.00, 'img_file/Smoked Salmon Sandwich.jpeg'),
(25, 'Veggie Club Sandwich', 'sandwich', 'Toast, Lettuce, Tomato, Cucumber, Cheese', 'Fresh Mediterranean-style veggie sandwich', 18.00, 'img_file/Veggie Club Sandwich.jpg'),
(26, 'Chocolate Cake', 'desserts', 'Chocolate, Flour, Sugar', 'Moist rich chocolate cake for any occasion', 20.00, 'img_file/Chocolate Cake.jpg'),
(27, 'Strawberry Ice Cream', 'desserts', 'Strawberries, Vanilla Ice Cream', 'Cool summertime dessert with fresh berries', 25.00, 'img_file/Strawberry Ice Cream.jpg'),
(28, 'Mango Pudding', 'desserts', 'Mango, Coconut Milk, Tapioca', 'Sweet tropical mango pudding with silky texture', 18.00, 'img_file/Mango Pudding.jpg'),
(40, '123', 'hamberger', '666', '85', 5.00, 'img_file/67fe7289b7b88.png');

-- --------------------------------------------------------

--
-- 資料表結構 `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `shopping_cart`
--

CREATE TABLE `shopping_cart` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `user_info`
--

CREATE TABLE `user_info` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `user_info`
--

INSERT INTO `user_info` (`user_id`, `user_name`, `email`, `password`, `phone`, `created_at`, `updated_at`, `photo`) VALUES
(123, 'Eric', 'zhengx@gmail.com', '$2y$10$rCXdLkHin7OWkQ9EVHDgteDRvIBe0vXXHTucMTtcJdXekrP1YMwRm', '12345678', '2025-04-21 16:13:54', '2025-04-21 16:13:54', 'img_file/Screenshot 2025-04-21 at 4.09.12 PM.png');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `admin_info`
--
ALTER TABLE `admin_info`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `admin_email` (`admin_email`),
  ADD UNIQUE KEY `admin_phone` (`admin_phone`);

--
-- 資料表索引 `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`ID`);

--
-- 資料表索引 `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_user_order` (`user_id`);

--
-- 資料表索引 `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `fk_order_menu` (`menu_item_id`),
  ADD KEY `fk_order_user` (`user_id`);

--
-- 資料表索引 `shopping_cart`
--
ALTER TABLE `shopping_cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_shopping` (`user_id`);

--
-- 資料表索引 `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `admin_info`
--
ALTER TABLE `admin_info`
  MODIFY `admin_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1238;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `shopping_cart`
--
ALTER TABLE `shopping_cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user_info`
--
ALTER TABLE `user_info`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41104;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_user_order` FOREIGN KEY (`user_id`) REFERENCES `user_info` (`user_id`);

--
-- 資料表的限制式 `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `fk_order_menu` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `user_info` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`ID`);

--
-- 資料表的限制式 `shopping_cart`
--
ALTER TABLE `shopping_cart`
  ADD CONSTRAINT `fk_user_shopping` FOREIGN KEY (`user_id`) REFERENCES `user_info` (`user_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
