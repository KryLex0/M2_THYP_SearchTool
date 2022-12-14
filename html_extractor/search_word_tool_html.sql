-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  Dim 04 déc. 2022 à 00:44
-- Version du serveur :  10.4.10-MariaDB
-- Version de PHP :  7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `search_word_tool_html`
--

-- --------------------------------------------------------

--
-- Structure de la table `page_data`
--

DROP TABLE IF EXISTS `page_data`;
CREATE TABLE IF NOT EXISTS `page_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fileURL` varchar(255) NOT NULL,
  `fileTitle` varchar(255) NOT NULL,
  `fileDescription` varchar(255) NOT NULL,
  `fileTimestamp` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `word_list`
--

DROP TABLE IF EXISTS `word_list`;
CREATE TABLE IF NOT EXISTS `word_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mot` varchar(255) NOT NULL,
  `nbOccurence` varchar(255) NOT NULL,
  `idPage` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `word_occurence_page`
--

DROP TABLE IF EXISTS `word_occurence_page`;
CREATE TABLE IF NOT EXISTS `word_occurence_page` (
  `idWord` int(11) NOT NULL,
  `idPage` int(11) NOT NULL,
  `nb_occurence` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
