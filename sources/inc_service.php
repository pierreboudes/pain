<?php /* -*- coding: utf-8 -*-*/
require_once("inc_connect.php");
require_once("inc_functions.php");

$id_enseignant = "";


if (isset($_POST['id_enseignant'])) {
    $id_enseignant = postclean('id_enseignant');
}
echo '<center><div class="infobox" style="width:200px;">';
echo '<form method="post" id="choixenseignant" class="formcours" name="enseignant" action="#">';
echo '<select name="id_enseignant" style="display:inline; width:150px;">';
ig_formselectenseignants($id_enseignant);
echo '</select>';
echo '<input type="submit" value="OK" style="display:inline;width:40px;"/>';
echo '</form>'."\n";
echo '</div></center>';

if ($id_enseignant != "") {
    $services = listeinterventions($id_enseignant);
    echo '<table class="formations">';
    echo '<tr>';
    ig_legendeintervention();
    echo '</tr>';
    while ($service = mysql_fetch_array($services)) {
	echo '<tr class="intervention">';
	ig_intervention($service);
	echo '</tr>';
    }
    echo '<tr>';
    ig_totauxinterventions($id_enseignant);
    echo '</tr>';
    echo '</table>';
}
?>