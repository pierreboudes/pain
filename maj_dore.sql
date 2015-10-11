
-- mauvais default a la creation des tables (fix)
ALTER TABLE pain_categorie DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE pain_listes DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE pain_service DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- augmenter la taille des champs texte
ALTER TABLE  `pain_choix` CHANGE  `choix`  `choix` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `pain_cours` CHANGE  `nom_cours`  `nom_cours` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
DROP TABLE IF EXISTS pain_edt;
ALTER TABLE  `pain_formation` CHANGE  `nom`  `nom` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `pain_formation` CHANGE  `id_enseignant`  `id_enseignant` mediumint(8) unsigned DEFAULT NULL;
ALTER TABLE  `pain_sformation` CHANGE  `nom`  `nom` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

-- pain_formation.annee_etude peut desormais etre NULL
ALTER TABLE  `pain_formation` CHANGE  `annee_etude`  `annee_etude` TINYINT( 3 ) UNSIGNED NULL DEFAULT NULL;
UPDATE pain_formation SET annee_etude = NULL WHERE annee_etude = 0;

-- nouvelles tables
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

CREATE TABLE IF NOT EXISTS pain_annee (
  annee_universitaire YEAR(4)  NOT NULL,
  PRIMARY KEY (annee_universitaire)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO pain_annee (`annee_universitaire`) VALUES (2000), (2001), (2002), (2003), (2004), (2005), (2006), (2007), (2008), (2009), (2010), (2011), (2012), (2013), (2014), (2015), (2016), (2017), (2018), (2019), (2020), (2021), (2022), (2023), (2024), (2025), (2026), (2027), (2028), (2029);

-------- REV 656

-- pain_cours.id_section nouveau
ALTER TABLE  `pain_cours` ADD  `id_section` INT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `code_geisha`;

-- table sectionscnu

CREATE TABLE IF NOT EXISTS `sectionscnu` (
  `id_section` int(3) unsigned NOT NULL DEFAULT '0',
  `intitule_section` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id_section`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `sectionscnu` (`id_section`, `intitule_section`) VALUES
(1, 'Droit privé et sciences criminelles'),
(2, 'Droit public'),
(3, 'Histoire du droit et des institutions'),
(4, 'Science politique'),
(5, 'Sciences économiques'),
(6, 'Sciences de gestion'),
(7, 'Sciences du langage : linguistique et phonétique générales'),
(8, 'Langues et littératures anciennes'),
(9, 'Langue et littérature françaises'),
(10, 'Littératures comparées'),
(11, 'Langues et littératures anglaises et anglo-saxonnes'),
(12, 'Langues et littératures germaniques et scandinaves'),
(13, 'Langues et littératures slaves'),
(14, 'Langues et littératures romanes : espagnol, italien, portugais, autres langues romanes'),
(15, 'Langues et littératures arabes, chinoises, japonaises, hébraique, d''autres domaines linguistiques'),
(16, 'Psychologie, psychologie clinique, psychologie sociale'),
(17, 'Philosophie'),
(18, 'Architecture (ses théories et ses pratiques), arts appliqués, arts plastiques, arts du spectacle, épistémologie des enseignements artistiques, esthétique, musicologie, musique, sciences de l''art'),
(19, 'Sociologie, démographie'),
(20, 'Ethnologie, préhistoire, anthropologie biologique'),
(21, 'Histoire, civilisations, archéologie et art des mondes anciens et médiévaux'),
(22, 'Histoire et civilisations : histoire des mondes modernes, histoire du monde contemporain ; de l''art ; de la musique'),
(23, 'Géographie physique, humaine, économique et régionale'),
(24, 'Aménagement de l''espace, urbanisme'),
(25, 'Mathématiques'),
(26, 'Mathématiques appliquées et applications des mathématiques'),
(27, 'Informatique'),
(28, 'Milieux denses et matériaux'),
(29, 'Constituants élémentaires'),
(30, 'Milieux dilués et optique'),
(31, 'Chimie théorique, physique, analytique'),
(32, 'Chimie organique, minérale, industrielle'),
(33, 'Chimie des matériaux'),
(34, 'Astronomie, astrophysique'),
(35, 'Structure et évolution de la terre et des autres planètes'),
(36, 'Terre solide : géodynamique des enveloppes supérieure, paléobiosphère'),
(37, 'Météorologie, océanographie physique de l''environnement'),
(60, 'Mécanique, génie mécanique, génie civil'),
(61, 'Génie informatique, automatique et traitement du signal'),
(62, 'Energétique, génie des procédés'),
(63, 'Génie électrique, électronique, photonique et systèmes'),
(64, 'Biochimie et biologie moléculaire'),
(65, 'Biologie cellulaire'),
(66, 'Physiologie'),
(67, 'Biologie des populations et écologie'),
(68, 'Biologie des organismes'),
(69, 'Neurosciences'),
(85, 'Personnels enseignants-chercheurs de pharmacie en sciences physico-chimiques et ingénierie appliquée à la santé'),
(86, 'Personnels enseignants-chercheurs de pharmacie en sciences du médicament et des autres produits de santé'),
(87, 'Personnels enseignants-chercheurs de pharmacie en sciences biologiques, fondamentales et cliniques'),
(70, 'Sciences de l''éducation'),
(71, 'Sciences de l''information et de la communication'),
(72, 'Epistémologie, histoire des sciences et des techniques'),
(73, 'Cultures et langues régionales'),
(74, 'Sciences et techniques des activités physiques et sportives'),
(76, 'Théologie catholique'),
(77, 'Théologie protestante');

-- fin rev 670
-- à adapter selon section CNU majoritaire :
-- ALTER TABLE  `pain_cours` CHANGE  `id_section`  `id_section` INT( 3 ) UNSIGNED NOT NULL DEFAULT  '71';
-- UPDATE pain_cours SET id_section = 71 WHERE id_section = 0;

-- rev 742
-- pain_tranche.declarer nouveau
ALTER TABLE  `pain_tranche` ADD  `declarer` text COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `descriptif`;
-- Fin rev 742


--- commit d6f54f09382063df0df75db3610256b0da1c305d
ALTER TABLE  `pain_cours` CHANGE  `code_geisha`  `code_ue` VARCHAR( 16 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
ALTER TABLE  `pain_cours` ADD  `code_etape_cours` VARCHAR( 16 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL  after `code_ue`;
ALTER TABLE  `pain_formation` ADD  `code_etape_formation` VARCHAR( 16 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL  after `id_formation`;
--- fin commit d6f54f09382063df0df75db3610256b0da1c305d

--- commit 2f330ce7105c17d168a80d230db74a239dad7135
CREATE TABLE IF NOT EXISTS `pain_config` (
  `configuration` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `valeur` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `aide` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`configuration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
--- fin commit

---- commit 3eb022307f5db9e53b1307e644b02d5140955dc7
ALTER TABLE `pain_choix` ADD
  `referentiel` double unsigned NOT NULL DEFAULT '0'
  AFTER `alt`;
ALTER TABLE `pain_choix` ADD
   `prp` double unsigned NOT NULL DEFAULT '0'
  AFTER `alt`;
ALTER TABLE `pain_cours` ADD
  `referentiel` double unsigned DEFAULT NULL
  AFTER `alt`;
ALTER TABLE `pain_cours` ADD
   `prp` double unsigned DEFAULT NULL
  AFTER `alt`;
ALTER TABLE `pain_tranche` ADD
  `referentiel` double unsigned DEFAULT NULL
  AFTER `alt`;
ALTER TABLE `pain_tranche` ADD
   `prp` double unsigned DEFAULT NULL
  AFTER `alt`;
--- fin commit


--- commit 64ee2673082e60e3c73ec7ed60b596f1bb265776
ALTER TABLE `pain_enseignant` ADD
        `id_section` int(3) unsigned NOT NULL DEFAULT '0'
  AFTER `categorie`;
ALTER TABLE `pain_service` ADD
        `id_section` int(3) unsigned NOT NULL DEFAULT '0'
  AFTER `categorie`;
--- fin commit


--- commit 30cf90375cd381d6f58933e2d25e20ef6781020d
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
--- fin commit

-- commit 7620893f3212604dc2f94396fe3d8692bba73aab
ALTER TABLE `codesue` ADD `id_codeue` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
--- fin commit
