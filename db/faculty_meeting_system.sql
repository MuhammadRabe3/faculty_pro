-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2023 at 01:08 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `faculty_meeting_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `application_data`
--

CREATE TABLE `application_data` (
  `app_id` tinyint(4) NOT NULL,
  `app_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Uni_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Faculty_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Program_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Faculty-Uni_logo` varchar(50) DEFAULT NULL,
  `Program_logo` varchar(50) DEFAULT NULL,
  `Faculty_Dean` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Post_grad_vice_dean` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `st_affairs_vice_dean` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Program_coordinator` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `application_data`
--

INSERT INTO `application_data` (`app_id`, `app_name`, `Uni_name`, `Faculty_name`, `Program_name`, `Faculty-Uni_logo`, `Program_logo`, `Faculty_Dean`, `Post_grad_vice_dean`, `st_affairs_vice_dean`, `Program_coordinator`) VALUES
(1, 'النظام الإلكتروني لإدارة موضوعات مجلس الكلية', 'جامعة حلوان', 'كلية التجارة وإدارة الأعمال', 'BIS برنامج نظم معلومات الأعمال', 'Facultylogo.jpg', 'program.png', 'أ.د. حسام الرفاعي', 'أ.د. هند عودة', 'أ.د. أماني فاخر', 'أ.م.د. رشا فرغلى');

-- --------------------------------------------------------

--
-- Table structure for table `p39_attendance`
--

CREATE TABLE `p39_attendance` (
  `user_id` smallint(5) UNSIGNED NOT NULL,
  `meeting_id` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `p39_dates`
--

CREATE TABLE `p39_dates` (
  `date_id` smallint(5) UNSIGNED NOT NULL,
  `month` tinyint(2) UNSIGNED DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `formation_id` smallint(5) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `p39_decision`
--

CREATE TABLE `p39_decision` (
  `decision_id` smallint(5) UNSIGNED NOT NULL,
  `decision_details` text CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `decision_type_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `subject_id` mediumint(8) UNSIGNED DEFAULT NULL,
  `needs_action` tinyint(3) UNSIGNED DEFAULT NULL,
  `action_to` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `is_action_done` tinyint(3) UNSIGNED DEFAULT NULL,
  `comments` text CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `added_by` smallint(5) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `p39_decision_attachment`
--

CREATE TABLE `p39_decision_attachment` (
  `attachment_id` smallint(5) UNSIGNED NOT NULL,
  `attachment_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `attachment_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `decision_id` smallint(5) UNSIGNED DEFAULT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `added_by` smallint(5) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `p39_decision_type`
--

CREATE TABLE `p39_decision_type` (
  `decision_type_id` tinyint(3) UNSIGNED NOT NULL,
  `decision_type_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `p39_decision_type`
--

INSERT INTO `p39_decision_type` (`decision_type_id`, `decision_type_name`) VALUES
(1, 'موافقة'),
(2, 'رفض'),
(3, 'تأجيل');

-- --------------------------------------------------------

--
-- Table structure for table `p39_department`
--

CREATE TABLE `p39_department` (
  `department_id` tinyint(3) UNSIGNED NOT NULL,
  `department_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `p39_department`
--

INSERT INTO `p39_department` (`department_id`, `department_name`) VALUES
(1, 'قسم المحاسبة'),
(2, 'قسم إدارة الأعمال'),
(3, 'قسم الاقتصاد والتجارة الخارجية'),
(4, 'قسم الإحصاء'),
(5, 'قسم العلوم السياسية'),
(6, 'قسم نظم المعلومات'),
(7, 'عضو خارجي'),
(8, 'إداري');

-- --------------------------------------------------------

--
-- Table structure for table `p39_formation`
--

CREATE TABLE `p39_formation` (
  `formation_id` smallint(5) UNSIGNED NOT NULL,
  `formation_number` mediumint(8) UNSIGNED DEFAULT NULL,
  `start_year` year(4) DEFAULT NULL,
  `is_current` tinyint(3) UNSIGNED DEFAULT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `added_by` smallint(5) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `p39_formation_user`
--

CREATE TABLE `p39_formation_user` (
  `formation_id` smallint(5) UNSIGNED NOT NULL,
  `user_id` smallint(5) UNSIGNED NOT NULL,
  `job_title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `p39_job_rank`
--

CREATE TABLE `p39_job_rank` (
  `job_rank_id` tinyint(3) UNSIGNED NOT NULL,
  `job_rank_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `p39_job_rank`
--

INSERT INTO `p39_job_rank` (`job_rank_id`, `job_rank_name`) VALUES
(1, 'أستاذ'),
(2, 'أستاذ مساعد'),
(3, 'مدرس'),
(4, 'خبير'),
(5, 'إداري');

-- --------------------------------------------------------

--
-- Table structure for table `p39_job_type`
--

CREATE TABLE `p39_job_type` (
  `job_type_id` tinyint(3) UNSIGNED NOT NULL,
  `job_type_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `p39_job_type`
--

INSERT INTO `p39_job_type` (`job_type_id`, `job_type_name`) VALUES
(1, 'عميد'),
(2, 'وكيل الكلية'),
(3, 'رئيس قسم'),
(4, 'عضو هيئة تدريس'),
(5, 'إداري'),
(6, 'عضو حارجي'),
(7, 'أخرى');

-- --------------------------------------------------------

--
-- Table structure for table `p39_meeting`
--

CREATE TABLE `p39_meeting` (
  `meeting_id` smallint(5) UNSIGNED NOT NULL,
  `meeting_number` mediumint(8) UNSIGNED DEFAULT NULL,
  `meeting_month` smallint(5) UNSIGNED DEFAULT NULL,
  `meeting_year` year(4) DEFAULT NULL,
  `meeting_date` date DEFAULT NULL,
  `is_current` tinyint(3) UNSIGNED DEFAULT 1,
  `status` enum('pending','confirmed','finished') DEFAULT 'pending',
  `is_shown` tinyint(3) UNSIGNED DEFAULT 0,
  `formation_id` smallint(5) UNSIGNED DEFAULT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `added_by` smallint(5) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `p39_meeting_attachment`
--

CREATE TABLE `p39_meeting_attachment` (
  `attachment_id` smallint(5) UNSIGNED NOT NULL,
  `attachment_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `attachment_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `meeting_id` smallint(5) UNSIGNED DEFAULT NULL,
  `is_final` tinyint(3) UNSIGNED DEFAULT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `added_by` smallint(5) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `p39_meeting_transaction`
--

CREATE TABLE `p39_meeting_transaction` (
  `transaction_id` smallint(5) UNSIGNED NOT NULL,
  `transaction_type` enum('Edit','Delete') DEFAULT NULL,
  `meeting_id` smallint(5) UNSIGNED DEFAULT NULL,
  `old_row` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `new_row` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `made_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `made_by` smallint(5) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `p39_subject`
--

CREATE TABLE `p39_subject` (
  `order_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `subject_id` mediumint(8) UNSIGNED NOT NULL,
  `subject_number` mediumint(8) UNSIGNED DEFAULT NULL,
  `subject_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `subject_details` text CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `subject_type_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `meeting_id` smallint(5) UNSIGNED DEFAULT NULL,
  `comments` text CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `added_by` smallint(5) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `p39_subject_attachment`
--

CREATE TABLE `p39_subject_attachment` (
  `attachment_id` smallint(5) UNSIGNED NOT NULL,
  `attachment_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `attachment_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `subject_id` mediumint(8) UNSIGNED DEFAULT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `added_by` smallint(5) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `p39_subject_picture`
--

CREATE TABLE `p39_subject_picture` (
  `picture_id` smallint(5) UNSIGNED NOT NULL,
  `picture_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `picture_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `subject_id` mediumint(8) UNSIGNED DEFAULT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `added_by` smallint(5) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `p39_subject_transaction`
--

CREATE TABLE `p39_subject_transaction` (
  `transaction_id` smallint(5) UNSIGNED NOT NULL,
  `transaction_type` enum('Edit','Delete') DEFAULT NULL,
  `subject_id` mediumint(8) UNSIGNED DEFAULT NULL,
  `old_row` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `new_row` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `made_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `made_by` smallint(5) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `p39_subject_type`
--

CREATE TABLE `p39_subject_type` (
  `subject_type_id` tinyint(3) UNSIGNED NOT NULL,
  `subject_type_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `p39_subject_type`
--

INSERT INTO `p39_subject_type` (`subject_type_id`, `subject_type_name`) VALUES
(1, 'شئون التعليم والطلاب'),
(2, 'شئون الدراسات العليا والبحوث'),
(3, 'شئون خدمة المجتمع وتنمية البيئة'),
(4, 'قسم المحاسبة'),
(5, 'قسم إدارة الأعمال'),
(6, 'قسم الاقتصاد والتجارة الخارجية'),
(7, 'قسم الإحصاء'),
(8, 'قسم العلوم السياسية'),
(9, 'قسم نظم المعلومات'),
(10, 'لجنة البرامج'),
(11, 'موضوعات عامة'),
(12, 'لجنة البرامج الجديدة');

-- --------------------------------------------------------

--
-- Table structure for table `p39_users`
--

CREATE TABLE `p39_users` (
  `user_id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci NOT NULL,
  `job_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `job_type_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `job_rank_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `department_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `gender` enum('M','F') CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT 'images/members/user.svg',
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `is_admin` tinyint(3) UNSIGNED DEFAULT NULL,
  `added_by` smallint(5) UNSIGNED DEFAULT NULL,
  `is_enabled` tinyint(3) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `p39_users`
--

INSERT INTO `p39_users` (`user_id`, `name`, `job_title`, `job_type_id`, `job_rank_id`, `department_id`, `gender`, `image`, `email`, `password`, `is_admin`, `added_by`, `is_enabled`) VALUES
(1, 'محمود بدر', 'Admin', 1, 1, 1, 'M', 'images/members/user.svg', 'm@hotmail.com', '$2y$10$QyL5sGwbWIk./cUXORlNV.9C4ZZsHPV6llGcX5WggZ8tyGcNo0tXS', 1, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `p39_user_transaction`
--

CREATE TABLE `p39_user_transaction` (
  `transaction_id` smallint(5) UNSIGNED NOT NULL,
  `transaction_type` enum('Edit','Delete') DEFAULT NULL,
  `user_id` smallint(5) UNSIGNED DEFAULT NULL,
  `old_row` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `new_row` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL,
  `made_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `made_by` smallint(5) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `p39_vote`
--

CREATE TABLE `p39_vote` (
  `user_id` smallint(5) UNSIGNED NOT NULL,
  `subject_id` mediumint(8) UNSIGNED NOT NULL,
  `vote_type_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `p39_vote_type`
--

CREATE TABLE `p39_vote_type` (
  `vote_type_id` tinyint(3) UNSIGNED NOT NULL,
  `vote_type_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_520_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `p39_vote_type`
--

INSERT INTO `p39_vote_type` (`vote_type_id`, `vote_type_name`) VALUES
(1, 'موافقة'),
(2, 'رفض'),
(3, 'امتناع'),
(4, 'تحفظ'),
(5, 'انسحاب');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `application_data`
--
ALTER TABLE `application_data`
  ADD PRIMARY KEY (`app_id`);

--
-- Indexes for table `p39_attendance`
--
ALTER TABLE `p39_attendance`
  ADD PRIMARY KEY (`user_id`,`meeting_id`),
  ADD KEY `meeting_id` (`meeting_id`);

--
-- Indexes for table `p39_dates`
--
ALTER TABLE `p39_dates`
  ADD PRIMARY KEY (`date_id`),
  ADD KEY `formation_id` (`formation_id`);

--
-- Indexes for table `p39_decision`
--
ALTER TABLE `p39_decision`
  ADD PRIMARY KEY (`decision_id`),
  ADD KEY `added_by` (`added_by`),
  ADD KEY `decision_type_id` (`decision_type_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `p39_decision_attachment`
--
ALTER TABLE `p39_decision_attachment`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `added_by` (`added_by`),
  ADD KEY `decision_id` (`decision_id`);

--
-- Indexes for table `p39_decision_type`
--
ALTER TABLE `p39_decision_type`
  ADD PRIMARY KEY (`decision_type_id`);

--
-- Indexes for table `p39_department`
--
ALTER TABLE `p39_department`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `p39_formation`
--
ALTER TABLE `p39_formation`
  ADD PRIMARY KEY (`formation_id`),
  ADD UNIQUE KEY `start_year` (`start_year`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexes for table `p39_formation_user`
--
ALTER TABLE `p39_formation_user`
  ADD PRIMARY KEY (`formation_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `p39_job_rank`
--
ALTER TABLE `p39_job_rank`
  ADD PRIMARY KEY (`job_rank_id`);

--
-- Indexes for table `p39_job_type`
--
ALTER TABLE `p39_job_type`
  ADD PRIMARY KEY (`job_type_id`);

--
-- Indexes for table `p39_meeting`
--
ALTER TABLE `p39_meeting`
  ADD PRIMARY KEY (`meeting_id`),
  ADD KEY `added_by` (`added_by`),
  ADD KEY `formation_id` (`formation_id`);

--
-- Indexes for table `p39_meeting_attachment`
--
ALTER TABLE `p39_meeting_attachment`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `added_by` (`added_by`),
  ADD KEY `meeting_id` (`meeting_id`);

--
-- Indexes for table `p39_meeting_transaction`
--
ALTER TABLE `p39_meeting_transaction`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `made_by` (`made_by`);

--
-- Indexes for table `p39_subject`
--
ALTER TABLE `p39_subject`
  ADD PRIMARY KEY (`subject_id`),
  ADD KEY `added_by` (`added_by`),
  ADD KEY `subject_type_id` (`subject_type_id`),
  ADD KEY `meeting_id` (`meeting_id`);

--
-- Indexes for table `p39_subject_attachment`
--
ALTER TABLE `p39_subject_attachment`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `added_by` (`added_by`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `p39_subject_picture`
--
ALTER TABLE `p39_subject_picture`
  ADD PRIMARY KEY (`picture_id`),
  ADD KEY `added_by` (`added_by`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `p39_subject_transaction`
--
ALTER TABLE `p39_subject_transaction`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `made_by` (`made_by`);

--
-- Indexes for table `p39_subject_type`
--
ALTER TABLE `p39_subject_type`
  ADD PRIMARY KEY (`subject_type_id`);

--
-- Indexes for table `p39_users`
--
ALTER TABLE `p39_users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `added_by` (`added_by`),
  ADD KEY `job_type_id` (`job_type_id`),
  ADD KEY `job_rank_id` (`job_rank_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `p39_user_transaction`
--
ALTER TABLE `p39_user_transaction`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `made_by` (`made_by`);

--
-- Indexes for table `p39_vote`
--
ALTER TABLE `p39_vote`
  ADD PRIMARY KEY (`user_id`,`subject_id`),
  ADD KEY `vote_type_id` (`vote_type_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `p39_vote_type`
--
ALTER TABLE `p39_vote_type`
  ADD PRIMARY KEY (`vote_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `application_data`
--
ALTER TABLE `application_data`
  MODIFY `app_id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `p39_dates`
--
ALTER TABLE `p39_dates`
  MODIFY `date_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p39_decision`
--
ALTER TABLE `p39_decision`
  MODIFY `decision_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p39_decision_attachment`
--
ALTER TABLE `p39_decision_attachment`
  MODIFY `attachment_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p39_decision_type`
--
ALTER TABLE `p39_decision_type`
  MODIFY `decision_type_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `p39_department`
--
ALTER TABLE `p39_department`
  MODIFY `department_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `p39_formation`
--
ALTER TABLE `p39_formation`
  MODIFY `formation_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p39_job_rank`
--
ALTER TABLE `p39_job_rank`
  MODIFY `job_rank_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `p39_job_type`
--
ALTER TABLE `p39_job_type`
  MODIFY `job_type_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `p39_meeting`
--
ALTER TABLE `p39_meeting`
  MODIFY `meeting_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p39_meeting_attachment`
--
ALTER TABLE `p39_meeting_attachment`
  MODIFY `attachment_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p39_meeting_transaction`
--
ALTER TABLE `p39_meeting_transaction`
  MODIFY `transaction_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p39_subject`
--
ALTER TABLE `p39_subject`
  MODIFY `subject_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p39_subject_attachment`
--
ALTER TABLE `p39_subject_attachment`
  MODIFY `attachment_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p39_subject_picture`
--
ALTER TABLE `p39_subject_picture`
  MODIFY `picture_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p39_subject_transaction`
--
ALTER TABLE `p39_subject_transaction`
  MODIFY `transaction_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p39_subject_type`
--
ALTER TABLE `p39_subject_type`
  MODIFY `subject_type_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `p39_users`
--
ALTER TABLE `p39_users`
  MODIFY `user_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `p39_user_transaction`
--
ALTER TABLE `p39_user_transaction`
  MODIFY `transaction_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p39_vote_type`
--
ALTER TABLE `p39_vote_type`
  MODIFY `vote_type_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `p39_attendance`
--
ALTER TABLE `p39_attendance`
  ADD CONSTRAINT `p39_attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `p39_users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `p39_attendance_ibfk_2` FOREIGN KEY (`meeting_id`) REFERENCES `p39_meeting` (`meeting_id`) ON UPDATE CASCADE;

--
-- Constraints for table `p39_dates`
--
ALTER TABLE `p39_dates`
  ADD CONSTRAINT `p39_dates_ibfk_1` FOREIGN KEY (`formation_id`) REFERENCES `p39_formation` (`formation_id`) ON UPDATE CASCADE;

--
-- Constraints for table `p39_decision`
--
ALTER TABLE `p39_decision`
  ADD CONSTRAINT `p39_decision_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `p39_users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `p39_decision_ibfk_2` FOREIGN KEY (`decision_type_id`) REFERENCES `p39_decision_type` (`decision_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `p39_decision_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `p39_subject` (`subject_id`) ON UPDATE CASCADE;

--
-- Constraints for table `p39_decision_attachment`
--
ALTER TABLE `p39_decision_attachment`
  ADD CONSTRAINT `p39_decision_attachment_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `p39_users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `p39_decision_attachment_ibfk_2` FOREIGN KEY (`decision_id`) REFERENCES `p39_decision` (`decision_id`) ON UPDATE CASCADE;

--
-- Constraints for table `p39_formation`
--
ALTER TABLE `p39_formation`
  ADD CONSTRAINT `p39_formation_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `p39_users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `p39_formation_user`
--
ALTER TABLE `p39_formation_user`
  ADD CONSTRAINT `p39_formation_user_ibfk_1` FOREIGN KEY (`formation_id`) REFERENCES `p39_formation` (`formation_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `p39_formation_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `p39_users` (`user_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `p39_meeting`
--
ALTER TABLE `p39_meeting`
  ADD CONSTRAINT `p39_meeting_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `p39_users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `p39_meeting_ibfk_2` FOREIGN KEY (`formation_id`) REFERENCES `p39_formation` (`formation_id`) ON UPDATE CASCADE;

--
-- Constraints for table `p39_meeting_attachment`
--
ALTER TABLE `p39_meeting_attachment`
  ADD CONSTRAINT `p39_meeting_attachment_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `p39_users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `p39_meeting_attachment_ibfk_2` FOREIGN KEY (`meeting_id`) REFERENCES `p39_meeting` (`meeting_id`) ON UPDATE CASCADE;

--
-- Constraints for table `p39_meeting_transaction`
--
ALTER TABLE `p39_meeting_transaction`
  ADD CONSTRAINT `p39_meeting_transaction_ibfk_1` FOREIGN KEY (`made_by`) REFERENCES `p39_users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `p39_subject`
--
ALTER TABLE `p39_subject`
  ADD CONSTRAINT `p39_subject_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `p39_users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `p39_subject_ibfk_2` FOREIGN KEY (`subject_type_id`) REFERENCES `p39_subject_type` (`subject_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `p39_subject_ibfk_3` FOREIGN KEY (`meeting_id`) REFERENCES `p39_meeting` (`meeting_id`) ON UPDATE CASCADE;

--
-- Constraints for table `p39_subject_attachment`
--
ALTER TABLE `p39_subject_attachment`
  ADD CONSTRAINT `p39_subject_attachment_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `p39_users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `p39_subject_attachment_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `p39_subject` (`subject_id`) ON UPDATE CASCADE;

--
-- Constraints for table `p39_subject_picture`
--
ALTER TABLE `p39_subject_picture`
  ADD CONSTRAINT `p39_subject_picture_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `p39_users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `p39_subject_picture_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `p39_subject` (`subject_id`) ON UPDATE CASCADE;

--
-- Constraints for table `p39_subject_transaction`
--
ALTER TABLE `p39_subject_transaction`
  ADD CONSTRAINT `p39_subject_transaction_ibfk_1` FOREIGN KEY (`made_by`) REFERENCES `p39_users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `p39_users`
--
ALTER TABLE `p39_users`
  ADD CONSTRAINT `p39_users_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `p39_users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `p39_users_ibfk_2` FOREIGN KEY (`job_type_id`) REFERENCES `p39_job_type` (`job_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `p39_users_ibfk_3` FOREIGN KEY (`job_rank_id`) REFERENCES `p39_job_rank` (`job_rank_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `p39_users_ibfk_4` FOREIGN KEY (`department_id`) REFERENCES `p39_department` (`department_id`) ON UPDATE CASCADE;

--
-- Constraints for table `p39_user_transaction`
--
ALTER TABLE `p39_user_transaction`
  ADD CONSTRAINT `p39_user_transaction_ibfk_1` FOREIGN KEY (`made_by`) REFERENCES `p39_users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `p39_vote`
--
ALTER TABLE `p39_vote`
  ADD CONSTRAINT `p39_vote_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `p39_users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `p39_vote_ibfk_2` FOREIGN KEY (`vote_type_id`) REFERENCES `p39_vote_type` (`vote_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `p39_vote_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `p39_subject` (`subject_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
