-- -*- coding: utf-8 -*-
USE `pain_demo`;

--
-- Table structure for table `pain_annee`
--

DROP TABLE IF EXISTS `pain_annee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pain_annee` (
  `annee_universitaire` year(4) NOT NULL,
  PRIMARY KEY (`annee_universitaire`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `pain_categorie`
--

DROP TABLE IF EXISTS `pain_categorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pain_categorie` (
  `id_categorie` mediumint(9) NOT NULL,
  `nom_court` varchar(40) NOT NULL,
  `nom_long` varchar(80) NOT NULL,
  `descriptif` text NOT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pain_choix`
--

DROP TABLE IF EXISTS `pain_choix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pain_choix` (
  `id_choix` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_cours` mediumint(8) unsigned NOT NULL,
  `id_enseignant` mediumint(8) unsigned NOT NULL DEFAULT 3,
  `choix` varchar(256) DEFAULT NULL,
  `cm` double unsigned NOT NULL DEFAULT 0,
  `td` double unsigned NOT NULL DEFAULT 0,
  `tp` double unsigned NOT NULL DEFAULT 0,
  `alt` double unsigned NOT NULL DEFAULT 0,
  `prp` double unsigned NOT NULL DEFAULT 0,
  `referentiel` double unsigned NOT NULL DEFAULT 0,
  `htd` double unsigned NOT NULL DEFAULT 0,
  `modification` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_choix`),
  KEY `id_enseignant` (`id_enseignant`),
  KEY `id_cours` (`id_cours`)
) ENGINE=MyISAM AUTO_INCREMENT=179 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pain_collection`
--

DROP TABLE IF EXISTS `pain_collection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pain_collection` (
  `id_collection` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_collection_prev` mediumint(8) unsigned DEFAULT NULL,
  `id_sformation` mediumint(8) unsigned DEFAULT NULL,
  `annee_universitaire` year(4) DEFAULT NULL,
  `nom_collection` varchar(128) NOT NULL,
  `descriptif` text DEFAULT NULL,
  `modification` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_collection`),
  KEY `id_collection_prev` (`id_collection_prev`),
  KEY `nom_collection` (`nom_collection`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `pain_collectionscours`
--

DROP TABLE IF EXISTS `pain_collectionscours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pain_collectionscours` (
  `id_cours` mediumint(8) unsigned NOT NULL,
  `id_collection` mediumint(8) unsigned NOT NULL,
  `modification` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_cours`,`id_collection`),
  KEY `id_cours` (`id_cours`),
  KEY `id_collection` (`id_collection`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `pain_config`
--

DROP TABLE IF EXISTS `pain_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pain_config` (
  `configuration` varchar(32) NOT NULL,
  `valeur` varchar(32) NOT NULL,
  `aide` text NOT NULL,
  PRIMARY KEY (`configuration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pain_cours`
--

DROP TABLE IF EXISTS `pain_cours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pain_cours` (
  `id_cours` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_cours_prev` mediumint(8) unsigned DEFAULT NULL,
  `id_formation` mediumint(8) unsigned NOT NULL,
  `semestre` tinyint(3) unsigned NOT NULL,
  `nom_cours` varchar(256) NOT NULL,
  `credits` tinyint(3) unsigned DEFAULT NULL,
  `id_enseignant` mediumint(8) unsigned NOT NULL,
  `cm` double unsigned DEFAULT NULL,
  `td` double unsigned DEFAULT NULL,
  `tp` double unsigned DEFAULT NULL,
  `alt` double unsigned DEFAULT NULL,
  `prp` double unsigned DEFAULT NULL,
  `referentiel` double unsigned DEFAULT NULL,
  `descriptif` text DEFAULT NULL,
  `code_ue` varchar(16) DEFAULT NULL,
  `code_etape_cours` varchar(16) DEFAULT NULL,
  `id_section` int(3) unsigned NOT NULL DEFAULT 0,
  `debut` date NOT NULL DEFAULT '1970-01-01',
  `fin` date NOT NULL DEFAULT '1970-01-01',
  `mcc` text DEFAULT NULL,
  `inscrits` smallint(5) unsigned DEFAULT NULL,
  `presents` smallint(5) unsigned DEFAULT NULL,
  `tirage` smallint(5) unsigned DEFAULT NULL,
  `modification` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_cours`),
  KEY `id_cours_prev` (`id_cours_prev`),
  KEY `id_formation` (`id_formation`),
  KEY `id_enseignant` (`id_enseignant`)
) ENGINE=MyISAM AUTO_INCREMENT=3074 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `pain_enseignant`
--

DROP TABLE IF EXISTS `pain_enseignant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pain_enseignant` (
  `id_enseignant` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(40) DEFAULT NULL,
  `prenom` varchar(40) NOT NULL,
  `nom` varchar(40) NOT NULL,
  `statut` varchar(40) DEFAULT NULL,
  `email` varchar(60) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `bureau` varchar(40) DEFAULT NULL,
  `service` float unsigned DEFAULT 192,
  `categorie` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `id_section` int(3) unsigned NOT NULL DEFAULT 0,
  `debut` date DEFAULT NULL,
  `fin` date DEFAULT NULL,
  `su` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `stats` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `modification` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_enseignant`),
  KEY `login` (`login`),
  KEY `nom` (`nom`),
  KEY `categorie` (`categorie`)
) ENGINE=MyISAM AUTO_INCREMENT=337 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `pain_etapes_annees`
--

DROP TABLE IF EXISTS `pain_etapes_annees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pain_etapes_annees` (
  `effectif` int(4) DEFAULT NULL,
  `ANNEE_INSCRIPTION` int(4) DEFAULT NULL,
  `CODE_ETAPE` varchar(8) DEFAULT NULL,
  `LIBELLE_LONG_ETAPE` varchar(125) DEFAULT NULL,
  `LIBELLE_COURT_COMPOSANTE` varchar(9) DEFAULT NULL,
  UNIQUE KEY `ANNEE` (`ANNEE_INSCRIPTION`,`CODE_ETAPE`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `pain_etapescours`
--

DROP TABLE IF EXISTS `pain_etapescours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pain_etapescours` (
  `id_cours` mediumint(8) unsigned NOT NULL,
  `code_etape` varchar(8) NOT NULL DEFAULT '',
  `modification` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_cours`,`code_etape`),
  KEY `id_cours` (`id_cours`),
  KEY `id_etape` (`code_etape`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `pain_etapesformations`
--

DROP TABLE IF EXISTS `pain_etapesformations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pain_etapesformations` (
  `id_formation` mediumint(8) unsigned NOT NULL,
  `code_etape` varchar(8) NOT NULL DEFAULT '',
  `modification` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_formation`,`code_etape`),
  KEY `id_formation` (`id_formation`),
  KEY `id_etape` (`code_etape`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `pain_formation`
--

DROP TABLE IF EXISTS `pain_formation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pain_formation` (
  `id_formation` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `code_etape_formation` varchar(16) DEFAULT NULL,
  `id_formation_prev` mediumint(8) unsigned DEFAULT NULL,
  `id_sformation` mediumint(8) unsigned NOT NULL,
  `numero` smallint(5) unsigned NOT NULL,
  `nom` varchar(256) NOT NULL,
  `annee_etude` tinyint(3) unsigned DEFAULT NULL,
  `parfum` varchar(40) NOT NULL,
  `id_enseignant` mediumint(8) unsigned DEFAULT NULL,
  `modification` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_formation`),
  KEY `id_sformation` (`id_sformation`),
  KEY `id_formation_prev` (`id_formation_prev`),
  KEY `numero` (`numero`),
  KEY `id_enseignant` (`id_enseignant`)
) ENGINE=MyISAM AUTO_INCREMENT=368 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `pain_hist`
--

DROP TABLE IF EXISTS `pain_hist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pain_hist` (
  `id_hist` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL,
  `id` mediumint(8) unsigned NOT NULL,
  `id_formation` mediumint(8) unsigned NOT NULL,
  `id_cours` mediumint(8) unsigned NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_hist`),
  KEY `timestamp` (`timestamp`),
  KEY `type` (`type`),
  KEY `id` (`id`),
  KEY `id_formation` (`id_formation`)
) ENGINE=MyISAM AUTO_INCREMENT=27618 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pain_listes`
--

DROP TABLE IF EXISTS `pain_listes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pain_listes` (
  `liste` varchar(60) NOT NULL,
  `id_enseignant` mediumint(9) NOT NULL,
  `email` varchar(60) NOT NULL,
  `modification` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`liste`,`id_enseignant`),
  KEY `liste` (`liste`),
  KEY `id_enseignant` (`id_enseignant`),
  KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `pain_service`
--

SET character_set_server = 'utf8mb4';
SET collation_server = 'utf8mb4_unicode_ci';

DROP TABLE IF EXISTS `pain_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pain_service` (
  `id_enseignant` mediumint(8) unsigned NOT NULL,
  `tmpnom` varchar(80) DEFAULT NULL,
  `annee_universitaire` year(4) NOT NULL,
  `categorie` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `id_section` int(3) unsigned NOT NULL DEFAULT 0,
  `service_annuel` float unsigned NOT NULL DEFAULT 192,
  `service_reel` float DEFAULT NULL,
  `service_potentiel` float DEFAULT NULL,
  `modification` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_enseignant`,`annee_universitaire`),
  KEY `id_enseignant` (`id_enseignant`),
  KEY `annee_universitaire` (`annee_universitaire`),
  KEY `categorie` (`categorie`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `pain_sformation`
--

DROP TABLE IF EXISTS `pain_sformation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pain_sformation` (
  `id_sformation` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_sformation_prev` mediumint(8) unsigned DEFAULT NULL,
  `id_enseignant` mediumint(8) unsigned NOT NULL,
  `nom` varchar(256) NOT NULL,
  `annee_universitaire` year(4) DEFAULT NULL,
  `numero` smallint(5) unsigned NOT NULL,
  `modification` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_sformation`),
  KEY `id_sformation_prev` (`id_sformation_prev`),
  KEY `id_enseignant` (`id_enseignant`),
  KEY `annee_universitaire` (`annee_universitaire`)
) ENGINE=MyISAM AUTO_INCREMENT=110 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `pain_tag`
--

DROP TABLE IF EXISTS `pain_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pain_tag` (
  `id_tag` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `nom_tag` varchar(80) NOT NULL,
  `descriptif` text DEFAULT NULL,
  `modification` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_tag`),
  KEY `nom_tag` (`nom_tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pain_tagscours`
--

DROP TABLE IF EXISTS `pain_tagscours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pain_tagscours` (
  `id_cours` mediumint(8) unsigned NOT NULL,
  `id_tag` mediumint(8) unsigned NOT NULL,
  `modification` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_cours`,`id_tag`),
  KEY `id_cours` (`id_cours`),
  KEY `id_tag` (`id_tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `pain_tranche`
--

DROP TABLE IF EXISTS `pain_tranche`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pain_tranche` (
  `id_tranche` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_cours` mediumint(8) unsigned NOT NULL,
  `id_enseignant` mediumint(8) unsigned NOT NULL DEFAULT 3,
  `groupe` tinyint(3) unsigned DEFAULT 0,
  `cm` double unsigned DEFAULT NULL,
  `td` double unsigned DEFAULT NULL,
  `tp` double unsigned DEFAULT NULL,
  `alt` double unsigned DEFAULT NULL,
  `prp` double unsigned DEFAULT NULL,
  `referentiel` double unsigned DEFAULT NULL,
  `type_conversion` tinyint(3) unsigned DEFAULT NULL,
  `remarque` text DEFAULT NULL,
  `htd` double unsigned DEFAULT NULL,
  `descriptif` text DEFAULT NULL,
  `declarer` text DEFAULT NULL,
  `modification` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_tranche`),
  KEY `id_cours` (`id_cours`),
  KEY `id_enseignant` (`id_enseignant`)
) ENGINE=MyISAM AUTO_INCREMENT=9987 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pain_validation_cours`
--

DROP TABLE IF EXISTS `pain_validation_cours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pain_validation_cours` (
  `id_cours` int(10) unsigned NOT NULL,
  `id_formation` int(10) unsigned NOT NULL,
  `valide` tinyint(1) NOT NULL,
  `commentaire_validation` varchar(256) NOT NULL,
  `modification` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_cours`),
  KEY `id_formation` (`id_formation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `pain_validation_tranche`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pain_validation_tranche` (
  `id_tranche` int(10) unsigned NOT NULL,
  `id_cours` int(10) unsigned NOT NULL,
  `valide` tinyint(1) NOT NULL,
  `commentaire_validation` varchar(256) NOT NULL,
  `modification` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_tranche`),
  KEY `id_cours` (`id_cours`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sectionscnu`
--

DROP TABLE IF EXISTS `sectionscnu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sectionscnu` (
  `id_section` int(3) unsigned NOT NULL DEFAULT 0,
  `intitule_section` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id_section`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


CREATE DATABASE `commun`;

USE `commun`;

CREATE TABLE `codesue` (
  `id_codeue` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code_annee` varchar(32) DEFAULT NULL,
  `nom_annee` varchar(128) DEFAULT NULL,
  `intitule_cours` varchar(128) DEFAULT NULL,
  `code_ue` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id_codeue`),
  KEY `code_ue` (`code_ue`)
) ENGINE=InnoDB AUTO_INCREMENT=996 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

GRANT ALL PRIVILEGES ON commun.* TO 'pain_demo'@'%';
 FLUSH PRIVILEGES;
