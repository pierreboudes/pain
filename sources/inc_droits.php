<?php /* -*- coding: utf-8 -*- */
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

/* gestion des droits (temporaire) */
require_once('authentication.php'); 
authrequired();

function peuttoutfaire() {
    global $user;
    if ($user["su"]) return true;
    return false;
}

function peutediter($type, $id, $id_parent) {
    if ($id != NULL) {
	if ($type == "sformation") return peuteditersformation($id);
	if ($type == "formation") return peutediterformation($id);
	if ($type == "cours") return peuteditercours($id);
	if ($type == "tranche") return peuteditertranche($id);
	if ($type == "enseignant") return peutediterenseignant($id);
	if ($type == "service") return peutediterservice($id);
	if ($type == "choix") return peutediterchoix($id);
    }
    if ($id_parent != NULL) {
	if ($type == "sformation") return peuteditersformationdelannee($id_parent);
	if ($type == "formation") return peutediterformationdelasformation($id_parent);
	if ($type == "cours") return peuteditercoursdelaformation($id_parent);
	if ($type == "tranche") return peuteditertrancheducours($id_parent);
	if ($type == "service") return peutediterservicedeenseignant($id_parent);
	if ($type == "choix") return peutchoisir();
    }
    if ($type == "enseignant") return peutproposerenseignant();
    return false;
}

function peutchoisir() {
    $query = "SELECT id_enseignant 
              FROM pain_enseignant 
              WHERE id_enseignant = ".$user["id"]." LIMIT 1";
    $result = mysql_query($query);
    if (mysql_fetch_array($result)) {
	return true;
    }
   return false;
}

function selectenseignantschoix($id_choix) {
    $query = "SELECT pain_choix.id_enseignant AS enseignant, 
                     pain_cours.id_enseignant AS respcours, 
                     pain_formation.id_enseignant AS respannee,
                     pain_sformation.id_enseignant AS respformation
              FROM pain_choix, pain_cours, pain_formation, pain_sformation
              WHERE pain_choix.id_choix = $id_choix
              AND pain_cours.id_cours = pain_choix.id_cours
              AND pain_formation.id_formation = pain_cours.id_formation
              AND pain_sformation.id_sformation = 
                  pain_formation.id_sformation";
    $res = mysql_query($query) or die("ERREUR selectenseignantschoix($id_choix): ".mysql_error());
    $r = mysql_fetch_array($res);
    return $r;
}

function peutediterchoix($id_choix) {
    global $user;
    if ($user["su"]) return true;
    $r = selectenseignantschoix($id_choix);
    /* l'intervenant ne peut pas editer sa propre intervention :
    if ($user["id_enseignant"] == $r["enseignant"]) return true;
    */
    /* le responsable du cours ne peut pas editer :
    if ($user["id_enseignant"] == $r["respcours"]) return true;
    */
    if ($user["id_enseignant"] == $r["respannee"]) return true;
    if ($user["id_enseignant"] == $r["respformation"]) return true;
    return false;
}


function peutsupprimersformation($id) {
    return peuttoutfaire();
}

function peutsupprimerformation($id) {
    return peuttoutfaire();
}

function peutsupprimerchoix($id_choix) {
    global $user;
    if ($user["su"]) return true;
    $r = selectenseignantschoix($id_choix);
    /* l'intervenant peut supprimer son choix : */
    if ($user["id_enseignant"] == $r["enseignant"]) return true;
    /* le responsable du cours ne peut pas 
     if ($user["id_enseignant"] == $r["respcours"]) return false; */
    if ($user["id_enseignant"] == $r["respannee"]) return true;
    if ($user["id_enseignant"] == $r["respformation"]) return true;
    return false;
}


function peutvoirstatsservices() {
    global $user;
    return ($user["stats"] == 1) or ($user["su"] == 1);
}


function peuteditercours($id_cours) {
    global $user;
    if ($user["su"]) return true;
    $query = "SELECT pain_cours.id_enseignant AS respcours, 
                     pain_formation.id_enseignant AS respannee,
                     pain_sformation.id_enseignant AS respformation
              FROM pain_cours, pain_formation, pain_sformation
              WHERE pain_cours.id_cours = $id_cours
              AND pain_formation.id_formation = pain_cours.id_formation
              AND pain_sformation.id_sformation = 
                  pain_formation.id_sformation";
    $res = mysql_query($query) or die("ERREUR peuteditercours($id_cours)");
    $r = mysql_fetch_array($res);
/* le responsable du cours ne peut plus !    
    if ($user["id_enseignant"] == $r["respcours"]) return true; */
    if ($user["id_enseignant"] == $r["respannee"]) return true;
    if ($user["id_enseignant"] == $r["respformation"]) return true;
    return false;
}


function peutmajcours($id_cours) {
    global $user;
    if ($user["su"]) return true;
    $query = "SELECT pain_cours.id_enseignant AS respcours, 
                     pain_formation.id_enseignant AS respannee,
                     pain_sformation.id_enseignant AS respformation
              FROM pain_cours, pain_formation, pain_sformation
              WHERE pain_cours.id_cours = $id_cours
              AND pain_formation.id_formation = pain_cours.id_formation
              AND pain_sformation.id_sformation = 
                  pain_formation.id_sformation";
    $res = mysql_query($query) or die("ERREUR peutmajcours($id_cours)");
    $r = mysql_fetch_array($res);
    if ($user["id_enseignant"] == $r["respcours"]) return true; 
    if ($user["id_enseignant"] == $r["respannee"]) return true;
    if ($user["id_enseignant"] == $r["respformation"]) return true;
    return false;
}


function peutediterformationducours($id_cours) {
    global $user;
    if ($user["su"]) return true;
    $query = "SELECT pain_formation.id_enseignant AS respannee,
                     pain_sformation.id_enseignant AS respformation
              FROM pain_cours, pain_formation, pain_sformation
              WHERE pain_cours.id_cours = $id_cours
              AND pain_formation.id_formation = pain_cours.id_formation
              AND pain_sformation.id_sformation = 
                  pain_formation.id_sformation";
    $res = mysql_query($query) or die("ERREUR peutediterformationducours($idcours)");
    $r = mysql_fetch_array($res);
    if ($user["id_enseignant"] == $r["respannee"]) return true;
    if ($user["id_enseignant"] == $r["respformation"]) return true;
    return false;
}

function peuteditercoursdelaformation($id_formation) {
    return  peutediterformation($id_formation);
}

function peuteditertranche($id_tranche) {
    global $user;
    if ($user["su"]) return true;
    $query = "SELECT pain_tranche.id_enseignant AS enseignant, 
                     pain_cours.id_enseignant AS respcours, 
                     pain_formation.id_enseignant AS respannee,
                     pain_sformation.id_enseignant AS respformation
              FROM pain_tranche, pain_cours, pain_formation, pain_sformation
              WHERE pain_tranche.id_tranche = $id_tranche
              AND pain_cours.id_cours = pain_tranche.id_cours
              AND pain_formation.id_formation = pain_cours.id_formation
              AND pain_sformation.id_sformation = 
                  pain_formation.id_sformation";
    $res = mysql_query($query) or die("ERREUR peuteditertranche($id_tranche)");
    $r = mysql_fetch_array($res);
    /*  if ($user["id_enseignant"] == $r["enseignant"]) return false;
     if ($user["id_enseignant"] == $r["respcours"]) return false; */
    if ($user["id_enseignant"] == $r["respannee"]) return true;
    if ($user["id_enseignant"] == $r["respformation"]) return true;
    return false;
}

function peuteditertrancheducours($id_cours) {
    return peuteditercours($id_cours); /* le responsable du cours ne peut pas */
}

function peuteditersformation($id_sformation) {
    return peuttoutfaire();
}

function peuteditersformationdelannee($annee) {
    return peuttoutfaire();
}


function peutediterformation($id_formation) {
    global $user;
    if ($user["su"]) return true;
    $query = "SELECT pain_formation.id_enseignant AS respannee,
                     pain_sformation.id_enseignant AS respformation
              FROM pain_formation, pain_sformation
              WHERE pain_formation.id_formation = $id_formation
              AND pain_sformation.id_sformation = 
                  pain_formation.id_sformation";
    $res = mysql_query($query) or die("ERREUR peutediterformation($id_formation)");
    $r = mysql_fetch_array($res);
    if ($user["id_enseignant"] == $r["respannee"]) return true;
    if ($user["id_enseignant"] == $r["respformation"]) return true;
    return false;
}

function peutediterformationdelasformation($id_sformation) {
    global $user;
    if ($user["su"]) return true;
    $query = "SELECT  pain_sformation.id_enseignant AS respformation
              FROM pain_sformation
              WHERE pain_sformation.id_sformation = $id_sformation";
    $res = mysql_query($query) or die("ERREUR peutediterformationdelasformation($id_sformation)");
    $r = mysql_fetch_array($res);
    if ($user["id_enseignant"] == $r["respformation"]) return true;
    return false;
}

function peutediterenseignant($id_enseignant = 0) {
    global $user;
    return ($user["su"] == 1) or ($id_enseignant == $user["id_enseignant"]);
}

function peutediterservice($id_serv = 0X0) {
    global $user;
    list($id_enseignant,$an) = split('X',$id_serv);
    return ($user["su"] == 1) or ($id_enseignant == $user["id_enseignant"]);
}

function peutsupprimerservice($id_enseignant, $an) {
    global $user;
    return ($user["su"] == 1) or ($id_enseignant == $user["id_enseignant"]);
}

function peutediterservicedeenseignant($id_enseignant) {
    global $user;
    return ($user["su"] == 1) or ($id_enseignant == $user["id_enseignant"]);
}

function peutproposerenseignant() {
    global $user;
    if ($user["su"]) return true;
    $id = $user["id_enseignant"];
    $q = "SELECT 
          ((SELECT COUNT(id_cours) FROM pain_cours 
                 WHERE id_enseignant = $id) +
          (SELECT COUNT(id_formation) FROM pain_formation 
                 WHERE id_enseignant = $id) +
          (SELECT COUNT(id_sformation) FROM pain_sformation
                 WHERE id_enseignant = $id)) AS resp";
    $res = mysql_result($q) or ("ERREUR peutproposerenseignant()");
    $r = mysql_fetch_array($res);
    return 0 < $r["resp"];
}

function peutsupprimerenseignant($id_enseignant = 0) {
    global $user;
    return ($user["su"] == 1);
}
?>