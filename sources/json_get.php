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
// require_once("inc_functions.php");


if (isset($_GET["type"])) {
    $readtype = getclean("type");
    if ($readtype == "cours") {
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
    } else {
	die('{"error": "type indefini"}');
    }
} else {
    $type = "cours";
    $par = "formation";
}

if (isset($_GET["id_parent"])) {
    $id_par = getclean("id_parent");
    $qcours = "SELECT pain_$type.*,  
                      pain_enseignant.prenom,
                      pain_enseignant.nom
             FROM pain_$type, pain_enseignant 
             WHERE `id_$par` = $id_par
             AND pain_$type.id_enseignant = pain_enseignant.id_enseignant
             $order";
    $rcours = mysql_query($qcours) 
	or die("Échec de la requête sur la table $type".$qcours."\n".mysql_error());
    $arr = array();
    while ($cours = mysql_fetch_object($rcours)) {
	$arr[] = $cours;
    }
    print json_encode($arr);
} else if (isset($_GET["id"])) {
    $id = getclean("id");
    $qcours = "SELECT \"$type\" AS type,
                      $id AS id,
                      pain_$type.*,
                      pain_enseignant.prenom,
                      pain_enseignant.nom
             FROM pain_$type, pain_enseignant 
             WHERE `id_$type` = $id
             AND pain_$type.id_enseignant = pain_enseignant.id_enseignant";
    $rcours = mysql_query($qcours) 
	or die("Échec de la requête sur la table $type".$qcours."\n".mysql_error());
    $arr = array();
    while ($cours = mysql_fetch_object($rcours)) {
	$arr[] = $cours;
    }
    print json_encode($arr);
}
?>