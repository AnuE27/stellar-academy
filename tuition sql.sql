-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 10, 2025 at 07:41 AM
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
-- Database: `tuition`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_allocation`
--

CREATE TABLE `tbl_allocation` (
  `SUBT_ID` int(20) NOT NULL,
  `ST_ID` int(20) DEFAULT NULL,
  `SUB_ID` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_allocation`
--

INSERT INTO `tbl_allocation` (`SUBT_ID`, `ST_ID`, `SUB_ID`) VALUES
(4001, 2003, 3001),
(4002, 2003, 3011),
(4011, 2021, 3005),
(4012, 2021, 3010),
(4013, 2021, 3018),
(4014, 2020, 3003),
(4015, 2020, 3021);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_attendance`
--

CREATE TABLE `tbl_attendance` (
  `ATT_ID` int(11) NOT NULL,
  `Att_date` date DEFAULT NULL,
  `E_ID` int(11) DEFAULT NULL,
  `Status` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_attendance`
--

INSERT INTO `tbl_attendance` (`ATT_ID`, `Att_date`, `E_ID`, `Status`) VALUES
(1101, '2024-09-09', 5013, 'Present'),
(1102, '2024-09-24', 5014, 'Present'),
(1103, '2024-09-29', 5013, 'Absent'),
(1104, '2024-09-29', 5014, 'Present');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_enrollment`
--

CREATE TABLE `tbl_enrollment` (
  `E_ID` int(20) NOT NULL,
  `S_ID` int(20) DEFAULT NULL,
  `SUB_ID` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_enrollment`
--

INSERT INTO `tbl_enrollment` (`E_ID`, `S_ID`, `SUB_ID`) VALUES
(5001, 1001, 3005),
(5002, 1001, 3003),
(5011, 1005, 3014),
(5012, 1005, 3015),
(5013, 1001, 3001),
(5014, 1006, 3011),
(5015, 1006, 3013),
(5016, 1007, 3011),
(5017, 1008, 3001);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_feepayment`
--

CREATE TABLE `tbl_feepayment` (
  `FP_ID` int(20) NOT NULL,
  `S_ID` int(20) DEFAULT NULL,
  `F_ID` int(20) DEFAULT NULL,
  `Payment_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_feepayment`
--

INSERT INTO `tbl_feepayment` (`FP_ID`, `S_ID`, `F_ID`, `Payment_date`) VALUES
(9032, 1001, 8003, '2024-09-23'),
(9034, 1006, 8001, '2024-09-29'),
(9036, 1001, 8001, '2024-10-29'),
(9037, 1008, 8001, '2024-10-30');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_fees`
--

CREATE TABLE `tbl_fees` (
  `F_ID` int(20) NOT NULL,
  `Type` varchar(50) NOT NULL,
  `Amount` int(10) NOT NULL,
  `Due_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_fees`
--

INSERT INTO `tbl_fees` (`F_ID`, `Type`, `Amount`, `Due_date`) VALUES
(8001, 'Admission', 2000, '2024-06-30'),
(8002, 'Exam', 600, '2025-02-28'),
(8003, 'Amenities', 700, '2025-02-28'),
(8004, 'S1', 3000, '2024-09-30'),
(8005, 'S2', 3400, '2024-12-31'),
(8006, 'S3', 4000, '2025-03-31');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_performance`
--

CREATE TABLE `tbl_performance` (
  `P_ID` int(20) NOT NULL,
  `E_ID` int(20) NOT NULL,
  `S1` int(2) DEFAULT NULL,
  `S2` int(2) DEFAULT NULL,
  `S3` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_performance`
--

INSERT INTO `tbl_performance` (`P_ID`, `E_ID`, `S1`, `S2`, `S3`) VALUES
(7002, 5001, NULL, NULL, NULL),
(7003, 5002, NULL, NULL, NULL),
(7004, 5013, 89, 82, NULL),
(7021, 5014, 79, 89, NULL),
(7025, 5016, 67, 67, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_schedule`
--

CREATE TABLE `tbl_schedule` (
  `SCH_ID` int(20) NOT NULL,
  `SUBT_ID` int(20) NOT NULL,
  `Day` varchar(10) NOT NULL,
  `Time` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_schedule`
--

INSERT INTO `tbl_schedule` (`SCH_ID`, `SUBT_ID`, `Day`, `Time`) VALUES
(6001, 4001, 'Monday', '5:30-6:30'),
(6002, 4002, 'Tuesday', '6:30-7:30'),
(6005, 4013, 'Friday', '5:30-6:30'),
(6006, 4014, 'Thursday', '6:30-7:30'),
(6007, 4015, 'Friday', '8:30-9:30');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_staff`
--

CREATE TABLE `tbl_staff` (
  `ST_ID` int(20) NOT NULL,
  `Fname` varchar(40) DEFAULT NULL,
  `Lname` varchar(40) DEFAULT NULL,
  `Ph_no` char(10) NOT NULL,
  `Email` varchar(30) NOT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_staff`
--

INSERT INTO `tbl_staff` (`ST_ID`, `Fname`, `Lname`, `Ph_no`, `Email`, `Password`, `Role`) VALUES
(2001, 'Anu', 'Emmanuel', '9807654321', 'anuemmanuel27@gmail.com', '$2y$10$uhdJzo.2FDVQlemkgQWhNestXv6wevxlYim.QoQ4CO8LWjZvHqcdW', 'Admin'),
(2002, 'Alphy', 'Tomi', '9675432018', 'alphy@gmail.com', '$2y$10$dOFAcRi6uFrPN6YjogUR3uJNWiwIbGM2CkwTawoMWU84JeEe5EeVS', 'Admin'),
(2003, 'Roy', 'Mathew', '9780654321', 'roy@gmail.com', '$2y$10$U6nScn/TD24Qg72jiWtmTOxSWYLPQpxCTQ0rvZfhr6ptIJ8BO9.WK', 'Teacher'),
(2020, 'Pinky', 'John', '9123456780', 'pinky@gmail.com', '$2y$10$Nu5cGDhO1D98v4os7mdbxu4gihXsRPNjLK8w6urRoh2uWQvS/.oX.', 'Teacher'),
(2021, 'Minta', 'Jose', '9675432018', 'minta@gmail.com', '$2y$10$qzyGzZaqPPR3uk8e8q7WDeox5ndnMHCW2qWlrGGheV.3gR6Q2/GFC', 'Teacher');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_stud`
--

CREATE TABLE `tbl_stud` (
  `S_ID` int(20) NOT NULL,
  `Fname` varchar(40) NOT NULL,
  `Lname` varchar(40) NOT NULL,
  `Ph_no` char(10) NOT NULL,
  `Email` varchar(30) NOT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Guar_ph` char(10) NOT NULL,
  `DOB` date NOT NULL,
  `adm_status` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_stud`
--

INSERT INTO `tbl_stud` (`S_ID`, `Fname`, `Lname`, `Ph_no`, `Email`, `Password`, `Guar_ph`, `DOB`, `adm_status`) VALUES
(1001, 'Nia', 'Jose', '9807654321', 'nia@gmail.com', '$2y$10$hLn5a6/LMPq7JHj7hzu4MOHhdp9ChTBGDHyMiWW1OfTyPs5MDcMtu', '9678054321', '2004-11-23', 1),
(1005, 'Rishi', 'M', '8976745634', 'rishi@gmail.com', '$2y$10$7S9wGTT6TP5qkZos7w5P1.sISvkZlTQth60zzvcuH/KNf9Ger3ldC', '8796567543', '2008-06-11', 1),
(1006, 'Mathew', 'M', '8976745634', 'mathew@gmail.com', '$2y$10$T2YokWvjErt7zxrXwU0wv.TYT1K8i30Mq7PTXQF3La7C9UckG0EUm', '8796567543', '2011-06-08', 1),
(1007, 'Kiran', 'Singh', '8976745634', 'kiran@gmail.com', '$2y$10$P7Td73Dk5vl/zYKyKthyje3b6I./.CAJew2TFqva8zfBR1mjd2l4u', '8796567543', '2009-02-01', 0),
(1008, 'Maya', 's', '8976745634', 'maya@gmail.com', '$2y$10$/pxm1I5C9jnB8EWOB.0A9.Ym7ieyMJkAwEz9cTg6PoVitUjtocYTS', '9871232454', '2011-03-02', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_subject`
--

CREATE TABLE `tbl_subject` (
  `SUB_ID` int(20) NOT NULL,
  `Name` varchar(40) NOT NULL,
  `stat` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_subject`
--

INSERT INTO `tbl_subject` (`SUB_ID`, `Name`, `stat`) VALUES
(3001, '8_Biology', 1),
(3002, '8_Physics', 0),
(3003, '8_Maths', 1),
(3004, '8_English', 0),
(3005, '8_Chemistry', 0),
(3006, '9_Physics', 1),
(3007, '9_Biology', 1),
(3008, '9_Maths', 1),
(3009, '9_English', 0),
(3010, '9_Chemistry', 0),
(3011, '10_Biology', 1),
(3012, '10_Physics', 0),
(3013, '10_Chemistry', 1),
(3014, '10_English', 1),
(3015, '10_Maths', 0),
(3016, '11_Maths', 0),
(3017, '11_English', 1),
(3018, '11_Chemistry', 0),
(3019, '11_Biology', 1),
(3020, '11_Physics', 1),
(3021, '12_Maths', 0),
(3022, '12_English', 0),
(3023, '12_Chemistry', 1),
(3024, '12_Biology', 1),
(3025, '12_Physics', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_allocation`
--
ALTER TABLE `tbl_allocation`
  ADD PRIMARY KEY (`SUBT_ID`),
  ADD KEY `ST_ID` (`ST_ID`),
  ADD KEY `SUB_ID` (`SUB_ID`);

--
-- Indexes for table `tbl_attendance`
--
ALTER TABLE `tbl_attendance`
  ADD PRIMARY KEY (`ATT_ID`),
  ADD KEY `E_ID` (`E_ID`);

--
-- Indexes for table `tbl_enrollment`
--
ALTER TABLE `tbl_enrollment`
  ADD PRIMARY KEY (`E_ID`),
  ADD KEY `S_ID` (`S_ID`),
  ADD KEY `SUB_ID` (`SUB_ID`);

--
-- Indexes for table `tbl_feepayment`
--
ALTER TABLE `tbl_feepayment`
  ADD PRIMARY KEY (`FP_ID`),
  ADD KEY `S_ID` (`S_ID`),
  ADD KEY `F_ID` (`F_ID`);

--
-- Indexes for table `tbl_fees`
--
ALTER TABLE `tbl_fees`
  ADD PRIMARY KEY (`F_ID`);

--
-- Indexes for table `tbl_performance`
--
ALTER TABLE `tbl_performance`
  ADD PRIMARY KEY (`P_ID`),
  ADD UNIQUE KEY `E_ID_2` (`E_ID`),
  ADD KEY `E_ID` (`E_ID`);

--
-- Indexes for table `tbl_schedule`
--
ALTER TABLE `tbl_schedule`
  ADD PRIMARY KEY (`SCH_ID`),
  ADD KEY `SUBT_ID` (`SUBT_ID`);

--
-- Indexes for table `tbl_staff`
--
ALTER TABLE `tbl_staff`
  ADD PRIMARY KEY (`ST_ID`),
  ADD UNIQUE KEY `Password` (`Password`);

--
-- Indexes for table `tbl_stud`
--
ALTER TABLE `tbl_stud`
  ADD PRIMARY KEY (`S_ID`),
  ADD UNIQUE KEY `Password` (`Password`);

--
-- Indexes for table `tbl_subject`
--
ALTER TABLE `tbl_subject`
  ADD PRIMARY KEY (`SUB_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_allocation`
--
ALTER TABLE `tbl_allocation`
  MODIFY `SUBT_ID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4016;

--
-- AUTO_INCREMENT for table `tbl_attendance`
--
ALTER TABLE `tbl_attendance`
  MODIFY `ATT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1105;

--
-- AUTO_INCREMENT for table `tbl_enrollment`
--
ALTER TABLE `tbl_enrollment`
  MODIFY `E_ID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5018;

--
-- AUTO_INCREMENT for table `tbl_feepayment`
--
ALTER TABLE `tbl_feepayment`
  MODIFY `FP_ID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9038;

--
-- AUTO_INCREMENT for table `tbl_fees`
--
ALTER TABLE `tbl_fees`
  MODIFY `F_ID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8007;

--
-- AUTO_INCREMENT for table `tbl_performance`
--
ALTER TABLE `tbl_performance`
  MODIFY `P_ID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7028;

--
-- AUTO_INCREMENT for table `tbl_schedule`
--
ALTER TABLE `tbl_schedule`
  MODIFY `SCH_ID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6008;

--
-- AUTO_INCREMENT for table `tbl_staff`
--
ALTER TABLE `tbl_staff`
  MODIFY `ST_ID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2022;

--
-- AUTO_INCREMENT for table `tbl_stud`
--
ALTER TABLE `tbl_stud`
  MODIFY `S_ID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1009;

--
-- AUTO_INCREMENT for table `tbl_subject`
--
ALTER TABLE `tbl_subject`
  MODIFY `SUB_ID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3026;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_allocation`
--
ALTER TABLE `tbl_allocation`
  ADD CONSTRAINT `fk_staff_allocation` FOREIGN KEY (`ST_ID`) REFERENCES `tbl_staff` (`ST_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_allocation_ibfk_2` FOREIGN KEY (`SUB_ID`) REFERENCES `tbl_subject` (`SUB_ID`);

--
-- Constraints for table `tbl_attendance`
--
ALTER TABLE `tbl_attendance`
  ADD CONSTRAINT `tbl_attendance_ibfk_1` FOREIGN KEY (`E_ID`) REFERENCES `tbl_enrollment` (`E_ID`);

--
-- Constraints for table `tbl_enrollment`
--
ALTER TABLE `tbl_enrollment`
  ADD CONSTRAINT `tbl_enrollment_ibfk_1` FOREIGN KEY (`S_ID`) REFERENCES `tbl_stud` (`S_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_enrollment_ibfk_2` FOREIGN KEY (`SUB_ID`) REFERENCES `tbl_subject` (`SUB_ID`);

--
-- Constraints for table `tbl_feepayment`
--
ALTER TABLE `tbl_feepayment`
  ADD CONSTRAINT `tbl_feepayment_ibfk_1` FOREIGN KEY (`S_ID`) REFERENCES `tbl_stud` (`S_ID`),
  ADD CONSTRAINT `tbl_feepayment_ibfk_2` FOREIGN KEY (`F_ID`) REFERENCES `tbl_fees` (`F_ID`);

--
-- Constraints for table `tbl_performance`
--
ALTER TABLE `tbl_performance`
  ADD CONSTRAINT `tbl_performance_ibfk_2` FOREIGN KEY (`E_ID`) REFERENCES `tbl_enrollment` (`E_ID`);

--
-- Constraints for table `tbl_schedule`
--
ALTER TABLE `tbl_schedule`
  ADD CONSTRAINT `tbl_schedule_ibfk_1` FOREIGN KEY (`SUBT_ID`) REFERENCES `tbl_allocation` (`SUBT_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
