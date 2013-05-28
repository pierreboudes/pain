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

/** @file inc_functions.php
 Les fonctions générales
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


function ig_formselectenseignants($id_enseignant) /* obsolete, modifier inc_service */
{
    global $link;
/*    global $annee;
    if ($annee == NULL) $annee = annee_courante();
*/
    $qens = "SELECT `id_enseignant`, `prenom`, `nom`
             FROM pain_enseignant WHERE 1 ORDER BY `nom`, `prenom` ASC";
    $rens = $link->query($qens)
	  or die("Échec de la requête sur la table enseignant");
    while ($ens = $rens->fetch_array()) {
	echo '<option ';
	if ($ens["id_enseignant"] == $id_enseignant) {
	    echo 'selected="selected" ';
	}
	echo  'value="'.$ens["id_enseignant"].'">';
	echo trim($ens["nom"]." ".$ens["prenom"]);
	echo '</option>';
    }
}

function lister_enseignantsannee($an, $categorie = NULL)
{
    global $link;
    $qens = "SELECT pain_enseignant.id_enseignant AS `id_enseignant`, ".
	           "pain_enseignant.id_enseignant AS `id`, ".
                   "TRIM(CONCAT(prenom, ' ',nom)) AS `label`, ".
                   "pain_service.service_annuel as service ".
             "FROM pain_enseignant, pain_service ".
             "WHERE pain_service.annee_universitaire = $an ".
             "AND pain_service.id_enseignant = pain_enseignant.id_enseignant ";
    if (NULL != $categorie) {
	$qens .= "AND pain_service.categorie = $categorie ";
    }
    $qens .= "ORDER BY nom, prenom ASC";
    $rens = $link->query($qens)
	or die("Échec de la requête sur la table enseignant: $qens mysql a repondu: ".$link->error);
    return $rens;
}

function lister_categories()
{
    global $link;
    $qcat = "SELECT id_categorie,".
                   "id_categorie AS `id`, ".
                   "TRIM(nom_court) AS `label`, ".
	           "nom_long, descriptif ".
	"FROM pain_categorie ".
	"WHERE descriptif <> \"\" ". /* <- debile, \TODO : trouver la bonne structure */
	"ORDER BY id_categorie ASC";
    $rcat = $link->query($qcat)
	or die("Échec de la requête sur la table categorie: $qens mysql a repondu: ".$link->error);
    return $rcat;
}

function selectionner_cours($id)
{
    global $link;
    $qcours = "SELECT * FROM pain_cours WHERE `id_cours` = $id";
    $cours = NULL;
    if ($rcours = $link->query($qcours)) {
	$cours = $rcours->fetch_assoc();
    } else {
	echo "Échec de la requête sur la table cours. $qcours ".$link->error;
    }
    return $cours;
}

function selectionner_tranche($id)
{
    global $link;
    $qtranche = "SELECT * FROM pain_tranche WHERE `id_tranche` = $id";
    $tranche = NULL;
    if ($rtranche = $link->query($qtranche)) {
	$tranche = $rtranche->fetch_assoc();
    } else {
	echo "Échec de la requête sur la table tranche. $qtranche ".$link->error;
    }
    return $tranche;
}

function selectionner_choix($id)
{
    global $link;
    $qchoix = "SELECT * FROM pain_choix WHERE `id_choix` = $id";
    $choix = NULL;
    if ($rchoix = $link->query($qchoix)) {
	$choix = $rchoix->fetch_assoc();
    } else {
	echo "Échec de la requête sur la table choix. $qchoix ".$link->error;
    }
    return $choix;
}

function selectionner_enseignant($id)
{
    global $link;
    $qens = "SELECT * FROM pain_enseignant WHERE `id_enseignant` = $id";
    $ens = NULL;
    if ($rens = $link->query($qens)) {
	$ens = $rens->fetch_assoc();
    } else {
	echo "Échec de la requête sur la table enseignant. $qens ".$link->error;
    }
    return $ens;
}


function formation_du_cours($id)
{
    global $link;
    $q = "SELECT id_formation FROM pain_cours WHERE `id_cours` = $id LIMIT 1";
    if ($r = $link->query($q)) {
	$f = $r->fetch_assoc();
    } else {
	echo "Échec de la requête sur la table cours. $q ".$link->error;
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
    global $link;
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
    $r = $link->query($q) or die("erreur d'acces aux tables: $q erreur: ".mysqlerror());
    $tab = array();

    while ($a = $r->fetch_assoc()) {
	$tab[$a["categorie"]] = $a["somme"];
    }

    return $tab;
}

function htd_cat2array($rperm) {
    global $link;
    $perm = 0; $nperm = 0; $libre = 0; $mutualise = 0; $autre = 0; $ext = 0; $servi = 0;
    $librecm=0; $libretd=0; $libretp=0; $librealt=0;
    $permcm=0; $permtd=0; $permtp=0; $permalt=0;
    $npermcm=0; $npermtd=0; $npermtp=0; $npermalt=0;
    $mutualisecm=0; $mutualisetd=0; $mutualisetp=0; $mutualisealt=0;
    $autrecm=0; $autretd=0; $autretp=0; $autrealt=0;
    $extcm=0; $exttd=0; $exttp=0; $extalt=0;
    while ($cat =$rperm->fetch_assoc()) {
	switch ($cat["categorie"]) {
	case 1: /* 'annule': decompte specifique */ break;
	case 2: /* permanents */
	    $perm += $cat["SUM(htd)"];
	    $permcm += $cat["SUM(pain_tranche.cm)"];
	    $permtd += $cat["SUM(pain_tranche.td)"];
	    $permtp += $cat["SUM(pain_tranche.tp)"];
	    $permalt += $cat["SUM(pain_tranche.alt)"];
	    break;
	case 3: /* non permanents */
	    $nperm += $cat["SUM(htd)"];
	    $npermcm += $cat["SUM(pain_tranche.cm)"];
	    $npermtd += $cat["SUM(pain_tranche.td)"];
	    $npermtp += $cat["SUM(pain_tranche.tp)"];
	    $npermalt += $cat["SUM(pain_tranche.alt)"];
	    break;
	case 22: /* enseignant 'mutualise' */
	    $mutualise += $cat["SUM(htd)"];
	    $mutualisecm += $cat["SUM(pain_tranche.cm)"];
	    $mutualisetd += $cat["SUM(pain_tranche.td)"];
	    $mutualisetp += $cat["SUM(pain_tranche.tp)"];
	    $mutualisealt += $cat["SUM(pain_tranche.alt)"];
	    break;
	case 23: /* enseignant 'libre' */
	    $libre += $cat["SUM(htd)"];
	    $librecm += $cat["SUM(pain_tranche.cm)"];
	    $libretd += $cat["SUM(pain_tranche.td)"];
	    $libretp += $cat["SUM(pain_tranche.tp)"];
	    $librealt += $cat["SUM(pain_tranche.alt)"];
	    break;
	case 29: /* enseignant 'autre' (exterieur) */
	    $autre += $cat["SUM(htd)"];
	    $autrecm += $cat["SUM(pain_tranche.cm)"];
	    $autretd += $cat["SUM(pain_tranche.td)"];
	    $autretp += $cat["SUM(pain_tranche.tp)"];
	    $autrealt += $cat["SUM(pain_tranche.alt)"];
	    break;
	default: /* tout le reste = exterieurs */
	    $ext += $cat["SUM(htd)"];
	    $extcm += $cat["SUM(pain_tranche.cm)"];
	    $exttd += $cat["SUM(pain_tranche.td)"];
	    $exttp += $cat["SUM(pain_tranche.tp)"];
	    $extalt += $cat["SUM(pain_tranche.alt)"];
	}
    }
    $servi = $ext + $autre + $perm + $nperm;
    return array("servi"=>$servi,
		 "libre"=>$libre,
		 "librecm"=>$librecm,
		 "libretd"=>$libretd,
		 "libretp"=>$libretp,
		 "librealt"=>$librealt,
		 "mutualise"=>$mutualise,
		 "mutualisecm"=>$mutualisecm,
		 "mutualisetd"=>$mutualisetd,
		 "mutualisetp"=>$mutualisetp,
		 "mutualisealt"=>$mutualisealt,
		 "permanents" => $perm,
		 "permcm"=>$permcm,
		 "permtd"=>$permtd,
		 "permtp"=>$permtp,
		 "permalt"=>$permalt,
		 "nonpermanents" => $nperm,
		 "npermcm"=>$npermcm,
		 "npermtd"=>$npermtd,
		 "npermtp"=>$npermtp,
		 "npermalt"=>$npermalt,
		 "exterieurs" =>$ext,
		 "extcm"=>$extcm,
		 "exttd"=>$exttd,
		 "exttp"=>$exttp,
		 "extalt"=>$extalt,
		 "autre" => $autre,
		 "autrecm"=>$autrecm,
		 "autretd"=>$autretd,
		 "autretp"=>$autretp,
		 "autrealt"=>$autrealt,
		 "total"=>$servi+$libre+$mutualise // ajouter $annule apres retour
	);
}

function htdtotaux($annee = NULL) {
    global $link;

    if ($annee == NULL) $annee = annee_courante();

    /* heures etudiants */
    $qetu = "SELECT SUM((coalesce(cm, 0) + coalesce(td, 0) + coalesce(tp, 0) + coalesce(alt,0)) * presents) as etu
             FROM pain_sformation, pain_formation, pain_cours
             WHERE  pain_formation.id_formation = pain_cours.id_formation
             AND pain_sformation.id_sformation = pain_formation.id_sformation
             AND annee_universitaire = $annee";
    $retu = $link->query($qetu)
	or die("erreur d'acces a la table tranche : $qetu erreur:".$link->error);
    $letu =$retu->fetch_assoc();
    $etu = $letu["etu"];
    if ($etu == "") {
	$etu = 0;
    }

    /* annulations */
    $qannule ='SELECT SUM(htd) FROM pain_sformation, pain_formation, pain_cours, pain_tranche WHERE pain_tranche.id_cours = pain_cours.id_cours AND pain_formation.id_formation = pain_cours.id_formation AND pain_sformation.id_sformation = pain_formation.id_sformation  AND annee_universitaire = '.$annee.' AND (pain_tranche.id_enseignant = 1 OR pain_cours.id_enseignant = 1)';
    $rannule = $link->query($qannule)
	or die("erreur d'acces aux tables : $qannule erreur:".$link->error);

    $annule = $rannule->fetch_assoc();
    $annule = $annule["SUM(htd)"];
    if ($annule == "") {
	$annule = 0;
    }

    /* tous CM, TD, TP, alt */
    $qcomp ='SELECT SUM(pain_tranche.cm) AS cm, SUM(pain_tranche.td) AS td, SUM(pain_tranche.tp) AS tp, SUM(pain_tranche.alt) AS alt FROM pain_sformation, pain_formation, pain_cours, pain_tranche WHERE pain_tranche.id_cours = pain_cours.id_cours AND pain_formation.id_formation = pain_cours.id_formation AND pain_sformation.id_sformation = pain_formation.id_sformation AND annee_universitaire = '.$annee;
    $rcomp = $link->query($qcomp)
	or die("erreur d'acces aux tables : $qcomp erreur:".$link->error);

    $comp = $rcomp->fetch_assoc();
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
    $qperm ='SELECT pain_service.categorie AS categorie, SUM(htd), SUM(pain_tranche.cm), SUM(pain_tranche.td), SUM(pain_tranche.tp), SUM(pain_tranche.alt) FROM pain_sformation, pain_formation, pain_cours, pain_tranche, pain_service WHERE pain_tranche.id_cours = pain_cours.id_cours AND pain_formation.id_formation = pain_cours.id_formation AND pain_sformation.id_sformation = pain_formation.id_sformation AND pain_sformation.annee_universitaire = '.$annee.' AND pain_tranche.id_enseignant = pain_service.id_enseignant AND pain_service.annee_universitaire = pain_sformation.annee_universitaire AND pain_cours.id_enseignant <> 1 GROUP BY pain_service.categorie';

    $rperm = $link->query($qperm)
	or die("erreur d'acces aux tables : $qperm erreur:".$link->error);

    $a = htd_cat2array($rperm);

    $a["total"] += $annule;
    $a["annule"] = $annule;
    $a["cm"] = $cm;
    $a["td"] = $td;
    $a["tp"] = $tp;
    $a["alt"] = $alt;
    $a["etu"] = $etu;

    return $a;
}

function htdsuper($id) {
    global $link;
    /* heures etudiants */
    $qetu = "SELECT SUM((coalesce(cm, 0) + coalesce(td, 0) + coalesce(tp, 0) + coalesce(alt,0)) * presents) as etu
             FROM pain_formation, pain_cours
             WHERE pain_formation.id_sformation  = $id
             AND pain_cours.id_formation = pain_formation.id_formation";
    $retu = $link->query($qetu)
	or die("erreur d'acces a la table tranche : $qetu erreur:".$link->error);
    $letu = $retu->fetch_assoc();
    $etu = $letu["etu"];
    if ($etu == "") {
	$etu = 0;
    }


    $qannule ="SELECT SUM(htd) FROM pain_formation, pain_cours, pain_tranche WHERE pain_tranche.id_cours = pain_cours.id_cours AND pain_formation.id_formation = pain_cours.id_formation AND pain_formation.id_sformation = $id AND (pain_tranche.id_enseignant = 1 OR pain_cours.id_enseignant = 1)";
    $rannule = $link->query($qannule)
	or die("erreur d'acces aux tables : $qannule erreur:".$link->error);

    $annule = $rannule->fetch_assoc();
    $annule = $annule["SUM(htd)"];
    if ($annule == "") {
	$annule = 0;
    }

    $qcomp ="SELECT SUM(pain_tranche.cm) AS cm, SUM(pain_tranche.td) AS td, SUM(pain_tranche.tp) AS tp, SUM(pain_tranche.alt) AS alt FROM pain_formation, pain_cours, pain_tranche WHERE pain_formation.id_sformation = $id AND pain_formation.id_formation = pain_cours.id_formation AND pain_tranche.id_cours = pain_cours.id_cours";
    $rcomp = $link->query($qcomp)
	or die("erreur d'acces aux tables : $qcomp erreur:".$link->error);

    $comp = $rcomp->fetch_assoc();
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
    $qperm ="SELECT pain_service.categorie AS categorie, SUM(htd), SUM(pain_tranche.cm), SUM(pain_tranche.td), SUM(pain_tranche.tp), SUM(pain_tranche.alt) FROM pain_sformation, pain_formation, pain_cours, pain_tranche, pain_service WHERE pain_tranche.id_cours = pain_cours.id_cours AND pain_formation.id_formation = pain_cours.id_formation AND pain_formation.id_sformation = $id AND pain_sformation.id_sformation = $id AND pain_tranche.id_enseignant = pain_service.id_enseignant AND pain_service.annee_universitaire = pain_sformation.annee_universitaire AND pain_cours.id_enseignant <> 1 GROUP BY pain_service.categorie";

    $rperm = $link->query($qperm)
	or die("erreur d'acces aux tables : $qperm erreur:".$link->error);

    $a = htd_cat2array($rperm);

    $a["total"] += $annule;
    $a["annule"] = $annule;
    $a["cm"] = $cm;
    $a["td"] = $td;
    $a["tp"] = $tp;
    $a["alt"] = $alt;
    $a["etu"] = $etu;

    return $a;
}

function htdformation($id) {
    global $link;
    /* heures etudiants */
    $qetu = "SELECT SUM((coalesce(cm, 0) + coalesce(td, 0) + coalesce(tp, 0) + coalesce(alt,0)) * presents) as etu
             FROM pain_cours WHERE id_formation = $id";
    $retu = $link->query($qetu)
	or die("erreur d'acces a la table tranche : $qetu erreur:".$link->error);
    $letu = $retu->fetch_assoc();
    $etu = $letu["etu"];
    if ($etu == "") {
	$etu = 0;
    }

/* TODO ATTENTION annuler une intervention dans un cours lui-même annulé doit faire que l'intervention est comptée deux fois dans le total des annulation, à vérifier ! */
    $qannule = "SELECT SUM(htd) FROM pain_cours, pain_tranche WHERE pain_tranche.id_cours = pain_cours.id_cours AND id_formation = $id AND (pain_tranche.id_enseignant = 1 OR pain_cours.id_enseignant = 1)";
    $rannule = $link->query($qannule)
	or die("erreur d'acces a la table tranche : $qannule erreur:".$link->error);

    $annule = $rannule->fetch_assoc();
    $annule = $annule["SUM(htd)"];
    if ($annule == "") {
	$annule = 0;
    }

    $qcomp ="SELECT SUM(pain_tranche.cm) AS cm, SUM(pain_tranche.td) AS td, SUM(pain_tranche.tp) AS tp, SUM(pain_tranche.alt) AS alt FROM pain_cours, pain_tranche WHERE pain_cours.id_formation = $id AND pain_tranche.id_cours = pain_cours.id_cours";
    $rcomp = $link->query($qcomp)
	or die("erreur d'acces aux tables : $qcomp erreur:".$link->error);

    $comp = $rcomp->fetch_assoc();
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
    $qperm ="SELECT pain_service.categorie AS categorie, SUM(htd),  SUM(pain_tranche.cm), SUM(pain_tranche.td), SUM(pain_tranche.tp), SUM(pain_tranche.alt) FROM pain_sformation, pain_formation, pain_cours, pain_tranche, pain_service WHERE pain_cours.id_formation = $id AND pain_tranche.id_cours = pain_cours.id_cours AND pain_formation.id_formation = $id AND pain_sformation.id_sformation = pain_formation.id_sformation AND pain_tranche.id_enseignant = pain_service.id_enseignant AND pain_service.annee_universitaire = pain_sformation.annee_universitaire AND pain_cours.id_enseignant <> 1 GROUP BY pain_service.categorie";

    $rperm = $link->query($qperm)
	or die("erreur d'acces aux tables : $qperm erreur:".$link->error);

    $a = htd_cat2array($rperm);

    $a["total"] += $annule;
    $a["annule"] = $annule;
    $a["cm"] = $cm;
    $a["td"] = $td;
    $a["tp"] = $tp;
    $a["alt"] = $alt;
    $a["etu"] = $etu;

    return $a;
}


function htdcours($id) {
    global $link;
    /* heures etudiants */
    $qetu = "SELECT SUM((coalesce(cm, 0) + coalesce(td, 0) + coalesce(tp, 0) + coalesce(alt,0)) * presents) as etu
             FROM pain_cours WHERE id_cours = $id";
    $retu = $link->query($qetu)
	or die("erreur d'acces a la table tranche : $qetu erreur:".$link->error);
    $letu = $retu->fetch_assoc();
    $etu = $letu["etu"];
    if ($etu == "") {
	$etu = 0;
    }

    $qannule = "SELECT SUM(htd) FROM pain_tranche WHERE pain_tranche.id_cours = $id AND pain_tranche.id_enseignant = 1";
    $rannule = $link->query($qannule)
	or die("erreur d'acces a la table tranche : $qannule erreur:".$link->error);

    $annule = $rannule->fetch_assoc();
    $annule = $annule["SUM(htd)"];
    if ($annule == "") {
	$annule = 0;
    }

    $qcomp ="SELECT SUM(pain_tranche.cm) AS cm, SUM(pain_tranche.td) AS td, SUM(pain_tranche.tp) AS tp, SUM(pain_tranche.alt) AS alt FROM pain_tranche WHERE pain_tranche.id_cours = $id";
    $rcomp = $link->query($qcomp)
	or die("erreur d'acces aux tables : $qcomp erreur:".$link->error);

    $comp = $rcomp->fetch_assoc();
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
    $qperm ="SELECT pain_service.categorie AS categorie, SUM(htd),  SUM(pain_tranche.cm), SUM(pain_tranche.td), SUM(pain_tranche.tp), SUM(pain_tranche.alt) FROM pain_sformation, pain_formation, pain_cours, pain_tranche, pain_service WHERE pain_cours.id_cours = $id AND pain_tranche.id_cours = pain_cours.id_cours AND pain_formation.id_formation = pain_cours.id_formation AND pain_sformation.id_sformation = pain_formation.id_sformation AND pain_tranche.id_enseignant = pain_service.id_enseignant AND pain_service.annee_universitaire = pain_sformation.annee_universitaire AND pain_cours.id_enseignant <> 1 GROUP BY pain_service.categorie";

    $rperm = $link->query($qperm)
	or die("erreur d'acces aux tables : $qperm erreur:".$link->error);

    $a = htd_cat2array($rperm);

    $a["total"] += $annule;
    $a["annule"] = $annule;
    $a["cm"] = $cm;
    $a["td"] = $td;
    $a["tp"] = $tp;
    $a["alt"] = $alt;
    $a["etu"] = $etu;

    return $a;
}

function ig_htdbarre($r) {
    $servi = $r["servi"];
    $mutualise = $r["mutualise"];
    $libre = $r["libre"];
    $annule = $r["annule"];
    $tp = $r["tp"];

    echo '<img class="imgbarre" src="act_barre.php?servi='.$servi.'&mutualise='.$mutualise.'&libre='.$libre.'&annule'.$annule.'" title="';
    ig_htd($r);
    echo '"/>';
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

function responsableducours($id) {
    global $link;
    $qresponsable = 'SELECT id_enseignant FROM pain_cours WHERE id_cours = '.$id;
    $rresponsable = $link->query($qresponsable)
	or die("erreur d'acces a la table cours : $qresponsable erreur:".$link->error);
    $responsable = $rresponsable->fetch_assoc();
    return $responsable["id_enseignant"];
}

function estintervenant($id_enseignant)
{
    global $link;
    $q = "SELECT 1 FROM pain_tranche WHERE id_enseignant = $id_enseignant LIMIT 1";
    $r = $link->query($q) or die("erreur estintervenant($id_enseignant): $q<br>mysql a repondu ".$link->error);
    return $r->num_rows;
}

function estresponsablecours($id_enseignant)
{
    global $link;
    $q = "SELECT 1 FROM pain_cours WHERE id_enseignant = $id_enseignant LIMIT 1";
    $r = $link->query($q) or die("erreur estresponsablecours($id_enseignant): $q<br>mysql a repondu ".$link->error);
    return $r->num_rows;
}

function estresponsableformation($id_enseignant)
{
    global $link;
    $q = "SELECT 1 FROM pain_formation WHERE id_enseignant = $id_enseignant LIMIT 1";
    $r = $link->query($q) or die("erreur estresponsableformation($id_enseignant): $q<br>mysql a repondu ".$link->error);
    return $r->num_rows;
}

function estresponsablesformation($id_enseignant)
{
    global $link;
    $q = "SELECT 1 FROM pain_sformation WHERE id_enseignant = $id_enseignant LIMIT 1";
    $r = $link->query($q) or die("erreur estresponsablesformation($id_enseignant): $q<br>mysql a repondu ".$link->error);
    return $r->num_rows;
}

function serviceestvide($id_enseignant, $an) {
    $res = listeinterventions($id_enseignant, $an);
    if ($res->fetch_array()) {
	return false;
    } else {
	return true;
    }
}

function listedeclarations($ids_enseignants, $an = NULL) {
    global $link;
    global $annee;
    if ($an == NULL) {
	if ($annee == NULL) {
	    $annee = annee_courante();
	}
	$an = $annee;
    }
    $query = "SELECT
pain_enseignant.login AS login,
pain_enseignant.prenom AS prenom,
pain_enseignant.nom AS nom,
pain_enseignant.id_enseignant AS id_enseignant,
pain_enseignant.statut AS statut,
pain_service.service_annuel AS service,
pain_enseignant.email AS email,
pain_formation.nom AS nom_formation,
pain_formation.annee_etude AS annee_etude,
pain_formation.parfum AS parfum,
pain_cours.semestre,
pain_cours.nom_cours,
pain_cours.code_geisha,
pain_cours.id_section,
pain_cours.id_section as section,
pain_cours.id_cours AS id_cours,
SUM(pain_tranche.cm) AS cm,
SUM(pain_tranche.td) AS td,
SUM(pain_tranche.tp) AS tp,
SUM(pain_tranche.alt) AS alt,
SUM(pain_tranche.htd) AS htd,
GROUP_CONCAT(pain_tranche.declarer ORDER BY pain_tranche.id_tranche SEPARATOR '; ') as declarer
FROM pain_tranche, pain_cours, pain_formation, pain_sformation, pain_enseignant, pain_service
WHERE pain_enseignant.id_enseignant IN (".$ids_enseignants.")
AND pain_sformation.annee_universitaire = $an
AND pain_service.annee_universitaire = pain_sformation.annee_universitaire
AND pain_service.id_enseignant = pain_enseignant.id_enseignant
AND pain_tranche.id_enseignant = pain_enseignant.id_enseignant
AND pain_cours.id_enseignant <> 1
AND pain_tranche.id_cours = pain_cours.id_cours
AND pain_cours.id_formation = pain_formation.id_formation
AND pain_formation.id_sformation = pain_sformation.id_sformation
GROUP BY id_cours, pain_enseignant.id_enseignant
ORDER BY pain_enseignant.nom ASC, pain_cours.semestre ASC, pain_formation.numero ASC, pain_cours.id_cours";

    ($result = $link->query($query)) or errmsg("Échec de la requête ".$query."\n".$link->error);

    return $result;
}

function listeinterventions($id_enseignant, $an = NULL) {
    global $link;
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
ORDER by alt ASC, pain_cours.semestre ASC, pain_formation.numero ASC, pain_cours.id_cours";
    ($result = $link->query($query)) or die("Échec de la requête ".$query."\n".$link->error);

    return $result;
}

function ig_legendeintervention() {
    echo '<th class="formation">formation</th>';
    echo '<th class="code_geisha">code UE</th>';
    echo '<th class="nom_cours">intitulé</th>';
    echo '<th class="semestre">Période</th>';
    echo '<th class="CM">CM</th>';
    echo '<th class="TD">TD</th>';
    echo '<th class="TP">TP</th>';
    echo '<th class="alt">alt.</th>';
    echo '<th class="HTD">htd</th>';
    echo '<th class="remarque">Remarque</th>';
    echo '<th class="groupe">Groupe</th>';
}

function ig_intervention($i) {
    $id = $i["id_tranche"];
    echo '<td class="formation">';
    debug_show_id($id);
    echo $i["nom"]." ".$i["annee_etude"]." ";
    echo $i["parfum"];
    echo '</td>';
    echo '<td class="code_geisha">';
    echo $i["code_geisha"];
    echo '</td>';
    echo '<td class="nom_cours">';
    echo $i["nom_cours"];
    echo '</td>';
    echo '<td class="semestre">';
    echo 'S'.$i["semestre"];
    echo '</td>';
    echo '<td class="CM">'.$i["cm"].'</td>';
    echo '<td class="TD">'.$i["td"].'</td>';
    echo '<td class="TP">'.$i["tp"].'</td>';
    echo '<td class="alt">'.$i["alt"].'</td>';
    echo '<td class="HTD">'.$i["htd"].'</td>';
    echo '<td class="remarque">'.$i["remarque"].'</td>';
    echo '<td class="groupe">'.$i["groupe"].'</td>';
}

function totauxinterventions($id_enseignant) {
    global $link;
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

    ($result = $link->query($query)) or die("Échec de la connexion à la base enseignant");
    $totaux = $result->fetch_array();
    return $totaux;
}

function ig_totauxinterventions($totaux) {
    echo '<th style="text-align:right;" colspan="4">';
    echo 'totaux';
    echo '</th>';
    echo '<td class="CM">'.$totaux["cm"].'</td>';
    echo '<td class="TD">'.$totaux["td"].'</td>';
    echo '<td class="TP">'.$totaux["tp"].'</td>';
    echo '<td class="alt">'.$totaux["alt"].'</td>';
    echo '<td class="HTD">'.$totaux["htd"].'</td>';
    echo '<th colspan="2"></th>';
}

/** Met à jour la table pain_service en recalculant les service_reel de l'année.

@param $id_ens si non NULL on ne recalcule que le service l'enseignant ayant cet id.
 */
function update_servicesreels($id_ens = NULL) {
    global $link;
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
	$qupdate .= " WHERE pain_service.annee_universitaire = ".$annee;
    if (NULL != $id_ens) {
	$qupdate .= " AND pain_service.id_enseignant = ".$id_ens;
    }
    $link->query($qupdate)
	or die("erreur update_servicesreels : $qupdate: ".$link->error);
}

/** Met à jour la table pain_service en recalculant les service_potentiel de l'année.

@param $id_ens si non NULL on ne recalcule que le service l'enseignant ayant cet id.
 */
function update_servicespotentiels($id_ens = NULL) {
    global $link;
    global $annee;
    if ($annee == NULL) $annee = annee_courante();
/* ne pas loguer */
    $qupdate = "update pain_service set
service_potentiel = (select
sum(greatest(ifnull(
(select sum(htd)
from pain_tranche
where pain_tranche.id_enseignant  = pain_service.id_enseignant
and pain_tranche.id_cours = tid.id_cours)
,0),ifnull(
(select sum(htd)
from pain_choix
where pain_choix.id_enseignant = pain_service.id_enseignant
and pain_choix.id_cours = tid.id_cours)
,0)))
from pain_cours as tid, pain_formation, pain_sformation
where pain_sformation.annee_universitaire = pain_service.annee_universitaire
and pain_formation.id_sformation = pain_sformation.id_sformation
and tid.id_formation = pain_formation.id_formation
)";
   $qupdate .= " WHERE pain_service.annee_universitaire = ".$annee;
    if (NULL != $id_ens) {
	$qupdate .= " AND pain_service.id_enseignant = ".$id_ens;
    }
    $link->query($qupdate)
	or die("erreur update_servicespotentiels : $qupdate: ".$link->error);
}

/** retourne la liste des enseignants appartennants à une catégorie.

@param $categorie l'identifiant d'une la catégorie.
 */
function liste_enseignantscategorie($categorie) {
    global $link;
    global $annee;
    if ($annee == NULL) $annee = annee_courante();
    $q = "SELECT pain_enseignant.id_enseignant AS id_enseignant,
                 nom,
                 prenom,
                 pain_service.service_annuel AS service,
                 pain_service.service_potentiel AS service_potentiel,
                 pain_service.service_reel AS service_reel
          FROM pain_enseignant, pain_service
          WHERE pain_service.categorie = $categorie
            AND pain_enseignant.id_enseignant > 9
            AND pain_service.id_enseignant = pain_enseignant.id_enseignant
            AND pain_service.annee_universitaire = $annee
          ORDER by nom,prenom ASC";
    ($r = $link->query($q)) or die("erreur liste_enseignantscategorie : $q: ".$link->error);
    return $r;
}


function historique_par_cmp($type, $before, $after) {
    global $link;
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
    $link->query($q) or die("$q ".$link->error);
    pain_log($q);
}

function historique_par_ajout($type, $new) {
    global $link;
    global $user;
    $id = 0;
    $id_formation = 0;
    $id_cours = 0;
    /* /todo élimier race condition en récupérant le timestamp réel
     $timestamp = $new["modification"];
    */
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
    } else if (4 == $type) {
	$id = $new["id_enseignant"];
	$id_cours = $new["categorie"]; // TODO revoir la structure BD
	$id_formation = $new["categorie"];
    } else {
	$s .= ' BUG ';
    }
    $s .= "création";
    $s .= '</div>';
    $q = "INSERT INTO pain_hist
          (type, id, id_formation, id_cours, message, timestamp)
          VALUES ('".$type."', '".$id."', '".$id_formation."',
                  '".$id_cours."', '".$s."', NOW())";
    $link->query($q) or die("$q ".$link->error);
    pain_log($q);
}

function historique_de_formation($id, $offset, $timestamp = NULL) {
    global $link;
    $q = "SELECT * from pain_hist
          WHERE id_formation = $id
          AND (type = 1 OR type = 2 OR type = 3)";
    if ($timestamp != NULL) {
	$q .= " AND timestamp <= \"$timestamp\" ";
    }
    $q .= "ORDER BY timestamp DESC LIMIT ".($offset).", 20";
    $r = $link->query($q)
	or die("historique_de_formation($id), $q ".$link->error);
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