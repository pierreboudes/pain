<?php

require_once('inc_connect_ro.php');
$ANNEE=date('Y', strtotime('-4 month'));
if (isset($_GET['annee'])) {
               $ANNEE = $_GET['annee'];
           }
$ANNEENEXT=$ANNEE+1;

/* L'entete */
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
<head><title>Services '. $ANNEE.'-'.$ANNEENEXT.'</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<link type="text/css" href="/styleServices.css" media="all" rel="stylesheet">
</head><body>
<h1 class=titre>Services des enseignants d\'informatique, département informatique, année '.$ANNEE.'-'.$ANNEENEXT.'</h1>';

/* on récupère les noms de super-formations */
$reqListeSuperFormations = "select nom,id_sformation from pain_sformation WHERE annee_universitaire = $ANNEE order by id_sformation";
$listeSuperFormations = $link->query($reqListeSuperFormations)
            or die("Échec de la requête".$reqListeSuperFormations."\n".$link->error);

while ($sformation = $listeSuperFormations->fetch_row()) {
	echo "<h1>$sformation[0]</h1>";
	$idsf=$sformation[1];

	$reqListeFormations="select pain_formation.nom,pain_formation.id_formation,prenom,pain_enseignant.nom from pain_formation,pain_enseignant where id_sformation=$idsf and pain_enseignant.id_enseignant=pain_formation.id_enseignant order by annee_etude,pain_formation.nom" ;
	$listeFormations = $link->query($reqListeFormations)
		or die ("Échec de la requête".$reqListeFormations."\n".$link->error);
	
	while ($formations = $listeFormations->fetch_row()) {
		echo "<h2>$formations[0] - $formations[2] $formations[3] ($formations[1])</h2>";
		$idf=$formations[1];
		$reqListeCours="select nom_cours,id_cours from pain_cours where id_formation=$idf and nom_cours != '(Responsabilité semestre)' order by nom_cours";
		$listeCours = $link->query($reqListeCours);

		echo '<table><thead>
			<tr>
			<th>Cours</th><th>CM</th><th>TD</th><th>EqTD</th><th>Nom</th><th>GRP</th><th>Remarques</th>
			</tr>
			</thead><tbody>';
		while ($cours = $listeCours->fetch_row()) {
			$reqChoix="select pain_enseignant.nom from pain_enseignant, pain_choix 
				where pain_enseignant.id_enseignant=pain_choix.id_enseignant 
				and pain_choix.id_cours=$cours[1]";
			$listeChoix=$link->query($reqChoix)
				or die ("Échec de la requête".$reqChoix."\n".$link->error);
			if ($listeChoix->num_rows >0)
				$existeCandidats=1;
			else
				$existeCandidats=0;


			$reqCoursT="select 
IF(pain_tranche.cm IS NULL, '', IF(pain_tranche.cm=0,'',pain_tranche.cm))  as CM, 
ifnull(pain_tranche.td,'') as TD, 
ifnull(pain_tranche.cm,0)*1.5 + ifnull(pain_tranche.td,0) as EqTD,
pain_enseignant.nom as Nom, 
IF(pain_tranche.groupe=0,'',pain_tranche.groupe) as GRP, 
ifnull(pain_tranche.remarque,'') as Remarques
from pain_tranche, pain_enseignant 
where 
pain_tranche.id_enseignant = pain_enseignant.id_enseignant 
and pain_tranche.id_cours = $cours[1]
ORDER by pain_tranche.cm DESC, pain_tranche.groupe";

		$listeCoursT = $link->query($reqCoursT) 
			or die ("Échec de la requête".$reqCoursT."\n".$link->error);

		$first=1;
		while ($coursT = $listeCoursT->fetch_row()) {
			echo '<tr class="';
			if ($coursT[3]=='')
				echo 'attention ' ;
			if ($first==1) {
				$first=0;
				echo 'prem"><th rowspan="'.($listeCoursT->num_rows+$existeCandidats).'">'.$cours[0].'</th>';
			} else
				echo '">';
			
			for ($i=0; $i<mysqli_field_count($link); $i++) 
				echo '<td>'.htmlspecialchars($coursT[$i]).'</td>';
			
			echo '</tr>';
		} // while coursT

		if ($existeCandidats>0) {
		   	echo '<tr class="candidats"><td colspan="6">';
			while ($candidat = $listeChoix->fetch_row()) {
				echo $candidat[0].',';
			} //while candidat
			echo '</td></tr>';
		}

		}// while cours
		echo '</tbody></table>';
	}	// while formations
} 		// while sformations
echo '</body></html>'
?>
