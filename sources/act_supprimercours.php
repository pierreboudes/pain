<?php
require_once("inc_connect.php");
require_once("inc_functions.php");

if (isset($_POST["id_cours"])) {
    $id = postclean("id_cours");
    supprimer_cours($id);
};
?>