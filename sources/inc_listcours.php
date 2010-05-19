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

/* Les totaux globalement */
echo '<div id="annee_'.$annee.'">';
echo '<table class="super">';
echo '<tr class="imgformation">';
echo '<td  class="imgformation">';
echo '<div class="imgformation" id="imgentete_'.$annee.'">';
echo '</div></td></tr>';
echo '<tr class="entete" id="entete_'.$annee.'"><td>';
//action_histodesformations();
$tot = htdtotaux($annee);
echo "Ensemble des formations de l'année $annee-".($annee+1).".";
echo ' <span class="totaux">';
//ig_htd($tot);
ig_totauxenpostes($tot);
echo "</span>\n";
echo '</td></tr></table>';

/* Le grand tableau des formations */

$rsformation = list_superformations($annee);

while($sformation = mysql_fetch_array($rsformation)) 
/* pour chaque super formation */
{
    $id_sformation = $sformation["id_sformation"];
    $totaux=htdsuper($id_sformation);

    echo '<table class="super" id="tablesuper_'.$id_sformation.'">';   
    /* affichage de la super formation */

    echo '<tr class="imgformation">';
    echo '<td  class="imgformation" colspan="4">';
    echo '<div class="imgformation" id="imgsformation_'.$id_sformation.'">';
    echo '</div></td></tr>';

    echo '<tr class="super" id="sformation_'.$id_sformation.'">';
    echo '<td class="laction">';
    debug_show_id($id_sformation);
    action_basculersuper($id_sformation);
    // TODO action_histodescours($id_sformation);
    echo '</td>';
    echo '<td class="intitule">';
    echo $sformation["nom"];	
    /* affichage du responsable de la super formation */
    echo '</td>';	
    echo ' <td class="enseignant">';
    ig_responsable($sformation["id_enseignant"]);
    echo '</td>';	
    echo ' <td class="totaux">';
//    ig_htd($totaux);
    ig_totauxenpostes($totaux);
    echo '</td>';	
    echo "</tr>\n";
    if (0):
    /* liste des annee de formation */
    $rformation = list_formations($id_sformation);

    while($formation = mysql_fetch_array($rformation)) 
    /* pour chaque annee de formation */
    {
	$id_formation = $formation["id_formation"];
	$totaux=htdformation($id_formation);
	echo '<tr class="imgformation">';
	echo '<td colspan="4" class="imgformation">';
	echo '<div class="imgformation" id="imgformation'.$id_formation.'">';
	echo '</div></td></tr>';
	echo '<tr class="formation" id="formation_'.$id_formation.'">';
	echo '<td class="laction">';
	debug_show_id($id_formation);    
	action_basculerformation($id_formation);
	action_histodescours($id_formation);
	echo '</td>';
	echo '<td class="nom">'; // colspan="11"
	echo '<span class="nomformation" id="nomformation'.$id_formation.'">';
	echo $formation["nom"]." ".$formation["annee_etude"]." ";
	echo $formation["parfum"]."</span>";
	echo '</td>';	
	echo ' <td class="enseignant">';	
	/* affichage du responsable de la formation */
	ig_responsable($formation["id_enseignant"]);
	echo '</td><td class="totaux">';
	ig_totauxenpostes($totaux);
//	ig_htd($totaux);
	echo '</td>';	
	echo "</tr>\n";
// 	echo '<tr class="sousformations" style="display: none;"><td colspan="2">';
// 	echo '<table class="formations" id="tableformation_'.$id_formation.'">'; 
// 	echo '</table>'."\n";
// 	echo '</td></tr>';
    } /* fin while formation */
    endif;
    echo '</table>';
} /* fin while superformation */
echo '</div>';
?>
