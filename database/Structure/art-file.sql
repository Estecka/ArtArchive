-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 01, 2019 at 01:24 PM
-- Server version: 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `artdump`
--

-- --------------------------------------------------------

--
-- Table structure for table `art-file`
--

CREATE TABLE `art-file` (
  `artworkId` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `url` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `art-file`
--
ALTER TABLE `art-file`
  ADD KEY `artworkId` (`artworkId`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `art-file`
--
ALTER TABLE `art-file`
  ADD CONSTRAINT `artworkId` FOREIGN KEY (`artworkId`) REFERENCES `artworks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
