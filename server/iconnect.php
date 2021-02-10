<?php
$link = new mysqli("pain-mariadb", "pain_demo", "anotherinsecuredpassword", "pain_demo");
if ($link->connect_errno) {
    printf("Échec de la connexion : %s\n", $mysqli->connect_error);
    die();
}
?>
