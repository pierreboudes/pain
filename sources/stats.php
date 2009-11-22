<?php /* -*- coding: utf-8 -*-*/
/* Pain - outil de gestion des services d'enseignement        
 *
 * Copyright 2009 Pierre Boudes, département d'informatique de l'institut Galilée.
 *
 * This file is part of Pain.
 *
 * Pain is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Pain is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public
 * License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Pain.  If not, see <http://www.gnu.org/licenses/>.
 */
?>
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

<?php
require_once('utils.php');
include("menu.php");
echo "<h3>Totaux toutes les formations</h3>";
echo "<p>";
include("act_totaux.php");
echo "</p>";
echo "<h3>Services actuels des différentes catégories d'intervenants</h3>";
include("inc_statsenseignants.php");
include("inc_statsservices.php");
ig_statsmysql();
?>

</body>
</html>