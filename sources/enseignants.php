<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">

<head>
     <title>Pain -- Gestion des enseignants (temporaire)</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel='stylesheet' media='all' href='general.css' type='text/css' />

<!--
<link rel='stylesheet' media='projection, screen, tv' href='affichage.css' type='text/css' />
<link rel="stylesheet" href="impression.css" type="text/css" media="print" />
-->

<script type='text/javascript' src='jquery.js'></script>
<script type='text/javascript' src='jquery.form.js'></script>
<script type='text/javascript' src='pain.js'></script>

<meta name="description" content="Système de gestion des services du département d'informatique" />

</head>

<body> 

<ul id="menu">
<li><a href="./">accueil</a></li>
<li><a href="enseignants.php">enseignants</a> (temporaire)</li>
<li><a >statistiques</a></li>
<li><a >feuille de service</a></li>
<li><a >annuaire d'une formation</a></li>
</ul>


<?php
include("act_ajouterenseignant.php");
include("inc_listenseignants.php");
?>

</body>
</html>