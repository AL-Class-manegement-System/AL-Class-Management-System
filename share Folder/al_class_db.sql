-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2025 at 02:39 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `al_class_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('Present','Absent') DEFAULT 'Present',
  `time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `student_id`, `date`, `status`, `time`) VALUES
(2, 5, '2025-12-02', 'Present', '16:31:06'),
(3, 7, '2025-12-05', 'Absent', '15:45:53'),
(4, 9, '2025-12-08', 'Present', '10:52:06'),
(5, 7, '2025-12-08', 'Present', '07:42:48'),
(6, 1, '2025-12-09', 'Present', '12:30:22'),
(7, 7, '2025-12-09', 'Absent', '11:28:48'),
(8, 5, '2025-12-09', 'Present', '09:28:59');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `stream` varchar(50) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `teacher_name` varchar(100) DEFAULT NULL,
  `fee` decimal(10,2) NOT NULL,
  `day` varchar(20) DEFAULT NULL,
  `time` varchar(20) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `class_name`, `stream`, `subject`, `teacher_name`, `fee`, `day`, `time`, `status`) VALUES
(1, '2025 Revision', 'Physical Science', 'Combined Maths', 'Mr. Perera', 2500.00, 'Saturday', '08:00 AM', 1),
(2, '2026 Theory', 'Bio Science', 'Biology', 'Mrs. Silva', 2000.00, 'Sunday', '10:00 AM', 1),
(3, '2025 Paper Class', 'Commerce', 'Econ', 'Mr. Kamal', 1500.00, 'Friday', '02:30 PM', 1),
(4, 'Bio Revision class', 'Bio Science', 'Bio Revision', 'Dinesh Muthugala', 2500.00, 'Friday', '08:00 am to 02:00 pm', 1),
(5, 'ET for Srimal Wijesinghe', 'Technology', 'Engineering Technology ', 'Srimal Wijesinghe', 2500.00, 'Friday', '08:00 am to 02:00 pm', 1),
(6, 'akila vimanga senevirathna  Sinhala class', 'Arts', 'Sinhala class', 'akila vimanga senevirathna', 2500.00, 'Wednesday', '08:00 to 12:00', 1),
(7, 'ICT fron abc', 'ICT', 'ICT', 'Mr.Ravindu bandaranayake', 3000.00, 'Saturday', '12:00 pm to 04:00 pm', 1);

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `joined_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `joined_date`) VALUES
(1, 7, 4, '2025-12-07 10:44:50'),
(7, 7, 1, '2025-12-07 11:02:54'),
(8, 1, 6, '2025-12-07 11:12:55'),
(9, 1, 7, '2025-12-09 18:27:20');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `exam_id` int(11) NOT NULL,
  `exam_name` varchar(100) NOT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `total_marks` int(11) DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`exam_id`, `exam_name`, `subject`, `date`, `total_marks`) VALUES
(1, '2026 1st term test', NULL, '2025-12-02', 100),
(2, '2026 tame 1', NULL, '2025-12-02', 100),
(3, '2026 tame 1', NULL, '2025-12-02', 100);

-- --------------------------------------------------------

--
-- Table structure for table `exam_marks`
--

CREATE TABLE `exam_marks` (
  `mark_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `marks` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `exp_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`exp_id`, `description`, `amount`, `date`) VALUES
(1, 'fff', 120000.00, '2025-12-02 16:18:01');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL DEFAULT 1,
  `month` varchar(20) NOT NULL,
  `year` int(4) NOT NULL DEFAULT 2025,
  `amount` decimal(10,2) NOT NULL,
  `payment_status` enum('paid','pending','failed') NOT NULL DEFAULT 'paid',
  `transaction_id` varchar(100) DEFAULT NULL,
  `method` varchar(20) NOT NULL DEFAULT 'Cash',
  `payment_type` varchar(20) NOT NULL DEFAULT 'Full',
  `paid_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `student_id`, `class_id`, `month`, `year`, `amount`, `payment_status`, `transaction_id`, `method`, `payment_type`, `paid_date`) VALUES
(2, 1, 1, 'November', 2025, 2500.00, 'paid', NULL, 'Card', 'Full', '2025-11-30 12:28:35'),
(3, 1, 1, 'May', 2025, 2500.00, 'paid', NULL, 'Card', 'Half', '2025-11-30 12:39:16'),
(6, 1, 1, 'November', 2025, 2500.00, 'paid', NULL, 'Cash', 'Full', '2025-11-30 13:36:13'),
(7, 1, 1, 'December', 2025, 2500.00, 'paid', NULL, 'Cash', 'Full', '2025-12-01 17:32:52'),
(8, 6, 1, 'December', 2025, 2500.00, 'paid', NULL, 'Cash', 'Full', '2025-12-02 12:16:38'),
(11, 1, 1, 'January', 2025, 2500.00, 'paid', NULL, 'Cash', 'Full', '2025-12-02 14:29:26'),
(12, 1, 1, 'January', 2025, 2500.00, 'paid', NULL, 'Cash', 'Full Payment', '2025-12-02 14:43:59'),
(13, 1, 1, 'January', 2025, 2500.00, 'paid', NULL, 'Cash', 'Full Payment', '2025-12-02 14:59:18'),
(14, 1, 1, 'April', 2025, 2500.00, 'paid', NULL, 'Cash', 'Full Payment', '2025-12-02 15:09:39'),
(15, 1, 1, 'February', 2025, 2500.00, 'paid', NULL, 'Cash', 'Full Payment', '2025-12-02 15:09:49'),
(16, 7, 1, 'January', 2025, 2500.00, 'paid', NULL, 'Cash', 'Full Payment', '2025-12-02 15:15:59'),
(17, 7, 1, 'November', 2025, 2500.00, 'paid', NULL, 'Cash', 'Full Payment', '2025-12-02 15:29:37'),
(18, 5, 1, 'January', 2025, 2500.00, 'paid', NULL, 'Cash', 'Full Payment', '2025-12-02 15:54:19'),
(19, 1, 1, 'August', 2025, 2500.00, 'paid', NULL, 'Cash', 'Full Payment', '2025-12-08 09:46:17'),
(20, 5, 1, 'September', 2025, 1250.00, 'paid', NULL, 'Cash', 'Half Payment', '2025-12-08 09:48:34'),
(21, 5, 1, 'February', 2025, 2500.00, 'paid', NULL, 'Cash', 'Full Payment', '2025-12-08 10:44:37'),
(22, 9, 1, 'January', 2025, 2500.00, 'paid', NULL, 'Cash', 'Full Payment', '2025-12-08 10:51:58'),
(23, 1, 1, 'March', 2025, 1250.00, 'paid', NULL, 'Cash', 'Half Payment', '2025-12-09 12:29:37');

-- --------------------------------------------------------

--
-- Table structure for table `salaries`
--

CREATE TABLE `salaries` (
  `salary_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `payment_month` varchar(7) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `status` enum('paid','pending') NOT NULL DEFAULT 'paid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `reg_number` varchar(20) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `nic` varchar(20) NOT NULL,
  `dob` date NOT NULL,
  `gender` varchar(10) NOT NULL,
  `school` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `student_phone` varchar(15) DEFAULT NULL,
  `parent_phone` varchar(15) NOT NULL,
  `email` varchar(150) NOT NULL,
  `stream` varchar(50) NOT NULL,
  `batch` varchar(10) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `registered_date` datetime DEFAULT current_timestamp(),
  `status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `reg_number`, `full_name`, `nic`, `dob`, `gender`, `school`, `address`, `student_phone`, `parent_phone`, `email`, `stream`, `batch`, `photo`, `registered_date`, `status`) VALUES
(1, 'ST2025001', 'janith', '200011234567', '2025-11-04', 'Male', 'richmend', 'galle', '0740614128', '0773093941', 'nuwanthanadee2005@gmail.com', 'Art', '2025', 'ST2025001.png', '2025-11-27 10:39:50', 1),
(5, 'ST2025004', 'damsara', '200801401349', '2025-12-11', 'Male', 'mahinda college', 'galle', '0773093941', '0773093941', 'janith@gmail.com', 'Tech', '2027', 'ST2025004.jpg', '2025-12-02 11:51:33', 1),
(6, 'ST2025005', 'ravindu', '200801401349', '2025-12-10', 'Male', 'ff', 'fbcnn', '0773093941', '0773093941', 'janith@gmail.com', 'Tech', '2027', 'ST2025005.jpg', '2025-12-02 11:56:28', 1),
(7, 'ST2025006', 'Ravindu chandeepa', '200401401349', '2025-12-10', 'Male', 'ff', 'galle', '0773093941', '0773093941', 'Ravindu@gmail.com', 'Maths', '2027', 'ST2025006.jpg', '2025-12-02 14:24:02', 1),
(8, 'ST2025007', 'chamika', '200612121212', '2025-12-17', 'Male', '', 'baddegama', '0766411887', '0766411887', 'chamika@gmail.com', 'Tech', '2026', 'ST2025007.jpg', '2025-12-04 08:04:07', 1),
(9, 'ST2025008', 'iruja pasandul', '200530301877', '2025-10-29', 'Male', 'siridhamma college', 'bangalawatta', '0774258282', '0774258282', 'iruja@gmail.com', 'Tech', '2025', 'ST2025008.jpg', '2025-12-08 10:51:35', 1);

-- --------------------------------------------------------

--
-- Table structure for table `study_materials`
--

CREATE TABLE `study_materials` (
  `material_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `material_title` varchar(255) NOT NULL,
  `material_type` enum('Notes','Past Paper','Model Paper','Reading','Assignment') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_date` date NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `study_materials`
--

INSERT INTO `study_materials` (`material_id`, `teacher_id`, `subject_name`, `material_title`, `material_type`, `file_path`, `upload_date`, `status`, `created_at`) VALUES
(1, 0, 'General', '2024-AL-ICT-PART-I-MCQ-PAPER-SINHALA-MEDIUM2024-AL-ICT-PART-I-MCQ-PAPER-SINHALA-MEDIUM', 'Past Paper', 'assets/study_materials/general/material_693823b71af02.pdf', '2025-12-09', 1, '2025-12-09 13:27:19'),
(2, 0, 'General', '2024-AL-ICT-PART-I-MCQ-PAPER-SINHALA-MEDIUM', 'Past Paper', 'assets/study_materials/general/material_69382442d503e.pdf', '2025-12-09', 1, '2025-12-09 13:29:38');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `description`) VALUES
('allow_registration', 'yes', 'නව ශිෂ්‍ය ලියාපදිංචියට අවසර දෙන්න (yes/no)'),
('monthly_fee_default', '1500.00', 'පෙරනිමි මාසික ගාස්තු මුදල'),
('system_name', 'AL Class Management System', 'පද්ධතියේ නම');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL,
  `teacher_number` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `teacher_number`, `password`, `full_name`, `phone`, `subject`, `description`, `image`, `status`, `created_at`) VALUES
(2, 'TC2025002', '295001', ' Mr.Dinesh Muthugala', '', 'Biology', 'In addition to his academic qualifications, Muthugala is a well-known Biology lecturer in Sri Lanka and is the founder of DM Education Private Limited. He is also an entrepreneur and film producer. ', '1764837787_6931499b53340.jpg', 1, '2025-12-04 08:43:07'),
(3, 'TC2025003', '770370', 'Mr.Ravindu bandaranayake', '0713510441', 'ICT', 'He holds a B.Sc. (Hons) in Cybersecurity and Forensics from the University of Gloucestershire, United Kingdom. He previously studied Software Engineering at the Sri Lanka Institute of Information Technology (SLIIT) and Electronics at Wayamba University of Sri Lanka (WUSL).', '1764839145_69314ee93cd88.jpg', 1, '2025-12-04 09:05:45'),
(4, 'TC2025004', '243687', 'Darshana Ukuwela', '0713510441', 'Physics', 'ssddddd', '1764841703_693158e753420.jpg', 1, '2025-12-04 09:48:23'),
(5, 'TC2025005', '880619', 'sahashra janith ', '0773099341', 'Biology', 'www', '1764943866_6932e7fa5c249.jpg', 1, '2025-12-05 14:11:06'),
(6, 'TC2025006', '101937', 'akila vimanga senevirathna', '0716548262', 'Arts', 'Thinking\r\nSearching\r\nAkila Vimanga Senevirathna is a\r\ntutor and teacher known for offering classes in the Sinhala language, and a search result indicates he holds a B.A. degree. His specific area of focus is teaching Sinhala language for GCE Ordinary Level (O/L) and Advanced Level (A/L) students in Sri Lanka.', '1765085853_6935129d42433.jpg', 1, '2025-12-07 05:37:33'),
(7, 'TC2025007', '878802', 'etryui', '0773093941', 'Chemistry', 'WRAETSYRUDTIFYOGU;FSGHJK', '1765164208_693644b017770.jpg', 1, '2025-12-08 03:23:28');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Teacher','Reception') NOT NULL DEFAULT 'Admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '1234', 'Admin', '2025-11-27 04:39:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`exam_id`);

--
-- Indexes for table `exam_marks`
--
ALTER TABLE `exam_marks`
  ADD PRIMARY KEY (`mark_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`exp_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `salaries`
--
ALTER TABLE `salaries`
  ADD PRIMARY KEY (`salary_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `reg_number` (`reg_number`);

--
-- Indexes for table `study_materials`
--
ALTER TABLE `study_materials`
  ADD PRIMARY KEY (`material_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`),
  ADD UNIQUE KEY `teacher_number` (`teacher_number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `exam_marks`
--
ALTER TABLE `exam_marks`
  MODIFY `mark_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `exp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `salaries`
--
ALTER TABLE `salaries`
  MODIFY `salary_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `study_materials`
--
ALTER TABLE `study_materials`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_marks`
--
ALTER TABLE `exam_marks`
  ADD CONSTRAINT `exam_marks_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_marks_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE;

--
-- Constraints for table `salaries`
--
ALTER TABLE `salaries`
  ADD CONSTRAINT `salaries_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
