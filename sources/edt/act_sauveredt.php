<?php
require_once('utils.php');
require_once('authentication.php');

$contenu = postclean('content');
$message = postclean('message');
$login = postclean('login');

require_once("../../secret/pconnect.php"); 
mysql_query("SET NAMES 'utf8'");
if (phpCas::isSessionAuthenticated()) {
$query = 'INSERT INTO pain_edt (edt_html, message, login) 
          VALUES ("'.$contenu.'", "'.$message.'", "'.$login.'")';
$result = mysql_query($query) or die("ERREUR : ".mysql_error());
echo 'emploi du temps sauvegardé';
} else {
    echo "ERREUR : échec de l'authentification";
}
?>
