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
require_once("inc_functions.php");

$id_enseignant = "";

if (isset($_GET['id_enseignant'])) {
    $id_enseignant = getclean('id_enseignant');
}

if (isset($_POST['id_enseignant'])) {
    $id_enseignant = postclean('id_enseignant');
}
echo '<center><div class="infobox" style="width:200px;">';
echo '<form method="post" id="choixenseignant" class="formcours" name="enseignant" action="#">';
echo '<select name="id_enseignant" style="display:inline; width:150px;">';
ig_formselectenseignants($id_enseignant);
echo '</select>';
echo '<input type="submit" value="OK" style="display:inline;width:40px;"/>';
echo '</form>'."\n";
echo '</div></center>';

if ($id_enseignant != "") {

    $totaux = totauxinterventions($id_enseignant);

    /* Feuille de service */
    echo "<h2>Déclaration du service d'enseignement</h2>";

    $services = listeservice($id_enseignant);
    echo '<p><table class="service">';
    ig_legendeservice();
    while ($ligne = mysql_fetch_array($services)) {
	ig_ligneservice($ligne);
    }
    ig_totauxservice($totaux);
    echo '</table></p>';

    /* Details (tranche par tranche) */
    echo "<h2>Détail des interventions</h2>";

    $services = listeinterventions($id_enseignant);
    echo '<p><table class="interventions noprint">';
    echo '<tr>';
    ig_legendeintervention();
    echo '</tr>';
    while ($service = mysql_fetch_array($services)) {
	echo '<tr class="intervention">';
	ig_intervention($service);
	echo '</tr>';
    }
    echo '<tr>';
    ig_totauxinterventions($totaux);
    echo '</tr>';
    echo '</table></p>';
}
?>