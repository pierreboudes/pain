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
require_once('CAS.php');
// error_reporting(E_ALL & ~E_NOTICE);
phpCAS::client(CAS_VERSION_2_0,'cas.univ-paris13.fr',443,'/cas/',true);
// phpCAS::setDebug();
phpCAS::setNoCasServerValidation();

require_once('inc_connect.php');

function set_year($annee) {
    setcookie("painAnnee", $annee, time()+3600);
}

function default_year() {
    if (isset($_COOKIE["painAnnee"])) {// && is_numeric($_COOKIE["painAnnee"])
	return $_COOKIE["painAnnee"];
    }
    return date('Y', strtotime('-7 month'));
}

function annee_courante() {
/* si on a reçu une annee dans le formulaire on utilise celle la */
    if (isset($_POST['annee'])) {
	$annee = postclean('annee');
	set_year($annee);
    } else { 
/* par défaut on sert l'année courante */
	$annee = default_year();
    }
    return $annee;
}

function authentication() {
    if (isset($_COOKIE['painAnonyme'])) {
	return array("id_enseignant"=> -1,
		     "prenom" => "",
		     "nom" => "Anonyme",
		     "login" => "anonymous",
		     "su" => 0,
		     "stat" => 0
	    );
    }
    phpCAS::forceAuthentication();
    $login = phpCAS::getUser();    
    $query = "SELECT id_enseignant, prenom, nom, login, su, stats 
              FROM pain_enseignant 
              WHERE login LIKE '$login' LIMIT 1";
    $result = mysql_query($query);
    if ($user = mysql_fetch_array($result)) {
	return $user;
    } else {
	die("D&ecute;sol&ecute; votre login ($login) n'est pas enregistr&ecute; dans la base du d&ecute;partement. (<a href='logout.php'>logout</a>)");
    };
}

function authrequired() {
    if (isset($_COOKIE['painAnonyme'])) return;
    if (!(phpCAS::isAuthenticated())) {
	header("Location: http://perdu.com");
	die('Die in terror, picnic boy');
    }
}

function pain_logout() {
   if (isset($_COOKIE['painAnonyme'])) {
       setcookie("painAnonyme", "", time()-3600);
       echo 'Sayonara';
   } else {
       phpCAS::logout();
   }
}
?>