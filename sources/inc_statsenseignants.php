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


echo '<table class="stat">';
echo '<tr><th>Catégorie</th><th>nombre de personnes</th><th>statutaire</th><th>réel</th></tr>';

echo '<tr><th>Ensemble du département</th>';
$stat = stats("COUNT(*)","pain_enseignant WHERE categorie = 2 OR categorie = 3");
echo '<td>'.$stat.'</td>';
$stat = round(stats("SUM(service)","pain_enseignant WHERE categorie = 2 OR categorie = 3"));
echo '<td>'.$stat.'HTD</td>'; 
$stat = round(stats("SUM(pain_tranche.htd)","pain_enseignant,pain_tranche,pain_cours WHERE (pain_enseignant.categorie = 2 OR pain_enseignant.categorie = 3) AND pain_tranche.id_enseignant = pain_enseignant.id_enseignant AND pain_cours.id_cours = pain_tranche.id_cours AND pain_cours.id_enseignant <> 1"));
echo '<td>'.$stat.'HTD</td>';
echo '</tr>';


echo '<tr><th>Titulaires du département</th>';
$stat = stats("COUNT(*)","pain_enseignant WHERE categorie = 2");
echo '<td>'.$stat.'</td>';
$stat = round(stats("SUM(service)","pain_enseignant WHERE categorie = 2"));
echo '<td>'.$stat.'HTD</td>';
$stat = round(stats("SUM(pain_tranche.htd)","pain_enseignant, pain_tranche, pain_cours WHERE pain_enseignant.categorie = 2 AND pain_tranche.id_enseignant = pain_enseignant.id_enseignant AND pain_cours.id_cours = pain_tranche.id_cours AND pain_cours.id_enseignant <> 1"));
echo '<td>'.$stat.'HTD</td>';
echo '</tr>';

echo '<tr><th>Non titulaires du département</th>';
$stat = stats("COUNT(*)","pain_enseignant WHERE categorie = 3");
echo '<td>'.$stat.'</td>';
$stat = round(stats("SUM(service)","pain_enseignant WHERE categorie = 3"));
echo '<td>'.$stat.'HTD</td>';
$stat = round(stats("SUM(pain_tranche.htd)","pain_enseignant, pain_tranche, pain_cours WHERE pain_enseignant.categorie = 3 AND pain_tranche.id_enseignant = pain_enseignant.id_enseignant AND pain_cours.id_cours = pain_tranche.id_cours AND pain_cours.id_enseignant <> 1"));
echo '<td>'.$stat.'HTD</td>';
echo '</tr>';


$autre = round(stats("SUM(pain_tranche.htd)","pain_tranche, pain_cours WHERE pain_tranche.id_enseignant = 9 AND pain_cours.id_cours = pain_tranche.id_cours AND pain_cours.id_enseignant <> 1"));

echo '<tr><th>Intervenants hors département</th>';
$stat = stats("COUNT(*)","pain_enseignant WHERE categorie > 3");
echo '<td>'.$stat.'</td>';
echo '<td></td>';
$stat = round(stats("SUM(pain_tranche.htd)","pain_enseignant, pain_tranche, pain_cours WHERE pain_enseignant.categorie > 3 AND pain_tranche.id_enseignant = pain_enseignant.id_enseignant AND pain_cours.id_cours = pain_tranche.id_cours AND pain_cours.id_enseignant <> 1")) + $autre;
echo '<td>'.$stat.'HTD</td>';
echo '</tr>';

echo '<tr><th>autres enseignants de Galilée</th>';
$stat = stats("COUNT(*)","pain_enseignant WHERE categorie = 4");
echo '<td>'.$stat.'</td>';
echo '<td></td>';
$stat = round(stats("SUM(pain_tranche.htd)","pain_enseignant, pain_tranche, pain_cours WHERE pain_enseignant.categorie = 4 AND pain_tranche.id_enseignant = pain_enseignant.id_enseignant AND pain_cours.id_cours = pain_tranche.id_cours AND pain_cours.id_enseignant <> 1"));
echo '<td>'.$stat.'HTD</td>';
echo '</tr>';


echo '<tr><th>enseignants de Paris 13 hors Galilée</th>';
$stat = stats("COUNT(*)","pain_enseignant WHERE categorie = 6");
echo '<td>'.$stat.'</td>';
echo '<td></td>';
$stat = round(stats("SUM(pain_tranche.htd)","pain_enseignant, pain_tranche, pain_cours WHERE pain_enseignant.categorie = 6 AND pain_tranche.id_enseignant = pain_enseignant.id_enseignant AND pain_cours.id_cours = pain_tranche.id_cours AND pain_cours.id_enseignant <> 1"));
echo '<td>'.$stat.'HTD</td>';
echo '</tr>';


echo '<tr><th>autres intervenants</th>';
$stat = stats("COUNT(*)","pain_enseignant WHERE categorie = 5");
echo '<td>'.$stat.'</td>';
echo '<td></td>';
$stat = round(stats("SUM(pain_tranche.htd)","pain_enseignant, pain_tranche, pain_cours WHERE pain_enseignant.categorie = 5 AND pain_tranche.id_enseignant = pain_enseignant.id_enseignant AND pain_cours.id_cours = pain_tranche.id_cours AND pain_cours.id_enseignant <> 1"));
echo '<td>'.$stat.'HTD</td>';
echo '</tr>';

echo '<tr><th>inconnus</th>';
echo '<td></td>';
echo '<td></td>';
echo '<td>'.$autre.'HTD</td>';
echo '</tr>';

echo '<tr><th>Total</th>';
$stat = stats("COUNT(*)","pain_enseignant WHERE categorie > 1");
echo '<td>'.$stat.'</td>';
echo '<td></td>';
$stat = round(stats("SUM(pain_tranche.htd)","pain_enseignant, pain_tranche, pain_cours WHERE pain_enseignant.categorie > 1 AND pain_tranche.id_enseignant = pain_enseignant.id_enseignant AND pain_cours.id_cours = pain_tranche.id_cours AND pain_cours.id_enseignant <> 1")) + $autre;
echo '<td>'.$stat.'HTD</td>';
echo '</tr>';


echo '<tr><th>En attente de catégorie</th>';
$stat = stats("COUNT(*)","pain_enseignant WHERE categorie = 0");
echo '<td>'.$stat.'</td>';
echo '<td></td>';
$stat = round(stats("SUM(pain_tranche.htd)","pain_enseignant, pain_tranche, pain_cours WHERE pain_enseignant.categorie = 0 AND pain_tranche.id_enseignant = pain_enseignant.id_enseignant AND pain_cours.id_cours = pain_tranche.id_cours AND pain_cours.id_enseignant <> 1"));
echo '<td>'.$stat.'HTD</td>';
echo '</tr>';
echo '</table>';
?>