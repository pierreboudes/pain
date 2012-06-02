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
$user = authentication();
$annee = get_and_set_annee_menu();
require_once("inc_headers.php"); /* pour en-tete et pied de page */
require_once('utils.php');

entete("gestion des enseignements et des services", "pain_admin.js");
include("menu.php");

/**
crée tout le code html initial de la page admin.
 */
function admin_php() {
    include("box_admin.html");
    echo '<div id="vueadmin"></div>';
    include("skel_admin.html");
    piedpage();
}

/* génération de la page */
admin_php();
?>
