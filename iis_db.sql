-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 23, 2020 at 04:02 PM
-- Server version: 5.7.14
-- PHP Version: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: iis_db
--

-- --------------------------------------------------------

--
-- Table structure for table `divak`
--

CREATE TABLE `divak` (
  `div_ID` int(11) NOT NULL,
  `jmeno` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  `prijmeni` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  `telefon` int(9) NOT NULL,
  `email` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `heslo` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `rol_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `rol_ID` int(11) NOT NULL,
  `nazev` varchar(10) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `festival`
--

CREATE TABLE `festival` (
  `fes_ID` int(11) NOT NULL,
  `nazev` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `popis` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `datum` date NOT NULL,
  `misto` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `adresa` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `cena` float NOT NULL,
  `kapacita` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interpret`
--

CREATE TABLE `interpret` (
  `int_ID` int(11) NOT NULL,
  `nazev` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `logo` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `clenove` varchar(200) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rezervace`
--

CREATE TABLE `rezervace` (
  `rez_ID` int(11) NOT NULL,
  `stav` tinyint(1) NOT NULL,
  `pocet_vstupenek` int(11) NOT NULL,
  `uhrazeno` tinyint(1) NOT NULL,
  `cas` timestamp NOT NULL,
  `div_id` int(11) NOT NULL,
  `fes_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stage`
--

CREATE TABLE `stage` (
  `stg_ID` int(11) NOT NULL,
  `nazev` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `fes_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zanr`
--

CREATE TABLE `zanr` (
  `zan_ID` int(11) NOT NULL,
  `nazev` varchar(50) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tagovani`
--

CREATE TABLE `tagovani` (
  `zan_id` int(11) NOT NULL,
  `int_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vystupuje`
--

CREATE TABLE `vystupuje` (
  `stg_id` int(11) NOT NULL,
  `int_id` int(11) NOT NULL,
  `cas` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `divak`
--
ALTER TABLE `divak`
  ADD PRIMARY KEY (`div_ID`),
  ADD KEY `rol_ID` (`rol_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`rol_ID`);

--
-- Indexes for table `festival`
--
ALTER TABLE `festival`
  ADD PRIMARY KEY (`fes_ID`);

--
-- Indexes for table `interpret`
--
ALTER TABLE `interpret`
  ADD PRIMARY KEY (`int_ID`);

--
-- Indexes for table `rezervace`
--
ALTER TABLE `rezervace`
  ADD PRIMARY KEY (`rez_ID`),
  ADD KEY `div_ID` (`div_id`),
  ADD KEY `fes_ID` (`fes_id`);

--
-- Indexes for table `stage`
--
ALTER TABLE `stage`
  ADD PRIMARY KEY (`stg_ID`),
  ADD KEY `fes_id` (`fes_id`);

--
-- Indexes for table `zanr`
--
ALTER TABLE `zanr`
  ADD PRIMARY KEY (`zan_ID`);

--
-- Indexes for table `tagovani`
--
ALTER TABLE `tagovani`
  ADD KEY `zan_id` (`zan_id`),
  ADD KEY `int_id` (`int_id`);

--
-- Indexes for table `vystupuje`
--
ALTER TABLE `vystupuje`
  ADD KEY `stg_id` (`stg_id`),
  ADD KEY `int_id` (`int_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `divak`
--
ALTER TABLE `divak`
  MODIFY `div_ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `rol_ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `festival`
--
ALTER TABLE `festival`
  MODIFY `fes_ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `interpret`
--
ALTER TABLE `interpret`
  MODIFY `int_ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `rezervace`
--
ALTER TABLE `rezervace`
  MODIFY `rez_ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `stage`
--
ALTER TABLE `stage`
  MODIFY `stg_ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `zanr`
--
ALTER TABLE `zanr`
  MODIFY `zan_ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `rezervace`
--
ALTER TABLE `rezervace`
  ADD CONSTRAINT `FK_div_rez` FOREIGN KEY (`div_id`) REFERENCES `divak` (`div_ID`),
  ADD CONSTRAINT `FK_fes_rez` FOREIGN KEY (`fes_id`) REFERENCES `festival` (`fes_ID`);

--
-- Constraints for table `divak`
--
ALTER TABLE `divak`
  ADD CONSTRAINT `FK_rol_div` FOREIGN KEY (`rol_id`) REFERENCES `role` (`rol_ID`);


--
-- Constraints for table `stage`
--
ALTER TABLE `stage`
  ADD CONSTRAINT `FK_fes_stg` FOREIGN KEY (`fes_id`) REFERENCES `festival` (`fes_ID`);

--
-- Constraints for table `tagovani`
--
ALTER TABLE `tagovani`
  ADD CONSTRAINT `FK_int_tagov` FOREIGN KEY (`int_id`) REFERENCES `interpret` (`int_ID`),
  ADD CONSTRAINT `FK_zan_tagov` FOREIGN KEY (`zan_id`) REFERENCES `zanr` (`zan_ID`);

--
-- Constraints for table `vystupuje`
--
ALTER TABLE `vystupuje`
  ADD CONSTRAINT `FK_int_vyst` FOREIGN KEY (`int_id`) REFERENCES `interpret` (`int_ID`),
  ADD CONSTRAINT `FK_stg_vyst` FOREIGN KEY (`stg_id`) REFERENCES `stage` (`stg_ID`);

--
-- Insert `zanr`
--
INSERT INTO zanr (nazev)
VALUES
    ('Blues'),
    ('Country'),
    ('Disco'),
    ('Electro'),
    ('Folk'),
    ('Funk'),
    ('Hip Hop'),
    ('Jazz'),
    ('Metal'),
	('Pop'),
    ('Punk'),
    ('RnB');
    ('Reggae'),
	
--
-- Insert `role`
--
INSERT INTO role (nazev)
VALUES
	('admin'),
    ('organiser'),
    ('accountant'),
    ('user');
	
--
-- Insert `admin`, password: '123'
--
INSERT INTO divak (jmeno, prijmeni, telefon, email, heslo, rol_id)
VALUES ('admin','admin','123456789','admin@fest.cz', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 1);

--
-- Insert `organiser`, password: '123'
--
INSERT INTO divak (jmeno, prijmeni, telefon, email, heslo, rol_id)
VALUES ('organiser','organiser','123456789','organiser@fest.cz', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 2);

--
-- Insert `accountant`, password: '123'
--
INSERT INTO divak (jmeno, prijmeni, telefon, email, heslo, rol_id)
VALUES ('ucetni','ucetni','123456789','ucetni@fest.cz', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 3);

--
-- Insert `user`, password: '123'
--
INSERT INTO divak (jmeno, prijmeni, telefon, email, heslo, rol_id)
VALUES ('user','user','123456789','user@fest.cz', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 4);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
