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
require_once('authentication.php'); 
authrequired();

function entete($titre, $js = NULL) {
    echo <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">

<head>
<title>Pain -- $titre</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel='stylesheet' media='all' href='css/general.css' type='text/css' />
<link type="text/css" href="css/custom-theme/jquery-ui-1.8.1.custom.css"
rel="stylesheet" />
<script type='text/javascript' src='http://getfirebug.com/releases/lite/1.2/firebug-lite-compressed.js'></script>
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript" src="js/dyntab.js"></script>
<script type='text/javascript' src='js/pain.js'></script>
EOD;
    if ($js != NULL) {
	echo "<script type='text/javascript' src='js/".$js."'></script>";
    }
/* ------ tweaks --------

<link rel='stylesheet' media='projection, screen, tv' href='affichage.css' type='text/css' />
<link rel="stylesheet" href="impression.css" type="text/css" media="print" />
<!-- FIREBUG DU PAUVRE !
<script type='text/javascript' src='http://getfirebug.com/releases/lite/1.2/firebug-lite-compressed.js'></script>
-->
<script type="text/javascript" src="http://dev.jquery.com/view/trunk/plugins/autocomplete/jquery.autocomplete.js"></script>
<!--
<link href="bubbletip/bubbletip/bubbletip.css" rel="stylesheet" type="text/css" />
<script src="bubbletip/jQuery.bubbletip-1.0.1.js" type="text/javascript"></script>
-->
<script type="text/javascript" src="js/jquery.highlight-3.js"></script>
*/
    echo <<<EOD
<meta name="description" content="Système de gestion des services du département d'informatique" />
</head>
<body>
EOD;
}

function piedpage() {
    ig_statsmysql();
    echo <<<EOD
</body>
</html>
EOD;
}
?>