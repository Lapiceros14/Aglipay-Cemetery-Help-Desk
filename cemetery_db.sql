-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 03, 2026 at 10:02 PM
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
-- Database: `cemetery_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cemetery_tb`
--

CREATE TABLE `cemetery_tb` (
  `UID` int(11) NOT NULL,
  `Fullname` varchar(30) NOT NULL,
  `Birthdate` varchar(100) NOT NULL,
  `Deathdate` varchar(100) NOT NULL,
  `Phase` int(10) NOT NULL,
  `Column` int(50) NOT NULL,
  `Row` int(100) NOT NULL,
  `X` float NOT NULL,
  `Y` float NOT NULL,
  `Z` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cemetery_tb`
--

INSERT INTO `cemetery_tb` (`UID`, `Fullname`, `Birthdate`, `Deathdate`, `Phase`, `Column`, `Row`, `X`, `Y`, `Z`) VALUES
(1, 'Richsan desierra', 'January 2 2000', 'January 2 2000', 1, 2, 3, -39.31, 5.94, 33.27),
(2, 'Karl Cedric Epil', 'January 2 2000', 'January 2 2000', 2, 3, 4, -14.59, 5.94, 33.27),
(3, 'Richsan Delos reyes', 'January 2 2000', 'January 2 2000', 4, 5, 6, -9.55, 5.94, 33.27),
(4, 'Karl reyes', 'January 2 2000', 'January 2 2000', 1, 4, 6, 16.01, 5.94, 33.27),
(5, 'Dunhill Lapiceros', 'January 2 2000', 'January 2 2000', 3, 4, 2, 41.6, 2.17, -59.55),
(6, 'sean tandog', 'January 2 2000', 'January 2 2000', 3, 2, 1, 120, 3.56, -80.17),
(7, 'Sean tandog Reyes', 'January 2 2000', 'January 2 2000', 2, 1, 7, 354.33, 13.88, -32.21),
(8, 'Dunhill P Lapiceros', 'January 2 2000', 'January 2 2000', 3, 5, 2, 327.2, 13.11, -217.65);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cemetery_tb`
--
ALTER TABLE `cemetery_tb`
  ADD PRIMARY KEY (`UID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cemetery_tb`
--
ALTER TABLE `cemetery_tb`
  MODIFY `UID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
