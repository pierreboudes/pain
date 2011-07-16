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

/* inclusion du fichier realisant la connexion avec les donnees sensibles en dur : */
require_once("../secret/pconnect.php"); 
/* ce fichier contient quelque chose comme :
<?php
@mysql_pconnect("localhost", "utilisateur", "mot de passe") or die("Échec de la connexion au serveur de la base de données.");
@mysql_selectdb("pain") or die("Échec de sélection de la base de données.");
?>
*/
mysql_query("SET NAMES 'utf8'");

require_once('utils.php');

function bkp_base($force = false) {
    $output = "";
    $dir = dirname($_SERVER["SCRIPT_FILENAME"]);
    $s = @stat($dir.'/bkp/void_bkp.txt');
    if  (time() > ($s[9] + 604800) || $force) /* weekly */
    {
	@touch($dir.'/bkp/void_bkp.txt');
	pain_log("-- backup base start");	
	$output = shell_exec("$dir/../secret/painbkp.sh $dir");
	if (0 == strlen($output)) {
	   pain_log("-- Warning: backup problem (no script output)");
	}
	pain_log("-- backup base end");
    }
    return $output;
}
/* le script painbkp.sh contient :
mkdir -p ${1}/bkp/;
cd ${1}/bkp/;
touch tata;
if [ -f "pain.2.sql.gz" ]; 
then rm pain.2.sql.gz; 
echo "rm  2"; 
fi
if [ -f "pain.1.sql.gz" ];
then mv pain.1.sql.gz pain.2.sql.gz;
echo "rotate 1->2";
fi
if [ -f "pain.sql.gz" ]; 
then mv pain.sql.gz pain.1.sql.gz; 
echo "rotate 0->1";
fi
mysqldump -p'PASS' -u'USER' BASE pain_cours pain_enseignant pain_tranche pain_formation pain_sformation pain_edt | gzip - > pain.sql.gz;
echo "backuped";
*/
/* BACKUP */
bkp_base();
?>