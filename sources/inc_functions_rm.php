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

/** @file inc_functions_rm.php
 Les fonctions utilisées par json_rm.php
 */
require_once('authentication.php'); 
authrequired();
require_once("utils.php");
require_once("inc_actions.php");
require_once("inc_droits.php");



/** supprimer une sformation et sa descendance, en informant les logs.
 */
function supprimer_sformation($id)
{
    global $link;
    if (!peutsupprimersformation($id)) {
	errmsg("droits insuffisants.");
    }

    pain_log("-- supprimer_sformation($id)");

    $q = "DELETE pain_choix FROM pain_choix, pain_cours, pain_formation 
          WHERE pain_formation.id_sformation = $id
          AND  pain_cours.id_formation = pain_formation.id_formation
          AND  pain_choix.id_cours = pain_cours.id_cours";
    if (!$link->query($q)) {
	errmsg("échec de la requête $q : ".$link->error());
    }
    pain_log($q);

    $q = "DELETE pain_tranche FROM pain_tranche, pain_cours, pain_formation
          WHERE pain_formation.id_sformation = $id
          AND  pain_cours.id_formation = pain_formation.id_formation
          AND  pain_tranche.id_cours = pain_cours.id_cours";
    if (!$link->query($q)) {
	errmsg("échec de la requête $q : ".$link->error());
    }
    pain_log($q);
    
    $q = "DELETE pain_collectionscours FROM pain_collectionscours, pain_cours, pain_formation 
          WHERE pain_formation.id_sformation = $id 
          AND pain_cours.id_formation = pain_formation.id_formation
          AND pain_collectionscours.id_cours = pain_cours.id_cours";
    if (!$link->query($q)) {
	errmsg("échec de la requête $q : ".$link->error());
    }
    pain_log($q);

    $q = "DELETE pain_tagscours FROM pain_tagscours, pain_cours, pain_formation 
          WHERE pain_formation.id_sformation = $id 
          AND pain_cours.id_formation = pain_formation.id_formation
          AND pain_tagscours.id_cours = pain_cours.id_cours";
    if (!$link->query($q)) {
	errmsg("échec de la requête $q : ".$link->error());
    }
    pain_log($q);

    $q = "DELETE pain_cours FROM pain_cours, pain_formation 
          WHERE pain_formation.id_sformation = $id
          AND  pain_cours.id_formation = pain_formation.id_formation";
    if (!$link->query($q)) {
	errmsg("échec de la requête $q : ".$link->error());
    }
    pain_log($q);

    $q = "DELETE pain_formation FROM pain_formation 
          WHERE pain_formation.id_sformation = $id";
    if (!$link->query($q)) {
	errmsg("échec de la requête $q : ".$link->error());
    }
    pain_log($q);

    /* collections */
    $q = "DELETE pain_collectionscours FROM pain_collection, pain_collectionscours 
          WHERE pain_collection.id_sformation = $id AND pain_collectionscours.id_collection = pain_collection.id_collection";
    if (!$link->query($q)) {
	errmsg("échec de la requête $q : ".$link->error());
    }
    pain_log($q);

    $q = "DELETE pain_collection FROM pain_collection 
          WHERE pain_collection.id_sformation = $id";
    if (!$link->query($q)) {
	errmsg("échec de la requête $q : ".$link->error());
    }
    pain_log($q);

    /* sformation */
    $q = "DELETE FROM pain_sformation WHERE `id_sformation` = $id LIMIT 1";
    if (!$link->query($q)) {
	errmsg("échec de la requête $q : ".$link->error());
    }
    pain_log($q);

    echo '{"ok": "ok"}';
}

/** supprimer une formation et sa descendance, en informant les logs.
 */
function supprimer_formation($id)
{
    global $link;
    if (!peutsupprimerformation($id)) {
	errmsg("droits insuffisants.");
    }

    pain_log("-- supprimer_formation($id)");

    $q = "DELETE pain_choix FROM pain_choix, pain_cours 
          WHERE pain_cours.id_formation = $id
          AND  pain_choix.id_cours = pain_cours.id_cours";
    if (!$link->query($q)) {
	errmsg("échec de la requête $q : ".$link->error());
    }
    pain_log($q);

    $q = "DELETE pain_tranche FROM pain_tranche, pain_cours 
          WHERE pain_cours.id_formation = $id
          AND  pain_tranche.id_cours = pain_cours.id_cours";
    if (!$link->query($q)) {
	errmsg("échec de la requête $q : ".$link->error());
    }
    pain_log($q);

    $q = "DELETE pain_cours FROM pain_cours 
          WHERE pain_cours.id_formation = $id ";
    if (!$link->query($q)) {
	errmsg("échec de la requête $q : ".$link->error());
    }
    pain_log($q);

    /* formation */
    $q = "DELETE FROM pain_formation WHERE `id_formation` = $id LIMIT 1";
    if (!$link->query($q)) {
	errmsg("échec de la requête $q : ".$link->error());
    }
    pain_log($q);

    echo '{"ok": "ok"}';
}


/** supprimer un cours et sa descendance, en informant les logs.
 */
function supprimer_cours($id)
{
    global $link;
    if (peuteditercours($id)) {
	$cours = selectionner_cours($id);

	$qcours = "DELETE FROM pain_cours WHERE `id_cours` = $id LIMIT 1";
	pain_log("-- supprimer_cours($id)");

        if ($link->query($qcours)) {
	    /* on efface les tranches associées */
	    pain_log("$qcours");
	    historique_par_suppression(1, $cours);

	    $qtranches = "DELETE FROM pain_tranche WHERE `id_cours` = $id";
	    
	    if ($link->query($qtranches)) {
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

/** supprimer un tag et ses associations à des cours, en informant les logs.
 */
function supprimer_tag($id)
{
    global $link;
    if (peuteditertag($id)) {
	$qtag = "DELETE FROM pain_tag WHERE `id_tag` = $id LIMIT 1";
	pain_log("-- supprimer_tag($id)");

        if ($link->query($qtag)) {
	    /* on efface les associations a des cours */
	    pain_log("$qtag");

	    $q = "DELETE FROM pain_tagscours WHERE `id_tag` = $id";
	    
	    if ($link->query($q)) {
		echo '{"ok": "ok"}';
		pain_log("$q");
	    } else {
		errmsg("échec de la requête sur la table tagscours.");
	    }
	} else {
	    errmsg("échec de la requête sur la table tag.");
	}
    } else {
	errmsg("droits insuffisants.");
    }
}

/** supprimer une association entre un tag et un cours, en informant les logs.
 */
function supprimer_tagcours($id, $id_par)
{
    global $link;
    if (peuteditercours($id_par)) {
	$qtag = "DELETE FROM pain_tagscours WHERE `id_tag` = $id AND `id_cours` = $id_par LIMIT 1";
	pain_log("-- supprimer_tagcours($id, $id_par)");

        if ($link->query($qtag)) {
		echo '{"ok": "ok"}';
		pain_log("$qtag");
	} else {
	    errmsg("échec de la requête sur la table tagscours. $qtag".$link->error());
	}
    } else {
	errmsg("droits insuffisants.");
    }
}

/** supprimer une collection et ses associations à des cours, en informant les logs.
 */
function supprimer_collection($id)
{
    global $link;
    if (peuteditercollection($id)) {
	$qcollection = "DELETE FROM pain_collection WHERE `id_collection` = $id LIMIT 1";
	pain_log("-- supprimer_collection($id)");

        if ($link->query($qcollection)) {
	    /* on efface les associations a des cours */
	    pain_log("$qcollection");

	    $q = "DELETE FROM pain_collectionscours WHERE `id_collection` = $id";
	    
	    if ($link->query($q)) {
		echo '{"ok": "ok"}';
		pain_log("$q");
	    } else {
		errmsg("échec de la requête sur la table collectionscours.");
	    }
	} else {
	    errmsg("échec de la requête sur la table collection.");
	}
    } else {
	errmsg("droits insuffisants.");
    }
}

/** supprimer une association entre une collection et un cours, en informant les logs.
 */
function supprimer_collectioncours($id, $id_par)
{
    global $link; 
    if (peuteditercours($id_par)) {
	$qcollection = "DELETE FROM pain_collectionscours WHERE `id_collection` = $id AND `id_cours` = $id_par LIMIT 1";
	pain_log("-- supprimer_collectioncours($id, $id_par)");

        if ($link->query($qcollection)) {
		echo '{"ok": "ok"}';
		pain_log("$qcollection");
	} else {
	    errmsg("échec de la requête sur la table collectionscours. $qcollection".$link->error());
	}
    } else {
	errmsg("droits insuffisants.");
    }
}


/** supprimer une tranche, en informant les logs.
 */
function supprimer_tranche($id) 
{
    global $link;
    if (peuteditertranche($id)) {
	$tranche = selectionner_tranche($id);
	$qtranche = "DELETE FROM pain_tranche WHERE `id_tranche` = $id
                 LIMIT 1";
	
	if ($link->query($qtranche)) {
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


/** supprimer un enseignant, en informant les logs.
 */
function supprimer_enseignant($id) {
    global $link;
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
	
	if ($link->query($qens)) {
	    historique_par_suppression(4, $ens);
	    pain_log("$qens -- supprimer_ens($id)");
	    $q = "DELETE FROM pain_service WHERE `id_enseignant` = $id";
	    $link->query($q) or ($q .= " -- ".$link->error());
	    pain_log("$q");
	    $q = "DELETE FROM pain_choix WHERE `id_enseignant` = $id";
	    $link->query($q) or ($q .= " -- ".$link->error());
	    pain_log("$q");
	    echo '{"ok": "ok"}';
	} else {
	    errmsg("échec de la requête sur la table enseignant.");
	}
    } else {
	errmsg("droits insuffisants.");
    }
}

/** supprimer un choix, en informant les logs.
 */
function supprimer_choix($id) {
    global $link;
    if (peutsupprimerchoix($id)) {
	$choix = selectionner_choix($id);
	$qchoix = "DELETE FROM pain_choix WHERE `id_choix` = $id
                 LIMIT 1";
	
	if ($link->query($qchoix)) {
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

/** supprimer un service d'un enseignant dans une année, en informant les logs.
 */
function supprimer_service($id_enseignant, $an) {
    global $link;
    if (peutsupprimerservice($id_enseignant, $an)) { 
	if (serviceestvide($id_enseignant, $an)) {	    
	    $qservice = "DELETE FROM pain_service WHERE `id_enseignant` = $id_enseignant AND `annee_universitaire` = $an LIMIT 1";
	
	    if ($link->query($qservice)) {
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

/** informer l'historique (table pain_hist) de la suppression d'un élément.
 */
function historique_par_suppression($type, $old) {
    global $link;
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
    $link->query($q) or die("$q ".$link->error());
    pain_log($q);
}
?>