<?php
/* connexion à la BD */

$USER='pain_ro';
$PASS='CHANGEME';

$link = new mysqli('localhost', $USER, $PASS, 'pain_db');
if ($link->connect_errno) {
            printf("Échec de la connexion : %s\n", $mysqli->connect_error);
                die();
}

$link->set_charset("utf8");
?>
