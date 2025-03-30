-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 29, 2025 at 01:35 PM
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
-- Database: `doctor_registration`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `department` enum('General Medicine','Neurology','Orthopedic','Gastroenterology') NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `status` enum('Booked','Confirmed','Completed','Cancelled','No-Show') DEFAULT 'Booked',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `bill_date` date NOT NULL,
  `due_date` date NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `status` enum('Generated','Paid','Partially Paid','Cancelled') DEFAULT 'Generated',
  `payment_method` enum('Cash','Card','UPI','Net Banking','Insurance') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `billing_items`
--

CREATE TABLE `billing_items` (
  `id` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `item_type` enum('Consultation','Procedure','Medicine','Test','Room','Other') DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) GENERATED ALWAYS AS (`quantity` * `unit_price`) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `birthdate` date NOT NULL,
  `gender` enum('male','female','others','') NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(10) NOT NULL,
  `optional_mobile` varchar(10) DEFAULT NULL,
  `qualification` varchar(255) NOT NULL,
  `specialization` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `full_name`, `birthdate`, `gender`, `email`, `mobile`, `optional_mobile`, `qualification`, `specialization`, `username`, `password`) VALUES
(1, 'patel princy', '2004-12-06', 'female', 'pallavi.patel876@gmail.com', '9327796864', NULL, 'MBBS and M.D. and M.S. ', 'Gynecologist', 'P@telprincyy123', 'patel@123'),
(2, 'Patel Bharat', '1976-10-27', 'male', 'Bpatel2710@gmail.com', '9756569875', '6847598641', 'MBBS', 'ortho', 'Bpatel2710', 'Patelb@27'),
(4, 'patel pallavi', '1986-02-22', 'female', 'pallavipatel123@gmail.com', '9755222214', '', 'MBBS', 'pediatrecian', 'pallavi@123', 'Pallavi@123'),
(7, 'rishika patel', '2004-07-25', 'female', 'rpatel@gmail.com', '8596741237', '', 'M.D.', 'Radiologist', 'Rpatel@25', 'RishikaP@tel'),
(9, 'devanshi', '2004-03-12', 'female', 'devpatel123@gmmail.com', '7895457594', '', 'M.D.', 'Dermatology', 'Dev@nshi123', 'Patel@dev'),
(10, 'hjvj', '2025-02-16', 'male', 'bgffxg@gmail.com', '9874569856', '', 'hjgvh', 'khhjcn', 'hnc@123', 'Patel@123'),
(11, 'jkbjh', '1980-11-12', 'male', 'jfvhgfc@gmail.com', '9874568576', '', 'kcdmsn', 'lsanmkl', 'lsaknda@123', 'Princy@123'),
(12, 'jhbes', '1996-05-22', 'male', 'lkjlkds@gmail.com', '9547681235', '', 'lkernrk,d', 'skwjdwensk', 'wle@123', '$2y$10$AsQLuXbYikt7XGTrSr1cyOiBiBS8bjUVIwdIJk4P7fNcp5juX9C/C'),
(13, 'mjbm', '1991-08-06', 'male', 'jdnjkbj@gmail.com', '9875648965', '', 'jsbdsj', 'lerih', 'eknd@123', 'Pallavi@123'),
(14, 'm,b nmj', '1992-10-16', 'male', 'kmjbm@gmail.com', '9586478569', '', 'knj', 'klhnk', 'lkihknknjh', '$2y$10$hFFEUu35TsR7Cg17HvEY6OelTz9MjrNyCd4net5iYEPk3neAGf1Mu'),
(15, 'devu', '2005-05-19', 'female', 'jksdhnk@gmail.com', '9656862438', '', 'MBBS', 'Gynecologist', 'jmbjb@123', '$2y$10$kOqiQqA38FvoPWODqHPBiOB.KPsy2ytV7Mh8M5bWv8PnutayFD6um'),
(16, 'esrgf', '1987-06-11', 'male', 'dsrtfjy@gmail.com', '9845888857', '', 'rwer', 'se4t', 'et45r6t', '$2y$10$Y4nxrszg0aMuxW7RkyUdA.YOuuJgiyMQaufUtdIA.Gnj2yAegYPry');

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

CREATE TABLE `medical_records` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `visit_date` date NOT NULL,
  `diagnosis` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `prescription` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `birthdate` date NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` char(10) NOT NULL,
  `optional_mobile` char(10) DEFAULT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `bloodgroup` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`id`, `fullname`, `birthdate`, `gender`, `email`, `mobile`, `optional_mobile`, `address`, `city`, `bloodgroup`, `username`, `password`, `created_at`) VALUES
(1, 'patel pallavi', '1985-02-22', 'female', 'pallavi123@gmail.com', '9874565624', '', 'Vidhyanagar, Bhavnagar', 'Bhavnagar', 'A+', 'p@llav!', '$2y$10$SwHmpcvo8NQ8WZ0Y6AaKy.D1IaP2bdOPG1IjuHiqHa5ItlzWxE2DS', '2025-02-22 05:38:46'),
(2, 'juytgh', '2004-12-06', 'female', 'jhgghbn@gmail.com', '9587468546', '', 'uytredfghyj', 'Surat', '', 'jhgfhj', '$2y$10$5mJbatuVJGcmKFMEllin/eO.OD0XU1Qq8K11.21vDEsJusINBrC.m', '2025-03-29 06:32:17'),
(3, ' hjyhgfh', '1994-10-19', 'male', 'kjuytrewertyui@gmail.com', '9575126965', '', 'jhgrfdsa', 'Anand', 'AB+', '876ytrert', '$2y$10$Ht3s7uX6IboEtzHmqJreU.waMj1NobkewApMqR42tP7hmXJAqLpEq', '2025-03-29 06:49:44');

-- --------------------------------------------------------

--
-- Table structure for table `patient_checkins`
--

CREATE TABLE `patient_checkins` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `checkin_time` datetime NOT NULL,
  `checkout_time` datetime DEFAULT NULL,
  `status` enum('Checked-In','With Doctor','Tests Done','Checked-Out') DEFAULT 'Checked-In',
  `nurse_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_method` enum('Cash','Card','UPI','Net Banking') DEFAULT NULL,
  `status` enum('Success','Failed','Pending') DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `billing_items`
--
ALTER TABLE `billing_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bill_id` (`bill_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `patient_checkins`
--
ALTER TABLE `patient_checkins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bill_id` (`bill_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `billing_items`
--
ALTER TABLE `billing_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `patient_checkins`
--
ALTER TABLE `patient_checkins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`);

--
-- Constraints for table `billing`
--
ALTER TABLE `billing`
  ADD CONSTRAINT `billing_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
  ADD CONSTRAINT `billing_ibfk_2` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`);

--
-- Constraints for table `billing_items`
--
ALTER TABLE `billing_items`
  ADD CONSTRAINT `billing_items_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `billing` (`id`);

--
-- Constraints for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
  ADD CONSTRAINT `medical_records_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`);

--
-- Constraints for table `patient_checkins`
--
ALTER TABLE `patient_checkins`
  ADD CONSTRAINT `patient_checkins_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `billing` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
