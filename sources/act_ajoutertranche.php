<?php

require_once("inc_connect.php");
require_once("inc_functions.php");

function errmsg_formtranche($s) {
    echo '<tr><td>ERREUR</td><td colspan="9">'.$s.'</td></tr>';
}

if (isset($_POST["id_cours"])) {
    $id_cours = postclean("id_cours");
    $groupe = postclean("groupe");
    $id_enseignant = postclean("id_enseignant");
    $cm = postclean("cm");
    $td = postclean("td");
    $tp = postclean("tp");
    $alt = postclean("alt");
    $htd = postclean("htd");    
    $type_conversion = postclean("type_conversion");
    $remarque = postclean("remarque");
    /* calcul de l'équivalent TD si la conversion est automatique */
    if (0 == $type_conversion) {
	$htd = 1.5 * $cm + $td + $tp + $alt;
    }

    /* test la validité du formulaire */
    if (0 == $htd)
    {
	errmsg_formtranche("nombre d'heures égal à zéro");
    } 
    else {/* valide */
	
	$query = "INSERT INTO pain_tranche (`id_cours`, `id_enseignant`, `type`, `groupe`, `cm`, `td`, `tp`, `alt`, `htd`, `type_conversion`, `remarque`) 
	      VALUES ('".$id_cours."', '".$id_enseignant."', '".$type."', '".$groupe."', '".$cm."', '".$td."', '".$tp."', '".$alt."', '".$htd."', '".$type_conversion."', '".$remarque."')";

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