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
// $user = no_auth(); /* pas d'authentification */
$user = weak_auth(); /* accès sans autorisation */
$annee = get_and_set_annee_menu();

/** 
un select de formulaire pour choisir les sformations de l'année
*/
function ig_formselectsformation($id_sformation)
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
	echo '<option disabled="disabled">Choisir un cycle</option>';
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
affiche la première ligne du tableau annuaire d'un cours
 */
function ig_entete_du_cours($cours) {
    echo '<table class="annuaire">';
    echo '<tr><th colspan="5">';
    echo $cours["nom_cours"];
    echo "<div class=\"formation\">".$cours["nom_formation"]." ".$cours["annee_etude"].($cours["parfum"]?" ".$cours["parfum"]:"")
	." (semestre ".$cours["semestre"].($cours["credits"]?", ".$cours["credits"]." ects":"").")</div>";
    echo '</th></tr>';
    /* legende */
    echo '<tr><th>role</th>';
    echo '<th>nom</th>';
    echo '<th>email</th>';
    echo '<th>tel</th>';
    echo '<th>bureau</th></tr>';
}

/**
affiche une ligne de tableau sur le responsable du cours et retourne son id 
*/
function ig_responsable_du_cours($cours) {
    echo '<tr><td>responsable</td>';
    echo '<td>'.$cours["prenom"].' '.$cours["nom"].'</td>';
    echo '<td>'.$cours["email"].'</td>';
    echo '<td>'.$cours["tel"].'</td>';
    echo '<td>'.$cours["bureau"].'</td></tr>';
    return $cours["id_enseignant"];
}

/**
affiche la liste des intervenants du cours sous forme de lignes de tableau et retourne
la liste csv des ids de ces intervenants.
*/
function ig_intervenants_du_cours($cours, $categories = NULL) {
    global $link;
    global $annee;
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
          AND pain_enseignant.id_enseignant = pain_tranche.id_enseignant ";
    if ($categories != NULL) {
	$q .= " AND pain_enseignant.id_enseignant IN (SELECT distinct id_enseignant from pain_service 
                               WHERE annee_universitaire = $annee
                               AND categorie IN ($categories)) ";
    }

    $q .= " GROUP BY pain_enseignant.id_enseignant ORDER BY groupe ASC, nom ASC";
    ($r = $link->query($q)) 
        or die("Échec de la requête $q<br>".$link->error);
    $ids = Array();
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
	if ($e["email"]=="" && $e['prenom']!='libre') {
		echo '<td class="email">'.$e['prenom'].'.'.$e['nom'].'@iutv.univ-paris13.fr ?';
	} else {
		echo '<td class="email">'.$e["email"].'</td>';
	}
	echo '<td class="tel">'.$e["tel"].'</td>';
	echo '<td class="bureau">'.$e["bureau"].'</td></tr>';
	$ids[] = $e["id_enseignant"];
    }
    return join(",",$ids);
}

function ig_emails($ids, $categories) {
    global $link;
    global $annee; /* pour filtre categories */

    $a = Array(); /* liste des emails */
    
    if ($ids != "") {/* il y a des ids dont on veut les emails */
	/* nota: lorsqu'on filtrera par categories il faudra faire la jointure avec pain_service */
	$q = "SELECT distinct email, prenom, nom
              FROM pain_enseignant WHERE id_enseignant IN ($ids) ORDER BY email ASC"; 
        /* rem: GROUP_CONCAT(DISTINCT email ORDER BY email ASC SEPARATOR ', ') limité à 1024 */
	($r = $link->query($q)) 
	    or die("Échec de la requête $q<br>".$link->error);
	while ($e = $r->fetch_array()) {
	    if ($e["email"] != "") {/* tester ici si email valide (regexp) */
		$a[] = $e["email"];
	    } else if ($e["prenom"] != "libre") {
		$a[] = $e["prenom"].'.'.$e["nom"]."@iutv.univ-paris13.fr";
	    }
	}
    }
    $rows = count($a)/3 + 1;    
    //echo '<tr><td colspan="5" style="text-align: center;"><a href="mailto:?bcc='.join(', ', $a)
    echo '<tr><td colspan="5" style="text-align: center;"><a href="mailto:'.join(', ', $a)
	    .'">Mail collectif à ces enseignants</a></td></tr>';
    /*echo '<tr><td colspan="5" style="width: 800px"><textarea dir="ltr" rows="'.$rows.'" cols="40">'.join(', ', $a).
	     '</textarea></td></tr>';*/
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
formulaire de sélection de formations et filtres.
*/
function annuaire_php_form() {
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
<center><div class="infobox" id="formannuaire">
    <form method="GET" class="formcours" name="enseignant" action="#">
    <fieldset>
    <legend>Formation</legend>
    <label>Cycle</label>
    <select id="sformations" name="sformations" style="width:150px;">
EOD;
    ig_formselectsformation($sformation);

    echo <<<EOD
    </select><br />
    <label>Formations</label>
    <input type="checkbox" id="cbtoutesformations" name="toutesformations" checked="checked" disabled="disabled" value="1" />
    <label for="cbtoutesformations">toutes</label>
    <div id="choix_formations"></div>
    <br />
    </fieldset>
    <fieldset>
    <legend>Filtres</legend>
    <label>Parcours</label>
    <input type="checkbox" id="cbtoutescollections" name="toutescollections" checked="checked" disabled="disabled" value="1" />
    <label for="cbtoutescollections">aucun filtre (tous les cours)</label>
    <div id="choix_collections"></div>
     <br />
    <label>Semestres</label>
    <input type="checkbox" id="cbtoussemestres" name="toussemestres" checked="checked" disabled="disabled" value="1" />
    <label for="cbtoussemestes">aucun filtre (tous les cours)</label>
    <div id="choix_semestres"></div>
     <br />
    <label>Catégories d&rsquo;intervenants</label>
    <input type="checkbox" id="cbtoutescategories" name="toutescategories" checked="checked" disabled="disabled" value="1" />
    <label for="cbtoutescategories">aucun filtre</label>
    <div id="choix_categories"></div>
     <br />
    </fieldset>
    <fieldset>
    <legend>Afficher</legend>
    <label for="listemails">Lister les adresses mail ?</label>
EOD;
    if ($listemails != NULL && $listemails == 0) {
	echo '<input type="radio" name="listemails" value="1" /> oui';
	echo '<input type="radio" name="listemails" checked="checked" value="0" /> non';
    } else {
	echo '<input type="radio" name="listemails" checked="checked" value="1" /> oui';
	echo '<input type="radio" name="listemails" value="0" /> non';
    }
    echo <<<EOD
    <input type="submit" value="OK" style="width:40px;"/>
    </form>
    </fieldset>
     </div></center>
EOD;
//return array($id_formation, $semestre);
}

/**

 */



/**
affiche l'annuaire des cours en fonction des paramètres GET.
*/
function annuaire_php() {
    global $link;

    $sformations = getlistnumeric("sformations");
    if (NULL == $sformations) return;
    if (NULL == getlistnumeric("toutesannees")) {
	$formations = getlistnumeric("formations");
    } else $formations = NULL;
    if (NULL == getlistnumeric("toussemestres")) {
	$semestres = getlistnumeric("semestres");
    } else $semestres = NULL;
    if (NULL == getlistnumeric("toutescollections")) {
	$collections = getlistnumeric("collections");
    } else $collections = NULL;
    if (NULL == getlistnumeric("toutescategories")) {
	$categories = getlistnumeric("categories");
    } else $categories = NULL;
    $listemails = getlistnumeric("listemails");
    $lmails = true;
    if ($listemails != NULL && $listemails == 0) {
	$lmails = false;
    }
    /* annuaire */
    echo "<h2>Les intervenants dans les cours sélectionnés</h2>";
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
    if ($lmails) {
	echo '<table class="annuaire"><tr><th colspan="5">Tous les emails</th></tr>';
	ig_emails(join(',',$allids), $categories);
	echo '</table>';
    }
}
?>
