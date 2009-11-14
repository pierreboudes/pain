<?php /* -*- coding: utf-8 -*-*/
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