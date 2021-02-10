-- -*- coding: utf-8 -*-
SET character_set_server = 'utf8mb4';
SET collation_server = 'utf8mb4_unicode_ci';

USE pain_demo;

/*!40000 ALTER TABLE `pain_categorie` DISABLE KEYS */;
INSERT INTO `pain_categorie` VALUES (0,'En attente','En attente de catégorie',''),(1,'Annulés','',''),(2,'Permanents','Enseignants permanents','Les permanents du département'),(3,'Non-permanents','Enseignants non-permanents','Les ATER, moniteurs, etc. du département '),(4,'Galilée','Autres enseignants de l\'institut Galilée','Autres enseignants de l\'institut Galilée (permanents ou non-permanents).'),(5,'Autres','Autres (vacataires, industriels, etc.)','Autres intervenants, enseignants vacataires, personnels non-enseignants de l\'université, industriels, etc.'),(6,'Paris 13','Autres enseignants de l\'université Paris 13','Autres enseignants de l\'université Paris 13 (permanents ou non-permanents).'),(10,'Anciens','Anciens enseignants','Anciens enseignants du département, anciens intervenants extérieurs'),(22,'Aidés','',''),(23,'Vacants','',''),(29,'Inconnu','','');
/*!40000 ALTER TABLE `pain_categorie` ENABLE KEYS */;



INSERT INTO `pain_enseignant` (`id_enseignant`, `login`, `prenom`, `nom`, `statut`, `email`, `telephone`, `bureau`, `service`, `categorie`, `debut`, `fin`, `su`, `stats`) VALUES(1, NULL, 'annulé', '', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 0, 0);
INSERT INTO `pain_enseignant` (`id_enseignant`, `login`, `prenom`, `nom`, `statut`, `email`, `telephone`, `bureau`, `service`, `categorie`, `debut`, `fin`, `su`, `stats`) VALUES(2, NULL, 'mutualisé', '', NULL, NULL, NULL, NULL, NULL, 22, NULL, NULL, 0, 0);
INSERT INTO `pain_enseignant` (`id_enseignant`, `login`, `prenom`, `nom`, `statut`, `email`, `telephone`, `bureau`, `service`, `categorie`, `debut`, `fin`, `su`, `stats`) VALUES(3, NULL, 'libre', '', NULL, NULL, NULL, NULL, NULL, 23, NULL, NULL, 0, 0);
INSERT INTO `pain_enseignant` (`id_enseignant`, `login`, `prenom`, `nom`, `statut`, `email`, `telephone`, `bureau`, `service`, `categorie`, `debut`, `fin`, `su`, `stats`) VALUES(9, NULL, 'autre', '', NULL, NULL, NULL, NULL, NULL, 29, NULL, NULL, 0, 0);
/* Un⋅e enseigant démo sera admin */
INSERT INTO `pain_enseignant` (`id_enseignant`, `login`, `prenom`, `nom`, `statut`, `email`, `telephone`, `bureau`, `service`, `categorie`, `debut`, `fin`, `su`, `stats`) VALUES(10, 'demo', 'Alma', 'Desmeaux', 'prof', 'demo@perdu.com', '', '', 192, 2, NULL, NULL, 1, 1);


INSERT INTO pain_annee (`annee_universitaire`) VALUES (2000), (2001), (2002), (2003), (2004), (2005), (2006), (2007), (2008), (2009), (2010), (2011), (2012), (2013), (2014), (2015), (2016), (2017), (2018), (2019), (2020), (2021), (2022), (2023), (2024), (2025), (2026), (2027), (2028), (2029);

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

/* Donnons un exemple de formation */
INSERT INTO `pain_sformation` VALUES (1,NULL,10,'Licence',2021,1,'2021-01-01 15:59:18');
INSERT INTO `pain_formation` VALUES (1,NULL,NULL,1,0,'Licence',1,'',10,'2021-01-01 15:59:19');
/* les services 2021 */
INSERT INTO `pain_service` VALUES (1,NULL,2021,1,0,0,0,NULL,'2021-01-01 21:41:04'), (2,NULL,2021,1,0,0,0,NULL,'2021-01-01 21:41:04'), (3,NULL,2021,1,0,0,0,NULL,'2021-01-01 21:41:04'), (9,NULL,2021,1,0,0,0,NULL,'2021-01-01 21:41:04'), (10,NULL,2021,2,27,192,0,NULL,'2021-01-01 21:41:04');
