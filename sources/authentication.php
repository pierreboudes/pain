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
require_once('CAS.php');
// error_reporting(E_ALL & ~E_NOTICE);
//phpCAS::client(CAS_VERSION_2_0,'cas.univ-paris13.fr',443,'/cas/',true);
phpCAS::client(CAS_VERSION_2_0,'portail.cevif.univ-paris13.fr',443,'/cas/',true);
// phpCAS::setDebug();
//phpCAS::setNoCasServerValidation();
phpCAS::setCasServerCACert("/etc/pki/CA/chaine.pem");

require_once('inc_connect.php');

function set_year($annee) {
    setcookie("painAnnee", $annee, time()+3600);
}

date_default_timezone_set('Europe/Paris'); /* pour strtotime() */

/**  a partir du 21 mars : l'annee universitaire suivante, s'il n'y existe pas de sformation l'annee la plus recente
 */
function default_year() {
    global $link;
    if (isset($_COOKIE["painAnnee"]) && $_COOKIE["painAnnee"]>2000 && $_COOKIE["painAnnee"]<2500) {// && is_numeric($_COOKIE["painAnnee"])
	return $_COOKIE["painAnnee"];
    }
    $an = date('Y', strtotime('-3 month 1 week'));
    //$q = "SELECT coalesce($an - min($an - annee_universitaire), $an) as annee FROM pain_sformation";
    //$r = $link->query($q);
    //$res = $r->fetch_array();
    //return $res["annee"];    
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

function pain_getuser() {
    global $link;
    $login = phpCAS::getUser();
    $query = "SELECT id_enseignant, prenom, nom, login, su, stats, service, statut
                 FROM pain_enseignant 
                 WHERE login LIKE '$login' LIMIT 1";
    $result = $link->query($query);
    if ($user = $result->fetch_array()) {
	if ( (1 == $user["su"]) && isset($_COOKIE['painFakeId']) ){
	    $query = "SELECT id_enseignant 
                          FROM pain_enseignant 
                          WHERE id_enseignant = ".$user["id"]." LIMIT 1";
	    $result =$link->query($query);
	    if ($result->fetch_array()) {
		$user["id"] = cookieclean('painFakeId');
	    }
	}
	return $user;
    } else {
	return NULL;
    }
}

function authentication() {
    phpCAS::forceAuthentication();
    $user = pain_getuser();
    if (NULL == $user) {
	$login = phpCAS::getUser();
	die("D&eacute;sol&eacute; votre login ($login) n'est pas enregistr&eacute; dans la base du d&eacute;partement.Si vous &ecirc;tes membre du d&eacute;partement, vous pouvez envoyer un message votre &agrave; chef de d&eacute;partement avec votre login : $login. Pour sortir c'est par ici : <a href='logout.php'>logout</a>.");
    }
    return $user;
}

function no_auth() {
    if (phpCAS::isAuthenticated()) {
	return pain_getuser();
    }
    return NULL;
}

function weak_auth() {
    phpCAS::forceAuthentication();
    return pain_getuser();
}

function authrequired() {
    if (!(phpCAS::isAuthenticated())) {
	header("Location: http://perdu.com");
	die('Die in terror, picnic boy');
    }
}

function pain_logout() {
    phpCAS::logout();
}
?>
