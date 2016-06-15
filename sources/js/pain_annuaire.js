/* -*- coding: utf-8 -*-*/
/* Pain - outil de gestion des services d'enseignement        
 *
 * Copyright 2009-2012 Pierre Boudes, département d'informatique de l'institut Galilée.
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

"use strict"; /* From now on, lets pretend we are strict */

function myinarray(x, t) {
    for (var i in t) {
	if (t[i] == x) return true;
    }
    return false;
}

function maj_form_annuaire() {
    var cycle = $('#sformations');
    var sformations = cycle.find("option:selected").val();

/*    $('#formannuaire').prepend(sformations); */
    var cf = $('#choix_formations');
    var cc = $('#choix_collections');
    var cs = $('#choix_semestres');
    var ce = $('#choix_categories');
    cf.html('<img src="css/img/ajax-loader.gif" />');
    cc.html('<img src="css/img/ajax-loader.gif" />');
    cs.html('<img src="css/img/ajax-loader.gif" />');
    ce.html('<img src="css/img/ajax-loader.gif" />');
    /* charger les formations */
    getjson("json_pub_get.php",{type: "formation", id_parent: sformations}, function (o) {
	var div = jQuery('<ul></ul>');
	var n = o.length;
	if (n == 0) div.html("<em>Pas d'année de formation définie dans ce cycle.</em>");
	var i;
	var cks = $('#formannuairevalues').find('.formations').text().split(',');
	for (i = 0; i < n; i++) {
	    var ck = "";
	    var v = o[i];
	    var nom = v["nom"];
	    var id = v["id_formation"];
	    if (v["annee_etude"] > 0) nom += " "+v["annee_etude"];
	    if (v["parfum"] != "") nom += " "+v["parfum"];
	    if(myinarray(id, cks)) ck = ' checked="checked"';
	    div.append('<li><input type="checkbox" name="formations[]" value='+id+ck+' /><label for="formations">'+nom+'</label></li>');
	}
	div.find('input').change(function() {$('#cbtoutesformations').removeAttr('checked');});
	cf.html('');	
	cf.append(div);
	$('#cbtoutesformations').removeAttr('disabled');
    });
    /* charger les collections */
    getjson("json_pub_get.php",{type: "collection", id_parent: sformations}, function (o) {
	var div = jQuery('<ul></ul>');
	var n = o.length;
	if (n == 0) div.html("<em>Pas de parcours défini dans ce cycle.</em>");
	var i;
	var cks = $('#formannuairevalues').find('.collections').text().split(',');
	for (i = 0; i < n; i++) {
	    var ck = "";
	    var v = o[i];
	    var nom = v["nom_collection"];
	    var id = v["id_collection"];
	    if(myinarray(id, cks)) ck = ' checked="checked"';
	    div.append('<li><input type="checkbox" name="collections[]" value='+id+ck+' /><label for="formations">'+nom+'</label></li>');
	}
	div.find('input').change(function() {$('#cbtoutescollections').removeAttr('checked');});
	cc.html('');	
	cc.append(div);
	$('#cbtoutescollections').removeAttr('disabled');
    });
    /* charger les semestres */
    getjson("json_pub_get.php",{type: "semestre", id_parent: sformations}, function (o) {
	var div = jQuery('<ul></ul>');
	var n = o.length;
	if (n == 0) div.html("<em>Pas de semestre renseigné dans ce cycle.</em>");
	var i;
	var cks = $('#formannuairevalues').find('.semestres').text().split(',');
	for (i = 0; i < n; i++) {
	    var ck = "";
	    var v = o[i];
	    var nom = "semestre "+v["semestre"];
	    var id = v["semestre"];
	    if(myinarray(id, cks)) ck = ' checked="checked"';
	    div.append('<li><input type="checkbox" name="semestres[]" value='+id+ck+' /><label for="semestres">'+nom+'</label></li>');
	}
	div.find('input').change(function() {$('#cbtoussemestres').removeAttr('checked');});
	cs.html('');	
	cs.append(div);
	$('#cbtoussemestres').removeAttr('disabled');
    });
    /* charger les categories */
    getjson("json_pub_get.php",{type: "categorie", id_parent: sformations}, function (o) {
	var div = jQuery('<ul></ul>');
	var n = o.length;
	if (n == 0) div.html("<em>Pas de catégories d'intervenants dans ce cycle.</em>");
	var i;
	var cks = $('#formannuairevalues').find('.categories').text().split(',');
	for (i = 0; i < n; i++) {
	    var ck = "";
	    var v = o[i];
	    var nom = v["nom_court"];
	    var id = v["id_categorie"];
	    if(myinarray(id, cks)) ck = ' checked="checked"';
	    div.append('<li><input type="checkbox" name="categories[]" value='+id+ck+' /><label for="categories">'+nom+'</label></li>');
	}
	div.find('input').change(function() {$('#cbtoutescategories').removeAttr('checked');});
	ce.html('');	
	ce.append(div);
	$('#cbtoutescategories').removeAttr('disabled');
    });
}
$(document).ready(function(){
    var cycle = $('#sformations');
    if (existsjQuery($('#formannuairevalues .sformations'))) {
	maj_form_annuaire();
    } else {
	cycle.find("option:disabled").attr('selected','selected');
    }
    /* deselectionne les wildcards */ 
    if (existsjQuery($('#formannuairevalues').find('.formations')) && !existsjQuery($('#formannuairevalues').find('.toutesformations'))) {
	$('#cbtoutesformations').removeAttr('checked');
    };
    if (existsjQuery($('#formannuairevalues').find('.collections')) && !existsjQuery($('#formannuairevalues').find('.toutescollections'))) {
	$('#cbtoutescollections').removeAttr('checked');
    };
    if (existsjQuery($('#formannuairevalues').find('.semestres')) && !existsjQuery($('#formannuairevalues').find('.toussemestres'))) {
	$('#cbtoussemestres').removeAttr('checked');
    };
    if (existsjQuery($('#formannuairevalues').find('.categories')) && !existsjQuery($('#formannuairevalues').find('.toutescategories'))) {
	$('#cbtoutescategories').removeAttr('checked');
    };

    cycle.change(maj_form_annuaire);
});
