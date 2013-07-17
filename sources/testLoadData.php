<?php
require_once('authentication.php');
$user = authentication();
$ANNEE = get_and_set_annee_menu();
require_once("inc_headers.php"); /* pour en-tete et pied de page */
require_once("inc_importfunc.php");
require_once('utils.php');

entete("Import CSV","pain_annuaire.js");


include("menu.php");

function select_enseignant($nom) {
	global $ANNEE;
	global $link;
	if ($nom==NULL || $nom=="" )
		return(3);//libre
	$reqform="SELECT pain_enseignant.id_enseignant FROM pain_enseignant, pain_service
		WHERE pain_enseignant.id_enseignant=pain_service.id_enseignant
		AND annee_universitaire=$ANNEE
		AND nom LIKE '$nom'";
	$r=$link->query($reqform) or die ('requete '.$reqform.' echouee '.$link->error);
	$row=$r->fetch_array(MYSQLI_NUM) or die ('Enseignant '.$nom.' inconnu');
	return($row[0]);
}

function select_formation($nom) {
	global $ANNEE;
	global $link;
	global $id_enseignant;

	$reqform="SELECT id_formation FROM pain_formation,pain_sformation 
		WHERE pain_formation.nom='$nom' 
		AND pain_formation.id_sformation=pain_sformation.id_formation 
		AND pain_sformation.annee_universitaire=$ANNEE";
	$r=$link->query($reqform) or die ('requete '.$reqform.' echouee '.$link->error);
	$row=$r->fetch_array(MYSQLI_NUM) or die ('formation '.$nom.' inconnue');
        return($row[0]);
}

function fun_string($s) {
	return ("'".addslashes($s)."'");
}
function idem($x) {
	return ($x);
}


$fichier="/tmp/FC.csv";
$table="pain_cours";

$fp = fopen("$fichier", "r") or die ("Fichier introuvable: Importation stoppée.");

// fgetcsv(ptr,longligne,delim, enclosure);
// récupère la première ligne pour le nom des champs.
$col = fgetcsv($fp,4096,';');  

$id_formation=142;
$id_enseignant=3; //libre

$champsLegaux = array(
	'nom_enseignant'=>'select_enseignant',
	'nom_formation'=>'select_formation',
	'nom_cours'=>'fun_string',
	'ctd'=>'idem',
	'cm'=>'idem',
	'td'=>'idem',
	'alt'=>'idem',
	'ctd'=>'ctd_to_cmtd');

$debreq='insert into '.$table.' (id_formation,';
for ($i=0;$i<count($col);$i++) {
	if (array_key_exists($col[$i],$champsLegaux)) {
		if ($col[$i]=='ctd') {
		       	if ($i==count($col)-1)  // cours-td doit etre converti en cours+TD et doit etre en der.
				$debreq=$debreq.'cm,td';
			else die ('Désolé ctd doit être en dernière position');
		} else if ($col[$i]=='nom_enseignant') {
			if ($id_enseignant!= 3) 
				die ('nom_enseignant aussi fixé dan le fichier!');
			$debreq=$debreq.'id_enseignant';
		} else if ($col[$i]=='nom_formation') {
			if ($id_formation>-1) die ('Erreur nom formation défini dans le fichier!');
			$debreq=$debreq.'id_formation';
		} else
			$debreq=$debreq.$col[$i];
	} else 
		die ('Champ '.$col[$i].' inconnu');
	if ($i<count($col)-1) {
		$debreq=$debreq.',';
	}
}

$debreq=$debreq.") VALUES ($id_formation,";

$numligne=1;
while ( ($ligne = fgetcsv($fp,4096,';')) ) {
	$req=$debreq;
	echo '<p>';
	for ($i=0;$i<count($ligne);$i++) {
		if ($col[$i]=='ctd') {
				$cm=round($ligne[$i]/3,0);
				$td=$ligne[$i] - 1.5*$cm;
				$req=$req."$cm,$td";
		} else {
				$req=$req.$champsLegaux[$col[$i]]($ligne[$i]);
		}
		if ($i<count($ligne)-1)
			$req=$req.',';
	}
	$req=$req.')';
	echo $req.'</p>';
	$r=$link->query($req) or die("erreur execution requete". $link->error);
}

 piedpage();
?>
