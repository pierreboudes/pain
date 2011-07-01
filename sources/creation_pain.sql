-- -*- coding: utf-8 -*-
DROP TABLE IF EXISTS `pain_categorie`;
CREATE TABLE IF NOT EXISTS `pain_categorie` (
  `id_categorie` mediumint(9) NOT NULL,
  `nom_court` varchar(40) NOT NULL,
  `nom_long` varchar(80) NOT NULL,
  `descriptif` text NOT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
-- --------------------------------------------------------

--
-- Structure de la table `pain_choix`
--

DROP TABLE IF EXISTS `pain_choix`;
CREATE TABLE IF NOT EXISTS `pain_choix` (
  `id_choix` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_cours` mediumint(8) unsigned NOT NULL,
  `id_enseignant` mediumint(8) unsigned NOT NULL DEFAULT '3',
  `choix` varchar(80) COLLATE utf8_swedish_ci DEFAULT NULL,
  `cm` double unsigned NOT NULL DEFAULT '0',
  `td` double unsigned NOT NULL DEFAULT '0',
  `tp` double unsigned NOT NULL DEFAULT '0',
  `alt` double unsigned NOT NULL DEFAULT '0',
  `htd` double unsigned NOT NULL DEFAULT '0',
  `modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_choix`),
  KEY `id_enseignant` (`id_enseignant`),
  KEY `id_cours` (`id_cours`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pain_cours`
--

DROP TABLE IF EXISTS `pain_cours`;
CREATE TABLE IF NOT EXISTS `pain_cours` (
  `id_cours` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_cours_prev` mediumint(8) unsigned DEFAULT NULL,
  `id_formation` mediumint(8) unsigned NOT NULL,
  `semestre` tinyint(3) unsigned NOT NULL,
  `nom_cours` varchar(80) COLLATE utf8_swedish_ci NOT NULL,
  `credits` tinyint(3) unsigned DEFAULT NULL,
  `id_enseignant` mediumint(8) unsigned NOT NULL,
  `cm` double unsigned DEFAULT NULL,
  `td` double unsigned DEFAULT NULL,
  `tp` double unsigned DEFAULT NULL,
  `alt` double unsigned DEFAULT NULL,
  `descriptif` text COLLATE utf8_swedish_ci,
  `code_geisha` varchar(16) COLLATE utf8_swedish_ci DEFAULT NULL,
  `debut` date NOT NULL DEFAULT '1970-01-01',
  `fin` date NOT NULL DEFAULT '1970-01-01',
  `mcc` text COLLATE utf8_swedish_ci,
  `inscrits` smallint(5) unsigned DEFAULT NULL,
  `presents` smallint(5) unsigned DEFAULT NULL,
  `tirage` smallint(5) unsigned DEFAULT NULL,
  `modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_cours`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pain_enseignant`
--

DROP TABLE IF EXISTS `pain_enseignant`;
CREATE TABLE IF NOT EXISTS `pain_enseignant` (
  `id_enseignant` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(40) COLLATE utf8_swedish_ci DEFAULT NULL,
  `prenom` varchar(40) COLLATE utf8_swedish_ci NOT NULL,
  `nom` varchar(40) COLLATE utf8_swedish_ci NOT NULL,
  `statut` varchar(40) COLLATE utf8_swedish_ci DEFAULT NULL,
  `email` varchar(60) COLLATE utf8_swedish_ci DEFAULT NULL,
  `telephone` varchar(20) COLLATE utf8_swedish_ci DEFAULT NULL,
  `bureau` varchar(40) COLLATE utf8_swedish_ci DEFAULT NULL,
  `service` float unsigned DEFAULT '192',
  `categorie` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `debut` date DEFAULT NULL,
  `fin` date DEFAULT NULL,
  `su` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `stats` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_enseignant`),
  KEY `login` (`login`),
  KEY `login_2` (`login`),
  KEY `login_3` (`login`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pain_formation`
--

DROP TABLE IF EXISTS `pain_formation`;
CREATE TABLE IF NOT EXISTS `pain_formation` (
  `id_formation` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_formation_prev` mediumint(8) unsigned DEFAULT NULL,
  `id_sformation` mediumint(8) unsigned NOT NULL,
  `numero` smallint(5) unsigned NOT NULL,
  `nom` varchar(40) COLLATE utf8_swedish_ci NOT NULL,
  `annee_etude` tinyint(3) unsigned NOT NULL,
  `parfum` varchar(40) COLLATE utf8_swedish_ci DEFAULT NULL,
  `id_enseignant` smallint(5) unsigned DEFAULT NULL,
  `modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_formation`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pain_hist`
--

DROP TABLE IF EXISTS `pain_hist`;
CREATE TABLE IF NOT EXISTS `pain_hist` (
  `id_hist` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL,
  `id` mediumint(8) unsigned NOT NULL,
  `id_formation` mediumint(8) unsigned NOT NULL,
  `id_cours` mediumint(8) unsigned NOT NULL,
  `message` text COLLATE utf8_swedish_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_hist`),
  KEY `timestamp` (`timestamp`),
  KEY `type` (`type`),
  KEY `id` (`id`),
  KEY `id_formation` (`id_formation`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pain_listes`
--

DROP TABLE IF EXISTS `pain_listes`;
CREATE TABLE IF NOT EXISTS `pain_listes` (
  `liste` varchar(60) NOT NULL,
  `id_enseignant` mediumint(9) NOT NULL,
  `email` varchar(60) NOT NULL,
  `tmpnom` varchar(80) DEFAULT NULL,
  `modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`liste`,`id_enseignant`),
  KEY `liste` (`liste`),
  KEY `id_enseignant` (`id_enseignant`),
  KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
-- --------------------------------------------------------

--
-- Structure de la table `pain_service`
--

DROP TABLE IF EXISTS `pain_service`;
CREATE TABLE IF NOT EXISTS `pain_service` (
  `id_enseignant` mediumint(8) unsigned NOT NULL,
  `tmpnom` varchar(80) DEFAULT NULL,
  `annee_universitaire` year(4) NOT NULL,
  `categorie` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `service_annuel` float unsigned NOT NULL DEFAULT '192',
  `service_reel` float DEFAULT NULL,
  PRIMARY KEY (`id_enseignant`,`annee_universitaire`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
-- --------------------------------------------------------

--
-- Structure de la table `pain_sformation`
--

DROP TABLE IF EXISTS `pain_sformation`;
CREATE TABLE IF NOT EXISTS `pain_sformation` (
  `id_sformation` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_sformation_prev` mediumint(8) unsigned DEFAULT NULL,
  `id_enseignant` mediumint(8) unsigned NOT NULL,
  `nom` varchar(40) COLLATE utf8_swedish_ci NOT NULL,
  `annee_universitaire` year(4) DEFAULT NULL,
  `numero` smallint(5) unsigned NOT NULL,
  `modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_sformation`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pain_tranche`
--

DROP TABLE IF EXISTS `pain_tranche`;
CREATE TABLE IF NOT EXISTS `pain_tranche` (
  `id_tranche` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_cours` mediumint(8) unsigned NOT NULL,
  `id_enseignant` mediumint(8) unsigned NOT NULL DEFAULT '3',
  `groupe` tinyint(3) unsigned DEFAULT '0',
  `cm` double unsigned DEFAULT NULL,
  `td` double unsigned DEFAULT NULL,
  `tp` double unsigned DEFAULT NULL,
  `alt` double unsigned DEFAULT NULL,
  `type_conversion` tinyint(3) unsigned DEFAULT NULL,
  `remarque` text COLLATE utf8_swedish_ci,
  `htd` double unsigned DEFAULT NULL,
  `descriptif` text COLLATE utf8_swedish_ci,
  `modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_tranche`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;


