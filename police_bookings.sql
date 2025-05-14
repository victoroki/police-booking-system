-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2025 at 07:06 PM
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
-- Database: `police_bookings`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(40) NOT NULL,
  `officer_id` int(11) DEFAULT NULL,
  `event_name` varchar(20) NOT NULL,
  `status` enum('Pending','Approved','Rejected','') NOT NULL,
  `county` varchar(15) NOT NULL,
  `location` varchar(15) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `officer_id`, `event_name`, `status`, `county`, `location`, `phone`, `event_date`, `event_time`, `created_at`, `updated_at`) VALUES
(2, 8, 11, 'wedding', 'Approved', 'Kisii', 'Nyamira Hall', '0154890085', '2025-05-11', '07:00:00', '2025-05-10 16:00:23', '2025-05-10 18:42:16'),
(3, 8, 5, 'wedding', 'Approved', 'Kisii', 'Nyamira Hall', '0154890085', '2025-05-11', '07:00:00', '2025-05-10 16:00:34', '2025-05-10 18:42:06'),
(4, 8, 5, 'wedding', 'Rejected', 'Kisii', 'Nyamira Hall', '0154890085', '2025-05-11', '07:00:00', '2025-05-10 16:03:00', '2025-05-10 18:48:42'),
(5, 8, 5, 'Corporate Event', 'Rejected', 'Kisumu', 'Ground', '0115759045', '2025-05-12', '09:46:00', '2025-05-10 18:46:56', '2025-05-10 18:48:33');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'admin'),
(3, 'customer'),
(2, 'officer');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','officer','customer') NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `remember_token` varchar(64) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `full_name`, `role`, `status`, `remember_token`, `token_expiry`, `created_at`, `updated_at`, `role_id`) VALUES
(1, 'okiomeriv@gmail.com', '$2y$10$EWrhg2itIoznu5ciMMhZLuDljXMoI8JaxNzkaBb5F/i1qGtaZLHLK', 'Victor Mongare', 'customer', 'active', NULL, NULL, '2025-04-20 17:54:32', '2025-04-20 17:54:32', NULL),
(2, 'test@gmail.com', '$2y$10$/XtjctF3BXcIeaxa8KzLOuOwsRG/rNTaV1eeFGmiNLniYPfN723c2', 'test user', 'admin', 'active', NULL, NULL, '2025-04-27 18:32:44', '2025-04-29 13:30:02', NULL),
(5, 'luke@gmail.com', '$2y$10$KJBCEN7HLQCT2ZOo8VVmKubGfdJoiLpqSbQermt1oag5Bqoi8.5u6', 'Luke Alan', 'officer', 'active', NULL, NULL, '2025-04-29 11:54:24', '2025-04-29 12:27:57', 2),
(8, 'emmanuel@gmail.com', '$2y$10$4ndFqHS1lCj7pS.HUiQIyO26dIMkVw63WiiHnEu8Tnm4Pjf5.fV2.', 'emmanuel ema', 'customer', 'active', NULL, NULL, '2025-04-30 10:59:30', '2025-04-30 10:59:30', NULL),
(11, 'john@gmail.com', '$2y$10$VrrRjYKwz404.V7lBJV1S.fNsQXlDk81.aldnRh4Kw.1YVvqN5j.W', 'John Opiyo', 'officer', 'active', NULL, NULL, '2025-05-10 18:33:07', '2025-05-10 18:33:07', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_officer` (`officer_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`officer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_officer` FOREIGN KEY (`officer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
