-- enseignants -- 

ALTER TABLE  `LesProfs` CHANGE  `numProf`  `numProf` INT( 11 ) NOT NULL;
ALTER TABLE  `LesProfs` DROP PRIMARY KEY;
ALTER TABLE LesProfs AUTO_INCREMENT = 10;
ALTER TABLE  `LesProfs` ADD  `id_enseignant` MEDIUMINT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT AFTER  `numProf` ,
ADD  `categorie` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '5' AFTER  `id_enseignant` ,
ADD  `service` FLOAT UNSIGNED NOT NULL DEFAULT  '192' AFTER  `categorie` ,
ADD PRIMARY KEY (  `id_enseignant` ) ,
ADD UNIQUE (
`id_enseignant`
);

update LesProfs set categorie = 2 where departement = "x" 
and (fonction = "Prof" or fonction = "MdC" or fonction = "PRAG" or fonction = "PAST");  
update LesProfs set categorie = 3 where departement = "x" 
and (fonction = "ATER" or fonction = "ATER 1/2" or fonction = "moniteur");

update LesProfs, LesFonctions set service = volume - `décharge` where categorie < 5 and statut = fonction; 
update LesProfs set service = 1 where categorie = 5; 


update pain_enseignant set id_enseignant = (select max(LesProfs.id_enseignant) + 1 from LesProfs where 1), service = 0, categorie = 0
where id_enseignant = 10;

insert into pain_enseignant 
(id_enseignant,
nom,
prenom,
categorie,
statut,
service) 
select 
id_enseignant,
nom,
prenom,
categorie,
fonction,
service
from LesProfs Where 1 ;

-- formations --
insert into pain_sformation
(id_sformation,
id_enseignant,
nom,
annee_universitaire,
numero)
select
num,
3,
NomCycle,
2011,
ordreC
from LesCycles where 1;

insert into pain_formation
(id_formation,
id_sformation,
id_enseignant,
nom,
parfum,
numero)
select
codeCursus,
num,
3,
NomCursus,
"",
ordre
from LesCursus, LesCycles where LesCursus.cycle = LesCycles.cycle;

-- cours --
insert into pain_cours
(id_cours,
id_formation,
semestre,
id_enseignant,
nom_cours)
select
codeUV,
codeCursus,
0,
3,
matiere
from LesUV where 1;


update pain_cours, LesCTD set
semestre = 1 where codeUV = id_cours 
and periode = "1er semestre"; 

update pain_cours, LesCTD set
semestre = 2 where codeUV = id_cours 
and periode = "2ème semestre"; 

update pain_cours, geishaUV set
code_geisha = geisha where uv = id_cours; 

-- les services de l'annee --
insert into pain_service
(id_enseignant,
annee_universitaire,
categorie,
service_annuel)
select
id_enseignant,
2011,
categorie,
service
from pain_enseignant where 1;

-- les interventions --
insert into pain_tranche
(id_tranche,
id_cours,
id_enseignant,
groupe,
cm,
td,
tp,
alt,
htd
)
select
codeService,
codeUV,
id_enseignant,
0,
equiv_TD / 1.5,
NULL,
NULL,
NULL,
equiv_TD
from lesServices, lesCTD, lesProfs
where lesServices.codeCTD = lesCTD.codeCTD
and lesServices.numProf = lesProfs.numProf
and abtype = "C";

insert into pain_tranche
(id_tranche,
id_cours,
id_enseignant,
groupe,
cm,
td,
tp,
alt,
htd
)
select
codeService,
codeUV,
id_enseignant,
0,
equiv_TD / 2.5,
equiv_TD / 2.5,
NULL,
NULL,
equiv_TD
from lesServices, lesCTD, lesProfs
where lesServices.codeCTD = lesCTD.codeCTD
and lesServices.numProf = lesProfs.numProf
and abtype = "CTD";

insert into pain_tranche
(id_tranche,
id_cours,
id_enseignant,
groupe,
cm,
td,
tp,
alt,
htd
)
select
codeService,
codeUV,
id_enseignant,
0,
NULL,
equiv_TD,
NULL,
NULL,
equiv_TD
from lesServices, lesCTD, lesProfs
where lesServices.codeCTD = lesCTD.codeCTD
and lesServices.numProf = lesProfs.numProf
and abtype = "TD";

insert into pain_tranche
(id_tranche,
id_cours,
id_enseignant,
groupe,
cm,
td,
tp,
alt,
htd
)
select
codeService,
codeUV,
id_enseignant,
0,
NULL,
NULL,
equiv_TD,
NULL,
equiv_TD
from lesServices, lesCTD, lesProfs
where lesServices.codeCTD = lesCTD.codeCTD
and lesServices.numProf = lesProfs.numProf
and abtype = "TP";

insert into pain_tranche
(id_tranche,
id_cours,
id_enseignant,
groupe,
cm,
td,
tp,
alt,
htd
)
select
codeService,
codeUV,
id_enseignant,
0,
NULL,
NULL,
NULL,
equiv_TD,
equiv_TD
from lesServices, lesCTD, lesProfs
where lesServices.codeCTD = lesCTD.codeCTD
and lesServices.numProf = lesProfs.numProf
and abtype <> "C"
and abtype <> "CTD"
and abtype <> "TD"
and abtype <> "TP";

update pain_tranche, LesServices
set pain_tranche.groupe = LesServices.groupe
where LesServices.groupe > 0
and id_tranche = codeService;

-- a donner -> libre
update pain_tranche, pain_enseignant set pain_tranche.id_enseignant = 3 where 
pain_enseignant.nom = "a donner"
and pain_tranche.id_enseignant = pain_enseignant.id_enseignant 

update pain_tranche, pain_enseignant set pain_tranche.id_enseignant = 3 where 
pain_enseignant.nom = "a donner"
and pain_tranche.id_enseignant = pain_enseignant.id_enseignant

-- quelques responsables de cours evidents 
UPDATE pain_cours, (SELECT MAX( pain_tranche.cm ), id_enseignant, id_cours 
                    FROM pain_tranche
                    WHERE pain_tranche.cm > 0 GROUP BY pain_tranche.id_cours) AS t1  
SET pain_cours.id_enseignant = t1.id_enseignant WHERE pain_cours.id_cours = t1.id_cours


--- Les collections pour récupérer les formations (sauf les périodes)
INSERT INTO pain_collection (id_collection, id_sformation, annee_universitaire, nom_collection)
SELECT LesFormations.num, LesCycles.num, "2011", nomf FROM LesFormations, LesCycles 
WHERE  LesFormations.cycle = LesCycles.cycle


--- Probleme d'unicite de cle :
--INSERT INTO pain_collectionscours (id_cours, id_collection)
--SELECT uv, formation FROM `Formation-UV` WHERE 1

-- Solution 1
--- On insere en premier les associations sans probleme d'unicite.
--- INSERT INTO pain_collectionscours (id_cours, id_collection)
--- SELECT formation, uv FROM `Formation-UV` AS t1 WHERE (SELECT DISTINCT count(num) FROM `Formation-UV` AS t2 WHERE t1.formation = t2.formation AND t1.uv = t2.uv) = 1 ORDER BY formation, uv
--- On insere ensuite les associations fautives en un seul exemplaire
--- INSERT INTO pain_collectionscours (id_cours, id_collection)
--- SELECT DISTINCT formation, uv FROM `Formation-UV` AS t1 WHERE (SELECT DISTINCT count(num) FROM `Formation-UV` AS t2 WHERE t1.formation = t2.formation AND t1.uv = t2.uv) > 1 ORDER BY formation, uv
-- Solution 2 (plus simple !)
INSERT INTO pain_collectionscours (id_cours, id_collection)
SELECT DISTINCT uv, formation FROM `Formation-UV` WHERE 1


