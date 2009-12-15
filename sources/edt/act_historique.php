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
$query = 'SELECT * FROM pain_edt WHERE 1 ORDER BY timestamp DESC';
$result = mysql_query($query) or die('ERREUR : '.mysql_error());
while($ligne = mysql_fetch_array($result)) {
    echo '<div>[';
    echo '<a href="#" onclick="charger('.$ligne["id_edt"].')">';
    echo $ligne["timestamp"].'</a> ';
    echo '<span class="login">'.$ligne["login"].'</span>] : ';
    echo '<span class="message">'.$ligne["message"].'</span>';
    echo '</div>';
}
?>
