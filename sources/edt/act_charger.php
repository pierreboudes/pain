<?php


require_once("../../secret/pconnect.php"); 
mysql_query("SET NAMES 'utf8'");
require_once('utils.php');

$id = postclean('id');
if ($id) {
    $query = 'SELECT * FROM pain_edt WHERE id_edt = '.$id.' LIMIT 1';
} else {
    $query = 'SELECT * FROM pain_edt WHERE 1 ORDER BY timestamp DESC LIMIT 1';
}
$result = mysql_query($query) or die('ERREUR : '.mysql_error());
$ligne = mysql_fetch_array($result);
echo $ligne["edt_html"];
?>
