<?php
include("header.html");
require_once("inc_connect.php");
$query = "SELECT *
              FROM signataire 
              WHERE 1 ORDER BY modification ASC";
$result = mysql_query($query) or die($query." erreur ".mysql_error());
echo "<table><tr><th>Prénom</th><th>Nom</th></tr>";
while ($user = mysql_fetch_array($result)) {
    echo "<tr><td>".$user["prenom"]."</td><td>".$user["nom"]."</td></tr>";
}
echo "</table>";
?>
<p><a href="./">Retour à la pétition</a>.</p></body></html>

