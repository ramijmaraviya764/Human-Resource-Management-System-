-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 03, 2026 at 11:20 AM
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
-- Database: `HR`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `emp_id` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `punch_in_time` time DEFAULT NULL,
  `punch_out_time` time DEFAULT NULL,
  `total_hours` decimal(5,2) DEFAULT NULL,
  `status` enum('present','absent','leave','half_day') NOT NULL DEFAULT 'present',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `emp_id`, `date`, `punch_in_time`, `punch_out_time`, `total_hours`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'EMP20260001', '2026-01-03', '09:01:00', '18:00:00', 8.99, 'present', 'On time', '2026-01-03 08:37:14', '2026-01-03 08:58:38'),
(2, 'EMP20260002', '2026-01-02', '09:45:00', '18:05:00', 8.33, 'half_day', 'Traffic delay', '2026-01-03 08:37:14', '2026-01-03 08:37:14'),
(3, 'EMP20260003', '2026-01-02', NULL, NULL, 0.00, 'absent', 'No punch recorded', '2026-01-03 08:37:14', '2026-01-03 08:37:14'),
(4, 'EMP20260004', '2026-01-02', '09:10:00', '17:45:00', 8.58, 'present', 'Completed client work', '2026-01-03 08:37:14', '2026-01-03 08:37:14'),
(5, 'EMP20260005', '2026-01-02', '10:00:00', '16:30:00', 6.50, 'half_day', 'Left early for appointment', '2026-01-03 08:37:14', '2026-01-03 08:37:14'),
(6, 'EMP20260006', '2026-01-02', NULL, NULL, 0.00, 'leave', 'Sick leave', '2026-01-03 08:37:14', '2026-01-03 08:37:14'),
(7, 'EMP20260007', '2026-01-02', '09:20:00', '18:10:00', 8.83, 'present', 'Attended marketing meeting', '2026-01-03 08:37:14', '2026-01-03 08:37:14'),
(8, 'EMP20260008', '2026-01-02', '09:00:00', '17:50:00', 8.83, 'present', 'Regular shift', '2026-01-03 08:37:14', '2026-01-03 08:37:14'),
(9, 'EMP20260009', '2026-01-02', '09:30:00', '18:40:00', 9.17, 'present', 'Meeting extended', '2026-01-03 08:37:14', '2026-01-03 08:37:14'),
(10, 'EMP20260010', '2026-01-02', '08:55:00', '17:55:00', 9.00, 'present', 'Excellent performance', '2026-01-03 08:37:14', '2026-01-03 08:37:14');

-- --------------------------------------------------------

--
-- Table structure for table `bank_details`
--

CREATE TABLE `bank_details` (
  `id` int(11) NOT NULL,
  `emp_id` varchar(50) NOT NULL,
  `account_holder_name` varchar(255) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `ifsc_code` varchar(20) NOT NULL,
  `branch_name` varchar(255) DEFAULT NULL,
  `upi_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_img` varchar(255) DEFAULT NULL,
  `department` varchar(100) NOT NULL,
  `salary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `joining_date` date NOT NULL,
  `emergency_contact` varchar(20) DEFAULT NULL,
  `blood_group` varchar(5) DEFAULT NULL,
  `status` enum('active','inactive','terminated') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `basic_salary` decimal(10,2) DEFAULT 0.00,
  `transport_allowance` decimal(10,2) DEFAULT 0.00,
  `special_allowance` decimal(10,2) DEFAULT 0.00,
  `pf_deduction` decimal(10,2) DEFAULT 0.00,
  `tax_deduction` decimal(10,2) DEFAULT 0.00,
  `tds_deduction` decimal(10,2) DEFAULT 0.00,
  `bank_account` varchar(50) DEFAULT NULL,
  `bank_ifsc` varchar(20) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `account_holder_name` varchar(255) DEFAULT NULL,
  `upi_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `position`, `phone`, `email`, `user_id`, `password`, `user_img`, `department`, `salary`, `address`, `date_of_birth`, `gender`, `joining_date`, `emergency_contact`, `blood_group`, `status`, `created_at`, `updated_at`, `basic_salary`, `transport_allowance`, `special_allowance`, `pf_deduction`, `tax_deduction`, `tds_deduction`, `bank_account`, `bank_ifsc`, `bank_name`, `account_holder_name`, `upi_id`) VALUES
(1, 'Aarav Patel', 'Software Engineer', '+91 9876543210', 'aarav.patel@example.com', 'EMP20260001', '$2y$10$Fz3Z0CzO6Z2uMh7Kw/7RAOiP1WlqZ9T4XOSDTrZbfn8jIQlxGJ4US', NULL, 'Engineering', 65000.00, 'Ahmedabad, Gujarat', '1996-05-10', 'Male', '2024-04-15', '+91 9823045612', 'B+', 'active', '2026-01-03 08:39:14', '2026-01-03 08:39:14', 50000.00, 3000.00, 12000.00, 1800.00, 2200.00, 1000.00, '123456789012', 'HDFC0001234', 'HDFC Bank', 'Aarav Patel', 'aarav@ybl'),
(2, 'Neha Sharma', 'HR Manager', '+91 9823004567', 'neha.sharma@example.com', 'EMP20260002', '$2y$10$V7LPzZJYxFCEfXyM6xXoyO8qHVsoJkJw.b61PR20sXSM6bdbzV8TC', NULL, 'Human Resources', 78000.00, 'Mumbai, Maharashtra', '1992-09-25', 'Female', '2022-01-01', '+91 9867001254', 'A+', 'active', '2026-01-03 08:39:14', '2026-01-03 08:39:14', 60000.00, 4000.00, 15000.00, 2500.00, 3000.00, 1500.00, '987654321987', 'ICIC0005678', 'ICICI Bank', 'Neha Sharma', 'neha@okicici'),
(3, 'Rohan Verma', 'Sales Executive', '+91 9876012345', 'rohan.verma@example.com', 'EMP20260003', '$2y$10$kUhCFlXjBo9ytI.qy9RKIeKqJ6U.0ERtQ4VhfbN2B17vRru6oVb/G', NULL, 'Sales', 50000.00, 'Delhi, India', '1998-02-14', 'Male', '2023-08-01', '+91 9812004421', 'O+', 'active', '2026-01-03 08:39:14', '2026-01-03 08:39:14', 38000.00, 2500.00, 8000.00, 1500.00, 1000.00, 1000.00, '456789123456', 'SBI0005566', 'SBI Bank', 'Rohan Verma', 'rohan@okaxis'),
(4, 'Simran Kaur', 'Graphic Designer', '+91 9845098761', 'simran.kaur@example.com', 'EMP20260004', '$2y$10$85dsTfKyo1HqPr2Qk3Rz6ebRh7LBiH.ZpOqgwdvQ5m3DQHx.1b8Ay', NULL, 'Design', 60000.00, 'Ludhiana, Punjab', '1997-11-30', 'Female', '2023-07-12', '+91 9876009876', 'AB+', 'active', '2026-01-03 08:39:14', '2026-01-03 08:39:14', 45000.00, 3000.00, 10000.00, 1800.00, 1200.00, 1000.00, '321654987012', 'AXIS0004321', 'Axis Bank', 'Simran Kaur', 'simran@okaxis'),
(5, 'Vivek Singh', 'System Administrator', '+91 9811122233', 'vivek.singh@example.com', 'EMP20260005', '$2y$10$kHcvBRv2w7dmNSS1OZg9GuoPG1oxqPevuMJskks2Z4NHfH3aQv0li', NULL, 'IT Support', 55000.00, 'Lucknow, UP', '1995-03-18', 'Male', '2024-01-10', '+91 9817654321', 'O-', 'active', '2026-01-03 08:39:14', '2026-01-03 08:39:14', 42000.00, 2000.00, 8000.00, 1500.00, 1000.00, 500.00, '789654123000', 'PNB0009876', 'PNB Bank', 'Vivek Singh', 'vivek@ybl'),
(6, 'Priya Nair', 'Accountant', '+91 9876099990', 'priya.nair@example.com', 'EMP20260006', '$2y$10$y7o5e2NoJqS.MW/5IYgT8OiTvexAJ9Oe/HRFGbGmDqTX9Uqho0I1W', NULL, 'Finance', 70000.00, 'Kochi, Kerala', '1993-12-05', 'Female', '2023-02-01', '+91 9800223344', 'A-', 'active', '2026-01-03 08:39:14', '2026-01-03 08:39:14', 54000.00, 3000.00, 10000.00, 2000.00, 1500.00, 1500.00, '852147963258', 'KKBK0004444', 'Kotak Bank', 'Priya Nair', 'priya@okicici'),
(7, 'Arjun Reddy', 'Marketing Specialist', '+91 9833011223', 'arjun.reddy@example.com', 'EMP20260007', '$2y$10$4SMdZL4cM2q6aMvz1DJZse3UQ6s5Kh/fgHnMMaTk4DqvbD3Wv8XfW', NULL, 'Marketing', 62000.00, 'Hyderabad, Telangana', '1994-10-20', 'Male', '2023-09-25', '+91 9912345678', 'B-', 'active', '2026-01-03 08:39:14', '2026-01-03 08:39:14', 48000.00, 3000.00, 10000.00, 1500.00, 1200.00, 1300.00, '963258741369', 'YESB0007788', 'Yes Bank', 'Arjun Reddy', 'arjun@okyes'),
(8, 'Ananya Gupta', 'Customer Support Executive', '+91 9812223344', 'ananya.gupta@example.com', 'EMP20260008', '$2y$10$Ng6U2QYdW0VLb5vWmWcB5eZ3Cmz2v6sXGJzB8U3zWqRwmtYqL0ClS', NULL, 'Customer Support', 48000.00, 'Bhopal, MP', '1999-04-28', 'Female', '2024-03-05', '+91 9844001133', 'AB-', 'active', '2026-01-03 08:39:14', '2026-01-03 08:39:14', 35000.00, 2000.00, 8000.00, 1500.00, 1000.00, 500.00, '741258963000', 'UBIN0009988', 'Union Bank', 'Ananya Gupta', 'ananya@okaxis'),
(9, 'Karan Mehta', 'Operations Manager', '+91 9810088776', 'karan.mehta@example.com', 'EMP20260009', '$2y$10$Po/VluGujgK8xxWqG5bBSOUBmWb1t1wvR5p9wEzySrB7ZyO.YS/h2', NULL, 'Operations', 85000.00, 'Surat, Gujarat', '1988-07-16', 'Male', '2021-10-01', '+91 9822113344', 'B+', 'active', '2026-01-03 08:39:14', '2026-01-03 08:39:14', 65000.00, 4000.00, 12000.00, 3000.00, 2000.00, 1000.00, '369852147000', 'HDFC0001122', 'HDFC Bank', 'Karan Mehta', 'karan@ybl'),
(10, 'Meera Desai', 'Frontend Developer', '+91 9822445566', 'meera.desai@example.com', 'EMP20260010', '$2y$10$W2A7g/NsBvG54FsVmIRw2OBK02P8bOEfAbzqVnVG8DqZf.mCXL14a', NULL, 'Engineering', 64000.00, 'Pune, Maharashtra', '1996-06-22', 'Female', '2023-11-10', '+91 9876009988', 'O+', 'active', '2026-01-03 08:39:14', '2026-01-03 08:39:14', 48000.00, 3000.00, 11000.00, 1500.00, 1500.00, 1000.00, '147258369000', 'ICIC0006677', 'ICICI Bank', 'Meera Desai', 'meera@okicici'),
(11, 'ramij maraviya', 'Cyber', '8511895114', 'ramij.maraviya1382525@marwadiuniversity.ac.in', 'EMP20269643', '$2y$10$Q6k6u5v3bZ0o2h7g0Xy0sO0Qm9k5bY5X0e5Q9yZQ5k8Gq8l2QK3uW', NULL, 'Engineering', 50000.00, 'Rajkot', '2008-01-02', 'Male', '2026-01-03', '9725861612', 'A+', 'active', '2026-01-03 09:09:51', '2026-01-03 09:14:33', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `leave_records`
--

CREATE TABLE `leave_records` (
  `id` int(11) NOT NULL,
  `leave_id` int(11) NOT NULL,
  `employee_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `leave_type` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `days` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `processed_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_details`
--

CREATE TABLE `payment_details` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `payment_method` enum('bank','upi','cash','cheque') NOT NULL,
  `bank_account_number` varchar(50) DEFAULT NULL,
  `bank_ifsc` varchar(20) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `transfer_type` enum('NEFT','RTGS','IMPS') DEFAULT NULL,
  `bank_transaction_ref` varchar(255) DEFAULT NULL,
  `company_upi_id` varchar(100) DEFAULT NULL,
  `employee_upi_id` varchar(100) DEFAULT NULL,
  `upi_app` varchar(50) DEFAULT NULL,
  `upi_transaction_id` varchar(255) DEFAULT NULL,
  `cash_received_by` varchar(255) DEFAULT NULL,
  `cash_paid_by` varchar(255) DEFAULT NULL,
  `cash_receipt_number` varchar(100) DEFAULT NULL,
  `cheque_number` varchar(50) DEFAULT NULL,
  `cheque_date` date DEFAULT NULL,
  `cheque_bank` varchar(255) DEFAULT NULL,
  `cheque_branch` varchar(255) DEFAULT NULL,
  `cheque_payee` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` int(11) NOT NULL,
  `payroll_id` int(11) NOT NULL,
  `emp_id` varchar(50) NOT NULL,
  `payment_method` enum('bank','upi','cash','cheque') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_ref` varchar(100) DEFAULT NULL,
  `transfer_type` varchar(20) DEFAULT NULL,
  `upi_app` varchar(50) DEFAULT NULL,
  `cheque_number` varchar(50) DEFAULT NULL,
  `cheque_date` date DEFAULT NULL,
  `cash_receipt_number` varchar(50) DEFAULT NULL,
  `paid_by` varchar(100) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_transactions`
--

INSERT INTO `payment_transactions` (`id`, `payroll_id`, `emp_id`, `payment_method`, `amount`, `transaction_ref`, `transfer_type`, `upi_app`, `cheque_number`, `cheque_date`, `cash_receipt_number`, `paid_by`, `payment_date`, `remarks`, `created_at`) VALUES
(1, 3, 'EMP20260005', 'upi', 49000.00, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-03', 'hi', '2026-01-03 08:41:20');

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(11) NOT NULL,
  `emp_id` varchar(50) NOT NULL,
  `month` varchar(20) NOT NULL,
  `year` int(4) NOT NULL,
  `working_days` int(3) NOT NULL,
  `present_days` int(3) NOT NULL,
  `absent_days` int(3) NOT NULL,
  `paid_leave` int(3) DEFAULT 0,
  `unpaid_leave` int(3) DEFAULT 0,
  `attendance_percentage` decimal(5,2) NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `hra` decimal(10,2) NOT NULL,
  `transport_allowance` decimal(10,2) NOT NULL,
  `special_allowance` decimal(10,2) NOT NULL,
  `gross_salary` decimal(10,2) NOT NULL,
  `attendance_deduction` decimal(10,2) DEFAULT 0.00,
  `pf_deduction` decimal(10,2) NOT NULL,
  `tax_deduction` decimal(10,2) NOT NULL,
  `tds_deduction` decimal(10,2) NOT NULL,
  `total_deductions` decimal(10,2) NOT NULL,
  `net_salary` decimal(10,2) NOT NULL,
  `payment_method` enum('bank','upi','cash','cheque') NOT NULL,
  `payment_date` date DEFAULT NULL,
  `transaction_reference` varchar(100) DEFAULT NULL,
  `payment_remarks` text DEFAULT NULL,
  `status` enum('pending','paid','processing') NOT NULL DEFAULT 'pending',
  `payslip_sent` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`id`, `emp_id`, `month`, `year`, `working_days`, `present_days`, `absent_days`, `paid_leave`, `unpaid_leave`, `attendance_percentage`, `basic_salary`, `hra`, `transport_allowance`, `special_allowance`, `gross_salary`, `attendance_deduction`, `pf_deduction`, `tax_deduction`, `tds_deduction`, `total_deductions`, `net_salary`, `payment_method`, `payment_date`, `transaction_reference`, `payment_remarks`, `status`, `payslip_sent`, `created_at`, `updated_at`) VALUES
(1, 'EMP20260001', 'January', 2026, 22, 1, 0, 0, 0, 4.55, 50000.00, 0.00, 3000.00, 12000.00, 65000.00, 0.00, 1800.00, 2200.00, 1000.00, 5000.00, 60000.00, 'cash', '2026-01-03', '', 'pay', 'paid', 0, '2026-01-03 08:39:51', '2026-01-03 08:40:21'),
(3, 'EMP20260005', 'January', 2026, 22, 1, 0, 0, 0, 4.55, 42000.00, 0.00, 2000.00, 8000.00, 52000.00, 0.00, 1500.00, 1000.00, 500.00, 3000.00, 49000.00, 'upi', '2026-01-03', '', 'hi', 'paid', 1, '2026-01-03 08:41:20', '2026-01-03 08:41:25');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_transactions`
--

CREATE TABLE `payroll_transactions` (
  `id` int(11) NOT NULL,
  `emp_id` varchar(50) NOT NULL,
  `pay_period_month` int(2) NOT NULL,
  `pay_period_year` int(4) NOT NULL,
  `working_days` int(3) NOT NULL,
  `days_present` int(3) NOT NULL,
  `days_absent` int(3) NOT NULL,
  `paid_leave` int(3) DEFAULT 0,
  `unpaid_leave` int(3) DEFAULT 0,
  `attendance_percentage` decimal(5,2) NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `hra` decimal(10,2) NOT NULL,
  `transport_allowance` decimal(10,2) NOT NULL,
  `special_allowance` decimal(10,2) NOT NULL,
  `gross_salary` decimal(10,2) NOT NULL,
  `attendance_deduction` decimal(10,2) DEFAULT 0.00,
  `pf_deduction` decimal(10,2) NOT NULL,
  `professional_tax` decimal(10,2) NOT NULL,
  `tds` decimal(10,2) NOT NULL,
  `total_deductions` decimal(10,2) NOT NULL,
  `net_salary` decimal(10,2) NOT NULL,
  `payment_method` enum('bank','upi','cash','cheque') NOT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_status` enum('pending','processing','paid','failed') NOT NULL DEFAULT 'pending',
  `transaction_reference` varchar(255) DEFAULT NULL,
  `payment_remarks` text DEFAULT NULL,
  `payslip_generated` tinyint(1) DEFAULT 0,
  `payslip_sent` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_components`
--

CREATE TABLE `salary_components` (
  `id` int(11) NOT NULL,
  `emp_id` varchar(50) NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `hra` decimal(10,2) NOT NULL DEFAULT 0.00,
  `transport_allowance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `special_allowance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `pf_deduction` decimal(10,2) NOT NULL DEFAULT 0.00,
  `professional_tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tds` decimal(10,2) NOT NULL DEFAULT 0.00,
  `effective_from` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_payments`
--

CREATE TABLE `salary_payments` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `month` varchar(7) NOT NULL COMMENT 'Format: YYYY-MM',
  `base_salary` decimal(10,2) NOT NULL,
  `attendance_percentage` decimal(5,2) NOT NULL,
  `present_days` int(11) NOT NULL,
  `working_days` int(11) NOT NULL,
  `deduction_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_salary` decimal(10,2) NOT NULL,
  `payment_date` datetime NOT NULL,
  `processed_by` int(11) DEFAULT NULL COMMENT 'User ID who processed the payment',
  `payment_method` varchar(50) DEFAULT 'Bank Transfer',
  `transaction_id` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) DEFAULT current_timestamp(),
  `user_role` varchar(100) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `last_login` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `user_role`, `status`, `reset_token`, `token_expiry`, `last_login`, `created_at`) VALUES
(1, 'ramij', 'maraviyaramij@gmail.com', '8511895114', '$2y$10$7FGD8oESnD.r7KLfxdaZs.0zK6XTz5mjOObh6aHKdaZKZtaIvVGIG', 'Hr', 0, '', NULL, '', '2026-01-03 04:57:35'),
(2, 'test', 'test@gmail.com', '8511995114', '$2y$10$2zVPfuPr/vFqW1AfAApeZ.vEVfjj2NqXrWM0aAbXW.uHeP9ucUMoa', 'Hr', 0, '', NULL, '2026-01-03 14:45:35', '2026-01-03 05:05:51'),
(15, 'viren', 'kingvora40@gmail.com', '1234567890', '$2y$10$3CO/3xXoJ3Q.GlV3F9k0cOcZsuPXa.wAtVhtELTSpYE1uImUfX.fi', 'employee', 1, '39d8d768df91d2b573400687765559af64ab9fac647843c5219379715e2c0e59', '2026-01-04 15:49:47', NULL, '2026-01-03 06:09:54'),
(16, 'ramij maraviya', 'ramij.maraviya1382525@marwadiuniversity.ac.in', '8511895114', '$2y$10$2zVPfuPr/vFqW1AfAApeZ.vEVfjj2NqXrWM0aAbXW.uHeP9ucUMoa', 'employee', 1, '', NULL, '2026-01-03 14:51:57', '2026-01-03 09:09:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`emp_id`,`date`),
  ADD KEY `emp_id` (`emp_id`),
  ADD KEY `date` (`date`);

--
-- Indexes for table `bank_details`
--
ALTER TABLE `bank_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emp_id` (`emp_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `department` (`department`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `leave_records`
--
ALTER TABLE `leave_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_leave` (`leave_id`);

--
-- Indexes for table `payment_details`
--
ALTER TABLE `payment_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_id` (`payroll_id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_payroll` (`emp_id`,`month`,`year`),
  ADD KEY `emp_id` (`emp_id`),
  ADD KEY `month_year` (`month`,`year`);

--
-- Indexes for table `payroll_transactions`
--
ALTER TABLE `payroll_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emp_period_unique` (`emp_id`,`pay_period_month`,`pay_period_year`),
  ADD KEY `emp_id` (`emp_id`),
  ADD KEY `pay_period` (`pay_period_month`,`pay_period_year`),
  ADD KEY `payment_status` (`payment_status`);

--
-- Indexes for table `salary_components`
--
ALTER TABLE `salary_components`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- Indexes for table `salary_payments`
--
ALTER TABLE `salary_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_emp_month` (`emp_id`,`month`),
  ADD KEY `emp_id` (`emp_id`),
  ADD KEY `month` (`month`),
  ADD KEY `payment_date` (`payment_date`),
  ADD KEY `idx_emp_month` (`emp_id`,`month`),
  ADD KEY `idx_payment_date` (`payment_date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `bank_details`
--
ALTER TABLE `bank_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `leave_records`
--
ALTER TABLE `leave_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_details`
--
ALTER TABLE `payment_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payroll_transactions`
--
ALTER TABLE `payroll_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salary_components`
--
ALTER TABLE `salary_components`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salary_payments`
--
ALTER TABLE `salary_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bank_details`
--
ALTER TABLE `bank_details`
  ADD CONSTRAINT `bank_details_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_details`
--
ALTER TABLE `payment_details`
  ADD CONSTRAINT `payment_details_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `payroll_transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll_transactions`
--
ALTER TABLE `payroll_transactions`
  ADD CONSTRAINT `payroll_transactions_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `salary_components`
--
ALTER TABLE `salary_components`
  ADD CONSTRAINT `salary_components_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `salary_payments`
--
ALTER TABLE `salary_payments`
  ADD CONSTRAINT `fk_salary_emp` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
