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
entete("les enseignants","pain_enseignants.js");
include("menu.php");

function ig_tablecategorie($id, $nom) {
    echo '<table id="tablecat_'.$id.'" class="categorie">';
    echo '<tr id="categorie_'.$id.'" class="categorie">';
    echo '<td class="laction"><div id="basculeCat_'.$id.'" class="basculeOff"></div></td>';
    echo '<th class="titre">'.$nom.'</th>';
    echo '<th class="action"></th>';    
    echo '</tr>';
    echo '</table>';
}

echo "<center><div class=\"infobox\">Les enseignants nouvellement ajoutés sont listés sous le titre <em>en attente de catégorie</em>.</div></center>";
ig_tablecategorie(2,"Titulaires du département");
ig_tablecategorie(3,"Non titulaires du département");
ig_tablecategorie(4,"Autres enseignants de Galilée");
ig_tablecategorie(6,"Autres enseignants de Paris 13");
ig_tablecategorie(5,"Extérieurs et vacataires");
ig_tablecategorie(0,"En attente de catégorie");
ig_tablecategorie(10,"Anciens enseignants");
?>

<div id="skel">
<table class="enseignants">
<tbody>
<tr id="skelenseignant" class="enseignant">
<th class="laction"></th>
<th class="login">login</th>
<th class="prenom">prénom</th>
<th class="nom">nom</th>
<th class="statut">statut</th>
<th class="email">email</th>
<th class="telephone">tél.</th>
<th class="service">service</th>
<!-- pas dans la base prod <th class="responsabilite">responsabilité</th> -->
<th class="debut">debut</th>
<th class="fin">fin</th>
<th class="categorie">categorie</th>
<th class="action"><div class="palette"></div></th>
</tr>
</tbody>
</table>
</div>
<?php
piedpage();
?>
