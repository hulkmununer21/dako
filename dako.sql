-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 05, 2025 at 04:39 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dako`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int NOT NULL,
  `full_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'superadmin',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `full_name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'System Administrator', 'admin@dako.edu.ng', '0192023a7bbd73250516f069df18b500', 'superadmin', '2025-10-21 00:29:29');

-- --------------------------------------------------------

--
-- Table structure for table `admission_letters`
--

CREATE TABLE `admission_letters` (
  `letter_id` int NOT NULL,
  `candidate_type` enum('utme','dental') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `candidate_id` char(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `course_id` int DEFAULT NULL,
  `session` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `issued_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dental_applications`
--

CREATE TABLE `dental_applications` (
  `app_id` int NOT NULL,
  `dental_id` char(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `course_id` int DEFAULT NULL,
  `payment_status` enum('paid','unpaid') COLLATE utf8mb4_general_ci DEFAULT 'unpaid',
  `admission_status` enum('pending','admitted','rejected') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `acceptance_status` enum('unpaid','paid') COLLATE utf8mb4_general_ci DEFAULT 'unpaid',
  `session` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dental_candidates`
--

CREATE TABLE `dental_candidates` (
  `dental_id` char(10) COLLATE utf8mb4_general_ci NOT NULL,
  `surname` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `middle_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dental_candidates`
--

INSERT INTO `dental_candidates` (`dental_id`, `surname`, `middle_name`, `first_name`, `email`, `password`, `created_at`) VALUES
('DEN1234567', 'Aliyu', 'Ibrahim', 'Musa', 'musa@example.com', 'eb7f9542101c6a94f27404fafc3efd53', '2025-10-21 00:29:31');

-- --------------------------------------------------------

--
-- Table structure for table `dental_courses`
--

CREATE TABLE `dental_courses` (
  `course_id` int NOT NULL,
  `course_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `course_duration` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `status` enum('active','inactive') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dental_courses`
--

INSERT INTO `dental_courses` (`course_id`, `course_name`, `course_duration`, `description`, `status`, `created_at`) VALUES
(1, 'Dental Surgery', '6 Years', 'Professional course in Dental Surgery', 'active', '2025-10-21 00:29:30'),
(2, 'Dental Nursing', '3 Years', 'Diploma in Dental Nursing', 'active', '2025-10-21 00:29:30'),
(3, 'Dental Therapy', '4 Years', 'Bachelor’s degree in Dental Therapy', 'active', '2025-10-21 00:29:30');

-- --------------------------------------------------------

--
-- Table structure for table `dental_documents`
--

CREATE TABLE `dental_documents` (
  `doc_id` int NOT NULL,
  `dental_id` char(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `doc_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `uploaded_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dental_education_background`
--

CREATE TABLE `dental_education_background` (
  `edu_id` int NOT NULL,
  `dental_id` char(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sitting` enum('1','2') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `exam_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `exam_year` int DEFAULT NULL,
  `exam_no` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `scratch_pin` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `scratch_serial` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subjects_json` json DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dental_parent_info`
--

CREATE TABLE `dental_parent_info` (
  `parent_id` int NOT NULL,
  `dental_id` char(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `guardian_name` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `occupation` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mother_name` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mother_occupation` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `guardian_address` text COLLATE utf8mb4_general_ci,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dental_personal_info`
--

CREATE TABLE `dental_personal_info` (
  `info_id` int NOT NULL,
  `dental_id` char(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gender` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `marital_status` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `blood_group` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `present_address` text COLLATE utf8mb4_general_ci,
  `permanent_address` text COLLATE utf8mb4_general_ci,
  `state` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lga` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `fee_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `candidate_type` enum('utme','dental','all') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fees`
--

INSERT INTO `fees` (`fee_id`, `name`, `amount`, `candidate_type`, `created_at`) VALUES
(1, 'UTME Application Fee', 2000.00, 'utme', '2025-10-21 00:29:32'),
(2, 'UTME Acceptance Fee', 15000.00, 'utme', '2025-10-21 00:29:32'),
(3, 'Dental Application Fee', 5000.00, 'dental', '2025-10-21 00:29:32'),
(4, 'Dental Acceptance Fee', 20000.00, 'dental', '2025-10-21 00:29:32');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notif_id` int NOT NULL,
  `sender_id` int DEFAULT NULL,
  `receiver_type` enum('utme','dental','all') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `receiver_id` char(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_general_ci,
  `delivery_mode` enum('dashboard','email','both') COLLATE utf8mb4_general_ci DEFAULT 'dashboard',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notif_id`, `sender_id`, `receiver_type`, `receiver_id`, `subject`, `message`, `delivery_mode`, `created_at`) VALUES
(1, 1, 'all', NULL, 'Portal Maintenance', 'The admissions portal will be down for maintenance on Saturday 10pm - 12am.', 'both', '2025-10-26 23:03:21'),
(2, 1, 'utme', 'UTM1234567', 'Document Missing', 'Dear candidate, we are missing your O-Level result. Please upload it via your dashboard -> Documents.', 'dashboard', '2025-10-26 23:03:21'),
(3, 1, 'utme', 'UTM7654321', 'Screening Invitation', 'You are invited for online screening. Check Online Screening section for details.', 'dashboard', '2025-10-26 23:03:21');

-- --------------------------------------------------------

--
-- Table structure for table `payment_gateways`
--

CREATE TABLE `payment_gateways` (
  `gateway_id` int NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `public_key` text COLLATE utf8mb4_general_ci,
  `secret_key` text COLLATE utf8mb4_general_ci,
  `callback_url` text COLLATE utf8mb4_general_ci,
  `status` enum('active','inactive') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_gateways`
--

INSERT INTO `payment_gateways` (`gateway_id`, `name`, `public_key`, `secret_key`, `callback_url`, `status`, `created_at`) VALUES
(1, 'Paystack', 'pk_test_xxxxxxxxxxxxxxx', 'sk_test_xxxxxxxxxxxxxxx', 'https://dako.edu.ng/paystack/callback', 'active', '2025-10-21 00:29:32'),
(2, 'Remita', 'remita_public_key', 'remita_secret_key', 'https://dako.edu.ng/remita/callback', 'active', '2025-10-21 00:29:32');

-- --------------------------------------------------------

--
-- Table structure for table `post_utme_json`
--

CREATE TABLE `post_utme_json` (
  `id` int NOT NULL,
  `utme_id` char(10) COLLATE utf8mb4_general_ci NOT NULL,
  `results` json NOT NULL,
  `session` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_utme_json`
--

INSERT INTO `post_utme_json` (`id`, `utme_id`, `results`, `session`, `created_at`) VALUES
(1, 'UTM1234567', '[{\"score\": 72, \"course\": \"Computer Science\", \"exam_date\": \"2025-09-01\"}, {\"score\": 68, \"course\": \"Microbiology\", \"exam_date\": \"2025-09-01\"}]', '2025/2026', '2025-10-26 14:46:48');

-- --------------------------------------------------------

--
-- Table structure for table `receipts_invoices`
--

CREATE TABLE `receipts_invoices` (
  `receipt_id` int NOT NULL,
  `candidate_type` enum('utme','dental') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `candidate_id` char(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_for` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `transaction_ref` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_gateway` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('success','failed','pending') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `utme_applications`
--

CREATE TABLE `utme_applications` (
  `app_id` int NOT NULL,
  `utme_id` char(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `course_id` int DEFAULT NULL,
  `payment_status` enum('paid','unpaid') COLLATE utf8mb4_general_ci DEFAULT 'unpaid',
  `admission_status` enum('pending','admitted','rejected') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `acceptance_status` enum('unpaid','paid') COLLATE utf8mb4_general_ci DEFAULT 'unpaid',
  `session` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `utme_candidates`
--

CREATE TABLE `utme_candidates` (
  `utme_id` char(10) COLLATE utf8mb4_general_ci NOT NULL,
  `surname` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `middle_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `utme_number` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `preferred_course_id` int DEFAULT NULL,
  `utme_score` int DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `eligibility_status` enum('eligible','ineligible','pending') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `email` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utme_candidates`
--

INSERT INTO `utme_candidates` (`utme_id`, `surname`, `middle_name`, `first_name`, `utme_number`, `preferred_course_id`, `utme_score`, `password`, `eligibility_status`, `email`, `created_at`) VALUES
('UTM1234567', 'Okafor', 'Chisom', 'Emeka', '2024123456', 2, 230, '$2a$12$1/sIeEDsjA.If2WAYlTXZ.mkrcggC/k3UzZfwJ4h9amkRvjgmsMrG', 'eligible', 'okafor@example.com', '2025-10-21 00:29:30');

-- --------------------------------------------------------

--
-- Table structure for table `utme_courses`
--

CREATE TABLE `utme_courses` (
  `course_id` int NOT NULL,
  `course_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `course_duration` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `status` enum('active','inactive') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utme_courses`
--

INSERT INTO `utme_courses` (`course_id`, `course_name`, `course_duration`, `description`, `status`, `created_at`) VALUES
(1, 'Medicine and Surgery', '6 Years', 'Undergraduate degree in Medicine', 'active', '2025-10-21 00:29:29'),
(2, 'Computer Science', '4 Years', 'Undergraduate degree in Computer Science', 'active', '2025-10-21 00:29:29'),
(3, 'Microbiology', '4 Years', 'Undergraduate degree in Microbiology', 'active', '2025-10-21 00:29:29');

-- --------------------------------------------------------

--
-- Table structure for table `utme_documents`
--

CREATE TABLE `utme_documents` (
  `doc_id` int NOT NULL,
  `utme_id` char(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `doc_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `uploaded_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `utme_education_background`
--

CREATE TABLE `utme_education_background` (
  `edu_id` int NOT NULL,
  `utme_id` char(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sitting` enum('1','2') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `exam_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `exam_year` int DEFAULT NULL,
  `exam_no` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `scratch_pin` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `scratch_serial` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subjects_json` json DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `utme_parent_info`
--

CREATE TABLE `utme_parent_info` (
  `parent_id` int NOT NULL,
  `utme_id` char(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `guardian_name` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `occupation` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mother_name` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mother_occupation` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `guardian_address` text COLLATE utf8mb4_general_ci,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utme_parent_info`
--

INSERT INTO `utme_parent_info` (`parent_id`, `utme_id`, `guardian_name`, `occupation`, `mother_name`, `mother_occupation`, `guardian_address`, `phone`, `created_at`) VALUES
(1, 'UTM1234567', 'ade', 'driver', 'kemi', 'trade', 'Minna, Bida Road, 23', '08025803285', '2025-10-27 00:41:02');

-- --------------------------------------------------------

--
-- Table structure for table `utme_personal_info`
--

CREATE TABLE `utme_personal_info` (
  `info_id` int NOT NULL,
  `utme_id` char(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gender` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `marital_status` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `blood_group` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `present_address` text COLLATE utf8mb4_general_ci,
  `permanent_address` text COLLATE utf8mb4_general_ci,
  `state` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lga` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utme_personal_info`
--

INSERT INTO `utme_personal_info` (`info_id`, `utme_id`, `dob`, `phone`, `gender`, `marital_status`, `blood_group`, `present_address`, `permanent_address`, `state`, `lga`, `created_at`) VALUES
(1, 'UTM1234567', '2025-10-21', '08025803285', 'Male', '', 'O', 'Minna, Bida Road', '23', 'Niger', 'bida', '2025-10-27 00:41:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `admission_letters`
--
ALTER TABLE `admission_letters`
  ADD PRIMARY KEY (`letter_id`);

--
-- Indexes for table `dental_applications`
--
ALTER TABLE `dental_applications`
  ADD PRIMARY KEY (`app_id`),
  ADD KEY `dental_id` (`dental_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `dental_candidates`
--
ALTER TABLE `dental_candidates`
  ADD PRIMARY KEY (`dental_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `dental_courses`
--
ALTER TABLE `dental_courses`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `dental_documents`
--
ALTER TABLE `dental_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `dental_id` (`dental_id`);

--
-- Indexes for table `dental_education_background`
--
ALTER TABLE `dental_education_background`
  ADD PRIMARY KEY (`edu_id`),
  ADD KEY `dental_id` (`dental_id`);

--
-- Indexes for table `dental_parent_info`
--
ALTER TABLE `dental_parent_info`
  ADD PRIMARY KEY (`parent_id`),
  ADD KEY `dental_id` (`dental_id`);

--
-- Indexes for table `dental_personal_info`
--
ALTER TABLE `dental_personal_info`
  ADD PRIMARY KEY (`info_id`),
  ADD KEY `dental_id` (`dental_id`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`fee_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notif_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `payment_gateways`
--
ALTER TABLE `payment_gateways`
  ADD PRIMARY KEY (`gateway_id`);

--
-- Indexes for table `post_utme_json`
--
ALTER TABLE `post_utme_json`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_utme_id_json` (`utme_id`);

--
-- Indexes for table `receipts_invoices`
--
ALTER TABLE `receipts_invoices`
  ADD PRIMARY KEY (`receipt_id`);

--
-- Indexes for table `utme_applications`
--
ALTER TABLE `utme_applications`
  ADD PRIMARY KEY (`app_id`),
  ADD KEY `utme_id` (`utme_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `utme_candidates`
--
ALTER TABLE `utme_candidates`
  ADD PRIMARY KEY (`utme_id`),
  ADD KEY `preferred_course_id` (`preferred_course_id`);

--
-- Indexes for table `utme_courses`
--
ALTER TABLE `utme_courses`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `utme_documents`
--
ALTER TABLE `utme_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `utme_id` (`utme_id`);

--
-- Indexes for table `utme_education_background`
--
ALTER TABLE `utme_education_background`
  ADD PRIMARY KEY (`edu_id`),
  ADD KEY `utme_id` (`utme_id`);

--
-- Indexes for table `utme_parent_info`
--
ALTER TABLE `utme_parent_info`
  ADD PRIMARY KEY (`parent_id`),
  ADD KEY `utme_id` (`utme_id`);

--
-- Indexes for table `utme_personal_info`
--
ALTER TABLE `utme_personal_info`
  ADD PRIMARY KEY (`info_id`),
  ADD KEY `utme_id` (`utme_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admission_letters`
--
ALTER TABLE `admission_letters`
  MODIFY `letter_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dental_applications`
--
ALTER TABLE `dental_applications`
  MODIFY `app_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dental_courses`
--
ALTER TABLE `dental_courses`
  MODIFY `course_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dental_documents`
--
ALTER TABLE `dental_documents`
  MODIFY `doc_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dental_education_background`
--
ALTER TABLE `dental_education_background`
  MODIFY `edu_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dental_parent_info`
--
ALTER TABLE `dental_parent_info`
  MODIFY `parent_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dental_personal_info`
--
ALTER TABLE `dental_personal_info`
  MODIFY `info_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `fee_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notif_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment_gateways`
--
ALTER TABLE `payment_gateways`
  MODIFY `gateway_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `post_utme_json`
--
ALTER TABLE `post_utme_json`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `receipts_invoices`
--
ALTER TABLE `receipts_invoices`
  MODIFY `receipt_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `utme_applications`
--
ALTER TABLE `utme_applications`
  MODIFY `app_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `utme_courses`
--
ALTER TABLE `utme_courses`
  MODIFY `course_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `utme_documents`
--
ALTER TABLE `utme_documents`
  MODIFY `doc_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `utme_education_background`
--
ALTER TABLE `utme_education_background`
  MODIFY `edu_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `utme_parent_info`
--
ALTER TABLE `utme_parent_info`
  MODIFY `parent_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `utme_personal_info`
--
ALTER TABLE `utme_personal_info`
  MODIFY `info_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dental_applications`
--
ALTER TABLE `dental_applications`
  ADD CONSTRAINT `dental_applications_ibfk_1` FOREIGN KEY (`dental_id`) REFERENCES `dental_candidates` (`dental_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dental_applications_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `dental_courses` (`course_id`);

--
-- Constraints for table `dental_documents`
--
ALTER TABLE `dental_documents`
  ADD CONSTRAINT `dental_documents_ibfk_1` FOREIGN KEY (`dental_id`) REFERENCES `dental_candidates` (`dental_id`) ON DELETE CASCADE;

--
-- Constraints for table `dental_education_background`
--
ALTER TABLE `dental_education_background`
  ADD CONSTRAINT `dental_education_background_ibfk_1` FOREIGN KEY (`dental_id`) REFERENCES `dental_candidates` (`dental_id`) ON DELETE CASCADE;

--
-- Constraints for table `dental_parent_info`
--
ALTER TABLE `dental_parent_info`
  ADD CONSTRAINT `dental_parent_info_ibfk_1` FOREIGN KEY (`dental_id`) REFERENCES `dental_candidates` (`dental_id`) ON DELETE CASCADE;

--
-- Constraints for table `dental_personal_info`
--
ALTER TABLE `dental_personal_info`
  ADD CONSTRAINT `dental_personal_info_ibfk_1` FOREIGN KEY (`dental_id`) REFERENCES `dental_candidates` (`dental_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `admins` (`admin_id`);

--
-- Constraints for table `post_utme_json`
--
ALTER TABLE `post_utme_json`
  ADD CONSTRAINT `post_utme_json_ibfk_1` FOREIGN KEY (`utme_id`) REFERENCES `utme_candidates` (`utme_id`) ON DELETE CASCADE;

--
-- Constraints for table `utme_applications`
--
ALTER TABLE `utme_applications`
  ADD CONSTRAINT `utme_applications_ibfk_1` FOREIGN KEY (`utme_id`) REFERENCES `utme_candidates` (`utme_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `utme_applications_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `utme_courses` (`course_id`);

--
-- Constraints for table `utme_candidates`
--
ALTER TABLE `utme_candidates`
  ADD CONSTRAINT `utme_candidates_ibfk_1` FOREIGN KEY (`preferred_course_id`) REFERENCES `utme_courses` (`course_id`);

--
-- Constraints for table `utme_documents`
--
ALTER TABLE `utme_documents`
  ADD CONSTRAINT `utme_documents_ibfk_1` FOREIGN KEY (`utme_id`) REFERENCES `utme_candidates` (`utme_id`) ON DELETE CASCADE;

--
-- Constraints for table `utme_education_background`
--
ALTER TABLE `utme_education_background`
  ADD CONSTRAINT `utme_education_background_ibfk_1` FOREIGN KEY (`utme_id`) REFERENCES `utme_candidates` (`utme_id`) ON DELETE CASCADE;

--
-- Constraints for table `utme_parent_info`
--
ALTER TABLE `utme_parent_info`
  ADD CONSTRAINT `utme_parent_info_ibfk_1` FOREIGN KEY (`utme_id`) REFERENCES `utme_candidates` (`utme_id`) ON DELETE CASCADE;

--
-- Constraints for table `utme_personal_info`
--
ALTER TABLE `utme_personal_info`
  ADD CONSTRAINT `utme_personal_info_ibfk_1` FOREIGN KEY (`utme_id`) REFERENCES `utme_candidates` (`utme_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
