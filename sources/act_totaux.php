<?php /* -*- coding: utf-8 -*-*/
require_once("inc_connect.php");
require_once("inc_functions.php");

$id = 2;

if (isset($_POST["annee_universitaire"])) {
    $annee = postclean("annee_universitaire");
} 

$r=htdtotaux($annee);
ig_htd($r);
?>
