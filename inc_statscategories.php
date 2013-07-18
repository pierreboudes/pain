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

/* TODO pain_service quelques jointures pour le service statutaire et formulaire annee */

echo '<table class="stat" id="tableCat">';
echo '<tr><th>Catégorie</th><th>nombre de personnes</th><th colspan="2">services statutaires</th><th colspan="2">services réels</th></tr>';

echo '<th>Ensemble du département</th>';
$stat = stats("COUNT(*)","pain_service WHERE (categorie = 2 OR categorie = 3) AND annee_universitaire = $annee");
echo '<th>'.$stat.'</th>';
$stat = round(stats("SUM(service_annuel)","pain_service WHERE (categorie = 2 OR categorie = 3) AND annee_universitaire = $annee"));
echo '<th>'.$stat.'HTD</th><th>'.enpostes($stat).' postes</th>'; 
$stat = round(stats("SUM(service_reel)","pain_service WHERE (categorie = 2 OR categorie = 3) AND annee_universitaire = $annee"));
echo '<th>'.$stat.'HTD</th><th>'.enpostes($stat).' postes</th>'; 
echo '</tr>';


echo '<tr><th>Permanents du département</th>';
$stat = stats("COUNT(*)","pain_service WHERE categorie = 2 AND annee_universitaire = $annee");
echo '<td>'.$stat.'</td>';
$stat = round(stats("SUM(service_annuel)","pain_service WHERE categorie = 2 AND annee_universitaire = $annee"));
echo '<td>'.$stat.'HTD</td><td>'.enpostes($stat).' postes</td>'; 
$stat = round(stats("SUM(service_reel)","pain_service WHERE categorie = 2 AND annee_universitaire = $annee"));
echo '<td>'.$stat.'HTD</td><td>'.enpostes($stat).' postes</td>'; 
echo '</tr>';
echo '<tr><th>Non permanents du département</th>';
$stat = stats("COUNT(*)","pain_service WHERE categorie = 3 AND annee_universitaire = $annee");
echo '<td>'.$stat.'</td>';
$stat = round(stats("SUM(service_annuel)","pain_service WHERE categorie = 3 AND annee_universitaire = $annee"));
echo '<td>'.$stat.'HTD</td><td>'.enpostes($stat).' postes</td>'; 
$stat = round(stats("SUM(service_reel)","pain_service WHERE categorie = 3 AND annee_universitaire = $annee"));
echo '<td>'.$stat.'HTD</td><td>'.enpostes($stat).' postes</td>'; 
echo '</tr>';


// autre id: 9 categorie: 29
$autre = round(stats("service_reel","pain_service WHERE id_enseignant = 9 AND annee_universitaire = $annee"));

echo '<tr><th>Intervenants hors département</th>';
$stat = stats("COUNT(*)","pain_service WHERE 
(categorie = 4
OR categorie = 5
OR categorie = 6)
AND service_reel > 0
AND annee_universitaire = $annee");
echo '<th>'.$stat.'</th>';
echo '<td rowspan="7" colspan="2"></td>';
$stat = round(stats("SUM(service_reel)","pain_service WHERE 
(categorie = 4
OR categorie = 5
OR categorie = 6)
AND annee_universitaire = $annee") + $autre);
echo '<th>'.$stat.'HTD</th><th>'.enpostes($stat).' postes</th>'; 
echo '</tr>';

echo '<tr><th>autres enseignants de Galilée</th>';
$stat = stats("COUNT(*)","pain_service WHERE categorie = 4 AND service_reel > 0 AND annee_universitaire = $annee");
echo '<td>'.$stat.'</td>';
//echo '<td></td>';
$stat = round(stats("SUM(service_reel)","pain_service WHERE categorie = 4 AND annee_universitaire = $annee"));
echo '<td>'.$stat.'HTD</td><td>'.enpostes($stat).' postes</td>'; 
echo '</tr>';


echo '<tr><th>enseignants de Paris 13 hors Galilée</th>';
$stat = stats("COUNT(*)","pain_service WHERE categorie = 6 AND service_reel > 0 AND annee_universitaire = $annee");
echo '<td>'.$stat.'</td>';
//echo '<td></td>';
$stat = round(stats("SUM(service_reel)","pain_service WHERE categorie = 6 AND annee_universitaire = $annee"));
echo '<td>'.$stat.'HTD</td><td>'.enpostes($stat).' postes</td>'; 
echo '</tr>';


echo '<tr><th>autres (vacataires, industriels etc.)</th>';
$stat = stats("COUNT(*)","pain_service WHERE categorie = 5 AND service_reel > 0 AND annee_universitaire = $annee");
echo '<td>'.$stat.'</td>'; 
//echo '<td></td>';
$stat = round(stats("SUM(service_reel)","pain_service WHERE categorie = 5 AND annee_universitaire = $annee"));
echo '<td>'.$stat.'HTD</td><td>'.enpostes($stat).' postes</td>'; 
echo '</tr>';

echo '<tr><th>inconnus</th>';
echo '<td></td>';
//echo '<td></td>';
echo '<td>'.$autre.'HTD</td><td>'.enpostes($autre).' postes</td>'; 
echo '</tr>';

echo '<tr><th>Total</th>';
$stat = stats("COUNT(*)","pain_service WHERE categorie < 10 AND categorie > 1 AND (service_reel > 0 OR categorie < 4)  AND annee_universitaire = $annee");
echo '<th>'.$stat.'</th>';
//echo '<td></td>';
$stat = round(stats("SUM(service_reel)","pain_service WHERE categorie > 1 AND categorie < 10 AND annee_universitaire = $annee") + $autre);
echo '<th>'.$stat.'HTD</th><th>'.enpostes($stat).' postes</th>'; 
echo '</tr>';


echo '<tr><th>En attente de catégorie</th>';
$stat = stats("COUNT(*)","pain_service WHERE categorie = 0 AND annee_universitaire = $annee");
echo '<td>'.$stat.'</td>';
//echo '<td></td>';
$stat = round(stats("SUM(service_reel)","pain_service WHERE categorie = 0 AND annee_universitaire = $annee"));
echo '<td>'.$stat.'HTD</td><td>'.enpostes($stat).' postes</td>'; 
echo '</tr>';
echo '</table>';
?>