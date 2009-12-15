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
require_once("inc_connect.php"); 
require_once('utils.php');

$id = postclean('id');
if ($id) {
    $query = 'SELECT * FROM pain_edt WHERE id_edt = '.$id.' LIMIT 1';
} else {
    $query = 'SELECT * FROM pain_edt WHERE 1 ORDER BY timestamp DESC LIMIT 1';
}
$result = mysql_query($query) or die('ERREUR : '.mysql_error());
$ligne = mysql_fetch_array($result);
$edt_html = preg_replace("/\n*$/", "\n", $ligne["edt_html"]);
echo $edt_html;
?>
