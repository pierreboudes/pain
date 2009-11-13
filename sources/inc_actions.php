<?php /* -*- coding: utf-8 -*- */

function action_nouveaucours($id_formation) {
    echo '<button type="button" id="buttonnouveaucours'.$id_formation.'" class="action" onclick="popFormCours($(this),'.$id_formation.');"'.(peuteditercoursdelaformation($id_formation)?'':' disabled="disabled"').'>nouveau</button>';
}


function action_histodesformations() {
 echo ' <div class="micropalette"><div id="globalHistoDesFormations" class="globalHistoOff" onclick="histoDesFormations()"></div></div>';
}

function action_histodescours($id_formation) {
 echo ' <div class="micropalette"><div class="histoOn" onclick="histoDesCours('.$id_formation.')"></div></div>';
}



function action_basculerformation($id_formation) {
    echo '<div class="basculeOn"';
    echo ' id="basculeformation'.$id_formation.'"'; 
    echo ' onclick="basculerFormation('.$id_formation.')">';   
    echo '</div>';
}

function action_basculercours($id_cours) {
    echo '<div class="basculeOff"';
    echo ' id="basculecours'.$id_cours.'"'; 
    echo ' onclick="basculerCours('.$id_cours.')">';
    echo '</div>';
}

function action_supprimercours($id_cours) {
    echo '<button type="button" id="boutonsupprimercours'.$id_cours.'" class="action" onclick="supprimerCours('.$id_cours.')"'.(peuteditercours($id_cours)?'':' disabled="disabled"').'>supprimer</button>';
}

function action_modifiercours($id_cours) {
    echo '<button type="button" id="boutonmodifiercours'.$id_cours.'" class="action" onclick="modifierCours('.$id_cours.')"'.(peuteditercours($id_cours)?'':' disabled="disabled"').'>modifier</button>';
}

function action_dblcmodifiercours($id_cours) {
    if (peuteditercours($id_cours)) {
	echo ' ondblclick="modifierCours('.$id_cours.')"';
    }
}

function action_annulerajoutercours($id_formation) {
    echo '<button type="button" onclick="annulerAjouterCours('.$id_formation.')">Annuler</button>';
}

function action_annulermodifiercours($id_cours) {
    echo '<button type="button" onclick="annulerModifierCours('.$id_cours.')">Annuler</button>';	
}

function action_supprimertranche($id_tranche) {
    echo '<button type="button" class="action" onclick="supprimerTranche('.$id_tranche.')"'.(peuteditertranche($id_tranche)?'':' disabled="disabled"').'>supprimer</button>';
}

function action_modifiertranche($id_tranche) {
    echo '<button type="button" class="action" id="boutonmodifiertranche'.$id_tranche.'" onclick="modifierTranche('.$id_tranche.')"'.(peuteditertranche($id_tranche)?'':' disabled="disabled"').'>modifier</button>';
}

function action_dblcmodifiertranche($id_tranche) {
    if (peuteditertranche($id_tranche)) {
	echo ' ondblclick="modifierTranche('.$id_tranche.')"';
    }
}

function action_annulermodifiertranche($id_cours) {
    echo '<button type="button" onclick="annulerModifierTranche('.$id_cours.')">Annuler</button>';
}



function action_envoyertrancheducours($id_cours, $id_tranche = NULL) {
    if ($id_tranche != NULL) {
	echo '<input type="submit" value="OK"'.(peuteditertranche($id_tranche)?'':' disabled="disabled"').'/>';
    } else {
	echo '<input type="submit" value="ajouter"'.(peuteditertrancheducours($id_cours)?'':' disabled="disabled"').'/>';	
    }
}
?>