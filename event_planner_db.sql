-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 25, 2026 at 08:44 PM
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
-- Database: `event_planner_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_name` varchar(200) NOT NULL,
  `event_date` date NOT NULL,
  `no_of_guests` int(11) DEFAULT 0,
  `budget` decimal(10,2) DEFAULT 0.00,
  `venue_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `user_id`, `event_name`, `event_date`, `no_of_guests`, `budget`, `venue_id`, `created_at`) VALUES
(4, 4, 'test', '2026-04-29', 200, 500000.00, 3, '2026-04-25 11:30:04');

-- --------------------------------------------------------

--
-- Table structure for table `event_vendors`
--

CREATE TABLE `event_vendors` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_vendors`
--

INSERT INTO `event_vendors` (`id`, `event_id`, `vendor_id`) VALUES
(5, 4, 1),
(6, 4, 5);

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `guest_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `guest_name` varchar(200) NOT NULL,
  `guest_phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(20) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `created_at`, `role`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$ydOZXgPlPhCkluJzbAn8jO9CDlbQs5uKXDHj6jAxswrRogSWvZuGq', '2026-04-25 11:27:38', 'admin'),
(4, 'user1', 'user1@gmail.com', '$2y$10$YyMclfiAFvNAN658VZXJ..JbOXjArCCt7XEGJiSG566RQQpsRAptq', '2026-04-25 11:29:39', 'user'),
(5, 'user2', 'user2@gmail.com', '$2y$10$lhotS8irQ7A4hMHQJ7uMIuXnkLfYI6836wqi306PTgz7V7UuRhqcq', '2026-04-25 11:36:58', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `vendor_id` int(11) NOT NULL,
  `vendor_name` varchar(200) NOT NULL,
  `vendor_type` varchar(100) NOT NULL,
  `price` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`vendor_id`, `vendor_name`, `vendor_type`, `price`) VALUES
(1, 'Best Catering', 'Catering', 250000.00),
(2, ' Reminiscence Photography', 'Photography', 75000.00),
(3, 'Flower Paradise', 'Decoration', 80000.00),
(4, 'Melody Band', 'Music', 120000.00),
(5, 'Elegant Events', 'Planning', 20000.00);

-- --------------------------------------------------------

--
-- Table structure for table `venues`
--

CREATE TABLE `venues` (
  `venue_id` int(11) NOT NULL,
  `venue_name` varchar(200) NOT NULL,
  `capacity` int(11) NOT NULL,
  `location` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venues`
--

INSERT INTO `venues` (`venue_id`, `venue_name`, `capacity`, `location`, `price`) VALUES
(1, 'ICCB', 700, 'Bashundhara', 500000.00),
(2, 'Army Golf Club', 400, 'Gulshan', 350000.00),
(3, 'Dhaka Club', 300, 'Mirpur', 120000.00),
(4, 'City Center', 200, 'Uttara', 150000.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `event_vendors`
--
ALTER TABLE `event_vendors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`guest_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`vendor_id`);

--
-- Indexes for table `venues`
--
ALTER TABLE `venues`
  ADD PRIMARY KEY (`venue_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `event_vendors`
--
ALTER TABLE `event_vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `guest_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `vendor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `venues`
--
ALTER TABLE `venues`
  MODIFY `venue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `event_vendors`
--
ALTER TABLE `event_vendors`
  ADD CONSTRAINT `event_vendors_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`),
  ADD CONSTRAINT `event_vendors_ibfk_2` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`);

--
-- Constraints for table `guests`
--
ALTER TABLE `guests`
  ADD CONSTRAINT `guests_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
