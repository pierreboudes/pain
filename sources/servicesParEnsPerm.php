<?php

// Sortie sous forme CSV d'un tableau des heures faites par les enseignants permanents

require_once('inc_connect_ro.php');
$ANNEE='2012';
if (isset($_GET['annee'])) {
               $ANNEE = $_GET['annee'];
           }
$ANNEENEXT=$ANNEE+1;

//categorie=2 pour les permanents par défaut, espèrons que cela ne changera pas !
// id_enseignant=1 : cours annulé.
//
$reqListePermRespCours = "
	SELECT pain_enseignant.nom,prenom, pain_sformation.nom as 'sfor', 
	  pain_formation.nom as 'form', 
	  nom_cours,cm,td,alt,semestre,pain_cours.code_geisha as code
	FROM pain_sformation,pain_formation, pain_enseignant, pain_cours
	WHERE pain_sformation.annee_universitaire = $ANNEE 
	AND pain_sformation.nom != 'PRP/référentiel'
	AND pain_sformation.id_sformation=pain_formation.id_sformation
	AND pain_formation.id_formation=pain_cours.id_formation
	AND nom_cours != '(Responsable)'
	AND nom_cours != '(Responsabilité Semestre)'
	AND pain_cours.id_enseignant <> 1
	AND cm+td>0
	AND pain_enseignant.categorie=2
	AND pain_enseignant.id_enseignant=pain_cours.id_enseignant
	ORDER by pain_sformation.nom, pain_formation.nom, nom_cours";

/*
		$reqListePerm = "SELECT nom,prenom,pain_cours.nom_cours, pain_cours.semestre, pain_tranche.groupe
   FROM pain_tranche, pain_cours, pain_formation, pain_sformation
   WHERE pain_tranche.id_enseignant = pain_service.id_enseignant
     AND pain_tranche.id_cours = pain_cours.id_cours 
     AND pain_cours.id_enseignant <> 1
     AND pain_formation.id_formation = pain_cours.id_formation
     AND pain_sformation.id_sformation = pain_formation.id_sformation 
     AND pain_sformation.annee_universitaire =  $annee";
 */
													    
$listePermRespCours = $link->query($reqListePermRespCours)
	            or die('Échec de la requête'.$reqListePermRespCours."\n".$link->error);


echo 'Nom;Cours;Form.;CM;TD;alt;code';
while ($sPerm = $listePermRespCours->fetch_assoc()) {
	//$nomPerm = $sPerm[0];
	//$ligneCSV = $sPerm['nom'].';'.$sPerm['prenom'].';';
	// recherche s'il est resp. de matière.
	//print_r($sPerm);
	echo html_entity_decode($sPerm['nom'].';'.$sPerm['nom_cours'].';'.$sPerm['form'].';'.$sPerm['cm'].';'.$sPerm['td'].';'.$sPerm['alt'].';'.$sPerm['code'],ENT_QUOTES)."\n";

	//echo $ligneCSV."\n";
}


?>
