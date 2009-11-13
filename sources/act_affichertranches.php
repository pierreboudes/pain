<?php /* -*- coding: utf-8 -*-*/
require_once("inc_connect.php");
require_once("inc_functions.php");

if (isset($_POST["id_cours"])) {
    $id = postclean("id_cours"); 
    echo '<tr class="trtranches" id="tranchesducours'.$id.'">';
    echo '<td colspan="11" class="tranches">';
    echo '<table class="tranches">';
    ig_legendetranches($id);
    ig_listtranches(tranchesdecours($id)); 
    echo '<tr class="trtrancheform"><td colspan="10">';
    echo '<form method="post" id="formtranche'.$id.'" class="formtranche" name="tranche" action="act_ajoutertranche.php">';
    echo '<table class="tabletrancheform">';
    ig_formtranche($id);
    echo '</table></form>';
    echo '</td></tr>';
    echo '</table></td></tr>';

};
?>