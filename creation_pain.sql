-- -*- coding: utf-8 -*-

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS pain_annee (
  annee_universitaire YEAR(4) NOT NULL,
  PRIMARY KEY (annee_universitaire)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS pain_categorie (
  id_categorie mediumint(9) NOT NULL,
  nom_court varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  nom_long varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  descriptif text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (id_categorie)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS pain_choix (
  id_choix mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  id_cours mediumint(8) unsigned NOT NULL,
  id_enseignant mediumint(8) unsigned NOT NULL DEFAULT '3',
  choix varchar(256) COLLATE utf8_general_ci DEFAULT NULL,
  cm double unsigned NOT NULL DEFAULT '0',
  td double unsigned NOT NULL DEFAULT '0',
  tp double unsigned NOT NULL DEFAULT '0',
  alt double unsigned NOT NULL DEFAULT '0',
  prp double unsigned NOT NULL DEFAULT '0',
  referentiel double unsigned NOT NULL DEFAULT '0',
  htd double unsigned NOT NULL DEFAULT '0',
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_choix),
  KEY id_enseignant (id_enseignant),
  KEY id_cours (id_cours)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS pain_cours (
  id_cours mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  id_cours_prev mediumint(8) unsigned DEFAULT NULL,
  id_formation mediumint(8) unsigned NOT NULL,
  semestre tinyint(3) unsigned NOT NULL,
  nom_cours varchar(256) COLLATE utf8_general_ci NOT NULL,
  credits tinyint(3) unsigned DEFAULT NULL,
  id_enseignant mediumint(8) unsigned NOT NULL,
  cm double unsigned DEFAULT NULL,
  td double unsigned DEFAULT NULL,
  tp double unsigned DEFAULT NULL,
  alt double unsigned DEFAULT NULL,
  prp double unsigned DEFAULT NULL,
  referentiel double unsigned DEFAULT NULL,
  descriptif text COLLATE utf8_general_ci,
  code_geisha varchar(16) COLLATE utf8_general_ci DEFAULT NULL,
  id_section int(3) unsigned NOT NULL DEFAULT '25',
  debut date NOT NULL DEFAULT '1970-01-01',
  fin date NOT NULL DEFAULT '1970-01-01',
  mcc text COLLATE utf8_general_ci,
  inscrits smallint(5) unsigned DEFAULT NULL,
  presents smallint(5) unsigned DEFAULT NULL,
  tirage smallint(5) unsigned DEFAULT NULL,
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_cours),
  KEY id_cours_prev (id_cours_prev),
  KEY id_formation (id_formation),
  KEY id_enseignant (id_enseignant)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;






CREATE TABLE IF NOT EXISTS pain_enseignant (
  id_enseignant mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  login varchar(40) COLLATE utf8_general_ci DEFAULT NULL,
  prenom varchar(40) COLLATE utf8_general_ci NOT NULL,
  nom varchar(40) COLLATE utf8_general_ci NOT NULL,
  statut varchar(40) COLLATE utf8_general_ci DEFAULT NULL,
  email varchar(60) COLLATE utf8_general_ci DEFAULT NULL,
  telephone varchar(20) COLLATE utf8_general_ci DEFAULT NULL,
  bureau varchar(40) COLLATE utf8_general_ci DEFAULT NULL,
  service float unsigned DEFAULT '192',
  categorie tinyint(3) unsigned NOT NULL DEFAULT '0',
  id_section int(3) unsigned NOT NULL DEFAULT '0',
  debut date DEFAULT NULL,
  fin date DEFAULT NULL,
  su tinyint(3) unsigned NOT NULL DEFAULT '0',
  stats tinyint(3) unsigned NOT NULL DEFAULT '0',
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_enseignant),
  KEY login (login),
  KEY nom (nom),
  KEY categorie (categorie)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS pain_formation (
  id_formation mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  id_formation_prev mediumint(8) unsigned DEFAULT NULL,
  id_sformation mediumint(8) unsigned NOT NULL,
  numero smallint(5) unsigned NOT NULL,
  nom varchar(256) COLLATE utf8_general_ci NOT NULL,
  annee_etude tinyint(3) unsigned NOT NULL,
  parfum varchar(40) COLLATE utf8_general_ci NOT NULL,
  id_enseignant mediumint(8) unsigned DEFAULT NULL,
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_formation),
  KEY id_sformation (id_sformation),
  KEY id_formation_prev (id_formation_prev),
  KEY numero (numero),
  KEY id_enseignant (id_enseignant)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS pain_hist (
  id_hist int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL,
  id mediumint(8) unsigned NOT NULL,
  id_formation mediumint(8) unsigned NOT NULL,
  id_cours mediumint(8) unsigned NOT NULL,
  message text COLLATE utf8_general_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_hist),
  KEY `timestamp` (`timestamp`),
  KEY `type` (`type`),
  KEY id (id),
  KEY id_formation (id_formation)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS pain_listes (
  liste varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  id_enseignant mediumint(9) NOT NULL,
  email varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (liste,id_enseignant),
  KEY liste (liste),
  KEY id_enseignant (id_enseignant),
  KEY email (email)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS pain_service (
  id_enseignant mediumint(8) unsigned NOT NULL,
  tmpnom varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  annee_universitaire year(4) NOT NULL,
  categorie tinyint(3) unsigned NOT NULL DEFAULT '0',
  id_section int(3) unsigned NOT NULL DEFAULT '0',
  service_annuel float unsigned NOT NULL DEFAULT '192',
  service_reel float DEFAULT NULL,
  service_potentiel float DEFAULT NULL,
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_enseignant,annee_universitaire),
  KEY id_enseignant (id_enseignant),
  KEY annee_universitaire (annee_universitaire),
  KEY categorie (categorie)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS pain_sformation (
  id_sformation mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  id_sformation_prev mediumint(8) unsigned DEFAULT NULL,
  id_enseignant mediumint(8) unsigned NOT NULL,
  nom varchar(256) COLLATE utf8_general_ci NOT NULL,
  annee_universitaire year(4) DEFAULT NULL,
  numero smallint(5) unsigned NOT NULL,
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_sformation),
  KEY id_sformation_prev (id_sformation_prev),
  KEY id_enseignant (id_enseignant),
  KEY annee_universitaire (annee_universitaire)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS pain_tranche (
  id_tranche mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  id_cours mediumint(8) unsigned NOT NULL,
  id_enseignant mediumint(8) unsigned NOT NULL DEFAULT '3',
  groupe tinyint(3) unsigned DEFAULT '0',
  cm double unsigned DEFAULT NULL,
  td double unsigned DEFAULT NULL,
  tp double unsigned DEFAULT NULL,
  alt double unsigned DEFAULT NULL,
  prp double unsigned DEFAULT NULL,
  referentiel double unsigned DEFAULT NULL,
  type_conversion tinyint(3) unsigned DEFAULT NULL,
  remarque text COLLATE utf8_general_ci,
  htd double unsigned DEFAULT NULL,
  descriptif text COLLATE utf8_general_ci,
  declarer text COLLATE utf8_general_ci NOT NULL DEFAULT '',
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_tranche),
  KEY id_cours (id_cours),
  KEY id_enseignant (id_enseignant)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS pain_tag (
  id_tag mediumint(8) unsigned  NOT NULL AUTO_INCREMENT,
  nom_tag varchar(80) COLLATE utf8_general_ci NOT NULL,
  descriptif text COLLATE utf8_general_ci,
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_tag),
  KEY nom_tag (nom_tag)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS pain_tagscours (
  id_cours mediumint(8) unsigned NOT NULL,
  id_tag mediumint(8) unsigned NOT NULL,
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_cours, id_tag),
  KEY id_cours (id_cours),
  KEY id_tag (id_tag)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS pain_collection (
  id_collection mediumint(8) unsigned  NOT NULL AUTO_INCREMENT,
  id_collection_prev mediumint(8) unsigned NULL DEFAULT NULL,
  id_sformation mediumint(8) unsigned DEFAULT NULL,
  annee_universitaire YEAR(4) NULL DEFAULT NULL,
  nom_collection varchar(128) COLLATE utf8_general_ci NOT NULL,
  descriptif text COLLATE utf8_general_ci,
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_collection),
  KEY id_collection_prev (id_collection_prev),
  KEY nom_collection (nom_collection)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS pain_collectionscours (
  id_cours mediumint(8) unsigned NOT NULL,
  id_collection mediumint(8) unsigned NOT NULL,
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_cours, id_collection),
  KEY id_cours (id_cours),
  KEY id_collection (id_collection)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `sectionscnu` (
  `id_section` int(3) unsigned NOT NULL DEFAULT '0',
  `intitule_section` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id_section`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `pain_validation_cours` (
  `id_cours` int(10) unsigned NOT NULL,
  `id_formation` int(10) unsigned NOT NULL,
  `valide` tinyint(1) NOT NULL,
  `commentaire_validation` varchar(256) NOT NULL,
  `modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY id_cours (id_cours),
  KEY id_formation (id_formation)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `pain_validation_tranche` (
  `id_tranche` int(10) unsigned NOT NULL,
  `id_cours` int(10) unsigned NOT NULL,
  `valide` tinyint(1) NOT NULL,
  `commentaire_validation` varchar(256) NOT NULL,
  `modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY id_tranche (id_tranche),
  KEY id_cours (id_cours)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
