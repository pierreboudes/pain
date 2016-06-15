<?php

$annee=2014;
$anneeSuite=$annee+1;
$fic=file_get_contents('./formsemestre_list.json',FILE_USE_INCLUDE_PATH) ;
if ($fic===FALSE) 
	echo "erreur ouverture fichier";

$tab=json_decode($fic,TRUE);

$i=0;
while ($tab[$i]) {
	//echo $i;
	$i++;
	$t=$tab[$i];
	if ($t["anneescolaire"]=="$annee - $anneeSuite") {
		echo $t["titre_num"].'-'.$t["modalite"]."\n";
	}
}
?>
