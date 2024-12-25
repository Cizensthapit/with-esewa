-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 25, 2024 at 01:13 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `medlifemis_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `approvedorder_db`
--

CREATE TABLE `approvedorder_db` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `dispatch_date` date NOT NULL,
  `contact` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approvedorder_db`
--

INSERT INTO `approvedorder_db` (`order_id`, `user_id`, `product_id`, `product_name`, `product_price`, `quantity`, `order_date`, `dispatch_date`, `contact`) VALUES
(1, 2, 1, 'acb', 2311.00, 23, '2024-11-26 11:18:55', '2024-12-17', '9808333328'),
(4, 4, 1, 'acb', 2311.00, 1, '2024-12-15 09:18:09', '2024-12-17', '9841204424'),
(5, 1, 1, 'acb', 2311.00, 10, '2024-12-24 07:12:51', '2024-12-26', '9841350901');

-- --------------------------------------------------------

--
-- Table structure for table `checkout`
--

CREATE TABLE `checkout` (
  `checkout_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `checkout_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `imported_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `complaint_text` text DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `completed_orders`
--

CREATE TABLE `completed_orders` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `dispatch_date` date NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `completed_orders`
--

INSERT INTO `completed_orders` (`id`, `order_id`, `user_id`, `product_id`, `product_name`, `product_price`, `quantity`, `order_date`, `dispatch_date`, `time`) VALUES
(1, 1, 2, 1, 'acb', 2311.00, 23, '2024-11-26', '2024-12-17', '2024-12-15 09:21:20'),
(2, 4, 4, 1, 'acb', 2311.00, 1, '2024-12-15', '2024-12-17', '2024-12-15 09:22:14'),
(3, 5, 1, 1, 'acb', 2311.00, 10, '2024-12-24', '2024-12-26', '2024-12-24 07:13:32');

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`order_id`, `user_id`, `product_id`, `product_name`, `product_price`, `quantity`, `contact`, `location`, `order_date`) VALUES
(39, 1, 11, 'Centrum MultiGummies Multi   Beauty', 4000.00, 1, '9841350901', 'Maitidevi', '2024-12-24 20:47:11'),
(40, 1, 16, 'Dabur Honitus Cough Syrup 100ml', 300.00, 1, '9841350901', 'Maitidevi', '2024-12-24 20:47:11'),
(41, 1, 6, 'MOISTURIZING LOTION', 4000.00, 1, '9841350901', 'Maitidevi', '2024-12-24 20:47:11'),
(42, 1, 13, 'Centrum MultiGummies Men Vitamins', 5000.00, 1, '9841350901', 'thames', '2024-12-25 02:19:23'),
(43, 1, 12, 'Centrum MultiGummies Multi   Omega-3 Fatty Acids', 6000.00, 1, '9841350901', 'thames', '2024-12-25 02:19:23'),
(45, 1, 11, 'Centrum MultiGummies Multi   Beauty', 4000.00, 1, '9841350901', 'thames', '2024-12-25 02:19:55'),
(46, 1, 12, 'Centrum MultiGummies Multi   Omega-3 Fatty Acids', 6000.00, 1, '9841350901', 'thames', '2024-12-25 02:19:55'),
(48, 1, 5, 'MULTI-PURPOSE OINTMENT', 1000.00, 1, '9841350901', 'thames', '2024-12-25 02:21:18'),
(49, 1, 7, 'SHEER MINERAL SUNSCREEN BROAD SPECTRUM SPF 30', 1299.00, 1, '9841350901', 'thames', '2024-12-25 02:23:12'),
(50, 1, 5, 'MULTI-PURPOSE OINTMENT', 1000.00, 1, '9841350901', 'thames', '2024-12-25 02:25:17'),
(51, 1, 17, 'Sensodyne Rapid Relief Extra Fresh Sensitive', 230.00, 1, '9841350901', 'thames', '2024-12-25 02:25:17'),
(53, 1, 11, 'Centrum MultiGummies Multi   Beauty', 4000.00, 1, '9841350901', 'abcd', '2024-12-25 03:03:48'),
(54, 1, 10, 'Centrum Women MultiGummies in Tropical Fruit Flavors', 3000.00, 1, '9841350901', 'abcd', '2024-12-25 03:03:48');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `pid` int(11) NOT NULL,
  `pname` varchar(255) NOT NULL,
  `pprice` decimal(10,2) NOT NULL,
  `pimage` varchar(255) NOT NULL,
  `ptype` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`pid`, `pname`, `pprice`, `pimage`, `ptype`) VALUES
(3, 'Daily Face Cleanser', 2300.00, 'uploads/Image 1.jpeg', 'Skin care'),
(4, 'MOISTURIZING RELIEF BODY WASH', 3400.00, 'uploads/Image 2.jpeg', 'Skin care'),
(5, 'MULTI-PURPOSE OINTMENT', 1000.00, 'uploads/Image 3.jpeg', 'Skin care'),
(6, 'MOISTURIZING LOTION', 4000.00, 'uploads/Image 4.jpeg', 'Skin care'),
(7, 'SHEER MINERAL SUNSCREEN BROAD SPECTRUM SPF 30', 1299.00, 'uploads/Image 5.jpeg', 'Skin care'),
(8, 'DERMACONTROL OIL REMOVING FOAM WASH', 4000.00, 'uploads/Image 6.jpeg', 'Skin care'),
(9, 'Centrum Kids MultiGummies in Tropical Punch Flavors', 2000.00, 'uploads/Image 7.jpeg', 'Health Supplement'),
(10, 'Centrum Women MultiGummies in Tropical Fruit Flavors', 3000.00, 'uploads/Image 8.jpeg', 'Health Supplement'),
(11, 'Centrum MultiGummies Multi + Beauty', 4000.00, 'uploads/Image 9.jpeg', 'Health Supplement'),
(12, 'Centrum MultiGummies Multi + Omega-3 Fatty Acids', 6000.00, 'uploads/Image 10.jpeg', 'Health Supplement'),
(13, 'Centrum MultiGummies Men Vitamins', 5000.00, 'uploads/Image 11.jpeg', 'Health Supplement'),
(15, 'DIGENE TAB', 21.00, 'uploads/Image 14.jpeg', 'OTC'),
(16, 'Dabur Honitus Cough Syrup 100ml', 300.00, 'uploads/Image 15.jpeg', 'OTC'),
(17, 'Sensodyne Rapid Relief Extra Fresh Sensitive', 230.00, 'uploads/Image 16.jpeg', 'OTC'),
(18, 'Vicks Vaporub - 50g', 320.00, 'uploads/Image 17.jpeg', 'OTC'),
(19, 'Crocin Pain Relief', 40.00, 'uploads/Image 18.jpeg', 'OTC'),
(20, 'Washproof Plaste', 20.00, 'uploads/Image 19.jpeg', 'OTC');

-- --------------------------------------------------------

--
-- Table structure for table `usercart`
--

CREATE TABLE `usercart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `pimage` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `contact`, `password`) VALUES
(1, 'ritu', 'ritu@gmail.com', '9841350901', '$2y$10$9FnRph0TQBfurmej7BCJUeUBo1WbE4cWXOq0slOaEEwSunQ4NUE8m'),
(2, 'rahul', 'rahul@gmail.com', '9808333328', '$2y$10$mq4C3jqs2DhDc0V.ETMz5.kdlp53CgysFuLWA3OiajoqUgHUANzG6'),
(3, 'Rushav', 'rusu.sthapit@gmail.com', '9861595991', '$2y$10$Khqwvb6ZsPkGPUhI1faS/Oq/kVVPdjOLtbH6lUfcr6KThIXakWOeS'),
(4, 'ran', 'ran@gmail.com', '9841204424', '$2y$10$o1kPbswr9cGvgLELCIjtluZH6AcGAVJZQsUqAkjWi0cPEHybhlnke');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `checkout`
--
ALTER TABLE `checkout`
  ADD PRIMARY KEY (`checkout_id`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `completed_orders`
--
ALTER TABLE `completed_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`pid`);

--
-- Indexes for table `usercart`
--
ALTER TABLE `usercart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `checkout`
--
ALTER TABLE `checkout`
  MODIFY `checkout_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `completed_orders`
--
ALTER TABLE `completed_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `usercart`
--
ALTER TABLE `usercart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
