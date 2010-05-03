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

//$(document).ready(function(){alert('toto');});

/* --------------- les fonctions de ligne/cellule -------------------*/

/* constructeur de cellule */
function cell() {
    this.name ="cell";
    this.mutable = true;

    /* passer la cellule en mode edition */
    this.edit = function (c) {
	c.wrapInner('<textarea />');
	c.addClass("edit");
    }

    /* recuperer la valeur de la cellule (en mode edition) */
    this.getval = function (c, o) {
	var s;
	if (c.hasClass("edit")) {
	    s = c.find('textarea').text();
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
	c.html('<a class="enseignant" href="service.php?id_enseignant='+o["id_enseignant"]+'">'+o["prenom"]+" "+o["nom"]+'</a><span class="hiddenvalue">'+o["id_enseignant"]+'</span>');
	c.find("a.enseignant").click(function(){window.open(this.href);return false;});
    }
}
enseignant.prototype = new cell();




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
    this.semestre = new cell();
    this.semestre.name = "semestre";
    /* nom_cours */
    this.nom_cours = new cell();
    this.nom_cours.name = "nom_cours";
    /* credits */
    this.credits = new cell();
    this.credits.name = "credits";
    /* id_enseignant */
    this.id_enseignant = new cell();
    this.id_enseignant.name = "id_enseignant";
    /* cm */
    this.cm = new cell();
    this.cm.name = "cm";
    /* td */
    this.td = new cell();
    this.td.name = "td";
    /* tp */
    this.tp = new cell();
    this.tp.name = "tp";
    /* alt */
    this.alt = new cell();
    this.alt.name = "alt";
    /* descriptif */
    this.descriptif = new cell(); /* faire une big cell et une small cell */
    this.descriptif.name = "descriptif";
    /* code_geisha */
    this.code_geisha = new cell();
    this.code_geisha.name = "code_geisha";    
    /* action */
    this.action = new notcell();
    this.action.name = "action";
    /* action a gauche */
    this.laction = new notcell();
    this.laction.name = "laction";    

    /* pain_tranche
     */
    /* id_cours */
    this.id_cours = new cell();    
    this.id_cours.name = "id_cours";
    /* type_conversion */
    this.type_conversion = new cell();    
    this.type_conversion.name = "type_conversion";
    /* remarque */
    this.remarque = new cell();    
    this.remarque.name = "remarque";
    /* htd */
    this.htd = new cell();
    this.htd.name = "htd";
    /* descriptif */
    this.descriptif = new cell();
    this.descriptif.name = "descriptif";
    /* pain_resa
     */
    // TODO
}


/* gestion des evenements */

/* Les bascules */
function basculerFormation(id) {
    var bascule =  $('#basculeformation_'+id);
    bascule.toggleClass('basculeOff');
    bascule.toggleClass('basculeOn');
    if (bascule.hasClass('basculeOff')) {
	$('#tableformation_'+id+' tr.legende').remove();
	$('#tableformation_'+id+' tr.cours div.basculeOn').trigger("click");
	$('#tableformation_'+id+' tr.cours').remove();
	$('#tableformation_'+id+' tr.imgcours').remove();	
    } else {
	appendList("cours","formation",id);
	$('#tableformation_'+id+' tr.legende').fadeIn("slow");
    }
    return false;
}


/* Ajout du bouton d'edition */
function addOk(jqcell) {
    var ligne = jqcell.parent('tr');
    if (ligne.hasClass('edit')) {
	/* il y avait deja au moins une cellule en cours d'edition dans la ligne */
	ligne.find('div.ok').remove();
	ligne.find('td:last div.okl').remove();
	ligne.find('td:last').append('<div class="okl"/>');
	ligne.find('div.okl').click(sendModifiedLine);
    } else {
	/* c'est la premiere cellule en cours d'edition dans la ligne. 
	   En cas de confirmation on peut envoyer toute la ligne */
	ligne.addClass('edit');
	jqcell.append('<div class="ok"/>');
	jqcell.find('div.ok').click(sendModifiedLine);
    }
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


/* ajax debug */
function getjsondb(url,data,callback) {
    $.ajax({ type: "GET",
	     url: url,
	     data:  data,
//	     datatype: 'json',
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


/* Menu contextuel du choix des champs */
function setMenuFields(tr) {
    var th = tr.find('th:first');
    th.prepend('<button>champs...</button>');
    var button = th.find('button').button({
			text: false,
			icons: {
				primary: "ui-icon-triangle-1-s"
			}
	});
    button.one("click", {th: th, button: button},
	       openMenuFields);
}

function openMenuFields(e) {
    var th = e.data.th;
    var list = th.nextAll('th');
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
			 button: button, menu: menu},
		 toggleColumn);
	menu.append(blob);
    }
    button.after(menu);
//    th.find('select').selectmenu();
}


function closeMenuFields(e) {
/*
    e.data.menu.removeClass('active').hide(100, function() {
            e.data.button.removeClass('active');
        });
*/
    e.data.menu.remove();
    e.data.button.one('click', {button: e.data.button, th: e.data.th}, openMenuFields);
}

function toggleColumn(e) {
//    alert('visible = '+e.data.visible+', classname = '+e.data.css+', type = '+e.data.type);
    if (e.data.visible) {
	$('th.'+e.data.css+', td.'+e.data.css).fadeOut('slow');
    } else {
	$('th.'+e.data.css+', td.'+e.data.css).fadeIn('slow');
    }
    closeMenuFields(e);
    return false;
}



/* Remplissage des tableaux par les donnees du serveur */
function  appendList(type,type_parent,id_parent) {
    var body = $('#table'+type_parent+'_'+id_parent+' tbody');
    var legende = $("#skel"+type).clone(true);
    var list = legende.find('th');
    legende.removeAttr('id');
    body.append(legende);
    setMenuFields(legende);
    getjson("json_get.php",{id_parent: id_parent, type: type}, function (o) {
	    var n = o.length;
	    var i = 0;
	    for (i = 0; i < n; i++) {
		appendItem(type,body,o[i],list);
	    }
	});
}

function appendItem(type,body,o,list) {
    var n = list.length;
    var i = 0;
    var id = idString({id: o["id_"+type], type: type});
    var line = jQuery('<tr id="'+id+'" class="'+type+'"></tr>');
    for (i = 0; i < n; i++) {
	var name = list.eq(i).attr("class");
	var cell = jQuery('<td class="'+name+'"></td>');
	L[name].setval(cell, o);
	L[name].showmutable(cell);
	cell.dblclick(edit);
	line.append(cell);
	if (list.eq(i).css('display') == 'none') {
	    cell.fadeOut('fast');
	}
    }
    body.append(line);
}



$(document).ready(function () {
	L = new ligne(); // var globale
    });
