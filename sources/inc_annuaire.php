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
require_once("inc_annuairefunc.php");


/* identifiant de formation en provenance du formulaire */
$id_formation = 0;
if (isset($_POST['id_formation'])) {
    $id_formation = postclean('id_formation');
}
$semestre = 0;
if (isset($_POST['semestre'])) {
    $semestre = postclean('semestre');
}

echo '<center><div class="infobox" style="width:290px;">';
echo '<form method="post" id="choixformation" class="formcours" name="enseignant" action="#">';
echo '<select name="id_formation" style="display:inline; width:150px;">';
ig_formselectformation($id_formation);
echo '</select>';
echo '<select name="semestre" style="display: inline; width: 100px;">';
echo '<option value="0" ';
if ($semestre == 0) echo 'selected="selected"';
echo '>semestre</option>';
for ($i = 1; $i <= 2; $i++) {
	echo '<option ';
	if ($i == $semestre) {
	    echo 'selected="selected" ';
	}
	echo  'value="'.$i.'">';
	echo 'S'.$i;
	echo '</option>';
}
echo '</select>';
echo '<input type="submit" value="OK" style="display:inline;width:40px;"/>';
echo '</form>'."\n";
echo '</div></center>';

if ($id_formation != 0) {

    /* annuaire */
    echo "<h2>Annuaire de la formation</h2>";

    $q = "SELECT id_cours, 
                 nom_cours,
                 credits,
                 semestre,
                 pain_cours.id_enseignant as id_enseignant,
                 pain_enseignant.prenom AS prenom, 
                 pain_enseignant.nom AS nom,
                 pain_enseignant.email AS email,
                 pain_enseignant.telephone AS tel,
                 pain_enseignant.bureau AS bureau
          FROM pain_cours, pain_enseignant
          WHERE pain_cours.id_formation = $id_formation "; 
    if ($semestre) $q .=" AND semestre = $semestre ";
    $q .=" AND pain_enseignant.id_enseignant = pain_cours.id_enseignant ";
    $q .=" ORDER BY semestre ASC, nom_cours ASC";
    ($r = mysql_query($q)) 
	or die("Échec de la connexion à la base $q<br>".mysql_error());
    while ($cours = mysql_fetch_array($r)) {
	ig_entete_du_cours($cours);
	ig_responsable_du_cours($cours);
	ig_intervenants_du_cours($cours);
	ig_pied_du_cours($cours);
    }
}
?>