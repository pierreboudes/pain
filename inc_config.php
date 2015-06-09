<?php /* -*- coding: utf-8 -*-*/
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
require_once('authentication.php');
require_once('utils.php');

function get_configuration($chaine) {
    global $link;
    $query = "SELECT valeur
              FROM pain_config
              WHERE configuration LIKE '$chaine'";
    $result = $link->query($query)  or die("Échec de la requête".$query."\n".$link->error);
    $res = "";
    if ($conf = $result->fetch_assoc()) {
        $res = $conf["valeur"];
    }
    return $res;
}

function set_year($annee) {
    setcookie("painAnnee", $annee, time()+3600);
}

date_default_timezone_set('Europe/Paris'); /* pour strtotime() */

/**  a partir du 1er juin : l'annee universitaire suivante, s'il n'y existe pas de sformation l'annee la plus recente
 */
function default_year() {
    global $link;
    /* si l'utilisateur a une preference etablie on la sert */
    if (isset($_COOKIE["painAnnee"])) {// && is_numeric($_COOKIE["painAnnee"])
        return $_COOKIE["painAnnee"];
    }
    /* sinon on tente notre chance avec l'annee courante */
    $an = date('Y', strtotime('-5 month'));
    $q = "SELECT annee_universitaire as annee FROM pain_sformation where annee_universitaire = $an";
    $r = $link->query($q);
    if ($res = $r->fetch_array()) {
       return $res["annee"];
    }
    /* L'annee courante n'est pas disponible, on retourne l'annee la
     * plus avancee */
    $q = "SELECT max(annee_universitaire) as annee FROM pain_sformation";
    $r = $link->query($q);
    if ($res = $r->fetch_array()) {
       return $res["annee"];
    }
    /* aucune annee configuree (ne doit pas arriver) */
    return $an;
}

function get_and_set_annee_menu() {
    $annee = getnumeric("annee_menu"); /* annee fixee par le menu en POST */
    if (NULL != $annee) {
	set_year($annee); /* change le cookie */
	return $annee;
    }
    /* pas d'annee par le menu */
    $annee = getnumeric("annee"); /* annee reçue une variable d'annee en get ou post */
    if (NULL == $annee) {
	$annee = default_year();
    }
    return $annee;
}

function annee_courante() {
    $annee = getnumeric("annee");
    if (NULL != $annee) {
	/* On a reçu une année dans en post ou GET */
	return $annee;
    }
    /* par défaut on sert l'année courante */
    return default_year();
}

?>