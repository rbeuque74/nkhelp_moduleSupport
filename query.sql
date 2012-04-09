-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le : Lun 09 Avril 2012 à 14:03
-- Version du serveur: 5.5.20
-- Version de PHP: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `nk179rc6`
--

-- --------------------------------------------------------

--
-- Structure de la table `nk_support_cat`
--

DROP TABLE IF EXISTS `nk_support_cat`;
CREATE TABLE `nk_support_cat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `ordre` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=2 ;

--
-- Contenu de la table `nk_support_cat`
--

INSERT INTO `nk_support_cat` (`id`, `nom`, `ordre`) VALUES
(1, 'Général', 1);

-- --------------------------------------------------------

--
-- Structure de la table `nk_support_messages`
--

DROP TABLE IF EXISTS `nk_support_messages`;
CREATE TABLE `nk_support_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `texte` text COLLATE latin1_general_ci NOT NULL,
  `date` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `auteur` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `auteur_id` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `auteur_ip` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `thread_id` int(10) unsigned NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nk_support_threads`
--

DROP TABLE IF EXISTS `nk_support_threads`;
CREATE TABLE `nk_support_threads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `date` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `closed` tinyint(1) NOT NULL DEFAULT '0',
  `auteur` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `auteur_id` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `cat_id` int(10) unsigned NOT NULL,
  `notify` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
