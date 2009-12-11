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

require_once("inc_connect.php");
require_once("inc_functions.php");

function errmsg_formtranche($s) {
    echo '<tr><td>ERREUR</td><td colspan="9">'.$s.'</td></tr>';
}

if (isset($_POST["id_cours"])) {
    $id_cours = postclean("id_cours");
    $groupe = postnumclean("groupe");
    $id_enseignant = postclean("id_enseignant");
    $cm = postnumclean("cm");
    $td = postnumclean("td");
    $tp = postnumclean("tp");
    $alt = postnumclean("alt");
    $htd = postnumclean("htd");    
    $remarque = postclean("remarque");
    /* calcul de l'équivalent TD */
    
    $htd = 1.5 * $cm + $td + $tp + $alt;

    /* test la validité du formulaire */
    if (0 == $htd)
    {
	errmsg_formtranche("nombre d'heures égal à zéro");
    } 
    else if (!peuteditertrancheducours($id)) {
	errmsg_formtranche("Droits insuffisants");
    }
    else {/* valide */
	
	$query = "INSERT INTO pain_tranche (`id_cours`, `id_enseignant`, `groupe`, `cm`, `td`, `tp`, `alt`, `htd`, `remarque`)  VALUES ('".$id_cours."', '".$id_enseignant."', '".$groupe."', '".$cm."', '".$td."', '".$tp."', '".$alt."', '".$htd."', '".$remarque."')";

	pain_log($query);
	
	if (!mysql_query($query)) {
	    errmsg_formtranche(mysql_error());
	} else {
	    $id_tranche = mysql_insert_id();

	    $qtranche = "SELECT * FROM pain_tranche WHERE `id_tranche` = ".$id_tranche;
	 
	    if (!($rtranche = mysql_query($qtranche))) {
		errmsg_formtranche(mysql_error());
	    } else {   
		$tranche = mysql_fetch_array($rtranche);
		ig_tranche($tranche);
	    }
	}
    }
} else {
    errmsg_formtranche("Donner un nom au nouveau cours !");
}
?>