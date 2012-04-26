-- mauvais default a la creation des tables (fix)
ALTER TABLE pain_categorie DEFAULT CHARACTER SET utf8 COLLATE utf8_swedish_ci;
ALTER TABLE pain_listes DEFAULT CHARACTER SET utf8 COLLATE utf8_swedish_ci;
ALTER TABLE pain_service DEFAULT CHARACTER SET utf8 COLLATE utf8_swedish_ci;

-- augmenter la taille des champs texte
ALTER TABLE  `pain_choix` CHANGE  `choix`  `choix` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NULL DEFAULT NULL;
ALTER TABLE  `pain_cours` CHANGE  `nom_cours`  `nom_cours` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL;
DROP TABLE IF EXISTS pain_edt;
ALTER TABLE  `pain_formation` CHANGE  `nom`  `nom` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL;
ALTER TABLE  `pain_sformation` CHANGE  `nom`  `nom` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL;

-- pain_formation.annee_etude peut desormais etre NULL
ALTER TABLE  `pain_formation` CHANGE  `annee_etude`  `annee_etude` TINYINT( 3 ) UNSIGNED NULL DEFAULT NULL;
UPDATE pain_formation SET annee_etude = NULL WHERE annee_etude = 0;


-- nouvelles tables
CREATE TABLE IF NOT EXISTS pain_tag (

  id_tag mediumint(8) unsigned  NOT NULL AUTO_INCREMENT,
  nom_tag varchar(80) COLLATE utf8_swedish_ci NOT NULL,
  descriptif text COLLATE utf8_swedish_ci,
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_tag),
  KEY nom_tag (nom_tag)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;


CREATE TABLE IF NOT EXISTS pain_tagscours (
  id_cours mediumint(8) unsigned NOT NULL,
  id_tag mediumint(8) unsigned NOT NULL,
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_cours, id_tag),
  KEY id_cours (id_cours),
  KEY id_tag (id_tag)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;


CREATE TABLE IF NOT EXISTS pain_collection (
  id_collection mediumint(8) unsigned  NOT NULL AUTO_INCREMENT,
  id_collection_prev mediumint(8) unsigned NULL DEFAULT NULL,
  id_sformation mediumint(8) unsigned DEFAULT NULL,
  annee_universitaire YEAR(4) NULL DEFAULT NULL,
  nom_collection varchar(128) COLLATE utf8_swedish_ci NOT NULL,
  descriptif text COLLATE utf8_swedish_ci,
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_collection),
  KEY id_collection_prev (id_collection_prev)
  KEY nom_collection (nom_collection)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE IF NOT EXISTS pain_collectionscours (
  id_cours mediumint(8) unsigned NOT NULL,
  id_collection mediumint(8) unsigned NOT NULL,
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_cours, id_collection),
  KEY id_cours (id_cours),
  KEY id_collection (id_collection)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
