-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 10 oct. 2022 à 12:19
-- Version du serveur : 8.0.27
-- Version de PHP : 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `search_word_tool`
--

-- --------------------------------------------------------

--
-- Structure de la table `word_file`
--

DROP TABLE IF EXISTS `word_file`;
CREATE TABLE IF NOT EXISTS `word_file` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_fichier` varchar(255) NOT NULL,
  `timestamp_modif` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `word_list`
--

DROP TABLE IF EXISTS `word_list`;
CREATE TABLE IF NOT EXISTS `word_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mot` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `word_occurence_file`
--

DROP TABLE IF EXISTS `word_occurence_file`;
CREATE TABLE IF NOT EXISTS `word_occurence_file` (
  `idWord` int NOT NULL,
  `idFile` int NOT NULL,
  `nb_occurence` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
