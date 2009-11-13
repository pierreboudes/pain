<?php /* -*- coding: utf-8 -*-*/
require_once("inc_connect.php");
require_once("inc_functions.php");

if (isset($_POST["id_tranche"])) {

    $id = postclean("id_tranche");
    $c = selectionner_tranche($id);
    echo '<tr class="trtrancheform" id="formedittranche'.$id.'">';    
    echo '<td colspan="10">'."\n";
    echo '<form method="post" id="fedittranche'.$id.
         '" class="formtranche" name="tranche" action="">';
    echo '<table class="tabletrancheform">';
    ig_formtranche($c["id_cours"], 
		 $id,
		 $c["cm"],
		 $c["td"],
		 $c["tp"],
		 $c["alt"],
		 $c["id_enseignant"],
		 $c["groupe"],
		 $c["type_conversion"],
		 $c["remarque"],
		 $c["htd"]);
    echo '</table>';
    echo '</form>'."\n";
    echo '</td></tr>'."\n";	
}
?>