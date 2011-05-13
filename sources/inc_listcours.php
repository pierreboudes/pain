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
echo '<div class="annees">';
echo '<table class="super an'.$annee.'">';
echo '<tr class="imgsformation">';
echo '<td  class="imgformation">';
echo '<div class="imgformation" id="imgentete_'.$annee.'">';
echo '</div></td></tr>';
echo '<tr class="entete" id="annee_'.$annee.'">';
$tot = htdtotaux($annee);
echo '<td class="laction">';
echo '<div id="basculeannee'.$annee.'" class="basculeOff" onclick="basculerAnnee('.$annee.')">';
echo '<div></td>';
echo "<td>Ensemble des formations de l'année $annee-".($annee+1).".";
echo ' <span class="totaux">';
//ig_htd($tot);
ig_totauxenpostes($tot);
echo "</span>\n";
echo '</td></tr></table>';
?>
