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

-- mise Ã  jour de pain_service (pour 2010)
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
INSERT INTO pain_listes SELECT "responsables", id_enseignant, email, CONCAT(prenom, " ",nom), NOW() 
FROM pain_enseignant WHERE email IS NOT NULL AND id_enseignant IN (
SELECT id_enseignant FROM pain_sformation WHERE annee_universitaire = 2010 
UNION
SELECT pain_formation.id_enseignant FROM pain_formation, pain_sformation WHERE pain_sformation.annee_universitaire = 2010 AND pain_formation.id_sformation = pain_sformation.id_sformation 
UNION
SELECT pain_tranche.id_enseignant FROM pain_tranche, pain_cours WHERE id_formation = 48 AND pain_tranche.id_cours = pain_cours.id_cours
 ORDER BY id_enseignant ASC)

--Maj droits
UPDATE pain_enseignant SET stats = 0 WHERE 1;
UPDATE pain_enseignant, pain_listes SET stats = 1 WHERE pain_enseignant.id_enseignant = pain_listes.id_enseignant AND pain_listes.liste LIKE "responsables"

--liste membres 
INSERT INTO pain_listes SELECT "membres", id_enseignant, email, CONCAT(prenom, " ",nom), NOW()
FROM pain_enseignant WHERE  email IS NOT NULL AND (categorie = 2 OR categorie = 3)

--- maj de la liste membre :
--- 1) lister les membres a desabonner
--- 2) lister les membres a  abonner
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


------Pseudonommage
UPDATE pain_enseignant, pseudos SET nom=pseudo_nom, prenom=pseudo_prenom, email=CONCAT(pseudo_prenom,'.',pseudo_nom,'@nullepart'), bureau='A501', telephone='40 67' WHERE id_enseignant > 9 AND id_enseignant = id_pseudo
UPDATE pain_enseignant, pseudos SET login=CONCAT(pseudo_prenom,'.',pseudo_nom) WHERE id_enseignant > 10 AND id_enseignant = id_pseudo

------ stats voeux 

select pain_choix.id_enseignant, pain_choix.id_cours,
sum(pain_choix.htd) from pain_choix, pain_service where
pain_service.annee_universitaire = 2011 AND pain_choix.id_enseignant =
pain_service.id_enseignant AND pain_service.service_annuel > 1 GROUP
BY pain_choix.id_cours

select * from (select pain_choix.id_enseignant as enseignant, pain_choix.id_cours as cours, pain_cours.nom_cours, sum(pain_choix.htd) as choix
from pain_choix, pain_cours, pain_formation, pain_sformation 
where
pain_sformation.annee_universitaire = 2011
and
pain_formation.id_sformation = pain_sformation.id_sformation
and
pain_cours.id_formation = pain_formation.id_formation
and
pain_choix.id_cours = pain_cours.id_cours
group by pain_choix.id_cours)
outer join
(select pain_tranche.id_enseignant as enseignant, pain_tranche.id_cours as cours, sum(pain_tranche.htd) as interventions
from pain_tranche, pain_cours, pain_formation, pain_sformation 
where
pain_sformation.annee_universitaire = 2011
and
pain_formation.id_sformation = pain_sformation.id_sformation
and
pain_cours.id_formation = pain_formation.id_formation
and
pain_tranche.id_cours = pain_cours.id_cours
group by pain_tranche.id_cours) as t1


pain_service.annee_universitaire = 2011 AND pain_choix.id_enseignant =
pain_service.id_enseignant AND pain_service.service_annuel > 1 GROUP
BY pain_choix.id_cours



select idc,
(select 
sum(cm) as cm,
sum(td) as td,
sum(tp) as tp,
sum(alt) as alt,
sum(htd) as htd
from pain_choix where pain_choix.id_cours = idc and pain_choix.id_enseignant = 10 
group by pain_choix.id_cours),
(select 
sum(htd) as htd2
from pain_tranche where pain_tranche.id_cours = idc and pain_tranche.id_enseignant = 10 
group by pain_tranche.id_cours)
from
((
select 
pain_choix.id_cours as idc, 
pain_cours.nom_cours,
pain_cours.id_cours,
pain_cours.semestre,
pain_formation.nom,
pain_formation.annee_etude,
pain_formation.parfum
from pain_choix, pain_cours, pain_formation, pain_sformation 
where pain_choix.id_enseignant = 10 
and pain_choix.id_cours = pain_cours.id_cours
and pain_cours.id_formation = pain_formation.id_formation
and pain_formation.id_sformation = pain_sformation.id_sformation
and annee_universitaire = 2011
)
union
(
select 
pain_tranche.id_cours as idc,
pain_cours.nom_cours,
pain_cours.id_cours,
pain_cours.semestre,
pain_formation.nom,
pain_formation.annee_etude,
pain_formation.parfum
from pain_tranche, pain_cours, pain_formation, pain_sformation 
where pain_tranche.id_enseignant = 10
and pain_tranche.id_cours = pain_cours.id_cours
and pain_cours.id_formation = pain_formation.id_formation
and pain_formation.id_sformation = pain_sformation.id_sformation
and annee_universitaire = 2011
)) as t2


select * from 
(select id_cours, sum(htd) as choix
from pain_choix
where
id_enseignant = 10
group by id_cours) as t1
outer join
(select id_cours, sum(htd) as tranche
from pain_tranche
where
id_enseignant = 10
group by id_cours) as t2 using(id_cours)



SELECT * 
FROM (
SELECT id_cours, SUM( htd ) AS choix
FROM pain_choix
WHERE id_enseignant =10
GROUP BY id_cours
) AS t1
LEFT JOIN (
SELECT id_cours, SUM( htd ) AS tranche
FROM pain_tranche
WHERE id_enseignant =10
GROUP BY id_cours
) AS t2
USING ( id_cours )


select *,
greatest(ifnull(tranche_cm,0),ifnull(choix_cm,0)) as cm,
greatest(ifnull(tranche_td,0),ifnull(choix_td,0)) as td,
greatest(ifnull(tranche_tp,0),ifnull(choix_tp,0)) as tp,
greatest(ifnull(tranche_alt,0),ifnull(choix_alt,0)) as alt,
greatest(ifnull(tranche_htd,0),ifnull(choix_htd,0)) as htd 
from
((
select
pain_cours.id_cours,
pain_cours.nom_cours,
pain_cours.semestre,
pain_formation.nom,
pain_formation.annee_etude,
pain_formation.parfum
from pain_choix, pain_cours, pain_formation, pain_sformation 
where
pain_choix.id_enseignant = 10 
and pain_choix.id_cours = pain_cours.id_cours
and pain_cours.id_formation = pain_formation.id_formation
and pain_formation.id_sformation = pain_sformation.id_sformation
and annee_universitaire = 2011)
union
(select
pain_cours.id_cours,
pain_cours.nom_cours,
pain_cours.semestre,
pain_formation.nom,
pain_formation.annee_etude,
pain_formation.parfum
from pain_tranche, pain_cours, pain_formation, pain_sformation 
where
pain_tranche.id_enseignant = 10 
and pain_tranche.id_cours = pain_cours.id_cours
and pain_cours.id_formation = pain_formation.id_formation
and pain_formation.id_sformation = pain_sformation.id_sformation
and annee_universitaire = 2011)) as t0
left join
(select id_cours, 
sum(cm) as choix_cm,
sum(td) as choix_td,
sum(tp) as choix_tp,
sum(alt) as choix_alt,
sum(htd) as choix_htd
from pain_choix
where
id_enseignant = 10
group by id_cours) as t1 using(id_cours)
left join
(select id_cours,
sum(cm) as tranche_cm,
sum(td) as tranche_td,
sum(tp) as tranche_tp,
sum(alt) as tranche_alt,
sum(htd) as tranche_htd
from pain_tranche
where
id_enseignant = 10
group by id_cours) as t2
 using(id_cours)












select bob.id_enseignant,
sum(greatest(ifnull(tranche_htd,0),ifnull(choix_htd,0)))
from pain_service as bob,
((
select
pain_sformation.numero,
pain_cours.id_cours,
pain_cours.nom_cours,
pain_cours.semestre,
pain_formation.nom,
pain_formation.annee_etude,
pain_formation.parfum
from pain_choix, pain_cours, pain_formation, pain_sformation 
where
pain_choix.id_enseignant = 10
and pain_choix.id_cours = pain_cours.id_cours
and pain_cours.id_formation = pain_formation.id_formation
and pain_formation.id_sformation = pain_sformation.id_sformation
and pain_sformation.annee_universitaire = 2011)
union
(select
pain_sformation.numero,
pain_cours.id_cours,
pain_cours.nom_cours,
pain_cours.semestre,
pain_formation.nom,
pain_formation.annee_etude,
pain_formation.parfum
from pain_tranche, pain_cours, pain_formation, pain_sformation 
where
pain_tranche.id_enseignant = 10
and pain_tranche.id_cours = pain_cours.id_cours
and pain_cours.id_formation = pain_formation.id_formation
and pain_formation.id_sformation = pain_sformation.id_sformation
and pain_sformation.annee_universitaire = 2011)) as t0
left join
(select id_cours, 
sum(cm) as choix_cm,
sum(td) as choix_td,
sum(tp) as choix_tp,
sum(alt) as choix_alt,
sum(htd) as choix_htd
from pain_choix
where
pain_choix.id_enseignant = bob.id_enseignant 
group by id_cours) as t1 using(id_cours)
left join
(select id_cours,
sum(cm) as tranche_cm,
sum(td) as tranche_td,
sum(tp) as tranche_tp,
sum(alt) as tranche_alt,
sum(htd) as tranche_htd
from pain_tranche
where
pain_tranche.id_enseignant = bob.id_enseignant 
group by id_cours) as t2
using(id_cours)
where bob.id_enseignant = 10



update pain_service set
service_potentiel = (select
sum(greatest(ifnull(
(select sum(htd)
from pain_tranche
where pain_tranche.id_enseignant  = pain_service.id_enseignant
and pain_tranche.id_cours = tid.id_cours)
,0),ifnull(
(select sum(htd)
from pain_choix
where pain_choix.id_enseignant = pain_service.id_enseignant
and pain_choix.id_cours = tid.id_cours)
,0)))
from pain_cours as tid, pain_formation, pain_sformation 
where pain_sformation.annee_universitaire = pain_service.annee_universitaire
and pain_formation.id_sformation = pain_sformation.id_sformation
and tid.id_formation = pain_formation.id_formation 
)
where
pain_service.annee_universitaire = 2011

