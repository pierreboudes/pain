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

function errmsg_formenseignant($s) {
    echo '<div style="border: 1px solid red;">';
    echo 'ERREUR : '.$s.'</div>';
}

if (isset($_POST["nom"])) {
    $nom = postclean("nom");
    $prenom = postclean("prenom");
    $statut = postclean("statut");
    $email = postclean("email");
    $telephone = postclean("telephone");
    $bureau = postclean("bureau");
    $service = postnumclean("service");

    if (("" === $nom) || (0 == $service))
    {
	errmsg_formenseignant("incomplet");
    } 
  /* Droits d'edition de cours dans la formation */
    else if (!peutediterenseignant()) { 
	errmsg_formenseignant("Vous ne pouvez pas ajouter d'enseignants.");
    }
    else {/* valide */
	
	$query = "INSERT INTO pain_enseignant 
                  (`nom`, `prenom`, `statut`, `email`, `telephone`, `bureau`, `service`) 
	          VALUES ('".$nom."', '".$prenom."', '".$statut."','".$email."', '".$telephone."', '".$bureau."', '".$service."')";

	pain_log($query);

	if (!mysql_query($query)) {
	    errmsg_formenseignant(mysql_error());
	} else {
	    $id_enseignant = mysql_insert_id();
	}
    }
}
?>