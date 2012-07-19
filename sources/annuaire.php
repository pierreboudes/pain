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

require_once("inc_headers.php"); /* pour en-tete et pied de page */
require_once("inc_annuairefunc.php");

entete("Annuaire des formations","pain_annuaire.js");
if ($user != NULL) {
    include("menu.php");
} else {
    echo '<ul id="menu" style="text-align:right;"><li><a href="logout.php">logout</a></li></ul>';
    echo '<h1>Annuaire public de Pain</h1>';
}

/* identifiant de formation en provenance du formulaire */
list($id_formation, $semestre) = annuaire_php_form();

/* affichage des tableaux des cours */
annuaire_php();

piedpage();
?>

