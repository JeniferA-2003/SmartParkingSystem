-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 16, 2024 at 06:39 AM
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
-- Database: `SmartParking`
--

-- --------------------------------------------------------

--
-- Table structure for table `ParkedVehicles`
--

CREATE TABLE `ParkedVehicles` (
  `id` int(11) NOT NULL,
  `parking_place_id` int(11) NOT NULL,
  `slot_number` int(11) NOT NULL,
  `vehicle_number` varchar(20) NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `owner_mobile` varchar(20) NOT NULL,
  `arrival_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `parking_duration` time DEFAULT NULL,
  `departure_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ParkingPlaces`
--

CREATE TABLE `ParkingPlaces` (
  `id` int(11) NOT NULL,
  `place_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `total_slots` int(11) NOT NULL,
  `token_id` varchar(100) NOT NULL,
  `location` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ParkingPlaces`
--

INSERT INTO `ParkingPlaces` (`id`, `place_name`, `address`, `total_slots`, `token_id`, `location`) VALUES
(1, 'Providence Mall', 'Cuddalore Road, Ambedkar Nagar, Orleanpet, Puducherry, 605001', 20, 'eSOTABd<d+E7XU2K', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3903.653641974019!2d79.81742671128477!3d11.929174988249649!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a5361cc2830430d%3A0xe9a1860f1f2b4969!2sProvidence%20Mall!5e0!3m2!1sen!2sin!4v1713242198923!5m2!1sen!2sin\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>'),
(2, 'Raja Talkies', 'Raja Talkies Puducherry, 246, Anna Salai, Ilango Nagar, Puducherry, 605001', 32, '3GS?CDTd3L&H8V_N', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15614.349737591538!2d79.79334698276624!3d11.933773837338617!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a53617ec7b98e67%3A0xde535ef9bf5ac246!2sRaja%20Talkies%20Puducherry!5e0!3m2!1sen!2sin!4v1713242302231!5m2!1sen!2sin\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>');

-- --------------------------------------------------------

--
-- Table structure for table `SlotQuery`
--

CREATE TABLE `SlotQuery` (
  `id` int(11) NOT NULL,
  `parking_place_id` int(11) NOT NULL,
  `slot_number` varchar(20) NOT NULL,
  `vehicle_number` varchar(20) NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `owner_mobile` varchar(20) NOT NULL,
  `arrival_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `parking_duration` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SlotStatus`
--

CREATE TABLE `SlotStatus` (
  `id` int(11) NOT NULL,
  `parking_place_id` int(11) NOT NULL,
  `slot_number` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `SlotStatus`
--

INSERT INTO `SlotStatus` (`id`, `parking_place_id`, `slot_number`, `status`, `timestamp`) VALUES
(16, 1, 1, 1, '2024-04-11 04:58:39'),
(17, 1, 14, 0, '2024-03-30 05:54:22'),
(18, 1, 2, 0, '2024-04-11 04:58:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ParkedVehicles`
--
ALTER TABLE `ParkedVehicles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ParkingPlaces`
--
ALTER TABLE `ParkingPlaces`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `SlotQuery`
--
ALTER TABLE `SlotQuery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `SlotStatus`
--
ALTER TABLE `SlotStatus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_slot` (`parking_place_id`,`slot_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ParkedVehicles`
--
ALTER TABLE `ParkedVehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ParkingPlaces`
--
ALTER TABLE `ParkingPlaces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `SlotQuery`
--
ALTER TABLE `SlotQuery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `SlotStatus`
--
ALTER TABLE `SlotStatus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
