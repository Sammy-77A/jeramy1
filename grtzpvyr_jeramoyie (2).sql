-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 04, 2026 at 11:27 AM
-- Server version: 8.0.44
-- PHP Version: 8.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `grtzpvyr_jeramoyie`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `details` text COLLATE utf8mb4_general_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `member_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(2, 23, 'Yearly Payout', 'Processed KSh 450.58248596341 for Member ID 5. Ref: HJFJSJ68KG', '::1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-17 18:27:09'),
(3, 23, 'Announcement Created', 'Title: member addition, Priority: normal', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-17 19:27:37'),
(4, 3, 'Vote Cast', 'Loan ID: 2, Type: normal, Vote: reject', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-17 19:28:27'),
(5, 23, 'Announcement Deleted', 'Title: member addition', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-17 19:50:57'),
(6, 23, 'Community Product Created', 'Community Saving scheme', '196.96.112.10', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-23 18:35:49'),
(7, 23, 'Community Customer Status', '#2 → suspended', '196.96.112.10', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-23 18:37:00'),
(8, 23, 'Community Customer Status', '#2 → active', '196.96.112.10', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-23 18:37:25'),
(9, 23, 'Member Added', 'Name: Rosemary John, Phone: 254726640973, Role: Member', '196.96.112.10', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-23 18:41:50'),
(10, 23, 'Community Customer Status', '#3 → suspended', '102.209.18.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 17:42:04'),
(11, 23, 'Manual Financial Entry', 'Member ID: 10, Amount: KSh 2000, Type: normal_savings, Ref: CASH-1772443425', '154.159.238.252', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 09:23:45'),
(12, 23, 'Manual Financial Entry', 'Member ID: 10, Amount: KSh 100, Type: welfare, Ref: CASH-1772443465', '154.159.238.252', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 09:24:25'),
(13, 23, 'Manual Financial Entry', 'Member ID: 10, Amount: KSh 100, Type: welfare, Ref: CASH-1772443503', '154.159.238.252', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 09:25:03'),
(14, 23, 'Manual Financial Entry', 'Member ID: 10, Amount: KSh 10000, Type: normal_savings, Ref: CASH-1772447400', '154.159.238.252', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 10:30:00'),
(15, 23, 'Manual Financial Entry', 'Member ID: 10, Amount: KSh 10000, Type: normal_savings, Ref: CASH-1772447400', '154.159.238.252', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 10:30:00'),
(16, 23, 'Manual Financial Entry', 'Member ID: 10, Amount: KSh 10000, Type: normal_savings, Ref: CASH-1772447404', '154.159.238.252', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 10:30:04'),
(17, 23, 'Manual Financial Entry', 'Member ID: 10, Amount: KSh 20000, Type: normal_savings, Ref: CASH-1772447423', '154.159.238.252', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 10:30:23'),
(18, 23, 'Manual Financial Entry', 'Member ID: 10, Amount: KSh 1000, Type: weekly, Ref: CASH-1772447665', '154.159.238.252', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 10:34:26'),
(19, 23, 'Manual Financial Entry', 'Member ID: 17, Amount: KSh 10000, Type: weekly, Ref: CASH-1772448006', '154.159.238.252', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 10:40:06'),
(20, 23, 'Manual Financial Entry', 'Member ID: 10, Amount: KSh 500, Type: welfare, Ref: CASH-1772448094', '154.159.238.252', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 10:41:34'),
(21, 23, 'Manual Financial Entry', 'Member ID: 17, Amount: KSh 1500, Type: loan_repayment, Ref: CASH-1772448138', '154.159.238.252', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 10:42:18'),
(22, 23, 'Community Customer Status', '#3 → active', '154.159.238.252', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 10:48:14'),
(23, 23, 'Announcement Created', 'Title: Hi let\'s meet, Priority: normal', '154.159.238.252', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 11:15:42'),
(24, 23, 'Manual Financial Entry', 'Member ID: 10, Amount: KSh 1, Type: loan_repayment, Ref: CASH-1772450420', '154.159.238.252', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 11:20:20'),
(25, 23, 'Member Added', 'Name: Tobias okumu okoth, Phone: +254791496850, Role: Chairperson', '154.159.238.115', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 13:29:20'),
(26, 23, 'Member Edited', 'Member ID: 26 - Role: Member, Status: active', '154.159.238.115', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 13:30:06'),
(27, 23, 'Member Edited', 'Member ID: 26 - Role: Member, Status: active', '154.159.238.115', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 13:33:06'),
(28, 23, 'Member Deleted', 'Deleted member ID: 26', '154.159.238.115', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 13:33:23'),
(29, 23, 'Member Added', 'Name: Tobias Okumu Okoth, Phone: 254791496850, Role: Member', '154.159.238.115', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 13:34:21'),
(30, 3, 'Manual Financial Entry', 'Member ID: 3, Amount: KSh 500, Type: table_banking, Ref: CASH-1772462182', '154.159.238.115', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 14:36:22'),
(31, 3, 'Manual Financial Entry', 'Member ID: 6, Amount: KSh 1000, Type: welfare, Ref: CASH-1772462247', '154.159.238.115', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 14:37:27'),
(32, 23, 'Member Edited', 'Member ID: 16 - Role: Member, Status: active', '154.159.238.7', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-04 07:02:22'),
(33, 23, 'Member Edited', 'Member ID: 22 - Role: Member, Status: active', '154.159.238.7', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-04 07:02:33'),
(34, 23, 'Member Edited', 'Member ID: 25 - Role: Member, Status: active', '154.159.238.7', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-04 07:02:40');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int NOT NULL,
  `admin_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `priority` enum('normal','important','urgent') COLLATE utf8mb4_general_ci DEFAULT 'normal',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `admin_id`, `title`, `message`, `priority`, `created_at`) VALUES
(2, 23, 'Hi let\'s meet', 'Today 2/3/26', 'normal', '2026-03-02 11:15:41');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `meeting_date` date NOT NULL,
  `status` enum('present','absent','late') COLLATE utf8mb4_general_ci NOT NULL,
  `apology_text` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `member_id`, `meeting_date`, `status`, `apology_text`) VALUES
(1, 10, '2026-01-28', 'late', NULL),
(2, 17, '2026-01-28', 'present', NULL),
(3, 18, '2026-01-28', 'present', NULL),
(4, 12, '2026-01-28', 'present', NULL),
(5, 9, '2026-01-28', 'present', NULL),
(6, 7, '2026-01-28', 'absent', NULL),
(7, 8, '2026-01-28', 'present', NULL),
(8, 5, '2026-01-28', 'present', NULL),
(9, 3, '2026-01-28', 'late', NULL),
(10, 16, '2026-01-28', 'present', NULL),
(11, 11, '2026-01-28', 'present', NULL),
(12, 15, '2026-01-28', 'present', NULL),
(13, 1, '2026-01-28', 'absent', NULL),
(14, 22, '2026-01-28', 'present', NULL),
(15, 2, '2026-01-28', 'present', NULL),
(16, 21, '2026-01-28', 'present', NULL),
(17, 13, '2026-01-28', 'present', NULL),
(18, 23, '2026-01-28', 'present', NULL),
(19, 6, '2026-01-28', 'present', NULL),
(20, 20, '2026-01-28', 'present', NULL),
(21, 4, '2026-01-28', 'present', NULL),
(22, 19, '2026-01-28', 'present', NULL),
(23, 14, '2026-01-28', 'present', NULL),
(139, 10, '2026-02-02', 'present', ''),
(140, 17, '2026-02-02', 'present', ''),
(141, 18, '2026-02-02', 'absent', ''),
(142, 12, '2026-02-02', 'present', ''),
(143, 9, '2026-02-02', 'present', ''),
(144, 7, '2026-02-02', 'present', ''),
(145, 8, '2026-02-02', 'present', ''),
(146, 5, '2026-02-02', 'present', ''),
(147, 3, '2026-02-02', 'present', ''),
(148, 16, '2026-02-02', 'present', ''),
(149, 11, '2026-02-02', 'present', ''),
(150, 15, '2026-02-02', 'present', ''),
(151, 1, '2026-02-02', 'present', ''),
(152, 22, '2026-02-02', 'present', ''),
(153, 2, '2026-02-02', 'present', ''),
(154, 21, '2026-02-02', 'present', ''),
(155, 13, '2026-02-02', 'present', ''),
(156, 23, '2026-02-02', 'present', ''),
(157, 6, '2026-02-02', 'present', ''),
(158, 20, '2026-02-02', 'present', ''),
(159, 4, '2026-02-02', 'present', ''),
(160, 19, '2026-02-02', 'present', ''),
(161, 14, '2026-02-02', 'present', '');

-- --------------------------------------------------------

--
-- Table structure for table `community_audit_logs`
--

CREATE TABLE `community_audit_logs` (
  `id` int NOT NULL,
  `user_type` enum('admin','customer') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'admin',
  `user_id` int NOT NULL,
  `action` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `details` text COLLATE utf8mb4_general_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT '0.0.0.0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_audit_logs`
--

INSERT INTO `community_audit_logs` (`id`, `user_type`, `user_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(1, 'customer', 1, 'Deposit', 'Amount: 2, Ref: DEP260219AC2FFA22', '::1', '2026-02-19 21:36:43'),
(2, 'customer', 1, 'Withdrawal', 'Amount: 2, Ref: WDR2602197A0032F7', '::1', '2026-02-19 21:37:02'),
(3, 'customer', 1, 'Deposit', 'Amount: 2, Ref: DEP2602193AC0D03B', '::1', '2026-02-19 21:37:27'),
(4, 'customer', 1, 'Login', 'Successful login', '::1', '2026-02-19 21:41:41'),
(5, 'customer', 1, 'Login', 'Successful login', '::1', '2026-02-19 22:42:06'),
(6, 'customer', 1, 'Withdrawal', 'Amount: 1, Ref: WDR26021911BB7A9B', '::1', '2026-02-19 22:43:35'),
(7, 'customer', 1, 'Login', 'Successful login', '::1', '2026-02-19 22:54:46'),
(8, 'customer', 1, 'Login', 'Successful login', '::1', '2026-02-19 23:08:14'),
(9, 'customer', 1, 'Login', 'Successful login', '::1', '2026-02-19 23:08:37'),
(10, 'customer', 1, 'Login', 'Successful login', '::1', '2026-02-19 23:19:41'),
(11, 'customer', 1, 'Login', 'Successful login', '::1', '2026-02-20 08:46:04'),
(12, 'customer', 1, 'Withdrawal', 'Amount: 1, Ref: WDR2602200B816861', '::1', '2026-02-20 08:46:24'),
(13, 'customer', 1, 'Loan Application', 'Product: Okoa Loan - Standard, Amount: 500', '::1', '2026-02-20 09:35:38'),
(14, 'customer', 1, 'Login', 'Successful login', '::1', '2026-02-20 11:08:33'),
(15, 'customer', 1, 'Login', 'Successful login', '102.209.18.40', '2026-02-20 13:41:55'),
(16, 'customer', 1, 'Login', 'Successful login', '102.209.18.40', '2026-02-20 18:24:37'),
(17, 'customer', 1, 'Login', 'Successful login', '102.209.18.40', '2026-02-20 18:25:11'),
(18, 'customer', 1, 'Login', 'Successful login', '102.209.18.40', '2026-02-22 17:46:10'),
(19, 'customer', 1, 'Login', 'Successful login', '102.209.18.40', '2026-02-22 18:14:12'),
(20, 'admin', 23, 'Product Created', 'Product: Community Saving scheme', '196.96.112.10', '2026-02-23 18:35:49'),
(21, 'admin', 23, 'Customer Status Changed', 'Customer #2 → suspended', '196.96.112.10', '2026-02-23 18:37:00'),
(22, 'admin', 23, 'Customer Status Changed', 'Customer #2 → active', '196.96.112.10', '2026-02-23 18:37:25'),
(23, 'customer', 1, 'Login', 'Successful login', '102.209.18.40', '2026-02-26 17:41:15'),
(24, 'admin', 23, 'Customer Status Changed', 'Customer #3 → suspended', '102.209.18.40', '2026-02-26 17:42:04'),
(25, 'customer', 2, 'Login', 'Successful login', '105.160.62.145', '2026-02-27 16:02:30'),
(26, 'admin', 23, 'Loan Approved', 'Loan #1', '154.159.238.252', '2026-03-02 10:47:39'),
(27, 'admin', 23, 'Customer Status Changed', 'Customer #3 → active', '154.159.238.252', '2026-03-02 10:48:13');

-- --------------------------------------------------------

--
-- Table structure for table `community_customers`
--

CREATE TABLE `community_customers` (
  `id` int NOT NULL,
  `full_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `national_id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('active','suspended') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_customers`
--

INSERT INTO `community_customers` (`id`, `full_name`, `phone_number`, `national_id`, `email`, `password`, `status`, `created_at`, `updated_at`) VALUES
(1, 'ndubi samuel', '254790370094', '506884609', 'samuendubi321@gmail.com', '$2y$10$/kNjZ.39wk.ZZP4uAD4Gk.mpDD.rtWyvxzIW64Cd5HdE9YDBkIEk2', 'active', '2026-02-19 21:35:26', '2026-02-19 21:35:26'),
(2, 'Kevin Njenga', '254796183933', '42531988', 'ghtdujkbi321@gmail.com', '$2y$10$59/Wh20MUqgE0sDIjB.7Re8N1i/zNnIcX1RJlbfFXXhykabJTQcRi', 'active', '2026-02-20 17:35:18', '2026-02-23 18:37:25'),
(3, 'njeri', '254758584639', '456789987', 'samuenuudubi321@gmail.com', '$2y$10$JTCStJBIjf1WMheUeVIBsuFjTI5zyn3b8J.InTQXNyTySgMscp2iC', 'active', '2026-02-20 19:12:28', '2026-03-02 10:48:13'),
(4, 'sammy', '254740264042', '531814761', 'sygensamsammy@gmail.com', '$2y$10$v9NK6C3cE02YPKj9v2JYv.7goIUJT86Kj0bNTnZhHSNChxhLBqKIC', 'active', '2026-02-28 13:39:58', '2026-02-28 13:39:58');

-- --------------------------------------------------------

--
-- Stand-in structure for view `community_finance_summary`
-- (See below for the actual view)
--
CREATE TABLE `community_finance_summary` (
`total_savings` decimal(37,2)
,`total_loans_issued` decimal(37,2)
,`total_interest_earned` decimal(37,2)
,`total_penalties_collected` decimal(37,2)
,`active_loans_count` bigint
,`defaulted_loans_count` bigint
);

-- --------------------------------------------------------

--
-- Table structure for table `community_loans`
--

CREATE TABLE `community_loans` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `product_id` int NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `interest_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_repayable` decimal(15,2) NOT NULL DEFAULT '0.00',
  `penalty_accrued` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','approved','rejected','completed','defaulted') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `approved_by` int DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_loans`
--

INSERT INTO `community_loans` (`id`, `customer_id`, `product_id`, `amount`, `interest_amount`, `total_repayable`, `penalty_accrued`, `status`, `approved_by`, `due_date`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 500.00, 150.00, 650.00, 0.00, 'approved', 23, '2026-05-20', '2026-02-20 09:35:38', '2026-03-02 10:47:39');

-- --------------------------------------------------------

--
-- Table structure for table `community_loan_approvals`
--

CREATE TABLE `community_loan_approvals` (
  `id` int NOT NULL,
  `loan_id` int NOT NULL,
  `approved_by` int NOT NULL,
  `decision` enum('approved','rejected') COLLATE utf8mb4_general_ci NOT NULL,
  `comment` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_loan_approvals`
--

INSERT INTO `community_loan_approvals` (`id`, `loan_id`, `approved_by`, `decision`, `comment`, `created_at`) VALUES
(1, 1, 23, 'approved', '', '2026-03-02 10:47:39');

-- --------------------------------------------------------

--
-- Table structure for table `community_loan_repayments`
--

CREATE TABLE `community_loan_repayments` (
  `id` int NOT NULL,
  `loan_id` int NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'mpesa',
  `reference_code` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_login_attempts`
--

CREATE TABLE `community_login_attempts` (
  `id` int NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_login_attempts`
--

INSERT INTO `community_login_attempts` (`id`, `phone_number`, `attempted_at`) VALUES
(46, '254703905360', '2026-03-04 09:09:43'),
(47, '254703905360', '2026-03-04 09:09:49'),
(21, '254708218330', '2026-02-24 14:18:02'),
(37, '254712206142', '2026-03-02 11:11:34'),
(16, '254720297440', '2026-02-23 15:59:01'),
(17, '254720297440', '2026-02-23 15:59:35'),
(38, '254720297440', '2026-03-02 14:17:50'),
(39, '254720297440', '2026-03-02 14:18:42'),
(43, '254720297440', '2026-03-02 14:34:20'),
(29, '254720859074', '2026-02-25 13:40:41'),
(19, '254721347039', '2026-02-23 18:46:09'),
(24, '254721347039', '2026-02-25 06:13:33'),
(25, '254721347039', '2026-02-25 10:36:18'),
(26, '254721347039', '2026-02-25 10:37:05'),
(27, '254721347039', '2026-02-25 11:26:35'),
(28, '254721347039', '2026-02-25 11:27:14'),
(30, '254721347039', '2026-02-26 09:23:22'),
(31, '254721347039', '2026-02-26 09:24:20'),
(22, '254724387700', '2026-02-24 14:18:17'),
(23, '254724387700', '2026-02-24 14:18:41'),
(18, '254726773296', '2026-02-23 18:45:10'),
(35, '254726773296', '2026-03-02 10:17:55'),
(36, '254726773296', '2026-03-02 10:17:59'),
(40, '254726773296', '2026-03-02 14:19:42'),
(41, '254726773296', '2026-03-02 14:19:57'),
(42, '254726773296', '2026-03-02 14:20:42'),
(20, '254790370096', '2026-02-23 18:46:46'),
(44, '254793750057', '2026-03-03 12:40:18'),
(45, '254793750057', '2026-03-03 12:40:31');

-- --------------------------------------------------------

--
-- Table structure for table `community_password_resets`
--

CREATE TABLE `community_password_resets` (
  `id` int NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `customer_id` int NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `checkout_request_id` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `used` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NOT NULL DEFAULT ((now() + interval 15 minute))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_pending_deposits`
--

CREATE TABLE `community_pending_deposits` (
  `id` int NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `customer_id` int NOT NULL,
  `product_id` int NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `checkout_request_id` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_pending_registrations`
--

CREATE TABLE `community_pending_registrations` (
  `id` int NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `national_id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `checkout_request_id` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_pending_registrations`
--

INSERT INTO `community_pending_registrations` (`id`, `token`, `full_name`, `phone_number`, `national_id`, `email`, `password_hash`, `created_at`, `checkout_request_id`) VALUES
(7, '2c7aaaf6699a47d23986a592aa90923a', 'Kevin Njenga', '254718983948', '123456789', 'njenkevo590@gmail.com', '$2y$10$T2/SwdTTxAWJ3DQjOoZf4eZbkeFapIXGEDylNB82QdxKnItXhXVFe', '2026-02-27 16:00:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `community_pending_repayments`
--

CREATE TABLE `community_pending_repayments` (
  `id` int NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `customer_id` int NOT NULL,
  `loan_id` int NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `checkout_request_id` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_products`
--

CREATE TABLE `community_products` (
  `id` int NOT NULL,
  `product_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `product_type` enum('saving_scheme','okoa_loan','custom') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'custom',
  `description` text COLLATE utf8mb4_general_ci,
  `minimum_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `maximum_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `interest_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `penalty_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `duration_in_months` int NOT NULL DEFAULT '1',
  `requires_approval` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_products`
--

INSERT INTO `community_products` (`id`, `product_name`, `product_type`, `description`, `minimum_amount`, `maximum_amount`, `interest_rate`, `penalty_rate`, `duration_in_months`, `requires_approval`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Jera Savings', 'saving_scheme', 'Flexible savings account for community members. No minimum lock-in period.', 100.00, 1000000.00, 0.00, 0.00, 0, 0, 1, '2026-02-19 20:24:10', '2026-02-19 20:24:10'),
(2, 'Okoa Loan - Standard', 'okoa_loan', 'Quick emergency loan up to KSh 50,000 with 10% interest over 3 months.', 500.00, 50000.00, 10.00, 5.00, 3, 1, 1, '2026-02-19 20:24:10', '2026-02-19 20:24:10'),
(3, 'Okoa Loan - Premium', 'okoa_loan', 'Higher-tier loan up to KSh 200,000 with 8% interest over 6 months.', 5000.00, 200000.00, 8.00, 3.00, 6, 1, 1, '2026-02-19 20:24:10', '2026-02-19 20:24:10'),
(4, 'Business Boost', 'custom', 'Business development loan with competitive rates over 12 months.', 10000.00, 500000.00, 12.00, 5.00, 12, 1, 1, '2026-02-19 20:24:10', '2026-02-19 20:24:10'),
(5, 'Community Saving scheme', 'saving_scheme', '', 50.00, 1000000.00, 0.00, 0.00, 12, 1, 1, '2026-02-23 18:35:49', '2026-02-23 18:35:49');

-- --------------------------------------------------------

--
-- Table structure for table `community_rate_limits`
--

CREATE TABLE `community_rate_limits` (
  `id` int NOT NULL,
  `action_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `identifier` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_rate_limits`
--

INSERT INTO `community_rate_limits` (`id`, `action_type`, `identifier`, `attempted_at`) VALUES
(17, 'deposit', 'cust_1', '2026-02-20 11:41:49'),
(18, 'deposit', 'cust_1', '2026-02-20 11:42:39'),
(19, 'deposit', 'cust_1', '2026-02-20 11:42:45'),
(20, 'deposit', 'cust_1', '2026-02-20 11:43:06'),
(21, 'deposit', 'cust_4', '2026-02-28 13:41:36');

-- --------------------------------------------------------

--
-- Table structure for table `community_savings`
--

CREATE TABLE `community_savings` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `product_id` int NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `transaction_type` enum('deposit','withdrawal') COLLATE utf8mb4_general_ci NOT NULL,
  `reference_code` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_savings`
--

INSERT INTO `community_savings` (`id`, `customer_id`, `product_id`, `amount`, `transaction_type`, `reference_code`, `created_at`) VALUES
(1, 1, 1, 2.00, 'deposit', 'DEP260219AC2FFA22', '2026-02-19 21:36:43'),
(2, 1, 1, 2.00, 'withdrawal', 'WDR2602197A0032F7', '2026-02-19 21:37:02'),
(3, 1, 1, 2.00, 'deposit', 'DEP2602193AC0D03B', '2026-02-19 21:37:27'),
(4, 1, 1, 1.00, 'withdrawal', 'WDR26021911BB7A9B', '2026-02-19 22:43:35'),
(5, 1, 1, 1.00, 'withdrawal', 'WDR2602200B816861', '2026-02-20 08:46:24'),
(6, 1, 1, 4.00, 'deposit', 'DEP2602209E451E08', '2026-02-20 11:39:52'),
(7, 1, 1, 7.00, 'deposit', 'DEP260220B1A3F0C5', '2026-02-20 11:43:19');

-- --------------------------------------------------------

--
-- Table structure for table `community_security_log`
--

CREATE TABLE `community_security_log` (
  `id` int NOT NULL,
  `event_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `customer_id` int DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `details` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_security_log`
--

INSERT INTO `community_security_log` (`id`, `event_type`, `customer_id`, `ip_address`, `user_agent`, `details`, `created_at`) VALUES
(1, 'stk_deposit_initiated', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Amount: 3, Token: 4a13dc1cefb69797d40b060966927288', '2026-02-19 22:42:36'),
(2, 'withdrawal', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Amount: 1, Ref: WDR26021911BB7A9B', '2026-02-19 22:43:35'),
(3, 'session_hijack_detected', NULL, '102.209.18.40', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Fingerprint mismatch', '2026-02-19 23:08:31'),
(4, 'withdrawal', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Amount: 1, Ref: WDR2602200B816861', '2026-02-20 08:46:24'),
(5, 'stk_deposit_initiated', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Amount: 2, Token: 78671d5a33f9d0eb7c0a2f73ba875f5e', '2026-02-20 08:47:13'),
(6, 'stk_deposit_initiated', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Amount: 3, Token: f069bceba17b7f14d84823e9509dcb7d', '2026-02-20 09:32:30'),
(7, 'loan_application', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Amount: 500, Product: Okoa Loan - Standard', '2026-02-20 09:35:38'),
(8, 'velocity_exceeded', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Action: deposit, Max: 5/hr', '2026-02-20 09:40:36'),
(9, 'stk_deposit_initiated', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Amount: 5, Token: c4aa8dc0a7fb997ab5e8a333cbf3bff3', '2026-02-20 10:38:53'),
(10, 'stk_deposit_initiated', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Amount: 5, Token: cd75806afdfe942d1bd5e4b29f5e358e', '2026-02-20 11:08:07'),
(11, 'stk_deposit_initiated', 1, '102.209.18.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Amount: 5, Token: 82949a8f8ec4550ffcc4841c7cf51c8a', '2026-02-20 11:09:28'),
(12, 'stk_deposit_initiated', 1, '102.209.18.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Amount: 3, Token: 7da684540c7a9cc005b8e72dede62332', '2026-02-20 11:14:01'),
(13, 'velocity_exceeded', 1, '102.209.18.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Action: deposit, Max: 5/hr', '2026-02-20 11:14:39'),
(14, 'velocity_exceeded', 1, '102.209.18.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Action: deposit, Max: 5/hr', '2026-02-20 11:18:51'),
(15, 'stk_deposit_initiated', 1, '102.209.18.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Amount: 5, Token: 00fef77f0b843fb7149dec39140aa9fc', '2026-02-20 11:19:28'),
(16, 'stk_deposit_initiated', 1, '102.209.18.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Amount: 5, Token: 9a8b8433c8cc834398d07b03ab4a151f', '2026-02-20 11:33:23'),
(17, 'stk_deposit_initiated', 1, '102.209.18.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Amount: 6, Token: 1a1384d5b96466c1561e9a52604683fe', '2026-02-20 11:34:02'),
(18, 'stk_deposit_initiated', 1, '102.209.18.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Amount: 4, Token: 3a2675df470e70189acc4af99c4a9b19', '2026-02-20 11:39:42'),
(19, 'deposit_confirmed', 1, NULL, NULL, 'Amount: 4.00, Ref: DEP2602209E451E08, Token: 3a2675df470e70189acc4af99c4a9b19', '2026-02-20 11:39:52'),
(20, 'stk_deposit_initiated', 1, '102.209.18.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Amount: 3, Token: 6aa04be4d502587e691a5812f7d4529e', '2026-02-20 11:40:13'),
(21, 'velocity_exceeded', 1, '102.209.18.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Action: deposit, Max: 5/hr', '2026-02-20 11:41:13'),
(22, 'stk_deposit_initiated', 1, '102.209.18.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Amount: 5, Token: 4f99e31e973e56aac8c6b40095584118', '2026-02-20 11:41:52'),
(23, 'stk_deposit_initiated', 1, '102.209.18.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Amount: 4, Token: bef515b0fdb8b44314bf277aac6aa3ec', '2026-02-20 11:42:45'),
(24, 'stk_deposit_initiated', 1, '102.209.18.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Amount: 7, Token: 588f6998f239caa0c22482bff09e21ae', '2026-02-20 11:43:09'),
(25, 'deposit_confirmed', 1, NULL, NULL, 'Amount: 7.00, Ref: DEP260220B1A3F0C5, Token: 588f6998f239caa0c22482bff09e21ae', '2026-02-20 11:43:19'),
(26, 'stk_deposit_initiated', 4, '102.209.18.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'Amount: 2, Token: e46af8a0e3b96b4756f10107e7e8f536', '2026-02-28 13:41:48');

-- --------------------------------------------------------

--
-- Table structure for table `community_withdrawal_requests`
--

CREATE TABLE `community_withdrawal_requests` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `product_id` int NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `reference_code` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `admin_comment` text COLLATE utf8mb4_general_ci,
  `approved_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_repayments`
--

CREATE TABLE `loan_repayments` (
  `id` int NOT NULL,
  `loan_id` int NOT NULL,
  `loan_type` enum('normal','table_banking','uwezo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `member_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `repayment_date` date NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'M-PESA',
  `reference_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recorded_by` int DEFAULT NULL COMMENT 'Admin who recorded',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loan_repayments`
--

INSERT INTO `loan_repayments` (`id`, `loan_id`, `loan_type`, `member_id`, `amount`, `repayment_date`, `payment_method`, `reference_number`, `recorded_by`, `notes`, `created_at`) VALUES
(1, 0, 'normal', 23, 1.00, '2026-01-28', 'M-PESA', 'UAS664ZQ6O', NULL, 'M-PESA Auto-Payment', '2026-01-27 21:45:30'),
(2, 0, 'normal', 23, 1.00, '2026-01-28', 'M-PESA', 'UAS664ZOPP', NULL, 'M-PESA Auto-Payment', '2026-01-27 21:58:37'),
(3, 0, 'normal', 23, 1.00, '2026-01-28', 'M-PESA', 'CASH-1769622548', NULL, 'Manual meeting repayment', '2026-01-28 17:49:08'),
(4, 0, 'normal', 23, 20.00, '2026-01-28', 'M-PESA', 'CASH-1769622800', NULL, 'Manual meeting repayment', '2026-01-28 17:53:20'),
(5, 0, 'normal', 10, 12.00, '2026-01-28', 'M-PESA', 'CASH-1769625097', NULL, 'Manual Meeting Repayment', '2026-01-28 18:31:38'),
(6, 0, 'normal', 23, 2390.00, '2026-01-28', 'M-PESA', 'CASH-1769629197', NULL, 'Manual Meeting Repayment', '2026-01-28 19:39:57'),
(7, 0, 'normal', 3, 1.00, '2026-03-02', 'M-PESA', 'UC28282Z41', NULL, 'M-PESA Auto-Payment', '2026-03-02 09:15:53'),
(8, 0, 'normal', 17, 1500.00, '2026-03-02', 'M-PESA', 'CASH-1772448138', NULL, 'Manual Meeting Repayment', '2026-03-02 10:42:18'),
(9, 0, 'normal', 10, 1.00, '2026-03-02', 'M-PESA', 'CASH-1772450420', NULL, 'Manual Meeting Repayment', '2026-03-02 11:20:20');

-- --------------------------------------------------------

--
-- Table structure for table `loan_votes`
--

CREATE TABLE `loan_votes` (
  `id` int NOT NULL,
  `loan_id` int NOT NULL,
  `loan_type` enum('normal','table_banking','uwezo') COLLATE utf8mb4_general_ci NOT NULL,
  `voter_id` int NOT NULL,
  `vote` enum('approve','reject') COLLATE utf8mb4_general_ci NOT NULL,
  `comment` text COLLATE utf8mb4_general_ci,
  `voted_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_votes`
--

INSERT INTO `loan_votes` (`id`, `loan_id`, `loan_type`, `voter_id`, `vote`, `comment`, `voted_at`) VALUES
(1, 1, 'table_banking', 21, 'approve', NULL, '2026-01-28 01:45:53'),
(2, 2, 'normal', 23, 'approve', NULL, '2026-01-28 02:08:28'),
(4, 2, 'normal', 21, 'approve', NULL, '2026-01-28 02:37:07'),
(6, 2, 'normal', 8, 'approve', NULL, '2026-01-28 03:36:50'),
(9, 2, 'normal', 2, 'reject', NULL, '2026-01-28 10:45:18'),
(10, 2, 'normal', 7, 'approve', NULL, '2026-01-28 10:55:37'),
(12, 2, 'normal', 6, 'reject', NULL, '2026-01-28 12:11:37'),
(13, 2, 'normal', 10, 'approve', NULL, '2026-01-28 20:26:31'),
(14, 4, 'normal', 8, 'reject', NULL, '2026-01-28 21:37:46'),
(15, 1, 'uwezo', 21, 'approve', NULL, '2026-01-28 22:34:33'),
(16, 1, 'uwezo', 3, 'approve', NULL, '2026-01-28 22:35:29'),
(17, 2, 'normal', 5, 'approve', NULL, '2026-01-28 22:45:02'),
(19, 2, 'normal', 12, 'approve', NULL, '2026-01-30 02:40:40'),
(20, 2, 'normal', 3, 'reject', NULL, '2026-02-17 19:28:27');

-- --------------------------------------------------------

--
-- Table structure for table `meetings`
--

CREATE TABLE `meetings` (
  `id` int NOT NULL,
  `meeting_date` date NOT NULL,
  `meeting_type` enum('weekly','special','annual_general') COLLATE utf8mb4_general_ci DEFAULT 'weekly',
  `venue` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `agenda` text COLLATE utf8mb4_general_ci,
  `minutes` text COLLATE utf8mb4_general_ci,
  `chairperson_id` int DEFAULT NULL,
  `secretary_id` int DEFAULT NULL,
  `status` enum('scheduled','ongoing','completed','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'scheduled',
  `created_by` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meeting_attendance`
--

CREATE TABLE `meeting_attendance` (
  `id` int NOT NULL,
  `meeting_id` int NOT NULL,
  `member_id` int NOT NULL,
  `status` enum('present','absent','excused','late') COLLATE utf8mb4_general_ci DEFAULT 'absent',
  `arrival_time` time DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `recorded_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meeting_minutes`
--

CREATE TABLE `meeting_minutes` (
  `id` int NOT NULL,
  `meeting_date` date NOT NULL,
  `recorder_id` int NOT NULL,
  `minutes_text` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meeting_minutes`
--

INSERT INTO `meeting_minutes` (`id`, `meeting_date`, `recorder_id`, `minutes_text`, `created_at`) VALUES
(1, '2026-01-28', 23, 'The way forward was discussed ', '2026-01-28 19:54:50'),
(7, '2026-02-02', 23, 'this and that', '2026-01-28 20:10:20');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `national_id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'Member' COMMENT 'Chairperson, Treasury, Member, etc.',
  `is_admin` tinyint(1) DEFAULT '0' COMMENT '1 for office bearers',
  `is_native` tinyint(1) DEFAULT '1' COMMENT 'All are native for now',
  `status` enum('active','inactive','suspended') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `paid` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 = has paid registration/activation fee, 0 = not yet paid',
  `joined_date` date DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `payout_preference` enum('mpesa','cash','savings') COLLATE utf8mb4_general_ci DEFAULT 'savings'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `name`, `national_id`, `phone`, `email`, `password_hash`, `role`, `is_admin`, `is_native`, `status`, `paid`, `joined_date`, `last_login`, `created_at`, `payout_preference`) VALUES
(1, 'Michael Odhoji', '8227440', '254721347039', NULL, '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'Chairperson', 1, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'savings'),
(2, 'Nancy Ndubi', '0559328', '254726152761', NULL, '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'Assistant Chairperson', 1, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'savings'),
(3, 'Jairus Ndubi', '26042208', '254726773296', 'jairusno@gmail.com', '$2y$10$PX.9RQB3FLBgNb/fB.QEHOhaqjRPwLD2DU4E86RN3scaC/ytiwB5G', 'Treasury', 1, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'savings'),
(4, 'Solomon Akuom', '20588805', '254724387700', NULL, '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'Secretary', 1, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'savings'),
(5, 'Jacob Otieno', '37129187', '254794168627', NULL, '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'Assistant Secretary', 1, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'mpesa'),
(6, 'Sarah Opiyo', '36942711', '254716814932', NULL, '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'Organizer', 1, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'savings'),
(7, 'Ezra Bwana', '6412055', '254723333854', NULL, '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'Trustee', 1, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'savings'),
(8, 'Fanuel Solomon', '24879191', '254726968219', NULL, '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'Loan Chairperson', 1, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'savings'),
(9, 'Esther Anyango', '8974824', '254719770170', NULL, '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'Member', 0, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'savings'),
(10, 'Allan Ouma', '32084059', '254712206142', NULL, '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'Member', 0, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'savings'),
(11, 'Josephine Ogee', '33958663', '254703261959', NULL, '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'Member', 0, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'savings'),
(12, 'Collins Ochieng', '32298816', '254720297440', NULL, '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'Member', 0, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'mpesa'),
(13, 'Reuben Aboki', '6938976', '254769009365', NULL, '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'Member', 0, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'savings'),
(14, 'Yvone Ntabo', '31317587', '254703905360', NULL, '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'Member', 0, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'savings'),
(15, 'Judith Omondi', '30519039', '254111718535', NULL, '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'Member', 0, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'savings'),
(16, 'John Oury', '0775401', '254727117125', NULL, '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'Member', 0, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'savings'),
(17, 'Beatrice Amisi', '13810201', '254720859074', NULL, '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'Member', 0, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'savings'),
(18, 'Bryan Ogutu', '34522902', '254793750057', NULL, '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'Member', 0, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'savings'),
(19, 'Titus Okwemba', '26345136', '254716804608', NULL, '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'Member', 0, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'savings'),
(20, 'Seline Akoth', '29069161', '254702679182', NULL, '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'Member', 0, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'savings'),
(21, 'Peninah Opiyo', '27760903', '254704978451', NULL, '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'Member', 0, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'cash'),
(22, 'Milton Obote', '30486456', '254714876361', NULL, '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'Member', 0, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:44:23', 'savings'),
(23, 'Samuel Ochieng', '506884609', '254790370094', 'samuendubi321@gmail.com', '$2y$10$pCJgyq7TziQnjXNH5Q9iwO0TDm4UZRQa8E8Eagd2/f9bdQ5raPY96', 'dev', 1, 1, 'active', 1, '2026-01-01', NULL, '2026-01-26 17:52:34', 'mpesa'),
(25, 'Rosemary John', '26369911', '254726640973', NULL, '$2y$10$PLpI4XXSOjmhiy6j/NlanekaCf7EwOnf1MfItUNCJ14SxsvyIOHeG', 'Member', 0, 1, 'active', 1, '2026-02-23', NULL, '2026-02-23 18:41:50', 'savings'),
(27, 'Tobias Okumu Okoth', '38459196', '254791496850', NULL, '$2y$10$fWorzCqTQrTzSYYUTHJcL.G5kPi04MZ/irIqI7Zsk9NKbdznlkCbi', 'Member', 0, 1, 'active', 1, '2026-03-02', NULL, '2026-03-02 13:34:21', 'savings');

-- --------------------------------------------------------

--
-- Stand-in structure for view `member_financial_summary`
-- (See below for the actual view)
--
CREATE TABLE `member_financial_summary` (
`id` int
,`normal_savings_balance` decimal(33,2)
,`table_banking_balance` decimal(33,2)
,`welfare_balance` decimal(32,2)
,`total_weekly_paid` decimal(32,2)
,`normal_loans_balance` decimal(33,2)
,`table_banking_loans_balance` decimal(33,2)
,`uwezo_loans_balance` decimal(33,2)
,`total_dividend_weight` decimal(34,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `normal_loans`
--

CREATE TABLE `normal_loans` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `purpose` text COLLATE utf8mb4_general_ci NOT NULL,
  `request_date` datetime NOT NULL,
  `status` enum('pending','approved','rejected','active','completed','defaulted') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `approved_by` int DEFAULT NULL COMMENT 'Admin/Loan Chair ID',
  `approval_date` datetime DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_general_ci,
  `disbursement_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL COMMENT '1 year from disbursement',
  `amount_disbursed` decimal(10,2) DEFAULT '0.00',
  `amount_repaid` decimal(10,2) DEFAULT '0.00',
  `balance` decimal(10,2) DEFAULT '0.00',
  `penalty` decimal(10,2) DEFAULT '0.00' COMMENT '10% penalty rules',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `penalty_accrued` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `normal_loans`
--

INSERT INTO `normal_loans` (`id`, `member_id`, `amount`, `purpose`, `request_date`, `status`, `approved_by`, `approval_date`, `rejection_reason`, `disbursement_date`, `due_date`, `amount_disbursed`, `amount_repaid`, `balance`, `penalty`, `created_at`, `penalty_accrued`) VALUES
(1, 23, 2.00, 'test', '2026-01-27 22:27:56', 'active', 8, '2026-01-28 00:40:28', NULL, NULL, '2027-01-27', 0.00, 0.00, 1.00, 0.00, '2026-01-27 21:27:56', 0.00),
(2, 23, 2.00, 'ree', '2026-01-28 00:06:32', 'pending', NULL, NULL, NULL, NULL, '2027-01-28', 0.00, 0.00, 2.00, 0.00, '2026-01-27 23:06:32', 0.00),
(3, 10, 200.00, 'emergenc', '2026-01-28 19:30:37', 'approved', 8, '2026-01-28 21:33:19', NULL, NULL, '2027-01-28', 0.00, 0.00, 200.00, 0.00, '2026-01-28 18:30:37', 0.00),
(4, 23, 18.00, 'pbck', '2026-01-28 19:37:01', 'approved', 8, '2026-01-28 21:38:10', NULL, NULL, '2027-01-28', 0.00, 0.00, 18.00, 0.00, '2026-01-28 18:37:01', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `normal_savings`
--

CREATE TABLE `normal_savings` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_type` enum('deposit','withdrawal') COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `transaction_date` date NOT NULL,
  `balance_after` decimal(10,2) NOT NULL COMMENT 'Running balance',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `normal_savings`
--

INSERT INTO `normal_savings` (`id`, `member_id`, `amount`, `transaction_type`, `description`, `transaction_date`, `balance_after`, `created_at`) VALUES
(1, 23, 5.00, 'deposit', 'M-PESA Savings', '2026-01-27', 5.00, '2026-01-27 18:50:54'),
(2, 23, 5.00, 'deposit', 'M-PESA Savings', '2026-01-27', 10.00, '2026-01-27 18:53:41'),
(3, 23, 2.00, 'deposit', 'M-PESA Savings', '2026-01-27', 12.00, '2026-01-27 19:08:27'),
(4, 13, 200.00, 'deposit', 'Manual entry: MANUAL-CF7AFCAA', '2026-01-28', 200.00, '2026-01-28 14:28:13'),
(5, 10, 150.00, 'deposit', 'Manual Meeting Entry: CASH-1769625007', '2026-01-28', 150.00, '2026-01-28 21:30:07'),
(6, 1, 0.00, 'deposit', 'Yearly Dividend', '0000-00-00', 0.00, '2026-01-30 18:32:17'),
(7, 5, 2000.00, 'deposit', 'Manual Meeting Entry: CASH-1770416930', '2026-02-07', 2000.00, '2026-02-07 01:28:50'),
(8, 2, 0.00, 'deposit', 'Yearly Reinvestment', '2026-02-07', 0.00, '2026-02-07 02:03:01'),
(9, 5, 372.79, 'deposit', 'Yearly Dividend', '0000-00-00', 0.00, '2026-02-07 02:03:41'),
(10, 5, 413.54, 'deposit', 'Yearly Dividend Reinvestment', '2026-02-07', 0.00, '2026-02-07 02:13:06'),
(11, 13, 32.51, 'deposit', 'Yearly Reinvestment', '2026-02-08', 0.00, '2026-02-08 15:58:45'),
(12, 10, 2000.00, 'deposit', 'Manual Meeting Entry: CASH-1772443425', '2026-03-02', 2150.00, '2026-03-02 09:23:45'),
(13, 10, 10000.00, 'deposit', 'Manual Meeting Entry: CASH-1772447400', '2026-03-02', 12150.00, '2026-03-02 10:30:00'),
(14, 10, 10000.00, 'deposit', 'Manual Meeting Entry: CASH-1772447400', '2026-03-02', 22150.00, '2026-03-02 10:30:00'),
(15, 10, 10000.00, 'deposit', 'Manual Meeting Entry: CASH-1772447404', '2026-03-02', 32150.00, '2026-03-02 10:30:04'),
(16, 10, 20000.00, 'deposit', 'Manual Meeting Entry: CASH-1772447423', '2026-03-02', 52150.00, '2026-03-02 10:30:23');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `token` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `phone`, `token`, `expires_at`, `used`, `created_at`) VALUES
(4, '254790370094', '9bdd83048e1df341d7f15de5798f83793d1dee2627959771cb9290d6efa2648b', '2026-02-22 18:53:09', 0, '2026-02-22 18:23:09');

-- --------------------------------------------------------

--
-- Table structure for table `pending_payments`
--

CREATE TABLE `pending_payments` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_type` enum('normal_savings','welfare','table_banking','weekly','loan_repayment') COLLATE utf8mb4_general_ci NOT NULL,
  `checkout_request_id` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mpesa_receipt` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('pending','completed','failed','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `result_desc` text COLLATE utf8mb4_general_ci,
  `phone_number` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pending_payments`
--

INSERT INTO `pending_payments` (`id`, `member_id`, `amount`, `payment_type`, `checkout_request_id`, `mpesa_receipt`, `status`, `result_desc`, `phone_number`, `created_at`, `completed_at`) VALUES
(1, 23, 1.00, 'normal_savings', 'ws_CO_27012026182159675790370094', NULL, 'pending', NULL, '254790370094', '2026-01-27 18:21:59', NULL),
(2, 23, 2.00, 'normal_savings', 'ws_CO_27012026182716542790370094', NULL, 'pending', NULL, '254790370094', '2026-01-27 18:27:16', NULL),
(3, 23, 5.00, 'normal_savings', 'ws_CO_27012026185044718790370094', 'UAR664YXJ3', 'completed', NULL, '254790370094', '2026-01-27 18:50:44', '2026-01-27 18:50:54'),
(4, 23, 1.00, 'normal_savings', 'ws_CO_27012026185248322790370094', NULL, 'failed', 'Request Cancelled by user.', '254790370094', '2026-01-27 18:52:48', NULL),
(5, 23, 5.00, 'normal_savings', 'ws_CO_27012026185319339790370094', 'UAR664YW9I', 'completed', NULL, '254790370094', '2026-01-27 18:53:19', '2026-01-27 18:53:41'),
(6, 23, 2.00, 'welfare', 'ws_CO_27012026185518467790370094', 'UAR664YWB6', 'completed', NULL, '254790370094', '2026-01-27 18:55:18', '2026-01-27 18:55:28'),
(7, 23, 2.00, 'normal_savings', 'ws_CO_27012026190205744790370094', NULL, 'pending', NULL, '254790370094', '2026-01-27 19:02:05', NULL),
(8, 23, 2.00, 'normal_savings', 'ws_CO_27012026190819039790370094', 'UAR664YP4W', 'completed', NULL, '254790370094', '2026-01-27 19:08:19', '2026-01-27 19:08:27'),
(9, 23, 2.00, 'table_banking', 'ws_CO_27012026231841362790370094', 'UAR664ZOEG', 'completed', NULL, '254790370094', '2026-01-27 23:18:41', '2026-01-27 23:18:50'),
(10, 23, 3.00, 'weekly', 'ws_CO_27012026232000608790370094', 'UAR664ZPVA', 'completed', NULL, '254790370094', '2026-01-27 23:20:00', '2026-01-27 23:20:12'),
(11, 23, 1.00, 'loan_repayment', 'ws_CO_28012026004521302790370094', 'UAS664ZQ6O', 'completed', NULL, '254790370094', '2026-01-28 00:45:21', '2026-01-28 00:45:30'),
(12, 23, 1.00, 'loan_repayment', 'ws_CO_28012026005829085790370094', 'UAS664ZOPP', 'completed', NULL, '254790370094', '2026-01-28 00:58:28', '2026-01-28 00:58:37'),
(13, 23, 1.00, 'normal_savings', 'ws_CO_28012026022243702790370094', NULL, 'failed', 'Request Cancelled by user.', '254790370094', '2026-01-28 02:22:43', NULL),
(14, 23, 1.00, 'normal_savings', 'ws_CO_28012026141617552790370094', NULL, 'failed', 'Request Cancelled by user.', '254790370094', '2026-01-28 14:16:17', NULL),
(15, 23, 1.00, 'normal_savings', 'ws_CO_28012026141656736790370094', NULL, 'failed', 'Request Cancelled by user.', '254790370094', '2026-01-28 14:16:56', NULL),
(16, 23, 1.00, 'welfare', 'ws_CO_28012026141718577790370094', NULL, 'failed', 'Request Cancelled by user.', '254790370094', '2026-01-28 14:17:18', NULL),
(17, 3, 1.00, 'loan_repayment', 'ws_CO_02032026121535544726773296', 'UC28282Z41', 'completed', NULL, '254726773296', '2026-03-02 09:15:39', '2026-03-02 09:15:53'),
(18, 3, 1.00, 'loan_repayment', 'ws_CO_02032026121550633726773296', NULL, 'pending', NULL, '254726773296', '2026-03-02 09:15:52', NULL),
(19, 3, 1.00, 'loan_repayment', 'ws_CO_02032026121557322726773296', NULL, 'failed', 'Rule limited.', '254726773296', '2026-03-02 09:15:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pending_registrations`
--

CREATE TABLE `pending_registrations` (
  `id` int NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `national_id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `share_out_history`
--

CREATE TABLE `share_out_history` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `cycle_type` enum('yearly','quarterly') COLLATE utf8mb4_general_ci NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payout_method` enum('mpesa','cash','savings') COLLATE utf8mb4_general_ci NOT NULL,
  `payout_status` enum('pending','completed','failed') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `transaction_reference` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `processed_by` int NOT NULL,
  `share_out_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mpesa_conversation_id` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mpesa_originator_id` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `share_out_history`
--

INSERT INTO `share_out_history` (`id`, `member_id`, `cycle_type`, `amount_paid`, `payout_method`, `payout_status`, `transaction_reference`, `processed_by`, `share_out_date`, `mpesa_conversation_id`, `mpesa_originator_id`) VALUES
(1, 1, 'quarterly', 0.00, 'savings', 'completed', 'TABLE-SAVINGS', 23, '2026-01-29 22:35:08', NULL, NULL),
(2, 21, 'yearly', 0.00, 'cash', 'completed', 'CSH-20260129-21', 23, '2026-01-29 22:46:44', NULL, NULL),
(4, 21, 'quarterly', 0.00, 'cash', 'completed', 'RCPT-23485421', 23, '2026-01-29 22:48:54', NULL, NULL),
(6, 12, 'yearly', 0.00, 'mpesa', 'completed', 'KJS8D40562', 23, '2026-01-29 23:41:00', NULL, NULL),
(8, 1, 'yearly', 0.00, 'savings', 'completed', 'SYS-DEP-20260130', 23, '2026-01-30 15:32:17', NULL, NULL),
(11, 23, 'yearly', 41.10, 'mpesa', 'failed', 'Error: API Reject', 23, '2026-01-30 21:49:51', 'AG_20260131_0100102401pp43s6gwta', NULL),
(12, 2, 'yearly', 0.00, 'savings', 'completed', 'REINV-20260207-2', 23, '2026-02-06 23:03:01', NULL, NULL),
(15, 13, 'yearly', 32.51, 'savings', 'completed', 'REINV-20260208-13', 23, '2026-02-08 12:58:45', NULL, NULL),
(17, 23, 'quarterly', 2.87, 'mpesa', 'completed', 'DGGTRU57FT', 23, '2026-02-10 16:27:43', NULL, NULL),
(20, 5, 'yearly', 450.58, 'mpesa', 'completed', 'HJFJSJ68KG', 23, '2026-02-17 15:27:09', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sharing_out`
--

CREATE TABLE `sharing_out` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `share_type` enum('table_banking','normal','weekly') COLLATE utf8mb4_general_ci NOT NULL,
  `period` varchar(50) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Q1 2026, Year 2026, Week 10',
  `total_shares` decimal(10,2) NOT NULL COMMENT 'Total member contributions',
  `total_interest` decimal(10,2) DEFAULT '0.00' COMMENT 'Interest earned',
  `total_amount` decimal(10,2) NOT NULL COMMENT 'Shares + Interest',
  `share_date` date NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'M-PESA',
  `payment_reference` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('pending','paid') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `table_banking_loans`
--

CREATE TABLE `table_banking_loans` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `purpose` text COLLATE utf8mb4_general_ci NOT NULL,
  `request_date` datetime NOT NULL,
  `status` enum('pending','approved','rejected','active','completed','defaulted') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `approved_by` int DEFAULT NULL,
  `approval_date` datetime DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_general_ci,
  `disbursement_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL COMMENT '3 months from disbursement',
  `amount_disbursed` decimal(10,2) DEFAULT '0.00',
  `amount_repaid` decimal(10,2) DEFAULT '0.00',
  `balance` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `interest_accrued` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_banking_loans`
--

INSERT INTO `table_banking_loans` (`id`, `member_id`, `amount`, `purpose`, `request_date`, `status`, `approved_by`, `approval_date`, `rejection_reason`, `disbursement_date`, `due_date`, `amount_disbursed`, `amount_repaid`, `balance`, `created_at`, `interest_accrued`) VALUES
(1, 23, 3.00, 'testtb', '2026-01-27 23:40:55', 'approved', 8, '2026-01-28 01:49:07', NULL, NULL, '2026-04-27', 0.00, 0.00, 3.00, '2026-01-27 22:40:55', 0.00),
(2, 1, 10000.00, 'Uwezo loan\r\n', '2026-02-26 09:35:40', 'pending', NULL, NULL, NULL, NULL, '2026-05-26', 0.00, 0.00, 10000.00, '2026-02-26 09:35:40', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `table_banking_shares`
--

CREATE TABLE `table_banking_shares` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_type` enum('share','withdrawal') COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `transaction_date` date NOT NULL,
  `balance_after` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_banking_shares`
--

INSERT INTO `table_banking_shares` (`id`, `member_id`, `amount`, `transaction_type`, `description`, `transaction_date`, `balance_after`, `created_at`) VALUES
(1, 23, 2.00, 'share', 'M-PESA Share', '2026-01-27', 2.00, '2026-01-27 23:18:50'),
(2, 15, 50.00, 'share', 'Manual entry: MANUAL-5C8C6C7D', '2026-01-28', 50.00, '2026-01-28 14:28:57'),
(3, 15, 50.00, 'share', 'Manual entry: MANUAL-B7041091', '2026-01-28', 100.00, '2026-01-28 14:35:25'),
(4, 10, 34.00, 'share', 'Meeting Cash: MANUAL-150025D1', '2026-01-28', 34.00, '2026-01-28 20:23:58'),
(5, 20, 560.00, 'share', 'Manual Meeting Entry: CASH-1769621874', '2026-01-28', 560.00, '2026-01-28 20:37:54'),
(6, 1, 0.00, 'share', NULL, '0000-00-00', 0.00, '2026-01-30 01:35:08'),
(7, 3, 500.00, 'share', 'Manual Meeting Entry: CASH-1772462182', '2026-03-02', 500.00, '2026-03-02 14:36:22');

-- --------------------------------------------------------

--
-- Table structure for table `uwezo_loans`
--

CREATE TABLE `uwezo_loans` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL COMMENT 'Either 5000 or 10000',
  `welfare_contribution_required` decimal(10,2) NOT NULL COMMENT '1000 for 5k, 2000 for 10k',
  `purpose` text COLLATE utf8mb4_general_ci NOT NULL,
  `request_date` datetime NOT NULL,
  `status` enum('pending','approved','rejected','active','completed','defaulted') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `approved_by` int DEFAULT NULL,
  `approval_date` datetime DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_general_ci,
  `disbursement_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL COMMENT '3 months from disbursement',
  `amount_disbursed` decimal(10,2) DEFAULT '0.00',
  `amount_repaid` decimal(10,2) DEFAULT '0.00',
  `balance` decimal(10,2) DEFAULT '0.00',
  `penalty` decimal(10,2) DEFAULT '0.00' COMMENT '10% if exceeding 3 months',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `penalty_accrued` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uwezo_loans`
--

INSERT INTO `uwezo_loans` (`id`, `member_id`, `amount`, `welfare_contribution_required`, `purpose`, `request_date`, `status`, `approved_by`, `approval_date`, `rejection_reason`, `disbursement_date`, `due_date`, `amount_disbursed`, `amount_repaid`, `balance`, `penalty`, `created_at`, `penalty_accrued`) VALUES
(1, 23, 5000.00, 1000.00, 'uwezo≡ƒÿü', '2026-01-28 20:17:07', 'approved', 3, '2026-01-28 22:35:38', NULL, NULL, '2026-04-28', 0.00, 0.00, 5000.00, 0.00, '2026-01-28 19:17:07', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `weekly_contributions`
--

CREATE TABLE `weekly_contributions` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `amount` decimal(10,2) DEFAULT '3000.00',
  `week_number` int NOT NULL COMMENT 'Week of the year (1-52)',
  `year` int NOT NULL,
  `contribution_date` date NOT NULL,
  `status` enum('paid','pending','late') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `paid_date` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `weekly_contributions`
--

INSERT INTO `weekly_contributions` (`id`, `member_id`, `amount`, `week_number`, `year`, `contribution_date`, `status`, `paid_date`, `created_at`) VALUES
(1, 23, 3.00, 5, 2026, '2026-01-27', 'paid', '2026-01-27 23:20:12', '2026-01-27 23:20:12'),
(2, 11, 3000.00, 6, 2026, '2026-02-07', 'paid', '2026-02-07 01:29:12', '2026-02-07 01:29:12'),
(3, 10, 1000.00, 10, 2026, '2026-03-02', 'paid', '2026-03-02 10:34:25', '2026-03-02 10:34:25'),
(4, 17, 10000.00, 10, 2026, '2026-03-02', 'paid', '2026-03-02 10:40:06', '2026-03-02 10:40:06');

-- --------------------------------------------------------

--
-- Table structure for table `welfare_contributions`
--

CREATE TABLE `welfare_contributions` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `contribution_type` enum('normal','friendly') COLLATE utf8mb4_general_ci DEFAULT 'normal' COMMENT 'normal or friendly contribution',
  `description` text COLLATE utf8mb4_general_ci,
  `contribution_date` date NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `welfare_contributions`
--

INSERT INTO `welfare_contributions` (`id`, `member_id`, `amount`, `contribution_type`, `description`, `contribution_date`, `created_at`) VALUES
(1, 23, 7800.00, 'normal', 'M-PESA Welfare', '2026-01-27', '2026-01-27 18:55:28'),
(2, 18, 230.00, 'normal', 'Manual entry: MANUAL-D980F1B2', '2026-01-28', '2026-01-28 14:48:45'),
(3, 10, 100.00, 'normal', 'Manual Meeting Entry: CASH-1772443465', '2026-03-02', '2026-03-02 09:24:25'),
(4, 10, 100.00, 'normal', 'Manual Meeting Entry: CASH-1772443503', '2026-03-02', '2026-03-02 09:25:03'),
(5, 10, 500.00, 'normal', 'Manual Meeting Entry: CASH-1772448094', '2026-03-02', '2026-03-02 10:41:34'),
(6, 6, 1000.00, 'normal', 'Manual Meeting Entry: CASH-1772462247', '2026-03-02', '2026-03-02 14:37:27');

-- --------------------------------------------------------

--
-- Table structure for table `welfare_requests`
--

CREATE TABLE `welfare_requests` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `request_type` enum('death_member','death_parent','death_child','emergency','other') COLLATE utf8mb4_general_ci NOT NULL,
  `amount_requested` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('pending','approved','rejected','paid') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `approved_by` int DEFAULT NULL,
  `approval_date` datetime DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_general_ci,
  `amount_approved` decimal(10,2) DEFAULT '0.00',
  `amount_paid` decimal(10,2) DEFAULT '0.00',
  `payment_date` date DEFAULT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_reference` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_member` (`member_id`),
  ADD KEY `idx_date` (`created_at`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`member_id`,`meeting_date`);

--
-- Indexes for table `community_audit_logs`
--
ALTER TABLE `community_audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_type`,`user_id`),
  ADD KEY `idx_action` (`action`);

--
-- Indexes for table `community_customers`
--
ALTER TABLE `community_customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone_number` (`phone_number`),
  ADD UNIQUE KEY `national_id` (`national_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_phone` (`phone_number`),
  ADD KEY `idx_national_id` (`national_id`);

--
-- Indexes for table `community_loans`
--
ALTER TABLE `community_loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customer` (`customer_id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_due` (`due_date`);

--
-- Indexes for table `community_loan_approvals`
--
ALTER TABLE `community_loan_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_loan` (`loan_id`);

--
-- Indexes for table `community_loan_repayments`
--
ALTER TABLE `community_loan_repayments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_loan` (`loan_id`);

--
-- Indexes for table `community_login_attempts`
--
ALTER TABLE `community_login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_phone_time` (`phone_number`,`attempted_at`);

--
-- Indexes for table `community_password_resets`
--
ALTER TABLE `community_password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_customer` (`customer_id`),
  ADD KEY `idx_phone` (`phone_number`);

--
-- Indexes for table `community_pending_deposits`
--
ALTER TABLE `community_pending_deposits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_customer` (`customer_id`);

--
-- Indexes for table `community_pending_registrations`
--
ALTER TABLE `community_pending_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_phone` (`phone_number`);

--
-- Indexes for table `community_pending_repayments`
--
ALTER TABLE `community_pending_repayments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_customer` (`customer_id`);

--
-- Indexes for table `community_products`
--
ALTER TABLE `community_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type` (`product_type`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `community_rate_limits`
--
ALTER TABLE `community_rate_limits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_action_id` (`action_type`,`identifier`),
  ADD KEY `idx_time` (`attempted_at`);

--
-- Indexes for table `community_savings`
--
ALTER TABLE `community_savings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customer` (`customer_id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_ref` (`reference_code`);

--
-- Indexes for table `community_security_log`
--
ALTER TABLE `community_security_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_event` (`event_type`),
  ADD KEY `idx_customer` (`customer_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `community_withdrawal_requests`
--
ALTER TABLE `community_withdrawal_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customer` (`customer_id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_ref` (`reference_code`);

--
-- Indexes for table `loan_repayments`
--
ALTER TABLE `loan_repayments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_loan` (`loan_id`,`loan_type`),
  ADD KEY `idx_member` (`member_id`),
  ADD KEY `idx_date` (`repayment_date`),
  ADD KEY `fk_repayment_recorder` (`recorded_by`);

--
-- Indexes for table `loan_votes`
--
ALTER TABLE `loan_votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_vote` (`loan_id`,`loan_type`,`voter_id`),
  ADD KEY `idx_loan` (`loan_id`,`loan_type`),
  ADD KEY `idx_voter` (`voter_id`);

--
-- Indexes for table `meetings`
--
ALTER TABLE `meetings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_date` (`meeting_date`),
  ADD KEY `idx_type` (`meeting_type`),
  ADD KEY `fk_meeting_creator` (`created_by`);

--
-- Indexes for table `meeting_attendance`
--
ALTER TABLE `meeting_attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`meeting_id`,`member_id`),
  ADD KEY `idx_meeting` (`meeting_id`),
  ADD KEY `idx_member` (`member_id`);

--
-- Indexes for table `meeting_minutes`
--
ALTER TABLE `meeting_minutes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `meeting_date` (`meeting_date`),
  ADD KEY `recorder_id` (`recorder_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_national_id` (`national_id`),
  ADD UNIQUE KEY `unique_phone` (`phone`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `normal_loans`
--
ALTER TABLE `normal_loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_member` (`member_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_approved_by` (`approved_by`);

--
-- Indexes for table `normal_savings`
--
ALTER TABLE `normal_savings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_member` (`member_id`),
  ADD KEY `idx_date` (`transaction_date`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_phone` (`phone`);

--
-- Indexes for table `pending_payments`
--
ALTER TABLE `pending_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_checkout` (`checkout_request_id`),
  ADD KEY `idx_member` (`member_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `pending_registrations`
--
ALTER TABLE `pending_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_phone` (`phone`),
  ADD KEY `idx_national_id` (`national_id`);

--
-- Indexes for table `share_out_history`
--
ALTER TABLE `share_out_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `processed_by` (`processed_by`);

--
-- Indexes for table `sharing_out`
--
ALTER TABLE `sharing_out`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_member` (`member_id`),
  ADD KEY `idx_type` (`share_type`),
  ADD KEY `idx_period` (`period`);

--
-- Indexes for table `table_banking_loans`
--
ALTER TABLE `table_banking_loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_member` (`member_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `fk_tb_loan_approver` (`approved_by`);

--
-- Indexes for table `table_banking_shares`
--
ALTER TABLE `table_banking_shares`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_member` (`member_id`),
  ADD KEY `idx_date` (`transaction_date`);

--
-- Indexes for table `uwezo_loans`
--
ALTER TABLE `uwezo_loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_member` (`member_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `fk_uwezo_loan_approver` (`approved_by`);

--
-- Indexes for table `weekly_contributions`
--
ALTER TABLE `weekly_contributions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_member_week` (`member_id`,`year`,`week_number`),
  ADD KEY `idx_week` (`year`,`week_number`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `welfare_contributions`
--
ALTER TABLE `welfare_contributions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_member` (`member_id`),
  ADD KEY `idx_date` (`contribution_date`);

--
-- Indexes for table `welfare_requests`
--
ALTER TABLE `welfare_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_member` (`member_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `fk_welfare_approver` (`approved_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT for table `community_audit_logs`
--
ALTER TABLE `community_audit_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `community_customers`
--
ALTER TABLE `community_customers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `community_loans`
--
ALTER TABLE `community_loans`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `community_loan_approvals`
--
ALTER TABLE `community_loan_approvals`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `community_loan_repayments`
--
ALTER TABLE `community_loan_repayments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `community_login_attempts`
--
ALTER TABLE `community_login_attempts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `community_password_resets`
--
ALTER TABLE `community_password_resets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `community_pending_deposits`
--
ALTER TABLE `community_pending_deposits`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `community_pending_registrations`
--
ALTER TABLE `community_pending_registrations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `community_pending_repayments`
--
ALTER TABLE `community_pending_repayments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `community_products`
--
ALTER TABLE `community_products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `community_rate_limits`
--
ALTER TABLE `community_rate_limits`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `community_savings`
--
ALTER TABLE `community_savings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `community_security_log`
--
ALTER TABLE `community_security_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `community_withdrawal_requests`
--
ALTER TABLE `community_withdrawal_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_repayments`
--
ALTER TABLE `loan_repayments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `loan_votes`
--
ALTER TABLE `loan_votes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `meetings`
--
ALTER TABLE `meetings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meeting_attendance`
--
ALTER TABLE `meeting_attendance`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meeting_minutes`
--
ALTER TABLE `meeting_minutes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `normal_loans`
--
ALTER TABLE `normal_loans`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `normal_savings`
--
ALTER TABLE `normal_savings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pending_payments`
--
ALTER TABLE `pending_payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `pending_registrations`
--
ALTER TABLE `pending_registrations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `share_out_history`
--
ALTER TABLE `share_out_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `sharing_out`
--
ALTER TABLE `sharing_out`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `table_banking_loans`
--
ALTER TABLE `table_banking_loans`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `table_banking_shares`
--
ALTER TABLE `table_banking_shares`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `uwezo_loans`
--
ALTER TABLE `uwezo_loans`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `weekly_contributions`
--
ALTER TABLE `weekly_contributions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `welfare_contributions`
--
ALTER TABLE `welfare_contributions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `welfare_requests`
--
ALTER TABLE `welfare_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Structure for view `community_finance_summary`
--
DROP TABLE IF EXISTS `community_finance_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`grtzpvyr`@`localhost` SQL SECURITY DEFINER VIEW `community_finance_summary`  AS SELECT (select coalesce(sum((case when (`community_savings`.`transaction_type` = 'deposit') then `community_savings`.`amount` else -(`community_savings`.`amount`) end)),0) from `community_savings`) AS `total_savings`, (select coalesce(sum(`community_loans`.`amount`),0) from `community_loans` where (`community_loans`.`status` in ('approved','completed','defaulted'))) AS `total_loans_issued`, (select coalesce(sum(`community_loans`.`interest_amount`),0) from `community_loans` where (`community_loans`.`status` in ('approved','completed','defaulted'))) AS `total_interest_earned`, (select coalesce(sum(`community_loans`.`penalty_accrued`),0) from `community_loans` where (`community_loans`.`penalty_accrued` > 0)) AS `total_penalties_collected`, (select count(0) from `community_loans` where (`community_loans`.`status` = 'approved')) AS `active_loans_count`, (select count(0) from `community_loans` where (`community_loans`.`status` = 'defaulted')) AS `defaulted_loans_count` ;

-- --------------------------------------------------------

--
-- Structure for view `member_financial_summary`
--
DROP TABLE IF EXISTS `member_financial_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`grtzpvyr`@`localhost` SQL SECURITY DEFINER VIEW `member_financial_summary`  AS SELECT `m`.`id` AS `id`, (coalesce((select sum(`normal_savings`.`amount`) from `normal_savings` where ((`normal_savings`.`member_id` = `m`.`id`) and (`normal_savings`.`transaction_type` = 'deposit'))),0) - coalesce((select sum(`normal_savings`.`amount`) from `normal_savings` where ((`normal_savings`.`member_id` = `m`.`id`) and (`normal_savings`.`transaction_type` = 'withdrawal'))),0)) AS `normal_savings_balance`, (coalesce((select sum(`table_banking_shares`.`amount`) from `table_banking_shares` where ((`table_banking_shares`.`member_id` = `m`.`id`) and (`table_banking_shares`.`transaction_type` = 'share'))),0) - coalesce((select sum(`table_banking_shares`.`amount`) from `table_banking_shares` where ((`table_banking_shares`.`member_id` = `m`.`id`) and (`table_banking_shares`.`transaction_type` = 'withdrawal'))),0)) AS `table_banking_balance`, coalesce((select sum(`welfare_contributions`.`amount`) from `welfare_contributions` where (`welfare_contributions`.`member_id` = `m`.`id`)),0) AS `welfare_balance`, coalesce((select sum(`weekly_contributions`.`amount`) from `weekly_contributions` where ((`weekly_contributions`.`member_id` = `m`.`id`) and (`weekly_contributions`.`status` = 'paid'))),0) AS `total_weekly_paid`, (coalesce((select sum(`normal_loans`.`amount`) from `normal_loans` where ((`normal_loans`.`member_id` = `m`.`id`) and (`normal_loans`.`status` in ('approved','active')))),0) - coalesce((select sum(`loan_repayments`.`amount`) from `loan_repayments` where ((`loan_repayments`.`member_id` = `m`.`id`) and (`loan_repayments`.`loan_type` = 'normal'))),0)) AS `normal_loans_balance`, (coalesce((select sum(`table_banking_loans`.`amount`) from `table_banking_loans` where ((`table_banking_loans`.`member_id` = `m`.`id`) and (`table_banking_loans`.`status` in ('approved','active')))),0) - coalesce((select sum(`loan_repayments`.`amount`) from `loan_repayments` where ((`loan_repayments`.`member_id` = `m`.`id`) and (`loan_repayments`.`loan_type` = 'table_banking'))),0)) AS `table_banking_loans_balance`, (coalesce((select sum(`uwezo_loans`.`amount`) from `uwezo_loans` where ((`uwezo_loans`.`member_id` = `m`.`id`) and (`uwezo_loans`.`status` in ('approved','active')))),0) - coalesce((select sum(`loan_repayments`.`amount`) from `loan_repayments` where ((`loan_repayments`.`member_id` = `m`.`id`) and (`loan_repayments`.`loan_type` = 'uwezo'))),0)) AS `uwezo_loans_balance`, (coalesce((select sum(`weekly_contributions`.`amount`) from `weekly_contributions` where ((`weekly_contributions`.`member_id` = `m`.`id`) and (`weekly_contributions`.`status` = 'paid'))),0) + (coalesce((select sum(`normal_savings`.`amount`) from `normal_savings` where ((`normal_savings`.`member_id` = `m`.`id`) and (`normal_savings`.`transaction_type` = 'deposit'))),0) - coalesce((select sum(`normal_savings`.`amount`) from `normal_savings` where ((`normal_savings`.`member_id` = `m`.`id`) and (`normal_savings`.`transaction_type` = 'withdrawal'))),0))) AS `total_dividend_weight` FROM `members` AS `m` ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `fk_log_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`);

--
-- Constraints for table `community_loans`
--
ALTER TABLE `community_loans`
  ADD CONSTRAINT `community_loans_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `community_customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `community_loans_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `community_products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `community_loan_approvals`
--
ALTER TABLE `community_loan_approvals`
  ADD CONSTRAINT `community_loan_approvals_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `community_loans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `community_loan_repayments`
--
ALTER TABLE `community_loan_repayments`
  ADD CONSTRAINT `community_loan_repayments_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `community_loans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `community_password_resets`
--
ALTER TABLE `community_password_resets`
  ADD CONSTRAINT `community_password_resets_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `community_customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `community_savings`
--
ALTER TABLE `community_savings`
  ADD CONSTRAINT `community_savings_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `community_customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `community_savings_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `community_products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `community_withdrawal_requests`
--
ALTER TABLE `community_withdrawal_requests`
  ADD CONSTRAINT `community_withdrawal_requests_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `community_customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `community_withdrawal_requests_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `community_products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `loan_repayments`
--
ALTER TABLE `loan_repayments`
  ADD CONSTRAINT `fk_repayment_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_repayment_recorder` FOREIGN KEY (`recorded_by`) REFERENCES `members` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `loan_votes`
--
ALTER TABLE `loan_votes`
  ADD CONSTRAINT `fk_vote_member` FOREIGN KEY (`voter_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `meetings`
--
ALTER TABLE `meetings`
  ADD CONSTRAINT `fk_meeting_creator` FOREIGN KEY (`created_by`) REFERENCES `members` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `meeting_attendance`
--
ALTER TABLE `meeting_attendance`
  ADD CONSTRAINT `fk_attendance_meeting` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_attendance_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `meeting_minutes`
--
ALTER TABLE `meeting_minutes`
  ADD CONSTRAINT `meeting_minutes_ibfk_1` FOREIGN KEY (`recorder_id`) REFERENCES `members` (`id`);

--
-- Constraints for table `normal_loans`
--
ALTER TABLE `normal_loans`
  ADD CONSTRAINT `fk_normal_loan_approver` FOREIGN KEY (`approved_by`) REFERENCES `members` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_normal_loan_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `normal_savings`
--
ALTER TABLE `normal_savings`
  ADD CONSTRAINT `fk_normal_savings_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pending_payments`
--
ALTER TABLE `pending_payments`
  ADD CONSTRAINT `fk_payment_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `share_out_history`
--
ALTER TABLE `share_out_history`
  ADD CONSTRAINT `share_out_history_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
  ADD CONSTRAINT `share_out_history_ibfk_2` FOREIGN KEY (`processed_by`) REFERENCES `members` (`id`);

--
-- Constraints for table `sharing_out`
--
ALTER TABLE `sharing_out`
  ADD CONSTRAINT `fk_share_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `table_banking_loans`
--
ALTER TABLE `table_banking_loans`
  ADD CONSTRAINT `fk_tb_loan_approver` FOREIGN KEY (`approved_by`) REFERENCES `members` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tb_loan_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `table_banking_shares`
--
ALTER TABLE `table_banking_shares`
  ADD CONSTRAINT `fk_tb_shares_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `uwezo_loans`
--
ALTER TABLE `uwezo_loans`
  ADD CONSTRAINT `fk_uwezo_loan_approver` FOREIGN KEY (`approved_by`) REFERENCES `members` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_uwezo_loan_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `weekly_contributions`
--
ALTER TABLE `weekly_contributions`
  ADD CONSTRAINT `fk_weekly_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `welfare_contributions`
--
ALTER TABLE `welfare_contributions`
  ADD CONSTRAINT `fk_welfare_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `welfare_requests`
--
ALTER TABLE `welfare_requests`
  ADD CONSTRAINT `fk_welfare_approver` FOREIGN KEY (`approved_by`) REFERENCES `members` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_welfare_req_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
