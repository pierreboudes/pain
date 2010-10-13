<?php /* -*- coding: utf-8 -*-*/

function postclean($s) {
    if (isset($_POST[$s])) {
        if(get_magic_quotes_gpc()) {
            return trim(htmlspecialchars(mysql_real_escape_string(stripslashes(($_POST[$s]))), ENT_QUOTES));
        }
        else {
            return trim(htmlspecialchars(mysql_real_escape_string($_POST[$s]), ENT_QUOTES));
        }
    }
    else return NULL;
}

require_once('inc_connect.php'); 
require_once('authentication.php'); 
$login = authentication();
$prenom = postclean('prenom');
$nom = postclean('nom');

include("header.html");

$query = "SELECT login
              FROM signataire 
              WHERE login LIKE '$login' LIMIT 1";
$result = mysql_query($query) or die($query." erreur ".mysql_error());
    if ($user = mysql_fetch_array($result)) {
?>
	<p> Vous êtes déjà signataire de cette pétition, merci.</p>
	<?php
    } else {
	$query = "INSERT INTO signataire (login,prenom,nom) VALUES ('$login', '$prenom', '$nom')";
	$result = mysql_query($query) or die("Problème avec la base de données.".$query." erreur ".mysql_error());
	echo "<p> Merci d'avoir signé cette pétition.</p>";
    }
?>
<p><a href="./">Retour à la pétition</a>.</p></body></html>


