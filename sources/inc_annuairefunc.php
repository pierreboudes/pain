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
authrequired();
require_once("inc_functions.php");

function ig_formselectformation($id_formation, $annee = NULL)
{
    global $link;
    global $annee;
    if ($annee == NULL) $annee = annee_courante();
    $q = "SELECT id_formation, 
                 pain_formation.nom AS nom, 
                 pain_formation.parfum AS parfum, 
                 pain_formation.annee_etude AS annee_etude 
          FROM pain_formation, pain_sformation          
          WHERE pain_formation.id_sformation = pain_sformation.id_sformation
          AND pain_sformation.annee_universitaire = $annee
          ORDER BY pain_formation.numero ASC";
    $r = $link->query($q) 
	or die("</select></form>Échec de la requête sur la table formation: ".$link->error);
    while ($form = $r->fetch_array()) {
	echo '<option ';
	if ($form["id_formation"] == $id_formation) {
	    echo 'selected="selected" ';
	}
	echo  'value="'.$form["id_formation"].'">';
	echo $form["nom"]." ";
	if ($form["parfum"] != "") echo $form["parfum"]." ";
	if ($form["annee_etude"] != 0) echo $form["annee_etude"];
	echo '</option>';
    }
}

function ig_entete_du_cours($cours) {
    echo '<table class="annuaire">';
    echo '<tr><th colspan="5">';
    echo $cours["nom_cours"].", S".$cours["semestre"].", ".$cours["credits"]." ects";
    echo '</th></tr>';
    /* legende */
    echo '<tr><th>role</th>';
    echo '<th>nom</th>';
    echo '<th>email</th>';
    echo '<th>tel</th>';
    echo '<th>bureau</th></tr>';
}
function ig_responsable_du_cours($cours) {
    echo '<tr><td>responsable</td>';
    echo '<td>'.$cours["prenom"].' '.$cours["nom"].'</td>';
    echo '<td>'.$cours["email"].'</td>';
    echo '<td>'.$cours["tel"].'</td>';
    echo '<td>'.$cours["bureau"].'</td></tr>';
}

function ig_intervenants_du_cours($cours) {
    global $link;
    $id_cours = $cours["id_cours"];
    $q = "SELECT 
                 GROUP_CONCAT(DISTINCT pain_tranche.groupe
                              ORDER BY pain_tranche.groupe
                              SEPARATOR ', G') as groupes,
                 SUM(cm) AS cm,
                 SUM(td) AS td,
                 SUM(tp) AS tp,
                 SUM(alt) AS alt,
                 pain_enseignant.id_enseignant as id_enseignant,
                 pain_enseignant.prenom AS prenom, 
                 pain_enseignant.nom AS nom,
                 pain_enseignant.email AS email,
                 pain_enseignant.telephone AS tel,
                 pain_enseignant.bureau AS bureau
          FROM pain_tranche, pain_enseignant
          WHERE pain_tranche.id_cours = $id_cours 
          AND pain_enseignant.id_enseignant = pain_tranche.id_enseignant
          GROUP BY pain_enseignant.id_enseignant
          ORDER BY groupe ASC, nom ASC";
    ($r = $link->query($q)) 
        or die("Échec de la connexion à la base $q<br>".$link->error);
    while ($e = $r->fetch_array()) {
	if (strcmp($e["groupes"], "0") != 0) {
	    $groupes = str_replace("0, G", "", $e["groupes"]);
	    $groupes = "G".$groupes;	    
	} else {
	    $groupes = NULL;
	}
	if ($e['cm'] + $e['td'] + $e['tp'] == 0) {
	    $role = 'autre';
	}
	if ($e['cm'] > 0) {
	    $role = 'cours';
	    if  ($e['td'] + $e['tp'] > 0) {
		$role .= " et TD";
		if ($groupes != NULL) $role .= " ".$groupes;
	    }
	}
	if (($e['cm'] == 0) && ($e['td'] + $e['tp'] > 0)) {
	    $role = "TD";
	    if ($groupes != NULL) $role .= " ".$groupes;
	}
	echo '<tr><td class="enseignant">'.$role.'</td>';
	echo '<td class="enseignant">'.$e["prenom"].' '.$e["nom"].'</td>';
	echo '<td class="email">'.$e["email"].'</td>';
	echo '<td class="tel">'.$e["tel"].'</td>';
	echo '<td class="bureau">'.$e["bureau"].'</td></tr>';	
    }
}


function ig_pied_du_cours($cours) {
    echo '</table>';
}


function liste_emails($type, $annee = NULL) {
    global $link;
    if ($annee == NULL) $annee = annee_courante();

    if ($type == "rsformation") {
	$q = "SELECT pain_enseignant.email
              FROM pain_sformation, pain_enseignant
              WHERE pain_sformation.annee_universitaire = $annee,
              AND pain_sformation.id_enseignant = pain_enseigant.id_enseignant
              AND pain_enseignant.email <> NULL";
    }
/* Pour plus tard: possibilite de selectionner une liste d'emails.
/* SELECT GROUP_CONCAT(DISTINCT email ORDER BY email ASC SEPARATOR ', ') FROM pain_sformation, pain_formation, pain_enseignant WHERE pain_sformation.annee_universitaire = "2009" AND pain_formation.id_sformation = pain_sformation.id_sformation AND pain_enseignant.id_enseignant = pain_formation.id_enseignant */
/* 
SELECT GROUP_CONCAT( DISTINCT email
ORDER BY pain_enseignant.nom, pain_enseignant.prenom ASC
SEPARATOR  ', ' )
FROM pain_sformation, pain_formation, pain_enseignant
WHERE pain_sformation.annee_universitaire =  "2009"
AND 
((pain_formation.id_sformation = pain_sformation.id_sformation
AND pain_enseignant.id_enseignant = pain_formation.id_enseignant)
OR
pain_enseignant.id_enseignant = pain_sformation.id_enseignant
)
*/
}


/** 
formulaire de sélection d'une formation de l'année et d'un semestre.
*/
function annuaire_php_form() {
    global $annee;
$id_formation = 0;
if (isset($_POST['id_formation'])) {
    $id_formation = postclean('id_formation');
}
$semestre = 0;
if (isset($_POST['semestre'])) {
    $semestre = postclean('semestre');
}

echo '<center><div class="infobox" style="width:290px;">';
echo '<form method="post" id="choixformation" class="formcours" name="enseignant" action="#">';
echo '<select name="id_formation" style="display:inline; width:150px;">';
ig_formselectformation($id_formation, $annee);
echo '</select>';
echo '<select name="semestre" style="display: inline; width: 100px;">';
echo '<option value="0" ';
if ($semestre == 0) echo 'selected="selected"';
echo '>semestre</option>';
for ($i = 1; $i <= 2; $i++) {
	echo '<option ';
	if ($i == $semestre) {
	    echo 'selected="selected" ';
	}
	echo  'value="'.$i.'">';
	echo 'S'.$i;
	echo '</option>';
}
echo '</select>';
echo '<input type="submit" value="OK" style="display:inline;width:40px;"/>';
echo '</form>'."\n";
echo '</div></center>';
return array($id_formation, $semestre);
}


/**
affiche l'annuaire de la formation $id_formation, éventuellement restreint au semestre $semestre.
*/
function annuaire_php($id_formation, $semestre = 0) {
    global $link;
    /* annuaire */
    echo "<h2>Annuaire de la formation</h2>";

    $q = "SELECT id_cours, 
                 nom_cours,
                 credits,
                 semestre,
                 pain_cours.id_enseignant as id_enseignant,
                 pain_enseignant.prenom AS prenom, 
                 pain_enseignant.nom AS nom,
                 pain_enseignant.email AS email,
                 pain_enseignant.telephone AS tel,
                 pain_enseignant.bureau AS bureau
          FROM pain_cours, pain_enseignant
          WHERE pain_cours.id_formation = $id_formation "; 
    if ($semestre) $q .=" AND semestre = $semestre ";
    $q .=" AND pain_enseignant.id_enseignant = pain_cours.id_enseignant ";
    $q .=" ORDER BY semestre ASC, nom_cours ASC";
    ($r = $link->query($q)) 
	or die("Échec de la connexion à la base $q<br>".$link->error);
    while ($cours = $r->fetch_array()) {
	ig_entete_du_cours($cours);
	ig_responsable_du_cours($cours);
	ig_intervenants_du_cours($cours);
	ig_pied_du_cours($cours);
    }
}
?>