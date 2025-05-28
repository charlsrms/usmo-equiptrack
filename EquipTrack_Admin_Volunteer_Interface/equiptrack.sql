-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2025 at 12:29 PM
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
-- Database: `equiptrack`
--

-- --------------------------------------------------------

--
-- Table structure for table `equipment_activities`
--

CREATE TABLE `equipment_activities` (
  `id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `user_id` varchar(10) NOT NULL,
  `activity_type` enum('added','updated','borrowed','returned','maintenance') NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment_activities`
--

INSERT INTO `equipment_activities` (`id`, `equipment_id`, `user_id`, `activity_type`, `description`, `created_at`) VALUES
(1, 1, 'A001', 'added', 'Initial inventory entry', '2025-05-17 10:41:21'),
(2, 2, 'A001', 'added', 'Purchased new for 2024 events', '2025-05-17 10:41:21'),
(3, 3, 'A001', 'added', 'Donated by local business', '2025-05-17 10:41:21'),
(4, 1, 'V001', 'borrowed', 'For quarterly meeting', '2025-05-17 10:41:21'),
(5, 2, 'V002', 'borrowed', 'University graduation ceremony', '2025-05-17 10:41:21'),
(6, 1, 'V001', 'returned', 'Returned with minor scratches', '2025-05-17 10:41:21'),
(7, 3, 'A001', 'maintenance', 'Sent for lens replacement', '2025-05-17 10:41:21'),
(8, 12, 'A001', 'added', 'Added new equipment: Electric Fan (SN: ELECT-2025-05-17) - Status: Borrowed, Condition: Good(Working), Location: Oval', '2025-05-17 12:18:55'),
(1, 1, 'A001', 'added', 'Initial inventory entry', '2025-05-17 10:41:21'),
(2, 2, 'A001', 'added', 'Purchased new for 2024 events', '2025-05-17 10:41:21'),
(3, 3, 'A001', 'added', 'Donated by local business', '2025-05-17 10:41:21'),
(4, 1, 'V001', 'borrowed', 'For quarterly meeting', '2025-05-17 10:41:21'),
(5, 2, 'V002', 'borrowed', 'University graduation ceremony', '2025-05-17 10:41:21'),
(6, 1, 'V001', 'returned', 'Returned with minor scratches', '2025-05-17 10:41:21'),
(7, 3, 'A001', 'maintenance', 'Sent for lens replacement', '2025-05-17 10:41:21'),
(8, 12, 'A001', 'added', 'Added new equipment: Electric Fan (SN: ELECT-2025-05-17) - Status: Borrowed, Condition: Good(Working), Location: Oval', '2025-05-17 12:18:55'),
(0, 12, 'admin001', 'updated', 'Updated equipment: Electric Fan (SN: ELECT-2025-05-17) - Status: Borrowed, Condition: Good(Working), Location: Oval', '2025-05-25 14:28:33'),
(0, 12, 'admin001', 'updated', 'Updated equipment: Electric Fan (SN: ELECT-2025-05-17) - Status: Borrowed, Condition: Good(Working), Location: Oval', '2025-05-25 14:28:47'),
(0, 12, 'admin001', 'updated', 'Updated equipment: Electric Fans (SN: ELECT-2025-05-17) - Status: Borrowed, Condition: Good(Working), Location: Oval', '2025-05-25 14:30:32');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_list`
--

CREATE TABLE `equipment_list` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `equipment_type` varchar(100) NOT NULL,
  `serial_number` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `conditions` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment_list`
--

INSERT INTO `equipment_list` (`id`, `name`, `equipment_type`, `serial_number`, `quantity`, `status`, `conditions`, `location`, `model`) VALUES
(2, 'Camera', 'Camera', 'CAMER-2025-05-16', 0, 'Available', 'Good', 'Lab C', NULL),
(3, 'Speaker', 'Speaker', 'SPEAK-2025-05-16', 0, 'Under Maintenance', 'Bad(Not Working)', 'ADMN. Building', NULL),
(4, 'Printer', 'Printer', 'PRINT-2025-05-16', 0, 'Under Maintenance', 'Defect(Needs Repair)', 'Rm. 1018', NULL),
(5, 'Mouse', 'Mouse', 'MOUSE-2025-05-16', 0, 'Available', 'Good(Working)', 'Rm. 1017', NULL),
(6, 'Keyboard', 'Keyboard', 'KEYBO-2025-05-17', 0, 'Available', 'Good(Working)', 'Cabinet A', NULL),
(7, 'Charger Type C', 'Charger', 'CHARG-2025-05-17', 0, 'Available', 'Good(Working)', 'Lab B', NULL),
(8, 'Mic', 'Microphone', 'MIC-2025-05-17', 0, 'Under Maintenance', 'Defect(Needs Repair)', 'Cabinet B', NULL),
(9, 'Projector XG-200', 'Projector', 'PROJ-XG200-001', 0, 'Available', 'Good(Working)', 'Storage Room A', NULL),
(10, 'Speaker System JBL-305', 'Speaker', 'JBL-305-042', 0, 'Borrowed', 'Good(Working)', 'Event Hall', NULL),
(11, 'Camera Canon EOS R5', 'Camera', 'CANON-R5-100', 0, 'Available', 'Defect(Needs Repair)', 'Media Room', NULL),
(12, 'Hanabishi', 'Electric Fans', 'ELECT-2025-05-17', 0, 'Borrowed', 'Good(Working)', 'Oval', NULL),
(13, 'Tecno Pova 5G Pro', 'Camera', 'NSHF2837472342', 0, 'Available', 'Defect(Needs Repair)', 'Cabinet A', 'Pova 5G Pro'),
(14, 'Tecno Pova 5G Pro', 'Camera', 'NSHF2837472342', 0, 'Under Maintenance', 'Defect(Needs Repair)', 'Cabinet A', 'Pova 5G Pro'),
(15, 'Tecno Pova 4G Pro', 'Camera', 'NSHF2837472342', 0, 'Under Maintenance', 'Defect(Needs Repair)', 'Cabinet A', 'Pova 4G Pro'),
(16, 'Canon EOS R6', 'Camera', 'CANON-R6-0425', 2, 'Available', 'Good(Working)', 'Media Room', 'EOS R6 Mark II'),
(17, 'Sony A7 IV', 'Camera', 'SONY-A7IV-0524', 1, 'Borrowed', 'Good(Working)', 'Photo Lab', 'Alpha 7 IV'),
(18, 'Nikon Z8', 'Camera', 'NIKON-Z8-0325', 1, 'Under Maintenance', 'Defect(Needs Repair)', 'Storage Room B', 'Z8'),
(19, 'MacBook Pro 16\"', 'Laptop', 'APPLE-MBP16-1123', 3, 'Available', 'Good(Working)', 'IT Office', 'M3 Pro'),
(20, 'Dell XPS 15', 'Laptop', 'DELL-XPS15-0124', 2, 'Available', 'Good(Working)', 'Computer Lab', 'XPS 9530'),
(21, 'JBL EON710', 'Speaker', 'JBL-EON710-0923', 4, 'Available', 'Good(Working)', 'Event Hall', 'EON710'),
(22, 'Bose L1 Pro8', 'Speaker', 'BOSE-L1P8-1023', 2, 'Borrowed', 'Good(Working)', 'Auditorium', 'L1 Pro8'),
(23, 'Epson EB-1781W', 'Projector', 'EPSON-EB1781-0723', 1, 'Under Maintenance', 'Defect(Needs Repair)', 'Conference Room', 'EB-1781W'),
(24, 'Logitech MX Keys', 'Keyboard', 'LOG-MXKEYS-0224', 5, 'Available', 'Good(Working)', 'Cabinet C', 'MX Keys S'),
(25, 'Logitech MX Master 3S', 'Mouse', 'LOG-MXM3S-0324', 5, 'Available', 'Good(Working)', 'Cabinet C', 'MX Master 3S'),
(26, 'DJI Ronin RS3 Pro', 'Camera Stabilizer', 'DJI-RS3P-0124', 1, 'Available', 'Good(Working)', 'Video Studio', 'RS3 Pro'),
(27, 'Manfrotto 190X', 'Tripod', 'MANF-190X-0923', 3, 'Borrowed', 'Good(Working)', 'Photo Studio', '190XPRO'),
(28, 'Godox AD200 Pro', 'Flash', 'GODOX-AD200-0823', 2, 'Available', 'Good(Working)', 'Studio A', 'AD200 Pro'),
(29, 'Sennheiser EW-D', 'Microphone', 'SENN-EWD-0524', 1, 'Under Maintenance', 'Defect(Needs Repair)', 'Audio Room', 'EW-D'),
(30, 'Panasonic AG-CX10', 'Camcorder', 'PANA-CX10-0424', 1, 'Available', 'Good(Working)', 'Video Lab', 'AG-CX10');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_undertaking`
--

CREATE TABLE `equipment_undertaking` (
  `borrower_id` int(11) NOT NULL,
  `recipient_name` varchar(100) NOT NULL,
  `contact_info` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `office_location` varchar(150) NOT NULL,
  `event_purpose` text NOT NULL,
  `equipment_name` varchar(100) NOT NULL,
  `model` varchar(50) NOT NULL,
  `equipment_code` varchar(100) NOT NULL,
  `serial_number` varchar(50) NOT NULL,
  `condition_status` varchar(50) NOT NULL,
  `usage_start` date NOT NULL,
  `usage_end` date NOT NULL,
  `date_submitted` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `undertaking_approval`
--

CREATE TABLE `undertaking_approval` (
  `id` int(11) NOT NULL,
  `volunteer_id` int(11) NOT NULL,
  `borrower_id` int(11) NOT NULL,
  `equipment_code` int(11) NOT NULL,
  `equipment_name` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `serial_number` varchar(200) NOT NULL,
  `condition_status` varchar(200) NOT NULL,
  `recipient_name` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `office_location` varchar(100) NOT NULL,
  `contact_info` varchar(50) NOT NULL,
  `event_purpose` text NOT NULL,
  `usage_start` date NOT NULL,
  `usage_end` date NOT NULL,
  `date_submitted` date NOT NULL,
  `status` enum('pending','approved','declined') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `approved_by` varchar(10) DEFAULT NULL,
  `returned` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `undertaking_approval`
--

INSERT INTO `undertaking_approval` (`id`, `volunteer_id`, `borrower_id`, `equipment_code`, `equipment_name`, `model`, `serial_number`, `condition_status`, `recipient_name`, `position`, `office_location`, `contact_info`, `event_purpose`, `usage_start`, `usage_end`, `date_submitted`, `status`, `admin_notes`, `approved_by`, `returned`) VALUES
(7, 0, 14, 0, 'Camera', 'Pova 5G Pro', 'DSLSJFB247563293374386', 'Working', 'Hanz De Jesus', 'Student', 'HPSB 607', '01929139139', 'Umak PhotoJourn', '2025-05-25', '2025-05-25', '2025-05-25', 'approved', NULL, NULL, ''),
(8, 0, 15, 0, 'Camera', 'Pova 5G Pro', 'DSLSJFB247563293374386', 'Working', 'Hanz De Jesus', 'Student', 'HPSB 607', '01929139139', 'Umak PhotoJourn', '2025-05-25', '2025-05-25', '2025-05-25', 'approved', NULL, NULL, 'Yes');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'volunteer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `disabled_at` timestamp NULL DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `schedule_start` date DEFAULT NULL,
  `schedule_end` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `username`, `password`, `role`, `created_at`, `disabled_at`, `status`, `schedule_start`, `schedule_end`) VALUES
('admin001', 'Admin User', 'admin', 'admin123', 'admin', '2025-05-18 12:11:11', NULL, 'active', NULL, '0000-00-00'),
('V001', 'Charmmain Rabano Lepiten', 'volunteer1', 'v1', 'volunteer', '2025-05-16 16:28:23', NULL, 'Active', NULL, NULL),
('V002', 'Charles Ramos', 'volunteer2', 'v2', 'volunteer', '2025-05-25 09:19:39', NULL, 'Active', '2025-05-26', '2025-05-30');

-- --------------------------------------------------------

--
-- Table structure for table `volunteer_submitted_report`
--

CREATE TABLE `volunteer_submitted_report` (
  `report_id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `check_type` enum('First Check','Final Check') NOT NULL,
  `condition_status` varchar(50) NOT NULL,
  `remarks` text DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `volunteer_submitted_report`
--

INSERT INTO `volunteer_submitted_report` (`report_id`, `user_id`, `equipment_id`, `check_type`, `condition_status`, `remarks`, `submitted_at`) VALUES
(1, 'V001', 13, 'First Check', 'Good(Working)', '', '2025-05-25 17:44:43'),
(2, 'V001', 14, 'First Check', 'Good(Working)', '', '2025-05-25 17:44:43'),
(3, 'V002', 15, 'First Check', 'Good(Working)', '', '2025-05-23 17:44:43'),
(4, 'V001', 13, 'Final Check', 'Good(Working)', '', '2025-05-25 17:49:25'),
(5, 'V001', 14, 'Final Check', 'Good(Working)', '', '2025-05-25 17:49:25'),
(6, 'V002', 15, 'Final Check', 'Good(Working)', '', '2025-05-25 17:49:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `equipment_list`
--
ALTER TABLE `equipment_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `equipment_undertaking`
--
ALTER TABLE `equipment_undertaking`
  ADD PRIMARY KEY (`borrower_id`);

--
-- Indexes for table `undertaking_approval`
--
ALTER TABLE `undertaking_approval`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `volunteer_submitted_report`
--
ALTER TABLE `volunteer_submitted_report`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `id` (`equipment_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `equipment_list`
--
ALTER TABLE `equipment_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `equipment_undertaking`
--
ALTER TABLE `equipment_undertaking`
  MODIFY `borrower_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `undertaking_approval`
--
ALTER TABLE `undertaking_approval`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `volunteer_submitted_report`
--
ALTER TABLE `volunteer_submitted_report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `undertaking_approval`
--
ALTER TABLE `undertaking_approval`
  ADD CONSTRAINT `undertaking_approval_ibfk_1` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `volunteer_submitted_report`
--
ALTER TABLE `volunteer_submitted_report`
  ADD CONSTRAINT `volunteer_submitted_report_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `volunteer_submitted_report_ibfk_2` FOREIGN KEY (`equipment_id`) REFERENCES `equipment_list` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
