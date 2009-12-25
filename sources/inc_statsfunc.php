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

function stats($valeur,$ou) {
 $qstat = 'SELECT '.$valeur.' FROM '.$ou;
    $rstat = mysql_query($qstat) 
	or die("erreur d'acces a la table : $qstat erreur:".mysql_error());
    
    $stat = mysql_fetch_assoc($rstat);
    $stat = $stat["$valeur"];
    if ($stat == "") {
	$stat = 0;
    }
    return $stat;
}

function statsenseignant1($id) {
    $q = "SELECT  sum(htd) AS h,
                  sum(pain_tranche.cm) AS cm, 
                  sum(pain_tranche.td) AS td,
                  sum(pain_tranche.tp) AS tp, 
                  sum(pain_tranche.alt) AS alt,
                  annee_etude,
                  id_sformation 
          FROM pain_tranche, pain_cours, pain_formation ";
    if ($id == 1) {
	$q .= "WHERE pain_cours.id_cours = pain_tranche.id_cours 
               AND   (pain_tranche.id_enseignant = 1
                      OR pain_cours.id_enseignant = 1) ";
    } else {
	$q .= "WHERE pain_tranche.id_enseignant = $id
                AND pain_cours.id_cours = pain_tranche.id_cours
	        AND pain_cours.id_enseignant <> 1 ";
    }
    $q .= "AND pain_formation.id_formation = pain_cours.id_formation 
          GROUP BY pain_formation.id_formation";
    $r = mysql_query($q) or die("statsenseignant1($id) : $q". mysql_error());
    $a = array();
    for ($i = 0; $i < 11; $i++) {
	$a[$i] = array("cm"=>0, "td"=>0, "tp"=>0, "alt"=>0);
    }
    while($l = mysql_fetch_array($r)) {
	switch($l["id_sformation"]) {
	case 1: 
	    $i = $l["annee_etude"];
	    break;
	case 2:
	    $i = $l["annee_etude"] + 3;
	    break;
	case 3:
	    $i = $l["annee_etude"] + 3;
	    break;
	case 5:
	    $i = $l["annee_etude"] + 5;
	    break;
	case 7:
	    $i = $l["annee_etude"] + 7;
	    break;
	default:
	    $i = 0;
	}
	$a[$i]["cm"] += $l["cm"];
	$a[$i]["td"] += $l["td"];	
	$a[$i]["tp"] += $l["tp"];	
	$a[$i]["alt"] += $l["alt"];	
    }
    return $a;
}

function statsenseignantresp($id) {
    $q = "SELECT  1 as type,
                  annee_etude,
                  id_sformation 
          FROM pain_formation 
          WHERE id_enseignant = $id
          UNION
          SELECT 2 as type,
                 annee_etude,
                 pain_sformation.id_sformation
          FROM pain_sformation, pain_formation
          WHERE pain_formation.id_sformation = pain_sformation.id_sformation
          AND pain_sformation.id_enseignant = $id";
    $r = mysql_query($q) or die("statsenseignantresp($id) : $q". mysql_error());
    $c = array();
    for ($i = 0; $i < 11; $i++) {
	$c[$i] = 0;
    }
    while($l = mysql_fetch_array($r)) {
	switch($l["id_sformation"]) {
	case 1: 
	    $i = $l["annee_etude"];
	    break;
	case 2:
	    $i = $l["annee_etude"] + 3;
	    break;
	case 3:
	    $i = $l["annee_etude"] + 3;
	    break;
	case 5:
	    $i = $l["annee_etude"] + 5;
	    break;
	case 7:
	    $i = $l["annee_etude"] + 7;
	    break;
	default:
	    $i = 0;
	}
	$c[$i] = $l["type"];
    }
    return $c;
}

function statsenseignant_semestre($id) {
    $q = "SELECT sum(pain_tranche.cm) AS cm, 
                 sum(pain_tranche.td) AS td,
                 sum(pain_tranche.tp) AS tp, 
                 sum(pain_tranche.alt) AS alt,
                 semestre
          FROM pain_tranche, pain_cours, pain_formation ";
    if ($id == 1) {
	$q .= "WHERE pain_cours.id_cours = pain_tranche.id_cours 
               AND   (pain_tranche.id_enseignant = 1
                      OR pain_cours.id_enseignant = 1) ";
    } else {
	$q .= "WHERE pain_tranche.id_enseignant = $id
                AND pain_cours.id_cours = pain_tranche.id_cours
	        AND pain_cours.id_enseignant <> 1 ";
    }
    $q .= " AND pain_formation.id_formation = pain_cours.id_formation 
            AND pain_formation.id_sformation <> 10
            GROUP BY pain_cours.semestre 
            ORDER BY pain_cours.semestre ASC";
    $r = mysql_query($q) or die(" statsenseignant_semestre($id) $q");
    $a = array();
    for ($i = 1; $i < 3; $i++) {
	$a[$i] = array("cm"=>0, "td"=>0, "tp"=>0, "alt"=>0);
    }
    while ($l = mysql_fetch_array($r)) {
	$a[$l["semestre"]] = $l;
    }
    return $a;
}

/*
function statsenseignant_nature($id) {
     $q = "SELECT sum(pain_tranche.cm) AS cm, 
                  sum(pain_tranche.td) AS td,
                  sum(pain_tranche.tp) AS tp, 
                  sum(pain_tranche.alt) AS alt
          FROM pain_tranche, pain_cours, pain_formation 
          WHERE pain_tranche.id_enseignant = $id
          AND pain_cours.id_cours = pain_tranche.id_cours 
          AND pain_formation.id_formation = pain_cours.id_formation 
          AND pain_formation.id_sformation <> 10";
    $r = mysql_query($q) or die(" statsenseignant_nature($id) ");
    $l = mysql_fetch_array($r);
    return $l;
}
*/

function ig_barrecmtdtpalt($a) {
    echo '<div class="barre cm" style="width: '.$a["cm"].'px;"></div>';
    echo '<div class="barre td" style="width: '.$a["td"].'px;"></div>';
    echo '<div class="barre tp" style="width: '.$a["tp"].'px;"></div>';
    echo '<div class="barre alt" style="width: '.$a["alt"].'px;"></div>';
    if ($a["cm"] > 1) {
	echo '<div class="barre ccm" style="width: '.($a["cm"]/2).'px;"></div>';
    }
}

function  ig_statsportrait($s, $a, $b, $c) {
    echo '<div class="statens">';
    echo '<center>';
    echo '<div class="statens1">';
    {
	echo '<div class="lstatens1">';
	for ($i = 5; $i > 0; $i--) {
	    echo '<div class="barrecont">';
	    ig_barrecmtdtpalt($a[$i]);	    
	    switch($c[$i]) {
	    case 1:
		$resp = " an";
		break;
	    case 2:
		$resp = " formation";
		break;
	    default:
		$resp = " ";
	    }
	    echo '<div class="legende'.$resp.'">';
	    if ($i > 3) {
		echo "M".($i - 3);
	    } else {
		echo "L$i";
	    }
	    echo '</div></div>';
	}
	echo '</div>';
	
	echo '<div class="rstatens1">';
	for ($i = 5; $i > 0; $i--) {
	    echo '<div class="barrecont">';
	    ig_barrecmtdtpalt($a[$i + 5]);
	    switch($c[$i+5]) {
	    case 1:
		$resp = " an";
		break;
	    case 2:
		$resp = " formation";
		break;
	    default:
		$resp = " ";
	    }
	    echo '<div class="legende'.$resp.'">';
	    if ($i > 2) {
		echo "I".($i - 2);
	    } else {
		echo "C$i";
	    }
	    echo '</div></div>';
	}
	echo '</div>';
    }
    echo '</div>'; // fin statens1
    echo '<div class="statens2 clear">';
    for ($i = 1; $i < 3; $i++) {
	echo '<div class="barrecont">';
	echo '<div class="legende">';
	echo "S$i";
	echo '</div>';
	ig_barrecmtdtpalt($b[$i]);
	echo '</div>';
    }
    echo '</div>'; // fin statens2
    echo '<div class="clear">';
    echo $s;
    echo '</div>';
    echo '<div class="barrecont">';
    echo '<div class="legende">';
    echo 'autres';
    echo '</div>';
    ig_barrecmtdtpalt($a[0]);
    echo '</center>';    
    echo '</div>';
}


function ig_statsenseignant($l) {
    $id = $l["id_enseignant"];
    $s =  $l["prenom"].' '.$l["nom"];
    $a = statsenseignant1($id);
    $b = statsenseignant_semestre($id);
    $c = statsenseignantresp($id);
    ig_statsportrait($s, $a, $b, $c);
}
?>