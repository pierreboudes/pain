<?php

require_once("inc_connect.php");

require_once("inc_functions.php");


function errmsg_formcours($s) {
    echo '<td>ERREUR</td><td colspan="10">'.$s.'</td>';
}


if (isset($_POST["id_cours"])) {
    $id_cours = postclean("id_cours");
    /* TODO : VERIFIER les droits sur cet id_cours */
    $nom_cours = postclean("nom_cours");
    $semestre = postclean("semestre");
    $credits = postnumclean("credits");
    $responsable = postclean("responsable_cours");
    $cm = postnumclean("cm");
    $td = postnumclean("td");
    $tp = postnumclean("tp");
    $alt = postnumclean("alt");
    $id_formation = postclean("id_formation");
    $descriptif = postclean("descriptif");
    $code_geisha = postclean("code_geisha");
    /* test la validité du formulaire */
    if ("" === trim($nom_cours) )
    {
	errmsg_formcours("Nom invalide");
    } 
    else if ((1 !=  $semestre) and (2 !=  $semestre) )
    { 
	errmsg_formcours("semestre invalide");
    }
    /*   else if (!is_int($credits) or (0 > $credits)) 
    {
	errmsg_formcours("Credits invalide");
    }
    */
    else {/* valide */
	
	$query = "UPDATE pain_cours SET `nom_cours`='".$nom_cours."', `id_formation`='".$id_formation."', `semestre`='".$semestre."', `credits`='".$credits."', `id_enseignant`='".$responsable."', `cm`= '".$cm."', `td`='".$td."', `tp`='".$tp."', `alt`='".$alt."', `descriptif`='".$descriptif."', `code_geisha`='".$code_geisha."' WHERE `id_cours`=".$id_cours;
	
	if (!mysql_query($query)) {
	    errmsg_formcours(mysql_error());
	} else {
	    $qcours = "SELECT * FROM pain_cours WHERE `id_cours` = ".$id_cours;
	    
	    $rcours = mysql_query($qcours) or 
		die("Échec de la requête sur la table cours");

	    $cours = mysql_fetch_array($rcours);
	    ig_cours($cours);
	}
    }
} else {
    errmsg_formcours("Donner un nom au cours !");
}
?>