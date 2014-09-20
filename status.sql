-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 20, 2014 at 03:12 PM
-- Server version: 5.5.38
-- PHP Version: 5.4.4-14+deb7u12

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `thermostat`
--

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE IF NOT EXISTS `status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(128) COLLATE utf8_bin NOT NULL,
  `value` varchar(128) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=22 ;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `description`, `value`) VALUES
(1, 'Current Temperature', '75.0866'),
(2, 'Current Set Point', '71'),
(3, 'Mode (heat/cool/off)', 'off'),
(4, 'Variance', '1.5'),
(5, 'Units (C/F)', 'F'),
(6, 'Fan (auto/on)', 'auto'),
(7, 'Last Occupied (time)', '2014-09-17 22:14:48'),
(8, 'Unoccupied Heat Set Point', '60'),
(9, 'Unoccupied Cool Set Point', '77'),
(11, 'Night Occupied Heat Set Point', '62'),
(12, 'Day Occupied Heat Set Point', '68'),
(13, 'Night Occupied Cool Set Point', '71'),
(14, 'Day Occupied Cool Set Point', '73'),
(15, 'PJs iPhone', 'Pauls-iPhone'),
(16, 'KTs iPhone', '10.0.10.18'),
(17, 'Override (True/False)', 'True'),
(18, 'Occupied (True/False)', 'False'),
(19, 'Heat Status (on/off)', 'off'),
(20, 'Cool Status (on/off)', 'off'),
(21, 'Fan Status (on/off)', 'off');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
