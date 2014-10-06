<?php /* -*- coding: utf-8 -*-*/
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

// $user = no_auth(); /* pas d'authentification */
$user = weak_auth(); /* accès sans autorisation */
$annee = get_and_set_annee_menu();

$query = "SELECT *
FROM
((
SELECT sf.nom as nom_sformation,
sf.id_sformation as id_sformation,
pain_formation.nom as nom_formation,
pain_formation.annee_etude as annee_etude,
pain_formation.parfum as parfum,
pain_formation.id_formation as id_formation,
pain_formation.code_etape_formation as code_etape_formation,
pain_cours.nom_cours as nom_cours,
pain_cours.id_cours as id_cours,
pain_cours.code_etape_cours as code_etape_cours,
pain_cours.code_ue as code_ue,
pain_cours.id_enseignant as id_enseignant_cours,
IF (pain_cours.id_enseignant = 1, 1, pain_tranche.id_enseignant) as id_enseignant,
pain_tranche.htd as htd
FROM
((((SELECT * FROM pain_sformation WHERE annee_universitaire = ".$annee.") as sf
JOIN (pain_formation) USING (id_sformation))
JOIN (pain_cours) USING (id_formation))
JOIN (pain_tranche) USING (id_cours))
WHERE 1
) as c
JOIN
(SELECT id_enseignant, categorie as id_categorie
FROM pain_service
WHERE annee_universitaire = ".$annee."
) as s
USING (id_enseignant))
JOIN (SELECT id_categorie, nom_court FROM pain_categorie WHERE 1) as cat USING (id_categorie)
WHERE 1";


$res = $link->query($query) or die("BD Impossible d'effectuer la requête: $query");


$tab = Array();
while ($line = $res->fetch_array(MYSQLI_ASSOC)) {
    $tab[] = $line;
}

/**
 * Prend en entrée un tableau t de tableaux associatifs, tous sur le
 * même format et donne en sortie une arborescence dont les feuilles
 * sont les lignes de t et dont les branchements se font sur
 * certains attributs dans l'ordre donné dans la liste fournie en argument.
 */
function to_forest($arr,$list, $aux = Array()) {

    $result = Array();

    if (count($aux) > 0) {
        $attr = $aux[0];
    }

    /* cas terminal */
    if (count($list) == 0) {
        /* nous sommes aux feuilles */
        if (isset($attr)) {/* il faut seulement filtrer  les
                            * attributs */
            $res = array();
            foreach ($arr as $leaf) {
                $item = array();
                foreach ($attr as $auxkey) {
                    $item[$auxkey] = $leaf[$auxkey];
                }
                $res[] = $item;
            }
            return $res;
        } else { /* rien à filtrer */
            return $arr;
        }
    }

    /* traiter au moins un niveau */
    $key = $list[0];
    $recursion = (count($list) > 1);
    $newlist = array_slice($list, 1);
    $newaux = array_slice($aux, 1);

    $very_beginning = true;

    while ( $line = array_pop($arr)) {
        if ( $very_beginning ) { /* nouveau noeud */
            $current_node = Array();
            $current_key = $line[$key];
            $very_beginning = false;
        }
        else if ($line[$key] != $current_key) {/* cette ligne ira dans
                                                * le noeud suivant */
            /* collectons le noeud courant */
            $item = Array("key" => $key);
            /* collecte des attributs auxilliaires du noeud */
            if (isset($attr)) {
                foreach ($attr as $auxkey) {
                    $item[$auxkey] = $current_node[0][$auxkey];
                }
            }
            /* collecte du sous-arbre ou des feuilles */
            if ($recursion) {
                $item["subtree"] = to_forest($current_node, $newlist, $newaux);
            } else {
                $item["leaves"] = to_forest($current_node,$newlist, $newaux);
            }
            $result[] = $item;
            $current_node = Array();
            $current_key = $line[$key];
        }
        /* ajout au noeud courant */
        $current_node[] = $line;
    }
    if ( $very_beginning ) {
        return $result;
    }
    /* collectons le dernier noeud */
    $item = Array("key" => $key);
    /* collecte des attributs auxilliaires du noeud */
    if (isset($attr)) {
        foreach ($attr as $auxkey) {
            $item[$auxkey] = $current_node[0][$auxkey];
        }
    }
    /* collecte du sous-arbre ou des feuilles */
    if ($recursion) {
        $item["subtree"] = to_forest($current_node, $newlist, $newaux);
    } else {
        $item["leaves"] = to_forest($current_node, $newlist, $newaux);
    }
    $result[] = $item;
    return $result;
}

function new_categorie_vect() {
    return array(
        2 => 0.,
        3 => 0.,
        4 => 0.,
        6 => 0.,
        5 => 0.,
        23 => 0.,
        1 => 0.,
        22 => 0.);
}

function compute_categories_sums($forest) {
    if (isset($forest["subtree"])) {
        $res = $forest; // copie de forest
        $res["subtree"] = array(); // dont on change le sous-arbre
        $v = new_categorie_vect();
        foreach($forest["subtree"] as $subtree) {
            $ressubtree = compute_categories_sums($subtree);
            foreach($ressubtree["sum"] as $categorie => $val) {
                $v[$categorie] += $val;
            }
            $res["subtree"][] = $ressubtree;
        }
        $res["sum"] = $v;
        return $res;
    }
    else if (isset($forest["leaves"])) {
        $res = $forest; // simple copie + vecteur à la racine
        $v = new_categorie_vect();
        foreach($forest["leaves"] as $leaf) {
            $categorie = $leaf["id_categorie"];
            $v[$categorie] += $leaf["htd"];
        }
        $res["sum"] = $v;
        return $res;
    }
    else  {
        $res = array();
        foreach ($forest as $tree) {
            $res[] = compute_categories_sums($tree);
        }
        return $res;
    }
}


$list = ["id_sformation",
         "id_formation",
         "id_cours"];
$data = [["id_sformation", "nom_sformation"],
         ["id_formation","nom_formation","annee_etude","parfum","code_etape_formation","id_formation"],
         ["id_cours", "nom_cours"],
         ["id_categorie","htd"]];
$forest = to_forest($tab, $list, $data);
$res = compute_categories_sums($forest);
print json_encode($res);
?>