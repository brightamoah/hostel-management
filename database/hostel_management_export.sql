-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 09, 2025 at 01:43 PM
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
-- Database: `hostel_management`
--
CREATE DATABASE IF NOT EXISTS `hostel_management` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `hostel_management`;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `department` varchar(100) NOT NULL,
  `access_level` enum('Super Admin','Regular Admin','Support Staff') NOT NULL DEFAULT 'Regular Admin',
  `hostel_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `user_id`, `first_name`, `last_name`, `department`, `access_level`, `hostel_id`) VALUES
(1, 4, 'Bright', 'Kweku Amoah', 'IT', 'Super Admin', NULL),
(2, 8, 'Joyce', 'Armah', 'Administration', 'Regular Admin', 5),
(5, 17, 'Bright', 'Malone', 'General', 'Regular Admin', 4),
(6, 23, 'Joyce', 'Armah', 'General', 'Regular Admin', 7);

-- --------------------------------------------------------

--
-- Table structure for table `allocations`
--

CREATE TABLE `allocations` (
  `allocation_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('Active','Expired','Canceled','Pending') NOT NULL DEFAULT 'Pending',
  `allocated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `academic_period` enum('Semester 1','Semester 2','Entire Year','Vacation Period') NOT NULL DEFAULT 'Semester 1'
) ;

--
-- Dumping data for table `allocations`
--

INSERT INTO `allocations` (`allocation_id`, `student_id`, `room_id`, `start_date`, `end_date`, `status`, `allocated_at`, `academic_period`) VALUES
(6, 2, 15, '2025-04-20', NULL, 'Active', '2025-04-20 08:27:04', 'Semester 1'),
(8, 3, 4, '2025-04-22', NULL, 'Active', '2025-04-22 15:23:55', 'Semester 1'),
(14, 8, 14, '2025-04-27', NULL, 'Active', '2025-04-27 13:47:03', 'Semester 1'),
(32, 11, 21, '2025-05-21', NULL, 'Active', '2025-05-21 16:26:48', 'Semester 1'),
(33, 13, 4, '2025-08-24', NULL, 'Active', '2025-08-24 13:23:03', 'Semester 1');

--
-- Triggers `allocations`
--
DELIMITER $$
CREATE TRIGGER `after_allocation_insert_activate_student` AFTER INSERT ON `allocations` FOR EACH ROW BEGIN IF NEW.status = 'Active' THEN
UPDATE students
SET
  resident_status = 'Active'
WHERE
  student_id = NEW.student_id
  AND resident_status = 'Inactive';

END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL,
  `posted_by` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `date_posted` timestamp NOT NULL DEFAULT current_timestamp(),
  `priority` enum('Low','Medium','High','Urgent') NOT NULL DEFAULT 'Medium',
  `target_audience` enum('Students','Admins','All','Specific') NOT NULL DEFAULT 'All',
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`announcement_id`, `posted_by`, `title`, `content`, `date_posted`, `priority`, `target_audience`, `is_read`) VALUES
(1, 1, 'Maintenance Schedule', 'Maintenance for the plumbing system in Hostel A is scheduled on 15th April 2025, from 9 AM to 12 PM. Please ensure your rooms are cleared before the scheduled time.', '2025-04-19 11:43:19', 'High', 'All', 0),
(2, 1, 'Holiday Visiting Hours', 'The visiting hours for holidays are being adjusted from 8 AM - 6 PM to 7AM - 9PM. Please inform your visitors accordingly.', '2025-04-19 11:43:19', 'Medium', 'Students', 0),
(3, 1, 'New Room Availability', 'We have added a new room in Hostel B on the 3rd floor. The room has a capacity of 2 and is available for students to use on weekdays from 10th April 2025.', '2025-04-19 11:43:19', 'Low', 'Students', 0),
(4, 1, 'Fire Drill', 'A mandatory fire drill will take place on November 15th at 3pm. All residents must participate.', '2025-04-19 11:43:19', 'Urgent', 'Students', 0),
(5, 1, 'Jams Night Coming Soon', '<p>Jams Night Coming Soon. It will take place on <b>25th of May, 2025</b> at the forecourt of <b>Viking hostel</b></p>', '2025-05-18 19:45:16', 'Medium', 'Students', 0),
(9, 1, 'Get Your Room Together', '<p>there have been reports of you. fix it now.</p>', '2025-06-20 09:37:33', 'Medium', 'Specific', 0),
(21, 1, 'Getting Started With Pinia', '<p>Getting Started with Pinia</p><p>What is pinia</p><p>where is pinia</p>', '2025-07-06 19:41:21', 'Medium', 'All', 0),
(22, 1, 'Meeting Happening Tomorrow ', '<p>There will be an emergency meeting tomorrow at the forecourt of the Hostel at 4pm&nbsp;&nbsp;</p>', '2025-07-06 19:46:49', 'Urgent', 'Admins', 0),
(23, 1, 'New Test ', '<p>Testing the notification</p>', '2025-07-18 08:22:47', 'High', 'All', 0),
(24, 1, 'Fire Drill', '<p>An emergency fire drill will take place tomorrow</p>', '2025-08-21 22:05:47', 'Urgent', 'Specific', 0);

-- --------------------------------------------------------

--
-- Table structure for table `announcement_reads`
--

CREATE TABLE `announcement_reads` (
  `read_id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `read_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcement_reads`
--

INSERT INTO `announcement_reads` (`read_id`, `announcement_id`, `student_id`, `read_date`) VALUES
(1, 4, 2, '2025-04-24 17:27:40'),
(2, 1, 2, '2025-04-24 17:28:31'),
(3, 2, 2, '2025-04-24 17:28:35'),
(5, 3, 2, '2025-04-24 17:55:01'),
(6, 4, 8, '2025-04-27 14:01:56'),
(7, 1, 8, '2025-04-27 14:02:15'),
(8, 2, 8, '2025-04-27 14:02:18'),
(9, 3, 8, '2025-04-27 14:02:21'),
(10, 5, 8, '2025-05-21 14:38:49'),
(11, 9, 2, '2025-06-26 11:54:05'),
(12, 5, 2, '2025-06-26 11:54:16'),
(13, 21, 2, '2025-07-18 06:53:40'),
(14, 23, 2, '2025-08-15 02:46:28');

-- --------------------------------------------------------

--
-- Table structure for table `announcement_specific_targets`
--

CREATE TABLE `announcement_specific_targets` (
  `id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `target_type` enum('student','admin','building','room') NOT NULL,
  `target_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcement_specific_targets`
--

INSERT INTO `announcement_specific_targets` (`id`, `announcement_id`, `target_type`, `target_id`) VALUES
(3, 9, 'student', 2),
(4, 24, 'student', 2);

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `billing_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `allocation_id` int(11) DEFAULT NULL,
  `hostel_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `date_issued` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_due` datetime NOT NULL,
  `status` enum('Unpaid','Fully Paid','Partially Paid','Overdue','Cancelled') NOT NULL DEFAULT 'Unpaid',
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `billing_type` enum('Hostel Fee','Security Deposit','Utility Fee','Maintenance Fee','Late Payment Penalty','Other') NOT NULL DEFAULT 'Hostel Fee',
  `academic_period` enum('Semester 1','Semester 2','Entire Year','Vacation Period') NOT NULL DEFAULT 'Semester 1',
  `payment_terms` enum('Net 15 Days','Net 30 Days','Net 45 Days','Immediate Payment') NOT NULL DEFAULT 'Net 30 Days',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `late_fee` decimal(10,2) DEFAULT 0.00,
  `outstanding_amount` decimal(10,2) GENERATED ALWAYS AS (greatest(`amount` + `late_fee` - `paid_amount`,0)) STORED
) ;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`billing_id`, `student_id`, `allocation_id`, `hostel_id`, `amount`, `description`, `date_issued`, `date_due`, `status`, `paid_amount`, `billing_type`, `academic_period`, `payment_terms`, `updated_at`, `late_fee`) VALUES
(3, 2, 6, 7, 4500.00, 'Room 300 allocation fee starting 2025-04-20', '2025-04-20 08:27:04', '2025-05-20 00:00:00', 'Fully Paid', 4500.00, 'Hostel Fee', 'Semester 1', 'Net 30 Days', '2025-08-26 12:21:01', 0.00),
(5, 3, 8, 5, 3500.00, 'Room 104 allocation fee starting 2025-04-22', '2025-04-22 15:23:55', '2025-05-22 00:00:00', 'Fully Paid', 3500.00, 'Hostel Fee', 'Semester 1', 'Net 30 Days', '2025-08-26 12:21:01', 0.00),
(7, 8, 14, 7, 1000.00, 'Room 110 allocation fee starting 2025-04-27', '2025-04-27 13:47:03', '2025-05-27 00:00:00', 'Fully Paid', 1000.00, 'Hostel Fee', 'Semester 1', 'Net 30 Days', '2025-08-26 12:21:01', 0.00),
(22, 11, 32, 7, 3000.00, 'Room fee for 109 -  in Vikings', '2025-05-21 16:26:48', '2025-06-20 18:26:48', 'Fully Paid', 3000.00, 'Hostel Fee', 'Semester 1', 'Net 30 Days', '2025-08-26 12:21:01', 0.00),
(24, 2, 6, 7, 200.00, 'Second semester utility fee', '2025-06-28 09:25:31', '2025-07-28 07:25:04', 'Fully Paid', 200.00, 'Utility Fee', 'Semester 2', 'Net 30 Days', '2025-08-26 12:21:01', 0.00),
(27, 11, 32, 7, 300.00, 'Maintenance fee for the items in the hostel for second semester. Please make sure to pay before the due date to prevent any additional charges ', '2025-06-29 09:15:07', '2025-07-14 07:13:35', 'Fully Paid', 300.00, 'Maintenance Fee', 'Semester 2', 'Net 15 Days', '2025-08-26 20:25:46', 15.00),
(28, 3, 8, 5, 20.00, 'Penalty for late payment', '2025-06-29 10:17:45', '2025-07-06 08:17:23', 'Overdue', 0.00, 'Late Payment Penalty', 'Semester 2', 'Immediate Payment', '2025-08-26 12:21:01', 1.00),
(29, 2, 6, 7, 10.00, 'Penalty for late payment', '2025-06-29 10:22:44', '2025-07-06 08:22:17', 'Fully Paid', 10.00, 'Late Payment Penalty', 'Semester 2', 'Immediate Payment', '2025-08-26 12:21:01', 0.00),
(30, 2, 6, 7, 60.00, 'Security Deposit', '2025-06-29 10:34:27', '2025-07-06 08:34:16', 'Fully Paid', 63.00, 'Security Deposit', 'Semester 2', 'Immediate Payment', '2025-08-26 12:21:01', 3.00),
(31, 12, NULL, 4, 10.00, 'Maintenance fee for the items in the hostel for second semester', '2025-06-29 11:34:32', '2025-07-29 09:33:00', 'Overdue', 0.00, 'Hostel Fee', 'Semester 2', 'Net 30 Days', '2025-08-26 12:24:51', 0.50),
(33, 2, 6, 7, 10.00, 'Maintenance fee for the new generator set', '2025-06-29 13:49:30', '2025-07-06 11:49:06', 'Overdue', 0.00, 'Maintenance Fee', 'Semester 2', 'Immediate Payment', '2025-08-26 12:21:01', 0.50),
(35, 3, 8, 5, 30.00, 'Added fees', '2025-06-29 20:58:16', '2025-07-29 18:57:50', 'Overdue', 0.00, 'Utility Fee', 'Entire Year', 'Net 30 Days', '2025-08-26 12:21:01', 1.50),
(36, 3, 8, 5, 56.00, 'new fees', '2025-06-29 21:16:05', '2025-07-06 19:15:00', 'Overdue', 36.00, 'Hostel Fee', 'Semester 1', 'Immediate Payment', '2025-08-26 12:21:01', 2.80),
(37, 2, 6, 7, 56.00, 'maintenance', '2025-06-30 10:00:06', '2025-07-15 07:59:55', 'Overdue', 55.00, 'Maintenance Fee', 'Semester 2', 'Net 15 Days', '2025-08-26 12:21:01', 2.80),
(39, 2, 6, 7, 34.00, 'Test ', '2025-06-30 13:00:53', '2025-07-15 11:00:30', 'Fully Paid', 35.70, 'Utility Fee', 'Semester 2', 'Net 15 Days', '2025-08-26 12:21:01', 1.70),
(40, 2, 6, 7, 20.00, 'New test', '2025-06-30 13:13:02', '2025-07-15 11:12:53', 'Fully Paid', 21.00, 'Security Deposit', 'Semester 1', 'Net 15 Days', '2025-08-27 07:59:33', 1.00),
(41, 2, 6, 7, 20.00, 'New test', '2025-06-30 13:13:53', '2025-07-15 11:12:53', 'Overdue', 5.00, 'Security Deposit', 'Semester 1', 'Net 15 Days', '2025-08-26 12:21:01', 1.00),
(42, 3, 8, 5, 20.00, 'Test mail', '2025-06-30 13:17:23', '2025-07-15 11:17:14', 'Overdue', 0.00, 'Hostel Fee', 'Semester 1', 'Net 15 Days', '2025-08-26 12:21:01', 1.00),
(43, 2, 6, 7, 40.00, 'awesome', '2025-06-30 13:22:23', '2025-07-30 11:22:00', 'Overdue', 35.00, 'Hostel Fee', 'Semester 1', 'Net 30 Days', '2025-08-26 12:21:01', 2.00),
(44, 2, 6, 7, 34.00, 'Description for test', '2025-06-30 13:25:14', '2025-07-15 11:24:00', 'Fully Paid', 35.70, 'Hostel Fee', 'Semester 1', 'Net 15 Days', '2025-08-26 12:21:01', 1.70),
(45, 2, 6, 7, 70.00, '78', '2025-06-30 13:27:39', '2025-07-15 11:27:00', 'Fully Paid', 70.00, 'Hostel Fee', 'Semester 2', 'Net 15 Days', '2025-08-26 12:21:01', 0.00),
(46, 2, 6, 7, 50.00, 'test', '2025-06-30 13:30:16', '2025-07-15 11:30:00', 'Fully Paid', 50.00, 'Hostel Fee', 'Semester 1', 'Net 15 Days', '2025-08-26 12:21:01', 0.00),
(47, 2, 6, 7, 67.00, 'awesome', '2025-06-30 13:36:54', '2025-07-30 11:36:00', 'Fully Paid', 67.00, 'Hostel Fee', 'Semester 1', 'Net 15 Days', '2025-08-26 12:21:01', 0.00),
(50, 3, 8, 5, 56.00, 'Test Utility', '2025-06-30 22:47:51', '2025-07-07 20:47:37', 'Fully Paid', 56.00, 'Utility Fee', 'Semester 1', 'Immediate Payment', '2025-08-26 12:21:01', 0.00),
(51, 2, 6, 7, 77.00, 'Test change of description ', '2025-06-30 22:53:11', '2025-07-30 20:52:00', 'Fully Paid', 77.00, 'Hostel Fee', 'Semester 1', 'Net 30 Days', '2025-08-26 12:21:01', 0.00),
(54, 2, 6, 7, 56.32, 'Late payment penalty', '2025-07-18 06:48:00', '2025-08-19 04:47:00', 'Fully Paid', 56.32, 'Late Payment Penalty', 'Semester 2', 'Net 45 Days', '2025-08-26 12:21:01', 0.00),
(55, 2, 6, 7, 20.00, 'Test fees', '2025-08-07 15:51:05', '2025-09-06 13:50:40', 'Fully Paid', 20.00, 'Late Payment Penalty', 'Semester 1', 'Net 30 Days', '2025-08-26 12:21:01', 0.00),
(56, 2, 6, 7, 50.00, 'Maintenance Fee for first semester ', '2025-08-21 23:58:23', '2025-09-20 21:56:59', 'Fully Paid', 50.00, 'Maintenance Fee', 'Semester 1', 'Net 30 Days', '2025-08-26 12:21:01', 0.00),
(57, 13, 33, 5, 3500.00, 'Room fee for 104 - Double in Hostel A', '2025-08-24 13:23:03', '2025-09-23 15:23:03', 'Partially Paid', 600.00, 'Hostel Fee', 'Semester 1', 'Net 30 Days', '2025-08-26 12:35:09', 0.00);

--
-- Triggers `billing`
--
DELIMITER $$
CREATE TRIGGER `update_billing_status_before_insert` BEFORE INSERT ON `billing` FOR EACH ROW BEGIN
    IF NEW.status = 'Cancelled' THEN
        -- Retain Cancelled status
        SET NEW.status = 'Cancelled';
    ELSEIF NEW.date_due < NOW() AND NEW.paid_amount < NEW.amount THEN
        -- Set to Overdue if due date has passed and not fully paid
        SET NEW.status = 'Overdue';
    ELSEIF NEW.paid_amount >= NEW.amount THEN
        -- Set to Fully Paid if payment covers the full amount
        SET NEW.status = 'Fully Paid';
    ELSEIF NEW.paid_amount > 0 AND NEW.paid_amount < NEW.amount THEN
        -- Set to Partially Paid if partial payment has been made
        SET NEW.status = 'Partially Paid';
    ELSE
        -- Set to Unpaid if no payment has been made
        SET NEW.status = 'Unpaid';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_billing_status_before_update` BEFORE UPDATE ON `billing` FOR EACH ROW BEGIN
    IF NEW.status = 'Cancelled' THEN
        -- Retain Cancelled status
        SET NEW.status = 'Cancelled';
    ELSEIF NEW.date_due < NOW() AND NEW.paid_amount < NEW.amount THEN
        -- Set to Overdue if due date has passed and not fully paid
        SET NEW.status = 'Overdue';
    ELSEIF NEW.paid_amount >= NEW.amount THEN
        -- Set to Fully Paid if payment covers the full amount
        SET NEW.status = 'Fully Paid';
    ELSEIF NEW.paid_amount > 0 AND NEW.paid_amount < NEW.amount THEN
        -- Set to Partially Paid if partial payment has been made
        SET NEW.status = 'Partially Paid';
    ELSE
        -- Set to Unpaid if no payment has been made
        SET NEW.status = 'Unpaid';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `complaint_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `hostel_id` int(11) DEFAULT NULL,
  `complaint_type` enum('Room Condition','Staff Behavior','Amenities','Noise','Security','Billing Issue','Other') NOT NULL,
  `description` text NOT NULL,
  `priority` enum('Low','Medium','High','Emergency') NOT NULL DEFAULT 'Medium',
  `status` enum('Pending','In-Progress','Resolved','Rejected') NOT NULL DEFAULT 'Pending',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolved_by` int(11) DEFAULT NULL
) ;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`complaint_id`, `student_id`, `room_id`, `hostel_id`, `complaint_type`, `description`, `priority`, `status`, `submitted_at`, `resolved_at`, `resolved_by`) VALUES
(26, 2, 8, 6, 'Amenities', 'Faulty electrical outlet in room 202-B', 'High', 'In-Progress', '2025-01-06 09:00:00', NULL, NULL),
(27, 2, NULL, 7, 'Room Condition', 'Stained mattress in room 301', 'Medium', 'Resolved', '2025-01-12 10:00:00', '2025-01-14 10:00:00', NULL),
(28, 2, NULL, 7, 'Staff Behavior', 'Cafeteria staff ignored dietary restrictions', 'Medium', 'Rejected', '2025-01-18 12:00:00', '2025-01-20 12:00:00', NULL),
(29, 2, 10, 5, 'Amenities', 'Gym equipment broken for a week', 'Low', 'Pending', '2025-01-22 14:00:00', NULL, NULL),
(30, 2, NULL, 7, 'Noise', 'Loud parties in common area after midnight', 'Medium', 'In-Progress', '2025-01-28 23:00:00', NULL, NULL),
(31, 2, NULL, 7, 'Security', 'Lost key not replaced promptly', 'High', 'Resolved', '2025-02-01 08:00:00', '2025-02-03 08:00:00', NULL),
(32, 2, 2, 6, 'Amenities', 'Broken showerhead in room 102', 'Medium', 'Resolved', '2025-02-04 10:00:00', '2025-02-06 10:00:00', NULL),
(33, 2, NULL, 7, 'Billing Issue', 'Double-charged for room rent', 'High', 'In-Progress', '2025-02-08 11:00:00', NULL, NULL),
(34, 2, 3, 6, 'Room Condition', 'Bad odor in room 201', 'Medium', 'Pending', '2025-02-12 09:00:00', NULL, NULL),
(35, 2, 4, 5, 'Amenities', 'No water dispenser in hostel block', 'Low', 'Resolved', '2025-02-18 13:00:00', '2025-02-20 13:00:00', NULL),
(36, 2, NULL, 7, 'Room Condition', 'No study room available during exams', 'Medium', 'Pending', '2025-02-22 15:00:00', NULL, NULL),
(37, 2, 5, 5, 'Amenities', 'AC not cooling properly in room 101-B', 'High', 'In-Progress', '2025-02-26 10:00:00', NULL, NULL),
(38, 2, 6, 6, 'Noise', 'Noisy plumbing pipes at night', 'Medium', 'Resolved', '2025-03-02 22:00:00', '2025-03-04 22:00:00', NULL),
(39, 2, NULL, 7, 'Staff Behavior', 'Maintenance staff entered room without permission', 'High', 'In-Progress', '2025-03-06 08:00:00', NULL, NULL),
(40, 2, 7, 6, 'Security', 'Broken window in common room', 'Emergency', 'Resolved', '2025-03-12 09:00:00', '2025-03-14 09:00:00', NULL),
(41, 2, 8, 6, 'Room Condition', 'Loose door hinges in room 202-B', 'Medium', 'Pending', '2025-03-16 11:00:00', NULL, NULL),
(42, 2, NULL, 7, 'Amenities', 'Flickering lights in room 301', 'Medium', 'Resolved', '2025-03-22 12:00:00', '2025-03-24 12:00:00', NULL),
(43, 2, NULL, 7, 'Billing Issue', 'Late fee applied incorrectly', 'Medium', 'In-Progress', '2025-03-26 14:00:00', NULL, NULL),
(44, 2, 10, 5, 'Amenities', 'Laundry machine out of service', 'Low', 'In-Progress', '2025-03-31 13:00:00', NULL, NULL),
(45, 2, NULL, 7, 'Noise', 'Students shouting in hallway at 2 AM', 'Medium', 'In-Progress', '2025-04-02 02:00:00', NULL, NULL),
(46, 2, NULL, 7, 'Other', 'No parking space for visitors', 'Low', 'Resolved', '2025-04-06 10:00:00', '2025-08-26 12:05:25', NULL),
(47, 2, 2, 6, 'Amenities', 'Broken toilet flush valve in room 102', 'High', 'Resolved', '2025-04-08 09:00:00', '2025-07-21 18:33:47', NULL),
(48, 2, 3, 6, 'Room Condition', 'Damaged wardrobe in room 201', 'Medium', 'Resolved', '2025-04-11 11:00:00', '2025-07-21 18:38:44', NULL),
(49, 2, NULL, 7, 'Staff Behavior', 'Security guard was unresponsive', 'Medium', 'Rejected', '2025-04-13 10:00:00', '2025-04-15 10:00:00', NULL),
(50, 2, 4, 5, 'Security', 'Emergency exit blocked by furniture', 'Emergency', 'Resolved', '2025-04-16 08:00:00', '2025-08-03 07:51:28', NULL),
(51, 8, NULL, 7, 'Noise', 'Students in room 103 keep playing loud music at late hours and it\'s disturbing my sleep', 'Medium', 'Resolved', '2025-04-27 13:54:34', '2025-07-21 14:37:53', NULL),
(52, 2, 4, 5, 'Noise', 'The residents of room 104 keep making noise affecting my sleep and study periods', 'Medium', 'Resolved', '2025-08-03 17:52:47', '2025-08-09 01:46:24', NULL),
(53, 2, 5, 5, 'Noise', 'Resident off room 311 keep making noise', 'Medium', 'In-Progress', '2025-08-21 21:24:17', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `complaint_responses`
--

CREATE TABLE `complaint_responses` (
  `response_id` int(11) NOT NULL,
  `complaint_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `response_text` text NOT NULL,
  `action_taken` enum('Assigned','Updated','Resolved','Rejected') NOT NULL,
  `response_date` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `complaint_responses`
--

INSERT INTO `complaint_responses` (`response_id`, `complaint_id`, `admin_id`, `response_text`, `action_taken`, `response_date`) VALUES
(23, 26, 1, 'Electrician assigned to fix outlet', 'Assigned', '2025-01-07 10:00:00'),
(24, 27, 1, 'Mattress replaced with new one', 'Resolved', '2025-01-14 12:00:00'),
(25, 28, 1, 'Dietary issue not reported earlier; rejected', 'Rejected', '2025-01-20 09:00:00'),
(26, 30, 1, 'Security warned about noise; monitoring situation', 'Assigned', '2025-01-29 09:00:00'),
(27, 31, 1, 'New key issued and lock changed', 'Resolved', '2025-02-03 10:00:00'),
(28, 32, 1, 'Showerhead replaced', 'Resolved', '2025-02-06 11:00:00'),
(29, 33, 1, 'Billing team investigating double charge', 'Assigned', '2025-02-09 10:00:00'),
(30, 33, 1, 'Refund processed for overcharge', 'Updated', '2025-02-10 12:00:00'),
(31, 35, 1, 'Water dispenser installed in block', 'Resolved', '2025-02-20 14:00:00'),
(32, 37, 1, 'AC technician scheduled for repair', 'Assigned', '2025-02-27 09:00:00'),
(33, 38, 1, 'Plumbing issue fixed', 'Resolved', '2025-03-04 10:00:00'),
(34, 39, 1, 'Staff retrained on entry protocols', 'Assigned', '2025-03-07 09:00:00'),
(35, 40, 1, 'Window repaired and secured', 'Resolved', '2025-03-14 11:00:00'),
(36, 42, 1, 'Electrician fixed lighting issue', 'Resolved', '2025-03-24 13:00:00'),
(37, 43, 1, 'Billing team correcting late fee', 'Assigned', '2025-03-27 10:00:00'),
(38, 46, 1, 'Security warned about noise violations', 'Assigned', '2025-04-03 09:00:00'),
(39, 47, 1, 'Plumber assigned to fix flush valve', 'Assigned', '2025-04-09 10:00:00'),
(40, 48, 1, 'Wardrobe repaired', 'Resolved', '2025-04-13 12:00:00'),
(41, 49, 1, 'No security lapse found; complaint rejected', 'Rejected', '2025-04-15 09:00:00'),
(42, 50, 1, 'Emergency exit cleared and checked', 'Resolved', '2025-04-17 10:00:00'),
(45, 49, 1, 'Doesn\'t meet the criteria', 'Rejected', '2025-08-03 09:55:33'),
(46, 48, 1, 'Doesn\'t meet the criteria', 'Rejected', '2025-08-03 09:56:58'),
(47, 51, 1, 'Complaint resolved', 'Resolved', '2025-08-03 10:01:41'),
(48, 44, 1, 'A technician has been assigned to fix it', 'Assigned', '2025-08-03 10:05:00'),
(49, 52, 1, 'Warning issued ', 'Updated', '2025-08-05 15:34:35'),
(50, 52, 1, 'The residents of room 104 have been given stern warning about noise making and they have agreed to bring it down', 'Resolved', '2025-08-09 01:48:25'),
(51, 46, 6, 'Issue resolved', 'Resolved', '2025-08-26 12:05:53');

-- --------------------------------------------------------

--
-- Table structure for table `disciplinary_records`
--

CREATE TABLE `disciplinary_records` (
  `record_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `reported_by` int(11) DEFAULT NULL,
  `violation_type` enum('Noise Complaint','Curfew Violation','Substance Abuse','Vandalism','Theft','Unauthorized Guest','Other') NOT NULL,
  `description` text NOT NULL,
  `date_reported` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_occurred` datetime DEFAULT NULL,
  `severity` enum('Minor','Moderate','Severe') NOT NULL,
  `action_taken` text NOT NULL,
  `status` enum('Pending','Resolved','Dismissed','Rejected') NOT NULL DEFAULT 'Pending',
  `resolution_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hostels`
--

CREATE TABLE `hostels` (
  `hostel_id` int(11) NOT NULL,
  `hostel_name` varchar(100) NOT NULL,
  `hostel_code` varchar(10) NOT NULL,
  `address` text DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Active','Inactive','Under Construction') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hostels`
--

INSERT INTO `hostels` (`hostel_id`, `hostel_name`, `hostel_code`, `address`, `contact_phone`, `contact_email`, `manager_id`, `created_at`, `status`) VALUES
(4, 'Diamond Jubilee  Hostel', 'DIA_1', 'Building Diamond Jubilee ', NULL, NULL, NULL, '2025-08-25 17:29:59', 'Active'),
(5, 'Hostel A Hostel', 'HOS_2', 'Building Hostel A', NULL, NULL, NULL, '2025-08-25 17:29:59', 'Active'),
(6, 'Hostel B Hostel', 'HOS_3', 'Building Hostel B', NULL, NULL, NULL, '2025-08-25 17:29:59', 'Active'),
(7, 'Vikings Hostel', 'VIK_4', 'Building Vikings', NULL, NULL, NULL, '2025-08-25 17:29:59', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_requests`
--

CREATE TABLE `maintenance_requests` (
  `request_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `hostel_id` int(11) DEFAULT NULL,
  `issue_type` enum('Plumbing','Electrical','Furniture','Appliance','Structural','Pest Control','Internet/Wi-Fi','Other') NOT NULL,
  `description` text NOT NULL,
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `priority` enum('Low','Medium','High','Emergency') NOT NULL DEFAULT 'Medium',
  `status` enum('Pending','Assigned','In-Progress','Completed','Rejected') NOT NULL DEFAULT 'Pending',
  `completion_date` timestamp NULL DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `maintenance_requests`
--

INSERT INTO `maintenance_requests` (`request_id`, `student_id`, `room_id`, `hostel_id`, `issue_type`, `description`, `request_date`, `priority`, `status`, `completion_date`, `assigned_to`) VALUES
(3, 2, 5, 5, 'Furniture', 'Desk drawer stuck and won’t open', '2025-04-17 08:30:00', 'Low', 'Assigned', NULL, NULL),
(4, 2, 5, 5, 'Electrical', 'Power outlet sparking intermittently', '2025-04-17 10:00:00', 'Emergency', 'Assigned', NULL, NULL),
(6, 2, 6, 6, 'Appliance', 'Air conditioner making loud noises', '2025-04-10 09:00:00', 'High', 'Completed', '2025-04-11 15:00:00', NULL),
(10, 2, 5, 5, 'Furniture', 'Desk drawer stuck and won’t open', '2025-04-17 08:30:00', 'Low', 'Rejected', NULL, NULL),
(11, 2, 5, 5, 'Electrical', 'Power outlet sparking intermittently', '2025-04-17 10:00:00', 'Emergency', 'Assigned', NULL, NULL),
(12, 2, 6, 6, 'Plumbing', 'Showerhead leaking constantly', '2025-04-14 11:00:00', 'Medium', 'In-Progress', NULL, NULL),
(15, 2, 15, 7, 'Electrical', 'Our ceiling fan is no longer working', '2025-05-11 14:18:20', 'High', 'Completed', '2025-08-26 11:27:11', NULL),
(16, 2, 15, 7, 'Structural', 'There\'s a huge crack on the wall in our room', '2025-08-03 17:59:56', 'High', 'Completed', '2025-08-26 11:35:06', NULL),
(17, 2, 15, 7, 'Plumbing', 'Leaking pipe', '2025-08-21 21:27:22', 'High', 'Assigned', NULL, NULL),
(18, 8, 14, 7, 'Furniture', 'The study table in the room has a broken leg and we would like it to be fixed or replaced', '2025-08-24 09:44:20', 'High', 'In-Progress', '2025-08-26 11:27:11', NULL),
(19, 2, 15, 7, 'Structural', 'There are huge crack on the room walls', '2025-08-27 08:00:48', 'Medium', 'Pending', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_responses`
--

CREATE TABLE `maintenance_responses` (
  `response_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `response_text` text NOT NULL,
  `response_date` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `maintenance_responses`
--

INSERT INTO `maintenance_responses` (`response_id`, `request_id`, `user_id`, `response_text`, `response_date`) VALUES
(34, 6, 4, 'Rejected: Construction noise is outside hostel control.', '2025-04-10 10:00:00'),
(36, 3, 4, 'Structural engineer assigned to assess the crack.', '2025-04-13 10:00:00'),
(37, 10, 4, 'Plumber dispatched to fix the showerhead.', '2025-04-15 09:00:00'),
(38, 12, 4, 'Please confirm the repair time.', '2025-04-15 11:00:00'),
(46, 10, 4, 'carpenter assigned', '2025-04-27 09:42:56'),
(51, 10, 4, 'The drawer and table has been fixed', '2025-04-27 10:32:23'),
(52, 3, 4, 'Plumber assigned to fix the problem', '2025-04-27 14:26:24'),
(53, 15, 4, 'The electrician has been assigned to fix the electric fan on Friday.', '2025-05-21 16:54:27'),
(54, 15, 4, 'Assigned', '2025-07-02 12:47:10'),
(55, 17, 4, 'A plumber has been assigned to fix the leak', '2025-08-21 21:55:00'),
(56, 18, 23, 'Testing the responses', '2025-08-26 11:14:31');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `billing_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `transaction_reference` varchar(100) NOT NULL,
  `payment_method` enum('Cash','Credit Card','Bank Transfer','Mobile Money') NOT NULL,
  `purpose` enum('Hostel Fee','Penalty','Security Deposit','Maintenance Charge','Other') NOT NULL,
  `status` enum('Pending','Completed','Failed','Refunded') NOT NULL DEFAULT 'Pending',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `student_id`, `billing_id`, `amount`, `payment_date`, `transaction_reference`, `payment_method`, `purpose`, `status`, `description`, `created_at`) VALUES
(2, 2, 3, 1200.00, '2025-04-20 08:31:25', 'TXN-111000111', 'Mobile Money', 'Hostel Fee', 'Completed', NULL, '2025-08-04 16:56:22'),
(3, 11, 22, 1000.00, '2025-05-23 06:18:11', 'TXN-111000115', 'Cash', 'Hostel Fee', 'Completed', NULL, '2025-08-04 16:56:22'),
(4, 3, 5, 1000.00, '2025-05-23 11:44:39', 'TXN-111000117', 'Credit Card', 'Hostel Fee', 'Completed', NULL, '2025-08-04 16:56:22'),
(5, 8, 7, 1000.00, '2025-05-23 11:44:39', 'TXN-111000118', 'Cash', 'Hostel Fee', 'Completed', NULL, '2025-08-04 16:56:22'),
(6, 2, 3, 1301.00, '2025-05-23 11:44:39', 'TXN-111000116', 'Mobile Money', 'Hostel Fee', 'Completed', NULL, '2025-08-04 16:56:22'),
(7, 3, 50, 56.00, '2025-06-30 20:51:25', 'TXN-111000119', 'Mobile Money', 'Other', 'Completed', NULL, '2025-08-04 16:56:22'),
(8, 2, 54, 56.32, '2025-07-18 06:04:11', 'TXN-INV-000054', 'Cash', 'Penalty', 'Completed', NULL, '2025-08-04 16:56:22'),
(9, 3, 5, 2500.00, '2025-07-18 06:31:12', 'TXN-INV-000005', 'Cash', 'Hostel Fee', 'Completed', NULL, '2025-08-04 16:56:22'),
(10, 2, 47, 67.00, '2025-07-18 06:33:27', 'TXN-INV-000047', 'Cash', 'Hostel Fee', 'Completed', NULL, '2025-08-04 16:56:22'),
(11, 2, 46, 50.00, '2025-07-18 06:34:20', 'TXN-INV-000046', 'Cash', 'Hostel Fee', 'Completed', NULL, '2025-08-04 16:56:22'),
(12, 2, 45, 40.00, '2025-07-18 06:33:00', 'TXN-INV-000045', 'Cash', 'Hostel Fee', 'Completed', NULL, '2025-08-04 16:56:22'),
(13, 2, 45, 30.00, '2025-07-18 06:33:00', 'TXN-INV-000045', 'Cash', 'Hostel Fee', 'Completed', NULL, '2025-08-04 16:56:22'),
(14, 3, 36, 36.00, '2025-07-18 06:39:10', 'TXN-INV-000036', 'Cash', 'Hostel Fee', 'Completed', NULL, '2025-08-04 16:56:22'),
(15, 2, 3, 1999.00, '2025-07-18 06:33:00', 'TXN-INV-000003', 'Cash', 'Hostel Fee', 'Completed', NULL, '2025-08-04 16:56:22'),
(18, 2, 51, 77.00, '2025-08-04 18:37:48', 'KH_51_1754332488_2_6584', 'Mobile Money', 'Hostel Fee', 'Completed', 'Test change of description ', '2025-08-04 18:34:50'),
(19, 2, 24, 200.00, '2025-08-04 18:44:18', 'KH_24_1754333033_2_3957', 'Mobile Money', 'Other', 'Completed', 'Second semester utility fee', '2025-08-04 18:43:55'),
(20, 2, 29, 10.00, '2025-08-04 19:16:33', 'KH_29_1754334921_2_1909', 'Credit Card', 'Penalty', 'Completed', 'Penalty for late payment', '2025-08-04 19:15:23'),
(21, 11, 22, 2000.00, '2025-08-05 15:28:07', 'KH_22_1754407645_11_4096', 'Credit Card', 'Hostel Fee', 'Completed', 'Room fee for 109 -  in Vikings', '2025-08-05 15:27:26'),
(22, 2, 44, 34.00, '2025-08-06 08:09:44', 'KH_44_1754467780_2_6321', '', 'Hostel Fee', 'Pending', 'Description for test', '2025-08-06 08:09:44'),
(23, 2, 44, 34.00, '2025-08-06 08:31:55', 'KH_44_1754469113_2_8937', '', 'Hostel Fee', 'Pending', 'Description for test', '2025-08-06 08:31:55'),
(24, 2, 44, 20.00, '2025-08-06 08:52:50', 'KH_44_1754470355_2_4408', 'Credit Card', 'Hostel Fee', 'Completed', 'Description for test', '2025-08-06 08:52:36'),
(25, 2, 43, 40.00, '2025-08-07 08:58:47', 'KH_43_1754557119_2_9640', '', 'Hostel Fee', 'Pending', 'awesome', '2025-08-07 08:58:47'),
(26, 2, 43, 40.00, '2025-08-07 09:52:40', 'KH_43_1754560359_2_2691', '', 'Hostel Fee', 'Pending', 'awesome', '2025-08-07 09:52:40'),
(27, 2, 43, 40.00, '2025-08-07 10:01:09', 'KH_43_1754560868_2_6324', '', 'Hostel Fee', 'Pending', 'awesome', '2025-08-07 10:01:09'),
(28, 2, 43, 30.00, '2025-08-07 10:01:58', 'KH_43_1754560902_2_7242', 'Credit Card', 'Hostel Fee', 'Completed', 'awesome', '2025-08-07 10:01:43'),
(29, 2, 41, 20.00, '2025-08-07 10:06:27', 'KH_41_1754561186_2_6675', '', 'Security Deposit', 'Pending', 'New test', '2025-08-07 10:06:27'),
(30, 2, 55, 10.00, '2025-08-08 00:50:52', 'KH_55_1754614238_2_4789', 'Credit Card', 'Penalty', 'Completed', 'Test fees', '2025-08-08 00:50:39'),
(31, 2, 55, 5.00, '2025-08-08 02:52:43', 'KH_55_1754621541_2_5399', 'Mobile Money', 'Penalty', 'Completed', 'Test fees', '2025-08-08 02:52:22'),
(32, 2, 55, 3.00, '2025-08-08 09:11:17', 'KH_55_1754642428_2_2015', 'Mobile Money', 'Penalty', 'Completed', 'Test fees', '2025-08-08 08:40:30'),
(33, 2, 44, 5.70, '2025-08-08 13:39:23', 'KH_44_1754660322_2_6890', 'Mobile Money', 'Hostel Fee', 'Completed', 'Description for test', '2025-08-08 13:38:44'),
(34, 2, 44, 5.00, '2025-08-08 14:02:30', 'KH_44_1754661732_2_2920', 'Credit Card', 'Hostel Fee', 'Completed', 'Description for test', '2025-08-08 14:02:13'),
(35, 2, 44, 5.00, '2025-08-08 14:34:42', 'KH_44_1754663661_2_3079', 'Credit Card', 'Hostel Fee', 'Completed', 'Description for test', '2025-08-08 14:34:23'),
(36, 2, 40, 1.00, '2025-08-08 14:37:55', 'KH_40_1754663854_2_4782', 'Mobile Money', 'Security Deposit', 'Completed', 'New test', '2025-08-08 14:37:36'),
(37, 2, 30, 10.00, '2025-08-08 15:38:33', 'KH_30_1754667494_2_3739', 'Mobile Money', 'Security Deposit', 'Completed', 'Security Deposit', '2025-08-08 15:38:16'),
(38, 2, 40, 5.00, '2025-08-08 15:43:42', 'KH_40_1754667803_2_4857', 'Mobile Money', 'Security Deposit', 'Completed', 'New test', '2025-08-08 15:43:25'),
(39, 2, 41, 5.00, '2025-08-08 15:49:36', 'KH_41_1754668156_2_8025', 'Credit Card', 'Security Deposit', 'Completed', 'New test', '2025-08-08 15:49:21'),
(40, 2, 43, 5.00, '2025-08-08 22:23:48', 'KH_43_1754691794_2_8785', 'Mobile Money', 'Hostel Fee', 'Completed', 'awesome', '2025-08-08 22:23:15'),
(41, 2, 37, 55.00, '2025-08-13 18:36:57', 'KH_37_1755110185_2_8673', 'Mobile Money', 'Maintenance Charge', 'Completed', 'maintenance', '2025-08-13 18:36:27'),
(42, 2, 55, 2.00, '2025-08-21 20:27:34', 'KH_55_1755808027_2_9284', 'Mobile Money', 'Penalty', 'Completed', 'Test fees', '2025-08-21 20:27:09'),
(43, 2, 39, 20.00, '2025-08-21 21:31:15', 'KH_39_1755811855_2_3608', 'Credit Card', 'Other', 'Completed', 'Test ', '2025-08-21 21:30:56'),
(44, 2, 56, 50.00, '2025-07-20 10:20:00', 'TXN-INV-000056', 'Cash', 'Maintenance Charge', 'Completed', NULL, '2025-08-21 22:01:22'),
(45, 2, 39, 15.70, '2025-08-25 12:33:06', 'KH_39_1756125153_2_4657', 'Credit Card', 'Other', 'Completed', 'Test ', '2025-08-25 12:32:42'),
(46, 2, 30, 53.00, '2025-08-25 12:35:04', 'KH_30_1756125284_2_8335', 'Mobile Money', 'Security Deposit', 'Completed', 'Security Deposit', '2025-08-25 12:34:45'),
(47, 13, 57, 600.00, '2025-08-26 12:35:09', 'KH_57_1756211689_13_9807', 'Credit Card', 'Hostel Fee', 'Completed', 'Room fee for 104 - Double in Hostel A', '2025-08-26 12:34:51'),
(48, 11, 27, 300.00, '2025-08-26 20:25:00', 'TXN-INV-000027', 'Cash', 'Maintenance Charge', 'Completed', NULL, '2025-08-26 20:25:46'),
(49, 2, 40, 15.00, '2025-08-27 07:59:33', 'KH_40_1756281551_2_1187', 'Mobile Money', 'Security Deposit', 'Completed', 'New test', '2025-08-27 07:59:13');

-- --------------------------------------------------------

--
-- Table structure for table `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `remember_tokens`
--

INSERT INTO `remember_tokens` (`id`, `user_id`, `token`, `expires_at`, `created_at`) VALUES
(1, 4, '23807f65d0ff7c0f2fc6c00716e2278320792ed32ea4507d8f246a68950d1461', '2025-06-15 08:20:04', '2025-05-16 06:20:04'),
(2, 4, 'b074b0d910367871ecbbde9de4bbbeaf4f77ab08e964c9f41bda223c7e81f799', '2025-06-17 12:31:02', '2025-05-18 10:31:02'),
(4, 4, '273e03bc34f5b24d67fc8c2be4be69603a2dddc3164846a5da50fd6497c9e3b3', '2025-06-17 12:34:30', '2025-05-18 10:34:30'),
(6, 4, 'a00888cf59604d52df068708bc80f2ff03df722309cdcb481da216ef0854fae3', '2025-06-21 20:23:54', '2025-05-22 18:23:54'),
(11, 4, '2a5ad8e71df5881b1bc83caf9706cfa7daf73b0e7c34fdc696f4b633dca528c6', '2025-06-22 07:10:51', '2025-05-23 05:10:51'),
(12, 4, 'b3124678fb49339c1f7ddb440628099835e0c876fdc7685d214b3f5326eb4f80', '2025-06-22 07:20:06', '2025-05-23 05:20:06'),
(13, 4, '86cb8a37a23afe8bb8da761e831412323a63aa7af520f3728206dd0e048ad688', '2025-06-22 07:23:27', '2025-05-23 05:23:27'),
(15, 4, '06ea42add670398118b130144d61784c3769190138626ee9ae0663fccb8ab368', '2025-06-22 07:26:51', '2025-05-23 05:26:51'),
(16, 4, '296275c7e74c84b2ed8fef6d8e236856e997a4e06c6bc381ecc79cd99f795712', '2025-06-23 11:33:07', '2025-05-24 09:33:07'),
(17, 4, '97f4b61105119f531c9a30f08e8300cbd9bd12df083ce3d2af1600ff1887457e', '2025-06-25 16:38:19', '2025-05-26 14:38:19'),
(18, 4, '322950a5008c43f26ebe6618ce00622ba5b9d512d47c024cf0f34bb7f2881760', '2025-06-28 15:22:48', '2025-05-29 13:22:48'),
(20, 4, '4902082ca5ded5c2216d30143c870e1b51f79000d7dbca99674aa83df474b96c', '2025-07-29 07:19:15', '2025-06-29 05:19:15'),
(21, 4, 'b79ce67ed7fb7a7d1202ba4f5cc0ad298dbeb41f8449e46a25f310bb341dd3b6', '2025-07-29 11:43:11', '2025-06-29 09:43:11'),
(22, 4, 'b7258ada921876b05102b7dfe2884bc4f5f661b4bc018d1ca1363044fb359489', '2025-07-29 13:13:13', '2025-06-29 11:13:13'),
(23, 4, '85429f2ed1a26e2920331bf08743eafc344c0f8ef52bba046b728112ae95c9c4', '2025-07-30 09:36:49', '2025-06-30 07:36:49'),
(26, 4, 'f74092c205bc9ebbdbd4d8d0e3028cddd1cc25e73ca14e51f2e3b98cad073f59', '2025-08-01 14:41:14', '2025-07-02 12:41:14'),
(27, 4, 'ceb3c790c7693ffc798b47ce5c12796d0a557c8cd39381f831439e8588626293', '2025-08-01 16:12:30', '2025-07-02 14:12:30'),
(28, 4, '8b7555f1e2074cb98c2fdfeb1e4f6c05336b62b3f7900d0520e2de370cce9c7b', '2025-08-01 21:38:25', '2025-07-02 19:38:25'),
(29, 4, '038c387626b8a2f91244d1421f708f85c1ad9a98aa172ccd8c50a3336d6f4b54', '2025-08-02 19:50:09', '2025-07-03 17:50:09'),
(30, 4, 'c6ea1bd6e01ac4993906bea72a24a35e73e86d7452bd5b0eddde4b20267233bd', '2025-08-02 20:25:22', '2025-07-03 18:25:22'),
(31, 4, 'e1156606d04528a6fbbdae88e403df7a6e885116a2a86ad39cc1f6040a9df6c3', '2025-08-03 00:12:45', '2025-07-03 22:12:45'),
(33, 4, '4445dc400c7973cf92c60838a10a2d3ea44dd9bac9db3b161d9cf97da0199f20', '2025-08-06 09:06:37', '2025-07-07 07:06:37'),
(35, 4, '272e18393b3a57a362e9a81756e01db000e5a61107caa7dde4ed5ec32f4172c5', '2025-08-17 06:09:32', '2025-07-18 04:09:32'),
(38, 4, '66b7152dd0464d1e5c346d8c32970744d2434967037bda98becaa0fa53914083', '2025-08-20 16:07:48', '2025-07-21 14:07:48'),
(41, 4, '07b558b8d6c2651f1c451ac5c66726d993d2f71ca7a19d5e47d1a1c4a9de5066', '2025-09-02 11:26:19', '2025-08-03 09:26:19');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `hostel_id` int(11) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `building` varchar(100) NOT NULL,
  `floor` int(11) NOT NULL,
  `room_type` enum('Single','Double','Triple','Quad') NOT NULL,
  `capacity` int(11) NOT NULL,
  `current_occupancy` int(11) NOT NULL DEFAULT 0,
  `features` text DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('Fully Occupied','Partially Occupied','Vacant','Under Maintenance','Reserved') NOT NULL DEFAULT 'Vacant'
) ;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `hostel_id`, `room_number`, `building`, `floor`, `room_type`, `capacity`, `current_occupancy`, `features`, `amount`, `status`) VALUES
(2, 6, '312', 'Hostel B', 3, 'Single', 1, 1, 'Private bathroom, Smart TV (4K), Mini fridge, Premium bedding, Soundproofing, Air-conditioning, Desk with ergonomic chair, High-speed Wi-Fi', 5000.00, 'Fully Occupied'),
(3, 6, '314', 'Hostel B', 3, 'Single', 1, 1, 'Private bathroom, Smart TV (4K), Mini fridge, Premium bedding, Soundproofing, Air-conditioning, Desk with ergonomic chair, High-speed Wi-Fi', 5000.00, 'Fully Occupied'),
(4, 5, '104', 'Hostel A', 1, 'Double', 2, 2, 'Shared bathroom, Smart TV, Mini fridge, Air-conditioning, Desk, High-speed Wi-Fi', 3500.00, 'Fully Occupied'),
(5, 5, '311', 'Hostel A', 3, 'Double', 2, 0, 'Shared bathroom, Smart TV, Mini fridge, Air-conditioning, Desk, High-speed Wi-Fi', 3500.00, 'Vacant'),
(6, 6, '209', 'Hostel B', 2, 'Double', 2, 0, 'Shared bathroom, Smart TV, Mini fridge, Air-conditioning, Desk, High-speed Wi-Fi', 3500.00, 'Vacant'),
(7, 6, '315', 'Hostel B', 3, 'Double', 2, 0, 'Shared bathroom, Smart TV, Mini fridge, Air-conditioning, Desk, High-speed Wi-Fi', 3500.00, 'Vacant'),
(8, 6, '207', 'Hostel B', 2, 'Triple', 3, 0, 'Shared bathroom, LED TV, Wi-Fi, Desk, Air-conditioning', 2500.00, 'Under Maintenance'),
(10, 5, '316', 'Hostel A', 3, 'Triple', 3, 0, 'Shared bathroom, LED TV, Wi-Fi, Desk, Air-conditioning', 2500.00, 'Vacant'),
(11, 6, '208', 'Hostel B', 2, 'Quad', 4, 4, 'Wi-Fi, Shared bathroom, Basic furniture (bed, desk, chair)', 1000.00, 'Fully Occupied'),
(12, 5, '313', 'Hostel A', 3, 'Quad', 4, 1, 'Wi-Fi, Shared bathroom, Basic furniture (bed, desk, chair)', 1000.00, 'Partially Occupied'),
(13, 7, '500', 'Vikings', 5, 'Double', 2, 0, 'Shared bathroom, LED TV, Wi-Fi, Desk, Air-conditioning', 4000.00, 'Vacant'),
(14, 7, '110', 'Vikings', 2, 'Quad', 4, 1, 'Shared bathroom, LED TV, Wi-Fi, Desk, Air-conditioning', 1000.00, 'Partially Occupied'),
(15, 7, '300', 'Vikings', 3, 'Single', 1, 1, 'Private bathroom, Smart TV (4K), Mini fridge, Premium bedding, Soundproofing, Air-conditioning, Desk with ergonomic chair, High-speed Wi-Fi', 4500.00, 'Fully Occupied'),
(16, 5, '111', 'Hostel A', 2, 'Double', 2, 0, 'Private bathroom, Smart TV (4K), Mini fridge, Premium bedding, Soundproofing, Air-conditioning, Desk with ergonomic chair, High-speed Wi-Fi', 2500.00, 'Vacant'),
(17, 7, '112', 'Vikings', 2, 'Single', 1, 1, 'Private bathroom, Smart TV (4K), Mini fridge, Premium bedding, Soundproofing, Air-conditioning, Desk with ergonomic chair, High-speed Wi-Fi', 4500.00, 'Fully Occupied'),
(19, 7, '115', 'Vikings', 2, 'Single', 1, 0, 'Private bathroom, Smart TV (4K), Mini fridge, Premium bedding, Soundproofing, Air-conditioning, Desk with ergonomic chair, High-speed Wi-Fi', 5000.00, 'Vacant'),
(20, 7, '502', 'Vikings', 4, 'Double', 2, 0, 'Private bathroom, Smart TV (4K), Mini fridge, Premium bedding, Soundproofing, Air-conditioning, Balcony, Desk with ergonomic chair, High-speed Wi-Fi', 3500.00, 'Under Maintenance'),
(21, 7, '109', 'Vikings', 2, 'Double', 2, 2, 'Private bathroom, Smart TV (4K), Mini fridge, Premium bedding, Soundproofing, Air-conditioning, Desk with ergonomic chair, High-speed Wi-Fi', 3000.00, 'Fully Occupied'),
(22, 7, '405', 'Vikings', 3, 'Quad', 4, 0, 'air-conditioning, cupboard, WiFi, bathroom ', 1000.00, 'Vacant'),
(23, 4, '101', 'Diamond Jubilee ', 1, 'Single', 1, 0, 'WiFi, Study desk, Bathroom, Air-conditioner ', 5000.00, 'Vacant');

--
-- Triggers `rooms`
--
DELIMITER $$
CREATE TRIGGER `update_room_status` BEFORE UPDATE ON `rooms` FOR EACH ROW BEGIN
    IF NEW.status NOT IN ('Under Maintenance', 'Reserved') THEN
        IF NEW.current_occupancy >= NEW.capacity THEN
            SET NEW.status = 'Fully Occupied';
        ELSEIF NEW.current_occupancy > 0 THEN
            SET NEW.status = 'Partially Occupied';
        ELSE
            SET NEW.status = 'Vacant';
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `date_of_birth` date NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `emergency_contact_name` varchar(100) NOT NULL,
  `emergency_contact_number` varchar(20) NOT NULL,
  `health_condition` text DEFAULT NULL,
  `enrollment_date` date NOT NULL,
  `resident_status` enum('Active','Inactive','Suspended','Graduated','Withdrawn') DEFAULT 'Inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`, `first_name`, `last_name`, `gender`, `date_of_birth`, `phone_number`, `address`, `emergency_contact_name`, `emergency_contact_number`, `health_condition`, `enrollment_date`, `resident_status`) VALUES
(2, 2, 'Bright', 'Amoah', 'Male', '2002-08-21', '+233577370388', 'G125/3 Kowe Jornaa, La-Accra', 'Samuel Amoah', '+233549684848', 'Nut Allergy, Pollen Allergy, bee allergy', '2025-04-19', 'Active'),
(3, 3, 'Jude', 'Amoah', 'Male', '2002-08-21', '+233549684848', 'G125/3 Kowe Jornaa La-Accra', 'Bright Amoah', '+233552223232', 'Asthma, Pollen Allergy', '2025-04-19', 'Active'),
(8, 17, 'James', 'Malone', 'Male', '2004-09-15', '+233549684848', 'G125/3 La Accra', 'Samuel Amoah', '+233549684848', 'Pollen allergy', '2025-04-22', 'Active'),
(11, 20, 'Marie', 'Lotte', 'Female', '2002-08-21', '+233577370388', 'G125/3 Kowe Jornaa , La', 'Samuel Amoah', '+233549684848', 'Nut Allergy', '2025-05-21', 'Active'),
(12, 22, 'king', 'Kong', 'Male', '2002-08-21', '+233577370388', 'G215/3 Kowe Jornaa , La', 'Samuel Amoah', '+233549684848', '', '2025-06-28', 'Inactive'),
(13, 24, 'Marie', 'Young', 'Female', '2005-06-24', '+233577370388', '12 Gibson lane, Accra', 'Samuel Amoah', '+233549684848', 'Dust allergy', '2025-08-24', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Student','Admin') NOT NULL DEFAULT 'Student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_email_verified` tinyint(1) DEFAULT 0,
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`, `created_at`, `is_email_verified`, `last_login`) VALUES
(2, 'Bright Amoah', 'brghtmalone@gmail.com', '$2y$10$SyuiRpBgHkl9SSsfCgyN4O8owYctzfj9qnmtNhIWvN.Ts3MEwK8Ry', 'Student', '2025-04-19 11:03:40', 1, '2025-09-09 11:19:53'),
(3, 'Jude Amoah', 'kingshostelmgt@gmail.com', '$2y$10$jLPuX.5b1wc7deHkRe39neUejdoPD/TpAuLHFYwRSibmJNWV4OPLy', 'Student', '2025-04-19 11:12:16', 1, '2025-08-24 13:18:55'),
(4, 'Bright Kweku Amoah', 'brightphenomenalamoah@gmail.com', '$2y$10$lIAyqmmy84PdNTkRygFqK.Wmb0dDLigBICpyhh5d7wsZB8OCzyLOO', 'Admin', '2025-04-19 11:25:57', 1, '2025-09-09 10:54:36'),
(8, 'Joyce Armah', 'bkamaoah02@gmail.com', '$2y$10$fnvhXp3sAMUOUSgpDZo1/eEq/NTlQrznkMXg.WmJtpHZ.KdCToDou', 'Admin', '2025-04-21 08:05:27', 1, '2025-08-25 18:55:58'),
(17, 'Bright Malone', 'brightphenomenalamoah+3@gmail.com', '$2y$10$912y2VQ6Wc2KpbQVvTe8ReV0PUyglosrYCT1ndjBVUU8PWVs9PEEm', 'Student', '2025-04-21 17:51:49', 1, '2025-08-24 10:49:37'),
(20, 'Marie Lotte', 'lottemarie77+1@gmail.com', '$2y$10$mQlO3VUa1Ongbd4vsWaE4.sEsrQYLXxad0ONOitp0eCY8zBzitzIS', 'Student', '2025-05-21 16:22:03', 1, '2025-08-24 09:24:13'),
(22, 'king Kong', 'brghtmalone+2@gmail.com', '$2y$10$qYBigmTDiMF9SBotmLHh1Og8K0R1ueY28v7I2TlTw1ndjxirmYkau', 'Student', '2025-06-28 05:28:31', 1, '2025-08-03 16:03:40'),
(23, 'Joyce Armah', 'kingshostelmgt+2@gmail.com', '$2y$10$StIWI.LL5jS..KMQVuNgjuTDVqWeDTY1swpl9qgS5JFbBQTh95t2W', 'Admin', '2025-08-21 21:51:20', 1, '2025-08-27 16:13:53'),
(24, 'Marie Young', 'kingshostelmgt+3@gmail.com', '$2y$10$/exJ3h9hYS1WX3jDJCb2h.QSY2YXcMX1hurdHpTL/AsWylGdhg5QS', 'Student', '2025-08-24 11:44:24', 1, '2025-08-27 16:13:41');

-- --------------------------------------------------------

--
-- Table structure for table `verification_codes`
--

CREATE TABLE `verification_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `verification_codes`
--

INSERT INTO `verification_codes` (`id`, `user_id`, `code`, `expires_at`, `created_at`) VALUES
(21, 23, '642244', '2025-08-24 14:09:59', '2025-08-24 11:39:59');

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE `visitors` (
  `visitor_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `visitor_name` varchar(100) NOT NULL,
  `relation` varchar(50) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `visit_date` date NOT NULL,
  `status` enum('Pending','Approved','Checked-In','Checked-Out','Cancelled','Denied') NOT NULL DEFAULT 'Pending',
  `purpose` text NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `visitors`
--

INSERT INTO `visitors` (`visitor_id`, `student_id`, `visitor_name`, `relation`, `phone_number`, `visit_date`, `status`, `purpose`, `registered_at`) VALUES
(2, 2, 'Elizabeth Osei', 'Sister', '+233501234529', '2025-04-16', 'Checked-In', 'Family visit', '2025-04-19 11:36:47'),
(3, 3, 'Kwabena Asante', 'Cousin', '+233501234530', '2025-04-17', 'Checked-Out', 'Casual visit', '2025-04-19 11:36:47'),
(4, 2, 'Sophia Mensah', 'Friend', '+233501234531', '2025-04-18', 'Checked-Out', 'Study group', '2025-04-19 11:36:47'),
(6, 3, 'Abigail Yeboah', 'Aunt', '+233501234533', '2025-04-20', 'Checked-Out', 'Family visit', '2025-04-19 11:36:47'),
(8, 2, 'Rebecca Ansah', 'Mother', '+233501234535', '2025-04-22', 'Checked-Out', 'Family visit', '2025-04-19 11:36:47'),
(9, 3, 'Thomas Agyeman', 'Friend', '+233501234536', '2025-04-23', 'Checked-In', 'Study session', '2025-04-19 11:36:47'),
(11, 2, 'Peter Amankwah', 'Cousin', '+233501234538', '2025-04-25', 'Cancelled', 'Cancelled visit', '2025-04-19 11:36:47'),
(12, 3, 'Grace Adjei', 'Friend', '+233501234539', '2025-04-26', 'Checked-In', 'Casual visit', '2025-04-19 11:36:47'),
(14, 2, 'Mary Nkrumah', 'Aunt', '+233501234541', '2025-04-28', 'Checked-In', 'Family visit', '2025-04-19 11:36:47'),
(15, 3, 'Joseph Appiah', 'Friend', '+233501234542', '2025-04-29', 'Denied', 'No prior notice', '2025-04-19 11:36:47'),
(17, 2, 'David Asare', 'Friend', '+233501234544', '2025-05-01', 'Pending', 'Study group', '2025-04-19 11:36:47'),
(18, 3, 'Linda Adu', 'Sister', '+233501234545', '2025-05-02', 'Approved', 'Family visit', '2025-04-19 11:36:47'),
(20, 2, 'Patricia Darko', 'Cousin', '+233501234547', '2025-05-04', 'Pending', 'Dropping off items', '2025-04-19 11:36:47'),
(21, 3, 'Rose Mensah', 'Friend', '+233501234548', '2025-05-05', 'Checked-In', 'Study session', '2025-04-19 11:36:47'),
(26, 2, 'Edward Bonsu', 'Friend', '+233501234553', '2025-08-25', 'Checked-Out', 'Casual visit', '2025-04-19 11:36:47'),
(27, 3, 'Lydia Boateng', 'Aunt', '+233501234554', '2025-05-11', 'Checked-Out', 'Family visit', '2025-04-19 11:36:47'),
(29, 2, 'Janet Agyeman', 'Sister', '+233501234556', '2025-10-10', 'Approved', 'Family visit', '2025-04-19 11:36:47'),
(32, 2, 'Cecilia Annan', 'Mother', '+233501234559', '2025-05-16', 'Checked-In', 'Family visit', '2025-04-19 11:36:47'),
(33, 3, 'Mark Adu', 'Friend', '+233501234560', '2025-05-17', 'Approved', 'Study group', '2025-04-19 11:36:47'),
(35, 2, 'George Kyei', 'Brother', '+233501234562', '2025-05-19', 'Approved', 'Family visit', '2025-04-19 11:36:47'),
(36, 3, 'Rebecca Otoo', 'Aunt', '+233501234563', '2025-05-20', 'Checked-In', 'Family visit', '2025-04-19 11:36:47'),
(38, 2, 'Ellen Amankwah', 'Sister', '+233501234565', '2025-05-22', 'Checked-Out', 'Family visit', '2025-04-19 11:36:47'),
(39, 3, 'Francis Boadu', 'Friend', '+233501234566', '2025-05-23', 'Pending', 'Study session', '2025-04-19 11:36:47'),
(41, 2, 'Timothy Nartey', 'Friend', '+233501234568', '2025-05-25', 'Checked-Out', 'Casual visit', '2025-04-19 11:36:47'),
(42, 3, 'Veronica Asiedu', 'Cousin', '+233501234569', '2025-05-26', 'Denied', 'No prior notice', '2025-04-19 11:36:47'),
(44, 2, 'Sandra Owusu', 'Friend', '+233501234571', '2025-05-28', 'Checked-In', 'Study group', '2025-04-19 11:36:47'),
(45, 3, 'Lawrence Agyei', 'Friend', '+233501234572', '2025-05-29', 'Approved', 'Casual visit', '2025-04-19 11:36:47'),
(48, 3, 'Kofi Mensah', 'Cousin', '+233501234575', '2025-06-01', 'Checked-In', 'Family visit', '2025-04-19 11:36:47'),
(50, 2, 'Yaw Boateng', 'Friend', '+233501234577', '2025-06-03', 'Checked-Out', 'Casual visit', '2025-04-19 11:36:47'),
(51, 3, 'Akosua Adjei', 'Sister', '+233501234578', '2025-06-04', 'Pending', 'Family visit', '2025-04-19 11:36:47'),
(53, 2, 'Adwoa Nkrumah', 'Mother', '+233501234580', '2025-06-06', 'Checked-In', 'Family visit', '2025-04-19 11:36:47'),
(54, 3, 'Kwesi Appiah', 'Brother', '+233501234581', '2025-06-07', 'Approved', 'Family visit', '2025-04-19 11:36:47'),
(56, 2, 'Kwame Osei', 'Cousin', '+233501234583', '2025-06-09', 'Pending', 'Casual visit', '2025-04-19 11:36:47'),
(57, 3, 'Abena Yeboah', 'Friend', '+233501234584', '2025-06-10', 'Checked-In', 'Study session', '2025-04-19 11:36:47'),
(59, 2, 'Ama Boateng', 'Aunt', '+233501234586', '2025-06-12', 'Checked-Out', 'Family visit', '2025-04-19 11:36:47'),
(60, 3, 'Yaw Asante', 'Friend', '+233501234587', '2025-06-13', 'Approved', 'Casual visit', '2025-04-19 11:36:47'),
(62, 2, 'Kwesi Nkrumah', 'Friend', '+233501234589', '2025-06-15', 'Checked-Out', 'Study group', '2025-04-19 11:36:47'),
(63, 3, 'Adwoa Appiah', 'Mother', '+233501234590', '2025-06-16', 'Denied', 'No prior notice', '2025-04-19 11:36:47'),
(65, 2, 'Efua Adu', 'Sister', '+233501234592', '2025-06-18', 'Checked-In', 'Family visit', '2025-04-19 11:36:47'),
(66, 3, 'Kwame Boateng', 'Brother', '+233501234593', '2025-06-19', 'Approved', 'Family visit', '2025-04-19 11:36:47'),
(69, 3, 'Ama Yeboah', 'Aunt', '+233501234596', '2025-06-22', 'Checked-In', 'Family visit', '2025-04-19 11:36:47'),
(71, 2, 'Akosua Appiah', 'Sister', '+233501234598', '2025-06-24', 'Checked-Out', 'Family visit', '2025-04-19 11:36:47'),
(72, 3, 'Kwesi Adu', 'Friend', '+233501234599', '2025-06-25', 'Pending', 'Study group', '2025-04-19 11:36:47'),
(75, 3, 'Efua Mensah', 'Friend', '+233501234602', '2025-06-28', 'Checked-Out', 'Casual visit', '2025-04-19 11:36:47'),
(77, 2, 'Abena Adu', 'Friend', '+233501234604', '2025-06-30', 'Checked-In', 'Study session', '2025-04-19 11:36:47'),
(78, 3, 'Kofi Boateng', 'Brother', '+233501234605', '2025-07-01', 'Approved', 'Family visit', '2025-04-19 11:36:47'),
(80, 2, 'Yaw Mensah', 'Friend', '+233501234607', '2025-08-27', 'Approved', 'Casual visit', '2025-04-19 11:36:47'),
(81, 3, 'Akosua Nkrumah', 'Sister', '+233501234608', '2025-07-04', 'Checked-In', 'Family visit', '2025-04-19 11:36:47'),
(83, 2, 'Adwoa Adu', 'Mother', '+233501234610', '2025-07-06', 'Checked-Out', 'Family visit', '2025-04-19 11:36:47'),
(84, 3, 'Kojo Boateng', 'Brother', '+233501234611', '2025-07-07', 'Checked-Out', 'Family visit', '2025-04-19 11:36:47'),
(87, 3, 'Abena Nkrumah', 'Friend', '+233501234614', '2025-07-10', 'Checked-Out', 'Casual visit', '2025-04-19 11:36:47'),
(90, 3, 'Yaw Boateng', 'Friend', '+233501234617', '2025-07-13', 'Checked-In', 'Study session', '2025-04-19 11:36:47'),
(96, 3, 'Kwame Adu', 'Cousin', '+233501234623', '2025-07-19', 'Checked-In', 'Casual visit', '2025-04-19 11:36:47'),
(99, 3, 'Ama Appiah', 'Sister', '+233501234626', '2025-07-22', 'Checked-Out', 'Family visit', '2025-04-19 11:36:47'),
(101, 2, 'Bright Amoah', 'Brother', '+233549684848', '2025-04-24', 'Checked-Out', 'Group Studies', '2025-04-22 13:43:31'),
(105, 2, 'Amoah Elvis', 'Brother', '+233549684848', '2025-05-01', 'Pending', 'Family visit', '2025-04-25 18:59:20'),
(106, 2, 'Bright Amoah', 'Friend', '+233549684848', '2025-04-30', 'Pending', 'Group Studies', '2025-04-27 12:02:11'),
(107, 8, 'Bright Amoah', 'Brother', '+233549684848', '2025-09-18', 'Approved', 'Group Discussions ', '2025-04-27 13:51:52'),
(108, 2, 'Bright Amoah', 'Brother', '+233549684848', '2025-04-27', 'Checked-In', 'Casual visit', '2025-04-27 14:17:06'),
(109, 8, 'Bright Amoah', 'Brother', '+233549684848', '2025-08-28', 'Denied', 'Group studies', '2025-05-21 14:38:16'),
(110, 11, 'Bright Amoah', 'Brother', '+233549684848', '2025-08-21', 'Checked-In', 'Birthday party 🎉 ', '2025-08-05 15:29:18'),
(111, 2, 'Bright Amoah', 'Brother', '+233549684848', '2025-08-22', 'Cancelled', 'Exam Prep', '2025-08-21 21:21:35'),
(112, 13, 'Amoah Elvis', 'Friend', '+233549684848', '2025-08-30', 'Approved', 'Study for exam', '2025-08-24 19:23:45'),
(113, 2, 'Kofi Bonsu', 'Course mate', '+233549684848', '2025-09-05', 'Approved', 'Project work preparation ', '2025-08-27 08:52:32');

-- --------------------------------------------------------

--
-- Table structure for table `visitor_logs`
--

CREATE TABLE `visitor_logs` (
  `log_id` int(11) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  `check_in_time` datetime NOT NULL,
  `check_out_time` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `visitor_logs`
--

INSERT INTO `visitor_logs` (`log_id`, `visitor_id`, `check_in_time`, `check_out_time`, `created_at`) VALUES
(1, 90, '2025-04-22 15:32:26', NULL, '2025-04-22 13:32:26'),
(2, 84, '2025-04-22 15:32:52', '2025-04-22 15:33:22', '2025-04-22 13:32:52'),
(3, 2, '2025-04-16 09:00:00', NULL, '2025-04-22 13:34:06'),
(4, 3, '2025-04-17 14:00:00', '2025-04-17 16:00:00', '2025-04-22 13:34:06'),
(5, 4, '2025-04-18 11:00:00', '2025-04-23 15:42:24', '2025-04-22 13:34:06'),
(6, 6, '2025-04-20 10:30:00', '2025-04-20 12:30:00', '2025-04-22 13:34:06'),
(7, 8, '2025-04-22 09:00:00', '2025-04-22 11:00:00', '2025-04-22 13:34:06'),
(8, 9, '2025-04-23 15:00:00', '2025-04-23 15:47:13', '2025-04-22 13:34:06'),
(9, 12, '2025-04-26 12:00:00', '2025-04-26 14:00:00', '2025-04-22 13:34:06'),
(10, 14, '2025-04-28 10:00:00', NULL, '2025-04-22 13:34:06'),
(11, 18, '2025-05-02 14:00:00', NULL, '2025-04-22 13:34:06'),
(12, 21, '2025-05-05 09:00:00', NULL, '2025-04-22 13:34:06'),
(13, 27, '2025-05-11 11:00:00', '2025-05-11 13:00:00', '2025-04-22 13:34:06'),
(14, 29, '2025-05-13 14:00:00', NULL, '2025-04-22 13:34:06'),
(15, 32, '2025-05-16 09:30:00', NULL, '2025-04-22 13:34:06'),
(16, 36, '2025-05-20 10:00:00', NULL, '2025-04-22 13:34:06'),
(17, 38, '2025-05-22 11:00:00', '2025-05-22 13:00:00', '2025-04-22 13:34:06'),
(18, 41, '2025-05-25 15:00:00', '2025-05-25 17:00:00', '2025-04-22 13:34:06'),
(19, 44, '2025-05-28 09:00:00', NULL, '2025-04-22 13:34:06'),
(20, 48, '2025-06-01 14:00:00', NULL, '2025-04-22 13:34:06'),
(21, 50, '2025-06-03 15:00:00', '2025-06-03 17:00:00', '2025-04-22 13:34:06'),
(22, 53, '2025-06-06 09:00:00', NULL, '2025-04-22 13:34:06'),
(23, 57, '2025-06-10 14:00:00', NULL, '2025-04-22 13:34:06'),
(24, 59, '2025-06-12 11:00:00', '2025-06-12 13:00:00', '2025-04-22 13:34:06'),
(25, 62, '2025-06-15 09:00:00', '2025-06-15 11:00:00', '2025-04-22 13:34:06'),
(26, 65, '2025-06-18 14:00:00', NULL, '2025-04-22 13:34:06'),
(27, 69, '2025-06-22 11:00:00', NULL, '2025-04-22 13:34:06'),
(28, 71, '2025-06-24 15:00:00', '2025-06-24 17:00:00', '2025-04-22 13:34:06'),
(29, 75, '2025-06-28 14:00:00', '2025-06-28 16:00:00', '2025-04-22 13:34:06'),
(30, 77, '2025-06-30 11:00:00', NULL, '2025-04-22 13:34:06'),
(31, 81, '2025-07-04 15:00:00', NULL, '2025-04-22 13:34:06'),
(32, 83, '2025-07-06 09:00:00', '2025-07-06 11:00:00', '2025-04-22 13:34:06'),
(33, 87, '2025-07-10 11:00:00', '2025-07-10 13:00:00', '2025-04-22 13:34:06'),
(37, 99, '2025-07-22 09:00:00', '2025-07-22 11:00:00', '2025-04-22 13:34:06'),
(66, 84, '2025-04-22 15:36:45', NULL, '2025-04-22 13:36:45'),
(67, 84, '2025-04-22 15:36:54', '2025-04-22 15:37:05', '2025-04-22 13:36:54'),
(68, 101, '2025-04-22 15:45:46', '2025-04-22 16:45:08', '2025-04-22 13:45:46'),
(70, 96, '2025-04-22 19:55:00', NULL, '2025-04-22 17:55:00'),
(71, 99, '2025-04-22 21:26:29', '2025-04-22 21:26:52', '2025-04-22 19:26:29'),
(72, 99, '2025-04-22 21:29:55', '2025-04-22 19:34:38', '2025-04-22 19:29:55'),
(73, 99, '2025-04-22 19:34:50', '2025-04-23 16:09:58', '2025-04-22 19:34:50'),
(74, 2, '2025-04-23 15:42:46', NULL, '2025-04-23 15:42:46'),
(75, 101, '2025-04-23 15:46:24', '2025-04-23 19:32:24', '2025-04-23 15:46:24'),
(76, 9, '2025-04-23 15:47:41', '2025-04-23 16:10:32', '2025-04-23 15:47:41'),
(77, 9, '2025-04-23 16:10:39', NULL, '2025-04-23 16:10:39'),
(78, 96, '2025-04-23 18:42:13', NULL, '2025-04-23 18:42:13'),
(79, 12, '2025-04-26 10:03:08', NULL, '2025-04-26 10:03:08'),
(80, 108, '2025-04-27 14:17:56', '2025-04-27 14:19:50', '2025-04-27 14:17:56'),
(81, 108, '2025-04-27 14:20:15', NULL, '2025-04-27 14:20:15'),
(82, 110, '2025-08-21 21:43:24', '2025-08-21 21:44:13', '2025-08-21 21:43:24'),
(83, 110, '2025-08-21 21:44:34', NULL, '2025-08-21 21:44:34'),
(84, 26, '2025-08-25 20:25:09', '2025-08-25 20:25:40', '2025-08-25 20:25:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_admin_user_id` (`user_id`),
  ADD KEY `idx_admin_access_level` (`access_level`),
  ADD KEY `idx_admins_hostel_id` (`hostel_id`);

--
-- Indexes for table `allocations`
--
ALTER TABLE `allocations`
  ADD PRIMARY KEY (`allocation_id`),
  ADD UNIQUE KEY `unique_active_allocation` (`student_id`,`status`),
  ADD KEY `idx_allocation_student_id` (`student_id`),
  ADD KEY `idx_allocation_room_id` (`room_id`),
  ADD KEY `idx_allocation_status` (`status`),
  ADD KEY `idx_allocation_dates` (`start_date`,`end_date`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_id`),
  ADD KEY `idx_announcement_posted_by` (`posted_by`),
  ADD KEY `idx_announcement_date_posted` (`date_posted`),
  ADD KEY `idx_announcement_target_audience` (`target_audience`),
  ADD KEY `idx_announcement_is_active` (`is_read`);

--
-- Indexes for table `announcement_reads`
--
ALTER TABLE `announcement_reads`
  ADD PRIMARY KEY (`read_id`),
  ADD UNIQUE KEY `announcement_id` (`announcement_id`,`student_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `announcement_specific_targets`
--
ALTER TABLE `announcement_specific_targets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcement_id` (`announcement_id`);

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`billing_id`),
  ADD KEY `idx_billing_student_id` (`student_id`),
  ADD KEY `idx_billing_allocation_id` (`allocation_id`),
  ADD KEY `idx_billing_status` (`status`),
  ADD KEY `idx_billing_date_due` (`date_due`),
  ADD KEY `idx_billing_hostel_id` (`hostel_id`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`complaint_id`),
  ADD KEY `resolved_by` (`resolved_by`),
  ADD KEY `idx_complaint_student_id` (`student_id`),
  ADD KEY `idx_complaint_room_id` (`room_id`),
  ADD KEY `idx_complaint_status` (`status`),
  ADD KEY `idx_complaint_priority` (`priority`),
  ADD KEY `idx_complaint_type` (`complaint_type`),
  ADD KEY `idx_complaint_submitted_at` (`submitted_at`),
  ADD KEY `idx_complaints_hostel_id` (`hostel_id`);

--
-- Indexes for table `complaint_responses`
--
ALTER TABLE `complaint_responses`
  ADD PRIMARY KEY (`response_id`),
  ADD KEY `idx_compl_resp_complaint_id` (`complaint_id`),
  ADD KEY `idx_compl_resp_user_id` (`admin_id`);

--
-- Indexes for table `disciplinary_records`
--
ALTER TABLE `disciplinary_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `reported_by` (`reported_by`),
  ADD KEY `idx_disc_student_id` (`student_id`),
  ADD KEY `idx_disc_status` (`status`),
  ADD KEY `idx_disc_violation_type` (`violation_type`),
  ADD KEY `idx_disc_severity` (`severity`),
  ADD KEY `idx_disc_date_reported` (`date_reported`);

--
-- Indexes for table `hostels`
--
ALTER TABLE `hostels`
  ADD PRIMARY KEY (`hostel_id`),
  ADD UNIQUE KEY `hostel_name` (`hostel_name`),
  ADD UNIQUE KEY `hostel_code` (`hostel_code`),
  ADD KEY `manager_id` (`manager_id`);

--
-- Indexes for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `idx_maint_student_id` (`student_id`),
  ADD KEY `idx_maint_room_id` (`room_id`),
  ADD KEY `idx_maint_status` (`status`),
  ADD KEY `idx_maint_priority` (`priority`),
  ADD KEY `idx_maint_request_date` (`request_date`),
  ADD KEY `idx_maintenance_requests_hostel_id` (`hostel_id`);

--
-- Indexes for table `maintenance_responses`
--
ALTER TABLE `maintenance_responses`
  ADD PRIMARY KEY (`response_id`),
  ADD KEY `idx_maint_resp_request_id` (`request_id`),
  ADD KEY `idx_maint_resp_user_id` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `idx_payment_student_id` (`student_id`),
  ADD KEY `idx_payment_billing_id` (`billing_id`),
  ADD KEY `idx_payment_status` (`status`),
  ADD KEY `idx_payment_purpose` (`purpose`),
  ADD KEY `idx_payment_transaction_reference` (`transaction_reference`),
  ADD KEY `idx_payment_date` (`payment_date`);

--
-- Indexes for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `unique_room` (`building`,`room_number`),
  ADD KEY `idx_building` (`building`),
  ADD KEY `idx_floor` (`floor`),
  ADD KEY `idx_room_type` (`room_type`),
  ADD KEY `idx_room_status` (`status`),
  ADD KEY `idx_room_number` (`room_number`),
  ADD KEY `idx_rooms_hostel_id` (`hostel_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_student_user_id` (`user_id`),
  ADD KEY `idx_student_resident_status` (`resident_status`),
  ADD KEY `idx_student_last_name` (`last_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `verification_codes`
--
ALTER TABLE `verification_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`visitor_id`),
  ADD KEY `idx_visitor_student_id` (`student_id`),
  ADD KEY `idx_visitor_status` (`status`),
  ADD KEY `idx_visitor_visit_date` (`visit_date`),
  ADD KEY `idx_visitor_phone` (`phone_number`);

--
-- Indexes for table `visitor_logs`
--
ALTER TABLE `visitor_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `visitor_id` (`visitor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `allocations`
--
ALTER TABLE `allocations`
  MODIFY `allocation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `announcement_reads`
--
ALTER TABLE `announcement_reads`
  MODIFY `read_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `announcement_specific_targets`
--
ALTER TABLE `announcement_specific_targets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `billing_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `complaint_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `complaint_responses`
--
ALTER TABLE `complaint_responses`
  MODIFY `response_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `disciplinary_records`
--
ALTER TABLE `disciplinary_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hostels`
--
ALTER TABLE `hostels`
  MODIFY `hostel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `maintenance_responses`
--
ALTER TABLE `maintenance_responses`
  MODIFY `response_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `verification_codes`
--
ALTER TABLE `verification_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `visitor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `visitor_logs`
--
ALTER TABLE `visitor_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `admins_ibfk_2` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`hostel_id`) ON DELETE SET NULL;

--
-- Constraints for table `allocations`
--
ALTER TABLE `allocations`
  ADD CONSTRAINT `allocations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `allocations_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE;

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`posted_by`) REFERENCES `admins` (`admin_id`) ON DELETE CASCADE;

--
-- Constraints for table `announcement_reads`
--
ALTER TABLE `announcement_reads`
  ADD CONSTRAINT `announcement_reads_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`announcement_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `announcement_reads_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `announcement_specific_targets`
--
ALTER TABLE `announcement_specific_targets`
  ADD CONSTRAINT `announcement_specific_targets_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`announcement_id`) ON DELETE CASCADE;

--
-- Constraints for table `billing`
--
ALTER TABLE `billing`
  ADD CONSTRAINT `billing_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `billing_ibfk_2` FOREIGN KEY (`allocation_id`) REFERENCES `allocations` (`allocation_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `billing_ibfk_3` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`hostel_id`) ON DELETE SET NULL;

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `complaints_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `complaints_ibfk_3` FOREIGN KEY (`resolved_by`) REFERENCES `admins` (`admin_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `complaints_ibfk_4` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`hostel_id`);

--
-- Constraints for table `complaint_responses`
--
ALTER TABLE `complaint_responses`
  ADD CONSTRAINT `complaint_responses_ibfk_1` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`complaint_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `complaint_responses_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`) ON DELETE CASCADE;

--
-- Constraints for table `disciplinary_records`
--
ALTER TABLE `disciplinary_records`
  ADD CONSTRAINT `disciplinary_records_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `disciplinary_records_ibfk_2` FOREIGN KEY (`reported_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `hostels`
--
ALTER TABLE `hostels`
  ADD CONSTRAINT `hostels_ibfk_1` FOREIGN KEY (`manager_id`) REFERENCES `admins` (`admin_id`) ON DELETE SET NULL;

--
-- Constraints for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD CONSTRAINT `maintenance_requests_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `maintenance_requests_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `maintenance_requests_ibfk_3` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`hostel_id`);

--
-- Constraints for table `maintenance_responses`
--
ALTER TABLE `maintenance_responses`
  ADD CONSTRAINT `maintenance_responses_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `maintenance_requests` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `maintenance_responses_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD CONSTRAINT `password_reset_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payment_billing` FOREIGN KEY (`billing_id`) REFERENCES `billing` (`billing_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`hostel_id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `verification_codes`
--
ALTER TABLE `verification_codes`
  ADD CONSTRAINT `verification_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `visitors`
--
ALTER TABLE `visitors`
  ADD CONSTRAINT `visitors_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `visitor_logs`
--
ALTER TABLE `visitor_logs`
  ADD CONSTRAINT `visitor_logs_ibfk_1` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`visitor_id`) ON DELETE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `check_overdue_bills` ON SCHEDULE EVERY 1 DAY STARTS '2025-05-22 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    UPDATE billing
    SET 
        status = 'Overdue',
        late_fee = CASE 
            WHEN late_fee = 0 THEN amount * 0.05 -- Add 5% late fee
            ELSE late_fee -- Keep existing late fee
        END
    WHERE 
        status IN ('Unpaid', 'Partially Paid') 
        AND date_due < NOW() 
        AND status != 'Cancelled';
END$$

CREATE DEFINER=`root`@`localhost` EVENT `update_overdue_bills` ON SCHEDULE EVERY 1 HOUR STARTS '2025-08-07 12:43:56' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE billing
      SET status = 'Overdue'
      WHERE date_due < NOW()
        AND paid_amount < amount
        AND status = 'Unpaid'$$

CREATE DEFINER=`root`@`localhost` EVENT `add_monthly_late_fees` ON SCHEDULE EVERY 1 MONTH STARTS '2025-08-08 04:25:32' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE billing
  SET late_fee = late_fee + (amount * 0.05),  -- Add 5% of the original amount to the existing late_fee
      status = 'Overdue' -- Ensure the status remains 'Overdue'
  WHERE (status = 'Unpaid' OR status = 'Overdue') -- Check if the bill is unpaid or already overdue
    AND date_due < NOW()$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
