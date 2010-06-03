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
$annee = annee_courante();

require_once("inc_connect.php");
require_once("utils.php");
// require_once("inc_functions.php");
if (isset($_GET["annee_universitaire"])) {
    $annee = getclean("annee_universitaire");
}

if (isset($_GET["type"])) {
    $readtype = getclean("type");
    if ($readtype == "formation") {
	$type = "formation";
	$par = "sformation";
	$order = "ORDER BY numero ASC";
    } else if ($readtype == "cours") {
	$type = "cours";
	$par = "formation";
	$order = "ORDER BY semestre, nom_cours ASC";
    } else if ($readtype == "tranche") {
	$type = "tranche";
	$par  = "cours";
	$order = "ORDER by groupe ASC";
    } else if ($readtype == "choix") {
	$type = "choix";
	$par = "cours";
	$order = "ORDER by modification ASC";
    } else if ($readtype == "enseignant") {
	$type = "enseignant";
	$par = "categorie";
	$requete = "SELECT *,
                    \"$type\" AS type,
                    id_$type AS id
                    FROM pain_enseignant";
	if (isset($_GET['id_parent'])) {
	    $id_par = $_GET['id_parent'];
	    $requete .= " WHERE categorie = $id_par ";	    
        } else if (isset($_GET["id"])) {
	    $id = $_GET['id'];
	    $requete .= " WHERE id_enseignant = $id ";	    
	}
	$requete .= "ORDER BY nom, prenom ASC";
    } else if ($readtype == "longchoix") {
	$type = "choix";
	$requete = "SELECT pain_choix.*,
                           pain_choix.id_choix AS id_longchoix,
                           pain_choix.id_choix AS id,
                           pain_cours.nom_cours, 
                           pain_cours.id_cours,
                           pain_cours.semestre,
                           pain_formation.nom,
                           pain_formation.annee_etude,
                           pain_formation.parfum,
                           pain_sformation.annee_universitaire,
                           \"long$type\" AS type
                     FROM  pain_choix, pain_cours, pain_formation, pain_sformation ";
	if (isset($_GET['id_parent'])) {
	    $id_par = $_GET['id_parent'];
	    $requete .= " WHERE pain_choix.id_enseignant = $id_par ";	    
        } else if (isset($_GET["id"])) {
	    $id = $_GET['id'];
	    $requete .= " WHERE pain_choix.id_choix = $id ";	    
	}
	$requete .="AND pain_cours.id_cours = pain_choix.id_cours
                    AND pain_formation.id_formation = pain_cours.id_formation
                    AND pain_sformation.id_sformation = pain_formation.id_sformation
                    AND pain_sformation.annee_universitaire = $annee
                    ORDER by pain_cours.semestre ASC, pain_formation.numero ASC";
    } else if ($readtype == "longtranche") {
	$type = "tranche";
	$requete = "SELECT pain_tranche.*, 
                           pain_tranche.id_tranche as id_longtranche,
                           pain_tranche.id_tranche AS id,
                           pain_cours.nom_cours, 
                           pain_cours.id_cours, 
                           pain_formation.nom,
                           pain_formation.annee_etude,
                           pain_formation.parfum,
                           pain_sformation.annee_universitaire,
                           \"long$type\" AS type
                     FROM pain_tranche, pain_cours, pain_formation, pain_sformation ";
	if (isset($_GET['id_parent'])) {
	    $id_par = getnumclean('id_parent');
	    $requete .= " WHERE pain_tranche.id_enseignant = $id_par ";	    
        } else if (isset($_GET["id"])) {
	    $id = getnumclean('id');
	    $requete .= " WHERE pain_tranche.id_tranche = $id ";	    
	}
	$requete .="AND pain_cours.id_cours = pain_tranche.id_cours
                    AND pain_formation.id_formation = pain_cours.id_formation
                    AND pain_sformation.id_sformation = pain_formation.id_sformation
                    AND pain_sformation.annee_universitaire = $annee
                    ORDER by  pain_cours.semestre ASC, pain_formation.numero ASC";
    } else {
	errmsg("erreur de script (type inconnu)");
    }
} else {
    errmsg("erreur de script (type non renseigné)");
}

if (isset($_GET["id_parent"])) {
    $id_par = getclean("id_parent");
    if (!isset($requete)) {
	$requete = "SELECT pain_$type.*,  
                      pain_enseignant.prenom AS prenom_enseignant,
                      pain_enseignant.nom AS nom_enseignant
             FROM pain_$type, pain_enseignant 
             WHERE `id_$par` = $id_par
             AND pain_$type.id_enseignant = pain_enseignant.id_enseignant
             $order";
    }
    $resultat = mysql_query($requete) 
	or die("Échec de la requête sur la table $type".$requete."\n".mysql_error());
    $arr = array();
    while ($element = mysql_fetch_object($resultat)) {
	$arr[] = $element;
    }
    print json_encode($arr);
} else if (isset($_GET["id"])) {
    $id = getclean("id");
    if (!isset($requete)) {
	$requete = "SELECT \"$type\" AS type,
                      $id AS id,
                      pain_$type.*,
                      pain_enseignant.prenom AS prenom_enseignant,
                      pain_enseignant.nom AS nom_enseignant
             FROM pain_$type, pain_enseignant 
             WHERE `id_$type` = $id
             AND pain_$type.id_enseignant = pain_enseignant.id_enseignant";
    }
    $resultat = mysql_query($requete) 
	or die("Échec de la requête sur la table $type".$requete."\n".mysql_error());
    $arr = array();
    while ($element = mysql_fetch_object($resultat)) {
	$arr[] = $element;
    }
    print json_encode($arr);
} else {
    errmsg("Erreur de script client (ni id ni parent)");
}
?>