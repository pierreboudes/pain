<?php

/** Error reporting */
error_reporting(E_ALL);

require_once('authentication.php');
$user = authentication();

require_once('inc_functions.php');
require_once('utilsExcel.php');
require_once('inc_connect_ro.php');

$id=$user['id_enseignant'];

/* par defaut on sert la feuille de l'utilisateur */
$id_enseignant = $id;
$NOM=$user['nom'].' '.$user['prenom'];
$GRADE=$user['statut'];
if ($GRADE=='')
	$GRADE='MCF Info';
$STATUTAIRE=$user['service'];

/* mais si on a un identifiant dans l'url on utilise plutot celui-ci */
if (isset($_GET['id_enseignant'])) {
	    $id_enseignant = getnumeric("id_enseignant");
	    $query = "SELECT prenom, nom, login, stats, service, statut, su
		           FROM pain_enseignant 
			   WHERE id_enseignant=$id_enseignant";
	    $result = $link->query($query);
	    if ($user = $result->fetch_array()) {
		    $NOM=$user['nom'].' '.$user['prenom'];
		    $GRADE=$user['statut'];
			if ($GRADE=='')
				$GRADE='MCF Info';
		    $STATUTAIRE=$user['service'];
	    }
}

/*
$ANNEE = getnumeric("annee");
if (NULL == $ANNEE &&
	        isset($_COOKIE["painAnnee"]) && $_COOKIE["painAnnee"]>2000 && $_COOKIE["painAnnee"]<2500) {

			        $ANNEE=$_COOKIE["painAnnee"];
		} else {
			        $ANNEE = date('Y', strtotime('-5 month'));
		}*/
$ANNEE=default_year();

/*L'entete */
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
<head><title>Declaration de Service Prévisionnel</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<link type="text/css" href="/styleServices.css" media="all" rel="stylesheet">
</head><body>';
echo '<h1 class=titre>Déclaration de service prévisionnel '.$ANNEE.' - '.($ANNEE+1).'</h1>';

if ($user['su']) { // admin
    echo '<center><div class="infobox" style="width:33%;">';
    echo '<form methode="GET" id="choixenseignant" class="formcours" name="enseignant" action="#">';
    echo '<select name="id_enseignant" style="display:inline;">';
    ig_formselectenseignants($id_enseignant);
    echo '</select>';
    echo '<input type="submit" value="OK" style="display:inline;"/>';
    echo '</form>'."\n";
    echo '</div></center>';
}

echo '<p>Génération en cours...</p>';flush();ob_flush();

$NOMsimple=str_replace("'","",$NOM);
$NOMsimple=str_replace("&#039;","",$NOM); // apostrophe codee html
$NOMsimple=str_replace(" ","",$NOMsimple);
init_excel($NOM,$GRADE,$STATUTAIRE,$ANNEE);


$requete="SELECT nom_cours,
	ifnull(pain_tranche.ctd,0) as ctd,
	ifnull(pain_tranche.cm,0) as cm,
	ifnull(pain_tranche.td,0) as td,
	ifnull(pain_tranche.tp,0) as tp,
	ifnull(pain_tranche.alt,0) as alt,
	semestre,
	parfum,
	pain_cours.code_geisha as geisha,
	pain_formation.code_geisha as code_etape,
	pain_formation.nom as nom_formation
	FROM pain_tranche,pain_cours,pain_formation,pain_sformation
	WHERE 
	pain_cours.id_formation=pain_formation.id_formation 
	AND pain_formation.id_sformation=pain_sformation.id_sformation
	AND pain_sformation.annee_universitaire= $ANNEE
	AND pain_tranche.id_cours=pain_cours.id_cours 
	AND pain_tranche.id_enseignant=$id_enseignant
	ORDER BY semestre";

$listeCours= $link->query($requete)
	       or die ("Échec de la requête".$requete."\n".$link->error);

/* d'apres le template, le max indique la position du sous-total */
$baserow['Initiale1']=13;
$maxrow['Initiale1']=24;
$baserow['Autre1']=$maxrow['Initiale1']+2;
$maxrow['Autre1']=$baserow['Autre1']+5;
$baserow['FC1']=$maxrow['Autre1']+2;
$maxrow['FC1']=$baserow['FC1']+5;

$baserow['Initiale2']=5;
$maxrow['Initiale2']=16;
$baserow['Autre2']=$maxrow['Initiale2']+2;
$maxrow['Autre2']=$baserow['Autre2']+5;
$baserow['FC2']=$maxrow['Autre2']+2;
$maxrow['FC2']=$baserow['FC2']+5;

$baserow['resp']=15;
$maxrow['resp']=23;

echo '<table><thead><tr><th>FC/FI</th><th>Sem.</th><th>Cours</th><th>type</th><th>Volume H.eq.TD</th></tr></thead>';
echo '<tbody>';
while ($cours = $listeCours->fetch_assoc()) {
	echo '<tr>';

	if ($cours['code_etape'] != '')  { // sinon matiere spéciale: resp ou stages ou...
		$duree=$cours['cm']*1.5+$cours['td']+$cours['tp']+$cours['alt']+1.125*$cours['ctd'];
		//echo $duree. 'H eq.TD</p>';
		if ($cours['ctd']!=0)
			$type='Cours-TD';
		else if ($cours['cm']==0 )
			$type='TD-TP';
		else if ($cours['td']==0)
			$type='Amphi';
		else 
			$type='divers';
		if ($cours['parfum']!='FC')
			$parfum='Initiale';
		else
			$parfum='FC';

	echo '<td>'.$parfum.'</td>';
	echo '<td>'.$cours['semestre'].'</td><td>'.$cours['nom_cours'].'</td>';
	echo '<td>'.$type.'</td>';
	echo '<td>'.$duree.'</td></tr>';

		if ($cours['semestre']==0) {/* de façon arbitraire 1/2 sur S1 et 1/2 sur S2 */
			//affecteCoursExcel($sem, $row, $codeEtape, $codeMatiere, $nomMatiere, $type, $eqTD) 
			$maxrow[$parfum.'1']=
				ajouteCoursExcel(1,$baserow[$parfum.'1']++, $maxrow[$parfum.'1'],$cours['code_etape'], 
				$cours['geisha'], $cours['nom_cours'], $type, $duree/2);
			$maxrow[$parfum.'2']=
				ajouteCoursExcel(2,$baserow[$parfum.'2']++, $maxrow[$parfum.'2'],$cours['code_etape'], 
				$cours['geisha'], $cours['nom_cours'], $type, $duree/2);
		} else {
			$maxrow[$parfum.$cours['semestre']]=
				ajouteCoursExcel($cours['semestre'],
					$baserow[$parfum.$cours['semestre']]++, 
					$maxrow[$parfum.$cours['semestre']],
					$cours['code_etape'], 
					$cours['geisha'], $cours['nom_cours'], $type, $duree);
		}

	} else {// if pas de code_etape
		$maxrow['resp']=
			ajouteRespExcel($baserow['resp']++, $maxrow['resp'],
				$cours['nom_cours'], $cours['nom_formation'], $cours['td']);
		echo '<td>resp.</td>';
		echo '<td>'.$cours['semestre'].'</td><td>'.$cours['nom_cours'].'</td>';
		echo '<td></td>';
		echo '<td>'.$cours['td'].'</td></tr>';
	}
	flush();
	ob_flush();
} // while cours

echo '</tbody></table>';

finaliseExcel($maxrow);

// Redirect output to a client’s web browser (Excel5)
/*header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="service.xls"');
header('Cache-Control: max-age=0');


$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
 */

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//$objWriter->setPreCalculateFormulas(FALSE);
$objWriter->save("declarations/declaration-$NOMsimple.xls");

echo '<p>Fichier Excel généré: <a href="declarations/declaration-'.$NOMsimple.'.xls">declaration-'.$NOMsimple.'.xls</a></p>';

exit;
?>
