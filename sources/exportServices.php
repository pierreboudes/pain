<?php
/*
Chaque "activité" serait composée des champs suivants:

*** login-iut 
Que tu dois avoir, comme pain est interfacé avec le CAS de l'IUT.

*** type
Enseignement/Referentiel/PRP

*** description
(uniquement pour referentiel et prp)

*** forme 
(uniquement pour les Enseignements)
Amphi/Cours-TD/TD-TP/Contrôle

*** nbSeances
(uniquement pour les Enseignements)
le nombre de séances

*** heuresParSeance
(uniquement pour les Enseignements)

*** heuresEquivTd
Pour les enseignements, ce champs peut se déduire de forme + nbSeances + heuresParSeance
Mais ce n'est pas le cas pour les Referentiel/PRP.
Specifier dans tous les cas.

*** formsemestreId
(uniquement pour les Enseignements)
C'est la ou ca se complique...
C'est l'identifiant fromsemestre ("Session") dans ScodDoc.
En 2013-2014, ca ressemblait a ca: SEM37290, SEM39008, ...
Pour 2014-2015, Jean-Christophe doit créer les nouvelles formations sur ScoDoc.
Si j'ai bien compris ils les créera avec les mêmes noms que sur GPU.
https://edt.iutv.univ-paris13.fr/data/edt/matieres.bd
Donc ca devrait ressembler à ca: "INFO-DUT-FI-S1-2014"
Si tu n'as pas accès à cette url sur SOJA, je t'ai copié les données ci-dessous:
colonne 1: formsemestreId, colonne2: module

*** module
(uniquement pour les Enseignements)
Exemple : M1105
Mêmes remarques que pour formsemestreId.
*/

/** Error reporting */
error_reporting(E_ALL);

require_once('authentication.php');
$user = authentication();

require_once('inc_connect.php');
require_once("inc_headers.php"); /* pour en-tete et pied de page */
entete("export BOSC CSV","pain.js");
require_once('utils.php');
//include("menu.php");
require_once('inc_functions.php');

$tabConversionSemestreId= array(
	'INFO-DUT-FI-S1-2014' => 'SEM43396',
	'INFO-DUT-FA-S2-2014' => 'SEM43929',
	'INFO-DUT-FA-S4-2014' => 'SEM43962',
	'INFO-DUT-FI-S4-2014' => 'SEM43437',
	'INFO-LP-FI-A1-2014'  => 'SEM44364',
);

function fromSemestreId($tab) {
	global $annee;
	$dept='INFO';
	/*if ($tab['parfum']=='FC') {
		$dept='FCINFO';
		$tab['form']='A1'; * pourquoi A1 ? *
	}*/
/*	if ($tab['sform']=='LP-FI' || $tab['sform']=='LP-FA') {
		$tab['form']='A1'; * pourquoi A1 ? *
	}*/
	return $dept.'-'.$tab['sform'].'-'.$tab['form'].'-'.$annee;
}

function nomModule($chaine,$sform) {
	$resu=substr($chaine,0,strcspn($chaine,' -: '));
	if ($sform=='LP-FA') {
		//$resu= 'LPA-'.$resu;
	} else if ($sform == 'LP-FI') {
		//$resu= 'LPFI-'.$resu;
	} else if ($sform == 'FA') {
		$resu = 'A-'.$resu;
	}
	return $resu;
}

function ecritLigne ($fp, $tab, $ref, $coef) {
	$chaine= $tab['login']
                .';'.'Enseignement'
                .';'; /* Pas de descriptif si enseignement ! */
	if (strcmp($ref,"Controle")==0)
		$chaine = $chaine .';' .'TD-TP';
	else
		$chaine = $chaine .';'.$ref;
	$chaine = $chaine 
                .';1' /* nbre de séances, désolé toujours 1 */
                .';'.$tab[$ref].';'.($tab[$ref]*$coef)
                .';'.fromSemestreId($tab)
                .';'.nomModule($tab['nom_cours'],$tab['sform']);
                
	
	echo "<p>$chaine</p>";

	fwrite($fp, html_entity_decode($chaine,ENT_QUOTES)."\n");
}

function ecritLigneResp ($fp, $tab) {
	$chaine= $tab['login'] .';';
	if ($tab['parfum']=='PRP') {
		$chaine = $chaine . 'PRP;' . $tab['form'];
	} else {
		$chaine = $chaine . 'Referentiel;' . $tab['form'];
	}

	$chaine = $chaine . ';;;'  /* pas  de forme, pas de nbre de séances, pas de duree seance  */
		. ';'.$tab['HTD']
		. ';;'; /* pas de fromSemestreId ni de nom de module */

	echo "<p>$chaine</p>";

	fwrite($fp, html_entity_decode($chaine, ENT_QUOTES)."\n");
}


/**
Génére le code HTML de la page export BOSC
*/

$chemin='bkp/exportServices';

$annee = default_year();


$ANNEENEXT=$annee+1;

//categorie=2 pour les permanents par défaut, espèrons que cela ne changera pas !
// id_enseignant=1 : cours annulé.

$req="CREATE TEMPORARY TABLE tmp
        SELECT pain_enseignant.nom,prenom, pain_enseignant.login,
          pain_sformation.nom as 'sform', 
          pain_formation.nom as 'form', 
          pain_formation.parfum,
          nom_cours,
          pain_tranche.cm as 'Amphi',
          IFNULL(pain_tranche.td,0) + IFNULL(pain_tranche.tp,0) as 'TD-TP',
          pain_tranche.ctd as 'Cours-TD',
          pain_tranche.alt as 'Controle',
          pain_tranche.htd as 'HTD',
          semestre,pain_cours.code_geisha as 'code'
        FROM pain_sformation,pain_formation, pain_enseignant, pain_cours, pain_tranche
        WHERE pain_sformation.annee_universitaire = $annee
        AND pain_sformation.id_sformation=pain_formation.id_sformation
        AND pain_formation.id_formation=pain_cours.id_formation
        AND pain_tranche.id_cours = pain_cours.id_cours
        AND pain_tranche.id_enseignant > 9
        AND pain_formation.nom NOT LIKE 'Test*'
        AND pain_enseignant.id_enseignant=pain_tranche.id_enseignant
        ORDER BY nom,prenom,sform,form,nom_cours";

if (! $link->query($req))
	  die('Echec creation table temporaire:'.$req.'\n'.$link->error);

$reqListe = "SELECT * from tmp";
$liste = $link->query($reqListe)
	            or die('Échec de la requête'.$reqListe."\n".$link->error);

$fp = fopen("$chemin$annee-$ANNEENEXT.csv",'w');

/* Attention suppose pour l'instant qu'il n'y a pas de ";" dans
 les noms des cours et formations ! */
fwrite($fp,"login-iut;type;description;forme;nbSeances;heuresParSeance;heuresEquivTd;formSemestreId;Module\n");
while ($resu = $liste->fetch_assoc()) {
	if ($resu['Amphi'] >0) 
		ecritLigne($fp,$resu,'Amphi', 1.5);
	if ($resu['Cours-TD']>0) 
		ecritLigne($fp,$resu,'Cours-TD', '1.125');
	if ($resu['TD-TP']>0 )
		ecritLigne($fp,$resu,'TD-TP', 1);
	if ($resu['Controle']>0)
		ecritLigne($fp,$resu,'Controle',1);

	if ($resu['sform']=='PRP/Référentiel')
		ecritLigneResp($fp,$resu);
}
fclose($fp);

echo '<center>';
echo '<h2>export Services</h2>';
echo '<p>Le fichier se trouve ici : <a href="'.$chemin.$annee.'-'.$ANNEENEXT.'.csv">www-info.iutv.univ-paris13.fr/pain/'.$chemin.$annee.'-'.$ANNEENEXT.'.csv</a></p>';

    piedpage();
?>
