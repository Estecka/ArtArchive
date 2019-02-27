-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 27, 2019 at 10:03 PM
-- Server version: 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `artdump`
--

-- --------------------------------------------------------

--
-- Table structure for table `art-tag`
--

CREATE TABLE `art-tag` (
  `artId` int(11) NOT NULL,
  `tagId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `art-tag`
--
ALTER TABLE `art-tag`
  ADD UNIQUE KEY `art-tag` (`artId`,`tagId`),
  ADD KEY `artwork` (`artId`),
  ADD KEY `tag` (`tagId`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `art-tag`
--
ALTER TABLE `art-tag`
  ADD CONSTRAINT `art's tags` FOREIGN KEY (`tagId`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tag's arts` FOREIGN KEY (`artId`) REFERENCES `artworks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
