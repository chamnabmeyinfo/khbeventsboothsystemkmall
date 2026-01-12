-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 11, 2026 at 03:40 PM
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
-- Database: `khbeventskmallxmas`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset`
--

CREATE TABLE `asset` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` int(11) NOT NULL DEFAULT 1,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `asset`
--

INSERT INTO `asset` (`id`, `name`, `type`, `status`) VALUES
(1, '10A', 1, 1),
(2, '20A', 1, 1),
(3, '30A', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `break_duration` int(11) DEFAULT 0 COMMENT 'Break duration in minutes',
  `total_hours` decimal(5,2) DEFAULT NULL,
  `status` enum('present','absent','late','half-day','on-leave','holiday') DEFAULT 'present',
  `late_minutes` int(11) DEFAULT 0,
  `overtime_hours` decimal(5,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `id` int(11) NOT NULL,
  `event_id` bigint(20) UNSIGNED DEFAULT NULL,
  `floor_plan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `clientid` int(11) NOT NULL,
  `boothid` mediumtext NOT NULL,
  `date_book` datetime NOT NULL,
  `userid` int(11) NOT NULL,
  `type` tinyint(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booth`
--

CREATE TABLE `booth` (
  `id` int(11) NOT NULL,
  `floor_plan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `booth_number` varchar(45) NOT NULL,
  `type` tinyint(2) NOT NULL DEFAULT 2,
  `price` double NOT NULL DEFAULT 0,
  `status` tinyint(2) NOT NULL DEFAULT 1,
  `client_id` int(11) DEFAULT 0,
  `userid` int(11) DEFAULT NULL,
  `bookid` int(11) DEFAULT 0,
  `category_id` int(11) DEFAULT NULL,
  `sub_category_id` int(11) DEFAULT NULL,
  `asset_id` int(11) DEFAULT NULL,
  `booth_type_id` int(11) DEFAULT NULL,
  `position_x` decimal(10,2) DEFAULT NULL,
  `position_y` decimal(10,2) DEFAULT NULL,
  `width` decimal(10,2) DEFAULT NULL,
  `height` decimal(10,2) DEFAULT NULL,
  `rotation` decimal(10,2) DEFAULT 0.00,
  `z_index` int(11) DEFAULT 10,
  `font_size` int(11) DEFAULT 14,
  `border_width` int(11) DEFAULT 2,
  `border_radius` int(11) DEFAULT 6,
  `opacity` decimal(3,2) DEFAULT 1.00,
  `background_color` varchar(50) DEFAULT '#ffffff',
  `border_color` varchar(50) DEFAULT '#007bff',
  `text_color` varchar(50) DEFAULT '#000000',
  `font_weight` varchar(20) DEFAULT '700',
  `font_family` varchar(255) DEFAULT 'Arial, sans-serif',
  `text_align` varchar(20) DEFAULT 'center',
  `box_shadow` varchar(255) DEFAULT '0 2px 8px rgba(0,0,0,0.2)'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booth`
--

INSERT INTO `booth` (`id`, `floor_plan_id`, `booth_number`, `type`, `price`, `status`, `client_id`, `userid`, `bookid`, `category_id`, `sub_category_id`, `asset_id`, `booth_type_id`, `position_x`, `position_y`, `width`, `height`, `rotation`, `z_index`, `font_size`, `border_width`, `border_radius`, `opacity`, `background_color`, `border_color`, `text_color`, `font_weight`, `font_family`, `text_align`, `box_shadow`) VALUES
(333, 1, 'D18', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4890.00, 648.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(332, 1, 'D17', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4832.00, 648.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(331, 1, 'D16', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4770.00, 648.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(330, 1, 'D15', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4710.00, 648.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(329, 1, 'D14', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4652.00, 648.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(328, 1, 'D13', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5351.00, 454.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(327, 1, 'D12', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5291.00, 452.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(326, 1, 'D11', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5234.00, 452.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(325, 1, 'D10', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5174.00, 454.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(324, 1, 'D09', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5113.00, 452.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(322, 1, 'D07', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4992.00, 451.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(321, 1, 'D06', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4930.00, 451.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(320, 1, 'D05', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4869.00, 451.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(319, 1, 'D04', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4809.00, 452.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(318, 1, 'D03', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4749.00, 452.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(317, 1, 'D02', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4689.00, 451.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(315, 1, 'C26', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4399.00, 652.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(314, 1, 'C25', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4337.00, 652.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(312, 1, 'C23', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4217.00, 650.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(311, 1, 'C22', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4155.00, 650.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(310, 1, 'C21', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4093.00, 650.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(309, 1, 'C20', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4033.00, 650.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(308, 1, 'C19', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3973.00, 650.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(305, 1, 'C16', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3792.00, 650.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(304, 1, 'C15', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3732.00, 650.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(303, 1, 'C14', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3670.00, 650.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(302, 1, 'C13', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4397.00, 452.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(301, 1, 'C12', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4336.00, 450.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(299, 1, 'C10', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4218.00, 449.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(298, 1, 'C09', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4156.00, 449.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(297, 1, 'C08', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4096.00, 449.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(296, 1, 'C07', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4033.00, 449.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(295, 1, 'C06', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3971.00, 449.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(293, 1, 'C04', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3851.00, 447.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(289, 1, 'B48', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3352.00, 642.00, 60.00, 60.00, 0.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(288, 1, 'B47', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3292.00, 642.00, 60.00, 60.00, 0.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(287, 1, 'B46', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3231.00, 642.00, 60.00, 60.00, 0.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(286, 1, 'B45', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3171.00, 642.00, 60.00, 60.00, 0.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(283, 1, 'B42', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2987.00, 642.00, 60.00, 60.00, 0.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(282, 1, 'B41', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2931.00, 642.00, 60.00, 60.00, 0.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(281, 1, 'B40', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2873.00, 642.00, 60.00, 60.00, 0.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(280, 1, 'B39', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2732.00, 641.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(279, 1, 'B38', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2676.00, 654.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(278, 1, 'B37', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2617.00, 668.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(277, 1, 'B36', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2557.00, 681.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(274, 1, 'B33', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2379.00, 717.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(273, 1, 'B32', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2319.00, 729.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(272, 1, 'B31', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2261.00, 743.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(271, 1, 'B30', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2201.00, 755.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(270, 1, 'B29', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2141.00, 767.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(269, 1, 'B28', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2007.00, 799.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(268, 1, 'B27', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1948.00, 811.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(267, 1, 'B26', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1891.00, 823.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(266, 1, 'B25', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1831.00, 835.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(265, 1, 'B24', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3354.00, 452.00, 60.00, 60.00, 0.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(264, 1, 'B23', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3291.00, 452.00, 60.00, 60.00, 0.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(262, 1, 'B21', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3170.00, 452.00, 60.00, 60.00, 0.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(261, 1, 'B20', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3110.00, 452.00, 60.00, 60.00, 0.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(260, 1, 'B19', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3048.00, 452.00, 60.00, 60.00, 0.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(259, 1, 'B18', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2987.00, 452.00, 60.00, 60.00, 0.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(255, 1, 'B14', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2639.00, 480.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(254, 1, 'B13', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2580.00, 493.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(253, 1, 'B12', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2520.00, 506.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(251, 1, 'B10', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2401.00, 531.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(250, 1, 'B09', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2342.00, 544.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(249, 1, 'B08', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2283.00, 556.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(248, 1, 'B07', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2225.00, 569.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(247, 1, 'B06', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2165.00, 582.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(242, 1, 'B01', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1795.00, 657.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(290, 1, 'C01', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3670.00, 445.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(241, 1, 'A49', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 10, 14, 2, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(240, 1, 'A48', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 10, 14, 2, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(239, 1, 'A47', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 10, 14, 2, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(237, 1, 'A45', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2693.99, 1715.00, 50.00, 50.00, 20.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(236, 1, 'A44', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2742.00, 1732.00, 50.00, 50.00, 18.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(235, 1, 'A43', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2791.51, 1749.98, 50.00, 50.00, 16.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(234, 1, 'A42', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2924.00, 1611.00, 50.00, 50.00, 16.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(233, 1, 'A41', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2937.14, 1562.00, 50.00, 50.00, 16.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(232, 1, 'A40', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2850.00, 1522.00, 50.00, 50.00, 12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(231, 1, 'A39', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2799.00, 1510.00, 50.00, 50.00, 12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(227, 1, 'A35', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2513.00, 1465.00, 50.00, 50.00, -70.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(226, 1, 'A34', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2495.00, 1515.00, 50.00, 50.00, -70.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(225, 1, 'A33', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2407.99, 1420.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(224, 1, 'A32', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2356.00, 1432.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(223, 1, 'A31', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2257.00, 1452.01, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(194, 1, 'A02', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1522.27, 1608.64, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(195, 1, 'A03', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1571.66, 1598.60, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(196, 1, 'A04', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1619.56, 1587.54, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(197, 1, 'A05', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1672.47, 1576.50, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(198, 1, 'A06', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1723.00, 1565.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(199, 1, 'A07', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1439.00, 1768.01, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(200, 1, 'A08', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1487.00, 1759.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(201, 1, 'A09', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1565.00, 1711.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(202, 1, 'A10', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1618.00, 1700.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(203, 1, 'A11', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1668.00, 1689.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(204, 1, 'A12', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1717.00, 1679.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(205, 1, 'A13', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1767.99, 1667.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(206, 1, 'A14', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1866.00, 1653.00, 50.00, 50.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(207, 1, 'A15', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1916.00, 1641.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(208, 1, 'A16', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1966.00, 1630.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(209, 1, 'A17', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2014.00, 1619.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(210, 1, 'A18', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2063.00, 1607.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(211, 1, 'A19', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2115.00, 1599.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(212, 1, 'A20', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1904.00, 1589.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(213, 1, 'A21', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1954.00, 1578.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(214, 1, 'A22', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2006.00, 1568.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(215, 1, 'A23', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2053.00, 1557.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(216, 1, 'A24', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2104.00, 1546.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(217, 1, 'A25', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2206.00, 1571.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(218, 1, 'A26', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2257.00, 1557.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(219, 1, 'A27', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2349.00, 1546.00, 50.00, 50.00, 6.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(220, 1, 'A28', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2399.00, 1553.00, 50.00, 50.00, 6.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(323, 1, 'D08', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5052.00, 451.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(316, 1, 'D01', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4628.00, 451.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(313, 1, 'C24', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4276.00, 650.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(307, 1, 'C18', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3911.00, 650.00, 60.00, 60.00, 0.00, 10, 26, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(306, 1, 'C17', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3850.00, 650.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(300, 1, 'C11', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4274.00, 450.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(294, 1, 'C05', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3911.00, 447.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(285, 1, 'B44', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3107.00, 642.00, 60.00, 60.00, 0.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(284, 1, 'B43', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3047.00, 642.00, 60.00, 60.00, 0.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(276, 1, 'B35', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2498.00, 693.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(275, 1, 'B34', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2438.00, 705.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(263, 1, 'B22', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3230.00, 452.00, 60.00, 60.00, 0.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(258, 1, 'B17', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2926.00, 452.00, 60.00, 60.00, 0.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(257, 1, 'B16', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2866.00, 452.00, 60.00, 60.00, 0.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(256, 1, 'B15', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2699.00, 467.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(252, 1, 'B11', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2462.00, 519.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(246, 1, 'B05', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2105.00, 594.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(245, 1, 'B04', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1972.00, 622.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(244, 1, 'B03', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1911.00, 634.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(243, 1, 'B02', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1853.00, 646.00, 60.00, 60.00, -12.00, 10, 27, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(291, 1, 'C02', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3730.00, 445.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(238, 1, 'A46', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2643.00, 1695.00, 50.00, 50.00, 22.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(228, 1, 'A36', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2532.00, 1417.00, 50.00, 50.00, -70.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(222, 1, 'A30', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2206.10, 1463.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(221, 1, 'A29', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2160.00, 1474.00, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(193, 1, 'A01', 2, 500, 2, 173, 61, 0, NULL, NULL, NULL, NULL, 1475.76, 1617.38, 50.00, 50.00, -12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(230, 1, 'A38', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2748.00, 1498.00, 50.00, 50.00, 12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(229, 1, 'A37', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2699.00, 1487.00, 50.00, 50.00, 12.00, 10, 22, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(341, 1, 'D26', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5377.00, 648.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(340, 1, 'D25', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5317.00, 646.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(339, 1, 'D24', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5259.00, 646.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(338, 1, 'D23', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5197.00, 646.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(337, 1, 'D22', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5136.00, 648.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(336, 1, 'D21', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5074.00, 648.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(335, 1, 'D20', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5012.00, 646.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(334, 1, 'D19', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4952.00, 648.00, 60.00, 60.00, 0.00, 10, 27, 5, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(292, 1, 'C03', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3789.00, 446.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(364, 4, 'A01', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 670.00, 1370.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(365, 4, 'A02', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 670.00, 1500.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(366, 4, 'A03', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 670.00, 1870.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(367, 4, 'A04', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 670.00, 2000.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(368, 4, 'A05', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 670.00, 2130.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(369, 4, 'A06', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 670.00, 2260.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(370, 4, 'A07', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 670.00, 2385.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(371, 4, 'A08', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 669.00, 2509.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(372, 4, 'A09', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 669.00, 2638.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(373, 4, 'A10', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 669.00, 2767.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(374, 4, 'A11', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 668.00, 2893.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(375, 4, 'A12', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 668.00, 3025.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(376, 4, 'A13', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 671.00, 3156.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(377, 4, 'A14', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 926.00, 3212.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(378, 4, 'A15', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1055.00, 3212.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(379, 4, 'A16', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 927.00, 3083.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(380, 4, 'A17', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1055.00, 3085.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(381, 4, 'A18', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 924.00, 2833.00, 130.00, 130.00, 0.00, 10, 44, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(382, 4, 'A19', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1050.00, 2835.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(383, 4, 'A20', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 930.00, 2705.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(384, 4, 'A21', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1057.00, 2707.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(385, 4, 'A22', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 926.00, 2463.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(386, 4, 'A23', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1062.00, 2462.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(387, 4, 'A24', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 925.00, 2330.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(388, 4, 'A25', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1059.00, 2333.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(389, 4, 'A26', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 928.00, 1963.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(390, 4, 'A27', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1060.00, 1964.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(391, 4, 'A28', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 926.00, 1834.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(392, 4, 'A29', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1060.00, 1830.00, 130.00, 130.00, 0.00, 10, 37, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(393, 4, 'A30', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 928.00, 1570.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(394, 4, 'A31', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1060.00, 1569.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(395, 4, 'A32', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 928.00, 1440.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(396, 4, 'A33', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1060.00, 1440.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(397, 4, 'A34', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1291.00, 1441.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(398, 4, 'A35', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1420.00, 1443.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(399, 4, 'A36', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1290.00, 1569.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(400, 4, 'A37', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1422.00, 1568.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(401, 4, 'A38', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1290.00, 1833.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(402, 4, 'A39', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1424.00, 1834.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(403, 4, 'A40', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1291.00, 1963.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(404, 4, 'A41', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1425.00, 1961.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(405, 4, 'A42', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1291.00, 2335.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(406, 4, 'A43', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1422.00, 2334.00, 130.00, 130.00, 0.00, 10, 36, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(407, 4, 'A44', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1289.00, 2464.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(408, 4, 'A45', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1423.00, 2460.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(409, 4, 'A46', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1285.00, 2702.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(410, 4, 'A47', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1419.00, 2704.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(411, 4, 'A48', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1287.00, 2832.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(412, 4, 'A49', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1419.00, 2834.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px');
INSERT INTO `booth` (`id`, `floor_plan_id`, `booth_number`, `type`, `price`, `status`, `client_id`, `userid`, `bookid`, `category_id`, `sub_category_id`, `asset_id`, `booth_type_id`, `position_x`, `position_y`, `width`, `height`, `rotation`, `z_index`, `font_size`, `border_width`, `border_radius`, `opacity`, `background_color`, `border_color`, `text_color`, `font_weight`, `font_family`, `text_align`, `box_shadow`) VALUES
(413, 4, 'A50', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1290.00, 3090.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(414, 4, 'A51', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1418.00, 3085.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(415, 4, 'A52', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1287.00, 3214.00, 130.00, 130.00, 0.00, 10, 43, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(416, 4, 'A53', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1419.00, 3212.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(417, 4, 'B01', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1655.00, 1439.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(418, 4, 'B02', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1783.00, 1439.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(419, 4, 'B03', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1655.00, 1565.00, 130.00, 130.00, 0.00, 10, 35, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(420, 4, 'B04', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1785.00, 1566.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(421, 4, 'B05', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1654.00, 1837.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(422, 4, 'B06', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1785.00, 1839.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(423, 4, 'B07', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1654.00, 1966.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(424, 4, 'B08', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1784.00, 1967.00, 130.00, 130.00, 0.00, 10, 36, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(425, 4, 'B09', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1655.00, 2337.00, 130.00, 130.00, 0.00, 10, 41, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(426, 4, 'B10', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1784.00, 2339.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(427, 4, 'B11', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1654.00, 2466.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(428, 4, 'B12', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1785.00, 2466.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(429, 4, 'B13', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1651.00, 2709.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(430, 4, 'B14', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1781.00, 2708.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(431, 4, 'B15', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1651.00, 2836.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(432, 4, 'B16', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1780.00, 2836.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(433, 4, 'B17', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1651.00, 3086.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(434, 4, 'B18', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1783.00, 3086.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(435, 4, 'B19', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1652.00, 3215.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(436, 4, 'B20', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1783.00, 3213.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(437, 4, 'B21', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2003.00, 3215.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(438, 4, 'B22', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2134.00, 3215.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(439, 4, 'B23', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2003.00, 3087.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(440, 4, 'B24', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2134.00, 3088.00, 130.00, 130.00, 0.00, 10, 39, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(441, 4, 'B25', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2004.00, 2834.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(442, 4, 'B26', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2135.00, 2835.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(443, 4, 'B27', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2003.00, 2708.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(444, 4, 'B28', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2135.00, 2710.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(445, 4, 'B29', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2008.00, 2465.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(446, 4, 'B30', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2137.00, 2463.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(447, 4, 'B31', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2006.00, 2335.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(448, 4, 'B32', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2136.00, 2337.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(449, 4, 'B33', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2008.00, 1963.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(450, 4, 'B34', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2138.00, 1962.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(451, 4, 'B35', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2008.00, 1835.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(452, 4, 'B36', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2139.00, 1836.00, 130.00, 130.00, 0.00, 10, 37, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(453, 4, 'B37', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2004.00, 1568.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(454, 4, 'B38', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2135.00, 1570.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(455, 4, 'B39', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2004.00, 1442.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(456, 4, 'B40', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2135.00, 1445.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(457, 4, 'C01', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2360.00, 1445.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(459, 4, 'C02', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2495.00, 1445.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(460, 4, 'C03', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2360.00, 1570.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(461, 4, 'C04', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2496.00, 1573.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(462, 4, 'C05', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2361.00, 1837.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(463, 4, 'C06', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2492.00, 1837.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(464, 4, 'C07', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2361.00, 1966.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(465, 4, 'C08', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2494.00, 1966.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(466, 4, 'C09', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2361.00, 2336.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(467, 4, 'C10', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2493.00, 2336.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(468, 4, 'C11', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2361.00, 2464.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(469, 4, 'C12', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2492.00, 2462.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(470, 4, 'C13', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2359.00, 2707.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(471, 4, 'C14', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2490.00, 2707.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(472, 4, 'C15', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2359.00, 2833.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(473, 4, 'C16', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2490.00, 2834.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(474, 4, 'C17', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2357.00, 3088.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(475, 4, 'C18', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2487.00, 3087.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(476, 4, 'C19', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2359.00, 3215.00, 130.00, 130.00, 0.00, 10, 37, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(477, 4, 'C20', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2487.00, 3213.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(478, 4, 'C21', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2713.00, 3216.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(479, 4, 'C22', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2843.00, 3215.00, 130.00, 130.00, 0.00, 10, 44, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(480, 4, 'C23', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2711.00, 3086.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(481, 4, 'C24', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2842.00, 3088.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(482, 4, 'C25', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2711.00, 2834.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(483, 4, 'C26', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2842.00, 2836.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(484, 4, 'C27', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2711.00, 2705.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(485, 4, 'C28', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2842.00, 2707.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(486, 4, 'C29', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2715.00, 2464.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(487, 4, 'C30', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2846.00, 2465.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(488, 4, 'C31', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2715.00, 2336.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(489, 4, 'C32', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2845.00, 2336.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(490, 4, 'C33', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2718.00, 1966.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(491, 4, 'C34', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2847.00, 1964.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(492, 4, 'C35', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2715.00, 1835.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(493, 4, 'C36', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2848.00, 1836.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(494, 4, 'C37', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2715.00, 1571.00, 130.00, 130.00, 0.00, 10, 39, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(495, 4, 'C38', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2846.00, 1571.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(496, 4, 'C39', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2715.00, 1444.00, 130.00, 130.00, 0.00, 10, 38, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(497, 4, 'C40', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2846.00, 1445.00, 130.00, 130.00, 0.00, 10, 40, 7, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(498, 4, 'D01', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3080.00, 1835.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(499, 4, 'D02', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3080.00, 1965.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(500, 4, 'D03', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3215.00, 1835.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(501, 4, 'D04', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3215.00, 1965.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(502, 4, 'D05', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3082.00, 2332.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(503, 4, 'D06', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3214.00, 2332.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(504, 4, 'D07', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3082.00, 2461.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(505, 4, 'D08', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3214.00, 2461.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(506, 4, 'D09', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3079.00, 2703.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(507, 4, 'D10', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3212.00, 2704.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(508, 4, 'D11', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3079.00, 2832.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(509, 4, 'D12', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3212.00, 2833.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(510, 4, 'D13', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3080.00, 3080.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(511, 4, 'D14', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3212.00, 3082.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(512, 4, 'D15', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3079.00, 3214.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(513, 4, 'D16', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3212.00, 3212.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(514, 4, 'D17', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3441.00, 1834.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(515, 4, 'D18', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3441.00, 1962.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(516, 4, 'D19', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3441.00, 2335.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(517, 4, 'D20', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3441.00, 2464.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(518, 4, 'D21', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3437.00, 2706.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(519, 4, 'D22', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3437.00, 2833.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(520, 4, 'D23', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3437.00, 3087.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(521, 4, 'D24', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3439.00, 3214.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(522, 4, 'D25', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3568.00, 3214.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(523, 4, 'D26', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3567.00, 3086.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(524, 4, 'D27', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3570.00, 2835.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(525, 4, 'D28', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3569.00, 2707.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(526, 4, 'D29', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3572.00, 2463.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(527, 4, 'D30', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3571.00, 2334.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(528, 4, 'D31', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3572.00, 1963.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(529, 4, 'D32', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3573.00, 1836.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(530, 4, 'D33', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3798.00, 1834.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(531, 4, 'D34', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3797.00, 1963.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(532, 4, 'D35', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3796.00, 2332.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(533, 4, 'D36', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3797.00, 2460.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(534, 4, 'D37', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3794.00, 2705.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(535, 4, 'D38', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3794.00, 2830.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(536, 4, 'D39', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3793.00, 3083.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(537, 4, 'D40', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3792.00, 3212.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(538, 4, 'D41', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3931.00, 1835.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(539, 4, 'D42', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3930.00, 1962.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(540, 4, 'D43', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3927.00, 2332.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(541, 4, 'D44', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3926.00, 2463.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(542, 4, 'D45', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3927.00, 2706.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(543, 4, 'D46', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3926.00, 2831.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(544, 4, 'D47', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3925.00, 3084.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(545, 4, 'D48', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3925.00, 3214.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(546, 4, 'D49', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4154.00, 1836.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(547, 4, 'D50', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4154.00, 1962.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(548, 4, 'D51', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4154.00, 2334.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(549, 4, 'D52', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4154.00, 2463.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(550, 4, 'D53', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4150.00, 2706.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(551, 4, 'D54', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4150.00, 2834.00, 132.00, 132.00, 0.00, 10, 26, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(552, 4, 'D55', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4150.00, 3084.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(553, 4, 'D56', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4150.00, 3215.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(554, 4, 'D57', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4282.00, 3214.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(555, 4, 'D58', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4282.00, 3084.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(556, 4, 'D59', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4282.00, 2834.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(557, 4, 'D60', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4282.00, 2706.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(558, 4, 'D61', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4286.00, 2462.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(559, 4, 'D62', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4286.00, 2334.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(560, 4, 'D63', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4284.00, 1962.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(561, 4, 'D64', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4286.00, 1836.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(562, 4, 'D65', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3082.00, 1440.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(563, 4, 'D66', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3214.00, 1440.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(564, 4, 'D67', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3082.00, 1572.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(565, 4, 'D68', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3214.00, 1572.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(566, 4, 'D69', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4155.00, 1440.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(567, 4, 'D70', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4285.00, 1440.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(568, 4, 'D71', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4155.00, 1570.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(569, 4, 'D72', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4285.00, 1570.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(570, 4, 'E01', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4505.00, 910.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(571, 4, 'E02', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4635.00, 910.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(572, 4, 'E03', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4505.00, 1440.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(573, 4, 'E04', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4635.00, 1440.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(574, 4, 'E05', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4505.00, 1565.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(575, 4, 'E06', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4635.00, 1565.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(576, 4, 'E07', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4505.00, 1835.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(577, 4, 'E08', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4635.00, 1835.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(578, 4, 'E09', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4505.00, 1960.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(579, 4, 'E10', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4635.00, 1960.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(580, 4, 'E11', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4505.00, 2335.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(581, 4, 'E12', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4635.00, 2335.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(582, 4, 'E13', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4505.00, 2460.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(583, 4, 'E14', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4635.00, 2460.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(584, 4, 'E15', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4500.00, 2705.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(585, 4, 'E16', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4630.00, 2705.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(586, 4, 'E17', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4500.00, 2830.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(587, 4, 'E18', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4630.00, 2830.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(588, 4, 'E19', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4500.00, 3085.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(589, 4, 'E20', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4630.00, 3085.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(590, 4, 'E21', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4500.00, 3210.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(591, 4, 'E22', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4630.00, 3215.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(592, 4, 'E23', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4855.00, 3215.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(593, 4, 'E24', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4990.00, 3215.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(594, 4, 'E25', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4855.00, 3085.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(595, 4, 'E26', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4985.00, 3085.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(596, 4, 'E27', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4855.00, 2835.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(597, 4, 'E28', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4985.00, 2835.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(598, 4, 'E29', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4855.00, 2705.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(599, 4, 'E30', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4985.00, 2705.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(600, 4, 'E31', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4860.00, 2465.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(601, 4, 'E32', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4990.00, 2465.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(602, 4, 'E33', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4860.00, 2335.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(603, 4, 'E34', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4990.00, 2335.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(604, 4, 'E35', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4850.00, 1960.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(605, 4, 'E36', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4985.00, 1960.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(606, 4, 'E37', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4850.00, 1835.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(607, 4, 'E38', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4985.00, 1835.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(608, 4, 'E39', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4855.00, 1570.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(609, 4, 'E40', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4990.00, 1570.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(610, 4, 'E41', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4855.00, 1440.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px');
INSERT INTO `booth` (`id`, `floor_plan_id`, `booth_number`, `type`, `price`, `status`, `client_id`, `userid`, `bookid`, `category_id`, `sub_category_id`, `asset_id`, `booth_type_id`, `position_x`, `position_y`, `width`, `height`, `rotation`, `z_index`, `font_size`, `border_width`, `border_radius`, `opacity`, `background_color`, `border_color`, `text_color`, `font_weight`, `font_family`, `text_align`, `box_shadow`) VALUES
(611, 4, 'E42', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4990.00, 1440.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(612, 4, 'E43', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4860.00, 910.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(613, 4, 'E44', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4995.00, 910.00, 132.00, 132.00, 0.00, 10, 40, 10, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(614, 4, 'F01', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5220.00, 910.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(615, 4, 'F02', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5350.00, 910.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(616, 4, 'F03', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5220.00, 1440.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(617, 4, 'F04', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5350.00, 1440.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(618, 4, 'F05', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5220.00, 1570.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(619, 4, 'F06', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5350.00, 1570.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(620, 4, 'F07', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5220.00, 1835.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(621, 4, 'F08', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5350.00, 1835.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(622, 4, 'F09', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5220.00, 1965.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(623, 4, 'F10', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5350.00, 1965.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(624, 4, 'F11', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5220.00, 2335.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(625, 4, 'F12', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5350.00, 2335.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(626, 4, 'F13', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5220.00, 2460.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(627, 4, 'F14', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5350.00, 2460.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(628, 4, 'F15', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5215.00, 2705.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(629, 4, 'F16', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5350.00, 2705.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(630, 4, 'F17', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5215.00, 2835.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(631, 4, 'F18', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5350.00, 2835.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(632, 4, 'F19', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5215.00, 3085.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(633, 4, 'F20', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5350.00, 3085.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(634, 4, 'F21', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5215.00, 3215.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(635, 4, 'F22', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5350.00, 3215.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(636, 4, 'F23', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5585.00, 910.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(637, 4, 'F24', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5720.00, 910.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(638, 4, 'F25', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5585.00, 1440.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(639, 4, 'F26', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5720.00, 1440.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(640, 4, 'F27', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5585.00, 1565.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(641, 4, 'F28', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5720.00, 1565.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(642, 4, 'F29', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5585.00, 1835.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(643, 4, 'F30', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5720.00, 1835.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(644, 4, 'F31', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5585.00, 1960.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(645, 4, 'F32', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5720.00, 1960.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(646, 4, 'F33', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5585.00, 2330.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(647, 4, 'F34', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5720.00, 2330.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(648, 4, 'F35', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5585.00, 2460.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(649, 4, 'F36', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5720.00, 2460.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(650, 4, 'F37', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5585.00, 2705.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(651, 4, 'F38', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5715.00, 2705.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(652, 4, 'F39', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5585.00, 2835.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(653, 4, 'F40', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5715.00, 2835.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(654, 4, 'F41', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5580.00, 3085.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(655, 4, 'F42', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5715.00, 3085.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(656, 4, 'F43', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5580.00, 3215.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(657, 4, 'F44', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5715.00, 3215.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(658, 4, 'F45', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5955.00, 3110.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(659, 4, 'F46', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5955.00, 2985.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(660, 4, 'F47', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5955.00, 2860.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(661, 4, 'F48', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5955.00, 2730.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(662, 4, 'F49', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5955.00, 2370.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(663, 4, 'F50', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5955.00, 2250.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(664, 4, 'F51', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5955.00, 2020.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(665, 4, 'F52', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5955.00, 1890.00, 134.00, 134.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(666, 4, 'F53', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 6225.00, 1435.00, 135.00, 180.00, 0.00, 10, 40, 8, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(667, 4, 'F54', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 6095.00, 1435.00, 130.00, 180.00, 0.00, 20, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(668, 4, 'F55', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 6095.00, 1270.00, 135.00, 170.00, 0.00, 20, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(669, 4, 'H01', 2, 1500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 965.00, 3695.00, 395.00, 160.00, 0.00, 10, 40, 7, 2, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(670, 4, 'H02', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1855.00, 3700.00, 395.00, 160.00, 0.00, 10, 40, 7, 2, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(671, 4, 'H03', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2255.00, 3700.00, 395.00, 160.00, 0.00, 10, 40, 7, 2, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(672, 4, 'H04', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3085.00, 3700.00, 395.00, 160.00, 0.00, 10, 40, 7, 2, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(673, 4, 'H05', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3485.00, 3700.00, 395.00, 160.00, 0.00, 10, 40, 7, 2, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(674, 4, 'H06', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4260.00, 3700.00, 395.00, 160.00, 0.00, 10, 40, 7, 2, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(675, 4, 'H07', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 4655.00, 3700.00, 395.00, 160.00, 0.00, 10, 40, 7, 2, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(676, 4, 'H08', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 5545.00, 3700.00, 395.00, 160.00, 0.00, 10, 40, 7, 2, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(677, 4, 'H09', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 6490.00, 3165.00, 175.00, 425.00, 0.00, 10, 40, 7, 2, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(678, 4, 'H10', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 6490.00, 2415.00, 175.00, 430.00, 0.00, 10, 40, 7, 2, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(679, 4, 'H11', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 6490.00, 1980.00, 175.00, 435.00, 0.00, 10, 40, 7, 2, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(680, 4, 'EX01', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 10, 14, 2, 6, 1.00, '#ffffff', '#007bff', '#000000', '700', 'Arial, sans-serif', 'center', '0 2px 8px rgba(0,0,0,0.2)');

-- --------------------------------------------------------

--
-- Table structure for table `booth_type`
--

CREATE TABLE `booth_type` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booth_type`
--

INSERT INTO `booth_type` (`id`, `name`, `status`) VALUES
(1, 'Space with booth', 1),
(2, 'Space only', 1);

-- --------------------------------------------------------

--
-- Table structure for table `canvas_settings`
--

CREATE TABLE `canvas_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `floor_plan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `canvas_width` int(11) NOT NULL DEFAULT 1200,
  `canvas_height` int(11) NOT NULL DEFAULT 800,
  `canvas_resolution` int(11) NOT NULL DEFAULT 300,
  `grid_size` int(11) NOT NULL DEFAULT 10,
  `zoom_level` decimal(5,2) NOT NULL DEFAULT 1.00,
  `pan_x` decimal(10,2) NOT NULL DEFAULT 0.00,
  `pan_y` decimal(10,2) NOT NULL DEFAULT 0.00,
  `floorplan_image` varchar(255) DEFAULT NULL,
  `grid_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `snap_to_grid` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `canvas_settings`
--

INSERT INTO `canvas_settings` (`id`, `floor_plan_id`, `canvas_width`, `canvas_height`, `canvas_resolution`, `grid_size`, `zoom_level`, `pan_x`, `pan_y`, `floorplan_image`, `grid_enabled`, `snap_to_grid`, `created_at`, `updated_at`) VALUES
(1, NULL, 6963, 4924, 300, 10, 0.27, 0.00, 0.00, NULL, 0, 1, '2026-01-07 06:57:00', '2026-01-11 05:35:22'),
(3, 4, 6963, 4924, 300, 5, 0.20, 0.00, 0.00, 'images/floor-plans/1768066307_floor_plan_4.jpg', 1, 1, '2026-01-10 15:19:50', '2026-01-11 14:16:07'),
(4, 1, 6250, 3125, 300, 10, 0.31, 0.00, 0.00, 'images/floor-plans/1768066318_floor_plan_1.jpg', 0, 1, '2026-01-10 15:19:50', '2026-01-11 08:46:05');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `parent_id` int(11) DEFAULT 0,
  `limit` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 1,
  `avatar` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `parent_id`, `limit`, `status`, `avatar`, `cover_image`, `create_time`, `update_time`) VALUES
(11, 'ប្រហិត', 10, 7, 1, NULL, NULL, '2023-10-23 10:31:31', '2023-11-22 09:28:00'),
(10, 'F&B', 0, 70, 1, NULL, NULL, '2023-10-23 10:31:18', '2023-12-19 19:12:26'),
(12, 'តែបៃតង', 10, 5, 1, NULL, NULL, '2023-10-23 10:31:41', '2023-12-19 19:12:36'),
(13, 'កាហ្វេ', 10, 6, 1, NULL, NULL, '2023-10-23 10:31:53', '2023-12-19 19:12:42'),
(14, 'មឹក', 10, 5, 1, NULL, NULL, '2023-10-23 10:32:15', NULL),
(15, 'បុកល្ហុង', 10, 5, 1, NULL, NULL, '2023-10-23 10:32:24', '2023-12-19 19:12:55'),
(16, 'ត្រីស៊ូម៉ុង', 10, 5, 1, NULL, NULL, '2023-10-23 10:32:44', '2023-12-13 11:57:32'),
(17, 'ការ៉េម', 10, 5, 1, NULL, NULL, '2023-10-23 10:32:55', '2023-12-19 16:24:52'),
(18, 'សាច់អាំង', 10, 5, 1, NULL, NULL, '2023-10-23 10:33:18', '2023-12-19 19:13:06'),
(19, 'Cosmetics ', 0, 15, 1, NULL, NULL, '2023-10-23 11:10:43', NULL),
(20, 'Cosmetics', 19, 15, 1, NULL, NULL, '2023-10-23 11:10:55', NULL),
(21, 'Fashion', 0, 40, 1, NULL, NULL, '2023-10-23 11:11:16', '2023-11-21 11:25:47'),
(22, 'Accessory', 21, 5, 1, NULL, NULL, '2023-10-23 11:11:36', NULL),
(23, 'សំលៀកបំពាក់', 21, 10, 1, NULL, NULL, '2023-10-23 11:11:58', NULL),
(24, 'ស្បែកជើង', 21, 5, 1, NULL, NULL, '2023-10-23 11:12:08', NULL),
(25, 'កាបូប', 21, 5, 1, NULL, NULL, '2023-10-23 11:12:24', NULL),
(26, 'សម្ភារៈកីឡា', 21, 7, 1, NULL, NULL, '2023-10-23 11:12:59', '2023-12-18 22:57:59'),
(27, 'Automobile', 0, 5, 1, NULL, NULL, '2023-10-23 11:13:14', '2023-10-23 11:19:24'),
(28, 'Auto', 27, 3, 1, NULL, NULL, '2023-10-23 11:13:42', NULL),
(29, 'Motorbike', 27, 3, 1, NULL, NULL, '2023-10-23 11:13:54', NULL),
(30, 'IT/Electronics', 0, 15, 1, NULL, NULL, '2023-10-23 11:14:20', NULL),
(31, 'Electronics', 30, 10, 1, NULL, NULL, '2023-10-23 11:14:41', NULL),
(32, 'Mobile App', 30, 5, 1, NULL, NULL, '2023-10-23 11:14:50', NULL),
(33, 'General Product ', 0, 30, 1, NULL, NULL, '2023-10-23 11:16:39', NULL),
(34, 'Local product', 33, 15, 1, NULL, NULL, '2023-10-23 11:16:55', NULL),
(35, 'Consuming products', 33, 15, 1, NULL, NULL, '2023-10-23 11:17:06', NULL),
(36, 'Property', 0, 8, 1, NULL, NULL, '2023-10-23 11:17:46', '2023-11-29 21:26:09'),
(37, 'Property', 36, 8, 1, NULL, NULL, '2023-10-23 11:17:55', '2023-11-29 21:30:39'),
(38, 'Finance', 0, 5, 1, NULL, NULL, '2023-10-23 11:18:16', NULL),
(39, 'Finance', 38, 5, 1, NULL, NULL, '2023-10-23 11:18:25', NULL),
(40, 'មី', 10, 5, 1, NULL, NULL, '2023-10-23 22:39:34', '2023-11-22 09:28:14'),
(41, 'BBQ', 10, 3, 1, NULL, NULL, '2023-10-23 22:39:47', NULL),
(42, 'ម៉ី ស៊ុប', 10, 10, 1, NULL, NULL, '2023-11-07 21:40:56', '2023-12-19 19:11:11'),
(43, 'Game', 0, 10, 1, NULL, NULL, '2023-11-07 21:43:37', NULL),
(44, 'Game', 43, 10, 1, NULL, NULL, '2023-11-07 21:43:48', NULL),
(45, 'មាន់បំពង', 10, 3, 1, NULL, NULL, '2023-11-22 09:35:21', NULL),
(50, 'Soft Drink ', 10, 5, 1, NULL, NULL, '2023-11-29 22:11:46', NULL),
(51, 'គុយទាវ', 10, 12, 1, NULL, NULL, '2023-12-17 23:19:10', '2023-12-19 15:10:28'),
(52, 'Beer', 10, 8, 1, NULL, NULL, '2023-12-18 09:45:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT 'N/A',
  `sex` int(2) DEFAULT NULL,
  `position` varchar(191) DEFAULT 'N/A',
  `company` varchar(191) DEFAULT 'N/A',
  `phone_number` varchar(15) DEFAULT 'N/A',
  `avatar` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`id`, `name`, `sex`, `position`, `company`, `phone_number`, `avatar`, `cover_image`) VALUES
(1, 'Seang Hai', NULL, 'owner ', 'company A', '010947640', NULL, NULL),
(2, 'Pisey Udom', NULL, 'Marketing Manager ', 'Car4you', '099226605', NULL, NULL),
(3, 'Phanna', NULL, 'CEO', 'Yadea', '081222212', NULL, NULL),
(4, 'Sem Srey Keo', NULL, 'Marketing Executive ', 'KK Brand and Investment ', '096 656 6661', NULL, NULL),
(5, 'Erika', NULL, 'Owner ', 'Med-B Cambodia', '096 656 6661', NULL, NULL),
(6, 'Pol Lyta', NULL, 'Marketing Executive ', 'CP Bank ', '096 656 6661', NULL, NULL),
(7, 'Ry Thany', NULL, 'Owner ', 'Norea Decore', '096 656 6661', NULL, NULL),
(8, 'Khim Kimsry', NULL, 'Marketing Executive ', 'Rich Avenue', '096 656 6661', NULL, NULL),
(9, 'William Von', NULL, 'Branch Manager ', 'Zando', '088 66 555 47', NULL, NULL),
(10, 'William Von', NULL, 'Marketing Executive ', 'Wing Bank and Wing Mall ', '088 66 555 47', NULL, NULL),
(11, 'Naly', NULL, 'Owner ', 'Chau Naly', '096 656 6661', NULL, NULL),
(12, 'Srey Tola', NULL, 'Owner ', 'Kin Kin', '096 656 6661', NULL, NULL),
(13, 'Thida', NULL, 'Owner ', 'Korean Town', '096 656 6661', NULL, NULL),
(14, 'Kunthea', NULL, 'Marketing Executive ', 'Dragon Dynasty', '096 656 6661', NULL, NULL),
(15, 'Lyly', NULL, 'Owner ', '3 Sisters', '096 656 6661', NULL, NULL),
(16, 'William Von', NULL, 'Owner ', 'កៅឡាក់', '088 66 555 47', NULL, NULL),
(17, 'Kanika', NULL, 'Owner ', 'ប្រហិតឆឹង', '076 240 3333', NULL, NULL),
(18, 'Soudiny', NULL, 'Marketing Manager ', 'Real Me', '096 656 6661', NULL, NULL),
(19, 'Heng ChongYean', NULL, 'Owner ', 'Allora store', '070628515', NULL, NULL),
(20, 'Prom Puthy', NULL, 'Owner ', 'Srey Pov', '015340403', NULL, NULL),
(21, 'Colin Colin', NULL, 'Sale-kHB Hanna', 'មីបឋម​​ ម៉ាម៉ាលីឃុន', '010761076', NULL, NULL),
(22, 'Chandavy ', NULL, 'Owner', 'Youth bag', '012372665', NULL, NULL),
(23, 'Ti Ti', NULL, 'owner', 'The sushi and takoyaki', '0965566733', NULL, NULL),
(24, 'Colin Colin', NULL, 'Sale-kHB Hanna', 'មីបឋម​​ ម៉ាម៉ាលីឃុន', '012898886', NULL, NULL),
(25, 'ពេជ្រលី', NULL, 'Owner ', 'ពេជ្រលី ម៊ីស៊ុប', '087523351', NULL, NULL),
(26, 'Van Da', NULL, 'Owner ', 'Van Da Game', '086649499', NULL, NULL),
(27, 'thyda', NULL, 'Owner ', 'Lok eyvan krob muk', '076 633 388', NULL, NULL),
(28, 'Kong Kandavy', NULL, 'owner', 'ត្បាល់បុក​ Two sisters', '017 900 426', NULL, NULL),
(29, 'Hana', NULL, 'Owner', 'Hana', ' 85510702846', NULL, NULL),
(30, 'Po Chantha', NULL, 'Sale-kHB Hanna', 'Miss Sweet (លក់ស្រ្តបឺរីស្រោបស្ករ  Drink)', '081255525', NULL, NULL),
(31, 'Ry Sovannarith ', NULL, 'General Manager ', 'Piphub Dey Meas', '096 656 6661', NULL, NULL),
(32, 'Thy Da', NULL, 'owner', 'Chocolate', '078 633 388', NULL, NULL),
(33, 'Sam Rithy', NULL, 'Marketing Executive ', 'Domnak Domrey Resort ', '096 656 6661', NULL, NULL),
(34, 'Seyha', NULL, 'Marketing Executive ', 'Borey Chan Kiri', '096 656 6661', NULL, NULL),
(35, 'Erika', NULL, 'Owner ', 'Med-B Cambodia', '096 656 6661', NULL, NULL),
(36, 'Rim Nara', NULL, 'Marketing Executive ', 'Rich Avenue', '096 777 7480', NULL, NULL),
(37, 'ban chunmove', NULL, 'GM', 'okithana', '087232912', NULL, NULL),
(38, 'Thy Da', NULL, 'owner', 'Chocolate', '078633388', NULL, NULL),
(39, 'Thy Da', NULL, 'owner', 'Chocolate', '078633388', NULL, NULL),
(40, 'ភិន រតនា', NULL, 'Owner', 'ហាងនំអន្សមខ្មែរ', '012860098', NULL, NULL),
(41, 'Khandavy', NULL, 'Owner', 'ត្បាល់បុក 2 sister ', '017900426', NULL, NULL),
(42, 'Chau Naly', NULL, 'Owner ', 'Naly', '010 544 162', NULL, NULL),
(43, 'Srey Tola', NULL, 'Owner ', 'Kin Kin', '093 858 512', NULL, NULL),
(44, 'Sophea', NULL, 'Owner ', 'Pu Henry Meat Ball', '010947640', NULL, NULL),
(45, 'Kunthea', NULL, 'Branch Manager ', 'Dragon Dynasty', '096 656 6661', NULL, NULL),
(46, 'Chettra Tung', NULL, 'owner', 'Sensuous Smells', '0886556586', NULL, NULL),
(47, 'Leang Khim', NULL, 'Marketing Manager', 'Leang Khim', '077 833 038', NULL, NULL),
(48, 'Punwaitcha Rityoungyoung', NULL, 'owner', 'មឹកអាំងផ្សារទួលទំពូង', '098 303 893', NULL, NULL),
(49, 'Punwaitcha Rityoungyoung', NULL, 'owner', 'មឹកអាំងផ្សារទួលទំពូង', '098303893', NULL, NULL),
(50, 'Buth Yong', NULL, 'owner', 'Buth Yong', '099 999 422', NULL, NULL),
(51, 'Khim Somphors', NULL, 'Owner ', 'AIA Insurance ', '092 222 890', NULL, NULL),
(52, 'ban chunmove', NULL, 'GM', 'okithana', '087232912', NULL, NULL),
(53, 'Khim Kimsry', NULL, 'Owner ', 'KHB Events ', '096 656 6661', NULL, NULL),
(54, 'William Von', NULL, 'Owner ', 'KHB Events ', '096 656 6661', NULL, NULL),
(55, 'Khim Kimsry', NULL, 'Marketing Executive ', 'Borey Bunly', '096 656 6661', NULL, NULL),
(56, 'Kheav Landy', NULL, 'Marketing Supervisor ', 'Lay Kong Emerald Residence ', '011 366 660', NULL, NULL),
(57, 'Pisey ', NULL, 'Marketing Executive ', 'Champion ', '096 656 6661', NULL, NULL),
(58, 'Sou Hour', NULL, 'Marketing Executive ', 'ស្រាសាច់ដុំពិសេស', '070 659 581', NULL, NULL),
(59, 'Lai Chhengly ', NULL, 'Owner ', 'Queenie Skin of Cambodia ', '071 755 5520', NULL, NULL),
(60, 'Rotana', NULL, 'Marketing Manager ', 'Onion ', '089 361 159', NULL, NULL),
(61, 'Chaira', NULL, 'Sale Manager', 'AIA', '086733737', NULL, NULL),
(62, 'Lim vannary', NULL, 'Owner', 'BUCKS CAMBODIA', '010330080', NULL, NULL),
(63, 'Phannet', NULL, 'Sale', 'owner', '086733737', NULL, NULL),
(64, 'Phannet', NULL, 'Sale', 'owner', '086733737', NULL, NULL),
(65, 'Panha', NULL, 'Marketing Manager', 'Pa cafe', '0717337378', NULL, NULL),
(66, 'Phon Dara', NULL, 'Sale-kHB Hanna', 'ME18KH', '093805049', NULL, NULL),
(67, 'Sam', NULL, 'Marketing Executive ', 'សិប្បកម្មឃីនរី', '095 783 456', NULL, NULL),
(68, 'Chau Naly', NULL, 'Owner ', 'Chau Naly', '010 544 162', NULL, NULL),
(69, 'Chau Naly', NULL, 'Marketing Executive ', 'Chau Naly', '010 544 162', NULL, NULL),
(70, 'Pon Dara', NULL, 'Marketing Executive ', 'ME 18 KH', '096 656 6661', NULL, NULL),
(71, 'che JJ', NULL, 'Marketing Manager', 'Bellissima Home　ｃｏ．，Ｌtd', '090787879', NULL, NULL),
(72, 'seng hout', NULL, 'Marketing Manager', 'Huy Yun', '0977772555', NULL, NULL),
(73, 'Pich Ly', NULL, 'Owner ', 'Pich Ly ម៊ីស៊ុប', '096 656 6661', NULL, NULL),
(74, 'Sok Sithika', NULL, 'Marketing Manager ', 'សិប្បកម្មឃីនរី', '096 656 6661', NULL, NULL),
(75, 'Erika', NULL, 'Owner ', 'Med-B Cambodia', '017 799 809', NULL, NULL),
(76, 'dalis', NULL, 'Sale&Marketing Manager', 'koreno', '070888 471', NULL, NULL),
(77, 'ly ly', NULL, 'Owner', 'solite', '070888471', NULL, NULL),
(78, 'Chea Sokha', NULL, 'owner', 'chea Sokhka(clothes)', '011889116', NULL, NULL),
(79, 'Chea Sokha', NULL, 'owner', 'chea Sokhka(clothes)', '011889116', NULL, NULL),
(80, 'Huoth Sokha', NULL, 'CEO', 'Bellewear', '098 363 707', NULL, NULL),
(81, 'តន់ មុទិត្យធីតា', NULL, 'owner', 'owner', '078 633 833', NULL, NULL),
(82, 'Rotha Na', NULL, 'owner', 'owner', '0717337378', NULL, NULL),
(83, 'show', NULL, 'owner', 'owner', '0717337378', NULL, NULL),
(84, 'LinLIn', NULL, 'owner', 'owner', '0717337378', NULL, NULL),
(85, 'ouch sonita', NULL, 'Sale&Marketing Manager', 'មឹកអាំងកោះស្នេហ៏', '070655466', NULL, NULL),
(86, 'Sovan Kanika', NULL, 'Owner ', 'SKIN7', '096 656 6661', NULL, NULL),
(87, 'khi socheata', NULL, 'Owner', 'brown round', '010739998', NULL, NULL),
(88, 'kim leng', NULL, 'Owner', 'នាងម្ទេស', '081609303', NULL, NULL),
(89, 'kim leng', NULL, 'Owner', 'នាងម្ទេស', '010330080', NULL, NULL),
(90, 'Lim Sokpanha', NULL, 'Owner ', 'Catpro', '096 656 6661', NULL, NULL),
(91, 'Pich', NULL, 'Marketing Manager', 'Pich', '0968450416', NULL, NULL),
(92, 'ឆាត ទ្រី', NULL, 'Owner ', 'ឆាត​ ទ្រី', '096 643 9542', NULL, NULL),
(93, 'kim leng', NULL, 'Owner', 'នាងម្ទេស', '010330080', NULL, NULL),
(94, 'kim leng', NULL, 'Owner', 'នាងម្ទេស', '010330080', NULL, NULL),
(95, 'chhing', NULL, 'Owner', 'តូប​ស្មោះ', '069474499', NULL, NULL),
(96, 'Ganburg', NULL, 'Sale Manager', 'Ganburg', '0717337378', NULL, NULL),
(97, 'Rotha', NULL, 'owner', 'owner', '0717337378', NULL, NULL),
(98, 'K24', NULL, 'owner', 'owner', '0717337378', NULL, NULL),
(99, 'Khiev Vidal', NULL, 'Marketing Executive ', 'Boostrong ', '081 988 868', NULL, NULL),
(100, 'Lao Phengjun', NULL, 'Sale Manager', 'LM Car', '069658368', NULL, NULL),
(101, 'Meta', NULL, 'Marketing Executive ', 'Cellcard ', '096 656 6661', NULL, NULL),
(102, 'Mina', NULL, 'Owner ', 'Japanese Samon Fish', '096 656 6661', NULL, NULL),
(103, 'Sum Pisal', NULL, 'owner', 'IG TRADING GROUP CO.,LTD', '093533030', NULL, NULL),
(104, 'Sopheap', NULL, 'Marketing Manager', 'Nobicha', '096 577 1469', NULL, NULL),
(105, 'Naro', NULL, 'owner', 'owner', '0717337378', NULL, NULL),
(106, 'Chaira', NULL, 'owner', 'owner', '0717337378', NULL, NULL),
(107, 'Phannet', NULL, 'owner', 'owner', '0717337378', NULL, NULL),
(108, 'roth', NULL, 'Owner', 'លក់គុយទាវសុប​ បុកល្ហុង​បាយ​ឆា ប្រហិត សាច់អាំង', '093282849', NULL, NULL),
(109, 'ហុងលីណាត', NULL, 'Owner', 'មិត្តល្អ', '015852829', NULL, NULL),
(110, 'Pich Ly', NULL, 'Owner ', 'Pich Ly ម៊ីស៊ុប', '096 656 6661', NULL, NULL),
(111, 'sievpey seng', NULL, 'owner', 'Longge hotpot express', '0962366665', NULL, NULL),
(112, 'Ibrahim', NULL, 'Owner ', 'Stanberry Supply', '096 656 6661', NULL, NULL),
(113, 'Naly', NULL, 'Owner ', 'Chau Naly', '096 656 6661', NULL, NULL),
(114, 'Channa', NULL, 'Marketing Manager ', 'Forte Life Insurance ', '096 656 6661', NULL, NULL),
(115, 'Young Theary', NULL, 'Country Manager ', 'My Car Consultant ', '096 656 6661', NULL, NULL),
(116, 'Jorataga', NULL, 'Owner ', 'Kebab Doner ', '096 656 6661', NULL, NULL),
(117, 'Alexander Hales ', NULL, 'Owner ', 'European Games ', '096 656 6661', NULL, NULL),
(118, 'Punleu', NULL, 'Marketing Executive ', 'Madam Da', '071 659 4955', NULL, NULL),
(119, 'Thany Catherine', NULL, 'Owner ', 'Norea Decore', '096 656 6661', NULL, NULL),
(120, 'Seng Sievpey', NULL, 'Owner ', 'Longge Hotpot Express', '096 23 66 665', NULL, NULL),
(121, 'Ti Ti', NULL, 'Owner ', 'The Sushi and Takoyaki', '096 55 66733', NULL, NULL),
(122, 'Bunthong', NULL, 'Owner ', 'គុយទាវ ប៊ុន​ ថុង', '087 218 424', NULL, NULL),
(123, 'Seavmean', NULL, 'Marketing Executive ', 'Ganzberg', '096 656 6661', NULL, NULL),
(124, 'Sovann', NULL, 'Marketing Manager ', 'ISI Dangkor Senchey FC', '077 262 711', NULL, NULL),
(125, 'Sovann', NULL, 'Marketing Manager ', 'Jersey Sport Store', '096 656 6661', NULL, NULL),
(126, 'Mia', NULL, 'owner', 'Manira', '016222295', NULL, NULL),
(127, 'ម៉ាក់វិរៈបុត្រ', NULL, 'Owner ', 'គុយទាវ ម៉ាកវិរៈបុត្រ', '096 656 6661', NULL, NULL),
(128, 'Da', NULL, 'Owner ', 'Da Closet', '096 656 6661', NULL, NULL),
(129, 'Lim Sokpanha', NULL, 'Owner ', 'Catpro', '096 656 6661', NULL, NULL),
(130, 'Ibrahim', NULL, 'Owner ', 'Straberry ', '096 656 6661', NULL, NULL),
(131, 'Madam Da', NULL, 'Owner ', 'Da Closet', '096 656 6661', NULL, NULL),
(132, 'Madam Da', NULL, 'Owner ', 'Da Closet', '096 656 6661', NULL, NULL),
(133, 'Sum Pisal', NULL, 'owner', 'IG TRADING GROUP CO.,LTD', '093533030', NULL, NULL),
(134, 'Un Samphors', NULL, 'Owner ', 'Jars of Clay Coffee Shop', '096 656 6661', NULL, NULL),
(135, 'នឹត នឹត', NULL, 'Owner ', 'នឹត នឹត កៅឡាក់ជប៉ុន', '096 656 6661', NULL, NULL),
(136, 'Phary', NULL, 'Owner ', 'Yummy Spicy', '096 656 6661', NULL, NULL),
(137, 'រដ្ឋា', NULL, 'Owner ', 'រដ្ឋាគុយទាវ', '096 656 6661', NULL, NULL),
(138, 'Ibrahim', NULL, 'Owner ', 'Stanberry Supply', '096 656 6661', NULL, NULL),
(139, 'ឆាត ទ្រី', NULL, 'Owner ', 'ឆាត​ ទ្រី', '096 656 6661', NULL, NULL),
(140, 'Thany Catherine', NULL, 'Owner ', 'Norea Decore', '096 656 6661', NULL, NULL),
(141, 'Seavmean', NULL, 'Marketing Executive ', 'Ganzberg', '096 656 6661', NULL, NULL),
(142, 'Chantha', NULL, 'owner', 'Granden Pizza', '081569699', NULL, NULL),
(143, 'Dunlop', NULL, 'Marketing Executive ', 'Dunlop Cambodia ', '096 656 6661', NULL, NULL),
(144, 'Eng Choeung ', NULL, 'Owner ', 'កៅឡាក់ភ្នំពេញ', '096 656 6661', NULL, NULL),
(145, 'Lao Phengjun', NULL, 'Sales Manager ', 'LM Car', '069 658 368', NULL, NULL),
(146, 'សុជាតិ', NULL, 'owner', 'កាបូបកំប្លោកខ្មែរ', '092866682', NULL, NULL),
(147, 'Sok Sithika', NULL, 'Marketing Manager ', 'សិប្បកម្មឃីនរី', '096 656 6661', NULL, NULL),
(148, 'Sok Sithika', NULL, 'Marketing Manager ', 'សិប្បកម្មឃីនរី', '096 656 6661', NULL, NULL),
(149, 'Khim Kimsry', NULL, 'Marketing Manager ', 'Classy Infinity ', '096 656 6661', NULL, NULL),
(150, 'Vidal', NULL, 'Marketing Manager ', 'Ganzberg', '096 656 6661', NULL, NULL),
(151, 'Vidal', NULL, 'Marketing Manager ', 'Ganzberg', '096 656 6661', NULL, NULL),
(152, 'Sovann', NULL, 'Marketing Executive ', 'ISI Dangkor Senchey FC', '096 656 6661', NULL, NULL),
(153, 'Sovann', NULL, 'Marketing Manager ', 'Jersey Sport Store', '096 656 6661', NULL, NULL),
(154, 'Khim Kimsry', NULL, 'Owner ', 'Somethea', '096 656 6661', NULL, NULL),
(155, 'Pich Ly', NULL, 'Owner ', 'Pich Ly ម៊ីស៊ុប', '096 656 6661', NULL, NULL),
(156, 'Pich Ly', NULL, 'Owner ', 'Pich Ly ម៊ីស៊ុប', '096 656 6661', NULL, NULL),
(157, 'Kong Kandavy', NULL, 'owner', 'ត្បាល់បុក​ Two sisters', '017900426', NULL, NULL),
(158, 'រដ្ឋាគុយទាវ', NULL, 'Owner ', 'រដ្ឋាគុយទាវ', '096 656 6661', NULL, NULL),
(159, 'Chea', NULL, 'owner', 'chea Sokhka(clothes)', '0717337378', NULL, NULL),
(160, 'រដ្ឋាគុយទាវ', NULL, 'Owner ', 'រដ្ឋាគុយទាវ', '096 656 6661', NULL, NULL),
(161, 'រដ្ឋា', NULL, 'Owner ', 'រដ្ឋាគុយទាវ', '096 656 6661', NULL, NULL),
(162, 'Chea Sokha', NULL, 'owner', 'chea Sokhka(clothes)', '0717337378', NULL, NULL),
(163, 'Vidal', NULL, 'Marketing Manager ', 'Ganzberg', '096 656 6661', NULL, NULL),
(164, 'Khim Kimsry', NULL, 'Marketing Executive ', 'Ganzberg', '096 656 6661', NULL, NULL),
(165, 'Yin Nat', NULL, 'Owner ', 'គីកាលក់ស៊ុបឆ្នាំងដី', '096 656 6661', NULL, NULL),
(166, 'Chetra', NULL, 'owner', 'owner', '0886556586', NULL, NULL),
(167, 'Khim Kimsry', NULL, 'Owner ', 'BLOC', '096 656 6661', NULL, NULL),
(168, 'Khim Kimsry', NULL, 'Owner ', 'BLOC', '096 656 6661', NULL, NULL),
(169, 'Vidal', NULL, 'Marketing Manager ', 'Ganzberg', '096 656 6661', NULL, NULL),
(170, 'Srey Laysim', NULL, 'Owner ', 'SR Kardo', '096 656 6661', NULL, NULL),
(171, 'Sreylen', NULL, 'owner', 'owner', '0717337378', NULL, NULL),
(172, 'Vs', NULL, 'Sala', 'V-S', '093551380', NULL, NULL),
(173, 'chamanb', 1, 'manager', 'antelite', '+85515705703', NULL, NULL),
(174, 'CHAMNAB MEY', 1, 'OWNER', 'ANT ELITE DIGITAL', '015705703', 'images/avatars/client/5d705af9-c1f5-424f-93db-86c8c64288a1_1768035611.png', 'images/covers/client/7f844bd7-d878-4e8a-a512-f583f829c80c_1768035634.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `costings`
--

CREATE TABLE `costings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Costing name/title',
  `description` text DEFAULT NULL,
  `floor_plan_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Link to floor plan/event',
  `booking_id` int(11) DEFAULT NULL COMMENT 'Link to booking',
  `estimated_cost` decimal(15,2) DEFAULT NULL COMMENT 'Estimated cost',
  `actual_cost` decimal(15,2) DEFAULT NULL COMMENT 'Actual cost',
  `costing_date` date NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'draft' COMMENT 'draft, approved, in_progress, completed, cancelled',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `budget` decimal(15,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `variables` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_code` varchar(50) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `id_card_number` varchar(100) DEFAULT NULL,
  `passport_number` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_phone` varchar(50) DEFAULT NULL,
  `emergency_contact_relationship` varchar(100) DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `position_id` bigint(20) UNSIGNED DEFAULT NULL,
  `manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employment_type` enum('full-time','part-time','contract','intern','temporary') DEFAULT 'full-time',
  `hire_date` date NOT NULL,
  `probation_end_date` date DEFAULT NULL,
  `contract_start_date` date DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `termination_date` date DEFAULT NULL,
  `termination_reason` text DEFAULT NULL,
  `status` enum('active','inactive','terminated','on-leave','suspended') DEFAULT 'active',
  `salary` decimal(10,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'USD',
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_account` varchar(100) DEFAULT NULL,
  `tax_id` varchar(100) DEFAULT NULL,
  `social_security_number` varchar(100) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_documents`
--

CREATE TABLE `employee_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `uploaded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_training`
--

CREATE TABLE `employee_training` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `training_name` varchar(255) NOT NULL,
  `training_provider` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('scheduled','in-progress','completed','cancelled') DEFAULT 'scheduled',
  `certificate_number` varchar(100) DEFAULT NULL,
  `certificate_file` varchar(500) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `expense_date` date NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'cash' COMMENT 'cash, bank_transfer, check, credit_card',
  `reference_number` varchar(255) DEFAULT NULL COMMENT 'Invoice number, receipt number, etc.',
  `vendor_name` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `floor_plan_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Link to floor plan/event',
  `booking_id` int(11) DEFAULT NULL COMMENT 'Link to booking',
  `status` varchar(50) NOT NULL DEFAULT 'pending' COMMENT 'pending, approved, paid, cancelled',
  `created_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `finance_categories`
--

CREATE TABLE `finance_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL COMMENT 'expense, revenue, costing',
  `description` text DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL COMMENT 'For UI display',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `floor_plans`
--

CREATE TABLE `floor_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `project_name` varchar(255) DEFAULT NULL,
  `floor_image` varchar(255) DEFAULT NULL,
  `canvas_width` int(11) DEFAULT 1200,
  `canvas_height` int(11) DEFAULT 800,
  `is_active` tinyint(1) DEFAULT 1,
  `is_default` tinyint(1) DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `floor_plans`
--

INSERT INTO `floor_plans` (`id`, `event_id`, `name`, `description`, `project_name`, `floor_image`, `canvas_width`, `canvas_height`, `is_active`, `is_default`, `created_by`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Kmall', 'Kmall', 'Main Project', 'images/floor-plans/1768066318_floor_plan_1.jpg', 6250, 3125, 1, 0, NULL, '2026-01-10 09:59:01', '2026-01-10 17:31:58'),
(4, NULL, 'Phnom Penh Shopping Festival', 'Phnom Penh Shopping Festival at kohpich', 'Phnom Penh Shopping Festival', 'images/floor-plans/1768066307_floor_plan_4.jpg', 6963, 4924, 1, 0, 61, '2026-01-10 13:49:44', '2026-01-10 17:31:47');

-- --------------------------------------------------------

--
-- Table structure for table `leave_balances`
--

CREATE TABLE `leave_balances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `leave_type_id` bigint(20) UNSIGNED NOT NULL,
  `year` int(11) NOT NULL,
  `allocated_days` decimal(5,2) DEFAULT 0.00,
  `used_days` decimal(5,2) DEFAULT 0.00,
  `remaining_days` decimal(5,2) DEFAULT 0.00,
  `carried_forward_days` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `leave_type_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` decimal(5,2) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','cancelled') DEFAULT 'pending',
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `max_days_per_year` int(11) DEFAULT NULL,
  `carry_forward` tinyint(1) DEFAULT 0,
  `requires_approval` tinyint(1) DEFAULT 1,
  `is_paid` tinyint(1) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`id`, `name`, `code`, `description`, `max_days_per_year`, `carry_forward`, `requires_approval`, `is_paid`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Annual Leave', 'ANNUAL', 'Annual vacation leave', 20, 1, 1, 1, 1, 1, '2026-01-11 14:39:28', '2026-01-11 14:39:28'),
(2, 'Sick Leave', 'SICK', 'Medical leave for illness', 10, 0, 1, 1, 1, 2, '2026-01-11 14:39:28', '2026-01-11 14:39:28'),
(3, 'Personal Leave', 'PERSONAL', 'Personal matters leave', 5, 0, 1, 1, 1, 3, '2026-01-11 14:39:28', '2026-01-11 14:39:28'),
(4, 'Maternity Leave', 'MATERNITY', 'Maternity leave', 90, 0, 1, 1, 1, 4, '2026-01-11 14:39:28', '2026-01-11 14:39:28'),
(5, 'Paternity Leave', 'PATERNITY', 'Paternity leave', 7, 0, 1, 1, 1, 5, '2026-01-11 14:39:28', '2026-01-11 14:39:28'),
(6, 'Unpaid Leave', 'UNPAID', 'Unpaid leave', NULL, 0, 1, 0, 1, 6, '2026-01-11 14:39:28', '2026-01-11 14:39:28'),
(7, 'Emergency Leave', 'EMERGENCY', 'Emergency leave', 3, 0, 1, 1, 1, 7, '2026-01-11 14:39:28', '2026-01-11 14:39:28');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `from_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'message',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_01_09_230442_create_notifications_table', 1),
(2, '2026_01_09_230443_create_payments_table', 1),
(3, '2026_01_09_230444_create_messages_table', 1),
(4, '2026_01_09_233158_create_roles_table', 1),
(5, '2026_01_09_233159_create_permissions_table', 1),
(6, '2026_01_09_233200_create_role_permissions_table', 1),
(7, '2026_01_09_233201_add_role_id_to_users_table', 1),
(8, '2026_01_09_234459_create_activity_logs_table', 1),
(9, '2026_01_09_234504_create_email_templates_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `booking_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `email_sent` tinyint(1) NOT NULL DEFAULT 0,
  `email_sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(255) NOT NULL DEFAULT 'cash',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `transaction_id` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `performance_reviews`
--

CREATE TABLE `performance_reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `reviewer_id` bigint(20) UNSIGNED NOT NULL,
  `review_period_start` date NOT NULL,
  `review_period_end` date NOT NULL,
  `review_date` date NOT NULL,
  `overall_rating` decimal(3,2) DEFAULT NULL COMMENT 'Rating out of 5.00',
  `goals_achieved` text DEFAULT NULL,
  `strengths` text DEFAULT NULL,
  `areas_for_improvement` text DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `employee_comments` text DEFAULT NULL,
  `status` enum('draft','submitted','acknowledged','completed') DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `performance_review_criteria`
--

CREATE TABLE `performance_review_criteria` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `review_id` bigint(20) UNSIGNED NOT NULL,
  `criterion_name` varchar(255) NOT NULL,
  `rating` decimal(3,2) DEFAULT NULL COMMENT 'Rating out of 5.00',
  `comments` text DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT 1.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `module` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `slug`, `module`, `description`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'View Dashboard', 'dashboard.view', 'dashboard', 'Access dashboard and view statistics', 1, 1, '2026-01-11 09:40:44', '2026-01-11 09:44:33'),
(2, 'View Bookings', 'bookings.view', 'bookings', 'View booking list and details', 1, 1, '2026-01-11 09:40:44', '2026-01-11 09:44:33'),
(3, 'Create Bookings', 'bookings.create', 'bookings', 'Create new bookings', 1, 2, '2026-01-11 09:40:44', '2026-01-11 09:44:33'),
(4, 'Edit Bookings', 'bookings.edit', 'bookings', 'Edit existing bookings', 1, 3, '2026-01-11 09:40:44', '2026-01-11 09:44:33'),
(5, 'Delete Bookings', 'bookings.delete', 'bookings', 'Delete bookings', 1, 4, '2026-01-11 09:40:44', '2026-01-11 09:44:33'),
(6, 'Manage All Bookings', 'bookings.manage', 'bookings', 'Full booking management including delete all', 1, 5, '2026-01-11 09:40:44', '2026-01-11 09:44:33'),
(13, 'View Booths', 'booths.view', 'booths', 'View booth list and floor plans', 1, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(14, 'Create Booths', 'booths.create', 'booths', 'Create new booths', 1, 2, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(15, 'Edit Booths', 'booths.edit', 'booths', 'Edit booth properties and positions', 1, 3, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(16, 'Delete Booths', 'booths.delete', 'booths', 'Delete booths', 1, 4, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(17, 'Manage Floor Plans', 'booths.floor-plans', 'booths', 'Manage floor plans and canvas settings', 1, 5, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(18, 'View Clients', 'clients.view', 'clients', 'View client list and details', 1, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(19, 'Create Clients', 'clients.create', 'clients', 'Create new clients', 1, 2, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(20, 'Edit Clients', 'clients.edit', 'clients', 'Edit client information', 1, 3, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(21, 'Delete Clients', 'clients.delete', 'clients', 'Delete clients', 1, 4, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(22, 'View Payments', 'finance.payments.view', 'finance', 'View payment records', 1, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(23, 'Manage Payments', 'finance.payments.manage', 'finance', 'Create, edit, and process payments', 1, 2, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(24, 'View Costings', 'finance.costings.view', 'finance', 'View costing records', 1, 3, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(25, 'Manage Costings', 'finance.costings.manage', 'finance', 'Create, edit, and approve costings', 1, 4, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(26, 'View Expenses', 'finance.expenses.view', 'finance', 'View expense records', 1, 5, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(27, 'Manage Expenses', 'finance.expenses.manage', 'finance', 'Create, edit, and approve expenses', 1, 6, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(28, 'View Revenues', 'finance.revenues.view', 'finance', 'View revenue records', 1, 7, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(29, 'Manage Revenues', 'finance.revenues.manage', 'finance', 'Create, edit, and approve revenues', 1, 8, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(30, 'View Booth Pricing', 'finance.booth-pricing.view', 'finance', 'View booth pricing information', 1, 9, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(31, 'Manage Booth Pricing', 'finance.booth-pricing.manage', 'finance', 'Update booth pricing and bulk updates', 1, 10, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(32, 'Manage Finance Categories', 'finance.categories.manage', 'finance', 'Manage finance categories', 1, 11, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(33, 'Approve Finance', 'finance.approve', 'finance', 'Approve financial transactions (costings, expenses, revenues)', 1, 12, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(34, 'View Users', 'users.view', 'users', 'View user list', 1, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(35, 'Create Users', 'users.create', 'users', 'Create new users', 1, 2, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(36, 'Edit Users', 'users.edit', 'users', 'Edit user information', 1, 3, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(37, 'Delete Users', 'users.delete', 'users', 'Delete users', 1, 4, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(38, 'Manage Roles', 'roles.manage', 'roles', 'Create, edit, and delete roles', 1, 5, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(39, 'Manage Permissions', 'permissions.manage', 'permissions', 'Manage permissions system', 1, 6, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(40, 'Assign Roles', 'users.assign-roles', 'users', 'Assign roles to users', 1, 7, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(41, 'View Reports', 'reports.view', 'reports', 'Access reports and analytics', 1, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(42, 'Export Reports', 'reports.export', 'reports', 'Export report data', 1, 2, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(43, 'View Categories', 'categories.view', 'categories', 'View categories', 1, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(44, 'Manage Categories', 'categories.manage', 'categories', 'Create, edit, and delete categories', 1, 2, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(45, 'View Notifications', 'notifications.view', 'notifications', 'View notifications', 1, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(46, 'Manage Notifications', 'notifications.manage', 'notifications', 'Send and manage notifications', 1, 2, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(47, 'View Communications', 'communications.view', 'communications', 'View messages and communications', 1, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(48, 'Send Communications', 'communications.send', 'communications', 'Send messages and announcements', 1, 2, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(49, 'View Activity Logs', 'activity-logs.view', 'activity-logs', 'View system activity logs', 1, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(50, 'Export Data', 'export.export', 'export', 'Export data to various formats', 1, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(51, 'Import Data', 'export.import', 'export', 'Import data from files', 1, 2, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(52, 'View Settings', 'settings.view', 'settings', 'View system settings', 1, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(53, 'Manage Settings', 'settings.manage', 'settings', 'Modify system settings', 1, 2, '2026-01-11 09:44:33', '2026-01-11 09:44:33');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `min_salary` decimal(10,2) DEFAULT NULL,
  `max_salary` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `revenues`
--

CREATE TABLE `revenues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `revenue_date` date NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'cash' COMMENT 'cash, bank_transfer, check, credit_card',
  `reference_number` varchar(255) DEFAULT NULL COMMENT 'Invoice number, receipt number, etc.',
  `client_id` int(11) DEFAULT NULL,
  `floor_plan_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Link to floor plan/event',
  `booking_id` int(11) DEFAULT NULL COMMENT 'Link to booking',
  `status` varchar(50) NOT NULL DEFAULT 'pending' COMMENT 'pending, confirmed, received, cancelled',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Owner', 'owner', 'System owner with full access to all features and settings', 1, 10, '2026-01-11 09:40:44', '2026-01-11 09:44:33'),
(2, 'Admin', 'admin', 'Administrator with access to all operational features except ownership settings', 1, 9, '2026-01-11 09:40:44', '2026-01-11 09:44:33'),
(3, 'Sales Manager', 'sales-manager', 'Manages sales team, bookings, client relationships, and sales reports', 1, 8, '2026-01-11 09:40:44', '2026-01-11 09:44:33'),
(4, 'Sales', 'sales', 'Handles bookings, client interactions, and booth sales', 1, 7, '2026-01-11 09:40:44', '2026-01-11 09:44:33'),
(5, 'Accounting Manager', 'accounting-manager', 'Manages finance team, approvals, and financial reports', 1, 8, '2026-01-11 09:40:44', '2026-01-11 09:44:33'),
(6, 'Accounting', 'accounting', 'Handles payments, expenses, revenues, and financial records', 1, 7, '2026-01-11 09:40:44', '2026-01-11 09:44:33'),
(7, 'Finance Manager', 'finance-manager', 'Manages finance operations, costing, and financial planning', 1, 8, '2026-01-11 09:40:44', '2026-01-11 09:44:33'),
(8, 'Finance', 'finance', 'Handles financial transactions, costing, and reporting', 1, 7, '2026-01-11 09:40:44', '2026-01-11 09:44:33'),
(9, 'HR Manager', 'hr-manager', 'Manages HR team, employee records, and HR policies', 1, 8, '2026-01-11 09:40:44', '2026-01-11 09:44:33'),
(10, 'HR', 'hr', 'Handles employee records, attendance, and HR operations', 1, 7, '2026-01-11 09:40:44', '2026-01-11 09:44:33'),
(11, 'Manager', 'manager', 'General manager with access to multiple departments', 1, 6, '2026-01-11 09:40:44', '2026-01-11 09:44:33'),
(12, 'Staff', 'staff', 'Basic staff access for daily operations', 1, 5, '2026-01-11 09:40:44', '2026-01-11 09:44:33');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `created_at`, `updated_at`) VALUES
(1, 1, 49, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(2, 1, 3, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(3, 1, 5, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(4, 1, 4, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(5, 1, 6, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(6, 1, 2, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(7, 1, 14, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(8, 1, 16, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(9, 1, 15, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(10, 1, 17, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(11, 1, 13, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(12, 1, 44, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(13, 1, 43, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(14, 1, 19, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(15, 1, 21, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(16, 1, 20, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(17, 1, 18, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(18, 1, 48, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(19, 1, 47, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(20, 1, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(21, 1, 50, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(22, 1, 51, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(23, 1, 33, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(24, 1, 31, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(25, 1, 30, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(26, 1, 32, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(27, 1, 25, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(28, 1, 24, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(29, 1, 27, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(30, 1, 26, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(31, 1, 23, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(32, 1, 22, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(33, 1, 29, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(34, 1, 28, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(35, 1, 46, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(36, 1, 45, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(37, 1, 39, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(38, 1, 42, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(39, 1, 41, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(40, 1, 38, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(41, 1, 53, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(42, 1, 52, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(43, 1, 40, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(44, 1, 35, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(45, 1, 37, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(46, 1, 36, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(47, 1, 34, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(64, 2, 49, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(65, 2, 3, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(66, 2, 5, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(67, 2, 4, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(68, 2, 6, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(69, 2, 2, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(70, 2, 14, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(71, 2, 16, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(72, 2, 15, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(73, 2, 17, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(74, 2, 13, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(75, 2, 44, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(76, 2, 43, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(77, 2, 19, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(78, 2, 21, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(79, 2, 20, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(80, 2, 18, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(81, 2, 48, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(82, 2, 47, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(83, 2, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(84, 2, 50, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(85, 2, 51, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(86, 2, 33, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(87, 2, 31, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(88, 2, 30, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(89, 2, 32, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(90, 2, 25, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(91, 2, 24, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(92, 2, 27, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(93, 2, 26, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(94, 2, 23, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(95, 2, 22, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(96, 2, 29, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(97, 2, 28, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(98, 2, 46, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(99, 2, 45, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(100, 2, 39, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(101, 2, 42, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(102, 2, 41, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(103, 2, 38, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(104, 2, 53, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(105, 2, 52, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(106, 2, 40, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(107, 2, 35, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(108, 2, 37, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(109, 2, 36, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(110, 2, 34, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(127, 3, 3, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(128, 3, 4, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(129, 3, 6, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(130, 3, 2, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(131, 3, 13, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(132, 3, 43, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(133, 3, 19, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(134, 3, 20, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(135, 3, 18, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(136, 3, 48, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(137, 3, 47, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(138, 3, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(139, 3, 45, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(140, 3, 42, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(141, 3, 41, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(142, 4, 3, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(143, 4, 4, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(144, 4, 2, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(145, 4, 13, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(146, 4, 19, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(147, 4, 20, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(148, 4, 18, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(149, 4, 48, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(150, 4, 47, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(151, 4, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(152, 4, 45, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(157, 5, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(158, 5, 18, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(159, 5, 22, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(160, 5, 23, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(161, 5, 24, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(162, 5, 25, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(163, 5, 26, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(164, 5, 27, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(165, 5, 28, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(166, 5, 29, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(167, 5, 30, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(168, 5, 31, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(169, 5, 32, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(170, 5, 33, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(171, 5, 41, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(172, 5, 42, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(173, 5, 50, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(188, 6, 18, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(189, 6, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(190, 6, 31, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(191, 6, 30, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(192, 6, 25, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(193, 6, 24, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(194, 6, 27, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(195, 6, 26, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(196, 6, 23, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(197, 6, 22, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(198, 6, 29, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(199, 6, 28, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(200, 6, 41, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(203, 7, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(204, 7, 22, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(205, 7, 23, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(206, 7, 24, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(207, 7, 25, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(208, 7, 26, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(209, 7, 27, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(210, 7, 28, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(211, 7, 29, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(212, 7, 30, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(213, 7, 31, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(214, 7, 32, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(215, 7, 33, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(216, 7, 41, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(217, 7, 42, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(218, 7, 50, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(234, 8, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(235, 8, 31, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(236, 8, 30, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(237, 8, 25, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(238, 8, 24, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(239, 8, 27, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(240, 8, 26, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(241, 8, 23, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(242, 8, 22, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(243, 8, 29, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(244, 8, 28, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(245, 8, 41, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(249, 9, 49, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(250, 9, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(251, 9, 46, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(252, 9, 45, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(253, 9, 41, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(254, 9, 35, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(255, 9, 36, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(256, 9, 34, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(264, 10, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(265, 10, 45, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(266, 10, 41, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(267, 10, 34, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(271, 11, 3, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(272, 11, 4, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(273, 11, 2, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(274, 11, 13, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(275, 11, 19, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(276, 11, 20, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(277, 11, 18, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(278, 11, 48, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(279, 11, 47, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(280, 11, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(281, 11, 30, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(282, 11, 24, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(283, 11, 26, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(284, 11, 22, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(285, 11, 28, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(286, 11, 45, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(287, 11, 42, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(288, 11, 41, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(302, 12, 2, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(303, 12, 13, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(304, 12, 18, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(305, 12, 1, '2026-01-11 09:44:33', '2026-01-11 09:44:33'),
(306, 12, 45, '2026-01-11 09:44:33', '2026-01-11 09:44:33');

-- --------------------------------------------------------

--
-- Table structure for table `salary_history`
--

CREATE TABLE `salary_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `effective_date` date NOT NULL,
  `salary` decimal(10,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'USD',
  `reason` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'string',
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'booth_default_width', '50', 'integer', 'Default width for new booths (in pixels)', '2026-01-06 11:15:51', '2026-01-07 04:34:08'),
(2, 'booth_default_height', '50', 'integer', 'Default height for new booths (in pixels)', '2026-01-06 11:15:51', '2026-01-07 04:34:08'),
(3, 'booth_default_rotation', '0', 'integer', 'Default rotation angle for new booths (in degrees)', '2026-01-06 11:15:51', '2026-01-06 16:17:11'),
(4, 'booth_default_z_index', '20', 'integer', 'Default z-index for new booths', '2026-01-06 11:15:51', '2026-01-07 04:34:08'),
(5, 'booth_default_font_size', '40', 'integer', 'Default font size for booth number text (in pixels)', '2026-01-06 11:15:51', '2026-01-06 13:22:39'),
(6, 'booth_default_border_width', '5', 'integer', 'Default border width for booths (in pixels)', '2026-01-06 11:15:51', '2026-01-06 12:00:46'),
(7, 'booth_default_border_radius', '1', 'integer', 'Default border radius for booths (in pixels)', '2026-01-06 11:15:51', '2026-01-06 11:59:25'),
(8, 'booth_default_opacity', '1', 'float', 'Default opacity for booths (0.0 to 1.0)', '2026-01-06 11:15:51', '2026-01-06 11:20:27'),
(9, 'booth_default_background_color', '#ffffff', 'string', 'Default background color for booths', '2026-01-08 06:13:53', '2026-01-08 06:13:53'),
(10, 'booth_default_border_color', '#ff0000', 'string', 'Default border color for booths', '2026-01-08 06:13:53', '2026-01-08 06:13:53'),
(11, 'booth_default_text_color', '#000000', 'string', 'Default text color for booth numbers', '2026-01-08 06:13:53', '2026-01-08 06:13:53'),
(12, 'booth_default_font_weight', '700', 'string', 'Default font weight for booth numbers', '2026-01-08 06:13:53', '2026-01-08 06:13:53'),
(13, 'booth_default_font_family', 'Arial, sans-serif', 'string', 'Default font family for booth numbers', '2026-01-08 06:13:53', '2026-01-08 06:13:53'),
(14, 'booth_default_text_align', 'center', 'string', 'Default text alignment for booth numbers', '2026-01-08 06:13:53', '2026-01-08 06:13:53'),
(15, 'booth_default_box_shadow', '0 2px 8px rgba(0,0,0,0.2)', 'string', 'Default box shadow for booths', '2026-01-08 06:13:53', '2026-01-08 06:13:53');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(45) NOT NULL,
  `password` varchar(120) NOT NULL,
  `type` varchar(11) NOT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(11) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `type`, `role_id`, `status`, `avatar`, `cover_image`, `last_login`, `create_time`, `update_time`) VALUES
(94, 'staff', '$2y$12$qB6hTgnRI4W10s5LmQJwX.HJGX29UrJFYhKsJLzLEv5WpF1ehX3o6', '2', 4, '1', NULL, NULL, NULL, NULL, NULL),
(95, 'owner', '$2y$10$nZH15cHbSPU/8QSlywrgJ.iZgZd26uQuGTHGBspiaArJaOzVMYFO6', '1', 1, '1', NULL, NULL, NULL, NULL, NULL),
(96, 'admin', '$2y$10$nZH15cHbSPU/8QSlywrgJ.iZgZd26uQuGTHGBspiaArJaOzVMYFO6', '1', 2, '1', NULL, NULL, NULL, NULL, NULL),
(97, 'sales_manager', '$2y$10$nZH15cHbSPU/8QSlywrgJ.iZgZd26uQuGTHGBspiaArJaOzVMYFO6', '0', 3, '1', NULL, NULL, NULL, NULL, NULL),
(98, 'sales', '$2y$10$nZH15cHbSPU/8QSlywrgJ.iZgZd26uQuGTHGBspiaArJaOzVMYFO6', '0', 4, '1', NULL, NULL, NULL, NULL, NULL),
(61, 'vutha_admin', '$2y$12$AdwcjrTEEr4TrkN4kV6lmu9g8u5XXC6SSlNWhbM6tf/bVygrq1dIO', '1', NULL, '1', 'images/avatars/user/c06937a6-b63c-4e28-a86d-1d0e51b2ddd7_1768063554.png', NULL, '2025-12-26 15:51:34', '2019-10-24 10:01:03', '2026-01-04 22:59:05'),
(105, 'manager', '$2y$10$nZH15cHbSPU/8QSlywrgJ.iZgZd26uQuGTHGBspiaArJaOzVMYFO6', '0', 11, '1', NULL, NULL, NULL, NULL, NULL),
(106, 'staff', '$2y$10$nZH15cHbSPU/8QSlywrgJ.iZgZd26uQuGTHGBspiaArJaOzVMYFO6', '0', 12, '1', NULL, NULL, NULL, NULL, NULL),
(100, 'accounting', '$2y$10$nZH15cHbSPU/8QSlywrgJ.iZgZd26uQuGTHGBspiaArJaOzVMYFO6', '0', 6, '1', NULL, NULL, NULL, NULL, NULL),
(101, 'finance_manager', '$2y$10$nZH15cHbSPU/8QSlywrgJ.iZgZd26uQuGTHGBspiaArJaOzVMYFO6', '0', 7, '1', NULL, NULL, NULL, NULL, NULL),
(102, 'finance', '$2y$10$nZH15cHbSPU/8QSlywrgJ.iZgZd26uQuGTHGBspiaArJaOzVMYFO6', '0', 8, '1', NULL, NULL, NULL, NULL, NULL),
(103, 'hr_manager', '$2y$10$nZH15cHbSPU/8QSlywrgJ.iZgZd26uQuGTHGBspiaArJaOzVMYFO6', '0', 9, '1', NULL, NULL, NULL, NULL, NULL),
(104, 'hr', '$2y$10$nZH15cHbSPU/8QSlywrgJ.iZgZd26uQuGTHGBspiaArJaOzVMYFO6', '0', 10, '1', NULL, NULL, NULL, NULL, NULL),
(99, 'accounting_manager', '$2y$10$nZH15cHbSPU/8QSlywrgJ.iZgZd26uQuGTHGBspiaArJaOzVMYFO6', '0', 5, '1', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `web`
--

CREATE TABLE `web` (
  `id` int(11) NOT NULL,
  `url` varchar(200) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zone_settings`
--

CREATE TABLE `zone_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `floor_plan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `zone_name` varchar(255) NOT NULL,
  `width` int(11) NOT NULL DEFAULT 80,
  `height` int(11) NOT NULL DEFAULT 50,
  `rotation` int(11) NOT NULL DEFAULT 0,
  `z_index` int(11) NOT NULL DEFAULT 10,
  `border_radius` float NOT NULL DEFAULT 6,
  `border_width` float NOT NULL DEFAULT 2,
  `opacity` float NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zone_settings`
--

INSERT INTO `zone_settings` (`id`, `floor_plan_id`, `zone_name`, `width`, `height`, `rotation`, `z_index`, `border_radius`, `border_width`, `opacity`, `created_at`, `updated_at`) VALUES
(1, 1, 'A', 50, 50, -12, 10, 1, 5, 1, '2026-01-07 05:44:09', '2026-01-07 06:14:43'),
(2, 1, 'B', 60, 60, -12, 10, 1, 5, 1, '2026-01-07 07:01:04', '2026-01-08 03:21:15'),
(3, 1, 'C', 60, 60, 0, 10, 6, 6, 1, '2026-01-08 03:35:36', '2026-01-08 03:36:26'),
(4, 1, 'D', 60, 60, 0, 10, 6, 5, 1, '2026-01-08 03:47:42', '2026-01-08 03:48:33'),
(5, 4, 'A', 130, 130, 0, 10, 1, 7, 1, '2026-01-10 14:19:23', '2026-01-10 14:41:26'),
(6, 4, 'B', 130, 130, 0, 10, 1, 7, 1, '2026-01-10 14:47:47', '2026-01-10 14:47:47'),
(7, 4, 'C', 130, 130, 0, 10, 1, 7, 1, '2026-01-11 06:14:14', '2026-01-11 06:14:14'),
(8, 4, 'D', 132, 132, 0, 10, 1, 10, 1, '2026-01-11 06:20:04', '2026-01-11 06:25:50'),
(9, 4, 'E', 132, 132, 0, 10, 1, 10, 1, '2026-01-11 06:45:59', '2026-01-11 06:45:59'),
(10, 4, 'F', 134, 134, 0, 10, 1, 8, 1, '2026-01-11 06:58:52', '2026-01-11 08:16:59'),
(11, 4, 'H', 395, 160, 0, 10, 2, 7, 1, '2026-01-11 08:24:08', '2026-01-11 08:24:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`),
  ADD KEY `activity_logs_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `activity_logs_created_at_index` (`created_at`);

--
-- Indexes for table `asset`
--
ALTER TABLE `asset`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_employee_date_unique` (`employee_id`,`date`),
  ADD KEY `attendance_employee_id_foreign` (`employee_id`),
  ADD KEY `attendance_approved_by_foreign` (`approved_by`),
  ADD KEY `attendance_date_index` (`date`);

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_event_id` (`event_id`),
  ADD KEY `idx_floor_plan_id` (`floor_plan_id`);

--
-- Indexes for table `booth`
--
ALTER TABLE `booth`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booth_number_floor_plan_unique` (`booth_number`,`floor_plan_id`),
  ADD KEY `idx_floor_plan_id` (`floor_plan_id`);

--
-- Indexes for table `booth_type`
--
ALTER TABLE `booth_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `canvas_settings`
--
ALTER TABLE `canvas_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_floor_plan_id` (`floor_plan_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `costings`
--
ALTER TABLE `costings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `costings_costing_date_index` (`costing_date`),
  ADD KEY `costings_status_index` (`status`),
  ADD KEY `costings_floor_plan_id_index` (`floor_plan_id`),
  ADD KEY `costings_booking_id_index` (`booking_id`),
  ADD KEY `costings_created_by_index` (`created_by`),
  ADD KEY `costings_approved_by_index` (`approved_by`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_code_unique` (`code`),
  ADD KEY `departments_manager_id_foreign` (`manager_id`),
  ADD KEY `departments_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_templates_slug_unique` (`slug`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_employee_code_unique` (`employee_code`),
  ADD UNIQUE KEY `employees_user_id_unique` (`user_id`),
  ADD KEY `employees_department_id_foreign` (`department_id`),
  ADD KEY `employees_position_id_foreign` (`position_id`),
  ADD KEY `employees_manager_id_foreign` (`manager_id`),
  ADD KEY `employees_created_by_foreign` (`created_by`);

--
-- Indexes for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_documents_employee_id_foreign` (`employee_id`),
  ADD KEY `employee_documents_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `employee_training`
--
ALTER TABLE `employee_training`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_training_employee_id_foreign` (`employee_id`),
  ADD KEY `employee_training_created_by_foreign` (`created_by`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expenses_expense_date_index` (`expense_date`),
  ADD KEY `expenses_category_id_index` (`category_id`),
  ADD KEY `expenses_status_index` (`status`),
  ADD KEY `expenses_floor_plan_id_index` (`floor_plan_id`),
  ADD KEY `expenses_booking_id_index` (`booking_id`),
  ADD KEY `expenses_created_by_index` (`created_by`),
  ADD KEY `expenses_approved_by_index` (`approved_by`);

--
-- Indexes for table `finance_categories`
--
ALTER TABLE `finance_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `finance_categories_type_index` (`type`),
  ADD KEY `finance_categories_is_active_index` (`is_active`),
  ADD KEY `finance_categories_created_by_index` (`created_by`);

--
-- Indexes for table `floor_plans`
--
ALTER TABLE `floor_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_event_id` (`event_id`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_is_default` (`is_default`);

--
-- Indexes for table `leave_balances`
--
ALTER TABLE `leave_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `leave_balances_unique` (`employee_id`,`leave_type_id`,`year`),
  ADD KEY `leave_balances_employee_id_foreign` (`employee_id`),
  ADD KEY `leave_balances_leave_type_id_foreign` (`leave_type_id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_requests_employee_id_foreign` (`employee_id`),
  ADD KEY `leave_requests_leave_type_id_foreign` (`leave_type_id`),
  ADD KEY `leave_requests_approved_by_foreign` (`approved_by`),
  ADD KEY `leave_requests_rejected_by_foreign` (`rejected_by`),
  ADD KEY `leave_requests_status_index` (`status`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `leave_types_code_unique` (`code`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_from_user_id_foreign` (`from_user_id`),
  ADD KEY `messages_to_user_id_foreign` (`to_user_id`),
  ADD KEY `messages_client_id_foreign` (`client_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`),
  ADD KEY `notifications_client_id_foreign` (`client_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_booking_id_foreign` (`booking_id`),
  ADD KEY `payments_client_id_foreign` (`client_id`),
  ADD KEY `payments_user_id_foreign` (`user_id`);

--
-- Indexes for table `performance_reviews`
--
ALTER TABLE `performance_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `performance_reviews_employee_id_foreign` (`employee_id`),
  ADD KEY `performance_reviews_reviewer_id_foreign` (`reviewer_id`);

--
-- Indexes for table `performance_review_criteria`
--
ALTER TABLE `performance_review_criteria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `performance_review_criteria_review_id_foreign` (`review_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_slug_unique` (`slug`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `positions_code_unique` (`code`),
  ADD KEY `positions_department_id_foreign` (`department_id`);

--
-- Indexes for table `revenues`
--
ALTER TABLE `revenues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `revenues_revenue_date_index` (`revenue_date`),
  ADD KEY `revenues_category_id_index` (`category_id`),
  ADD KEY `revenues_status_index` (`status`),
  ADD KEY `revenues_client_id_index` (`client_id`),
  ADD KEY `revenues_floor_plan_id_index` (`floor_plan_id`),
  ADD KEY `revenues_booking_id_index` (`booking_id`),
  ADD KEY `revenues_created_by_index` (`created_by`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_permissions_role_id_permission_id_unique` (`role_id`,`permission_id`),
  ADD KEY `role_permissions_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `salary_history`
--
ALTER TABLE `salary_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `salary_history_employee_id_foreign` (`employee_id`),
  ADD KEY `salary_history_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`),
  ADD KEY `settings_key_index` (`key`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_role_id_index` (`role_id`);

--
-- Indexes for table `web`
--
ALTER TABLE `web`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zone_settings`
--
ALTER TABLE `zone_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `zone_name_floor_plan_unique` (`zone_name`,`floor_plan_id`),
  ADD KEY `zone_settings_zone_name_index` (`zone_name`),
  ADD KEY `idx_floor_plan_id` (`floor_plan_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset`
--
ALTER TABLE `asset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `book`
--
ALTER TABLE `book`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

--
-- AUTO_INCREMENT for table `booth`
--
ALTER TABLE `booth`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=681;

--
-- AUTO_INCREMENT for table `booth_type`
--
ALTER TABLE `booth_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `canvas_settings`
--
ALTER TABLE `canvas_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

--
-- AUTO_INCREMENT for table `costings`
--
ALTER TABLE `costings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_documents`
--
ALTER TABLE `employee_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_training`
--
ALTER TABLE `employee_training`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `finance_categories`
--
ALTER TABLE `finance_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `floor_plans`
--
ALTER TABLE `floor_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `leave_balances`
--
ALTER TABLE `leave_balances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `performance_reviews`
--
ALTER TABLE `performance_reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `performance_review_criteria`
--
ALTER TABLE `performance_review_criteria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `revenues`
--
ALTER TABLE `revenues`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=309;

--
-- AUTO_INCREMENT for table `salary_history`
--
ALTER TABLE `salary_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `web`
--
ALTER TABLE `web`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zone_settings`
--
ALTER TABLE `zone_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `costings`
--
ALTER TABLE `costings`
  ADD CONSTRAINT `costings_floor_plan_id_foreign` FOREIGN KEY (`floor_plan_id`) REFERENCES `floor_plans` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `departments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `finance_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_floor_plan_id_foreign` FOREIGN KEY (`floor_plan_id`) REFERENCES `floor_plans` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `positions_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `revenues`
--
ALTER TABLE `revenues`
  ADD CONSTRAINT `revenues_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `finance_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `revenues_floor_plan_id_foreign` FOREIGN KEY (`floor_plan_id`) REFERENCES `floor_plans` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `zone_settings`
--
ALTER TABLE `zone_settings`
  ADD CONSTRAINT `fk_zone_settings_floor_plan` FOREIGN KEY (`floor_plan_id`) REFERENCES `floor_plans` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
