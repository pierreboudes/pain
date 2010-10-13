<?php /* -*- coding: utf-8 -*-*/
require_once("inc_connect.php");
include("header.html");
?>
<h1>Retraites : opposition de l'université Paris 13 à la réforme du Gouvernement</h1>
<p>
Les personnels de l'université Paris 13 soussignés s'opposent à la réforme des retraites telle que proposée par le gouvernement et actuellement en discussion au Parlement. 
</p>
<p>
Les personnels de l'université Paris 13 soussignés demandent l'ouverture de réelles négociations avec les partenaires sociaux.
</p>
<p>
 Ils soutiennent la mobilisation en cours contre cette réforme.
</p>

<p>
<?php
$query = "SELECT COUNT(*) AS total
              FROM signataire 
              WHERE 1";
$result = mysql_query($query) or die($query." erreur ".mysql_error());
$nombre = mysql_fetch_array($result);
echo "Déjà <b>".$nombre["total"]." signatures</b>.";
?>
</p>
<h2> Premiers signataires</h2>
<?php
$query = "SELECT *
              FROM signataire 
              WHERE 1 ORDER BY modification ASC LIMIT 10";
$result = mysql_query($query) or die($query." erreur ".mysql_error());
echo "<table><tr><th>Prénom</th><th>Nom</th></tr>";
 while ($user = mysql_fetch_array($result)) {
  echo "<tr><td>".$user["prenom"]."</td><td>".$user["nom"]."</td></tr>";
}
echo "</table>";
?>
<a href="list.php"> Liste complète des signataires</a>



<h2>Pour signer :</h2>

<form method="post" action="validate.php">
<fieldset>
<legend>Signataire</legend>
<label>Prénom</label>
<input type="text" class="formulaire" name="prenom" value="" /><br/>
<label>Nom</label>
<input type="text" class="formulaire"  name="nom" value="" /><br/>
</fieldset>

<div class="bouton"><input type="submit" value="Signer" /></div>
</form>
</body>
</html>



