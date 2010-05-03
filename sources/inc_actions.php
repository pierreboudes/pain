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
require_once('authentication.php'); 
authrequired();

function action_nouveaucours($id_formation) {
    echo '<button type="button" id="buttonnouveaucours'.$id_formation.'" class="action" onclick="popFormCours($(this),'.$id_formation.');"'.(peuteditercoursdelaformation($id_formation)?'':' disabled="disabled"').'>nouveau</button>';
}


function action_histodesformations() {
 echo ' <div class="micropalette"><div id="globalHistoDesFormations" class="globalHistoOff" onclick="histoDesFormations()"></div></div>';
}

function action_histodescours($id_formation) {
 echo ' <div class="micropalette"><div class="histoOff" id="histoDesCoursFormation'.$id_formation.'" onclick="histoDesCours('.$id_formation.')"></div><div class="logOff" id="logsFormation'.$id_formation.'" onclick="logsFormation('.$id_formation.')"></div></div>';
}

function action_basculersuper($id_sformation) {
    echo '<div class="basculeOff"';
    echo ' id="basculesuper'.$id_sformation.'"'; 
    echo ' onclick="basculerSuperFormation('.$id_sformation.')">';   
    echo '</div>';
}

function action_basculerformation($id_formation) {
    echo '<div class="basculeOff"';
    echo ' id="basculeformation_'.$id_formation.'"'; 
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
    echo '<button type="button" id="boutonsupprimercours'.$id_cours.'" class="action" onclick="supprimerCours('.$id_cours.')"'.(peutediterformationducours($id_cours)?'':' disabled="disabled"').'>supprimer</button>';
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