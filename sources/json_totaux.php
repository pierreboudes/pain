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
$user = authentication();

require_once("inc_connect.php");
require_once("utils.php");
require_once("inc_functions.php");

$id = 2;

if (isset($_GET["id"])) {
    $id = getclean("id");
}
if (isset($_GET["type"])) {
    $readtype = getclean("type");
}
if ($readtype == "annee") {
    $r = htdtotaux($id);
}
if ($readtype == "sformation") {
    $r = htdsuper($id);
}
if ($readtype == "formation") {
    $r = htdformation($id);
}
if ($readtype == "annee") {
    $r = htdtotaux($id);
}
if ($readtype == "cours") {
    $r = htdcours($id);
}

print json_encode($r);
?>