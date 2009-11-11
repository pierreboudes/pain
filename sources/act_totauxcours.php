<?php
/* -*- coding: utf-8 -*-*/
require_once("inc_connect.php");

$id = 0;

if (isset($_POST["id_cours"])) {
    $id = $_POST["id_cours"];
} 

$qservi = 'SELECT SUM(htd) FROM pain_tranche WHERE id_cours = '.$id.' AND id_enseignant > 9';
$rservi = mysql_query($qservi) 
    or die("erreur d'acces a la table tranche : $qservi erreur:".mysql_error());

$servi = mysql_fetch_assoc($rservi);
$servi = $servi["SUM(htd)"];
if ($servi == "") {
    $servi = 0;
} 

$qlibre = 'SELECT SUM(htd) FROM pain_tranche WHERE id_cours = '.$id.' AND id_enseignant = -1';
$rlibre = mysql_query($qlibre) 
    or die("erreur d'acces a la table tranche : $qlibre erreur:".mysql_error());

$libre = mysql_fetch_assoc($rlibre);
$libre = $libre["SUM(htd)"];
if ($libre == "") {
    $libre = 0;
}

$qannule = 'SELECT SUM(htd) FROM pain_tranche WHERE id_cours = '.$id.' AND id_enseignant = 1';
$rannule = mysql_query($qannule) 
    or die("erreur d'acces a la table tranche : $qannule erreur:".mysql_error());

$annule = mysql_fetch_assoc($rannule);
$annule = $annule["SUM(htd)"];
if ($annule == "") {
    $annule = 0;
}
?>
<img class="imgbarre" src="act_barre.php?servi=<?=$servi?>&libre=<?=$libre?>&annule=<?=$annule?>" title="<?=$servi?>H servies, <?=$libre?>H à pourvoir et <?=$annule?>H annulées."/>
