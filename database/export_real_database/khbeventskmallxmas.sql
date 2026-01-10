-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 10, 2026 at 03:28 PM
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
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `id` int(11) NOT NULL,
  `clientid` int(11) NOT NULL,
  `boothid` mediumtext NOT NULL,
  `date_book` datetime NOT NULL,
  `userid` int(11) NOT NULL,
  `type` tinyint(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`id`, `clientid`, `boothid`, `date_book`, `userid`, `type`) VALUES
(113, 101, '[\"133\"]', '2023-12-13 11:43:52', 61, 2),
(157, 145, '[\"38\"]', '2023-12-20 19:14:39', 61, 2),
(163, 151, '[\"129\"]', '2023-12-21 19:50:01', 61, 2),
(61, 60, '[\"132\"]', '2023-11-30 12:25:58', 61, 2),
(146, 134, '[\"57\"]', '2023-12-19 16:18:24', 61, 2),
(164, 152, '[\"155\"]', '2023-12-21 19:57:30', 61, 2),
(183, 171, '[\"63\"]', '2023-12-28 17:15:37', 84, 5),
(166, 154, '[\"43\",\"44\"]', '2023-12-21 20:36:16', 61, 2),
(39, 39, '[\"77\"]', '2023-11-21 15:27:48', 75, 5),
(147, 135, '[\"150\",\"151\"]', '2023-12-19 16:25:36', 61, 2),
(175, 163, '[\"69\"]', '2023-12-21 21:27:21', 61, 2),
(161, 149, '[\"139\"]', '2023-12-21 19:32:16', 61, 2),
(160, 148, '[\"29\"]', '2023-12-20 19:54:43', 61, 2),
(129, 117, '[\"121\",\"122\"]', '2023-12-17 23:02:09', 61, 2),
(173, 161, '[\"101\"]', '2023-12-21 21:22:48', 61, 2),
(170, 158, '[\"134\"]', '2023-12-21 21:11:34', 61, 2),
(167, 155, '[\"130\"]', '2023-12-21 21:06:29', 61, 2),
(181, 169, '[\"64\",\"65\"]', '2023-12-28 15:50:34', 61, 2),
(34, 34, '[\"19\",\"20\"]', '2023-11-18 13:06:14', 61, 2),
(171, 159, '[\"39\",\"40\"]', '2023-12-21 21:12:12', 84, 2),
(120, 108, '[\"54\",\"55\",\"56\"]', '2023-12-13 15:35:45', 75, 5),
(33, 33, '[\"17\"]', '2023-11-18 13:05:30', 61, 2),
(125, 113, '[\"81\"]', '2023-12-15 17:16:20', 61, 2),
(138, 126, '[\"47\"]', '2023-12-19 15:06:16', 87, 2),
(182, 170, '[\"146\"]', '2023-12-28 15:58:03', 61, 2),
(153, 141, '[\"128\"]', '2023-12-20 09:19:01', 61, 2),
(152, 140, '[\"34\",\"35\"]', '2023-12-20 09:13:06', 61, 2),
(168, 156, '[\"83\",\"84\"]', '2023-12-21 21:07:23', 61, 2),
(141, 129, '[\"15\"]', '2023-12-19 15:31:20', 61, 2),
(184, 172, '[\"41\"]', '2024-01-10 13:48:46', 87, 2),
(65, 62, '[\"18\"]', '2023-11-30 14:34:02', 75, 5),
(52, 51, '[\"16\"]', '2023-11-22 21:05:00', 61, 2),
(60, 59, '[\"28\"]', '2023-11-29 22:21:03', 61, 2),
(57, 56, '[\"21\",\"22\",\"23\"]', '2023-11-29 21:28:48', 61, 2),
(56, 55, '[\"36\",\"37\"]', '2023-11-29 10:45:51', 61, 2),
(145, 133, '[\"49\"]', '2023-12-19 16:13:45', 75, 2),
(140, 128, '[\"52\",\"51\"]', '2023-12-19 15:29:31', 61, 2),
(144, 132, '[\"50\"]', '2023-12-19 16:13:42', 61, 2),
(135, 123, '[\"124\",\"126\"]', '2023-12-18 09:45:49', 61, 2),
(151, 139, '[\"103\",\"104\",\"105\",\"106\",\"107\",\"108\"]', '2023-12-19 19:19:00', 61, 2),
(154, 142, '[\"59\"]', '2023-12-20 10:04:31', 87, 2),
(101, 90, '[\"14\"]', '2023-12-11 09:46:32', 61, 2),
(126, 114, '[\"33\",\"32\"]', '2023-12-15 17:19:01', 61, 2),
(80, 70, '[\"25\",\"24\"]', '2023-12-03 15:19:09', 61, 2),
(103, 91, '[\"140\"]', '2023-12-11 10:59:16', 84, 2),
(91, 80, '[\"53\"]', '2023-12-07 13:49:04', 61, 2),
(172, 160, '[\"99\",\"100\"]', '2023-12-21 21:12:37', 61, 2),
(159, 147, '[\"30\"]', '2023-12-20 19:53:29', 61, 2),
(128, 116, '[\"149\"]', '2023-12-17 21:32:54', 61, 2),
(169, 157, '[\"60\"]', '2023-12-21 21:11:19', 75, 2),
(105, 93, '[\"79\"]', '2023-12-11 12:53:39', 75, 5),
(158, 146, '[\"27\"]', '2023-12-20 19:15:50', 84, 2),
(116, 104, '[\"148\"]', '2023-12-13 13:39:37', 75, 2),
(176, 164, '[\"68\"]', '2023-12-21 21:27:55', 61, 2),
(97, 86, '[\"45\",\"46\"]', '2023-12-09 13:48:27', 61, 2),
(98, 87, '[\"142\"]', '2023-12-10 15:35:26', 75, 3),
(155, 143, '[\"131\"]', '2023-12-20 12:47:08', 61, 2),
(148, 136, '[\"78\"]', '2023-12-19 16:48:32', 61, 2),
(177, 165, '[\"85\",\"86\",\"87\",\"88\",\"89\",\"90\"]', '2023-12-21 21:36:26', 61, 2),
(180, 168, '[\"48\"]', '2023-12-22 11:47:15', 61, 2);

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
(307, 1, 'C18', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 3911.00, 650.00, 60.00, 60.00, 0.00, 10, 27, 6, 6, 1.00, 'rgb(255, 255, 255)', 'rgb(0, 123, 255)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
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
(364, 4, 'A01', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 670.00, 1370.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(365, 4, 'A02', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 670.00, 1500.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(366, 4, 'A03', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 670.00, 1870.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(367, 4, 'A04', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 670.00, 2000.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(368, 4, 'A05', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 670.00, 2130.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(369, 4, 'A06', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 670.00, 2260.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(370, 4, 'A07', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 670.00, 2385.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(371, 4, 'A08', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 669.00, 2509.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(372, 4, 'A09', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 669.00, 2638.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(373, 4, 'A10', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 669.00, 2767.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(374, 4, 'A11', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 668.00, 2893.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(375, 4, 'A12', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 668.00, 3025.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(376, 4, 'A13', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 671.00, 3156.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(377, 4, 'A14', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 926.00, 3212.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(378, 4, 'A15', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1055.00, 3212.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(379, 4, 'A16', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 927.00, 3083.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(380, 4, 'A17', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1055.00, 3085.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(381, 4, 'A18', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 924.00, 2833.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(382, 4, 'A19', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1055.00, 2836.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(383, 4, 'A20', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 923.00, 2705.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(384, 4, 'A21', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1057.00, 2707.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(385, 4, 'A22', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 926.00, 2463.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(386, 4, 'A23', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1062.00, 2462.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(387, 4, 'A24', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 926.00, 2334.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(388, 4, 'A25', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1059.00, 2333.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(389, 4, 'A26', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 928.00, 1963.00, 130.00, 130.00, 0.00, 20, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'bold', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(390, 4, 'A27', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1710.00, 2470.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(391, 4, 'A28', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1860.00, 2470.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(392, 4, 'A29', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2007.00, 2467.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(393, 4, 'A30', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2463.00, 2692.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(394, 4, 'A31', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2310.00, 2470.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(395, 4, 'A32', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2460.00, 2470.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(396, 4, 'A33', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2610.00, 2470.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(397, 4, 'A34', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2760.00, 2470.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(398, 4, 'A35', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1710.00, 2620.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(399, 4, 'A36', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1860.00, 2620.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(400, 4, 'A37', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2010.00, 2620.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(401, 4, 'A38', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2160.00, 2620.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(402, 4, 'A39', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2310.00, 2620.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(403, 4, 'A40', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2460.00, 2620.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(404, 4, 'A41', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2610.00, 2620.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(405, 4, 'A42', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2760.00, 2620.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(406, 4, 'A43', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1710.00, 2770.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(407, 4, 'A44', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1860.00, 2770.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(408, 4, 'A45', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2010.00, 2770.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(409, 4, 'A46', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2160.00, 2770.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(410, 4, 'A47', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2310.00, 2770.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(411, 4, 'A48', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2460.00, 2770.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(412, 4, 'A49', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2610.00, 2770.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px');
INSERT INTO `booth` (`id`, `floor_plan_id`, `booth_number`, `type`, `price`, `status`, `client_id`, `userid`, `bookid`, `category_id`, `sub_category_id`, `asset_id`, `booth_type_id`, `position_x`, `position_y`, `width`, `height`, `rotation`, `z_index`, `font_size`, `border_width`, `border_radius`, `opacity`, `background_color`, `border_color`, `text_color`, `font_weight`, `font_family`, `text_align`, `box_shadow`) VALUES
(413, 4, 'A50', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2760.00, 2770.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(414, 4, 'A51', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1710.00, 2920.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(415, 4, 'A52', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 1860.00, 2920.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px'),
(416, 4, 'A53', 2, 500, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, 2010.00, 2920.00, 130.00, 130.00, 0.00, 10, 40, 5, 1, 1.00, 'rgb(255, 255, 255)', 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', '700', 'Arial, sans-serif', 'center', 'rgba(0, 0, 0, 0.2) 0px 2px 8px');

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

INSERT INTO `canvas_settings` (`id`, `canvas_width`, `canvas_height`, `canvas_resolution`, `grid_size`, `zoom_level`, `pan_x`, `pan_y`, `floorplan_image`, `grid_enabled`, `snap_to_grid`, `created_at`, `updated_at`) VALUES
(1, 6250, 3125, 300, 10, 0.22, 0.00, 0.00, '{{ asset($currentFloorPlan->floor_image) }}', 0, 1, '2026-01-07 06:57:00', '2026-01-10 14:27:48');

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
(1, NULL, 'Kmall', NULL, 'Main Project', 'images/floor-plans/1768054390_floor_plan_1.jpg', 6250, 3125, 1, 0, NULL, '2026-01-10 09:59:01', '2026-01-10 14:22:48'),
(4, NULL, 'Phnom Penh Shopping Festival', 'Phnom Penh Shopping Festival at kohpich', 'Phnom Penh Shopping Festival', NULL, 1200, 800, 1, 0, 61, '2026-01-10 13:49:44', '2026-01-10 14:22:43');

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
(74, 'Hanna', '$2y$13$pR5QzJhQcpCZsXSScQijSefWM9MhPokRTZg2ATGJSS7pt9lz3Fg36', '2', NULL, '1', NULL, NULL, '2023-12-02 11:59:21', '2023-10-03 18:43:40', '2023-10-03 18:43:40'),
(30, 'vutha', '$2y$13$c/ruiCdTaNuO599eJSC1i.mTWXzkwMUti0sz0Q1S1z9PWwmxxYw9a', '2', NULL, '1', NULL, NULL, '2023-02-27 08:57:48', '2019-10-24 10:01:03', '2023-03-18 11:23:50'),
(75, 'Rany', '$2y$13$ll4z6/fRI50GQWuXFnmLYONvCMPn5ij494DAhZ.2G05NhiKBqj.4S', '2', NULL, '1', NULL, NULL, '2024-01-03 13:51:52', '2023-10-03 18:45:40', '2023-10-03 18:45:40'),
(76, 'Supov', '$2y$13$XXIW.0AXP3Pw8Dt.y.T8BeRPoubb6QVt39LgVN9JTFJh4ceF9DN2u', '2', NULL, '1', NULL, NULL, '2023-11-12 15:29:04', '2023-10-04 08:49:28', '2023-10-04 08:49:28'),
(77, 'Dina', '$2y$13$0OeoC5yKO5PXgT3FizvfiuhZCSdTLcTE3KfR4um0BPRoFMSvUGvAe', '2', NULL, '1', NULL, NULL, '2023-10-24 13:44:41', '2023-10-04 16:10:20', '2023-10-04 16:10:20'),
(78, 'vichetra', '$2y$13$4MJVsGIJ7ZXup.0ITH2J2.nqByNAGdTSssJyMrbTz1kHujOaeGudO', '2', NULL, '1', NULL, NULL, NULL, '2023-10-12 13:17:03', '2023-10-12 13:17:03'),
(79, 'Chanrany', '$2y$13$tzSOAa9F1jN.g9WpqfGAY.lqYTvpAK0VlJhZCxnT5qJSiEsAGh.LW', '2', NULL, '1', NULL, NULL, '2023-10-25 13:22:48', '2023-10-17 14:50:57', '2023-10-17 14:50:57'),
(61, 'vutha_admin', '$2y$12$AdwcjrTEEr4TrkN4kV6lmu9g8u5XXC6SSlNWhbM6tf/bVygrq1dIO', '1', NULL, '1', NULL, NULL, '2025-12-26 15:51:34', '2019-10-24 10:01:03', '2026-01-04 22:59:05'),
(84, 'Kunthea', '$2y$13$ZIB7bCEZQP.KvL2/8S2ha.f.p8u2/0FSEDGBdS02dPy6e6olqpzB6', '1', NULL, '1', NULL, NULL, '2024-01-12 09:12:56', '2023-11-16 10:38:46', '2023-11-16 10:38:46'),
(81, 'Yermal', '$2y$13$i47BF8XJbswbzSoH59oBIextkNOccz3xKprV1C09EACNK9QHrLlXC', '2', NULL, '1', NULL, NULL, '2023-10-19 08:50:20', '2023-10-19 08:48:37', '2023-10-19 08:48:37'),
(83, 'Naly', '$2y$13$386w/9r9qdGTiBqr26JBzORa6QqdDXfYQU4ePUUw826oWMmofG6bW', '2', NULL, '1', NULL, NULL, NULL, '2023-10-23 11:32:27', '2023-10-23 11:32:27'),
(85, 'Kuntheathea', '$2y$13$OUCMEnszPM.Z3HpAS1gwLu0NJGyZc1mHshZFAA61rq9ehwfw7Gj3a', '2', NULL, '1', NULL, NULL, '2023-11-16 15:46:54', '2023-11-16 15:46:15', '2023-11-16 15:46:15'),
(86, 'Socheata', '$2y$13$nrPtsUYOoAyQoT3krtDOXu5xUMbNEZYFRI6D/MAx5rVe6dYE7JS6G', '2', NULL, '1', NULL, NULL, '2024-02-07 14:10:46', '2023-12-04 12:48:45', '2023-12-04 12:48:45'),
(87, 'Sina', '$2y$13$0trEyykrKQW3qvcFyc.CUuJZXT/0UE.k4Qz69.EgQWViw6L8MLfTy', '2', NULL, '1', NULL, NULL, '2024-01-10 13:43:30', '2023-12-11 09:23:21', '2023-12-11 09:23:21'),
(88, 'Sariya', '$2y$13$HErZMdBjzcAu1LgOYXSKQuVb0oeHO5aSDct92ME7JvXSu0gbVWi0e', '2', NULL, '1', NULL, NULL, '2023-12-11 16:03:08', '2023-12-11 10:19:47', '2023-12-11 10:19:47'),
(89, 'jinh', '$2y$13$VhCisi1b/NhVwaJnjHxefusS8zDJZ4AtYO2349s1Uj3nPhvFPmBHa', '2', NULL, '1', NULL, NULL, '2023-12-11 10:35:24', '2023-12-11 10:30:22', '2023-12-11 10:30:22'),
(90, 'Linna', '$2y$13$EvNr3GJc4/DVzh6pyyjy5OwCcGGo6qg8eFpefwvZ24uCLikrvdlWO', '1', NULL, '1', NULL, NULL, '2023-12-18 12:33:30', '2023-12-13 08:46:50', '2023-12-13 10:24:32'),
(91, 'Chihour', '$2y$13$ka/jR.yfwSKxactzxWi6f.TcY7Fd0WmC9FRXiclUsOxsVgeHqgqce', '2', NULL, '1', NULL, NULL, '2024-01-03 13:50:10', '2023-12-13 15:31:42', '2023-12-13 15:31:42'),
(92, 'Sokpanha', '$2y$13$D2ZsaYvjRChmXUN1fveL8e50Mu9KU42It7MCYrO5vOPwAZW8FM5em', '2', NULL, '1', NULL, NULL, '2024-01-18 14:23:24', '2023-12-15 17:38:36', '2023-12-15 17:38:36'),
(93, 'Somethea', '$2y$13$3jlc3UdtPCbtr/qlD.9dQu52HAu3M2ife6omATi.ecfDYkRmRZqZS', '2', NULL, '1', NULL, NULL, NULL, '2023-12-19 14:59:42', '2023-12-19 14:59:42');

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
(5, 4, 'A', 130, 130, 0, 10, 1, 5, 1, '2026-01-10 14:19:23', '2026-01-10 14:20:57');

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
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_templates_slug_unique` (`slug`);

--
-- Indexes for table `floor_plans`
--
ALTER TABLE `floor_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_event_id` (`event_id`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_is_default` (`is_default`);

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
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_slug_unique` (`slug`);

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
-- AUTO_INCREMENT for table `book`
--
ALTER TABLE `book`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

--
-- AUTO_INCREMENT for table `booth`
--
ALTER TABLE `booth`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=417;

--
-- AUTO_INCREMENT for table `booth_type`
--
ALTER TABLE `booth_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `canvas_settings`
--
ALTER TABLE `canvas_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `floor_plans`
--
ALTER TABLE `floor_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `web`
--
ALTER TABLE `web`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zone_settings`
--
ALTER TABLE `zone_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `zone_settings`
--
ALTER TABLE `zone_settings`
  ADD CONSTRAINT `fk_zone_settings_floor_plan` FOREIGN KEY (`floor_plan_id`) REFERENCES `floor_plans` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
