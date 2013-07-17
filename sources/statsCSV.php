<?php /* -*- coding: utf-8 -*-*/
/* Pain - outil de gestion des services d'enseignement        
 *
 * Copyright 2009-2012 Pierre Boudes,
 * département d'informatique de l'institut Galilée.
 *
 * This file is part of Pain.
 *
 * Pain is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Pain is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public
 * License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Pain.  If not, see <http://www.gnu.org/licenses/>.
 */
require_once('inc_connect_ro.php');
require_once("inc_headers.php"); /* pour en-tete et pied de page */
entete("statistiques CSV","pain.js");
require_once('utils.php');
$user="";
include("menu.php");
require_once('inc_functions.php');
// require_once('inc_statsfunc.php');

/**
génére le code HTML de la page statsCSV
 */

$ANNEE = getnumeric("annee");
if (NULL == $ANNEE &&
	isset($_COOKIE["painAnnee"]) && $_COOKIE["painAnnee"]>2000 && $_COOKIE["painAnnee"]<2500) {

        $ANNEE=$_COOKIE["painAnnee"];
} else {
	$ANNEE = date('Y', strtotime('-5 month'));
}


$ANNEENEXT=$ANNEE+1;

//categorie=2 pour les permanents par défaut, espèrons que cela ne changera pas !
// id_enseignant=1 : cours annulé.

$req = "CREATE TEMPORARY TABLE tmp
	SELECT pain_enseignant.nom,prenom, pain_enseignant.categorie,
	  pain_sformation.nom as 'sform', 
	  pain_formation.nom as 'form', 
	  nom_cours,pain_tranche.cm,pain_tranche.td,semestre,pain_cours.code_geisha as code
	FROM pain_sformation,pain_formation, pain_enseignant, pain_cours, pain_tranche
	WHERE pain_sformation.annee_universitaire = $ANNEE 
	AND pain_sformation.nom != 'PRP/référentiel'
	AND pain_sformation.id_sformation=pain_formation.id_sformation
	AND pain_formation.id_formation=pain_cours.id_formation
	AND pain_tranche.id_cours = pain_cours.id_cours
	AND nom_cours != '(Responsable)'
	AND nom_cours != '(Responsabilité Semestre)'
	AND pain_cours.id_enseignant <> 1
	AND ( pain_tranche.cm>0 OR pain_tranche.td>0 )
	AND pain_enseignant.id_enseignant=pain_tranche.id_enseignant
	ORDER BY nom,prenom,sform,form,nom_cours";

$link->query($req) or die('Echec creation table temporaire');

$reqListe = "SELECT * from tmp";
$liste = $link->query($reqListe)
	            or die('Échec de la requête'.$reqListe."\n".$link->error);

$fp = fopen("bkp/stats$ANNEE-$ANNEENEXT.csv",'w');

fwrite($fp,"Nom;Prenom;Categorie;sForm.;Form.;Cours;CM;TD;Code\n");
while ($sPerm = $liste->fetch_assoc()) {
	fwrite($fp, html_entity_decode(
		$sPerm['nom'].';'.$sPerm['prenom'].';'.$sPerm['categorie']
		.';'.$sPerm['sform']
		.';'.$sPerm['form']
		.';'.$sPerm['nom_cours']
		.';'.$sPerm['cm'].';'.$sPerm['td'],
		ENT_QUOTES)."\n");
}
fclose($fp);

echo '<center>';
echo '<h2>Statistiques</h2>';
echo '<p>Le fichier se trouve ici : <a href="bkp/stats'.$ANNEE.'-'.$ANNEENEXT.'.csv">www-info.iutv.univ-paris13.fr/pain/bkp/stats'.$ANNEE.'-'.$ANNEENEXT.'.csv</a></p>';
echo '<p>La catégorie 2 signifie permanent.</p>';
echo '<p></p>';
echo '<p> Uniquement les permanents : ';

$reqListePerm=$reqListe.' WHERE categorie=2';

$listePerm = $link->query($reqListePerm)
	            or die('Échec de la requête'.$reqListePerm."\n".$link->error);

$fp = fopen("bkp/statsPerm$ANNEE-$ANNEENEXT.csv",'w');
fwrite($fp,"Nom;Prenom;Cours;Form.;CM;TD;Code\n");
while ($sPerm = $listePerm->fetch_assoc()) {
	fwrite($fp, html_entity_decode(
		$sPerm['nom'].';'.$sPerm['prenom'].';'.$sPerm['nom_cours'].';'.$sPerm['form'].';'.$sPerm['cm'].';'.$sPerm['td'].';'.$sPerm['code'],
		ENT_QUOTES)."\n");
}
fclose($fp);

echo '<a href="bkp/statsPerm'.$ANNEE.'-'.$ANNEENEXT.'.csv">www-info.iutv.univ-paris13.fr/pain/bkp/statsPerm'.$ANNEE.'-'.$ANNEENEXT.'.csv</a></p>';

echo '<p></p>';
echo '<h3> Sommes par domaine</h3>';

$reqListe="SELECT sum(pain_tranche.cm) as cm,sum(pain_tranche.td) as td,
	ifnull(pain_cours.code_geisha,'Autre') as Domaine
	FROM pain_sformation, pain_formation, pain_enseignant, pain_cours, pain_tranche
	WHERE pain_sformation.annee_universitaire = $ANNEE 
	   AND pain_sformation.nom != 'PRP/référentiel'
	   AND pain_sformation.id_sformation =pain_formation.id_sformation
	   AND pain_formation.id_formation  =pain_cours.id_formation
	   AND pain_tranche.id_cours = pain_cours.id_cours
	   AND nom_cours != '(Responsable)'
	   AND nom_cours != '(Responsabilité Semestre)'
	   AND pain_cours.id_enseignant <> 1
	   AND ( pain_tranche.cm>0 OR pain_tranche.td>0 )
	   AND pain_enseignant.id_enseignant=pain_tranche.id_enseignant";

$reqListePerm=$reqListe." AND pain_enseignant.categorie=2";

$reqListe=$reqListe." GROUP BY pain_cours.code_geisha";
$reqListePerm=$reqListePerm." GROUP BY pain_cours.code_geisha";

$liste = $link->query($reqListe)
	                    or die('Échec de la requête'.$reqListe."\n".$link->error);

echo '<p align="center"><table><thead align="center"><tr>
	<th>Total CM</th><th>Total TD</th><th>Domaine</th></tr></thead>';
echo '<tbody>';
while ($s = $liste->fetch_assoc()) {
	echo '<tr><td>'.$s['cm'].'</td><td>'.$s['td'].'</td><td>'.$s['Domaine'].'</td></tr>';
}
echo '</tbody></table>';

echo '<h3> Sommes par domaine pour les permanents uniquement</h3>';
$listePerm = $link->query($reqListePerm)
	                    or die('Échec de la requête'.$reqListePerm."\n".$link->error);

echo '<p align="center"><table><thead align="center"><tr>
	<th>Total CM</th><th>Total TD</th><th>Domaine</th></tr></thead>';
echo '<tbody>';
while ($s = $listePerm->fetch_assoc()) {
	echo '<tr><td>'.$s['cm'].'</td><td>'.$s['td'].'</td><td>'.$s['Domaine'].'</td></tr>';
}
echo '</tbody></table>';
echo '</center>';

    piedpage();
?>
