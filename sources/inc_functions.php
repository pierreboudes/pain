<?php

function postclean($s) {
    if(get_magic_quotes_gpc()) {
	return trim(htmlspecialchars(mysql_real_escape_string(stripslashes(($_POST[$s]))), ENT_QUOTES));
    }
    else {
	return trim(htmlspecialchars(mysql_real_escape_string($_POST[$s]), ENT_QUOTES));
    }
}

function postnumclean($s) {
    return str_replace(',','.',str_replace(' ', '',postclean($s)));
}

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
	echo "inconnu";
    }
}

function ig_responsable($id)
{
    if ($id < 0) {
	echo "libre";
    }
    if ($id == 0) {
	echo "autre";
    }
    if ($id > 0) {
	$qresponsable = "SELECT * FROM pain_enseignant WHERE `id_enseignant` = $id";
	$rresponsable = mysql_query($qresponsable) 
	    or die("Échec de la requête sur la table enseignant");
	$responsable = mysql_fetch_array($rresponsable);
	echo $responsable["prenom"]." ";
	echo $responsable["nom"]." ";
    }
}

function ig_formselectenseignants($id_enseignant)
{
   echo '<option value="-1"><i>libre</i></option>'; 
   $qens = "SELECT `id_enseignant`, `prenom`, `nom` 
            FROM pain_enseignant ORDER BY `nom`,`prenom` ASC";
   $rens = mysql_query($qens) 
                  or die("Échec de la requête sur la table enseignant");
   while ($ens = mysql_fetch_array($rens)) {
       echo '<option ';
       if ($ens["id_enseignant"] == $id_enseignant) echo 'selected ';
       echo  'value="'.$ens["id_enseignant"].'">';
       echo $ens["prenom"]." ";
       echo $ens["nom"]." ";
       echo '</option>';
   }
   echo '<option value="0"><i>autre</i></option>';
}


function list_formations()
{
    $qformation = "SELECT * FROM pain_formation 
                   WHERE `annee_universitaire` = 2009
                   ORDER BY numero ASC";    

    $rformation = mysql_query($qformation) 
	or die("Échec de la requête sur la table formation");

    return $rformation;
}

function list_cours($id)
{
      $qcours = "SELECT * FROM pain_cours WHERE `id_formation` = $id
                 ORDER BY semestre ASC";

    $rcours = mysql_query($qcours) or 
	die("Échec de la requête sur la table cours");

    return $rcours;
}


function selectionner_cours($id)
{
    $qcours = "SELECT * FROM pain_cours WHERE `id_cours` = $id";
    $cours = NULL;
    if ($rcours = mysql_query($qcours)) {
	$cours = mysql_fetch_array($rcours);
    } else {
	echo "Échec de la requête sur la table cours.";
    }
    return $cours;
}

function supprimer_cours($id)
{
      $qcours = "DELETE FROM pain_cours WHERE `id_cours` = $id
                 LIMIT 1";

    $droits = 1; /* TODO: tester si l'utilisateur a ce droit */
    
    if ($droits) {	
        if (mysql_query($qcours)) {
	    /* on efface les tranches associées */
	    $qtranches = "DELETE FROM pain_tranche WHERE `id_cours` = $id";	    
	    if (mysql_query($qtranches)) {
		echo "OK";
	    } else {
		echo "Échec de la requête sur la table tranches.";
	    }
	} else {
	    echo "Échec de la requête sur la table cours.";
	}
    } else {
	echo "Droits insuffisants.";
    }
}


function ig_cours($cours)
{
    $id = $cours["id_cours"];

    echo '<td class="id" id="cours'.$id.'"></td>';
    
    echo '<td class="nom_cours">';
    echo $cours["nom_cours"];
    echo '</td>';
    
    echo '<td class="semestre">';
    echo '<div class="basculeOff"';
    echo ' id="basculecours'.$id.'"'; 
    echo ' onclick="basculerCours('.$id.')">';
    echo '</div>';
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
    
    echo '<td class="action">';
    echo '<button type="button" id="boutonsupprimercours'.$id.'" class="action" onclick="supprimerCours('.$id.')">supprimer</button><br/>';
    echo '<button type="button" id="boutonmodifiercours'.$id.'" class="action" onclick="modifierCours($(this),'.$id.')">modifier</button><br/>';
    echo '</td>';
}

function ig_formcours($id_formation, $id_cours="", $nom_cours="", $semestre=0, $id_enseignant = NULL, $credits = "", $cm = "", $td="", $tp="", $alt="", $descriptif="", $code_geisha = "")
{
    $id = $id_cours;    
    echo '<table class="formcours"><tr>';
    echo '<td class="nom_cours">';
    echo '<input type="text" name="nom_cours" value="'.$nom_cours.' "/></td>';
    echo '<td class="semestre">';
    echo '<input value="1" '; 
    if (1 == $semestre) echo 'checked ';
    echo 'type="radio" class="semestre" name="semestre"/>1<br/>';
    echo '<input value="2" ';
    if (2 == $semestre) echo 'checked ';
    echo 'type="radio" class="semestre" name="semestre"/>2</td>';
    echo '<td class="credits"><input type="text" name="credits" value="'.$credits.'" /></td>';
    echo '<td class="responsable">';
    echo '<select name="responsable_cours">';
    echo '<option value=""><i>responsable</i></option>';
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
    echo '<textarea name="descriptif" rows="3" >'.$descriptif.'</textarea></td>';
    echo '<td class="code_geisha"><input type="text" name="code_geisha" value="'.$code_geisha.'" /></td>';
    if ($id) {/* modification de cours */
	echo '<td class="action" id="formmodifcours'.$id.'">';
        echo '<input type="submit" value="OK"/><br/>';
	echo '<button type="button" onclick="annulerModifierCours('.$id.')">Annuler</button>';	
    } else {
	echo '<td class="action"><input type="submit" value="OK"/><br/>';
	echo '<button type="button" onclick="annulerAjouterCours('.$id_formation.')">Annuler</button>';
    }
    echo '</td></tr></table>'."\n";    
}

function ig_legendetranches($id) {
    echo '<th class="groupe">Groupe</th>';
    echo '<th class="enseignant">Enseignant</th>';
    echo '<th class="cm">CM</th>';
    echo '<th class="td">TD</th>';
    echo '<th class="tp">TP</th>';
    echo '<th class="alt">alt.</th>';
    echo '<th class="type_conversion">conversion</th>';
    echo '<th class="htd">htd</th>';
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

function ig_tranche($t) {
    $id = $t["id_tranche"];
    echo '<tr class="tranche">';
    echo '<td class="groupe">'.$t["groupe"].'</td>';
    echo '<td class="enseignant">';
    echo ig_responsable($t["id_enseignant"]);
    echo '</td>';
    echo '<td class="CM">'.$t["cm"].'</td>';
    echo '<td class="TD">'.$t["td"].'</td>';
    echo '<td class="TP">'.$t["tp"].'</td>';
    echo '<td class="alt">'.$t["alt"].'</td>';
    echo '<td class="type_conversion">';
    ig_typeconversion($t["type_conversion"]);
    echo '</td>';
    echo '<td class="HTD">'.$t["htd"].'</td>';
    echo '<td class="remarque">'.$t["remarque"].'</td>';
    echo '<td class="action" id="tranche'.$id.'">';
    echo '<button type="button" class="action" onclick="supprimerTranche('.$id.')">';
    echo 'supprimer</button><br/>';
   /* echo '<button type="button" class="action" onclick="editerTranche('.$id.')">';
    echo 'modifier</button>'; */
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


function ig_formtranche($id_cours, $id_tranche = NULL, $cm = 0, $td= 0, $tp= 0, $alt= 0, $id_enseignant = -1, $groupe = 0, $type_conversion = 0, $remarque = "", $htd = 0)
{    
    echo '<tr class="formtranche">';
    echo '<td class="groupe">';
    echo '<input type="text" name="groupe" value="'.$groupe.'" />';
    echo '</td>';
    echo '<td class="enseignant">';
    echo '<select style="width:95%;" name="id_enseignant">';
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
    echo '<td class="type_conversion">'; 
    echo '<input value="0" '; 
    if (0 == $type_conversion) echo 'checked ';
    echo 'type="radio" class ="type_conversion" name="type_conversion" />auto<br/>';
    echo '<input value="1" ';
    if (1 == $type_conversion) echo 'checked ';
    echo 'type="radio" class ="type_conversion" name="type_conversion"/>manuel</td>';
    echo '</td>';
    echo '<td class="HTD">';
    echo '<input type="text" name="htd" value="'.$htd.'" />';
    echo '</td>';
    echo '<td class="remarque">';
    echo '<input type="hidden" name="id_cours" value="'.$id_cours.'"/>';
    if ($id_tranche != NULL) {
	echo '<input type="hidden" name="id_tranche" value="'.$id_tranche.'"/>';
    }
    echo '<textarea name="remarque" rows="2" >';
    echo $remarque;
    echo '</textarea></td>';
    echo '<td class="action">';
    echo '<input type="submit" value="OK"/>';
    echo '</td>';
    echo '</tr>';
}


function supprimer_tranche($id)
{
      $qtranche = "DELETE FROM pain_tranche WHERE `id_tranche` = $id
                 LIMIT 1";

    $droits = 1; /* TODO: tester si l'utilisateur a ce droit */
    
    if ($droits) {	
        if (mysql_query($qtranche)) {
	    echo "OK";
	} else {
	    echo "Échec de la requête sur la table cours.";
	}
    } else {
	echo "Droits insuffisants.";
    }
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
    echo '<td class="prenom">'.$t["prenom"].'</td>';
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

function ig_listenseignants() {
    $q = "SELECT * from pain_enseignant ORDER by nom,prenom ASC";
    ($r = mysql_query($q)) or die("Échec de la connexion à la base enseignant");
    while ($t = mysql_fetch_array($r))
    {
	ig_enseignant($t);
    }
}

function ig_formenseignant()
{
    echo '<tr class="enseignant">';
    echo '<td class="prenom"><input type="text" name="prenom" value="" /></td>';
    echo '<td class="nom"><input type="text" name="nom" value="" /></td>';
    echo '<td class="statut"><input type="text" name="statut" value="" /></td>';
    echo '<td class="email"><input type="text" name="email" value="" /></td>';
    echo '<td class="tel"><input type="text" name="telephone" value="" /></td>';
    echo '<td class="bureau"><input type="text" name="bureau" value="" /></td>';
    echo '<td class="service"><input type="text" name="service" value="192" /></td>';
    echo '<td>';
    echo '<input type="submit" value="Ajouter"/>';
    echo '</td>';
    echo '</tr>';
    echo "\n";
}

?>