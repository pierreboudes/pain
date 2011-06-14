<?php /* -*- coding: utf-8 -*- */
/* Pain - outil de gestion des services d'enseignement        
 *
 * Copyright 2009 Pierre Boudes, département d'informatique de l'institut Galilée.
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
$debug_show_id = false; /* afficher les id */
function debug_show_id($s) {
    global $debug_show_id;
    if ($debug_show_id) {
	echo '<div class="debug_id">'.$s.'</div>';
    }
}

require_once('authentication.php'); 
authrequired();
require_once("utils.php");
require_once("inc_actions.php");
require_once("inc_droits.php");

function ig_typeconversion($type)
{
    switch ($type) {
    case 0:
	echo "auto";
	break;
    case 1:
	echo "manuel";
	break;
    default:
	echo "inconnu"; /* jamais atteint */
    }
}

function ig_responsable($id)
{
    if ($id < 0) {
	echo "Libre"; /* jamais atteint */
    }
    if ($id == 0) {
	echo "Autre"; /* jamais atteint */
    }
    if ($id > 0) {
	$qresponsable = "SELECT * FROM pain_enseignant WHERE `id_enseignant` = $id";
	$rresponsable = mysql_query($qresponsable) 
	    or die("Échec de la requête sur la table enseignant");
	$responsable = mysql_fetch_array($rresponsable);
	debug_show_id($id);    
	echo '<a class="enseignant" href="service.php?id_enseignant='.$id.'">';
	echo $responsable["prenom"]." ";
	echo $responsable["nom"];
	echo '</a>';
    }
}

function ig_formselectenseignants($id_enseignant) /* obsolete, modifier inc_service */
{
/*    global $annee;
    if ($annee == NULL) $annee = annee_courante();
*/
    $qens = "SELECT `id_enseignant`, `prenom`, `nom` 
             FROM pain_enseignant WHERE 1 ORDER BY `nom`, `prenom` ASC";
    $rens = mysql_query($qens) 
	  or die("Échec de la requête sur la table enseignant");
    while ($ens = mysql_fetch_array($rens)) {
	echo '<option ';
	if ($ens["id_enseignant"] == $id_enseignant) {
	    echo 'selected="selected" ';
	}
	echo  'value="'.$ens["id_enseignant"].'">';
	echo trim($ens["nom"]." ".$ens["prenom"]);
	echo '</option>';
    }
}

function lister_enseignantsannee($an)
{
    $qens = "SELECT pain_enseignant.id_enseignant AS `id`, ".
                    "TRIM(CONCAT(prenom, ' ',nom)) AS `label` ".
             "FROM pain_enseignant, pain_service ".
             "WHERE pain_service.annee_universitaire = $an ".
	     "AND pain_service.id_enseignant = pain_enseignant.id_enseignant ".
	     "ORDER BY nom, prenom ASC";
    $rens = mysql_query($qens) 
	or die("Échec de la requête sur la table enseignant: $qens mysql a repondu: ".mysql_error());
    return $rens;
}

function lister_categories($an)
{
    $qcat = "SELECT id_categorie AS `id`, ".
                    "TRIM(nom_court) AS `label`, ".
	            "nom_long, descriptif ".
	"FROM pain_categorie ".
	"WHERE descriptif <> \"\" ". /* <- debile, TODO : trouver la bonne structure */
	"ORDER BY id_categorie ASC";
    $rcat = mysql_query($qcat) 
	or die("Échec de la requête sur la table categorie: $qens mysql a repondu: ".mysql_error());
    return $rcat;
}


function list_superformations($annee = NULL)
{
    if ($annee == NULL) $annee = annee_courante();
    $qsformation = "SELECT * FROM pain_sformation 
                   WHERE `annee_universitaire` = ".$annee."
                   ORDER BY numero ASC";

    $rsformation = mysql_query($qsformation) 
	or die("Échec de la requête sur la table formation");

    return $rsformation;
}

function list_formations($id_sformation)
{
    $qformation = "SELECT * FROM pain_formation 
                   WHERE `id_sformation` = ".$id_sformation."
                   ORDER BY numero ASC";    

    $rformation = mysql_query($qformation) 
	or die("Échec de la requête sur la table formation");

    return $rformation;
}

function list_cours($id)
{
      $qcours = "SELECT * FROM pain_cours WHERE `id_formation` = $id
                 ORDER BY semestre, nom_cours ASC";

    $rcours = mysql_query($qcours) or 
	die("Échec de la requête sur la table cours");

    return $rcours;
}


function selectionner_cours($id)
{
    $qcours = "SELECT * FROM pain_cours WHERE `id_cours` = $id";
    $cours = NULL;
    if ($rcours = mysql_query($qcours)) {
	$cours = mysql_fetch_assoc($rcours);
    } else {
	echo "Échec de la requête sur la table cours. $qcours ".mysql_error();
    }
    return $cours;
}
/* supprimer sformation */
function supprimer_sformation($id)
{    
    if (!peutsupprimersformation($id)) {
	errmsg("droits insuffisants.");
    }

    pain_log("-- supprimer_sformation($id)");

    $q = "DELETE pain_choix FROM pain_choix, pain_cours, pain_formation 
          WHERE pain_formation.id_sformation = $id
          AND  pain_cours.id_formation = pain_formation.id_formation
          AND  pain_choix.id_cours = pain_cours.id_cours";
    if (!mysql_query($q)) {
	errmsg("échec de la requête $q : ".mysql_error());
    }
    pain_log($q);

    $q = "DELETE pain_tranche FROM pain_tranche, pain_cours, pain_formation
          WHERE pain_formation.id_sformation = $id
          AND  pain_cours.id_formation = pain_formation.id_formation
          AND  pain_tranche.id_cours = pain_cours.id_cours";
    if (!mysql_query($q)) {
	errmsg("échec de la requête $q : ".mysql_error());
    }
    pain_log($q);

    $q = "DELETE pain_cours FROM pain_cours, pain_formation 
          WHERE pain_formation.id_sformation = $id
          AND  pain_cours.id_formation = pain_formation.id_formation";
    if (!mysql_query($q)) {
	errmsg("échec de la requête $q : ".mysql_error());
    }
    pain_log($q);

    $q = "DELETE pain_formation FROM pain_formation 
          WHERE pain_formation.id_sformation = $id";
    if (!mysql_query($q)) {
	errmsg("échec de la requête $q : ".mysql_error());
    }
    pain_log($q);

    /* sformation */
    $q = "DELETE FROM pain_sformation WHERE `id_sformation` = $id LIMIT 1";
    if (!mysql_query($q)) {
	errmsg("échec de la requête $q : ".mysql_error());
    }
    pain_log($q);

    echo '{"ok": "ok"}';
}

/* supprimer formation */
function supprimer_formation($id)
{    
    if (!peutsupprimerformation($id)) {
	errmsg("droits insuffisants.");
    }

    pain_log("-- supprimer_formation($id)");

    $q = "DELETE pain_choix FROM pain_choix, pain_cours 
          WHERE pain_cours.id_formation = $id
          AND  pain_choix.id_cours = pain_cours.id_cours";
    if (!mysql_query($q)) {
	errmsg("échec de la requête $q : ".mysql_error());
    }
    pain_log($q);

    $q = "DELETE pain_tranche FROM pain_tranche, pain_cours 
          WHERE pain_cours.id_formation = $id
          AND  pain_tranche.id_cours = pain_cours.id_cours";
    if (!mysql_query($q)) {
	errmsg("échec de la requête $q : ".mysql_error());
    }
    pain_log($q);

    $q = "DELETE pain_cours FROM pain_cours 
          WHERE pain_cours.id_formation = $id ";
    if (!mysql_query($q)) {
	errmsg("échec de la requête $q : ".mysql_error());
    }
    pain_log($q);

    /* formation */
    $q = "DELETE FROM pain_formation WHERE `id_formation` = $id LIMIT 1";
    if (!mysql_query($q)) {
	errmsg("échec de la requête $q : ".mysql_error());
    }
    pain_log($q);

    echo '{"ok": "ok"}';
}


/* a conserver */
function supprimer_cours($id)
{    
    if (peuteditercours($id)) {
	$cours = selectionner_cours($id);

	$qcours = "DELETE FROM pain_cours WHERE `id_cours` = $id LIMIT 1";
	pain_log("-- supprimer_cours($id)");

        if (mysql_query($qcours)) {
	    /* on efface les tranches associées */
	    pain_log("$qcours");
	    historique_par_suppression(1, $cours);

	    $qtranches = "DELETE FROM pain_tranche WHERE `id_cours` = $id";
	    
	    if (mysql_query($qtranches)) {
		echo '{"ok": "ok"}';
		pain_log("$qtranches");
	    } else {
		errmsg("échec de la requête sur la table tranches.");
	    }
	} else {
	    errmsg("échec de la requête sur la table cours.");
	}
    } else {
	errmsg("droits insuffisants.");
    }
}


function ig_cours($cours,$tag="")
{
    $id = $cours["id_cours"];
    
    echo '<td class="nom_cours">';
    debug_show_id($id);
    echo $cours["nom_cours"];
    echo '</td>';
    
    echo '<td class="semestre">';
    action_basculercours($id);
    echo $cours["semestre"];
    echo '</td>';
    
    echo '<td class="credits">';
    echo $cours["credits"];
    echo '</td>';
    
    echo '<td class="responsable">';
    ig_responsable($cours["id_enseignant"]); 
    echo '</td>';
    
    echo '<td class="CM">';
    echo $cours["cm"];
    echo '</td>';
    
    echo '<td class="TD">';
    echo $cours["td"];
    echo '</td>';

    echo '<td class="TP">';
    echo $cours["tp"];
    echo '</td>';
    
    echo '<td class="alt">';
    echo $cours["alt"];
    echo '</td>';

    echo '<td class="descriptif">';
    echo $cours["descriptif"];
    echo '</td>';
    
    echo '<td class="code_geisha">';
    echo $cours["code_geisha"];
    echo '</td>';
   
    echo '<td class="action" id="cours'.$tag.$id.'">';
    action_supprimercours($id);
    echo '<br/>';
    action_modifiercours($id);
    echo '</td>';
}

function ig_formcours($id_formation, $id_cours="", $nom_cours="", $semestre=0, $id_enseignant = NULL, $credits = "", $cm = "", $td="", $tp="", $alt="", $descriptif="", $code_geisha = "")
{
    $id = $id_cours;    
    echo '<table class="formcours"><tr>';
    echo '<td class="nom_cours">';
    echo '<input type="text" name="nom_cours" value="'.$nom_cours.'"/></td>';
    echo '<td class="semestre">';
    echo '<input value="1" '; 
    if (1 == $semestre) echo 'checked ';
    echo 'type="radio" class="semestre" name="semestre"/>1<br/>';
    echo '<input value="2" ';
    if (2 == $semestre) echo 'checked ';
    echo 'type="radio" class="semestre" name="semestre"/>2</td>';
    echo '<td class="credits"><input type="text" name="credits" value="'.$credits.'" /></td>';
    echo '<td class="responsable">';
    echo '<select name="responsable_cours" class="autocomplete">';
    ig_formselectenseignants($id_enseignant);
    echo '</select>';
    echo '</td>';
    echo '<td class="CM"><input type="text" name="cm" value="'.$cm.'" /></td>';
    echo '<td class="TD"><input type="text" name="td" value="'.$td.'" /></td>';
    echo '<td class="TP"><input type="text" name="tp"  value="'.$tp.'" /></td>';
    echo '<td class="alt"><input type="text" name="alt" value="'.$alt.'" /></td>';
    echo '<td class="descriptif">';
    echo '<input type="hidden" name="id_formation" value="'.$id_formation.'"/>';
echo '<input type="hidden" name="id_cours" value="'.$id_cours.'"/>';
    echo '<textarea name="descriptif" rows="4" cols="10">'.$descriptif.'</textarea></td>';
    echo '<td class="code_geisha"><input type="text" name="code_geisha" value="'.$code_geisha.'" /></td>';
    if ($id) {/* modification de cours */
	echo '<td class="action" id="formmodifcours'.$id.'">';
        echo '<input type="submit" value="OK"/><br/>';
	action_annulermodifiercours($id);
    } else {
	echo '<td class="action"><input type="submit" value="OK"/><br/>';
	action_annulerajoutercours($id_formation);
    }
    echo '</td></tr></table>'."\n";    
}

function ig_legendecours($id_formation) {
    echo '<tr class="legende"><th class="nom_cours">intitulé</th><th class="semestre">semestre</th><th class="credits">crédits</th><th class="responsable">responsable</th><th class="CM">CM</th><th class="TD">TD</th><th class="TP">TP</th><th class="alt">alt.</th><th class="descriptif">remarque</th><th class="code_geisha">code</th>';
    echo '<th class="action">';
    action_nouveaucours($id_formation);
    echo '</th></tr>'."\n";

}


function ig_legendetranches($id) {
    echo '<th class="groupe">Groupe</th>';
    echo '<th class="enseignant">Enseignant</th>';
    echo '<th class="CM">CM</th>';
    echo '<th class="TD">TD</th>';
    echo '<th class="TP">TP</th>';
    echo '<th class="alt">alt.</th>';
    echo '<th class="HTD">htd</th>';
    echo '<th class="remarque">Remarque</th>';
    echo '<th class="action">';
    echo '</th>';
}

function tranchesdecours($id) {
    $qtranches = "SELECT * FROM pain_tranche WHERE `id_cours`=".$id." ORDER BY groupe ASC";

    $rtranches = mysql_query($qtranches) or 
	die(mysql_error());

    return $rtranches;
}

function selectionner_tranche($id)
{
    $qtranche = "SELECT * FROM pain_tranche WHERE `id_tranche` = $id";
    $tranche = NULL;
    if ($rtranche = mysql_query($qtranche)) {
	$tranche = mysql_fetch_assoc($rtranche);
    } else {
	echo "Échec de la requête sur la table tranche. $qtranche ".mysql_error();
    }
    return $tranche;
}

function selectionner_choix($id)
{
    $qchoix = "SELECT * FROM pain_choix WHERE `id_choix` = $id";
    $choix = NULL;
    if ($rchoix = mysql_query($qchoix)) {
	$choix = mysql_fetch_assoc($rchoix);
    } else {
	echo "Échec de la requête sur la table choix. $qchoix ".mysql_error();
    }
    return $choix;
}

function selectionner_enseignant($id)
{
    $qens = "SELECT * FROM pain_enseignant WHERE `id_enseignant` = $id";
    $ens = NULL;
    if ($rens = mysql_query($qens)) {
	$ens = mysql_fetch_assoc($rens);
    } else {
	echo "Échec de la requête sur la table enseignant. $qens ".mysql_error();
    }
    return $ens;
}


function ig_tranche($t,$tag="") {
    $id = $t["id_tranche"];
    echo '<tr class="tranche"';
    action_dblcmodifiertranche($id);
    echo '>';
    echo '<td class="groupe">';
    debug_show_id($id);    
    echo  $t["groupe"];    
    echo '</td>';
    echo '<td class="enseignant">';
    echo ig_responsable($t["id_enseignant"]);
    echo '</td>';
    echo '<td class="CM">'.$t["cm"].'</td>';
    echo '<td class="TD">'.$t["td"].'</td>';
    echo '<td class="TP">'.$t["tp"].'</td>';
    echo '<td class="alt">'.$t["alt"].'</td>';
    echo '<td class="HTD">'.$t["htd"].'</td>';
    echo '<td class="remarque">'.$t["remarque"].'</td>';
    echo '<td class="action" id="tranche'.$tag.$id.'">';
    action_supprimertranche($id);
    echo '<br/>';
    action_modifiertranche($id);
    echo '</td>';
    echo '</tr>';
    echo "\n";
}

function ig_listtranches($tranches) {

    while ($tranche = mysql_fetch_array($tranches))
    {
	ig_tranche($tranche);
    }
}


function ig_formtranche($id_cours, $id_tranche = NULL, $cm = 0, $td= 0, $tp= 0, $alt= 0, $id_enseignant = -1, $groupe = 0, $remarque = "")
{    
    echo '<tr class="formtranche">';
    echo '<td class="groupe">';
    echo '<input type="text" name="groupe" value="'.$groupe.'" />';
    echo '</td>';
    echo '<td class="enseignant">';
    echo '<select name="id_enseignant" class="autocomplete">';
    ig_formselectenseignants($id_enseignant);
    echo '</select>';
    echo '</td>';
    echo '<td class="CM">';
    echo '<input type="text" name="cm" value="'.$cm.'" />';
    echo '</td>';
    echo '<td class="TD">'; 
    echo '<input type="text" name="td" value="'.$td.'" />';
    echo '</td>';
    echo '<td class="TP">'; 
    echo '<input type="text" name="tp" value="'.$tp.'" />';
    echo '</td>';
    echo '<td class="alt">';
    echo '<input type="text" name="alt" value="'.$alt.'" />';
    echo '</td>';
    echo '<td class="HTD">';
    echo '</td>';
    echo '<td class="remarque">';
    echo '<input type="hidden" name="id_cours" value="'.$id_cours.'"/>';
    if ($id_tranche != NULL) {
	echo '<input type="hidden" name="id_tranche" value="'.$id_tranche.'"/>';
    }
    echo '<textarea name="remarque" rows="2" cols="10">';
    echo $remarque;
    echo '</textarea></td>';
    echo '<td class="action">';
    action_envoyertrancheducours($id_cours, $id_tranche);
    if ($id_tranche != NULL) {
	action_annulermodifiertranche($id_tranche);
    }
    echo '<br/>';
    echo '</td>';
    echo '</tr>';
}


function supprimer_tranche($id)
{
    if (peuteditertranche($id)) {
	$tranche = selectionner_tranche($id);
	$qtranche = "DELETE FROM pain_tranche WHERE `id_tranche` = $id
                 LIMIT 1";
	
	if (mysql_query($qtranche)) {
	    historique_par_suppression(2, $tranche);
	    pain_log("$qtranche -- supprimer_tranche($id)");
	    echo '{"ok": "ok"}';
	} else {
	    errmsg("échec de la requête sur la table tranche.");
	}
    } else {
	errmsg("droits insuffisants.");
    }
}


function supprimer_enseignant($id) {
    if (peutsupprimerenseignant($id)) {
	if (estintervenant($id) 
	    || estresponsablecours($id)
	    || estresponsableformation($id)
	    || estresponsablesformation($id)) {
	    errmsg("suppression impossible. Cet enseignant a au moins une intervention ou une responsabilité renseignée dans la base.");
	    return;
	}

	$ens = selectionner_enseignant($id);
	$qens = "DELETE FROM pain_enseignant WHERE `id_enseignant` = $id LIMIT 1";
	
	if (mysql_query($qens)) {
	    historique_par_suppression(4, $ens);
	    pain_log("$qens -- supprimer_ens($id)");
	    $q = "DELETE FROM pain_service WHERE `id_enseignant` = $id";
	    mysql_query($q) or ($q .= " -- ".mysql_error());
	    pain_log("$q");
	    $q = "DELETE FROM pain_choix WHERE `id_enseignant` = $id";
	    mysql_query($q) or ($q .= " -- ".mysql_error());
	    pain_log("$q");
	    echo '{"ok": "ok"}';
	} else {
	    errmsg("échec de la requête sur la table enseignant.");
	}
    } else {
	errmsg("droits insuffisants.");
    }
}

function supprimer_choix($id) {
    if (peutsupprimerchoix($id)) {
	$choix = selectionner_choix($id);
	$qchoix = "DELETE FROM pain_choix WHERE `id_choix` = $id
                 LIMIT 1";
	
	if (mysql_query($qchoix)) {
	    historique_par_suppression(3, $choix);
	    pain_log("$qchoix -- supprimer_choix($id)");
	    echo '{"ok": "ok"}';
	} else {
	    errmsg("échec de la requête sur la table choix.");
	}
    } else {
	errmsg("droits insuffisants.");
    }
}

function supprimer_service($id_enseignant, $an) {
    if (peutsupprimerservice($id_enseignant, $an)) { 
	if (serviceestvide($id_enseignant, $an)) {	    
	    $qservice = "DELETE FROM pain_service WHERE `id_enseignant` = $id_enseignant AND `annee_universitaire` = $an LIMIT 1";
	
	    if (mysql_query($qservice)) {
//		historique_par_suppression(3, $choix);
		pain_log("$qservice -- supprimer_service($id_enseignant, $an)");
		echo '{"ok": "ok"}';
	    } else {
		errmsg("échec de la requête sur la table choix.");
	    }
	} else {
		errmsg("Il y a des interventions associées à ce service.");
	}
    } else {
	errmsg("droits insuffisants.");
    }
}

function formation_du_cours($id)
{
    $q = "SELECT id_formation FROM pain_cours WHERE `id_cours` = $id LIMIT 1";
    if ($r = mysql_query($q)) {
	$f = mysql_fetch_assoc($r);
    } else {
	echo "Échec de la requête sur la table cours. $q ".mysql_error();
    }
    return $f["id_formation"];
}



function ig_legendeenseignant() {
    echo '<tr>';
    echo '<th class="prenom">Prénom</th>';
    echo '<th class="nom">Nom</th>';
    echo '<th class="statut">statut</th>';
    echo '<th class="email">email</th>';
    echo '<th class="tel">tel</th>';
    echo '<th class="bureau">bureau</th>';
    echo '<th class="service">service plein statutaire</th>';
    echo '<th></th>';
    echo '</tr>';
    echo "\n";
}

function ig_enseignant($t) {
    $id = $t["id_enseignant"];
    echo '<tr class="enseignant">';
    echo '<td class="prenom">';
    debug_show_id($id);
    echo $t["prenom"];
    echo '</td>';
    echo '<td class="nom">'.$t["nom"].'</td>';
    echo '<td class="statut">'.$t["statut"].'</td>';
    echo '<td class="email">'.$t["email"].'</td>';
    echo '<td class="tel">'.$t["telephone"].'</td>';
    echo '<td class="bureau">'.$t["bureau"].'</td>';
    echo '<td class="service">'.$t["service"].'</td>';
    echo '<td class="action" id="enseignant'.$t["id_enseignant"].'"></td>';
    echo '</tr>';
    echo "\n";
}

function stats_sform($idsf) {
    $q = "SELECT pain_service.categorie AS categorie, SUM(pain_tranche.htd) as somme
          FROM pain_sformation, pain_formation, pain_cours, pain_tranche, pain_service
          WHERE pain_tranche.id_cours = pain_cours.id_cours
          AND pain_formation.id_formation = pain_cours.id_formation
          AND pain_formation.id_sformation = $idsf
          AND pain_sformation.id_sformation = $idsf
          AND ((pain_tranche.id_enseignant = pain_service.id_enseignant
                AND pain_cours.id_enseignant <> 1)
              OR
              (pain_cours.id_enseignant = 1 
               AND pain_service.categorie = 1))
          AND pain_service.annee_universitaire = pain_sformation.annee_universitaire
          GROUP BY pain_service.categorie";
    $r = mysql_query($q) or die("erreur d'acces aux tables: $q erreur: ".mysqlerror());
    $tab = array();

    while ($a = mysql_fetch_assoc($r)) {
	$tab[$a["categorie"]] = $a["somme"];
    }

    return $tab;
}



function htdtotaux($annee = NULL) {
    if ($annee == NULL) $annee = annee_courante();

    /* heures etudiants */
    $qetu = "SELECT SUM((cm + td + tp + alt) * presents) as etu
             FROM pain_sformation, pain_formation, pain_cours 
             WHERE  pain_formation.id_formation = pain_cours.id_formation 
             AND pain_sformation.id_sformation = pain_formation.id_sformation  
             AND annee_universitaire = $annee";
    $retu = mysql_query($qetu) 
	or die("erreur d'acces a la table tranche : $qetu erreur:".mysql_error());
    $letu = mysql_fetch_assoc($retu);    
    $etu = $letu["etu"];
    if ($etu == "") {
	$etu = 0;
    }


    $qannule ='SELECT SUM(htd) FROM pain_sformation, pain_formation, pain_cours, pain_tranche WHERE pain_tranche.id_cours = pain_cours.id_cours AND pain_formation.id_formation = pain_cours.id_formation AND pain_sformation.id_sformation = pain_formation.id_sformation  AND annee_universitaire = '.$annee.' AND (pain_tranche.id_enseignant = 1 OR pain_cours.id_enseignant = 1)';
    $rannule = mysql_query($qannule) 
	or die("erreur d'acces aux tables : $qannule erreur:".mysql_error());

    $annule = mysql_fetch_assoc($rannule);
    $annule = $annule["SUM(htd)"];
    if ($annule == "") {
	$annule = 0;
    } 

    $qcomp ='SELECT SUM(pain_tranche.cm) AS cm, SUM(pain_tranche.td) AS td, SUM(pain_tranche.tp) AS tp, SUM(pain_tranche.alt) AS alt FROM pain_sformation, pain_formation, pain_cours, pain_tranche WHERE pain_tranche.id_cours = pain_cours.id_cours AND pain_formation.id_formation = pain_cours.id_formation AND pain_sformation.id_sformation = pain_formation.id_sformation AND annee_universitaire = '.$annee;
    $rcomp = mysql_query($qcomp) 
	or die("erreur d'acces aux tables : $qcomp erreur:".mysql_error());

    $comp = mysql_fetch_assoc($rcomp);
    $cm = $comp["cm"];
    if ($cm == "") {
	$cm = 0;
    } 
    $td = $comp["td"];
    if ($td == "") {
	$td = 0;
    } 
    $tp = $comp["tp"];
    if ($tp == "") {
	$tp = 0;
    } 
    $alt = $comp["alt"];
    if ($alt == "") {
	$alt = 0;
    }
    $qperm ='SELECT pain_service.categorie AS categorie, SUM(htd) FROM pain_sformation, pain_formation, pain_cours, pain_tranche, pain_service WHERE pain_tranche.id_cours = pain_cours.id_cours AND pain_formation.id_formation = pain_cours.id_formation AND pain_sformation.id_sformation = pain_formation.id_sformation AND pain_sformation.annee_universitaire = '.$annee.' AND pain_tranche.id_enseignant = pain_service.id_enseignant AND pain_service.annee_universitaire = pain_sformation.annee_universitaire AND pain_cours.id_enseignant <> 1 GROUP BY pain_service.categorie';

    $rperm = mysql_query($qperm) 
	or die("erreur d'acces aux tables : $qperm erreur:".mysql_error());

    $perm = 0; $nperm = 0; $libre = 0; $mutualise = 0; $autre = 0; $ext = 0; $servi = 0;
    while ($cat = mysql_fetch_assoc($rperm)) {
	switch ($cat["categorie"]) {
	case 1: /* 'annule': decompte specifique */ break;
	case 2: /* permanents */
	    $perm += $cat["SUM(htd)"];
	    break;
	case 3: /* non permanents */
	    $nperm += $cat["SUM(htd)"];
	    break;
	case 22: /* enseignant 'mutualise' */
	    $mutualise += $cat["SUM(htd)"];
	    break;
	case 23: /* enseignant 'libre' */
	    $libre += $cat["SUM(htd)"];
	    break;
	case 29: /* enseignant 'autre' (exterieur) */ 
	    $autre += $cat["SUM(htd)"];
	    break;
	default: /* tout le reste = exterieurs */
	    $ext += $cat["SUM(htd)"];
	}
    }
    $servi = $ext + $autre + $perm + $nperm;

    return array("servi"=>$servi, 
		 "libre"=>$libre,
		 "mutualise"=>$mutualise,
		 "annule"=>$annule,
		 "permanents" => $perm,
		 "nonpermanents" => $nperm,
		 "exterieurs" =>$ext,
		 "autre" => $autre,
		 "cm"=>$cm,
		 "td"=>$td,
		 "tp"=>$tp,
		 "alt"=>$alt,
		 "total"=>$servi+$libre+$mutualise+$annule,
	         "etu"=>$etu);
}

function htdsuper($id) {    
    /* heures etudiants */
    $qetu = "SELECT SUM((cm + td + tp + alt) * presents) as etu
             FROM pain_formation, pain_cours
             WHERE pain_formation.id_sformation  = $id 
             AND pain_cours.id_formation = pain_formation.id_formation";
    $retu = mysql_query($qetu) 
	or die("erreur d'acces a la table tranche : $qetu erreur:".mysql_error());
    $letu = mysql_fetch_assoc($retu);    
    $etu = $letu["etu"];
    if ($etu == "") {
	$etu = 0;
    }


    $qannule ="SELECT SUM(htd) FROM pain_formation, pain_cours, pain_tranche WHERE pain_tranche.id_cours = pain_cours.id_cours AND pain_formation.id_formation = pain_cours.id_formation AND pain_formation.id_sformation = $id AND (pain_tranche.id_enseignant = 1 OR pain_cours.id_enseignant = 1)";
    $rannule = mysql_query($qannule) 
	or die("erreur d'acces aux tables : $qannule erreur:".mysql_error());

    $annule = mysql_fetch_assoc($rannule);
    $annule = $annule["SUM(htd)"];
    if ($annule == "") {
	$annule = 0;
    } 

    $qcomp ="SELECT SUM(pain_tranche.cm) AS cm, SUM(pain_tranche.td) AS td, SUM(pain_tranche.tp) AS tp, SUM(pain_tranche.alt) AS alt FROM pain_formation, pain_cours, pain_tranche WHERE pain_formation.id_sformation = $id AND pain_formation.id_formation = pain_cours.id_formation AND pain_tranche.id_cours = pain_cours.id_cours";
    $rcomp = mysql_query($qcomp) 
	or die("erreur d'acces aux tables : $qcomp erreur:".mysql_error());

    $comp = mysql_fetch_assoc($rcomp);
    $cm = $comp["cm"];
    if ($cm == "") {
	$cm = 0;
    } 
    $td = $comp["td"];
    if ($td == "") {
	$td = 0;
    } 
    $tp = $comp["tp"];
    if ($tp == "") {
	$tp = 0;
    } 
    $alt = $comp["alt"];
    if ($alt == "") {
	$alt = 0;
    }
    $qperm ="SELECT pain_service.categorie AS categorie, SUM(htd) FROM pain_sformation, pain_formation, pain_cours, pain_tranche, pain_service WHERE pain_tranche.id_cours = pain_cours.id_cours AND pain_formation.id_formation = pain_cours.id_formation AND pain_formation.id_sformation = $id AND pain_sformation.id_sformation = $id AND pain_tranche.id_enseignant = pain_service.id_enseignant AND pain_service.annee_universitaire = pain_sformation.annee_universitaire AND pain_cours.id_enseignant <> 1 GROUP BY pain_service.categorie";

    $rperm = mysql_query($qperm) 
	or die("erreur d'acces aux tables : $qperm erreur:".mysql_error());

    $perm = 0; $nperm = 0; $libre = 0; $mutualise = 0; $autre = 0; $ext = 0; $servi = 0;
    while ($cat = mysql_fetch_assoc($rperm)) {
	switch ($cat["categorie"]) {
	case 1: /* 'annule': decompte specifique */ break;
	case 2: /* permanents */
	    $perm += $cat["SUM(htd)"];
	    break;
	case 3: /* non permanents */
	    $nperm += $cat["SUM(htd)"];
	    break;
	case 22: /* enseignant 'mutualise' */
	    $mutualise += $cat["SUM(htd)"];
	    break;
	case 23: /* enseignant 'libre' */
	    $libre += $cat["SUM(htd)"];
	    break;
	case 29: /* enseignant 'autre' (exterieur) */ 
	    $autre += $cat["SUM(htd)"];
	    break;
	default: /* tout le reste = exterieurs */
	    $ext += $cat["SUM(htd)"];
	}
    }
    $servi = $ext + $autre + $perm + $nperm;

    return array("servi"=>$servi, 
		 "libre"=>$libre,
		 "mutualise"=>$mutualise,
		 "annule"=>$annule,
		 "permanents" => $perm,
		 "nonpermanents" => $nperm,
		 "exterieurs" =>$ext,
		 "autre" => $autre,
		 "cm"=>$cm,
		 "td"=>$td,
		 "tp"=>$tp,
		 "alt"=>$alt,
		 "total"=>$servi+$libre+$mutualise+$annule,
	         "etu"=>$etu);
}

function htdformation($id) {
    /* heures etudiants */
    $qetu = "SELECT SUM((cm + td + tp + alt) * presents) as etu
             FROM pain_cours WHERE id_formation = $id";
    $retu = mysql_query($qetu) 
	or die("erreur d'acces a la table tranche : $qetu erreur:".mysql_error());
    $letu = mysql_fetch_assoc($retu);    
    $etu = $letu["etu"];
    if ($etu == "") {
	$etu = 0;
    }

/* TODO ATTENTION annuler une intervention dans un cours lui-même annulé doit faire que l'intervention est compté deux fois dans le total des annulation, à vérifier ! */
    $qannule = "SELECT SUM(htd) FROM pain_cours, pain_tranche WHERE pain_tranche.id_cours = pain_cours.id_cours AND id_formation = $id AND (pain_tranche.id_enseignant = 1 OR pain_cours.id_enseignant = 1)";
    $rannule = mysql_query($qannule) 
	or die("erreur d'acces a la table tranche : $qannule erreur:".mysql_error());

    $annule = mysql_fetch_assoc($rannule);
    $annule = $annule["SUM(htd)"];
    if ($annule == "") {
	$annule = 0;
    }

    $qcomp ="SELECT SUM(pain_tranche.cm) AS cm, SUM(pain_tranche.td) AS td, SUM(pain_tranche.tp) AS tp, SUM(pain_tranche.alt) AS alt FROM pain_cours, pain_tranche WHERE pain_cours.id_formation = $id AND pain_tranche.id_cours = pain_cours.id_cours";
    $rcomp = mysql_query($qcomp) 
	or die("erreur d'acces aux tables : $qcomp erreur:".mysql_error());

    $comp = mysql_fetch_assoc($rcomp);
    $cm = $comp["cm"];
    if ($cm == "") {
	$cm = 0;
    } 
    $td = $comp["td"];
    if ($td == "") {
	$td = 0;
    } 
    $tp = $comp["tp"];
    if ($tp == "") {
	$tp = 0;
    } 
    $alt = $comp["alt"];
    if ($alt == "") {
	$alt = 0;
    }
    $qperm ="SELECT pain_service.categorie AS categorie, SUM(htd) FROM pain_sformation, pain_formation, pain_cours, pain_tranche, pain_service WHERE pain_cours.id_formation = $id AND pain_tranche.id_cours = pain_cours.id_cours AND pain_formation.id_formation = $id AND pain_sformation.id_sformation = pain_formation.id_sformation AND pain_tranche.id_enseignant = pain_service.id_enseignant AND pain_service.annee_universitaire = pain_sformation.annee_universitaire AND pain_cours.id_enseignant <> 1 GROUP BY pain_service.categorie";

    $rperm = mysql_query($qperm) 
	or die("erreur d'acces aux tables : $qperm erreur:".mysql_error());

    $perm = 0; $nperm = 0; $libre = 0; $mutualise = 0; $autre = 0; $ext = 0; $servi = 0;
    while ($cat = mysql_fetch_assoc($rperm)) {
	switch ($cat["categorie"]) {
	case 1: /* 'annule': decompte specifique */ break;
	case 2: /* permanents */
	    $perm += $cat["SUM(htd)"];
	    break;
	case 3: /* non permanents */
	    $nperm += $cat["SUM(htd)"];
	    break;
	case 22: /* enseignant 'mutualise' */
	    $mutualise += $cat["SUM(htd)"];
	    break;
	case 23: /* enseignant 'libre' */
	    $libre += $cat["SUM(htd)"];
	    break;
	case 29: /* enseignant 'autre' (exterieur) */ 
	    $autre += $cat["SUM(htd)"];
	    break;
	default: /* tout le reste = exterieurs */
	    $ext += $cat["SUM(htd)"];
	}
    }
    $servi = $ext + $autre + $perm + $nperm;

    return array("servi"=>$servi, 
		 "libre"=>$libre,
		 "mutualise"=>$mutualise,
		 "annule"=>$annule,
		 "permanents" => $perm,
		 "nonpermanents" => $nperm,
		 "exterieurs" =>$ext,
		 "autre" => $autre,
		 "cm"=>$cm,
		 "td"=>$td,
		 "tp"=>$tp,
		 "alt"=>$alt,
		 "total"=>$servi+$libre+$mutualise+$annule,
	         "etu"=>$etu);
}


function htdcours($id) {
    /* heures etudiants */
    $qetu = "SELECT (cm + td + tp + alt) * presents as etu
             FROM pain_cours WHERE id_cours = $id";
    $retu = mysql_query($qetu) 
	or die("erreur d'acces a la table tranche : $qetu erreur:".mysql_error());
    $letu = mysql_fetch_assoc($retu);    
    $etu = $letu["etu"];
    if ($etu == "") {
	$etu = 0;
    }

    $qannule = "SELECT SUM(htd) FROM pain_tranche WHERE pain_tranche.id_cours = $id AND pain_tranche.id_enseignant = 1";
    $rannule = mysql_query($qannule) 
	or die("erreur d'acces a la table tranche : $qannule erreur:".mysql_error());

    $annule = mysql_fetch_assoc($rannule);
    $annule = $annule["SUM(htd)"];
    if ($annule == "") {
	$annule = 0;
    }

    $qcomp ="SELECT SUM(pain_tranche.cm) AS cm, SUM(pain_tranche.td) AS td, SUM(pain_tranche.tp) AS tp, SUM(pain_tranche.alt) AS alt FROM pain_tranche WHERE pain_tranche.id_cours = $id";
    $rcomp = mysql_query($qcomp) 
	or die("erreur d'acces aux tables : $qcomp erreur:".mysql_error());

    $comp = mysql_fetch_assoc($rcomp);
    $cm = $comp["cm"];
    if ($cm == "") {
	$cm = 0;
    } 
    $td = $comp["td"];
    if ($td == "") {
	$td = 0;
    } 
    $tp = $comp["tp"];
    if ($tp == "") {
	$tp = 0;
    } 
    $alt = $comp["alt"];
    if ($alt == "") {
	$alt = 0;
    }
    $qperm ="SELECT pain_service.categorie AS categorie, SUM(htd) FROM pain_sformation, pain_formation, pain_cours, pain_tranche, pain_service WHERE pain_cours.id_cours = $id AND pain_tranche.id_cours = pain_cours.id_cours AND pain_formation.id_formation = pain_cours.id_formation AND pain_sformation.id_sformation = pain_formation.id_sformation AND pain_tranche.id_enseignant = pain_service.id_enseignant AND pain_service.annee_universitaire = pain_sformation.annee_universitaire AND pain_cours.id_enseignant <> 1 GROUP BY pain_service.categorie";

    $rperm = mysql_query($qperm) 
	or die("erreur d'acces aux tables : $qperm erreur:".mysql_error());

    $perm = 0; $nperm = 0; $libre = 0; $mutualise = 0; $autre = 0; $ext = 0; $servi = 0;
    while ($cat = mysql_fetch_assoc($rperm)) {
	switch ($cat["categorie"]) {
	case 1: /* 'annule': decompte specifique */ break;
	case 2: /* permanents */
	    $perm += $cat["SUM(htd)"];
	    break;
	case 3: /* non permanents */
	    $nperm += $cat["SUM(htd)"];
	    break;
	case 22: /* enseignant 'mutualise' */
	    $mutualise += $cat["SUM(htd)"];
	    break;
	case 23: /* enseignant 'libre' */
	    $libre += $cat["SUM(htd)"];
	    break;
	case 29: /* enseignant 'autre' (exterieur) */ 
	    $autre += $cat["SUM(htd)"];
	    break;
	default: /* tout le reste = exterieurs */
	    $ext += $cat["SUM(htd)"];
	}
    }
    $servi = $ext + $autre + $perm + $nperm;

    return array("servi"=>$servi, 
		 "libre"=>$libre,
		 "mutualise"=>$mutualise,
		 "annule"=>$annule,
		 "permanents" => $perm,
		 "nonpermanents" => $nperm,
		 "exterieurs" =>$ext,
		 "autre" => $autre,
		 "cm"=>$cm,
		 "td"=>$td,
		 "tp"=>$tp,
		 "alt"=>$alt,
		 "total"=>$servi+$libre+$mutualise+$annule,
	         "etu"=>$etu);
}


function htdcours_old($id) {

    $qservi = 'SELECT SUM(htd) FROM pain_tranche WHERE id_cours = '.$id.' AND id_enseignant > 8';
    $rservi = mysql_query($qservi) 
	or die("erreur d'acces a la table tranche : $qservi erreur:".mysql_error());

    $servi = mysql_fetch_assoc($rservi);
    $servi = $servi["SUM(htd)"];
    if ($servi == "") {
	$servi = 0;
    } 

    $qmutualise = 'SELECT SUM(htd) FROM pain_tranche WHERE id_cours = '.$id.' AND id_enseignant = 2';
    $rmutualise = mysql_query($qmutualise) 
	or die("erreur d'acces a la table tranche : $qmutualise erreur:".mysql_error());
    
    $mutualise = mysql_fetch_assoc($rmutualise);
    $mutualise = $mutualise["SUM(htd)"];
    if ($mutualise == "") {
	$mutualise = 0;
    }

    $qlibre = 'SELECT SUM(htd) FROM pain_tranche WHERE id_cours = '.$id.' AND id_enseignant = 3';
    $rlibre = mysql_query($qlibre) 
	or die("erreur d'acces a la table tranche : $qlibre erreur:".mysql_error());

    $libre = mysql_fetch_assoc($rlibre);
    $libre = $libre["SUM(htd)"];
    if ($libre == "") {
	$libre = 0;
    }

    $qannule = 'SELECT SUM(htd) FROM pain_tranche WHERE id_cours = '.$id.' AND id_enseignant = 1';
    $rannule = mysql_query($qannule) 
	or die("erreur d'acces a la table tranche : $qannule erreur:".mysql_error());
    
    $annule = mysql_fetch_assoc($rannule);
    $annule = $annule["SUM(htd)"];
    if ($annule == "") {
	$annule = 0;
    }

    $qtp = 'SELECT SUM(tp) FROM pain_tranche WHERE id_cours = '.$id;
    $rtp = mysql_query($qtp) 
	or die("erreur d'acces a la table tranche : $qtp erreur:".mysql_error());
    
    $tp = mysql_fetch_assoc($rtp);
    $tp = $tp["SUM(tp)"];
    if ($tp == "") {
	$tp = 0;
    }

    if (responsableducours($id) == 1) {/* cours annulé */
	$annule += $servi + $libre + $mutualise;
	$servi = $libre = $mutualise = 0;
    }

    return array("servi"=>$servi, 
		 "libre"=>$libre, 
		 "annule"=>$annule, 
		 "mutualise"=>$mutualise,
		 "tp"=>$tp);
}

function ig_htd($totaux) {
    $total = $totaux["servi"] + $totaux["mutualise"] + $totaux["libre"] + $totaux["annule"];
    echo $total.'H ';
    echo '(dont '.$totaux["tp"].'H&nbsp;TP) = ';
    echo $totaux["servi"].'H&nbsp;servies +&nbsp;';
    echo $totaux["mutualise"].'H&nbsp;mutualisées +&nbsp;';
    echo $totaux["libre"].'H&nbsp;à pourvoir +&nbsp;';
    echo $totaux["annule"].'H&nbsp;annulées';
    echo ' (dont '.$totaux["permanents"].'H&nbsp;permanents)';
}

function enpostes($htd) {
    return round($htd / 192.0, 2);
}

function ig_totauxenpostes($totaux) {
    echo enpostes($totaux["total"]).' postes ';
    if (1) {
	echo '('.enpostes($totaux["cm"]).'&nbsp;CM + '.enpostes($totaux["td"]).'&nbsp;TD + '.enpostes($totaux["tp"]).'&nbsp;TP + '.enpostes($totaux["alt"]).'&nbsp;alt)<br/>';
    }
    echo '=&nbsp;';
    echo enpostes($totaux["servi"]).'&nbsp;servis +&nbsp;';
    echo enpostes($totaux["mutualise"]).'&nbsp;mutualisés +&nbsp;';
    echo enpostes($totaux["libre"]).'&nbsp;à pourvoir +&nbsp;';
    echo enpostes($totaux["annule"]).'&nbsp;annulés';
    echo '<br/>Département: '.enpostes($totaux["permanents"] + $totaux["nonpermanents"] + $totaux["libre"]).'  = '.enpostes($totaux["permanents"]).'&nbsp;permanents + '.enpostes($totaux["nonpermanents"]).'&nbsp;non-permanents + '.enpostes($totaux["libre"]).'&nbsp;à pourvoir';
    echo '<br/>Extérieurs: '.enpostes($totaux["exterieurs"] + $totaux["autre"]).' = '.enpostes($totaux["exterieurs"])." servis + ".enpostes($totaux["autre"])." inconnus";
}

function responsableducours($id) {
    $qresponsable = 'SELECT id_enseignant FROM pain_cours WHERE id_cours = '.$id;
    $rresponsable = mysql_query($qresponsable)
	or die("erreur d'acces a la table cours : $qresponsable erreur:".mysql_error());
    $responsable = mysql_fetch_assoc($rresponsable);
    return $responsable["id_enseignant"];    
}

function estintervenant($id_enseignant)
{
    $q = "SELECT 1 FROM pain_tranche WHERE id_enseignant = $id_enseignant LIMIT 1";
    $r = mysql_query($q) or die("erreur estintervenant($id_enseignant): $q<br>mysql a repondu ".mysql_error());
    return mysql_num_rows($r);
}

function estresponsablecours($id_enseignant)
{
    $q = "SELECT 1 FROM pain_cours WHERE id_enseignant = $id_enseignant LIMIT 1";
    $r = mysql_query($q) or die("erreur estresponsablecours($id_enseignant): $q<br>mysql a repondu ".mysql_error());
    return mysql_num_rows($r);
}

function estresponsableformation($id_enseignant)
{
    $q = "SELECT 1 FROM pain_formation WHERE id_enseignant = $id_enseignant LIMIT 1";
    $r = mysql_query($q) or die("erreur estresponsableformation($id_enseignant): $q<br>mysql a repondu ".mysql_error());
    return mysql_num_rows($r);
}

function estresponsablesformation($id_enseignant)
{
    $q = "SELECT 1 FROM pain_sformation WHERE id_enseignant = $id_enseignant LIMIT 1";
    $r = mysql_query($q) or die("erreur estresponsablesformation($id_enseignant): $q<br>mysql a repondu ".mysql_error());
    return mysql_num_rows($r);
}

function serviceestvide($id_enseignant, $an) {
    $res =  listeinterventions($id_enseignant, $an);
    if (mysql_fetch_array($res)) {
	return false;
    } else {
	return true;
    }
}

function listeinterventions($id_enseignant, $an = NULL) {    
    global $annee;
    if ($an == NULL) {
	if ($annee == NULL) {
	    $annee = annee_courante();
	}
	$an = $annee;
    }
    $query = "SELECT 
pain_tranche.id_tranche,
pain_formation.nom,
pain_formation.annee_etude,
pain_formation.parfum,
pain_cours.semestre,
pain_cours.nom_cours,
pain_cours.code_geisha,
pain_cours.id_cours AS id_cours,
pain_tranche.groupe,
pain_tranche.cm,
pain_tranche.td,
pain_tranche.tp,
pain_tranche.alt,
pain_tranche.htd,
pain_tranche.remarque
FROM pain_tranche, pain_cours, pain_formation, pain_sformation
WHERE ".(($id_enseignant == 1)?
	 "(pain_tranche.id_enseignant = 1 OR pain_cours.id_enseignant = 1)"
	 :"pain_tranche.id_enseignant =".$id_enseignant." AND pain_cours.id_enseignant <> 1")."
AND pain_tranche.id_cours = pain_cours.id_cours
AND pain_cours.id_formation = pain_formation.id_formation
AND pain_formation.id_sformation = pain_sformation.id_sformation
AND pain_sformation.annee_universitaire = $an
ORDER by  pain_cours.semestre ASC, pain_formation.numero ASC, pain_cours.id_cours";

    ($result = mysql_query($query)) or die("Échec de la connexion à la base");
    return $result;
}

function ig_legendeintervention() {
    echo '<th class="formation">formation</th>';
    echo '<th class="nom_cours">intitulé</th>';
    echo '<th class="code_geisha">code geisha</th>';
    echo '<th class="semestre">semestre</th>';
    echo '<th class="groupe">Groupe</th>';
    echo '<th class="CM">CM</th>';
    echo '<th class="TD">TD</th>';
    echo '<th class="TP">TP</th>';
    echo '<th class="alt">alt.</th>';
    echo '<th class="HTD">htd</th>';
    echo '<th class="remarque">Remarque</th>';
}

function ig_intervention($i) {
    $id = $i["id_tranche"];
    echo '<td class="formation">';
    debug_show_id($id);
    echo $i["nom"]." ".$i["annee_etude"]." ";
    echo $i["parfum"];
    echo '</td>';
    echo '<td class="nom_cours">';
    echo $i["nom_cours"];
    echo '</td>';
    echo '<td class="code_geisha">';
    echo $i["code_geisha"];
    echo '</td>';    
    echo '<td class="semestre">';
    echo $i["semestre"];
    echo '</td>';
    echo '<td class="groupe">'.$i["groupe"].'</td>';
/*    echo '<td class="enseignant">';
    echo ig_responsable($i["pain_tranche.id_enseignant"]);
    echo '</td>'; */
    echo '<td class="CM">'.$i["cm"].'</td>';
    echo '<td class="TD">'.$i["td"].'</td>';
    echo '<td class="TP">'.$i["tp"].'</td>';
    echo '<td class="alt">'.$i["alt"].'</td>';
    echo '<td class="HTD">'.$i["htd"].'</td>';
    echo '<td class="remarque">'.$i["remarque"].'</td>';
}

function totauxinterventions($id_enseignant) {
    global $annee;
    if ($annee == NULL) $annee = annee_courante();
    $query = "SELECT 
SUM(pain_tranche.cm) AS cm,
SUM(pain_tranche.td) AS td,
SUM(pain_tranche.tp) AS tp,
SUM(pain_tranche.alt) AS alt,
SUM(pain_tranche.htd) AS htd
FROM pain_tranche, pain_cours, pain_formation, pain_sformation
WHERE ".(($id_enseignant == 1)?
	 "(pain_tranche.id_enseignant = 1 OR pain_cours.id_enseignant = 1)"
	 :"pain_tranche.id_enseignant =".$id_enseignant." AND pain_cours.id_enseignant <> 1")."
AND pain_tranche.id_cours = pain_cours.id_cours
AND pain_cours.id_formation = pain_formation.id_formation
AND pain_formation.id_sformation = pain_sformation.id_sformation
AND pain_sformation.annee_universitaire = $annee
ORDER by pain_formation.numero ASC, pain_cours.semestre ASC";

    ($result = mysql_query($query)) or die("Échec de la connexion à la base enseignant");
    $totaux = mysql_fetch_array($result);
    return $totaux;
}

function ig_totauxinterventions($totaux) {
    echo '<th style="text-align:right;" colspan="5">';
    echo 'totaux';
    echo '</th>';
    echo '<td class="CM">'.$totaux["cm"].'</td>';
    echo '<td class="TD">'.$totaux["td"].'</td>';
    echo '<td class="TP">'.$totaux["tp"].'</td>';
    echo '<td class="alt">'.$totaux["alt"].'</td>';
    echo '<td class="HTD">'.$totaux["htd"].'</td>';
    echo '<th></th>';
}



function listeservice($id_enseignant) {
    global $annee;
    if ($annee == NULL) $annee = annee_courante();
$query = "SELECT 
pain_formation.nom,
pain_formation.annee_etude,
pain_formation.parfum,
pain_cours.semestre,
pain_cours.nom_cours,
pain_cours.code_geisha,
pain_cours.id_cours AS id_cours,
SUM(pain_tranche.cm) AS cm,
SUM(pain_tranche.td) AS td,
SUM(pain_tranche.tp) AS tp,
SUM(pain_tranche.alt) AS alt
FROM pain_tranche, pain_cours, pain_formation, pain_sformation
WHERE ".(($id_enseignant == 1)?
	 "(pain_tranche.id_enseignant = 1 OR pain_cours.id_enseignant = 1)"
	 :"pain_tranche.id_enseignant =".$id_enseignant." AND pain_cours.id_enseignant <> 1")."
AND pain_tranche.id_cours = pain_cours.id_cours
AND pain_cours.id_formation = pain_formation.id_formation
AND pain_formation.id_sformation = pain_sformation.id_sformation
AND pain_sformation.annee_universitaire = $annee
GROUP BY pain_cours.id_cours
ORDER by  pain_cours.semestre ASC, pain_formation.numero ASC";

   ($result = mysql_query($query)) or die("Échec de la connexion à la base= $query");
    return $result;
}


function ig_legendeservice() {
    echo '<tr class="ligne_service">';
    echo '<th class="code_geisha">';
    echo  "Code UE";
    echo '</th>';
    echo '<th class="nom_cours">';
    echo "Libellé de l'UE";
    echo '</th>';
    echo '<th class="semestre">';
    echo "Période";
    echo '</th>';
    echo '<th class="CM">CM</th>';
    echo '<th class="TD">TD</th>';
    echo '<th class="TP">TP</th>';
/*    echo '<th class="regime">Régime</th>'; */
    echo '</tr>';
}
function ig_ligneservice($ligne) {
    echo '<tr class="ligne_service">';
    echo '<td class="code_geisha">';
    echo $ligne["code_geisha"];
    echo '</td>';
    echo '<td class="nom_cours">';
    echo $ligne["nom_cours"];
    echo '</td>';
    echo '<td class="semestre">';
    echo 'S'.$ligne["semestre"];
    echo '</td>';
    echo '<td class="CM">'.$ligne["cm"].'</td>';
    echo '<td class="TD">'.($ligne["td"] + $ligne["alt"]).'</td>';
    echo '<td class="TP">'.$ligne["tp"].'</td>';
/*    echo '<td class="regime">FI</td>'; */
    echo '</tr>';
}

function ig_totauxservice($totaux) {
    echo '<tr class="ligne_service">';
    echo '<td colspan="2"></td>';
    echo '<td>';
    echo 'TOTAL';
    echo '</td>';
    echo '<td class="CM">'.$totaux["cm"].'</td>';
    echo '<td class="TD">'.($totaux["td"] + $totaux["alt"]).'</td>';
    echo '<td class="TP">'.$totaux["tp"].'</td>';
/*    echo '<td class="regime"></td>'; */
    echo '</tr>';
}

function update_servicesreels($id_ens = NULL) {
    global $annee;
    if ($annee == NULL) $annee = annee_courante();
/* ne pas loguer */
    $qupdate = "UPDATE pain_service
                SET 
                service_reel =
                  (SELECT SUM(pain_tranche.htd) 
                    FROM pain_tranche, pain_cours, pain_formation, pain_sformation 
                    WHERE pain_tranche.id_enseignant = pain_service.id_enseignant 
                    AND pain_tranche.id_cours = pain_cours.id_cours AND pain_cours.id_enseignant <> 1 
                    AND pain_formation.id_formation = pain_cours.id_formation
                    AND pain_sformation.id_sformation = pain_formation.id_sformation ".
	//" AND pain_formation.id_formation <> 22 ".
                  " AND pain_sformation.annee_universitaire =  pain_service.annee_universitaire) ";
    if (NULL == $id_ens) {
	$qupdate .= " WHERE pain_service.annee_universitaire = ".$annee;
    } else {
	$qupdate .= " WHERE pain_service.id_enseignant = ".$id_ens;
    }
    mysql_query($qupdate)
	or die("erreur update_servicesreels : $qupdate: ".mysql_error());
}

function liste_enseignantscategorie($categorie) {
    global $annee;
    if ($annee == NULL) $annee = annee_courante();
    $q = "SELECT pain_enseignant.id_enseignant AS id_enseignant,
                 nom,
                 prenom,
                 pain_service.service_annuel AS service,
                 pain_service.service_reel AS service_reel 
          FROM pain_enseignant, pain_service
          WHERE pain_service.categorie = $categorie 
            AND pain_enseignant.id_enseignant > 9
            AND pain_service.id_enseignant = pain_enseignant.id_enseignant
            AND pain_service.annee_universitaire = $annee
          ORDER by nom,prenom ASC";
    ($r = mysql_query($q)) or die("Échec de la connexion à la base enseignant");
    return $r;
}


function historique_par_cmp($type, $before, $after) {
    global $user;
    $id = 0;
    $id_formation = 0;
    $id_cours = 0;
    $modifie = false;
    $timestamp = $after["modification"];
    $s = '<div class="nom">'.$user["prenom"].' '.$user["nom"].'</div>';
    $s .= '<div class="diff">';
    if ((1 == $type) 
	&& ($before["id_cours"] == $after["id_cours"])) {
	$id_cours = $id = $before["id_cours"];
	$id_formation = $after["id_formation"];
    } else if ((2 == $type) 
               && ($before["id_tranche"] == $after["id_tranche"])) {
	$id = $before["id_tranche"];
	$id_cours = $before["id_cours"];
	$id_formation = formation_du_cours($after["id_cours"]);
   } else if ((3 == $type) 
               && ($before["id_choix"] == $after["id_choix"])) {
	$id = $before["id_choix"];
	$id_cours = $before["id_cours"];
	$id_formation = formation_du_cours($after["id_cours"]);
    } else {
	$s .= ' BUG ';	
    }
    foreach($before as $key => $value) { /* calcul du diff */
	if (0 != strcmp($key, "modification")) {
	    if (0 != strcmp($value, $after[$key])) {
		$modifie = true;
		$s .= '<div class="champ">';
		$s .= $key;
		$s .= '</div>';
		$s .= '<div class="before">';
		$s .= $value;
		$s .= '</div>';
		$s .= '<div class="after">';
		$s .= $after[$key];
		$s .= '</div>';
	    }
	}
    }
    $s .= '</div>';
    if (!$modifie) return; /* pas de modification on ne log pas */
    $q = "INSERT INTO pain_hist 
          (type, id, id_formation, id_cours, message, timestamp) 
          VALUES ('".$type."', '".$id."', '".$id_formation."',
                  '".$id_cours."', '".$s."', '".$timestamp."')";
    mysql_query($q) or die("$q ".mysql_error());
    pain_log($q);
}

function historique_par_ajout($type, $new) {
    global $user;
    $id = 0;
    $id_formation = 0;
    $id_cours = 0;
    $timestamp = $new["modification"];
    $s = '<div class="nom">'.$user["prenom"].' '.$user["nom"].'</div>';
    $s .= '<div class="diff">';
    if (1 == $type) {
	$id_cours = $id = $new["id_cours"];
	$id_formation = $new["id_formation"];
    } else if (2 == $type) {
	$id = $new["id_tranche"];
	$id_cours = $new["id_cours"];
	$id_formation = formation_du_cours($new["id_cours"]);
    } else if (3 == $type) {
	$id = $new["id_choix"];
	$id_cours = $new["id_cours"];
	$id_formation = formation_du_cours($new["id_cours"]);   
    } else {
	$s .= ' BUG ';	
    }
    $s .= "création";
    $s .= '</div>';    
    $q = "INSERT INTO pain_hist 
          (type, id, id_formation, id_cours, message, timestamp) 
          VALUES ('".$type."', '".$id."', '".$id_formation."', 
                  '".$id_cours."', '".$s."', NOW())";
    mysql_query($q) or die("$q ".mysql_error());
    pain_log($q);
}

function historique_par_suppression($type, $old) {
    global $user;
    $id = 0;
    $id_formation = 0;
    $id_cours = 0;
    $s = '<div class="nom">'.$user["prenom"].' '.$user["nom"].'</div>';
    $s .= '<div class="diff">';
    if (1 == $type) {
	$id_cours = $id = $old["id_cours"];
	$id_formation = $old["id_formation"];
    } else if (2 == $type) {
	$id = $old["id_tranche"];
	$id_cours = $old["id_cours"];
	$id_formation = formation_du_cours($old["id_cours"]);
    } else if (3 == $type) {
	$id = $old["id_choix"];
	$id_cours = $old["id_cours"];
	$id_formation = formation_du_cours($old["id_cours"]);
    } else if (4 == $type) {
	$id = $old["id_enseignant"];
	$s .= $old["prenom"]." ".$old["nom"]." : ";
    } else {
	$s .= "BUG ";
    }
    $s .= "suppression";
    $s .= '</div>';
    $q = "INSERT INTO pain_hist (type, id, id_formation, id_cours, message) 
          VALUES ('".$type."', '".$id."', '".$id_formation."', '".$id_cours."',
                  '".$s."')";
    mysql_query($q) or die("$q ".mysql_error());
    pain_log($q);
}

function historique_de_formation($id,$timestamp = NULL) {
    $q = "SELECT * from pain_hist 
          WHERE id_formation = $id
          AND (type = 1 OR type = 2 OR type = 3)";
    if ($timestamp != NULL) {
	$q .= " AND timestamp <= \"$timestamp\" ";
    }
    $q .= "ORDER BY timestamp DESC LIMIT 51";
    $r = mysql_query($q) 
	or die("historique_de_formation($id), $q ".mysql_error());
    return $r;
}

function ig_historique($h) {
    echo '<div class="nav">';
    switch ($h["type"]) {
    case 1:
	echo '<a href="#cours_'.$h["id"].'">';
	echo '<img src="css/img/cours.png" />';
	echo '</a>';
	break;
    case 2:
	echo '<a href="#tranche_'.$h["id"].'">';
	echo '<img src="css/img/tranche.png" />';
	echo '</a>';
	break;
    case 3:
	echo '<a href="#choix_'.$h["id"].'">';
	echo '<img src="css/img/choix.png" />';
	echo '</a>';
	break;
    default: 
	echo 'BUG';
    }
    echo '<span class="id">'.$h["id"].'</span>';
    echo '</div>';
    echo '<div class="timestamp">';
    echo $h["timestamp"];
    echo '</div>';
    echo '<div class="message">';
    echo $h["message"];
    echo '</div>';
}
?>