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

/* TODO virer code mort, sauver et utiliser existsjQuery ou l'utilitaire equivalent, utiliser le trim de jQuery */

function contientERREUR(str) {
    var patt=/ERREUR/g;
    return patt.test(str);
}


/*****  Les bascules *********/


function toutBasculer() {
    jQuery.fx.off = true;
    $("tr.super div.basculeOff").trigger("click");
    $("tr.super div.basculeOn").trigger("click");
    jQuery.fx.off = false;
}

function trim(str) 
{ 
    return str.replace(/^\s+/g,'').replace(/\s+$/g,'');
}

/* Calculer les couts */
function htdCours(id) {
    jQuery.post("act_totauxcours.php", {id_cours: id}, function (data) {
        // DEBUG alert('htdCours('+id+') : data = '+data);
	    if (!contientERREUR(data)) {
		data = trim(data);
		$('#imgcours'+id).html(data);
	    } else {
		$('#imgcours'+id).html('PLOUM');
	    }
	}, 'html');
    return false;
}

function htdFormation(id) {
    jQuery.post("act_totauxformation.php", {id_formation: id}, function (data) {
	    if (!contientERREUR()) {
        // DEBUG       alert('htdFormation('+id+') : data = '+data);
		data = trim(data);
		$('#imgformation'+id).html(data);
		var totaux = $('#imgformation'+id+' img').attr('title');
		$('#formation'+id+' td.intitule span.totaux').text(totaux);
	    } else {
		$('#imgformation'+id).html('');
	    }
	}, 'html');
    return false;
}

function coursDeLaTranche(id_tranche) {
    var s;
    var id_cours;
    s = $('#tranche'+id_tranche).parents('tr.trtranches').attr('id');
    /* s = 'formtranche'+id */
    id_cours = parseInt(s.replace('tranchesducours',''));
    return id_cours;
}

function formationDuCours(id_cours) {
    var s;    
    var id_formation;
    s = $('#cours'+id_cours).parent().prevAll('tr.formation').attr('id');
    /* s = 'formation' + id */
    id_formation  = parseInt(s.replace('formation',''));
    return id_formation;
}

function superDeLaFormation(id_formation) {
    var s;
    var id_sformation;
    s = $('#tableformation_'+id_formation).parents('table.super').attr('id');
    /* s = 'tablesuper'+id */
    id_sformation = parseInt(s.replace('tablesuper',''));
    return id_sformation;
}


function montrerFormation(id_formation) {
    var id_sformation = superDeLaFormation(id_formation);
    var basculeS =  $('#basculesuper'+id_sformation);
    var basculeF =  $('#basculeformation'+id_formation);
    if (basculeS.hasClass('basculeOff')) {
	basculerSuperFormation(id_sformation);
    }
    if (basculeF.hasClass('basculeOff')) {
	basculerFormation(id_formation);
    }
}

function totauxCoursChanged(id_cours) {
    var id_formation = 0;
    id_formation = formationDuCours(id_cours);
    id_sformation = superDeLaFormation(id_formation);
    htdCours(id_cours);
    htdFormation(id_formation);
    htdSuperFormation(id_sformation);
    /* dans l'ideal on trouve aussi l'annee universitaire pour la
     * passer au php */
    htdTotaux();
    return false;
}

function histoDesCours(e) {
    var id = e.data.id;
    var bascule = $('#histoDesCoursFormation'+id);
    bascule.toggleClass('histoOff');
    bascule.toggleClass('histoOn');
    if (bascule.hasClass('histoOn')) {
	$('#tablecours_'+id+' div.imgcours').show();
	$('#tablecours_'+id+' tr.cours').each(function (i) {
		var tag = this.id;
		if (tag != undefined) {
		    var id = tag.replace('cours_','');
		    htdCours(id);
		}
	    });
    } else {
	$('#tablecours_'+id+' div.imgcours').hide();
    }
    return false;
}

function logsFormation(e) {
    var id = e.data.id;
    var bascule = $('#logsFormation'+id);
    bascule.toggleClass('logOff');
    bascule.toggleClass('logOn');
    if (bascule.hasClass('logOn')) {
	var titre = 'Logs '+$('#formation_'+id+'> td.intitule').text();
	jQuery.post("act_historique.php", {id_formation: id}, function (data) {
	    if (!contientERREUR(data)) {
		$('#formation_'+id+' > td.intitule').append('<div class="logsformation" id="logF'+id+'">'+data+'</div>');
		$('#logF'+id).dialog({autopen: true, 
			    draggable: true, 
			    resizable: true, 
			    width: 700,
			    height: 300,
			    close: function (event,ui) {logsFormation(e);},
			    title: titre
			    });
	    } else {
		alert(data);
	    }
	    return false;
	}, 'html');
    }  else {
	$('#logF'+id).dialog('destroy');
	$('#logF'+id).remove();	
    }
    return false;
}

/* 
a tester pour l'animation des tableaux

voir : http://old.nabble.com/Animating-table-rows-td20491521s27240.html

$("tr").click(function() {
  var tr = $(this);
  tr.children("td").each(function() {
    $(this).wrapInner("<div></div>").children("div").slideUp(function() {
      tr.hide();
    });
  });
});
*/

function logoutcomplete(request, status) {
    // window.location.reload();
    alert("logoutcomplete");
}

function logouterror(request, status) {
    alert("logouterror");
    window.location.replace("http://perdu.com");
}

function mylogout() {
    var options = {
    type: "GET",
    cache: false,
    url: "logout.php",
    username: "fakelogin",
    password: "fakepass",
    complete: logoutcomplete,
    error: logouterror
    };
    $.ajax(options);
}
