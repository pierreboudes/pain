---- replication 2009 -> 2010 ----

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


---- replication 2001 -> 2002 ----

INSERT INTO pain_sformation (id_prev, annee_universitaire, id_enseignant, nom, numero) SELECT `id_sformation` as id_prev, "2002", `id_enseignant`, `nom`, `numero` FROM pain_sformation WHERE `annee_universitaire` = "2001";

-- REVERT : DELETE FROM `pain_sformation` WHERE annee_universitaire = "2002"

-- formation:
INSERT INTO pain_formation
(id_prev, id_sformation, numero, nom, annee_etude, parfum, id_enseignant)
 SELECT pain_formation.id_formation as id_prev, pain_sformation.id_sformation as id_sformation, pain_formation.numero, pain_formation.nom, pain_formation.annee_etude, pain_formation.parfum, pain_formation.id_enseignant FROM pain_formation, pain_sformation WHERE pain_sformation.id_prev = pain_formation.id_sformation AND pain_sformation.annee_universitaire = "2002";

-- cours:
INSERT INTO pain_cours
(id_cours_prev, id_formation, semestre, nom_cours, credits, id_enseignant, cm, td, tp, alt, descriptif, code_geisha)
SELECT
pain_cours.id_cours as id_cours_prev,
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
WHERE pain_sformation.annee_universitaire = "2002"
AND   pain_formation.id_sformation = pain_sformation.id_sformation
AND   pain_cours.id_formation = pain_formation.id_prev;

-------------- STOP 2001 -> 2002


---- replication 2002 -> 2003 ----

INSERT INTO pain_sformation (id_prev, annee_universitaire, id_enseignant, nom, numero) SELECT `id_sformation` as id_prev, "2003", `id_enseignant`, `nom`, `numero` FROM pain_sformation WHERE `annee_universitaire` = "2002";

-- REVERT : DELETE FROM `pain_sformation` WHERE annee_universitaire = "2003"

-- formation:
INSERT INTO pain_formation
(id_prev, id_sformation, numero, nom, annee_etude, parfum, id_enseignant)
 SELECT pain_formation.id_formation as id_prev, pain_sformation.id_sformation as id_sformation, pain_formation.numero, pain_formation.nom, pain_formation.annee_etude, pain_formation.parfum, pain_formation.id_enseignant FROM pain_formation, pain_sformation WHERE pain_sformation.id_prev = pain_formation.id_sformation AND pain_sformation.annee_universitaire = "2003";

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
WHERE pain_sformation.annee_universitaire = "2003"
AND   pain_formation.id_sformation = pain_sformation.id_sformation
AND   pain_cours.id_formation = pain_formation.id_prev;

-------------- STOP 2002 -> 2003

-- annualisation des services
CREATE TABLE `pain_service` (
  `id_enseignant` mediumint(8) unsigned NOT NULL,
  `annee_universitaire` year(4) NOT NULL,
  `categorie` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `service_annuel` float unsigned NOT NULL DEFAULT '192',
  `service_reel` float DEFAULT NULL,
  PRIMARY KEY (`id_enseignant`,`annee_universitaire`)
);

-- mise a jour de pain_service (pour 2010)
DELETE FROM pain_service WHERE annee_universitaire = 2010;
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

--- pour faciliter les editions manuelles de pain_service :
UPDATE pain_service SET tmpnom =(SELECT CONCAT(nom,' ', prenom) FROM pain_enseignant WHERE pain_enseignant.id_enseignant = pain_service.id_enseignant)

---Selection des responsables (supprimer manuellement les responsables hors departement)
SELECT DISTINCT CONCAT(prenom, " ",nom," <",email,">") FROM pain_enseignant WHERE id_enseignant IN (
SELECT id_enseignant FROM pain_sformation WHERE annee_universitaire = 2010
UNION
SELECT pain_formation.id_enseignant FROM pain_formation, pain_sformation WHERE pain_sformation.annee_universitaire = 2010 AND pain_formation.id_sformation = pain_sformation.id_sformation
UNION
SELECT pain_tranche.id_enseignant FROM pain_tranche, pain_cours WHERE id_formation = 48 AND pain_tranche.id_cours = pain_cours.id_cours
 ORDER BY id_enseignant ASC)

---Selection des membres
SELECT CONCAT(prenom, " ",nom," <",email,">") FROM pain_enseignant WHERE categorie = 2 OR categorie = 3


---Listes
CREATE TABLE `pain_listes` (
`liste` VARCHAR( 60 ) NOT NULL ,
`id_enseignant` MEDIUMINT NOT NULL ,
`email` VARCHAR( 60 ) NOT NULL ,
`tmpnom` VARCHAR( 80 ) NULL DEFAULT NULL ,
`modification` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`liste`,`id_enseignant`)
);
ALTER TABLE  `pain_listes` ADD INDEX (  `liste` );
ALTER TABLE  `pain_listes` ADD INDEX (  `id_enseignant` )
ALTER TABLE  `pain_listes` ADD INDEX (  `email` );

--liste responsables (supprimer manuellement les responsables hors departement)
DELETE FROM pain_listes WHERE liste = "responsables";
INSERT INTO pain_listes SELECT "responsables", id_enseignant, email, CONCAT(prenom, " ",nom), NOW()
FROM pain_enseignant WHERE email IS NOT NULL AND id_enseignant IN (
SELECT id_enseignant FROM pain_sformation WHERE annee_universitaire = 2011
UNION
SELECT pain_formation.id_enseignant FROM pain_formation, pain_sformation WHERE pain_sformation.annee_universitaire = 2011 AND pain_formation.id_sformation = pain_sformation.id_sformation
 ORDER BY id_enseignant ASC);
SELECT CONCAT(tmpnom, " <",email,">") FROM `pain_listes` WHERE liste = "responsables";
--Maj droits
UPDATE pain_enseignant SET stats = 0 WHERE 1;
UPDATE pain_enseignant, pain_listes SET stats = 1 WHERE pain_enseignant.id_enseignant = pain_listes.id_enseignant AND pain_listes.liste LIKE "responsables"

--liste membres
INSERT INTO pain_listes SELECT "membres", id_enseignant, email, CONCAT(prenom, " ",nom), NOW()
FROM pain_enseignant WHERE  email IS NOT NULL AND (categorie = 2 OR categorie = 3)

--- maj de la liste membre :
--- 1) lister les membres a desabonner
--- 2) lister les membres a abonner
--- 3) lister les membres ayant change d'adresse
--- 4) mettre a jour la table pain_listes

--- 1) liste des membres a sortir de la liste :
SELECT id_enseignant, tmpnom, email FROM `pain_listes` WHERE liste = "membres"
AND NOT EXISTS
(SELECT pain_enseignant.id_enseignant FROM pain_enseignant WHERE (categorie = 3 OR categorie = 2) AND pain_enseignant.id_enseignant = pain_listes.id_enseignant);
----- mis en forme :
SELECT CONCAT(tmpnom, " <",email,">") FROM `pain_listes` WHERE liste = "membres"
AND NOT EXISTS
(SELECT pain_enseignant.id_enseignant FROM pain_enseignant WHERE (categorie = 3 OR categorie = 2) AND pain_enseignant.id_enseignant = pain_listes.id_enseignant);

--- 2) liste des membres a abonner
SELECT id_enseignant, CONCAT(prenom," ", nom), email FROM `pain_enseignant` WHERE (categorie = 3 OR categorie = 2)
AND NOT EXISTS
(SELECT pain_listes.id_enseignant FROM pain_listes WHERE liste = "membres" AND pain_enseignant.id_enseignant = pain_listes.id_enseignant);
----- mise en forme :
SELECT CONCAT(prenom," ", nom, " <", email,">") FROM `pain_enseignant` WHERE (categorie = 3 OR categorie = 2)
AND NOT EXISTS
(SELECT pain_listes.id_enseignant FROM pain_listes WHERE liste = "membres" AND pain_enseignant.id_enseignant = pain_listes.id_enseignant);
--- 3) liste des membres dont l'adresse doit etre changee
SELECT pain_listes.id_enseignant, tmpnom, pain_listes.email, pain_enseignant.email FROM pain_listes, pain_enseignant
WHERE liste = "membres"
AND pain_listes.id_enseignant = pain_enseignant.id_enseignant
AND pain_listes.email <> pain_enseignant.email;
--- 4) Mettre a jour
DELETE FROM pain_listes WHERE liste = "membres";
INSERT INTO pain_listes SELECT "membres", id_enseignant, email, CONCAT(prenom, " ",nom), NOW()
FROM pain_enseignant WHERE  email IS NOT NULL AND (categorie = 2 OR categorie = 3)

/* seulement ajouter les nouveaux et mettre ≒ jour les mails (pas de sortie des anciens pour le moment) */
REPLACE INTO pain_listes SELECT "membres", id_enseignant, email, CONCAT(prenom, " ",nom), NOW()
FROM pain_enseignant WHERE  email IS NOT NULL AND (categorie = 2 OR categorie = 3);
SELECT CONCAT(tmpnom, " <",email,">") FROM `pain_listes` WHERE liste = "membres";



------Pseudonommage
UPDATE pain_enseignant, pseudos SET nom=pseudo_nom, prenom=pseudo_prenom, email=CONCAT(pseudo_prenom,'.',pseudo_nom,'@nullepart'), bureau='A501', telephone='40 67' WHERE id_enseignant > 9 AND id_enseignant = id_pseudo
UPDATE pain_enseignant, pseudos SET login=CONCAT(pseudo_prenom,'.',pseudo_nom) WHERE id_enseignant > 10 AND id_enseignant = id_pseudo

------ stats liste des responsabilites


(select
concat('cours: ', nom_cours, ', ', pain_formation.nom, ' ', annee_etude) as resp_nom,
concat('c', id_cours) as id_responsabilite,
1 as resp_type_num,
from pain_cours, pain_formation, pain_sformation
where pain_cours.id_enseignant = 10
and pain_cours.id_formation = pain_formation.id_formation
and pain_formation.id_sformation = pain_sformation.id_sformation
and pain_sformation.annee_universitaire = 2009
)
union
(select
concat('annee de formation: ', pain_formation.nom, ' ', annee_etude) as resp_nom,
concat('f', id_formation) as id_responsabilite,
2 as resp_type_num
from pain_formation, pain_sformation
where pain_formation.id_enseignant = 10
and pain_formation.id_sformation = pain_sformation.id_sformation
and pain_sformation.annee_universitaire = 2009
)
union
(select
concat('formation: ', pain_sformation.nom) as resp_nom,
concat('s', id_sformation) as id_responsabilite,
3 as resp_type_num
from pain_sformation
where pain_sformation.id_enseignant = 10
and pain_sformation.annee_universitaire = 2009
)



--- Tous les cours avec de vrais intervenants mais sans code_ue
--- valide, en 2014, en informatique.
SELECT sf.nom, informatique.pain_formation.nom, informatique.pain_formation.annee_etude, informatique.pain_formation.parfum, informatique.pain_cours.nom_cours, informatique.pain_cours.descriptif
FROM
((((SELECT * FROM informatique.pain_sformation WHERE annee_universitaire = 2014) as sf
JOIN (informatique.pain_formation) USING (id_sformation))
JOIN (informatique.pain_cours) USING (id_formation))
NATURAL JOIN (select id_cours from informatique.pain_tranche where id_enseignant > 9  group by id_cours) as tr)
LEFT JOIN commun.codesue USING (code_ue)
WHERE commun.codesue.intitule_cours IS NULL



SELECT sf.nom, physique.pain_formation.nom, physique.pain_formation.annee_etude, physique.pain_formation.parfum, physique.pain_cours.nom_cours, physique.pain_cours.descriptif
FROM
((((SELECT * FROM physique.pain_sformation WHERE annee_universitaire = 2014) as sf
JOIN (physique.pain_formation) USING (id_sformation))
JOIN (physique.pain_cours) USING (id_formation))
NATURAL JOIN (select id_cours from physique.pain_tranche where id_enseignant > 9  group by id_cours) as tr)
LEFT JOIN commun.codesue USING (code_ue)
WHERE commun.codesue.intitule_cours IS NULL


SELECT sf.nom, mathematiques.pain_formation.nom, mathematiques.pain_formation.annee_etude, mathematiques.pain_formation.parfum, mathematiques.pain_cours.nom_cours, mathematiques.pain_cours.descriptif
FROM
((((SELECT * FROM mathematiques.pain_sformation WHERE annee_universitaire = 2014) as sf
JOIN (mathematiques.pain_formation) USING (id_sformation))
JOIN (mathematiques.pain_cours) USING (id_formation))
NATURAL JOIN (select id_cours from mathematiques.pain_tranche where id_enseignant > 9  group by id_cours) as tr)
LEFT JOIN commun.codesue USING (code_ue)
WHERE commun.codesue.intitule_cours IS NULL



https://servens-galilee.univ-paris13.fr/p-m-aaa/sql.php?db=commun&table=codesue&printview=1&sql_query=SELECT+sf.nom%2C+mathematiques.pain_formation.nom%2C+mathematiques.pain_formation.annee_etude%2C+mathematiques.pain_formation.parfum%2C+mathematiques.pain_cours.nom_cours%2C+mathematiques.pain_cours.descriptif%0D%0AFROM%0D%0A%28%28%28%28SELECT+%2A+FROM+mathematiques.pain_sformation+WHERE+annee_universitaire+%3D+2014%29+as+sf%0D%0AJOIN+%28mathematiques.pain_formation%29+USING+%28id_sformation%29%29%0D%0AJOIN+%28mathematiques.pain_cours%29+USING+%28id_formation%29%29%0D%0ANATURAL+JOIN+%28select+id_cours+from+mathematiques.pain_tranche+where+id_enseignant+%3E+9++group+by+id_cours%29+as+tr%29%0D%0ALEFT+JOIN+commun.codesue+USING+%28code_ue%29%0D%0AWHERE+commun.codesue.intitule_cours+IS+NULL%0D%0A&token=bfd1074f338c4cb8b2765c18f1880b03





---- Grosse jointure pour ensuite calculer les totaux
SELECT *
FROM
((
SELECT sf.nom as nom_sformation,
sf.id_sformation as id_sformation,
mathematiques.pain_formation.code_etape_formation as code_etape_formation,
mathematiques.pain_formation.nom as nom_formation,
mathematiques.pain_formation.annee_etude as annee_etude,
mathematiques.pain_formation.parfum as parfum,
mathematiques.pain_formation.id_formation as id_formation,
mathematiques.pain_cours.nom_cours as nom_cours,
mathematiques.pain_cours.id_cours as id_cours,
mathematiques.pain_cours.id_enseignant as id_enseignant_cours,
mathematiques.pain_tranche.id_enseignant as id_enseignant,
mathematiques.pain_tranche.htd as htd
FROM
((((SELECT * FROM mathematiques.pain_sformation WHERE annee_universitaire = 2014) as sf
JOIN (mathematiques.pain_formation) USING (id_sformation))
JOIN (mathematiques.pain_cours) USING (id_formation))
JOIN (mathematiques.pain_tranche) USING (id_cours))
WHERE 1
) as c
JOIN
(SELECT id_enseignant, categorie as id_categorie
FROM mathematiques.pain_service
WHERE annee_universitaire = 2014
) as s
USING (id_enseignant))
JOIN pain_categorie USING (id_categorie)
WHERE 1



--- Traiter en SQL le cas particulier de l'enseignant de cours annulé
SELECT *
FROM
((
SELECT sf.nom as nom_sformation,
sf.id_sformation as id_sformation,
mathematiques.pain_formation.code_etape_formation as code_etape_formation,
mathematiques.pain_formation.nom as nom_formation,
mathematiques.pain_formation.annee_etude as annee_etude,
mathematiques.pain_formation.parfum as parfum,
mathematiques.pain_formation.id_formation as id_formation,
mathematiques.pain_cours.nom_cours as nom_cours,
mathematiques.pain_cours.id_cours as id_cours,
mathematiques.pain_cours.id_enseignant as id_enseignant_cours,
IF (mathematiques.pain_cours.id_enseignant = 1, 1, mathematiques.pain_tranche.id_enseignant) as id_enseignant,
mathematiques.pain_tranche.htd as htd
FROM
((((SELECT * FROM mathematiques.pain_sformation WHERE annee_universitaire = 2014) as sf
JOIN (mathematiques.pain_formation) USING (id_sformation))
JOIN (mathematiques.pain_cours) USING (id_formation))
JOIN (mathematiques.pain_tranche) USING (id_cours))
WHERE 1
) as c
JOIN
(SELECT id_enseignant, categorie as id_categorie
FROM mathematiques.pain_service
WHERE annee_universitaire = 2014
) as s
USING (id_enseignant))
JOIN pain_categorie USING (id_categorie)
WHERE 1



--- Version locale
SELECT *
FROM
((
SELECT sf.nom as nom_sformation,
sf.id_sformation as id_sformation,
pain_formation.code_etape_formation as code_etape_formation,
pain_formation.nom as nom_formation,
pain_formation.annee_etude as annee_etude,
pain_formation.parfum as parfum,
pain_formation.id_formation as id_formation,
pain_cours.nom_cours as nom_cours,
pain_cours.id_cours as id_cours,
pain_cours.id_enseignant as id_enseignant_cours,
pain_tranche.id_enseignant as id_enseignant,
IF (pain_cours.id_enseignant = 1, 1, pain_tranche.id_enseignant) as id_enseignant,
pain_tranche.htd as htd
FROM
((((SELECT * FROM pain_sformation WHERE annee_universitaire = 2014) as sf
JOIN (pain_formation) USING (id_sformation))
JOIN (pain_cours) USING (id_formation))
JOIN (pain_tranche) USING (id_cours))
WHERE 1
) as c
JOIN
(SELECT id_enseignant, categorie as id_categorie
FROM pain_service
WHERE annee_universitaire = 2014
) as s
USING (id_enseignant))
JOIN pain_categorie USING (id_categorie)
WHERE 1

-----
SELECT sf.nom, informatique.pain_formation.nom, informatique.pain_formation.annee_etude, informatique.pain_formation.parfum, informatique.pain_cours.nom_cours, informatique.pain_cours.descriptif
FROM
((((SELECT * FROM informatique.pain_sformation WHERE annee_universitaire = 2014) as sf
JOIN (informatique.pain_formation) USING (id_sformation))
JOIN (informatique.pain_cours) USING (id_formation))
NATURAL JOIN (select id_cours from informatique.pain_tranche where id_enseignant > 9  group by id_cours) as tr)
LEFT JOIN commun.codesue USING (code_ue)
WHERE commun.codesue.intitule_cours IS NULL





--- Validation


DROP TABLE IF EXISTS pain_validation_cours;
DROP TABLE IF EXISTS pain_validation_tranche;

CREATE TABLE `pain_validation_cours` (
  `id_cours` int(10) unsigned NOT NULL,
  `id_formation` int(10) unsigned NOT NULL,
  `valide` tinyint(1) NOT NULL,
  `commentaire_validation` varchar(256) NOT NULL,
  `modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY id_cours (id_cours),
  KEY id_formation (id_formation)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;


CREATE TABLE `pain_validation_tranche` (
  `id_tranche` int(10) unsigned NOT NULL,
  `id_cours` int(10) unsigned NOT NULL,
  `valide` tinyint(1) NOT NULL,
  `commentaire_validation` varchar(256) NOT NULL,
  `modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY id_tranche (id_tranche),
  KEY id_cours (id_cours)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;



TRUNCATE table pain_validation_cours;

INSERT INTO pain_validation_cours (id_cours, id_formation, valide, commentaire_validation, modification)
SELECT id_cours, id_formation, 1, 'valide', NOW()
FROM
(((SELECT id_sformation FROM pain_sformation WHERE annee_universitaire = 2015) as sf
JOIN (pain_formation) USING (id_sformation))
JOIN (pain_cours) USING (id_formation));

UPDATE pain_validation_cours SET valide = 0, commentaire_validation = ' code ue inconnu' WHERE
id_cours in
(
        select id_cours FROM
        (pain_cours LEFT JOIN commun.codesue USING (code_ue))
        WHERE commun.codesue.intitule_cours IS NULL
);

TRUNCATE TABLE pain_validation_tranche;

INSERT INTO pain_validation_tranche (id_tranche, id_cours, valide, commentaire_validation, modification)
SELECT id_tranche, id_cours, 1, 'valide', NOW()
FROM  ((pain_validation_cours)
JOIN (pain_tranche) USING (id_cours));

UPDATE pain_validation_tranche, pain_tranche
SET valide = 0, commentaire_validation = (concat(commentaire_validation, ' stage sans nom du stagiaire'))
WHERE
pain_validation_tranche.id_tranche = pain_tranche.id_tranche
AND
pain_tranche.id_enseignant > 9
AND
pain_tranche.declarer LIKE ""
AND
pain_tranche.id_cours in
(
        select id_cours FROM
        (pain_cours LEFT JOIN commun.codesue USING (code_ue))
        WHERE commun.codesue.intitule_cours LIKE '%stage'
);
