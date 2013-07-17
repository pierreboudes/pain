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
$user = authentication();
$ANNEE = get_and_set_annee_menu();
require_once("inc_headers.php"); /* pour en-tete et pied de page */
require_once("inc_importfunc.php");
require_once('utils.php');

entete("Import CSV","pain_annuaire.js");


include("menu.php");

/*
 * $query = "LOAD DATA INFILE '$fichier' 
 * INTO TABLE essai
 * FIELDS TERMINATED BY ';' 
 * ENCLOSED BY '' ";
 */

if (1 == $user["su"]) {

$ANNEENEXT=$ANNEE+1;

echo '<center><h2>Import CSV</h2></center>';

echo "<p>Import de fichier CSV (séparateur: ; ) : La première ligne doit comporter
	les noms des champs.</p>";
echo '<p>Les champs peuvent être (en rouge ceux obligatoires): </p><ul>
	<li>id_enseignant (voir statCSV, si champ absent alors libre); </li>
	<li class="rouge">nom_sformation (ex: DUT INFO FI);</li>
	<li class="rouge">nom_formation; </li>
	<li class="rouge">nom_cours; </li>
	<li>cm (nbre d\'heures de cours);</li>
	<li>td;</li> 
	<li>tp;</li>
	<li>alt (par ex. pour ctrl);</li> 
	<li>remarques;</li>
	<li>groupe (en cas de présence, le fichier contient des tranches de cours, sinon des cours) </li></ul>';

echo "<p>En cas d'erreur d'analyse, l'importation sera annulée.</p>";


echo '<div class="import" id="formimport">';
echo '<form method="POST" action="importCSV2.php" enctype="multipart/form-data">';
echo '<fieldset><legend>Préremplissages</legend>';
list($id_formation, $semestre) = import_php_form();
echo '</fieldset>';
echo '<input type="file" name="import.csv" value="500000">';// 500ko max
echo '<input type="submit" value="envoi">';
echo '</form></div>';

//categorie=2 pour les permanents par défaut, espèrons que cela ne changera pas !
// id_enseignant=1 : cours annulé.

} // user su
    piedpage();
?>
