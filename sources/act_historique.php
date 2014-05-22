<?php  /* -*- coding: utf-8 -*-*/
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

/** @file act_historique.php
 code HTML de l'historique des modifications d'une formation, chargé en ajax.
 */

require_once('authentication.php'); 
$user = authentication();
require_once("inc_connect.php");
require_once("inc_functions.php");

/**
 produit le code HTML de l'historique de la formation.
 */
function act_historique_php($id, $offset, $timestamp) {
    $liste = historique_de_formation($id, $offset, $timestamp);
    while ($h = $liste->fetch_assoc()) {
	++$offset;
	echo '<div class="historique">';
	ig_historique($h);
	echo '<div class="clear"></div>';
	echo '</div>';
    }
    echo '<div class="hiddenvalue">'.$offset.'</class>';
}

if (isset($_POST["id_formation"])) {
    $id = postclean("id_formation");
} else if (isset($_GET["id_formation"])) {
    $id = getnumeric("id_formation");
}  else {
    die("pas de id_formation ?");
}
$offset = 0;
if (isset($_POST["offset"])) {
    $offset = postclean("offset");
} else if (isset($_GET["offset"])) {
    $offset = getnumeric("offset");
}
$timestamp = NULL;
if (isset($_POST["timestamp"])) {
    $timestamp = postclean("timestamp");
} else if (isset($_GET["timestamp"])) {
    $timestamp = getclean("timestamp");
}

act_historique_php($id, $offset, $timestamp);
?>

