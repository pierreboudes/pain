<?php
$link = new mysqli("localhost", "pain_admin", "CHANGEME", "pain_db");
if ($link->connect_errno) {
    printf("Échec de la connexion : %s\n", $mysqli->connect_error);
    die();
}
?>

