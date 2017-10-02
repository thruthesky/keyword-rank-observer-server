-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 02, 2017 at 07:55 AM
-- Server version: 10.1.23-MariaDB
-- PHP Version: 7.0.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `keyword-rank-observer`
--

-- --------------------------------------------------------

--
-- Table structure for table `keyword_ranks`
--

CREATE TABLE `keyword_ranks` (
  `idx` int(10) UNSIGNED NOT NULL,
  `platform` char(7) NOT NULL,
  `keyword` varchar(128) NOT NULL,
  `date` char(8) NOT NULL,
  `time` char(4) NOT NULL,
  `name` varchar(128) NOT NULL,
  `rank` tinyint(4) NOT NULL,
  `title` text NOT NULL,
  `href` text NOT NULL,
  `type` char(4) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `keyword_ranks`
--
ALTER TABLE `keyword_ranks`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `keyword_index` (`platform`,`keyword`,`date`,`time`,`name`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `keyword_ranks`
--
ALTER TABLE `keyword_ranks`
  MODIFY `idx` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;