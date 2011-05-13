<?php /* -*- coding: utf-8 -*-*/
/* Pain - outil de gestion des services d'enseignement        
 *
 * Copyright 2009 Pierre Boudes, département d'informatique de l'institut Galilée.
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

if (isset($_GET["type"])) {
    $readtype = getclean("type");
    if ($readtype == "annee") {	
	$type = "annee";
	$par = "annee";
    } else if ($readtype == "sformation") {	
	$type = "sformation";
	$par = "annee";
    } else if ($readtype == "formation") {	
	$type = "formation";
	$par = "sformation";	
    } else if ($readtype == "cours") {	
	$type = "cours";
	$par = "formation";
    } else if ($readtype == "tranche") {
	$type = "tranche";
	$par  = "cours";
    } else if ($readtype == "enseignant") {
	$type = "enseignant";
    } else if ( ($readtype == "choix") || ($readtype == "longchoix")) {
	$type = "choix";
	$par = "cours";
    } else if ($readtype == "service") {
	$type = "service";
    } else {
	errmsg("type indéfini");
    }
} else {
    errmsg('erreur du script (type manquant).');
}
if (isset($_GET["id"])) {
    $id = getclean("id");
} else {
    errmsg('erreur du script (id source manquant).');    
}
if (isset($_GET["id_cible"])) {
    $id_cible = getclean("id_cible");
} else {
    errmsg('erreur du script (id_cible manquant).');    
}
if (isset($_GET["profondeur"])) {
    $profondeur = getclean("profondeur");
} else {
    errmsg('erreur du script (profondeur de copie manquante).');    
}

/* récupérer l'année cible (pour les services) */
if ($type == "annee") {
    $annee_cible = $id_cible;

    /* verifions toutefois que l'annee est bien vide */
    $q = "SELECT COUNT(*) AS tot FROM pain_sformation WHERE annee_universitaire = ".$id_cible; 
    if (!($r = mysql_query($q))) {
	errmsg("erreur avec la requete :\n".$q."\n".mysql_error());
    }
    if (!($ligne = mysql_fetch_array($r))) {
	errmsg("cible non existante !");
    }
    if ($ligne["tot"] > 0) {
	errmsg("l'annee ciblée ne doit pas contenir de formations");
    }
} else if ($type == "sformation") {
    $annee_cible = $id_cible;

/*
    $q = "SELECT annee_universitaire FROM pain_sformation WHERE id_sformation = ".$id_cible; 
    if (!($r = mysql_query($q))) {
	errmsg("erreur avec la requete :\n".$q."\n".mysql_error());
    }
    if (!($ligne = mysql_fetch_array($r))) {
	errmsg("cible non existante !");
    }
    $annee_cible = $ligne["annee_universitaire"];
*/
} else {
    errmsg("type non géré");
}

$qmajservices = 'REPLACE INTO pain_service
                 (id_enseignant, annee_universitaire, categorie, service_annuel, tmpnom)
                 SELECT 
                 pain_enseignant.id_enseignant,
                 "'.$annee_cible.'",
                 pain_enseignant.categorie,  
                 pain_enseignant.service,
                 CONCAT(nom," ",prenom)
                 FROM pain_enseignant
                 WHERE pain_enseignant.id_enseignant IN ';


/* supers formations */
if ($profondeur > 0) {

    if ($type == "annee") {
	$cond = 'annee_universitaire =  '.$id;
    } else if ($type == "sformation") {
	$cond = "id_sformation = ".$id;
    }

    /* mise à jour des services des responsables de sformation */

    $q = $qmajservices." (SELECT DISTINCT id_enseignant FROM pain_sformation WHERE ". $cond.') ';
    if (!mysql_query($q)) {
	errmsg("erreur avec la requete :\n".$q."\n".mysql_error());
    }


    /* insertion de la ou des nouvelles sformation */
    $q = 'INSERT INTO pain_sformation (id_sformation_prev, annee_universitaire, id_enseignant, nom, numero) SELECT `id_sformation` as id_sformation_prev, "'.$annee_cible.'", `id_enseignant`, `nom`, `numero` FROM pain_sformation WHERE '.$cond;

    if (!mysql_query($q)) {
	errmsg("erreur avec la requete :\n".$q."\n".mysql_error());
    }

    if ($type == "sformation") {
	$id_new = mysql_insert_id();
    }
    $profondeur -= 1;    
}

/* formations */
if ($profondeur > 0) {

    if ($type == "annee") {
	$cond = "annee_universitaire =  ".$annee_cible."  AND 
                 pain_formation.id_sformation  = id_sformation_prev ";
    } else if ($type == "sformation") {
	$cond = "pain_sformation.id_sformation = ".$id_new."
                 AND pain_formation.id_sformation  = id_sformation_prev";
    }

    /* mise à jour des services des responsables de formations */
    $q = $qmajservices." (SELECT DISTINCT pain_formation.id_enseignant FROM  pain_sformation, pain_formation WHERE ". $cond.') ';
    if (!mysql_query($q)) {
	errmsg("erreur avec la requete :\n".$q."\n".mysql_error());
    }

    /* insertion de la ou des nouvelles formation */
    $q = 'INSERT INTO pain_formation (id_formation_prev, id_sformation, numero, nom, annee_etude, parfum, id_enseignant) SELECT pain_formation.id_formation as id_formation_prev, pain_sformation.id_sformation as id_sformation, pain_formation.numero, pain_formation.nom, pain_formation.annee_etude, pain_formation.parfum, pain_formation.id_enseignant FROM pain_formation, pain_sformation WHERE '.$cond;

    if (!mysql_query($q)) {
	errmsg("erreur avec la requete :\n".$q."\n".mysql_error());
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
    if (!mysql_query($q)) {
	errmsg("erreur avec la requete :\n".$q."\n".mysql_error());
    }

    /* insertion des nouveaux cours */
    $q = 'INSERT INTO pain_cours (id_cours_prev, id_formation, semestre, nom_cours, credits, id_enseignant, cm, td, tp, alt, descriptif,
code_geisha) SELECT pain_cours.id_cours as id_cours_prev,
pain_formation.id_formation, pain_cours.semestre,
pain_cours.nom_cours, pain_cours.credits, pain_cours.id_enseignant,
pain_cours.cm, pain_cours.td, pain_cours.tp, pain_cours.alt,
pain_cours.descriptif, pain_cours.code_geisha FROM pain_cours,
pain_formation, pain_sformation WHERE '.$cond;

    if (!mysql_query($q)) {
	errmsg("erreur avec la requete :\n".$q."\n".mysql_error());
    }    
    $profondeur -= 1;    
}

/* tranches */
if ($profondeur > 0) {

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
    if (!mysql_query($q)) {
	errmsg("erreur avec la requete :\n".$q."\n".mysql_error());
    }

    /* insertion de nouvelles tranches */
    $q = 'INSERT INTO pain_tranche (id_cours, id_enseignant, groupe, cm, td, tp, alt, type_conversion, remarque, htd) SELECT 
pain_cours.id_cours, pain_tranche.id_enseignant, pain_tranche.groupe, pain_tranche.cm, pain_tranche.td, pain_tranche.tp, pain_tranche.alt, pain_tranche.type_conversion, pain_tranche.remarque, pain_tranche.htd FROM pain_tranche, pain_cours,
pain_formation, pain_sformation WHERE '.$cond;
    error_log($q);

    if (!mysql_query($q)) {
	errmsg("erreur avec la requete :\n".$q."\n".mysql_error());
    }    
    $profondeur -= 1;    
}

echo '{"ok": "ok"}';
?>