<?php
require_once("../../secret/pconnect.php"); 
mysql_query("SET NAMES 'utf8'");

$query = 'SELECT * FROM pain_edt WHERE 1 ORDER BY timestamp DESC';
$result = mysql_query($query) or die('ERREUR : '.mysql_error());
while($ligne = mysql_fetch_array($result)) {
    echo '<div>[';
    echo '<a href="#" onclick="charger('.$ligne["id_edt"].')">';
    echo $ligne["timestamp"].'</a> ';
    echo '<span class="login">'.$ligne["login"].'</span>] : ';
    echo '<span class="message">'.$ligne["message"].'</span>';
    echo '</div>';
}
?>
