<?php  /* -*- coding: utf-8 -*-*/
require_once("inc_connect.php");
require_once("inc_functions.php");


function errmsg_formtranche($s) {
    echo '<td>ERREUR</td><td colspan="9">'.$s.'</td>';
}


if (isset($_POST["id_tranche"])) {
    $id_tranche = postclean("id_tranche");
    $groupe = postnumclean("groupe");
    $id_enseignant = postclean("id_enseignant");
    $cm = postnumclean("cm");
    $td = postnumclean("td");
    $tp = postnumclean("tp");
    $alt = postnumclean("alt");
    $htd = postnumclean("htd");    
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
    else if (!peuteditertrancheducours($id)) {
	errmsg_formtranche("Droits insuffisants");
    }
    else {/* valide */
	
	$query = "UPDATE pain_tranche SET `id_enseignant`='".$id_enseignant."', `groupe`='".$groupe."', `cm`='".$cm."', `td`='".$td."', `tp`='".$tp."', `alt`='".$alt."', `htd`= '".$htd."', `type_conversion`='".$type_conversion."', `remarque`='".$remarque."' WHERE `id_tranche`=".$id_tranche;

	pain_log($query);

	if (!mysql_query($query)) {
	    errmsg_formtranche(mysql_error());
	} else {
	    $qtranche = "SELECT * FROM pain_tranche WHERE `id_tranche` = ".$id_tranche;
	 
	    if (!($rtranche = mysql_query($qtranche))) {
		errmsg_formtranche(mysql_error());
	    } else {
		$tranche = mysql_fetch_array($rtranche);
		ig_tranche($tranche, "new");
	    }
	}
    }
} else {
    errmsg_formtranche("erreur interne");
}
?>