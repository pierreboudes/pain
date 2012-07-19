<?php /* -*- coding: utf-8 -*-*/
/* Pain - outil de gestion des services d'enseignement        
 *
 * Copyright 2009-2012 Pierre Boudes,
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
// $user = no_auth(); /* pas d'authentification */
$user = weak_auth(); /* accès sans autorisation */
$annee = get_and_set_annee_menu();

/**
retourne des entrées de type $readtype, prises dans la base, sélectionnées par le contexte d'une requête HTTP/GET.

Les entrées sont éventuellement calculées par jointures et aggrégats. La sélection dépend soit de l'identifiant de l'entrée fourni par le contexte d'une requête HTTP/GET, ou bien d'un identifiant de groupe d'entrées ou bien de l'année courante. 
 */
function json_get_php($annee, $readtype) {
    global $link;
    if ($readtype == "sformation") {
	$type = "sformation";
	$par = "annee_universitaire";
	if (isset($_GET["id_parent"])) {
	    $annee = getnumeric("id_parent");
	}
	$requete = "SELECT nom,
                           id_sformation,
                           id_sformation AS id
                    FROM pain_sformation
                    WHERE annee_universitaire = $annee
                    ORDER BY numero ASC";
    } else if ($readtype == "formation") {
	if (isset($_GET['id_parent'])) {
	    $type = "formation";
	    $par = "id_sformation";
	    $order = "ORDER BY numero ASC";
	} else {
	    errmsg("le type semestre nécessite un id_parent");
	}
    } else if ($readtype == "collection") {
	if (isset($_GET['id_parent'])) {
	    $id_par =  getnumeric("id_parent");
	    $type = "collection";
	    $requete = "SELECT pain_collection.*,                    
                       \"$type\" AS type,
                       id_$type AS id
                       FROM pain_collection 
                       WHERE pain_collection.id_sformation = $id_par 
                       ORDER BY nom_collection ASC";
        } else {
	    errmsg("le type collection nécessite un id_parent");
	}
    } else if ($readtype == "semestre") {
	if (isset($_GET['id_parent'])) {
	    $type = "semestre";
	    $id_par =  getnumeric("id_parent");
	    $requete = "SELECT distinct pain_cours.semestre as semestre
                        FROM pain_cours, pain_formation";
	    $requete .= " WHERE pain_formation.id_sformation = $id_par
                          AND pain_cours.id_formation = pain_formation.id_formation";	    
	    $requete .= " ORDER BY semestre ASC";
        } else {
	    errmsg("le type semestre nécessite un id_parent");
	}
    } else {
	errmsg("erreur de script (type inconnu)");
    }

   if (isset($_GET["id_parent"])) {
       $id_par = getnumeric("id_parent");
       if (!isset($requete)) {
	   $requete = "SELECT 
                      pain_$type.*,
                       \"$type\" AS type, 
                      pain_$type.id_$type AS id
                      FROM pain_$type
                      WHERE pain_$type.$par = $id_par ";
	   $requete .= $order;
       }
       $resultat = $link->query($requete) 
	   or die("Échec de la requête sur la table $type".$requete."\n".$link->error);
       $arr = array();
       while ($element = $resultat->fetch_object()) {
	   $arr[] = $element;
       }
       return $arr;
   } else {
       errmsg("Erreur de script client (id_parent absent)");
   }
}

if (isset($_GET["annee_universitaire"])) {
    $annee = getnumeric("annee_universitaire");
}

if (isset($_GET["type"])) {
    $readtype = getclean("type");
     
    $out = json_get_php($annee, $readtype);
    print json_encode($out);
} else {
    errmsg("erreur de script (type non renseigné)");
}

?>