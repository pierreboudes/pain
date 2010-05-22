/* -*- coding: utf-8 -*-*/
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

/* BLOC ---- CONSTRUCTION DE L'OBJET LIGNE --------------*/
/*
bd : ligne_bd, tableau associatif (objet js) colonne => val.
html : ligne de tableau contenant des cellules (td)
js : objet cellule de nom la classe du td, qui fait le lien entre la bd et le tableau.
Nom de cellule (td, js) et nom de colonne coincident sauf quelques cas particuliers.
setval(cellule_jquery, ligne_bd) : insere la valeur bd dans le html 
edit(cellule_jquery) passe la cellule html en mode edition par l'utilisateur
getval(cellule_jquery, ligne_bd) insere la valeur html dans la ligne bd

-Cas particuliers :
1) cellule: enseignant | colonnes: nom prenom id | choix utilisateur: liste
2) cellule: timestamp
*/

var colcours = 18;

/* constructeur de cellule */
function cell() {
    this.name ="cell";
    this.mutable = true;

    /* passer la cellule en mode edition */
    this.edit = function (c) {
	c.wrapInner("<textarea />");
	c.addClass("edit");
    }

    /* recuperer la valeur de la cellule (en mode edition) */
    this.getval = function (c, o) {
	var s;
	if (c.hasClass("edit")) {
	    s = c.find('textarea').val();
	} else {
	    s = c.text();
	}
	o[this.name] = s;
    }
    /* fixer la valeur de la cellule (fais un retour mode normal) n'ajoute pas 'mutable' */
    this.setval = function (c, o) {
	c.removeClass("edit");
	c.html(o[this.name]);
    }
    /* rajoute mutable */
    this.showmutable = function (c) {
	if(!c.hasClass("mutable")) {
	    c.addClass("mutable");
	}
    }
}

function numcell() {
    this.edit = function(c) {
	var s = c.text();
	c.html('<input type="text" value="'+s+'"/>');
	c.addClass('edit');
    }
    this.getval = function (c, o) {
	var s;
	if (c.hasClass("edit")) {
	    s = c.find('input').val();
	} else {
	    s = c.text();
	}
	
	o[this.name] = s.replace(" ","").replace(",",".");
    }
}
numcell.prototype = new cell();

function datecell() {
    this.setval = function (c, o) {
	var isos = o[this.name];
	c.removeClass("edit");
	if ((isos != null) && (isos.length > 0) && (isos != "1970-01-01")) {
	    var s = $.datepicker.formatDate('dd/mm/yy',new Date(isos));  // <--conversion
	    c.html(s);
	} else {
	    c.html('');
	}
    }
    this.edit = function(c) {
	var s = c.text();
	c.html('');
	var dp = jQuery('<input type="text"/>');
	c.append(dp);
	dp.datepicker($.datepicker.regional['fr']);
	if ((s != null) && (s.length > 0) && (s != "1970-01-01")) {
	    dp.datepicker("setDate",s);
	}
	c.addClass('edit');
    }
    this.getval = function (c, o) {
	var s;
	var isos = "1970-01-01";
	if (c.hasClass("edit")) {
	    var dp = c.children('input');
	    s = dp.datepicker("getDate");
	} else {
	    s = new Date(c.text());
	}
	if (s != null) {
	    isos = $.datepicker.formatDate('yy-mm-dd', s); // <- conversion
	} 
	o[this.name] = isos;

    }
}
datecell.prototype = new cell();

/* constructeur de cellule non modifiable */
function immutcell () {
    this.mutable = false;
    this.edit = function (c) {};
    this.showmutable = this.edit;
}
immutcell.prototype = new cell();

/* constructeur de cellule non modifiable et sans valeur */
function notcell () {
    this.getval = function (c, o) {};
    this.setval = function (c, o) {};
}
notcell.prototype = new immutcell();

/* constructeur du composite enseignant */
function enseignant () {
    this.name = "enseignant";
    this.mutable = true;
    this.edit = function (c) {
	/* sauvegarder l'id actuel */
	var ensid = c.find('.hiddenvalue').text(); 
	// TODO refaire avec value au lien de span hidden
	c.remove('.hiddenvalue');
	var ensname = $.trim(c.find('a').text());
	/* installer la zone d'input */
	c.html('<input type="text" value="'+ensname+'"/><span class="hiddenvalue">'+ensid+'</span>');
	/* charger une seule fois la liste des enseignants */	
	/* mettre en place l'autocomplete */
	var ens = c.find("input");
	getjson("json_enseignants.php",{term: ""}, function (data) {
		ens.autocomplete({ minLength: 2,
			    source: data,
			    mustMatch: true,
			    selectFirst: true,
			    autoFill: true,
			    select: function(e, ui) {
			    if (!ui.item) {
				// remove invalid value, as it didn't match anything
				$(this).val("");
				return false;
			    }
			    $(this).focus();
			    ens.val(ui.item.label);
			    c.find('.hiddenvalue').html(ui.item.id);
			} 
		    })});
	c.addClass("edit");
    };
    this.getval = function (c,o) {
	var ensid = c.find('.hiddenvalue').text();
//	var ensid = c.find('input').autocomplete( "widget" ).item.id;
	o["id_enseignant"] = ensid;
    }
    this.setval = function (c,o) {
	c.html('<a class="enseignant" href="service.php?id_enseignant='+o["id_enseignant"]+'">'+o["prenom_enseignant"]+" "+o["nom_enseignant"]+'</a><span class="hiddenvalue">'+o["id_enseignant"]+'</span>');
	c.find("a.enseignant").click(function(){window.open(this.href);return false;});
    }
}
enseignant.prototype = new cell();
/* constructeur du composite intitule de formation */
function intitule() {
    this.name = "intitule";
    this.setval = function (c,o) {
	var s;
	s = o["nom"]+' '+o['annee_etude'];
	if (o["parfum"] != null) s = s+' '+o["parfum"];
	c.text(s);
    }
}
intitule.prototype = new immutcell();

/* constructeur du composite totaux */
function totaux() {
    this.setval = function (c,o) {
	c.text('attente de données');
	getjson("json_totauxformation.php",{id_formation: o["id_formation"]},function (o) {
		var s;
		s = htdpostes(o["total"]) + ' postes (dont '+htdpostes(o["tp"])+'&nbsp;TP) = '
                    +htdpostes(o["servi"])+'&nbsp;servis +&nbsp;'
                    +htdpostes(o["mutualise"])+'&nbsp;mutualisés +&nbsp;'
                    +htdpostes(o["libre"])+'&nbsp;à pourvoir +&nbsp;'
                    +htdpostes(o["annule"])+'&nbsp;annulés';
		c.html(s);
	    });
    }
    this.name = "totaux";
}
totaux.prototype = new immutcell();




/* constructeur du composite nature de l'intervention */
function nature() {
    this.setval = function (c,o) {
	var s;
	c.html('<table class="nature"><tr><td class="ncm">CM</td><td class="nalt">alt</td></tr><tr><td class="ntd">TD</td><td class="ntp">TP</td></tr></table>');
	c.find("table.nature td").addClass("inact");
	if (o["cm"] > 0) c.find("td.ncm").removeClass("inact");
	if (o["alt"] > 0) c.find("td.nalt").removeClass("inact");
	if (o["td"] > 0) c.find("td.ntd").removeClass("inact");
	if (o["tp"] > 0) c.find("td.ntp").removeClass("inact");
    }
    this.name = "nature";
}
nature.prototype = new immutcell();


/* objet ligne de tableau */
function ligne() {
    /* pain_enseignant 
     */
    
    /* composite : enseignant */
    this.enseignant = new enseignant();
    
    /* nom */
    this.nom = new cell();
    this.nom.name = "name";
    /* prenom */
    this.prenom = new cell();
    this.prenom.name = "surname";
    /* statut */
    this.statut = new cell();
    this.statut.name = "statut";
    /* email */
    this.email = new cell();
    this.email.name = "email";
    /* telephone */
    this.tel = new cell();
    this.tel.name = "email";
    /* bureau */
    this.email = new cell();
    this.email.name = "email";
    /* service statutaire */
    this.service = new cell();
    this.service.name = "service";
    /* service reel */
    this.service_reel = new immutcell();
    this.service_reel.name = "service_reel";
    /* categorie */
    this.categorie = new immutcell();
    this.categorie.name = "categorie";
    /* peut stats */
    this.stats = new immutcell();
    this.stats.name = "stats";
    /* su: peut tout ;) */
    this.su = new immutcell();
    this.su.name = "su";
    /* modification */
    this.modification = new immutcell();
    this.modification.name = "modification";

    /* pain_cours 
     */
    /* semestre */
    this.semestre = new numcell();
    this.semestre.name = "semestre";
    /* nom_cours */
    this.nom_cours = new cell();
    this.nom_cours.name = "nom_cours";
    /* credits */
    this.credits = new numcell();
    this.credits.name = "credits";
    /* id_enseignant */
    this.id_enseignant = new cell();
    this.id_enseignant.name = "id_enseignant";
    /* cm */
    this.cm = new numcell();
    this.cm.name = "cm";
    /* td */
    this.td = new numcell();
    this.td.name = "td";
    /* tp */
    this.tp = new numcell();
    this.tp.name = "tp";
    /* alt */
    this.alt = new numcell();
    this.alt.name = "alt";
    /* debut */
    this.debut = new datecell();
    this.debut.name = "debut";
    /* fin */
    this.fin = new datecell();
    this.fin.name = "fin";
    /* mcc */
    this.mcc = new cell();
    this.mcc.name = "mcc";
    /* inscrits */
    this.inscrits = new numcell();
    this.inscrits.name = "inscrits";
    /* presents */
    this.presents = new numcell();
    this.presents.name = "presents";
    /* tirage */
    this.tirage = new numcell();
    this.tirage.name = "tirage";
    /* descriptif */
    this.descriptif = new cell(); /* faire une big cell et une small cell */
    this.descriptif.name = "descriptif";
    /* code_geisha */
    this.code_geisha = new cell();
    this.code_geisha.name = "code_geisha";    
    /* action */
    this.action = new notcell();
    this.action.setval = function(c,o) {
	if (0 == c.find('div.palette').length) c.append('<div class="palette"/>');
    }
    this.action.name = "action";
    /* action a gauche */
    this.laction = new notcell();
    this.laction.setval = function(c,o) {
	if (0 == c.find('div.palette').length) c.append('<div class="palette"/>');
    }
    this.laction.name = "laction";    

    /* pain_tranche
     */
    /* nature */
    this.nature = new nature();
    
    /* id_cours */
    this.id_cours = new cell();    
    this.id_cours.name = "id_cours";
    /* groupe */
    this.groupe = new numcell();
    this.groupe.name = "groupe";
    /* type_conversion */
    this.type_conversion = new cell();    
    this.type_conversion.name = "type_conversion";
    /* remarque */
    this.remarque = new cell();    
    this.remarque.name = "remarque";
    /* htd */
    this.htd = new numcell();
    this.htd.name = "htd";
    /* descriptif */
    this.descriptif = new cell();
    this.descriptif.name = "descriptif";
    /* pain_choix
     */
    this.choix = new cell();
    this.choix.name = "choix";
    /* pain_formation
     */
    this.intitule = new intitule();
    this.totaux = new totaux();
}
/*--------  FIN OBJET LIGNE --------------*/

/* BLOC ----- UTILITAIRES ----- */
function htdpostes(htd) {
    return Math.round(htd*100/192)/100;
}

function edit() {
    if ($(this).hasClass("mutable")) {
	$(this).removeClass("mutable");
	var name = $(this).attr('class');
	L[name].edit($(this));
	addOk($(this));
    }
}

/* des id css aux couples type, id et ...*/
function parseIdString(s) {
    var tid = new Object();
    var tab = s.split('_',2);
    tid['type'] = tab[0];
    tid['id'] = tab[1];	
    return tid;
}

/* ...reciproquement */
function idString(o) {
    return o['type'] + '_' + o['id'];
}


/* Pour envoyer et recevoir au format json */
function getjson(url,data,callback) {
    $.ajax({ type: "GET",
		url: url,
		data:  data,
		datatype: 'json',
		error: function () {alert('erreur ajax !');},
		success: function(data) {
		try {
		    o = jQuery.parseJSON(data);
		    if (o.error != null) {
			// if (confirm()) ...
			alert(o.error);
			return;
		    }
		} catch (e) {
		    alert("Erreur: vous avez peut être été déconnecté du CAS, rechargez la page.\n"+data);
		    return;
		}
		callback(o);
	    }
	});
}
/* la meme version debug */
function getjsondb(url,data,callback) {
    $.ajax({ type: "GET",
	     url: url,
	     data:  data,
	     error: function () {alert('erreur ajax !');},
	     success: function(data) {
		alert("RECEIVED: " + data); 
		o = jQuery.parseJSON(data);
		if (o.error != null) {
		    // if (confirm()) ...
		    alert(o.error);
		} else {
		    callback(o);
		}
	    }
	});
}
/* ----- FIN UTILITAIRES ----- */


/* BLOC ---- BOUTONS ET ACTIONNEURS --------------*/

/* bloc --- Les bascules --- */
function basculerSuperFormation(id) {
    var bascule =  $('#basculesuper'+id);
    bascule.toggleClass('basculeOff');
    bascule.toggleClass('basculeOn');
    if (bascule.hasClass('basculeOff')) {
	$('#tablesuper_'+id+' > tbody > tr.formation div.basculeOn').trigger("click");
	$('#tablesuper_'+id+' tr.sousformations').fadeOut("slow").remove();
	$('#tablesuper_'+id+' tr.imgformation').fadeOut("slow").remove();
	$('#tablesuper_'+id+' tr.formation').fadeOut("slow").remove();
    } else {
	appendList("formation",$('#tablesuper_'+id+' > tbody'),id,
		   function () {
		       var legende = $('#legendeformation'+id);
		       legende.remove();
		       if ($('#imgsformation_'+id+' img').is(':visible')) {
			   $('#tablesuper_'+id+' > tbody div.imgformation').show();
			   $('#tablesuper_'+id+' > tbody tr.formation').each(function (i) {
				   var tag = this.id;
				   if (tag != undefined) {
				       var id = tag.replace('formation_','');
				       htdFormation(id);
				   }
			       });
		       }
		   });
    }
    return false;
}

function basculerFormation(e) {
    var id = e.data.id;
    var sid = idString({id: id, type: "formation"});
    var bascule =  $('#basculeformation_'+id);
    bascule.toggleClass('basculeOff');
    bascule.toggleClass('basculeOn');
    if (bascule.hasClass('basculeOff')) {
	$('#tablecours_'+id+' tr.cours div.basculeOn').trigger("click");
	$('#tablecours_'+id+' tr.cours').remove();
	$('#tablecours_'+id+' tr.imgcours').remove();
	var histo = $('#histoDesCoursFormation'+id);
	if (histo.hasClass('histoOn')) {
	    histo.toggleClass('histoOff');
	    histo.toggleClass('histoOn');
	}
	$('#trtablecours_'+id).remove();
    } else {
	$('#formation_'+id).after('<tr class="sousformations" id="trtablecours_'+id+'"><td colspan="4"><table class="cours" id="tablecours_'+id+'"><tbody></tbody></table></td></tr>');
	appendList("cours",$('#tablecours_'+id+' > tbody'),id, function(){
		var legende = $('#legendecours'+id);
		addMenuFields(legende);
		addAdd(legende.find('th.action'));
		$('#tablecours_'+id+' tr.cours').fadeIn("slow");
	    });
    }
    return false;
}

function basculerCours(e) {
    var id = e.data.id;
    var sid = idString({id: id, type: "cours"});
    var bascule =  $('#basculecours_'+id);
    bascule.toggleClass('basculeOff');
    bascule.toggleClass('basculeOn');
    if (bascule.hasClass('basculeOn')) {
	$('#'+sid).after('<tr id="trtabletranches'+id+'" class="trtranches"><td class="tranches" colspan="'+colcours+'"><table id="tabletranches_'+id+'" class="tranches"><tbody></tbody></table></td></tr>');
	appendList("tranche",$('#tabletranches_'+id+' tbody'),id, function () {
	var legende = $('#legendetranche'+id);
	addMenuFields(legende);
	addAdd(legende.find('th.action'));
	    });
    } else {
	$('#trtabletranches'+id).remove();
    }
    basculerChoix(e);
    return false;
}

function basculerChoix(e) {
   var id = e.data.id;
   $("#tabletranches_"+id).before('<div class="choix"><table class="choix" id="tablechoix_'+id+'"><tbody></tbody></table></div>');
   appendList("choix",$('#tablechoix_'+id+' tbody'),id, function(){});
   var legende = $('#legendechoix'+id);
   addMenuFields(legende);
   addAdd(legende.find('th.action'));
//   $('div.choix').resizable();
   /* positionner les actions */
   return false;
}
/* bloc --- fin des bascules --- */

/* bloc --- Boutons ---*/

/* duplication de ligne */
function addMult(td) {
    var tr = td.parent('tr');
    var oid = parseIdString(tr.attr('id'));
    var multl = jQuery('<button class="multl">Dupliquer '+oid["type"]+'</button>');
    multl.button({
	text: false,
		icons: {
	    primary: "ui-icon-copy" // ui-icon-cart
		    }
	});
    multl.bind("click",oid,duplicateLine);
    removeMult(td);
    td.find('div.palette').append(multl);    
}
function removeMult(td) {
    td.find('button.multl').remove();
}

/* caddy (choix) */
function addChoisir(td) {
    var tr = td.parent('tr');
    var oid = parseIdString(tr.attr('id'));
    var choixl = jQuery('<button class="choixl">Se proposer pour cette intervention</button>');
    choixl.button({
	text: false,
		icons: {
	    primary: "ui-icon-cart" // ui-icon-cart
		    }
	});
    choixl.bind("click",oid,selectLine);
    removeChoisir(td);
    td.find('div.palette').append(choixl);
}
function removeChoisir(td) {
    td.find('button.choixl').remove();
}


/* ajout de ligne */
function addAdd(td) {
    var type = td.closest('tr').attr('class');
    var addl = jQuery('<button class="addl">Ajouter '+type+'</button>');
    addl.button({
	text: false,
		icons: {
	    primary: "ui-icon-plus"
		    }
	});
    addl.bind("click",newLine);
    td.find('div.palette').append(addl);
}
function removeAdd(td) {
    td.find('button.addl').remove();
}

/* effacement de ligne */
function addRm(td) {
    var type = td.closest('tr').attr('class');
    var tr = td.parent('tr');
    var oid = parseIdString(tr.attr('id'));
    var rml = jQuery('<button class="rml">Effacer la ligne</button>');
    var removel = jQuery('<div class="removel"/>');
    rml.button({
	text: false,
		icons: {
	    primary: "ui-icon-trash"
		    }
	});
    rml.bind("click",oid,removeLine);
    removeRm(td);
    td.append(removel.append(rml));
}
function removeRm(td) {
    td.find('div.removel').remove();
}

/* reload de ligne */
function addReload(td) {
    var sid = td.closest('tr').attr('id');
    // alert(sid);
    var oid = parseIdString(sid);
    var reloadl = jQuery('<button class="reloadl">annuler les modifications</button>');
    reloadl.button({
	text: false,
		icons: {
//	    primary: "ui-icon-arrowrefresh-1-w"
	    primary: "ui-icon-cancel"
		    }
	});
    reloadl.bind("click",oid,refreshLine);
    td.find('div.palette').append(reloadl);
}
function removeReload(td) {
    td.find('button.reloadl').remove();
}

/* bouton d'envoi */
function addOk(jqcell) {
    var ligne = jqcell.parent('tr');
    var td = ligne.find('td.action');
    if (ligne.hasClass('edit')) {
	/* il y avait deja au moins une cellule en cours d'edition dans la ligne */
	ligne.find('div.ok').remove();
    } else {
	/* c'est la premiere cellule en cours d'edition dans la ligne. 
	   En cas de confirmation on peut envoyer toute la ligne */
	ligne.addClass('edit');
	jqcell.append('<div class="ok"/>');
	jqcell.find('div.ok').click(sendModifiedLine);
	removeReload(td);
	removeRm(td);
	removeMult(td);
	addReload(td); // <-- ajout du reload
	var okl = jQuery('<button class="okl">envoyer les modifications</button>');
	okl.button({
			text: false,
			icons: {
				primary: "ui-icon-check"
			}
	});
	okl.bind("click",sendModifiedLine);
	td.find('div.palette').append(okl);
    }
}

function addMenuFields(tr) {
    var th = tr.find('th.action');
    th.find('div.palette').prepend('<button class="menufields">champs...</button>');
    var button = th.find('button').button({
			text: false,
			icons: {
				primary: "ui-icon-triangle-1-s"
			}
	});
    button.one("click", {th: th, button: button},
	       openMenuFields);
    return false;
}
function removeMenuFields(td) {
    td.find('button.menufields').remove();
}

/*---- fin boutons ----*/

/*bloc--------histogrammes ---------*/
function addHistoGlobal(td) {
    var oid = parseIdString(td.parent().attr('id'));
    var annee = oid["id"];
    td.find("micropalette").remove();
    td.prepend( '<div class="micropalette"><div id="globalHisto_'+annee+'" class="globalHistoOff"></div></div>');
    $('#globalHisto_'+annee).bind("click",{annee: annee},histoDesFormations);
}

function histoDesFormations(e) {
    var annee = e.data.annee;
    var divan = $('#annee_'+annee);
    var bascule = $('#globalHisto_'+annee);
    bascule.toggleClass('globalHistoOff');
    bascule.toggleClass('globalHistoOn');
    if (bascule.hasClass('globalHistoOn')) {
	divan.find('div.imgformation').show();
	$('#annee_'+annee+' tr.formation').each(function (i) {
		var tag = this.id;
		if (tag != undefined) {
		    var id = tag.replace('formation_','');
		    htdFormation(id);
		}
	    });
	$('#annee_'+annee+' table.super').each(function (i) {
		var tag = this.id;
		if (tag != undefined) {
		    var id = tag.replace('tablesuper_','');
		    htdSuperFormation(id); // ok
		}
	    });
	htdTotaux(annee); // ok
    } else {
	$('#annee_'+annee+' div.imgformation').hide();
    }
    return false;
}


function htdTotaux(annee) {// OK pour le moment
    jQuery.post("act_totaux.php", {annee_universitaire: annee}, function (data) {
	    if (!contientERREUR(data)) {
		data = trim(data);
		$('#imgentete_'+annee).html(data);
		var totaux = $('#imgentete img').attr('title');
		$('#entete_'+annee+' td span.totaux').text(totaux);		
	    } else {
		$('#imgentete_'+annee).html('');
	    }
	}, 'html');
    return false;
}


function htdSuperFormation(id) {
    jQuery.post("act_totauxsuper.php", {id_sformation: id}, function (data) {
	    if (!contientERREUR(data)) {
        // DEBUG       alert('htdFormation('+id+') : data = '+data);
		data = trim(data);
		$('#imgsformation_'+id).html(data);
	    } else {
		$('#imgsformation_'+id).html('');
	    }
	}, 'html');
    return false;
}

/*---------fin histogrammes---------*/



/* ------- FIN BOUTONS ET ACTIONNEURS --------------*/

/*BLOC-------- MENU CONTEXTUEL DU CHOIX DES CHAMPS ---------*/
/*
addmenufields: positionne et arme le bouton de menu contextuel (bloc precedent)
openmenufields: construit et deroule le menu contextuel
closemenufields: detruit le menu (dont le contenu est dynamique)
togglecolumn: masque ou affiche une colonne puis detruit le menu.
 */
function openMenuFields(e) {
    var th = e.data.th;
    var list = th.siblings('th').not(th);
    var type = th.parent().parent().parent().attr('class');
    var button = e.data.button;
    var menu = jQuery('<ul class="menu"></ul>');
    var offset = button.offset();
    var h = (button.outerHeight) ? button.outerHeight() : button.height();
    menu.css({position: 'absolute', 
            'top': offset.top + h - 1, 'left': offset.left
		}).click(function(e) { e.stopPropagation(); }).show(200, function() {
			$(document).one('click', {button: button, menu: menu, th: th}, 
					closeMenuFields);
		    });
    var n = list.length;
    var i = 0;
    for (i = 0; i < n; i++) {
	var item = list.eq(i);
	var classname = item.attr("class");
        var titre = item.text();
	var visible = (item.css('display') != 'none');
	var blob = jQuery('<li>'+titre+'</li>');
	if (!visible) blob.addClass('inv');
	blob.one('click',
		 {type: type, visible: visible, 
			 css: classname, 
			 button: button, menu: menu, th: th},
		 toggleColumn);
	menu.append(blob);
    }
    $('body').append(menu);
//    th.find('select').selectmenu();
    return false;
}


function closeMenuFields(e) {
/*
    e.data.menu.removeClass('active').hide(100, function() {
            e.data.button.removeClass('active');
        });
*/
    e.data.menu.fadeOut('slow');
    e.data.menu.remove();
    e.data.button.one('click', {button: e.data.button, th: e.data.th}, openMenuFields);
    return false;
}

function toggleColumn(e) {
//    alert('visible = '+e.data.visible+', classname = '+e.data.css+', type = '+e.data.type);
    closeMenuFields(e);
    if (e.data.visible) {
	$('th.'+e.data.css+', td.'+e.data.css).fadeOut('slow');
    } else {
	$('th.'+e.data.css+', td.'+e.data.css).fadeIn('slow');
    }
    return false;
}
/* ---------- FIN MENU CONTEXTUEL DES CHAMPS ---------*/



/* BLOC ------- REMPLISSAGE DES TABLEAUX ---------*/
function  appendList(type,body,id_parent, do_it_last) {
    var legende = $("#skel"+type);
    var list = legende.children('th');
    /*  etait buggy !
    legende = legende.clone(true);
    legende.removeAttr('id');
    body.append(legende);
    */
    legende.clone(true).attr('id','legende'+type+id_parent).appendTo(body);
    legende = $('#legende'+type+id_parent);
    getjson("json_get.php",{id_parent: id_parent, type: type}, function (o) {
	    var n = o.length;
	    var i = 0;
	    for (i = n - 1; i >= 0; i--) {
		appendItem(type,legende,o[i],list);
	    }
	    do_it_last(); 
	});
}


function appendItem(type,prev,o,list) {
    var n = list.length;
    var i = 0;
    var id = idString({id: o["id_"+type], type: type});
    var line = jQuery('<tr id="'+id+'" class="'+type+'"></tr>');
    prev.after(line);
    for (i = 0; i < n; i++) {
	var name = list.eq(i).attr("class");
	var cell = jQuery('<td class="'+name+'"></td>');
	if (L[name] == null) alert('undefined in line: '+name);
	L[name].setval(cell, o);
	L[name].showmutable(cell);
	cell.dblclick(edit);
	line.append(cell);
	if (list.eq(i).css('display') == 'none') {
	    cell.fadeOut('fast');
	}
    }
    if (type == "cours") {
	line.children('td.laction')
	    .prepend('<div class="basculeOff" id="basculecours_'+o["id_cours"]+'" />')
	    .bind('click',{id: o["id_cours"]},basculerCours);
	line.before('<tr class="imgcours"><td class="imgcours" colspan="'+colcours+'"><div id="imgcours'+o["id_cours"]+'" class="imgcours"></div></td></tr>');
    }
    if (type == "formation") {
	var idf = o["id_formation"];
	/* histogrammes et logs */
	line.children('td.laction')
	    .prepend('<div class="micropalette"><div class="histoOff" id="histoDesCoursFormation'+idf+'"></div><div class="logOff" id="logsFormation'+idf+'"></div></div>');
	$('#histoDesCoursFormation'+idf).bind('click',{id: idf},histoDesCours);
	$('#logsFormation'+idf).bind('click',{id: idf},logsFormation);
	/* bascule de formation */
	line.children('td.laction')
	    .prepend('<div class="basculeOff" id="basculeformation_'+idf+'" />');
	$('#basculeformation_'+idf).bind('click',{id: idf},basculerFormation);
	/* */
	line.before('<tr class="imgformation"><td class="imgformation" colspan="'+colcours+'"><div id="imgformation'+idf +'" class="imgformation"></div></td></tr>');	
    } else {/* pas pour les formations */
	addRm(line.find('td.action')); 
    }
    if (type == "tranche") {
	addMult(line.children('td.action')); 
	if (o["id_enseignant"] == 3) {
	    addChoisir(line.children('td.laction')); 	    
	} else {
	    removeChoisir(line.children('td.laction'));
	}
    }
}
/* ------- FIN REMPLISSAGE DES TABLEAUX ---------*/

/* BLOC ----- ENVOI DE MODIFS AU SERVEUR --------*/
function findIdParent(tr,type) {/* ne fonctionnera pas avec le type formation */
    var table = tr.closest("table");
    var sid = table.attr("id");
    var oid = parseIdString(sid);
    return oid.id;
}

/* ajouter une nouvelle ligne sur le serveur et dans la vue */
function newLine() {
    var tr = $(this).closest('tr');
    var type = tr.attr('class');
    var list = tr.children('th');
    var n = list.length;
    var i = 0;
    var id_parent = findIdParent(tr,type);
    var data = new Object();
    data.type = type;
    data.id_parent = id_parent;
    if (type == "tranche") {
	data.id_enseignant = 3;
    }
    getjson("json_new.php", data, function (tabo) {
	    appendItem(type,tr,tabo[i],list);
	});
       
}

/* supprimer une ligne du serveur et de la vue */
function removeLine(o) {
    var oid = o.data;   
    var tr = $("#"+idString(oid));

    tr.find('div.basculeOn').trigger('click');
    tr.effect('highlight',{},800,function () {
	    if (confirm("Voulez vous vraiment effacer cette ligne ("+oid.type+") et les données associées ?\n Attention : cette opération est définitive.")) {
		getjson("json_rm.php", oid, function() {
			if (oid.type == 'cours') {
			    tr.prev('tr.imgcours').remove();
			}
			tr.fadeOut('slow');
			tr.remove();
		    });
	    }
	});
}

/* soumettre les modifications d'une ligne au serveur */
function sendModifiedLine() {
    var ligne = $(this).closest('tr');
    var parent = ligne.closest('table');
    var donnees = new Object();
    var tid = parseIdString(ligne.attr('id'));
    // alert(ligne.attr('id'));
    donnees['type'] = tid['type'];
    donnees['id'] = tid['id'];
    tid =  parseIdString(parent.attr('id'));
    donnees['id_parent'] = tid['id'];
    ligne.children('td.modification').each(function (i,e) {
	    L.modification.getval($(this),donnees);
	});

    ligne.children('td.edit').each(
	function () {
	    $(this).removeClass('edit');
	    var name = $(this).attr('class');
	    $(this).addClass('edit');
	    L[name].getval($(this),donnees);
	});

    getjson("json_modify.php",donnees, replaceLine);
}

/* rafraichir la vue sur une ligne avec les donnees fournie par le serveur */
function replaceLine(tabo) {
    var o = tabo[0];
    var id = idString(o);
    var ligne = $('#'+id);
    ligne.attr('class', o.type);
    ligne.children('td').not('td.action').not('td.laction').each(
	function () {
	    var td = $(this);
	    var nom;
	    td.removeClass('edit');
	    td.removeClass('mutable');
	    nom = td.attr('class');
	    L[nom].setval(td, o);
//	    if ("editable" in o) {
	    L[nom].showmutable(td);
//	    }
	});
    ligne.find('td.action button.okl').remove();
    removeReload(ligne.children('td.action'));
    addRm(ligne.children('td.action'));
    if (o["type"] == "tranche") {
	addMult(ligne.children('td.action')); 
	if (o["id_enseignant"] == 3) {
	    addChoisir(ligne.children('td.laction'));
	} else {
	    removeChoisir(ligne.children('td.laction'));
	}
    }
}


/* Dupliquer une ligne */
function duplicateLine(e) {
    var oid = e.data;
    var sid = idString(oid);
    var original = $('#'+sid);
    /* faut-il demander une confirmation ? */
    getjson("json_duplicate.php",oid,function (tabo) {	    
	    var o = tabo[0];
	    var legende = $("#skel"+o["type"]);
	    var list = legende.children('th');
	    appendItem(o["type"],original,o,list);
	});
}

/* Faire un choix d'intervention */
function selectLine(e) {
    var oid = e.data;
    var source = $('#'+idString(oid));
    source.effect('highlight',{},800,function () {});	   
    getjson("json_get.php",oid,function (tabo) {
	    var o = tabo[0];
	    o.type = "choix";
	    delete o.id;
	    delete o.id_tranche;
	    delete o.id_enseignant;
	    o.id_parent = o.id_cours;
	    getjson("json_new.php",o,function (tabo) {
		    var o = tabo[0];
		    var legende = $("#legendechoix"+o["id_cours"]);
		    var list = legende.children('th');
		    appendItem(o["type"],legende.siblings().andSelf().filter('tr:last'),o,list);
		});
	});
    return false;
}


/*------- FIN ENVOI DE MODIFS AU SERVEUR --------*/

/*  */

function refreshLine(o) {
    var oid = o.data;
    getjson("json_get.php",oid, replaceLine);
}

/* BLOC--- GESTION (AFFICHAGE) DE DROITS --------*/

/* ne fonctionne pas
function responsable_add(td,resp) {
    var oid = parseIdString(td.parent('tr').attr('id'));	    
    resp[oid["type"]] = td.html();
    resp.s = resp.s + oid["type"] + ": " + td.text() + "\n";
    if (oid["type"] == "tranche") {
	responsable_add(td.closest('tr > td > table').prev().prev().children('td.enseignant'),o);
    } else if (oid["type"] == "cours") {
	responsable_add(td.closest('tr.sousformation').prev().children('td.enseignant'),o);
    } else if  (oid["type"] == "formation") {
        responsable_add(td.closest('table').children('tr.super').children('td.enseignant'),o);
    }
}

function responsables(jq) {
    var resp = new Object();
    var sel;
    resp.s = 'responsables:\n';
    sel = jq.closest('tr').children('td.enseignant');
    responsable_add(sel, resp);
    alert(resp.s);
    return resp;
}
*/


/*------- FIN GESTION DE DROITS --------*/


$(document).ready(function () {
	$.datepicker.setDefaults($.datepicker.regional['fr']); 
	L = new ligne(); // <-- var globale

	/* infobox: liens externes */
	$("div.infobox a").click(function(){window.open(this.href);return false;});

	/* TEST */
	if (false) {/* bascules ... */
	    $('#basculesuper7').trigger('click');
	    window.setTimeout(function() {$('#basculeformation_17').trigger('click');}, 1000);
	    window.setTimeout(function() {$('#basculecours_156').trigger('click');}, 2000);	
            //	window.setTimeout(function() {responsables($('#tranche_375'));}, 3000);	
	}
	
});
