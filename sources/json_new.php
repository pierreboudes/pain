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
require_once("inc_functions.php");

$champs = array(
    "cours"=> array(
	"semestre", "nom_cours", "credits", "id_enseignant",
	"cm", "td", "tp", "alt", "descriptif", "code_geisha"
	/* modification */
	),
    "tranche"=> array(
	"id_enseignant", "groupe", "cm", "td", "tp",
	"alt", "type_conversion", "remarque", "htd", "descriptif"
	),
    "choix" => array(
	"id_enseignant", "cm", "td", "tp", "alt"
	)
    );

//print_r($champs);
//print_r($_GET);


if (isset($_GET["type"])) {
    $readtype = getclean("type");
    if ($readtype == "cours") {	
	$type = "cours";
	$par = "formation";
    } else if ($readtype == "tranche") {
	$type = "tranche";
	$par  = "cours";
    } else if ($readtype == "choix") {
	$type = "choix";
	$par = "cours";
    } else {
	errmsg("type indéfini");
    }
} else {
    errmsg('erreur du script (type manquant).');
}

if (isset($_GET["id_parent"])) {
    $id_parent = getclean("id_parent");

    if (!peutediter($type,NULL,$id_parent)) { 
	errmsg("droits insuffisants.");
    }
    $set = array();

    if ($type != "enseignant") $set["id_".$par] = $id_parent;

    if (!isset($_GET["nom_cours"])) {
	$_GET["nom_cours"] = 'Donner un nom';
    }

    foreach ($champs[$type] as $field) {
	if (isset($_GET[$field])) {
	    $set[$field] = getclean($field);
	}
    };

    if ((isset($set["nom cours"])) && ("" == trim($set["nom_cours"]))) {
	errmsg("donner un nom au cours.");
    }

    /* Champs particuliers a controler */
    if ($type != "enseignant") {
	$set["id_enseignant"] = $user["id_enseignant"];
    }
    /* formation de la requete */
    $setsql = array();
    foreach ($set as $field => $val) {
	    $setsql[] = '`'.$field.'` = "'.$val.'"';
    };
    $strset = implode(", ", $setsql);
    $query = "INSERT INTO pain_${type} 
              SET $strset, modification = NOW()";
    
    /* requete */

    if (!mysql_query($query)) {
	errmsg("erreur avec la requete :\n".$query."\n".mysql_error());
    } 
    
    /* affichage de la nouvelle entree en json */
    $_GET["id"] = mysql_insert_id();
    unset($_GET["id_parent"]);
    include("json_get.php");
}
?>