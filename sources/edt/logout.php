<?php
$url = $_SERVER['HTTP_REFERER'];
require_once('authentication.php'); 
phpCAS::logoutWithRedirectService($url);
?>