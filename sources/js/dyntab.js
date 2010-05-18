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

/*
$(document).ready(function(){
	var s = "1 2,3";
	var reg=new RegExp(",", "g");
	var ns = s.replace(" ","").replace(reg,".");
	alert(s+' : '+ns);}
    );
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

$(document).ready(function () {$("#skelcours th:first + th").bind("click",
     function () {
	var legende = $("#skelcours");
	var list = legende.find('th');
	var n = list.length;
	var i;
	var s = "liste: ";
	for (i = 0; i < n; i++) {
	    var blob = list.eq(i);
            s = s + blob.attr("class") + ": " + blob.css('display') + '\n'; 
	}
	alert(s);
    }
	    )});

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
	c.text('TODO : getstats dynamique');
    }
}
totaux.prototype = new immutcell();

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
    this.laction.name = "laction";    

    /* pain_tranche
     */
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
	$('#tablesuper'+id+' > tbody > tr.formation div.basculeOn').trigger("click");
	$('#tablesuper'+id+' tr.sousformations').fadeOut("slow").remove();
	$('#tablesuper'+id+' tr.imgformation').fadeOut("slow").remove();
	$('#tablesuper'+id+' tr.formation').fadeOut("slow").remove();
    } else {
//	appendList("formation",$('#testing tbody'),id);
	appendList("formation",$('#tablesuper'+id+' > tbody'),id,
		   function () {
		       var legende = $('#legendeformation'+id);
		       legende.remove();
//	addMenuFields(legende);
//	addAdd(legende.find('th.action'));
//	$('#tablesuper'+id+' tr.imgformation').fadeIn("slow");
//	$('#tablesuper'+id+' tr.formation').fadeIn("slow");
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
	$('#'+sid).after('<tr id="trtabletranches'+id+'" class="trtranches"><td class="tranches" colspan="12"><table id="tabletranches_'+id+'" class="tranches"><tbody></tbody></table></td></tr>');
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
   appendList("choix",$('#tablechoix_'+id+' tbody'),id);
   var legende = $('#legendechoix'+id);
   addMenuFields(legende);
   addAdd(legende.find('th.action'));
//   $('div.choix').resizable();
   /* positionner les actions */
   return false;
}
/* bloc --- fin des bascules --- */

/* bloc --- Boutons ---*/
/* ajout de ligne */
function addAdd(td) {
    var type = td.parent('tr').attr('class');
    var addl = jQuery('<button class="addl">ajouter '+type+'</button>');
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
    var tr = td.parent('tr');
    var oid = parseIdString(tr.attr('id'));
    var rml = jQuery('<button class="rml">effacer la ligne</button>');
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
    var sid = td.parents('tr').attr('id');
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

/* bouton d'envoie */
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
    var list = legende.find('th');
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
	line.find('td.laction')
	    .prepend('<div class="basculeOff" id="basculecours_'+o["id_cours"]+'" />')
	    .bind('click',{id: o["id_cours"]},basculerCours);
	line.before('<tr class="imgcours"><td class="imgcours" colspan="12"><div id="imgcours'+o["id_cours"]+'" class="imgcours"></div></td></tr>');
    }
    if (type == "formation") {
	line.find('td.laction')
	    .prepend('<div class="basculeOff" id="basculeformation_'+o["id_formation"]+'" />')
	    .bind('click',{id: o["id_formation"]},basculerFormation);
	line.before('<tr class="imgformation"><td class="imgformation" colspan="12"><div id="imgformation'+o["id_formation"]+'" class="imgformation"></div></td></tr>');	
    } else {
	addRm(line.find('td.action')); 
    }
}
/* ------- FIN REMPLISSAGE DES TABLEAUX ---------*/

/* BLOC ----- ENVOI DE MODIFS AU SERVEUR --------*/
function findIdParent(tr,type) {
    var table = tr.parent().parent();
    var sid = table.attr("id");
    var oid = parseIdString(sid);
    return oid.id;
}

/* ajouter une nouvelle ligne */
function newLine() {
    var tr = $(this).parent().parent('tr');
    var type = tr.attr('class');
    var list = tr.find('th');
    var n = list.length;
    var i = 0;
    var id_parent = findIdParent(tr,type);
    getjson("json_new.php", {type: type, id_parent: id_parent}, function (tabo) {
	    appendItem(type,tr,tabo[i],list);
	});
       
}

/* supprimer une ligne */
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

/* soumettre les modifications d'une ligne */
function sendModifiedLine() {
    var ligne = $(this).parent().parent('tr');
    var parent = ligne.parent().parent('table');
    var donnees = new Object();
    var tid = parseIdString(ligne.attr('id'));
    donnees['type'] = tid['type'];
    donnees['id'] = tid['id'];
    tid =  parseIdString(parent.attr('id'));
    donnees['id_parent'] = tid['id'];
    ligne.find('td.modification').each(function (i,e) {
	    L.modification.getval($(this),donnees);
	});

    ligne.find('td.edit').each(
	function () {
	    $(this).removeClass('edit');
	    var name = $(this).attr('class');
	    $(this).addClass('edit');
	    L[name].getval($(this),donnees);
	});

    getjson("json_modify.php",donnees, replaceLine);
}
function replaceLine(tabo) {
    var o = tabo[0];
    var id = idString(o);
    var ligne = $('#'+id);
    ligne.attr('class', o.type);
    ligne.find('td').not('td.action').each(
	function () {
	    var td = $(this);
	    td.removeClass('edit');
	    td.removeClass('mutable');
//	    alert(td.attr('class'));
	    L[td.attr('class')].setval(td, o);
//	    if ("editable" in o) {
		td.addClass('mutable');
//	    }
	});
    ligne.find('td.action button.okl').remove();
    removeReload(ligne.find('td.action'));
    addRm(ligne.find('td.action'));
}
/*------- FIN ENVOI DE MODIFS AU SERVEUR --------*/

/*  */

function refreshLine(o) {
    var oid = o.data;
    getjson("json_get.php",oid, replaceLine);
}

$(document).ready(function () {
	L = new ligne(); // <-- var globale
	/* masqer certaines colonnes */
	$('th.code_geisha, th.alt').fadeOut('fast');
//	$('#skel').fadeOut('fast');
    });
