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
require_once('inc_connect.php');
function ig_formselectannee($annee)
{
    global $link;
    $qans = "SELECT DISTINCT `annee_universitaire` 
             FROM pain_sformation WHERE 1 ORDER BY `annee_universitaire` ASC";
    $rans = $link->query($qans) 
	  or die("Échec de la requête sur la table sformation");
    while ($an =$rans->fetch_array()) {
	echo '<option ';
	if ($an["annee_universitaire"] == $annee) {
	    echo 'selected="selected" ';
	}
	echo  'value="'.$an["annee_universitaire"].'">';
	echo trim($an["annee_universitaire"].'-'.($an["annee_universitaire"] + 1));
	echo '</option>';
    }
}
?>
<ul id="menu">
      <li><a href="./">accueil</a></li>
      <li><a href="service.php">service</a></li>
      <li><a href="stats.php">stats</a></li>
      <li><a href="enseignants.php">enseignants</a></li>
      <li><a href="annuaire.php">annuaire</a></li>
<?php
    if (1 == $user["su"]) {
	echo '<li><a href="admin.php">admin</a></li>';
    }
?>
      <li><a href="logout.php">logout</a></li>
      <li>
<?php

echo '<form method="post" id="choixannee" class="formcours" name="annee" action="#" style="display:inline;">';
echo '<select name="annee_menu" style="display:inline; width:100px;">';
$anneecivile = date('Y', time());
ig_formselectannee($annee);
echo '</select>';
echo '<input type="submit" value="OK" style="display:inline;width:40px;"/>';
echo '</form>'."\n";
?>
</li>
</ul>
<?php
echo '<div id="user" class="hiddenvalue">';
echo '<span class="id">'.$user["id_enseignant"].'</span>';
echo '<span class="su">'.$user["su"].'</span>';
echo '</div>';
?>