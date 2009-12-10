<?php
require_once('../CAS.php');
// error_reporting(E_ALL & ~E_NOTICE);
phpCAS::client(CAS_VERSION_2_0,'cas.univ-paris13.fr',443,'/cas/',true);
// phpCAS::setDebug();
// no SSL validation for the CAS server
phpCAS::setNoCasServerValidation();
?>
