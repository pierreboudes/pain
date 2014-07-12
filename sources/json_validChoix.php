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
require_once('authentication.php'); 
$user = authentication();
require_once("inc_connect.php");
require_once("utils.php");
require_once("inc_functions.php");
$annee = annee_courante();


if (! peuttoutfaire() ) {
	errmsg("désolé vous n'avez pas les droits nécessaires");
	echo '{"nok":"1"}';
} else {
	
	if (isset($_GET["id_choix"]) && isset($_GET["id_cours"])) {
		$id_cours=$_GET["id_cours"];
		$id_choix=$_GET["id_choix"];

		$reqChoix= "select * from pain_choix where id_choix=$id_choix";
		$resChoix=$link->query($reqChoix) or die("Échec de la requête ".$reqChoix);
		$cibleChoix=$resChoix->fetch_assoc();

		// recherche d'une tranche libre correspondante
		$req="SELECT * FROM pain_tranche WHERE id_cours=$id_cours 
			AND id_enseignant=3" ;
		// NULL pour éviter des 0 dans les cases : pas joli.
		if ($cibleChoix['cm'] !=0) 
			$req = $req . " AND cm=".$cibleChoix['cm'];
		else {
			$req = $req . " AND isnull(cm)";
			$cibleChoix['cm']="NULL";
		}
		if ($cibleChoix['td'] !=0)
			$req = $req . " AND td=".$cibleChoix['td'];
		else {
			$req = $req . " AND isnull(td)";
			$cibleChoix['td']="NULL";
		}
		if ($cibleChoix['tp'] !=0)
			$req = $req . " AND tp=".$cibleChoix['tp'];
		else {
			$req = $req . " AND isnull(tp)";
			$cibleChoix['tp']="NULL";
		}
		if ($cibleChoix['alt'] !=0)
			$req = $req . " AND alt=".$cibleChoix['alt'];
		else {
			$req = $req . " AND isnull(alt)";
			$cibleChoix['alt']="NULL";
		}
		if ($cibleChoix['ctd'] !=0)
			$req = $req . " AND ctd=".$cibleChoix['ctd'];
		else {
			$req = $req . " AND isnull(ctd)";
			$cibleChoix['ctd']="NULL";
		}

		//errmsg($req);
		
		$res=$link->query($req) or die("Échec de la requête ".$req);
		
		$cible=$res->fetch_assoc();
		if ($cible==NULL)  errmsg("Pas de tranche libre correspondante, désolé ");
		else {
			// on ajoute une ligne dans la table pain_tranche puis on supprime
			// celle qui a correspondu précédemment car mysql ne semble pas
			// aimer les update/select
		
			$req2="insert into pain_tranche 
				(id_cours, id_enseignant,cm,td,tp,alt,ctd,htd,groupe,modification) 
				VALUES ($id_cours,". $cibleChoix['id_enseignant'] .",".
				$cibleChoix['cm'].",".
				$cibleChoix['td'].",".
				$cibleChoix['tp'].",".
				$cibleChoix['alt'].",".
				$cibleChoix['ctd'].",".
				$cibleChoix['htd'].",".
				$cible['groupe'].",NOW())";
			// suppression de la liste des souhaits
			// et de la tranche libre corresp.
			$res2=$link->query($req2) or die ("Echec requête ".$req2);
			$req2="delete from pain_choix where id_choix=$id_choix";
			$res2=$link->query($req2) or die ("Echec requête ".$req2);
			$req2="delete from pain_tranche where id_tranche=".$cible['id_tranche'];
			$res2=$link->query($req2) or die ("Echec requête ".$req2);

			$cibleChoix['id_tranche']=$cible['id_tranche'];
			historique_par_cmp(2,$cible,$cibleChoix);

			// Bug qq part la dedans
			//historique_par_suppression(3,$cibleChoix);
			// ou encore supprimer_choix($cibleChoix['id_choix']);
			echo '{"ok":"ok"}';
		}
    }
}
?>
