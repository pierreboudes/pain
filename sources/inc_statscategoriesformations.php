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

function statcatform($cat, $form) {
    $stat = round(stats("SUM(pain_tranche.htd)","pain_enseignant, pain_tranche, pain_cours, pain_formation, pain_sformation, pain_service WHERE pain_tranche.id_enseignant = pain_enseignant.id_enseignant AND pain_cours.id_cours = pain_tranche.id_cours AND pain_cours.id_enseignant <> 1 AND pain_formation.id_formation = pain_cours.id_formation AND pain_formation.id_sformation = $form AND pain_sformation.id_sformation = pain_formation.id_sformation AND pain_enseignant.id_enseignant = pain_service.id_enseignant AND pain_service.annee_universitaire = pain_sformation.annee_universitaire AND pain_service.categorie = $cat"));
    return $stat;
}

function statensform($ens, $form) {
    if ($ens != 1) {
	$stat = round(stats("SUM(pain_tranche.htd)","pain_tranche, pain_cours, pain_formation WHERE pain_tranche.id_enseignant = $ens AND pain_cours.id_cours = pain_tranche.id_cours AND pain_cours.id_enseignant <> 1 AND pain_formation.id_formation = pain_cours.id_formation AND pain_formation.id_sformation = $form"));
    } else {
	$stat = round(stats("SUM(pain_tranche.htd)","pain_tranche, pain_cours, pain_formation WHERE (pain_tranche.id_enseignant = 1 OR pain_cours.id_enseignant = 1) AND pain_cours.id_cours = pain_tranche.id_cours  AND pain_formation.id_formation = pain_cours.id_formation AND pain_formation.id_sformation = $form"));
    }
    return $stat;
}

$query = "SELECT id_sformation, nom FROM pain_sformation WHERE annee_universitaire = $annee ORDER BY numero";
$res = mysql_query($query) or die("BD Impossible d'effectuer la requête: $query");
$formation = mysql_fetch_assoc($res);

echo '<table class="stat">';
echo '<tr><th>Catégorie</th>';
mysql_data_seek($res,0);
while ($formation = mysql_fetch_assoc($res)) {
    echo '<th>'.$formation["nom"].'</th>';
}
echo '<tr></tr>';

echo '<tr><th>Titulaires du département</th>'; // cat 2
$cat = 2;
mysql_data_seek($res,0);
while ($formation = mysql_fetch_assoc($res)) {
    $stat = statcatform($cat, $formation["id_sformation"]);
    echo '<td>'.enpostes($stat).'</td>'; 
}
echo '</tr>';

echo '<tr><th>Non titulaires du département</th>'; // cat 3
$cat = 3;
mysql_data_seek($res,0);
while ($formation = mysql_fetch_assoc($res)) {
    $stat = statcatform($cat, $formation["id_sformation"]);
    echo '<td>'.enpostes($stat).'</td>'; 
}
echo '</tr>';

echo '<tr><th>Autres enseignants de Galilée</th>'; // cat 4
$cat = 4;
mysql_data_seek($res,0);
while ($formation = mysql_fetch_assoc($res)) {
    $stat = statcatform($cat, $formation["id_sformation"]);
    echo '<td>'.enpostes($stat).'</td>'; 
}
echo '</tr>';
echo '<tr><th>Autres enseignants de Paris 13 hors Galilée</th>'; // cat 6
$cat = 6;
mysql_data_seek($res,0);
while ($formation = mysql_fetch_assoc($res)) {
    $stat = statcatform($cat, $formation["id_sformation"]);
    echo '<td>'.enpostes($stat).'</td>'; 
}
echo '</tr>';
echo '<tr><th>Autres (vacataires, industriels etc.)</th>'; // cat 5
$cat = 5;
mysql_data_seek($res,0);
while ($formation = mysql_fetch_assoc($res)) {
    $stat = statcatform($cat, $formation["id_sformation"]);
    echo '<td>'.enpostes($stat).'</td>'; 
}
echo '</tr>';
echo '<tr><th>Cours annulés</th>'; // ens 1
$ens = 1;
mysql_data_seek($res,0);
while ($formation = mysql_fetch_assoc($res)) {
    $stat = statensform($ens, $formation["id_sformation"]);
    echo '<td>'.enpostes($stat).'</td>'; 
}
echo '</tr>';
echo '<tr><th>Cours vacants</th>'; // ens 3
$ens = 3;
mysql_data_seek($res,0);
while ($formation = mysql_fetch_assoc($res)) {
    $stat = statensform($ens, $formation["id_sformation"]);
    echo '<td>'.enpostes($stat).'</td>'; 
}
echo '</tr>';
echo '</table>';
?>