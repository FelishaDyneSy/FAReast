-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 22, 2025 at 04:12 AM
-- Server version: 10.3.39-MariaDB-0ubuntu0.20.04.2
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `admin_Administrative`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounting`
--

CREATE TABLE `accounting` (
  `id` int(11) NOT NULL,
  `transaction_code` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','approved','denied') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounting`
--

INSERT INTO `accounting` (`id`, `transaction_code`, `amount`, `description`, `status`, `created_at`) VALUES
(2, 'TXN002', 3000.75, 'Service Fee', 'approved', '2025-02-26 17:27:02'),
(3, 'TXN0fdf2', 3000.75, 'Servdfdice Fee', 'pending', '2025-03-01 00:24:55');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `visitor_name` varchar(100) NOT NULL,
  `visitor_email` varchar(100) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `visitor_name`, `visitor_email`, `appointment_date`, `appointment_time`, `status`) VALUES
(1, 'ysfysdfyids', 'sanchezlando333@gmail.com', '2025-03-20', '20:18:00', 'Pending'),
(2, 'payns', 'pinesjohnlester15@gmail.com', '2025-03-08', '08:00:00', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `approval`
--

CREATE TABLE `approval` (
  `id` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Position` varchar(255) NOT NULL,
  `ApprovalDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `budget_summary`
--

CREATE TABLE `budget_summary` (
  `id` int(11) NOT NULL,
  `item` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budget_summary`
--

INSERT INTO `budget_summary` (`id`, `item`, `description`, `quantity`, `unit_price`, `total_amount`) VALUES
(2, 'Notebegrdgfgooks', 'A4 Size', 100, 3.50, 350.00),
(3, 'Notebegrdgfgooks', 'A4 Size', 10, 3.50, 35.00),
(4, 'Notebegrdgfgooks', 'A4 Size', 10, 3.50, 35.00);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `shipping_address` varchar(255) DEFAULT NULL,
  `billing_address` varchar(255) DEFAULT NULL,
  `registration_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `first_name`, `last_name`, `email`, `phone`, `shipping_address`, `billing_address`, `registration_date`) VALUES
(1, 'otintic', 'Doe', 'johndoe@example.com', NULL, NULL, NULL, '0000-00-00'),
(3, 'Alice', 'Johnson', 'alicejohnson@example.com', '1122334455', '789 Pine St, City, Country', '789 Pine St, City, Country', '2025-02-15'),
(4, 'Bob', 'Lee', 'boblee@example.com', '5566778899', '101 Oak St, City, Country', '101 Oak St, City, Country', '2025-01-25'),
(5, 'Charlie', 'Brown', 'charliebrown@example.com', '6677889900', '202 Birch St, City, Country', '202 Birch St, City, Country', '2025-02-18');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `created_at`) VALUES
(58, 'ADMIN', '2025-03-01 15:00:40'),
(59, 'FINANCE', '2025-03-01 15:11:00'),
(61, 'LOGISTIC', '2025-03-01 15:11:47'),
(62, 'HR', '2025-03-01 15:14:42'),
(69, 'CORE', '2025-03-02 08:28:46');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `department_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `title`, `department_id`) VALUES
(53, 'Shipping & Delivery', 61),
(54, 'Inventory & Stock', 61),
(55, 'Budget Summary', 59),
(57, 'Accounting & Reports', 59),
(61, 'Employee Records', 62);

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `reorder_level` int(11) NOT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `last_restocked` date DEFAULT NULL,
  `status` enum('In Stock','Low Stock','Out of Stock') NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`product_id`, `product_name`, `category`, `price`, `stock_quantity`, `reorder_level`, `sku`, `supplier`, `last_restocked`, `status`, `description`) VALUES
(3, 'otin product', 'Electronics', 100.00, 50, 10, 'SKU12335', 'Supplier Inc.', '2025-02-28', 'In Stock', 'A new product description.'),
(4, 'Wireless Headphones', 'Electronics', 150.00, 100, 20, 'SKU54321', 'AudioTech Co.', '2025-01-15', 'In Stock', 'High-quality wireless headphones with noise cancellation.'),
(5, 'Bluetooth Speaker', 'Electronics', 80.00, 75, 15, 'SKU67890', 'SoundWave Corp.', '2025-02-10', 'In Stock', 'Portable Bluetooth speaker with 10 hours of battery life.'),
(6, 'Smartwatch 4.0', 'Electronics', 200.00, 30, 5, 'SKU11223', 'TechWear Ltd.', '2025-02-25', 'In Stock', 'Smartwatch with fitness tracking and heart rate monitor.'),
(7, 'Gaming Mouse', 'Electronics', 50.00, 150, 30, 'SKU33445', 'GameTech Solutions', '2025-02-20', 'In Stock', 'Ergonomic gaming mouse with RGB lighting and customizable buttons.'),
(8, 'LED TV 55-inch', 'Electronics', 400.00, 25, 5, 'SKU77889', 'Home Electronics Inc.', '2025-01-30', 'Low Stock', '55-inch LED TV with 4K resolution and smart features.'),
(9, 'Digital Camera', 'Electronics', 500.00, 40, 10, 'SKU99001', 'CameraWorld', '2025-02-15', 'In Stock', 'Digital camera with 24MP and 4K video recording.');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_status` enum('Pending','Processing','Shipped','Completed','Cancelled') NOT NULL,
  `payment_status` enum('Paid','Unpaid','Refunded') NOT NULL,
  `shipping_address` varchar(255) NOT NULL,
  `billing_address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `order_date`, `total_amount`, `order_status`, `payment_status`, `shipping_address`, `billing_address`) VALUES
(1, 1, '2025-02-28', 200.00, 'Shipped', 'Paid', '456 Avenue, City, Country', '456 Avenue, City, Country'),
(3, 3, '2025-02-28', 200.00, 'Shipped', 'Paid', '456 Avenue, City, Country', '456 Avenue, City, Country'),
(4, 1, '2025-02-28', 200.00, 'Shipped', 'Paid', '456 Avenue, City, Country', '456 Avenue, City, Country');

-- --------------------------------------------------------

--
-- Table structure for table `otp_verification`
--

CREATE TABLE `otp_verification` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `otp_expiry` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `report_name` varchar(100) NOT NULL,
  `details` text NOT NULL,
  `accounting_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `report_name`, `details`, `accounting_id`, `created_at`) VALUES
(1, 'Updated Monthly Summary', 'Updated details for January report', 2, '2025-02-26 17:35:50'),
(2, 'Monthly Summary', 'Report on transactions for January', 2, '2025-02-26 17:36:59'),
(3, 'juls', 'juls report', 2, '2025-02-28 10:28:23');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `department_id`, `name`) VALUES
(39, 59, 'EMPLOYEE'),
(40, 62, 'ADMIN'),
(46, 58, 'EMPLOYEE'),
(47, 58, 'ADMIN'),
(48, 58, 'visitor'),
(49, 61, 'MANAGER'),
(50, 59, 'EMPLOYEE'),
(51, 58, 'employee');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_delivery`
--

CREATE TABLE `shipping_delivery` (
  `shipment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `shipping_address` varchar(255) NOT NULL,
  `shipping_method` enum('Standard','Express','Overnight') NOT NULL,
  `shipping_cost` decimal(10,2) NOT NULL,
  `shipping_date` date NOT NULL,
  `estimated_delivery_date` date NOT NULL,
  `delivery_status` enum('Pending','Shipped','Out for Delivery','Delivered','Failed') NOT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `delivery_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_delivery`
--

INSERT INTO `shipping_delivery` (`shipment_id`, `order_id`, `customer_id`, `shipping_address`, `shipping_method`, `shipping_cost`, `shipping_date`, `estimated_delivery_date`, `delivery_status`, `tracking_number`, `delivery_date`, `delivery_notes`) VALUES
(5, 1, 3, '123 Main St, City, Country', 'Express', 15.50, '2025-02-28', '2025-03-05', 'Pending', NULL, NULL, 'Please handle with care.'),
(6, 1, 3, '123 Main St, City, Country', 'Express', 15.50, '2025-02-28', '2025-03-05', 'Pending', NULL, NULL, 'Please handle with care.'),
(7, 1, 3, '123 Main St, City, Country', 'Express', 15.50, '2025-02-28', '2025-03-05', 'Pending', '45', '0000-00-00', 'Please handle with care.'),
(8, 1, 3, '123 Main St, City, Country', 'Express', 15.50, '2025-02-28', '2025-03-05', 'Pending', '45', '0000-00-00', 'Please handle with care.'),
(9, 1, 3, '123 Main St, City, Country', 'Express', 15.50, '2025-02-28', '2025-03-05', 'Pending', '45', '0000-00-00', 'Please handle with care.'),
(10, 1, 3, '123 Main St, City, Country', 'Express', 15.50, '2025-02-28', '2025-03-05', 'Pending', '45', '0000-00-00', 'Please handle with care.'),
(11, 1, 3, '123 Main St, City, Country', 'Express', 15.50, '2025-02-28', '2025-03-05', 'Pending', '45', '0000-00-00', 'Please handle with care.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `department_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `otp`, `otp_expiry`, `department_id`, `role_id`) VALUES
(66, 'pelisya', 'darylruter@gmail.com', 'papamo', '900559', '2025-03-02 08:24:20', 58, 47),
(67, 'lily', 'test@example.com', 'sasagurl', NULL, NULL, 61, 49),
(68, 'pines', 'pinesjohnlester15@gmail.com', 'papamo', NULL, NULL, 59, 39),
(69, 'lance', 'iji@gmail.com', 'password', NULL, NULL, 58, 46);

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`id`, `user_id`, `address`, `phone`, `date_of_birth`, `gender`, `profile_picture`, `nationality`, `occupation`, `bio`, `created_at`, `updated_at`) VALUES
(99, 66, 'caloocan', '09984561234', '2025-02-18', 'Female', ' https://admin.fareastcafeshop.com/api/uploads/1740893580_42.jpg', 'filipino', 'IT consultant', 'Ma ano ulam?', '2025-03-02 05:33:00', '2025-03-02 05:33:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounting`
--
ALTER TABLE `accounting`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_code` (`transaction_code`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `approval`
--
ALTER TABLE `approval`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `budget_summary`
--
ALTER TABLE `budget_summary`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `otp_verification`
--
ALTER TABLE `otp_verification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `accounting_id` (`accounting_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `shipping_delivery`
--
ALTER TABLE `shipping_delivery`
  ADD PRIMARY KEY (`shipment_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounting`
--
ALTER TABLE `accounting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `approval`
--
ALTER TABLE `approval`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `budget_summary`
--
ALTER TABLE `budget_summary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `otp_verification`
--
ALTER TABLE `otp_verification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `shipping_delivery`
--
ALTER TABLE `shipping_delivery`
  MODIFY `shipment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Constraints for table `otp_verification`
--
ALTER TABLE `otp_verification`
  ADD CONSTRAINT `otp_verification_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`accounting_id`) REFERENCES `accounting` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `roles_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shipping_delivery`
--
ALTER TABLE `shipping_delivery`
  ADD CONSTRAINT `shipping_delivery_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `shipping_delivery_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_details`
--
ALTER TABLE `user_details`
  ADD CONSTRAINT `user_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
