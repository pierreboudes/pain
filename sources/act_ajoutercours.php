<?php

require_once("inc_connect.php");
require_once("inc_functions.php");


function errmsg_formcours($s) {
    echo '<td>ERREUR</td><td colspan="10">'.$s.'</td>';
}


if (isset($_POST["nom_cours"])) {
    $nom_cours = postclean("nom_cours");
    $semestre = postclean("semestre");
    $credits = postclean("credits");
    $responsable = postclean("responsable_cours");
    /* convertir en id_enseignant en amont */
    $cm = postclean("cm");
    $td = postclean("td");
    $tp = postclean("tp");
    $alt = postclean("alt");
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
	
	$query = "INSERT INTO  cours (`nom_cours`, `id_formation`, `semestre`, `credits`, `id_enseignant`, `cm`, `td`, `tp`, `alt`, `descriptif`, `code_geisha`) 
	      VALUES ('".$nom_cours."', '".$id_formation."', '".$semestre."', '".$credits."', '".$responsable."', '".$cm."', '".$td."', '".$tp."', '".$alt."', '".$descriptif."', '".$code_geisha."')";
	
	if (!mysql_query($query)) {
	    errmsg_formcours(mysql_error());
	} else {
	    $id_cours = mysql_insert_id();

	    $qcours = "SELECT * FROM cours WHERE `id_cours` = ".$id_cours;
	    
	    $rcours = mysql_query($qcours) or 
		die("Échec de la requête sur la table cours");

	    $cours = mysql_fetch_array($rcours);
	    ig_cours($cours);
	}
    }
} else {
    errmsg_formcours("Donner un nom au nouveau cours !");
}
?>