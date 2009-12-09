<?php
require_once('../CAS.php');
phpCAS::client(CAS_VERSION_2_0,'cas.univ-paris13.fr',443,'/cas/',true);

// no SSL validation for the CAS server
phpCAS::setNoCasServerValidation();
?>