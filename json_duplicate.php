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
require_once("inc_functions.php");

/**
duplique l'entrée $id de type $type en incrémentant le champs (numéro de) groupe,
et retourne l'id de la nouvelle entrée.
*/
function json_duplicate_php($type, $id) {
    global $link;
    $champs = array(
	"tranche"=> array(
	    "id_cours","id_enseignant", "groupe", "cm", "td", "tp",
	    "alt", "prp", "referentiel", "type_conversion", "remarque", "htd", "descriptif", "declarer"
	    )
	);

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

    if (!$link->query($query)) {
	errmsg("erreur avec la requete :\n".$query."\n".$link->error);
    }

    return $link->insert_id;
}

if (isset($_GET["type"])) {
    $readtype = getclean("type");
    if ($readtype == "tranche") {
	$type = "tranche";
    } else {
	errmsg("type indéfini");
    }
} else {
    errmsg('erreur du script (type manquant).');
}

if (isset($_GET["id"])) {
    $id = getnumeric("id");

    $_GET["id"] = json_duplicate_php($type, $id);

    /* affichage de la nouvelle entree en json */
    unset($_GET["id_parent"]);
    include("json_get.php");
} else {
    errmsg('erreur du script (identifiant manquant).');
}
?>