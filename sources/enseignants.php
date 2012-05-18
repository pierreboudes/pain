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
require_once("inc_headers.php"); /* pour en-tete et pied de page */
require_once("inc_functions.php");

function ig_tablecategorie($id, $nom) {
    echo '<table id="tablecat_'.$id.'" class="categorie">';
    echo '<tr id="categorie_'.$id.'" class="categorie">';
    echo '<td class="laction"><div id="basculeCat_'.$id.'" class="basculeOff"></div></td>';
    echo '<th class="titre">'.$nom.'</th>';
    echo '<th class="action"></th>';    
    echo '</tr>';
    echo '</table>';
}

/**
crée tout le code html initial de la page enseignants.
*/
function enseignants_php() {
    $user = authentication();
    $annee = annee_courante();
    
    entete("les enseignants","pain_enseignants.js");
    include("menu.php");
    include("box_enseignants.html");

    /* affichage des catégories */
    $r = lister_categories();
    while ($cat = $r->fetch_assoc()) {
	ig_tablecategorie($cat["id"], $cat["nom_long"]);
    }

    include("skel_enseignants.html");
    piedpage();
}

enseignants_php();
?>
