<?php
require_once('authentication.php'); 
if (isset($_SERVER['HTTP_REFERER'])) {
  $url = $_SERVER['HTTP_REFERER'];
  phpCAS::logoutWithRedirectService($url);
} else {
  phpCAS::logout();
  echo 'Sayonara';
}
?>
