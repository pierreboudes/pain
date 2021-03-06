<?php /* -*- coding: utf-8 -*-*/
/* Pain - outil de gestion des services d'enseignement
 *
 * Copyright 2009-2015 Pierre Boudes,
 * département d'informatique de l'institut Galilée.
 *
 * This file is part of Pain.
 *
 * Pain is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Pain is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public
 * License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Pain.  If not, see <http://www.gnu.org/licenses/>.
 */
require_once('authentication.php');
$user = authentication();
require_once("inc_connect.php");
require_once("utils.php");


/** Copie récursivement un élément de type $type et d'id $id, sous un élément d'id $id_cible
 */
function json_copy($type, $id, $id_cible, $profondeur) {
    global $link;
/* récupérer l'année cible (pour les services) */
if ($type == "annee") {
    $annee_cible = $id_cible;

    /* verifions toutefois que l'annee est bien vide */
    $q = "SELECT COUNT(*) AS tot FROM pain_sformation WHERE annee_universitaire = ".$id_cible;
    if (!($r = $link->query($q))) {
	errmsg("erreur avec la requete :\n".$q."\n".$link->error);
    }
//     if (!($ligne = $r->fetch_array())) {
// 	errmsg("cible non existante !");
//     }
    if ($ligne["tot"] > 0) {
	errmsg("l'annee ciblée ne doit pas contenir de formations");
    }
} else if ($type == "sformation") {
    $annee_cible = $id_cible;
} else {
    errmsg("type non géré");
}

$qmajservices = 'REPLACE INTO pain_service
                 (id_enseignant, annee_universitaire, categorie, service_annuel)
                 SELECT
                 pain_enseignant.id_enseignant,
                 "'.$annee_cible.'",
                 pain_enseignant.categorie,
                 COALESCE(pain_enseignant.service,0)
                 FROM pain_enseignant
                 WHERE  (pain_enseignant.id_enseignant NOT IN (SELECT pain_service.id_enseignant FROM pain_service WHERE pain_service.annee_universitaire = "'.$annee_cible.'")) AND pain_enseignant.id_enseignant IN ';


/* supers formations */
if ($profondeur > 0) {

    if ($type == "annee") {
	$cond = 'annee_universitaire =  '.$id;
    } else if ($type == "sformation") {
	$cond = "id_sformation = ".$id;
    }

    /* mise à jour des services des responsables de sformation */

    $q = $qmajservices." (SELECT DISTINCT id_enseignant FROM pain_sformation WHERE ". $cond.') ';
    if (!$link->query($q)) {
	errmsg("erreur avec la requete :\n".$q."\n".$link->error);
    }


    /* insertion de la ou des nouvelles sformation */
    $q = 'INSERT INTO pain_sformation (id_sformation_prev, annee_universitaire, id_enseignant, nom, numero) SELECT `id_sformation` as id_sformation_prev, "'.$annee_cible.'", `id_enseignant`, `nom`, `numero` FROM pain_sformation WHERE '.$cond;

    if (!$link->query($q)) {
	errmsg("erreur avec la requete :\n".$q."\n".$link->error);
    }

    if ($type == "sformation") {
	$id_new = $link->insert_id;
    }
    $profondeur -= 1;
}

/* formations */
if ($profondeur > 0) {

    if ($type == "annee") {
	$cond = "annee_universitaire =  ".$annee_cible."  AND
                 pain_formation.id_sformation  = id_sformation_prev ";
	$cocond = "pain_sformation.annee_universitaire =  ".$annee_cible."  AND
                  pain_collection.id_sformation = id_sformation_prev";
    } else if ($type == "sformation") {
	$cond = "pain_sformation.id_sformation = ".$id_new."
                 AND pain_formation.id_sformation  = id_sformation_prev";
	$cocond = "pain_sformation.id_sformation = ".$id_new."
                 AND pain_collection.id_sformation  = id_sformation_prev";
    }

    /* mise à jour des services des responsables de formations */
    $q = $qmajservices." (SELECT DISTINCT pain_formation.id_enseignant FROM  pain_sformation, pain_formation WHERE ". $cond.') ';
    if (!$link->query($q)) {
	errmsg("erreur avec la requete :\n".$q."\n".$link->error);
    }

    /* insertion de la ou des nouvelles formation */
    $q = 'INSERT INTO pain_formation (id_formation_prev, id_sformation, numero, nom, annee_etude, parfum, id_enseignant, code_etape_formation) SELECT pain_formation.id_formation as id_formation_prev, pain_sformation.id_sformation as id_sformation, pain_formation.numero, pain_formation.nom, pain_formation.annee_etude, pain_formation.parfum, pain_formation.id_enseignant, pain_formation.code_etape_formation FROM pain_formation, pain_sformation WHERE '.$cond;

    if (!$link->query($q)) {
	errmsg("erreur avec la requete :\n".$q."\n".$link->error);
    }
    /* insertion des collections associées aux sformations */
    $q = 'INSERT INTO pain_collection (id_collection_prev, id_sformation, annee_universitaire, nom_collection, descriptif) SELECT pain_collection.id_collection, pain_sformation.id_sformation, pain_sformation.annee_universitaire, nom_collection, descriptif FROM pain_collection, pain_sformation WHERE '.$cocond;
    if (!$link->query($q)) {
	errmsg("erreur avec la requete :\n".$q."\n".$link->error);
    }
    if ($type == "annee") {
	/* insertion des collections de l'année non associées aux sformations */
	$q = 'INSERT INTO pain_collection (id_collection_prev, id_sformation, annee_universitaire, nom_collection, descriptif) SELECT id_collection, id_sformation, '.$annee_cible.', nom_collection, descriptif FROM pain_collection WHERE id_sformation IS NULL AND pain_collection.annee_universitaire = '.$id;
	if (!$link->query($q)) {
	    errmsg("erreur avec la requete :\n".$q."\n".$link->error);
	}
    }
    $profondeur -= 1;
}

/* cours */
if ($profondeur > 0) {

    if ($type == "annee") {
	$cond = "annee_universitaire = ".$annee_cible."
                 AND pain_formation.id_sformation =
                     pain_sformation.id_sformation
                 AND pain_cours.id_formation =
                     pain_formation.id_formation_prev ";
    } else if ($type == "sformation") {
	$cond = "pain_formation.id_sformation = ".$id_new."
                 AND pain_sformation.id_sformation =  ".$id_new
/*la derniere clause juste pour eviter que le resultat soit multipliee
 * par le nb de sformations ! */
              ." AND pain_cours.id_formation =
                     pain_formation.id_formation_prev ";
    }

    /* mise à jour des services des responsables de cours */

    $q = $qmajservices." (SELECT DISTINCT pain_cours.id_enseignant FROM pain_sformation, pain_formation, pain_cours WHERE ". $cond.') ';
    if (!$link->query($q)) {
	errmsg("erreur avec la requete :\n".$q."\n".$link->error);
    }

    /* insertion des nouveaux cours */
    $q = 'INSERT INTO pain_cours (id_cours_prev, id_formation, semestre, nom_cours, credits, id_enseignant, cm, td, tp, alt, prp, referentiel, descriptif,
code_ue, code_etape_cours) SELECT pain_cours.id_cours as id_cours_prev,
pain_formation.id_formation, pain_cours.semestre,
pain_cours.nom_cours, pain_cours.credits, pain_cours.id_enseignant,
pain_cours.cm, pain_cours.td, pain_cours.tp, pain_cours.alt, pain_cours.prp, pain_cours.referentiel,
pain_cours.descriptif, pain_cours.code_ue, pain_cours.code_etape_cours FROM pain_cours,
pain_formation, pain_sformation WHERE '.$cond;

    if (!$link->query($q)) {
	errmsg("erreur avec la requete :\n".$q."\n".$link->error);
    }
    /* insertion des associations tagscours */
    $q = 'INSERT IGNORE INTO pain_tagscours (id_tag, id_cours) SELECT pain_tagscours.id_tag, pain_cours.id_cours FROM pain_tagscours, pain_cours, pain_formation, pain_sformation WHERE pain_cours.modification > (NOW() - INTERVAL 90 SECOND) AND pain_cours.id_cours_prev = pain_tagscours.id_cours AND pain_formation.id_formation = pain_cours.id_formation AND pain_sformation.id_sformation = pain_formation.id_sformation AND pain_sformation.annee_universitaire = '.$annee_cible.' AND pain_cours.id_cours_prev IN (SELECT pain_cours.id_cours FROM pain_cours, pain_formation, pain_sformation WHERE '.$cond.")";

    if (!$link->query($q)) {
	errmsg("erreur avec la requete :\n".$q."\n".$link->error);
    }

/* insertion des associations collectionscours */
    $q = 'INSERT IGNORE INTO pain_collectionscours (id_collection, id_cours) SELECT pain_collection.id_collection, pain_cours.id_cours FROM pain_collection, pain_collectionscours, pain_cours, pain_formation, pain_sformation WHERE pain_cours.modification > (NOW() - INTERVAL 90 SECOND) AND pain_cours.id_cours_prev = pain_collectionscours.id_cours AND pain_formation.id_formation = pain_cours.id_formation AND pain_sformation.id_sformation = pain_formation.id_sformation AND pain_sformation.annee_universitaire = '.$annee_cible.' AND pain_cours.id_cours_prev IN (SELECT pain_cours.id_cours FROM pain_cours, pain_formation, pain_sformation WHERE '.$cond.') AND pain_collection.id_collection_prev = pain_collectionscours.id_collection AND pain_collection.annee_universitaire = '.$annee_cible;

    if (!$link->query($q)) {
	errmsg("erreur avec la requete :\n".$q."\n".$link->error);
    }
    $profondeur -= 1;
}


/* tranches rendues anonymes */
if (1 == $profondeur) {

 if ($type == "annee") {
	$cond = "annee_universitaire = ".$annee_cible."
                 AND pain_formation.id_sformation =
                     pain_sformation.id_sformation
                 AND pain_cours.id_formation =
                     pain_formation.id_formation
                 AND pain_tranche.id_cours =
                     pain_cours.id_cours_prev ";
    } else if ($type == "sformation") {
	$cond = "pain_formation.id_sformation = ".$id_new."
                 AND pain_sformation.id_sformation =  ".$id_new
/*la derniere clause juste pour eviter que le resultat soit multipliee
 * par le nb de sformations ! */
              ." AND pain_cours.id_formation =
                     pain_formation.id_formation
                 AND pain_tranche.id_cours =
                     pain_cours.id_cours_prev ";
    }

    /*Pas de mise à jour des services des intervenants de tranches */


    /* insertion de nouvelles tranches anonymes */
    $q = 'INSERT INTO pain_tranche (id_cours, id_enseignant, groupe, cm, td, tp, alt, prp, referentiel, type_conversion, remarque, htd, declarer) SELECT
pain_cours.id_cours, 3, pain_tranche.groupe, pain_tranche.cm, pain_tranche.td, pain_tranche.tp, pain_tranche.alt, pain_tranche.prp, pain_tranche.referentiel, pain_tranche.type_conversion, pain_tranche.remarque, pain_tranche.htd, "" FROM pain_tranche, pain_cours,
pain_formation, pain_sformation WHERE '.$cond;
    error_log($q);

    if (!$link->query($q)) {
	errmsg("erreur avec la requete :\n".$q."\n".$link->error);
    }
}

/* tranches avec noms des intervenants */
if ($profondeur == 2) {

 if ($type == "annee") {
	$cond = "annee_universitaire = ".$annee_cible."
                 AND pain_formation.id_sformation =
                     pain_sformation.id_sformation
                 AND pain_cours.id_formation =
                     pain_formation.id_formation
                 AND pain_tranche.id_cours =
                     pain_cours.id_cours_prev ";
    } else if ($type == "sformation") {
	$cond = "pain_formation.id_sformation = ".$id_new."
                 AND pain_sformation.id_sformation =  ".$id_new
/*la derniere clause juste pour eviter que le resultat soit multipliee
 * par le nb de sformations ! */
              ." AND pain_cours.id_formation =
                     pain_formation.id_formation
                 AND pain_tranche.id_cours =
                     pain_cours.id_cours_prev ";
    }

    /* mise à jour des services des responsables de tranches */

    $q = $qmajservices." (SELECT DISTINCT pain_tranche.id_enseignant FROM pain_sformation, pain_formation, pain_cours, pain_tranche WHERE ". $cond.') ';
    if (!$link->query($q)) {
	errmsg("erreur avec la requete :\n".$q."\n".$link->error);
    }

    /* insertion de nouvelles tranches */
    $q = 'INSERT INTO pain_tranche (id_cours, id_enseignant, groupe, cm, td, tp, alt, prp, referentiel, type_conversion, remarque, htd, declarer) SELECT
pain_cours.id_cours, pain_tranche.id_enseignant, pain_tranche.groupe, pain_tranche.cm, pain_tranche.td, pain_tranche.tp, pain_tranche.alt, pain_tranche.prp, pain_tranche.referentiel, pain_tranche.type_conversion, pain_tranche.remarque, pain_tranche.htd, pain_tranche.declarer FROM pain_tranche, pain_cours,
pain_formation, pain_sformation WHERE '.$cond;
    error_log($q);

    if (!$link->query($q)) {
	errmsg("erreur avec la requete :\n".$q."\n".$link->error);
    }
    $profondeur -= 1;
}
}


if (isset($_GET["type"])) {
    $type = getclean("type");
} else {
    errmsg('erreur du script (type manquant).');
}
if (isset($_GET["id"])) {
    $id = getnumeric("id");
} else {
    errmsg('erreur du script (id source manquant).');
}
if (isset($_GET["id_cible"])) {
    $id_cible = getnumeric("id_cible");
} else {
    errmsg('erreur du script (id_cible manquant).');
}
if (isset($_GET["profondeur"])) {
    $profondeur = getnumeric("profondeur");
} else {
    errmsg('erreur du script (profondeur de copie manquante).');
}
json_copy($type, $id, $id_cible, $profondeur);

echo '{"ok": "ok"}';
?>