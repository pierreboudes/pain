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
authrequired();

require_once("inc_connect.php");
require_once("inc_functions.php");
require_once("inc_statsfunc.php");

/* par defaut on sert la feuille de l'utilisateur */
$id_enseignant = $user["id_enseignant"]; 
/* mais si on a un identifiant dans l'url on utilise plutot celui-ci */
if (isset($_GET['id_enseignant'])) {
    $id_enseignant = getclean('id_enseignant');
}
/* et si l'identifiant provient du formulaire, c'est plutot celui-la */
if (isset($_POST['id_enseignant'])) {
    $id_enseignant = postclean('id_enseignant');
}

/* le formulaire */
echo '<center><div class="infobox" style="width:200px;">';
echo '<form method="post" id="choixenseignant" class="formcours" name="enseignant" action="#">';
echo '<select name="id_enseignant" style="display:inline; width:150px;">';
ig_formselectenseignants($id_enseignant);
echo '</select>';
echo '<input type="submit" value="OK" style="display:inline;width:40px;"/>';
echo '</form>'."\n";
echo '</div></center>';

if ($id_enseignant != "") {
    /* annuaire */
    echo "<h2>Informations d'annuaire</h2>";

    echo '<table class="enseignants">';
    ig_legendeenseignant();
    $q = "SELECT * from pain_enseignant 
          WHERE id_enseignant = $id_enseignant";
    ($r = mysql_query($q)) 
    or die("Échec de la connexion à la base enseignant");
    if ($ens = mysql_fetch_array($r)) {
	ig_enseignant($ens);
    }
    echo '</table>';

    $totaux = totauxinterventions($id_enseignant);

    /* Feuille de service */
    echo "<h2>Déclaration du service d'enseignement</h2>";

 
    $services = listeservice($id_enseignant);
    echo '<table class="service">';
    ig_legendeservice();
    while ($ligne = mysql_fetch_array($services)) {
	ig_ligneservice($ligne);
    }
    ig_totauxservice($totaux);
    echo '</table>';
   

    /* Details (tranche par tranche) */
    echo "<h2>Détail des interventions</h2>";

    $services = listeinterventions($id_enseignant);
    echo '<table class="interventions noprint">';
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
    echo '</table>';
}

    echo '<div class="vignette">';
    ig_statsenseignant($ens);
    echo '</div>';

?>