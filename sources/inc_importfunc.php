<?php /* -*- coding: utf-8 -*- */
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
require_once('authentication.php'); 
$user = authentication(); 
$annee = get_and_set_annee_menu();

/** 
un select de formulaire pour choisir les sformations de l'année
*/
function formselectsformation($id_sformation)
{
    global $link;
    global $annee;
    $q = "SELECT id_sformation, 
                 nom 
          FROM pain_sformation          
          WHERE annee_universitaire = $annee
          ORDER BY numero ASC";
    $r = $link->query($q) 
	or die("</select></form>Échec de la requête sur la table sformation: ".$link->error);
    if (NULL == $id_sformation) {
	echo '<option disabled="disabled">Définie dans le fichier</option>';
    };
    while ($form = $r->fetch_array()) {
	echo '<option ';
	if ($form["id_sformation"] == $id_sformation) {
	    echo 'selected="selected" ';
	}
	echo  'value="'.$form["id_sformation"].'">';
	echo $form["nom"];
	echo '</option>';
    }
}


/** 
formulaire de sélection de formations et filtres.
*/
function import_php_form() {
    global $annee;
    $sformation = getlistnumeric("sformations");
    $formations = getlistnumeric("formations");
    $semestres = getlistnumeric("semestres");
    $collections = getlistnumeric("collections");
    $categories = getlistnumeric("categories");
    $listemails = getlistnumeric("listemails");
    $toutesformations = getlistnumeric("toutesformations");
    $toutescategories = getlistnumeric("toutescategories");
    $toutescollections = getlistnumeric("toutescollections");
    $toussemestres = getlistnumeric("toussemestres");
    echo '<div id="formannuairevalues" class="hiddenvalue">';
    if (NULL != $sformation) echo "<span class=\"sformations\">$sformation</span>";
    if (NULL != $formations) echo "<span class=\"formations\">$formations</span>";
    if (NULL != $semestres) echo "<span class=\"semestres\">$semestres</span>";
    if (NULL != $collections) echo "<span class=\"collections\">$collections</span>";
    if (NULL != $categories) echo "<span class=\"categories\">$categories</span>";
    if (NULL != $listemails) echo "<span class=\"listemails\">$listemails</span>";
    if (NULL != $toutesformations) echo "<span class=\"toutesformations\">$toutesformations</span>";
    if (NULL != $toutescategories) echo "<span class=\"toutescategories\">$toutescategories</span>";
    if (NULL != $toutescollections) echo "<span class=\"toutescollections\">$toutescollections</span>";
    if (NULL != $toussemestres) echo "<span class=\"toussemestres\">$toussemestres</span>";
    echo '</div>';

    echo <<<EOD
    <fieldset>
    <legend>Formation</legend>
    <label>Cycle</label>
    <select id="sformations" name="sformations" style="width:150px;">
EOD;

    formselectsformation($sformation);

    echo <<<EOD
    </select><br />
    <label>Formations</label>
    <input type="checkbox" id="cbtoutesformations" name="toutesformations" checked="checked" disabled="disabled" value="1" />
    <label for="cbtoutesformations">toutes</label>
    <div id="choix_formations"></div>
    <br />
    </fieldset>
EOD;
//return array($id_formation, $semestre);
}

/**
affiche l'annuaire des cours en fonction des paramètres GET.
*/
function import_php() {
    global $link;

    $sformations = getlistnumeric("sformations");
    if (NULL == $sformations) return;
    if (NULL == getlistnumeric("toutesannees")) {
	$formations = getlistnumeric("formations");
    } else $formations = NULL;
    if (NULL == getlistnumeric("toussemestres")) {
	$semestres = getlistnumeric("semestres");
    } else $semestres = NULL;
    /* selection des cours à afficher */
    $q = "SELECT id_cours, 
                 nom_cours,
                 credits,
                 semestre,
                 pain_formation.nom as nom_formation,
                 pain_formation.annee_etude as annee_etude,
                 pain_formation.parfum as parfum,
                 pain_cours.id_enseignant as id_enseignant,
                 pain_enseignant.prenom AS prenom, 
                 pain_enseignant.nom AS nom,
                 pain_enseignant.email AS email,
                 pain_enseignant.telephone AS tel,
                 pain_enseignant.bureau AS bureau
          FROM pain_cours, pain_enseignant, pain_formation
          WHERE pain_cours.id_formation = pain_formation.id_formation 
          AND pain_formation.id_sformation IN ($sformations)
          AND pain_enseignant.id_enseignant = pain_cours.id_enseignant ";
    /* filtre par annee de formation */
    if ($formations != NULL) {
	$q.=" AND pain_cours.id_formation IN ($formations) ";
    }
    /* filtre par semestre */
    if ($semestres != NULL) $q .=" AND semestre IN ($semestres) ";
    /* filtre par collections */
    if ($collections != NULL) {
	$q.=" AND id_cours IN (SELECT distinct id_cours from pain_collectionscours 
                               WHERE id_collection IN ($collections)) ";
    }
    $q .=" ORDER BY pain_formation.numero ASC, semestre ASC, nom_cours ASC";
    ($r = $link->query($q)) 
	or die("Échec de la requête $q<br>".$link->error);
    $allids = Array();
    while ($cours = $r->fetch_array()) {
	ig_entete_du_cours($cours);
	$ids = ig_responsable_du_cours($cours);
	$idsinterv = ig_intervenants_du_cours($cours, $categories);
	if ($idsinterv != "") $ids = $ids.",".$idsinterv;
	if ($lmails) ig_emails($ids, $categories);
	ig_pied_du_cours($cours);
	$allids[] = $ids;
    }
}
?>
