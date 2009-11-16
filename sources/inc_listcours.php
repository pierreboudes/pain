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
require_once("inc_connect.php");
require_once("inc_functions.php");


/* Les totaux globalement */
echo '<table class="formations">';
echo '<tr class="entete" id="entete"><td>';
action_histodesformations();
$tot = htdtotaux("2009");
echo "Ensemble des formations de l'année 2009. ";
echo ' <span class="totaux">';
ig_htd($tot);
echo "</span>\n";
echo '</td></tr></table>';

/* Le grand tableau des formations */

$rformation = list_formations();

while($formation = mysql_fetch_array($rformation)) /* pour chaque formation */
{
    $id_formation = $formation["id_formation"];
    $totaux=htdformation($id_formation);
    
    echo '<table class="formations" id="tableformation'.$id_formation.'">';
    /* affichage de la formation */
    echo '<tr class="imgformation">';
    echo '<td colspan="11" class="imgformation">';
    echo '<div class="imgformation" id="imgformation'.$id_formation.'">';
    echo '</div></td></tr>';
    echo '<tr class="formation" id="formation'.$id_formation.'">';
    echo '<td class="intitule" colspan="11">';
    action_basculerformation($id_formation);
    action_histodescours($id_formation);
    echo $formation["nom"]." ".$formation["annee_etude"]." ";
    echo $formation["parfum"]." ";

    /* affichage du responsable de la formation */
    echo "responsable : ";
    ig_responsable($formation["id_enseignant"]);
    echo ' <span class="totaux">';
    ig_htd($totaux);
    echo '</span>';
    echo '</td>';

    echo "</tr>\n";

    /* affichage des cours de la formation */

    /* légende */    
    ig_legendecours($id_formation);

    /* formulaire d'ajout d'un cours dans la formation */
    echo '<tr class="formcours" id="formcours'.$id_formation.'"><td colspan="11">'."\n";
    echo '<form method="post" id="fformation'.$id_formation.
         '" class="formcours" name="cours" action="">';
    ig_formcours($id_formation);
    echo '</form>'."\n";
    echo '</td></tr>'."\n";

   
    $rcours = list_cours($id_formation);

    while ($cours = mysql_fetch_array($rcours)) /* pour chaque cours */
    {
	$id_cours = $cours["id_cours"];
	echo '<tr class="imgcours">';
	echo '<td colspan="11" class="imgcours">';
	echo '<div class="imgcours" id="imgcours'.$id_cours.'">';
	echo '</div></td></tr>'."\n";
	echo '<tr class="cours"';
        action_dblcmodifiercours($id_cours);
	echo '>';
	ig_cours($cours);
	echo '</tr>'."\n";
    }
echo '</table>'."\n";

} /* fin while formation */

?>

<p>
<a href="http://validator.w3.org/check?uri=referer"><img
    src="http://www.w3.org/Icons/valid-xhtml10-blue"
    alt="Valid XHTML 1.0 Transitional" height="31" width="88" /></a>
    </p>