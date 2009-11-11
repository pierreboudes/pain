<?php
/* -*- coding: utf-8 -*-*/

require_once("inc_connect.php");
require_once("inc_functions.php");

if (isset($_POST["id_tranche"])) {
    $id = postclean("id_tranche");
    supprimer_tranche($id);
};
?>