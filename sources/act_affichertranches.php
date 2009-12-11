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
require_once("inc_functions.php");

if (isset($_POST["id_cours"])) {
    $id = postclean("id_cours"); 
    echo '<tr class="trtranches" id="tranchesducours'.$id.'">';
    echo '<td colspan="11" class="tranches">';
    echo '<table class="tranches">';
    ig_legendetranches($id);
    ig_listtranches(tranchesdecours($id)); 
    echo '<tr class="trtrancheform"><td colspan="10">';
    echo '<form method="post" id="formtranche'.$id.'" class="formtranche" name="tranche" action="act_ajoutertranche.php">';
    echo '<table class="tabletrancheform">';
    ig_formtranche($id);
    echo '</table></form>';
    echo '</td></tr>';
    echo '</table></td></tr>';

};
?>