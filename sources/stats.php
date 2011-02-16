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
$annee = annee_courante();
require_once("inc_headers.php"); /* pour en-tete et pied de page */
entete("statistiques","jquery.gchart.js","pain_stats.js");
require_once('utils.php');
require_once("inc_connect.php");
require_once('inc_droits.php');
require_once('inc_functions.php');
require_once('inc_statsfunc.php');
include("menu.php");
/* mise a jour des services  (pain_service) */
update_servicesreels();

echo "<h2>Totaux pour l'ensemble du département (toutes les formations)</h2>";
echo "<p>";
$totaux = htdtotaux($annee);
ig_htd($totaux);
echo "</p><p>";
ig_totauxenpostes($totaux);
echo "</p>";
echo "Compter les TP comme du TD coûte : ".enpostes($totaux["tp"]/3)." postes.";
echo "<h2>Services actuels des différentes catégories d'intervenants</h2>";
echo '<div style="text-align: center; margin: 10px;"><span id="graphCat" style="width: 600px; height: 200px;"></span></div>';
include("inc_statscategories.php");
echo "<h2>Services réels par catégorie d'enseignant et par formation (en postes)</h2>";
echo '<div style="text-align: center; margin: 10px;"><span id="graphCatForm" style="width: 860px; height: 200px;"></span></div>';
include("inc_statscategoriesformations.php");
//echo "<h2>Graphiques par formation</h2>";
//include("inc_statsformations.php");
if (peutvoirstatsservices()) {
    include("inc_statsservices.php");
}

/*
echo "<h2>Graphiques par enseignant</h2>";
include("inc_statsenseignants.php");
*/
piedpage();
?>
