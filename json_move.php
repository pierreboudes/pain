<?php /* -*- coding: utf-8 -*-*/
/* Pain - outil de gestion des services d'enseignement        
 *
 * Copyright 2009-2012 Pierre Boudes,
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
require_once('authentication.php'); 
$user = authentication();
require_once("inc_connect.php");
require_once("utils.php");
require_once("inc_functions.php");
$annee = annee_courante();


/** Modifie le parent d'une entrée (attache l'entrée à un nouveau parent).
 */ 
function json_move_php($annee, $readtype, $id, $id_but) {
    global $user;
    global $link;
    if ($readtype == "cours") {	
	$type = "cours";
	$par = "formation";
/* 
    } else if ($readtype == "formation") {	
	$type = "formation";
	$par = "sformation";	
    } else if ($readtype == "tranche") {
	$type = "tranche";
	$par  = "cours";
    } else if ( ($readtype == "choix") || ($readtype == "longchoix")) {
	$type = "choix";
	$par = "cours"; */
    } else {
	errmsg("type indéfini");
    }

    if (!peutdeplacer($type,$id,$id_type)) {
        errmsg("droits insuffisants.");
    }

    /* ancienne entree */
    if ($type == "cours") {
	$old = selectionner_cours($id);
    }
    if ($type == "tranche") {
	$old = selectionner_tranche($id);
    }
    if ($type == "choix") {
	$old = selectionner_choix($id);
    }

    /* formation de la requete */
    $strset = "id_$par = $id_but";
       
    $query = "UPDATE pain_${type} ".
	    "SET $strset, modification = NOW() ".
	    "WHERE `id_$type`=".$id;
    
    if (!$link->query($query)) {
        errmsg("erreur avec la requete :\n".$query."\n".$link->error);
    }

    /* log de la requete */
    pain_log($query);
    if ($type == "cours") {
        historique_par_cmp(1, $old, $id);
    }
    if ($type == "tranche") {
        $tranchenew = selectionner_tranche($id);
        historique_par_cmp(2, $old, $tranchenew);	        
    }
    if ($type == "choix") {
        $choixnew = selectionner_choix($id);
        historique_par_cmp(3, $old, $choixnew);	        
    }

    echo '{"ok": "ok"}';
} 

/* récupération ndes paramètres GET/HTTP */
if (isset($_GET["type"])) {
    $readtype = getclean("type");
} else {
    errmsg('erreur du script (type manquant).');
}
if (isset($_GET["id"])) {
    $id = getnumeric("id");
} else {
    errmsg('erreur du script (id manquant).');
}
if (isset($_GET["id_but"])) {
    $id_but = getnumeric("id_but");
} else {
    errmsg('erreur du script (id_but manquant).');
}

/* appel */
json_move_php($annee, $readtype, $id, $id_but);

?>