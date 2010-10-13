<?php
@mysql_pconnect("localhost", "departement", "passdepartement") or die("Échec de la connexion au serveur de la base de données.");
@mysql_selectdb("petition") or die("Échec de sélection de la base de données.");
mysql_query("SET NAMES 'utf8'");
?>