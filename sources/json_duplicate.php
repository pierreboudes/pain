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
	"id_cours","id_enseignant", "groupe", "cm", "td", "tp",
	"alt", "type_conversion", "remarque", "htd", "descriptif"
	),
    "choix" => array(
	"id_enseignant", "choix", "htd", "cm", "td", "tp", "alt"
	)
    );

//print_r($champs);
//print_r($_GET);


if (isset($_GET["type"])) {
    $readtype = getclean("type");
    if ($readtype == "tranche") {
	$type = "tranche";
	$par  = "cours";
    } else {
	errmsg("type indéfini");
    }
} else {
    errmsg('erreur du script (type manquant).');
}

if (isset($_GET["id"])) {
    $id = getclean("id");
    if (!peutediter($type,$id,NULL)) { 
	errmsg("droits insuffisants.");
    }

    $strset = implode(", ", $champs[$type]);
    if ($i = array_search("groupe",$champs[$type])) {
	$champs[$type][$i] = "groupe + 1";
    }
    $strsetsource = implode(", source.", $champs[$type]);
    $query = "INSERT INTO pain_${type} ($strset) 
              SELECT $strsetsource FROM pain_${type} as source
              WHERE source.id_${type} = $id";
    
    /* requete */

    if (!mysql_query($query)) {
	errmsg("erreur avec la requete :\n".$query."\n".mysql_error());
    } 
    
    /* affichage de la nouvelle entree en json */
    $_GET["id"] = mysql_insert_id();
    unset($_GET["id_parent"]);
    include("json_get.php");
} else {
    errmsg('erreur du script (identifiant manquant).');
}
?>