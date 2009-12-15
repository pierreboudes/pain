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
require_once('utils.php');
require_once('../authentication.php');
require_once("inc_connect.php"); 

$contenu = postclean('content');
$message = postclean('message');
$login = postclean('login');

if (phpCas::isSessionAuthenticated()) {
$query = 'INSERT INTO pain_edt (edt_html, message, login) 
          VALUES ("'.$contenu.'", "'.$message.'", "'.$login.'")';
$result = mysql_query($query) or die("ERREUR : ".mysql_error());
echo 'emploi du temps sauvegardé';
} else {
    echo "ERREUR : échec de l'authentification";
}
?>
