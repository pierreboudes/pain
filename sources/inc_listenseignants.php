<?php /* -*- coding: utf-8 -*-*/
require_once("inc_connect.php");
require_once("inc_functions.php");

echo '<form method="post" id="formenseignant" class="formenseignant" name="enseignant" action="'.$_SERVER['PHP_SELF'].'">';
echo '<table class="enseignants">';
ig_legendeenseignant();
ig_listenseignants();
ig_formenseignant();
echo '</table></form></td></tr>';
?>