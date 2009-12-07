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
require_once("inc_functions.php");

if (isset($_POST["id_tranche"])) {

    $id = postclean("id_tranche");
    $c = selectionner_tranche($id);
    echo '<tr class="trtrancheform" id="formedittranche'.$id.'">';    
    echo '<td colspan="10">'."\n";
    echo '<form method="post" id="fedittranche'.$id.
         '" class="formtranche" name="tranche" action="">';
    echo '<table class="tabletrancheform">';
    ig_formtranche($c["id_cours"], 
		 $id,
		 $c["cm"],
		 $c["td"],
		 $c["tp"],
		 $c["alt"],
		 $c["id_enseignant"],
		 $c["groupe"],
		 $c["remarque"]);
    echo '</table>';
    echo '</form>'."\n";
    echo '</td></tr>'."\n";	
}
?>