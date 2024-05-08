-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: May 08, 2024 at 06:47 AM
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
-- Database: `loginsystem_php`
--

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE `test` (
  `id` int(6) UNSIGNED NOT NULL,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `log_password` varchar(50) NOT NULL CHECK (8 <= octet_length(`log_password`)),
  `reg_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `test`
--

INSERT INTO `test` (`id`, `firstname`, `lastname`, `email`, `log_password`, `reg_date`) VALUES
(1, 'mark', 'zuck', 'mark@fb.com', 'mark_1234', '2024-05-03 05:44:03'),
(2, 'daniel', 'krossing', 'dani@fb.com', 'dani_1234', '2024-05-06 10:02:20'),
(3, 'mark', 'henry', 'henry@fb.com', 'mark_12567', '2024-05-06 11:04:10'),
(4, '678', 'scrum', 'kevin@fb.com', 'mark_1234', '2024-05-07 07:01:39'),
(10, '678', 'scrum', 'kevin@fb.com', 'mark_1234', '2024-05-07 07:06:16'),
(11, 'rick', 'scrum', 'kevin@fb.com', 'mark_1234', '2024-05-07 07:07:21'),
(56, 'rick', 'scrum', 'kevin@fb.com', 'mark_1234', '2024-05-07 07:09:37'),
(57, 'rick', 'scrum', 'kevin@fb.com', 'mark_1234', '2024-05-07 07:10:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(6) UNSIGNED NOT NULL,
  `user_name` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `hashed_password` varchar(255) NOT NULL,
  `reg_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_name`, `email`, `hashed_password`, `reg_date`) VALUES
(1, 'mark zuck', 'mark@abc.com', '$2y$10$NjN5rl3Gc4/MVpWeQXP9e.NJahlxSCTOgZLgEVYzHPDQuapAO.y1i', '2024-05-07 05:18:05'),
(2, 'gemunu lakshan', 'gemunu@gmail.com', '$2y$10$QFcfTNZX7i6MYBjXciSmaOkqLBTQwlQV9vd2Y.MmDMkVutI9WicCG', '2024-05-07 06:10:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `test`
--
ALTER TABLE `test`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
