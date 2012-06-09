-- -*- coding: utf-8 -*-
INSERT INTO `pain_categorie` (`id_categorie`, `nom_court`, `nom_long`, `descriptif`) VALUES(0, 'En attente', 'En attente de catégorie', '');
INSERT INTO `pain_categorie` (`id_categorie`, `nom_court`, `nom_long`, `descriptif`) VALUES(1, 'Annulés', '', '');
INSERT INTO `pain_categorie` (`id_categorie`, `nom_court`, `nom_long`, `descriptif`) VALUES(2, 'Permanents', 'Enseignants permanents', 'Les permanents du département');
INSERT INTO `pain_categorie` (`id_categorie`, `nom_court`, `nom_long`, `descriptif`) VALUES(3, 'Non-permanents', 'Enseignants non-permanents', 'Les ATER, moniteurs, etc. du département ');
INSERT INTO `pain_categorie` (`id_categorie`, `nom_court`, `nom_long`, `descriptif`) VALUES(4, 'Galilée', 'Autres enseignants de l''institut Galilée', 'Autres enseignants de l''institut Galilée (permanents ou non-permanents).');
INSERT INTO `pain_categorie` (`id_categorie`, `nom_court`, `nom_long`, `descriptif`) VALUES(5, 'Autres', 'Autres (vacataires, industriels, etc.)', 'Autres intervenants, enseignants vacataires, personnels non-enseignants de l''université, industriels, etc.');
INSERT INTO `pain_categorie` (`id_categorie`, `nom_court`, `nom_long`, `descriptif`) VALUES(6, 'Paris 13', 'Autres enseignants de l''université Paris 13', 'Autres enseignants de l''université Paris 13 (permanents ou non-permanents).');
INSERT INTO `pain_categorie` (`id_categorie`, `nom_court`, `nom_long`, `descriptif`) VALUES(10, 'Anciens', 'Anciens enseignants', 'Anciens enseignants du département, anciens intervenants extérieurs');
INSERT INTO `pain_categorie` (`id_categorie`, `nom_court`, `nom_long`, `descriptif`) VALUES(22, 'Aidés', '', '');
INSERT INTO `pain_categorie` (`id_categorie`, `nom_court`, `nom_long`, `descriptif`) VALUES(23, 'Vacants', '', '');
INSERT INTO `pain_categorie` (`id_categorie`, `nom_court`, `nom_long`, `descriptif`) VALUES(29, 'Inconnu', '', '');


INSERT INTO `pain_enseignant` (`id_enseignant`, `login`, `prenom`, `nom`, `statut`, `email`, `telephone`, `bureau`, `service`, `categorie`, `debut`, `fin`, `su`, `stats`) VALUES(1, NULL, 'annulé', '', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 0, 0);
INSERT INTO `pain_enseignant` (`id_enseignant`, `login`, `prenom`, `nom`, `statut`, `email`, `telephone`, `bureau`, `service`, `categorie`, `debut`, `fin`, `su`, `stats`) VALUES(2, NULL, 'mutualisé', '', NULL, NULL, NULL, NULL, NULL, 22, NULL, NULL, 0, 0);
INSERT INTO `pain_enseignant` (`id_enseignant`, `login`, `prenom`, `nom`, `statut`, `email`, `telephone`, `bureau`, `service`, `categorie`, `debut`, `fin`, `su`, `stats`) VALUES(3, NULL, 'libre', '', NULL, NULL, NULL, NULL, NULL, 23, NULL, NULL, 0, 0);
INSERT INTO `pain_enseignant` (`id_enseignant`, `login`, `prenom`, `nom`, `statut`, `email`, `telephone`, `bureau`, `service`, `categorie`, `debut`, `fin`, `su`, `stats`) VALUES(9, NULL, 'autre', '', NULL, NULL, NULL, NULL, NULL, 29, NULL, NULL, 0, 0);
INSERT INTO `pain_enseignant` (`id_enseignant`, `login`, `prenom`, `nom`, `statut`, `email`, `telephone`, `bureau`, `service`, `categorie`, `debut`, `fin`, `su`, `stats`) VALUES(10, 'boudes', 'PRENONADMIN', 'NOMADMIN', 'admin', 'EMAILADMIN', '', '', 192, 2, NULL, NULL, 1, 1);


INSERT INTO pain_annee (`annee_universitaire`) VALUES (2000), (2001), (2002), (2003), (2004), (2005), (2006), (2007), (2008), (2009), (2010), (2011), (2012), (2013), (2014), (2015), (2016), (2017), (2018), (2019), (2020), (2021), (2022), (2023), (2024), (2025), (2026), (2027), (2028), (2029);