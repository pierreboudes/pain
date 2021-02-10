<?php
/* Pain - outil de gestion des services d'enseignement
 *
 * Copyright 2009-2015 Pierre Boudes,
 * département d'informatique de l'institut Galilée.
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

/* database connection link required for escaping strings */
require("iconnect.php");
$linkcas = $link;
$linkcas->query("SET NAMES 'utf8'");


function login() {
    return "demo";
}

class phpCAS
{
    function forceAuthentication() {
        return true;
    }
    function isAuthenticated() {
        return true;
    }
    function logout() {
        return true;
    }
}

/** recupere une chaine passee en HTTP/GET ou POST
 */
function _getclean($s) {
    global $link;
    if (isset($_GET[$s])) {
	$source = $_GET[$s];
    } else if (isset($_POST[$s])) {
	$source = $_POST[$s];
    } else {
	return NULL;
    }
    if(get_magic_quotes_gpc()) {
	return trim(htmlspecialchars($link->real_escape_string(stripslashes($source)), ENT_QUOTES));
    }
    else {
	return trim(htmlspecialchars($link->real_escape_string($source), ENT_QUOTES));
    }
}

function _cookieclean($s) {
    global $link;
    if (isset($_COOKIE[$s])) {
        if(get_magic_quotes_gpc()) {
            return trim(htmlspecialchars($link->real_escape_string(stripslashes(($_COOKIE[$s]))), ENT_QUOTES));
        }
        else {
            return trim(htmlspecialchars($link->real_escape_string($_COOKIE[$s]), ENT_QUOTES));
        }
    }
    else return NULL;
}
?>
