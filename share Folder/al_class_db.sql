-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 04, 2025 at 10:22 AM
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
(2, 5, '2025-12-02', 'Present', '16:31:06');

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
(5, 'ET for Srimal Wijesinghe', 'Technology', 'Engineering Technology ', 'Srimal Wijesinghe', 2500.00, 'Friday', '08:00 am to 02:00 pm', 1);

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
  `method` varchar(20) NOT NULL DEFAULT 'Cash',
  `payment_type` varchar(20) NOT NULL DEFAULT 'Full',
  `paid_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `student_id`, `class_id`, `month`, `year`, `amount`, `method`, `payment_type`, `paid_date`) VALUES
(2, 1, 1, 'November', 2025, 2500.00, 'Card', 'Full', '2025-11-30 12:28:35'),
(3, 1, 1, 'May', 2025, 2500.00, 'Card', 'Half', '2025-11-30 12:39:16'),
(6, 1, 1, 'November', 2025, 2500.00, 'Cash', 'Full', '2025-11-30 13:36:13'),
(7, 1, 1, 'December', 2025, 2500.00, 'Cash', 'Full', '2025-12-01 17:32:52'),
(8, 6, 1, 'December', 2025, 2500.00, 'Cash', 'Full', '2025-12-02 12:16:38'),
(11, 1, 1, 'January', 2025, 2500.00, 'Cash', 'Full', '2025-12-02 14:29:26'),
(12, 1, 1, 'January', 2025, 2500.00, 'Cash', 'Full Payment', '2025-12-02 14:43:59'),
(13, 1, 1, 'January', 2025, 2500.00, 'Cash', 'Full Payment', '2025-12-02 14:59:18'),
(14, 1, 1, 'April', 2025, 2500.00, 'Cash', 'Full Payment', '2025-12-02 15:09:39'),
(15, 1, 1, 'February', 2025, 2500.00, 'Cash', 'Full Payment', '2025-12-02 15:09:49'),
(16, 7, 1, 'January', 2025, 2500.00, 'Cash', 'Full Payment', '2025-12-02 15:15:59'),
(17, 7, 1, 'November', 2025, 2500.00, 'Cash', 'Full Payment', '2025-12-02 15:29:37'),
(18, 5, 1, 'January', 2025, 2500.00, 'Cash', 'Full Payment', '2025-12-02 15:54:19');

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
(8, 'ST2025007', 'chamika', '200612121212', '2025-12-17', 'Male', '', 'baddegama', '0766411887', '0766411887', 'chamika@gmail.com', 'Tech', '2026', 'ST2025007.jpg', '2025-12-04 08:04:07', 1);

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
(1, 'TC2025001', '893411', 'SVFdsfgswre', '', 'Physics', 'fsdfsedfef', '1764832639_6931357f6f360.jpg', 1, '2025-12-04 07:17:19'),
(2, 'TC2025002', '295001', ' Mr.Dinesh Muthugala', '', 'Biology', 'In addition to his academic qualifications, Muthugala is a well-known Biology lecturer in Sri Lanka and is the founder of DM Education Private Limited. He is also an entrepreneur and film producer. ', '1764837787_6931499b53340.jpg', 1, '2025-12-04 08:43:07'),
(3, 'TC2025003', '770370', 'Mr.Ravindu bandaranayake', '0713510441', 'ICT', 'He holds a B.Sc. (Hons) in Cybersecurity and Forensics from the University of Gloucestershire, United Kingdom. He previously studied Software Engineering at the Sri Lanka Institute of Information Technology (SLIIT) and Electronics at Wayamba University of Sri Lanka (WUSL).', '1764839145_69314ee93cd88.jpg', 1, '2025-12-04 09:05:45');

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
(1, 'admin', '123', 'Admin', '2025-11-27 04:39:07');

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
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `reg_number` (`reg_number`);

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
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
