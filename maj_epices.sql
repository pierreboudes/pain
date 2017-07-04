DROP TABLE IF EXISTS `pain_etapescours`;
CREATE TABLE `pain_etapescours` (
  `id_cours` mediumint(8) UNSIGNED NOT NULL,
  `code_etape` varchar(8) DEFAULT NULL,
  `modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_cours, code_etape),
  KEY id_cours (id_cours),
  KEY id_etape (code_etape)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pain_etapesformations`;
CREATE TABLE `pain_etapesformations` (
  `id_formation` mediumint(8) UNSIGNED NOT NULL,
  `code_etape` varchar(8) DEFAULT NULL,
  `modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_formation, code_etape),
  KEY id_formation (id_formation),
  KEY id_etape (code_etape)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pain_etapes_annees`;
CREATE TABLE `pain_etapes_annees` (
  `NB` int(4) DEFAULT NULL,
  `ANNEE_INSCRIPTION` int(4) DEFAULT NULL,
  `CODE_ETAPE` varchar(8) DEFAULT NULL,
  `LIBELLE_LONG_ETAPE` varchar(125) DEFAULT NULL,
  `LIBELLE_COURT_COMPOSANTE` varchar(9) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `pain_etapes_annees`
 ADD UNIQUE KEY (`ANNEE_INSCRIPTION`,`CODE_ETAPE`);
