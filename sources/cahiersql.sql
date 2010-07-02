INSERT INTO pain_sformation (id_prev, annee_universitaire, id_enseignant, nom, numero) SELECT `id_sformation` as id_prev, "2010", `id_enseignant`, `nom`, `numero` FROM pain_sformation WHERE `annee_universitaire` = "2009";

-- REVERT : DELETE FROM `pain_sformation` WHERE annee_universitaire = "2010"

-- formation:
INSERT INTO pain_formation 
(id_prev, id_sformation, numero, nom, annee_etude, parfum, id_enseignant)
 SELECT pain_formation.id_formation as id_prev, pain_sformation.id_sformation as id_sformation, pain_formation.numero, pain_formation.nom, pain_formation.annee_etude, pain_formation.parfum, pain_formation.id_enseignant FROM pain_formation, pain_sformation WHERE pain_sformation.id_prev = pain_formation.id_sformation AND pain_sformation.annee_universitaire = "2010";

-- cours:
INSERT INTO pain_cours
(id_prev, id_formation, semestre, nom_cours, credits, id_enseignant, cm, td, tp, alt, descriptif, code_geisha)
SELECT
pain_cours.id_cours as id_prev,
pain_formation.id_formation,
pain_cours.semestre,
pain_cours.nom_cours,
pain_cours.credits,
pain_cours.id_enseignant,
pain_cours.cm,
pain_cours.td,
pain_cours.tp,
pain_cours.alt,
pain_cours.descriptif,
pain_cours.code_geisha
FROM
pain_cours, pain_formation, pain_sformation
WHERE pain_sformation.annee_universitaire = "2010"
AND   pain_formation.id_sformation = pain_sformation.id_sformation
AND   pain_cours.id_formation = pain_formation.id_prev;

-- annualisation des services
CREATE TABLE `pain_service` (
  `id_enseignant` mediumint(8) unsigned NOT NULL,
  `annee_universitaire` year(4) NOT NULL,
  `categorie` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `service_annuel` float unsigned NOT NULL DEFAULT '192',
  `service_reel` float DEFAULT NULL,
  PRIMARY KEY (`id_enseignant`,`annee_universitaire`)
);

-- mise à jour de pain_service (pour 2010)
REPLACE INTO pain_service 
  (id_enseignant, annee_universitaire, categorie, service_annuel, tmpnom)
SELECT 
  pain_enseignant.id_enseignant,
  "2010",
  pain_enseignant.categorie,  
  pain_enseignant.service,
  CONCAT(nom," ",prenom)
FROM pain_enseignant
WHERE 1;


---- ajouts de champs pour les cours
ALTER TABLE  `pain_cours` ADD  `debut` DATE NOT NULL DEFAULT '1970-01-01' AFTER  `code_geisha` ,
ADD  `fin` DATE NOT NULL DEFAULT '1970-01-01' AFTER  `debut` ,
ADD  `mcc` TEXT NULL AFTER  `fin` ,
ADD  `inscrits` SMALLINT UNSIGNED NULL AFTER  `mcc` ,
ADD  `presents` SMALLINT UNSIGNED NULL AFTER  `inscrits`,
ADD  `tirage` SMALLINT UNSIGNED NULL AFTER  `presents`


------nouvelles extractions possibles
SELECT  nom, annee_etude, nom_cours, (pain_cours.cm+pain_cours.td+pain_cours.tp+pain_cours.alt)*presents AS vetu,
SUM(pain_tranche.htd) AS vens,
vetu / vens
FROM pain_formation, pain_cours, pain_tranche 
WHERE presents > 0
AND pain_formation.id_formation = pain_cours.id_formation
AND pain_tranche.id_cours = pain_cours.id_cours
AND pain_tranche.id_enseignant > 9
GROUP BY pain_cours.id_cours
ORDER BY vens DESC

---- annee anterieure 2006-2007
INSERT INTO pain_sformation (id_prev, annee_universitaire, id_enseignant, nom, numero) SELECT `id_sformation` as id_prev, "2006", `id_enseignant`, `nom`, `numero` FROM pain_sformation WHERE `annee_universitaire` = "2009";

-- REVERT : DELETE FROM `pain_sformation` WHERE annee_universitaire = "2010"

-- formation:
INSERT INTO pain_formation 
(id_prev, id_sformation, numero, nom, annee_etude, parfum, id_enseignant)
 SELECT pain_formation.id_formation as id_prev, pain_sformation.id_sformation as id_sformation, pain_formation.numero, pain_formation.nom, pain_formation.annee_etude, pain_formation.parfum, pain_formation.id_enseignant FROM pain_formation, pain_sformation WHERE pain_sformation.id_prev = pain_formation.id_sformation AND pain_sformation.annee_universitaire = "2006";


---service
INSERT INTO pain_service (id_enseignant, annee_universitaire, categorie, service_annuel) SELECT serv.id_enseignant, "2006", serv.categorie, serv.service_annuel FROM pain_service AS serv WHERE serv.annee_universitaire = "2009"

--- pour faciliter les éditions manuelles de pain_service :
UPDATE pain_service SET tmpnom =(SELECT CONCAT(nom,' ', prenom) FROM pain_enseignant WHERE pain_enseignant.id_enseignant = pain_service.id_enseignant)

---Selection des responsables (supprimer manuellement les responsables hors departement)
SELECT GROUP_CONCAT(CONCAT(prenom, " ",nom," <",email,">") SEPARATOR ",\n") FROM pain_enseignant WHERE id_enseignant IN (
SELECT id_enseignant FROM pain_sformation WHERE annee_universitaire = 2010 
UNION 
SELECT pain_formation.id_enseignant FROM pain_formation, pain_sformation WHERE pain_sformation.annee_universitaire = 2010 AND pain_formation.id_sformation = pain_sformation.id_sformation 
UNION 
SELECT pain_tranche.id_enseignant FROM pain_tranche, pain_cours WHERE id_formation = 48 AND pain_tranche.id_cours = pain_cours.id_cours
 ORDER BY id_enseignant ASC)

---Selection des membres 
SELECT CONCAT(prenom, " ",nom," <",email,">") FROM pain_enseignant WHERE categorie = 2 OR categorie = 3