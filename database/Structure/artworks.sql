-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 26, 2021 at 10:33 PM
-- Server version: 5.7.31
-- PHP Version: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `artdump`
--

-- --------------------------------------------------------

--
-- Table structure for table `artworks`
--

CREATE TABLE `artworks` (
  `id` int(11) NOT NULL,
  `slug` varchar(32) CHARACTER SET ascii NOT NULL,
  `title` varchar(128) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `description` text,
  `links` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `artworks`
--
-- CREATE TRIGGER `artworks_CHECK_slug_INSERT` BEFORE INSERT ON `artworks` FOR EACH ROW CALL Check_Slug(new.slug)
-- ;
-- CREATE TRIGGER `artworks_CHECK_slug_UPDATE` BEFORE UPDATE ON `artworks` FOR EACH ROW CALL Check_Slug(new.slug)
-- ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `artworks`
--
ALTER TABLE `artworks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `artworks`
--
ALTER TABLE `artworks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
