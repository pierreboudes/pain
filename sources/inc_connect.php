<?php
/* inclusion du fichier realisant la connexion avec les donnees sensibles en dur : */
include("../secret/pconnect.php"); 
/* ce fichier contient quelque chose comme :
<?php
@mysql_pconnect("localhost", "utilisateur", "mot de passe") or die("Échec de la connexion au serveur de la base de données.");
@mysql_selectdb("pain") or die("Échec de sélection de la base de données.");
?>
*/
mysql_query("SET NAMES 'utf8'");
?>
