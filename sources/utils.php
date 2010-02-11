<?php
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

function microsecoffset() {
    static $us = 0;
    if (!$us) $us = microtime(true);
    return (microtime(true) - $us);
}

microsecoffset(); /* armee */

function postclean($s) {
    if (isset($_POST[$s])) {
	if(get_magic_quotes_gpc()) {
	    return trim(htmlspecialchars(mysql_real_escape_string(stripslashes(($_POST[$s]))), ENT_QUOTES));
	}
	else {
	    return trim(htmlspecialchars(mysql_real_escape_string($_POST[$s]), ENT_QUOTES));
	}
    }
    else return NULL;
}

function getclean($s) {
    if (isset($_GET[$s])) {
	if(get_magic_quotes_gpc()) {
	    return trim(htmlspecialchars(mysql_real_escape_string(stripslashes(($_GET[$s]))), ENT_QUOTES));
	}
	else {
	    return trim(htmlspecialchars(mysql_real_escape_string($_GET[$s]), ENT_QUOTES));
	}
    }
    else return NULL;
}


function postnumclean($s) {
    return str_replace(',','.',str_replace(' ', '',postclean($s)));
}

function pain_log($message, $logname='pain') {
    global $user;
        /* vu qu'on écrit un fichier on ne veut pas de process appelant qui boucle... */
	static $compteur;
	if ($compteur++ > 10) return;

//	$pid = '(pid '.@getmypid().')';
	$message = preg_replace("/\n+/", " ", $message);
//	$message = preg_replace("/\n*$/", "\n", $message);
	$message .= ' -- '.date("M d H:i:s").' '.$user['login']. '\n';
        $logfile = dirname($_SERVER['SCRIPT_FILENAME'])."/painlogs/".$logname .'.log';
/*	echo $logfile;
		if (@is_readable($logfile)) echo "readable";
*/
	if (@is_readable($logfile)
	AND (!$s = @filesize($logfile) OR $s > 100*1024)) {
		$rotate = true;
		$message .= "-- [ rotate ]\n";
	} else $rotate = '';
	$f = @fopen($logfile, "ab");
	if ($f) {
		fputs($f, htmlspecialchars($message));
		fclose($f);
	}
	if ($rotate) {
		@unlink($logfile.'.5');
		@rename($logfile.'.4',$logfile.'.5');
		@rename($logfile.'.3',$logfile.'.4');
		@rename($logfile.'.2',$logfile.'.3');
		@rename($logfile.'.1',$logfile.'.2');
		@rename($logfile,$logfile.'.1');
		
	}
}

function ig_statsmysql() {
    echo '<div><pre>';
    $status = implode("\n",explode('  ', mysql_stat()));
    echo "MYSQL\n";
    echo $status;
    $us = round(microsecoffset() * 1000);
    echo "\nPage servie en : ".$us."ms ";
    echo '</pre></div>';
}
?>