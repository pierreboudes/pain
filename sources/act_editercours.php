<?php /* -*- coding: utf-8 -*-*/
require_once("inc_connect.php");
require_once("inc_functions.php");

if (isset($_POST["id_cours"])) {

    $id = postclean("id_cours");
    $c = selectionner_cours($id);
    echo '<tr class="formcours" id="formeditcours'.$id.'">';    
    echo '<td colspan="11">'."\n";
    echo '<form method="post" id="feditcours'.$id.
         '" class="formcours" name="cours" action="">';
    ig_formcours($c["id_formation"], 
		 $id,
		 $c["nom_cours"],
		 $c["semestre"],
		 $c["id_enseignant"],
		 $c["credits"],
		 $c["cm"],
		 $c["td"],
		 $c["tp"],
		 $c["alt"],
		 $c["descriptif"],
		 $c["code_geisha"]);
    echo '</form>'."\n";
    echo '</td></tr>'."\n";	
}
?>