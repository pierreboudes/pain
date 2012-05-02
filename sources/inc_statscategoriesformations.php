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
authrequired();

require_once("inc_connect.php");
require_once("inc_functions.php");

$query = "SELECT id_sformation, nom FROM pain_sformation WHERE annee_universitaire = $annee ORDER BY numero";
$res = mysql_query($query) or die("BD Impossible d'effectuer la requête: $query");
$formation = mysql_fetch_assoc($res);

/* TODO : mettre les catégorie et leurs légendes (longue, courte, détaillée) dans une table. passer la catégorie 29 à 5. */

$tab = array(
    array("Catégorie"),
    array("Permanents"),
    array("Non-permanents"),
    array("Galilée"),
    array("Paris&nbsp;13"),
    array("Autres"),
    array("Vacants"),
    array("ss totaux"),
    array("Annulés"),
    array("Aidés")
    );

mysql_data_seek($res,0);
$i = 1;

while ($formation = mysql_fetch_assoc($res)) {
    $tab[0][$i] = $formation["nom"]; /* nom de la super formation */
    $sfid = $formation["id_sformation"];

    $stat = stats_sform($sfid);

    $tab[1][$i] = isset($stat[2])?$stat[2]:0;
    $tab[2][$i] = isset($stat[3])?$stat[3]:0;    
    $tab[3][$i] = isset($stat[4])?$stat[4]:0;    
    $tab[4][$i] = isset($stat[6])?$stat[6]:0;    
    $tab[5][$i] = (isset($stat[5])?$stat[5]:0) + (isset($stat[29])?$stat[29]:0);
    $tab[6][$i] = isset($stat[23])?$stat[23]:0;
    $tab[7][$i] = 0;
    $tab[8][$i] = isset($stat[1])?$stat[1]:0;
    $tab[9][$i] = isset($stat[22])?$stat[22]:0;

    ++$i;
}
$nbsf = $i - 1;
$nbcat = 9;
/* ajout des totaux par formation */
$tab[$nbcat + 1][0] = "totaux";
for ($i = 1; $i <= $nbsf; ++$i) {
    $sum = 0;
    for ($j = 1; $j <= $nbcat; ++$j) {
	$sum = $sum + $tab[$j][$i];
    }
    $tab[$nbcat + 1][$i] = $sum;
}
/* ajout des sous-totaux par formation */
$tab[$nbcat + 1][0] = "totaux";
for ($i = 1; $i <= $nbsf; ++$i) {
    $sum = 0;
    for ($j = 1; $j <= 6; ++$j) {
	$sum = $sum + $tab[$j][$i];
    }
    $tab[7][$i] = $sum;
}

/* ajout des totaux par categorie, et total global */
$tab[0][$nbsf + 1] = "totaux"; 
for ($j = 1; $j <= $nbcat + 1; ++$j) {
    $sum = 0;
    for ($i = 1; $i <= $nbsf; ++$i) {
	$sum = $sum + $tab[$j][$i];
    }
    $tab[$j][$nbsf + 1] = $sum;
}

/* affichage du tab */
echo '<table class="stat" id="tableCatForm">';
/* les categories en ligne d'en-tete */
echo '<tr>';
for ($i = 0; $i <= $nbcat + 1; ++$i) {
    echo '<th>'.$tab[$i][0].'</th>';
}
echo '</tr>';
/* une ligne par formation */
for ($j = 1; $j <= $nbsf + 1; $j = $j + 1)
{
echo '<tr>';
echo '<th>'.$tab[0][$j].'</th>';
for ($i = 1; $i <= $nbcat + 1; ++$i) {
    if ($tab[$i][$j] > 0) {
	echo '<td>'.enpostes($tab[$i][$j]).'</td>';
    } else {
	echo '<td></td>';
    }
}
echo '</tr>';
}
echo '</table>';
/*
echo '<pre>';
print_r($tab);
echo '</pre>';
*/
?>