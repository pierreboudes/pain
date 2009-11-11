<?php /* -*- coding: utf-8 -*-*/
require_once("inc_connect.php");
require_once("inc_functions.php");

if (isset($_POST["id_cours"])) {    
    $id = postclean("id_cours"); 
    echo '<tr id="tranchesducours'.$id.'">';
    echo '<td colspan="11" class="tranches">';
    echo '<form method="post" id="formtranche'.$id.
         '" class="formtranche" name="tranche" action="act_ajoutertranche.php">';
    echo '<table class="tranches">';
    ig_legendetranches($id);
    ig_listtranches(tranchesdecours($id));
    ig_formtranche($id);
    echo '</table></form></td>';
    '</tr>';
};
?>