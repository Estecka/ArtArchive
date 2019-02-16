-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 16, 2019 at 11:39 PM
-- Server version: 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `artdump`
--

-- --------------------------------------------------------

--
-- Table structure for table `arts-tags`
--

CREATE TABLE `arts-tags` (
  `artwork` int(11) NOT NULL,
  `tag` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `arts-tags`
--
ALTER TABLE `arts-tags`
  ADD UNIQUE KEY `art-tag` (`artwork`,`tag`),
  ADD KEY `artwork` (`artwork`),
  ADD KEY `tag` (`tag`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `arts-tags`
--
ALTER TABLE `arts-tags`
  ADD CONSTRAINT `art's tags` FOREIGN KEY (`tag`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tag's arts` FOREIGN KEY (`artwork`) REFERENCES `artworks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
