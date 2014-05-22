<?php
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

/* inclusion du fichier realisant la connexion avec les donnees sensibles en dur : */
require_once("iconnect.php"); 
/* ce fichier contient quelque chose comme :
<?php
$link = new mysqli("localhost", "utilisateur", "mot de passe", "base");
if ($link->connect_errno) {
    printf("Échec de la connexion : %s\n", $mysqli->connect_error);
    die();
}
?>
*/
$link->query("SET NAMES 'utf8'");

require_once('utils.php');

/**
   Sauvegarde la base à intervalles réguliers (chaque quinzaine), avec rotation des fichiers.
   @param $force forcer la backup et la rotation courte.
 */
function bkp_base($force = false) {

    $nbl = 3; /* nombre de sauvegardes long terme (8w) */
    $nb = 4; /* nombre de sauvegardes court terme (2w) */

    /* vu qu'on écrit un fichier on ne veut pas de process appelant qui boucle... */
    static $compteur = 0;
    if (++$compteur > 1) return;

    $dir = dirname($_SERVER["SCRIPT_FILENAME"]);
    $s = @stat($dir.'/bkp/void_bkp.txt');

    /* faut-il faire la backup ? */
    if  ((time() < ($s[9] + 1209600)) && !$force) return false; 
    @touch($dir.'/bkp/void_bkp.txt');

    $filename = $dir."/bkp/bkp.sql.gz";

    /* Faut-il faire la rotation des fichiers long terme ? */
    $longrotate = false;
    if (!@file_exists($filename.".L1")) {
	$longrotate = true;
    } else {
	$s = @stat($filename.".L1");
	if  (time() > ($s[9] + 4838400)) { /* L1 a plus de huit semaines */
	    $longrotate = true;
	}
    }

    if ($longrotate) { /* rotation des sauvegardes long terme */ 	    
	@unlink($filename.".L$nbl");
	for ($i = $nbl; $i > 1; --$i) {
	    if (@file_exists($filename.".L".($i - 1))) {
		@rename($filename.".L".($i - 1), $filename.".L$i");
	    }
	}
	if (@file_exists($filename.".$nb")) {
	    @rename($filename.".$nb", $filename.'.L1');
	    @touch($filename.'.L1');

	}
    } else {
	@unlink($filename.".$nb");
    }
    
    /* rotation court terme */
    for ($i = $nb; $i > 1; --$i) {
	@rename($filename.".".($i - 1), $filename.".$i");
    }
    @rename($filename, $filename.'.1');	
    
    /* sauvegarde fraiche */
    pain_log("-- backup base : ".$dir."/bkp/bkp.sql.gz");	
    $done = dump($filename, false);
    if (!$done) {
	pain_log("-- warning: backup problem (incomplete output)");
    } else {
	pain_log("-- backup complete");
    }
    return $done;
}

/**
   Enregistre le contenu de la base dans un fichier sql.gz
   @param $filename nom du fichier à créer.
   @param $echoes faut-il activer une sortie écran pour contrôle ?
 */
function dump($filename, $echoes = false) {
/* source: http://www.lyxia.org/blog/developpement/script-php-de-sauvegarde-mysql-571 */
    global $link;

    /* fichier de sortie gzippé */
    $fp = gzopen($filename, 'w');

    if (! $fp) {
	errmsg("erreur avec la creation de fichier $filename");
	return(false);
    }
    /* liste des tables de la base */
    $query = 'SHOW TABLES ';
    if (!($tables = $link->query($query))) {
	if ($echoes) errmsg("erreur avec la requete :\n".$query."\n".$link->error);
	return false;
    }

    while ($donnees = $tables->fetch_array())     /* Pour chaque table */
    {
	$table = $donnees[0];
	if ($echoes) echo "<p>".$table;
       
	/* Creation de la table (structure de la table) */
	$query = 'SHOW CREATE TABLE '.$table;
	if (!($tres = $link->query($query))) {
	    if ($echoes) errmsg("erreur avec la requete :\n".$query."\n".$link->error);
	    return false;
	}	    
	$tableau = $tres->fetch_array();
	$tableau[1] .= ";\n";
	$insertions = $tableau[1];
	gzwrite($fp, $insertions);
	$tres->free();


	/* Remplissage de la table */
	$query = 'SELECT * FROM '.$table;
	if (!($tres = $link->query($query))) {
	    if ($echoes) errmsg("erreur avec la requete :\n".$query."\n".$link->error);
	    return false;
	}

	$nbr_champs = $tres->field_count;
	while ($ligne = $tres->fetch_array())
	{
	    if ($echoes) echo '. ';
	    $insertions = 'INSERT INTO '.$table.' VALUES (';
	    for ($i=0; $i<$nbr_champs; $i++)
	    {
		$insertions .= '\'' . $link->real_escape_string($ligne[$i]) . '\', ';
	    }
	    $insertions = substr($insertions, 0, -2);
	    $insertions .= ");\n";
	    gzwrite($fp, $insertions);
	}
	if ($echoes) echo "(".$tres->num_rows.")</p>";
	$tres->free(); 
    }
    gzclose($fp);
    if ($echoes) echo "<p>Done.</p>";
    return true;
}


/* appel du controle de backup */
bkp_base();
?>
