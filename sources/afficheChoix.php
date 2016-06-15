<?php

$idchoix=trim($_GET['id']);
if (!is_numeric($idchoix)) {
	exit;
}
require_once('authentication.php');
$user = authentication();
$annee = get_and_set_annee_menu();
require_once('utils.php');

$req = "SELECT pain_formation.id_sformation, pain_formation.id_formation, pain_cours.id_cours "
	."FROM pain_formation, pain_cours, pain_choix "
	."WHERE pain_choix.id_choix=$idchoix "
	."and pain_cours.id_cours=pain_choix.id_cours "
	."and pain_cours.id_formation=pain_formation.id_formation";

$liste=$link->query($req) or die('Echec requete'.$req);
while ($resu = $liste->fetch_assoc()) {
	$sf = $resu['id_sformation'];
	$f = $resu['id_formation'];
	$c = $resu['id_cours'];
}
header("Location:index.php?sf=$sf&f=$f&c=$c");
?>
