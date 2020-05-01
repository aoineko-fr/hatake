-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Ven 01 Mai 2020 à 12:10
-- Version du serveur :  5.7.11
-- Version de PHP :  5.6.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `hatake`
--

-- --------------------------------------------------------

--
-- Structure de la table `@PREFIX@crop`
--

CREATE TABLE `@PREFIX@crop` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL COMMENT 'STRING',
  `variety` int(11) NOT NULL COMMENT 'LINK',
  `loc_type` enum('point','circle','rectangle') COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'ENUM',
  `loc_unit` enum('pixel','coordinate') COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'ENUM',
  `loc_pos` point DEFAULT NULL COMMENT 'POINT',
  `loc_pos2` point DEFAULT NULL COMMENT 'POINT'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `@PREFIX@crop_event`
--

CREATE TABLE `@PREFIX@crop_event` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `crop` int(11) NOT NULL COMMENT 'Crop ID',
  `type` enum('sowing_bucket','sowing_ground','conservation','harvest','plantation','flowering') COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `@PREFIX@project`
--

CREATE TABLE `@PREFIX@project` (
  `id` int(11) NOT NULL,
  `name` char(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lang` enum('en','fr') COLLATE utf8_unicode_ci DEFAULT 'fr',
  `map_image` longblob,
  `map_marker1_pixel` point DEFAULT NULL,
  `map_marker1_coordinate` point DEFAULT NULL,
  `map_marker2_pixel` point DEFAULT NULL,
  `map_marker2_coordinate` point DEFAULT NULL,
  `display_moon_phase` tinyint(1) DEFAULT '0',
  `allow_edit` tinyint(1) DEFAULT '1',
  `allow_dev` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `@PREFIX@project`
--

INSERT INTO `@PREFIX@project` (`id`, `name`, `lang`, `map_image`, `map_marker1_pixel`, `map_marker1_coordinate`, `map_marker2_pixel`, `map_marker2_coordinate`, `display_moon_phase`, `allow_edit`, `allow_dev`) VALUES
(1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `@PREFIX@user`
--

CREATE TABLE `@PREFIX@user` (
  `id` int(11) NOT NULL,
  `login` char(64) COLLATE utf8_unicode_ci NOT NULL,
  `password` char(33) COLLATE utf8_unicode_ci NOT NULL,
  `familyname` char(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `firstname` char(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `birthday` datetime DEFAULT NULL,
  `avatar` longblob,
  `admin` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `@PREFIX@user`
--

INSERT INTO `@PREFIX@user` (`id`, `login`, `password`, `familyname`, `firstname`, `birthday`, `avatar`, `admin`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', NULL, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `@PREFIX@variety`
--

CREATE TABLE `@PREFIX@variety` (
  `id` int(11) NOT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `name_latin` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` enum('tree','shrub','mushroom','climbing','herbaceous','root','trailing') COLLATE utf8_unicode_ci DEFAULT 'tree',
  `image` longblob,
  `exposure` set('fullsun','partsun','partshade','fullshade') COLLATE utf8_unicode_ci DEFAULT 'fullsun',
  `height` point DEFAULT NULL,
  `width` point DEFAULT NULL,
  `emergence` int(11) DEFAULT NULL,
  `hardiness` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `watering` enum('without','little','medium','many') COLLATE utf8_unicode_ci DEFAULT 'medium',
  `ph` point DEFAULT NULL,
  `cycle` enum('annual','biennial','vivace') COLLATE utf8_unicode_ci DEFAULT 'annual',
  `growth` enum('slow','average','fast') COLLATE utf8_unicode_ci DEFAULT 'average',
  `edible_part` set('flower','fruit','root','walnuts','leaf','sap','seed','sprout','young leaf','stem','bulb','whole') COLLATE utf8_unicode_ci DEFAULT NULL,
  `edible_desc` text COLLATE utf8_unicode_ci,
  `edible_color` char(7) COLLATE utf8_unicode_ci DEFAULT NULL,
  `edible_weight` float DEFAULT NULL,
  `edible_size` float DEFAULT NULL,
  `resistance` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sensitivity` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pollinator` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `@PREFIX@variety_period`
--

CREATE TABLE `@PREFIX@variety_period` (
  `id` int(11) NOT NULL,
  `variety` int(11) NOT NULL,
  `type` enum('sowing_bucket','sowing_ground','conservation','harvest','plantation','flowering','pruning') COLLATE utf8_unicode_ci NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `color` char(7) COLLATE utf8_unicode_ci DEFAULT '#A0C0A0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `@PREFIX@crop`
--
ALTER TABLE `@PREFIX@crop`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_2` (`id`);

--
-- Index pour la table `@PREFIX@crop_event`
--
ALTER TABLE `@PREFIX@crop_event`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `crop` (`crop`),
  ADD KEY `id_2` (`id`);

--
-- Index pour la table `@PREFIX@project`
--
ALTER TABLE `@PREFIX@project`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `@PREFIX@user`
--
ALTER TABLE `@PREFIX@user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD KEY `id_2` (`id`);

--
-- Index pour la table `@PREFIX@variety`
--
ALTER TABLE `@PREFIX@variety`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `id_2` (`id`);

--
-- Index pour la table `@PREFIX@variety_period`
--
ALTER TABLE `@PREFIX@variety_period`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `@PREFIX@crop`
--
ALTER TABLE `@PREFIX@crop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- AUTO_INCREMENT pour la table `@PREFIX@crop_event`
--
ALTER TABLE `@PREFIX@crop_event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- AUTO_INCREMENT pour la table `@PREFIX@project`
--
ALTER TABLE `@PREFIX@project`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `@PREFIX@user`
--
ALTER TABLE `@PREFIX@user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pour la table `@PREFIX@variety`
--
ALTER TABLE `@PREFIX@variety`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT pour la table `@PREFIX@variety_period`
--
ALTER TABLE `@PREFIX@variety_period`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
