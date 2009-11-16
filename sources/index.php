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
<title>Pain</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="stylesheet" type="text/css" href="autocomplete.css"/>
<link rel='stylesheet' media='all' href='general.css' type='text/css' />
<!--
<link href="bubbletip/bubbletip/bubbletip.css" rel="stylesheet" type="text/css" />
-->

<!--
<link rel='stylesheet' media='projection, screen, tv' href='affichage.css' type='text/css' />
<link rel="stylesheet" href="impression.css" type="text/css" media="print" />
-->
<!-- FIREBUG DU PAUVRE !
<script type='text/javascript' src='http://getfirebug.com/releases/lite/1.2/firebug-lite-compressed.js'></script>
-->
<!--
<script type="text/javascript" src="http://dev.jquery.com/view/trunk/plugins/autocomplete/jquery.autocomplete.js"></script>
-->
<script type='text/javascript' src='jquery.js'></script>
<script type='text/javascript' src='jquery.form.js'></script>
<script type="text/javascript" src="jquery.autocomplete.js"></script>
<script type="text/javascript" src="jquery.select-autocomplete.js"></script>
<!--
<script src="bubbletip/jQuery.bubbletip-1.0.1.js" type="text/javascript"></script>
-->
<script type='text/javascript' src='pain.js'></script>
<!--

<script type='text/javascript' src='bulleaide.js'></script>
-->

<meta name="description" content="Système de gestion des services du département d'informatique" />

</head>

<body> 

<?php
include("menu.php");
include("inc_infobox.php");
include("inc_listcours.php");
/* include("inc_aide.php"); */
?>
</body>
</html>