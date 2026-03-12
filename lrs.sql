-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2026 at 09:18 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `consignment`
--

-- --------------------------------------------------------

--
-- Table structure for table `lrs`
--

CREATE TABLE `lrs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `consignor_id` bigint(20) UNSIGNED NOT NULL,
  `consignee_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_no` varchar(50) NOT NULL,
  `lr_no` varchar(15) NOT NULL,
  `bill_date` date NOT NULL,
  `insurance` varchar(10) NOT NULL,
  `registered_address_id` bigint(20) UNSIGNED NOT NULL,
  `billing_address_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_address_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `fssai_no` varchar(25) DEFAULT NULL,
  `gstin` varchar(25) DEFAULT NULL,
  `msme` varchar(25) DEFAULT NULL,
  `origin` varchar(50) NOT NULL,
  `destination` varchar(50) NOT NULL,
  `vehicle_no` varchar(50) DEFAULT NULL,
  `consignor` varchar(100) DEFAULT NULL,
  `consignee` varchar(100) DEFAULT NULL,
  `packages` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `actual_weight` decimal(10,2) DEFAULT NULL,
  `charged` decimal(10,2) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `invoice_value` decimal(12,2) DEFAULT NULL,
  `surcharge` decimal(10,2) DEFAULT 0.00,
  `hamali` decimal(10,2) DEFAULT 0.00,
  `risk_charge` decimal(10,2) DEFAULT 0.00,
  `b_charge` decimal(10,2) DEFAULT 0.00,
  `other_charge` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(12,2) DEFAULT 0.00,
  `caution` text DEFAULT NULL,
  `notice` text DEFAULT NULL,
  `indent_no` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lrs`
--

INSERT INTO `lrs` (`id`, `vendor_id`, `consignor_id`, `consignee_id`, `invoice_no`, `lr_no`, `bill_date`, `insurance`, `registered_address_id`, `billing_address_id`, `branch_address_id`, `created_at`, `updated_at`, `fssai_no`, `gstin`, `msme`, `origin`, `destination`, `vehicle_no`, `consignor`, `consignee`, `packages`, `description`, `actual_weight`, `charged`, `rate`, `amount`, `invoice_value`, `surcharge`, `hamali`, `risk_charge`, `b_charge`, `other_charge`, `total_amount`, `caution`, `notice`, `indent_no`) VALUES
(1, 5, 1, 76, 'LR-2025-2026-00001', 'LR%$3', '2026-03-11', 'Yes', 2, NULL, NULL, '2026-03-11 10:31:43', '2026-03-11 10:31:43', 'FSSAI1234', '09AAHFT2345C', 'MSME2345', 'South Sikkim', 'South Sikkim', 'hgsdasah123', NULL, NULL, '123', 'sdjsf dsfsdfsdf sdfsdf sd', 1250.00, 1200.00, NULL, NULL, 20000.00, 12.00, 11.00, 23.00, 45.00, 67.00, 20158.00, 'Lorem ipsum caution Lorem Ipsum Lorem ipsum caution Lorem IpsumLorem ipsum caution Lorem Ipsum', 'Lorem ipsum caution Lorem IpsumLorem ipsum caution Lorem Ipsum', NULL),
(2, 5, 1, 75, 'LR-2025-2026-00002', 'wqerew1234', '2026-03-13', 'Yes', 2, NULL, NULL, '2026-03-11 13:50:34', '2026-03-11 13:50:34', 'FSSAI1234', '09AAHFT2345C', 'MSME2345', 'South Sikkim', 'South Sikkim', 'wdeher er ew', 'Synergy India Pvt Ltd', 'ZWPL Sikkim', '123456', 'test', 1250.00, 1200.00, NULL, NULL, 25000.00, 1.00, 0.60, 1.80, 12.00, 2.00, 25017.40, 'Lorem ipsum caution Lorem Ipsum Lorem ipsum caution Lorem IpsumLorem ipsum caution Lorem Ipsum', 'Lorem ipsum caution Lorem IpsumLorem ipsum caution Lorem Ipsum', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lrs`
--
ALTER TABLE `lrs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lrs`
--
ALTER TABLE `lrs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
