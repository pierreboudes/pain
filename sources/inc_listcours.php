<?php /* -*- coding: utf-8 -*-*/
require_once("inc_connect.php");
require_once("inc_functions.php");


/* Les totaux globalement */
echo '<table class="formations">';
echo '<tr class="entete" id="entete"><td>';
$tot = htdtotaux("2009");
echo "Ensemble des formations de l'année 2009. ";
echo ' <span class="totaux">';
ig_htd($tot);
echo "</span>\n";
echo '</td></tr></table>';

/* Le grand tableau des formations */

$rformation = list_formations();

while($formation = mysql_fetch_array($rformation)) /* pour chaque formation */
{
    $id_formation = $formation["id_formation"];
    $totaux=htdformation($id_formation);
    
    echo '<table class="formations" id="tableformation'.$id_formation.'">';
    /* affichage de la formation */
    echo '<tr class="imgformation">';
    echo '<td colspan="11" class="imgformation">';
    echo '<div class="imgformation" id="imgformation'.$id_formation.'">';
    echo '</div></td></tr>';
    echo '<tr class="formation" id="formation'.$id_formation.'">';
    echo '<td class="intitule" colspan="11">';
    action_basculerformation($id_formation);
    echo $formation["nom"]." ".$formation["annee_etude"]." ";
    echo $formation["parfum"]." ";

    /* affichage du responsable de la formation */
    echo "responsable : ";
    ig_responsable($formation["id_enseignant"]);
    echo ' <span class="totaux">';
    ig_htd($totaux);
    echo '</span>';
    echo '</td>';

    echo "</tr>\n";

    /* affichage des cours de la formation */

    /* légende */
    echo '<tr class="legende"><th class="nom_cours">intitulé</th><th class="semestre">semestre</th><th class="credits">crédits</th><th class="responsable">responsable</th><th class="CM">CM</th><th class="TD">TD</th><th class="TP">TP</th><th class="alt">alt.</th><th class="descriptif">remarque</th><th class="code_geisha">code</th><th class="action">';
    action_nouveaucours($id_formation);
    echo '</th></tr>'."\n";

    /* formulaire d'ajout d'un cours dans la formation */
    echo '<tr class="formcours" id="formcours'.$id_formation.'"><td colspan="11">'."\n";
    echo '<form method="post" id="fformation'.$id_formation.
         '" class="formcours" name="cours" action="">';
    ig_formcours($id_formation);
    echo '</form>'."\n";
    echo '</td></tr>'."\n";

   
    $rcours = list_cours($id_formation);

    while ($cours = mysql_fetch_array($rcours)) /* pour chaque cours */
    {
	echo '<tr class="imgcours">';
	echo '<td colspan="11" class="imgcours">';
	echo '<div class="imgcours" id="imgcours'.$cours["id_cours"].'">';
	echo '</div></td></tr>'."\n";
	echo '<tr class="cours">';
	ig_cours($cours);
	echo '</tr>'."\n";
    }
echo '</table>'."\n";

} /* fin while formation */

?>

<p>
<a href="http://validator.w3.org/check?uri=referer"><img
    src="http://www.w3.org/Icons/valid-xhtml10-blue"
    alt="Valid XHTML 1.0 Transitional" height="31" width="88" /></a>
    </p>