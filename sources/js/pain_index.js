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

"use strict"; /* From now on, lets pretend we are strict */

function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function montrerCours(id_sformation,id_formation,id_cours) {
    $(document).ajaxComplete(function(){   
	var f=$('#basculeformation_'+id_formation); 
	var c=$('#basculecours_'+id_cours);
	if (f.length && f.hasClass('basculeOff')) {f.click();}
	if (c.length && c.hasClass('basculeOff')) {c.click();}
	}); 
    var sf=$('#bascule_sformation'+id_sformation);
    if (sf.length && sf.hasClass('basculeOff')) {sf.click();}
}

$(document).ready(function(){
	/* masquer certaines colonnes et les squelettes de lignes */
	$('#skelcours').children('th.section, th.credits, th.mcc, th.debut, th.fin, th.tirage, th.inscrits, th.presents, th.totaux_loader, th.collections, th.tags').fadeOut(0); // th.alt, th.inscrits, th.presents,
	$('#skeltranche').children('th.declarer').fadeOut(0);
	$('#skelchoix').children('th.cm, th.td, th.tp, th.alt, th.ctd, th.choix').fadeOut(0);
	$('#skellongchoix').children('th.cm, th.td, th.tp, th.alt, th.ctd, th.choix, th.semestre').fadeOut(0);
	$('#skel').fadeOut(0);

	$( "select" ).change(function () {
                $(this).children("option:selected" ).each(function() {
                    location.assign("index.php?"
                        + "&annee_menu=" + $("#choixannee select option:selected").val());
                });
        });


	/* histogrammes */
	$('tr.entete > td.laction').each( function() {
		addHistoGlobal($(this));
	    });

	/* on peut re-arranger les annees */
/*	$("#annee_2009, #annee_2010").sortable({
	    connectWith: '.annee',
		    handle: 'td.intitule',
		    revert: true
		    }).disableSelection();
BUG: meme avec handle, très mauvaise interaction avec les textarea
*/
	/* on peut déplacer des cours ?? */
//	$(".basculeOff").draggable(); pas encore !
/* aide */
	$('#basculeAide').button({text: true});
	$('#basculeAide').bind('click',basculerAide);


       $("#vuecourante").append('<table class="super" id="tableannees"><tbody></tbody></table>');
       appendList({type: "annee", cetteannee: "1"}, /* ajouter quoi ? */
		  $('#tableannees > tbody'),   /* ou ? */
		  function(){ /* fonction de post-traitement */
			$('#tableannees div.basculeOff').click();

		      return false;
		  });

/* dialogues */
	$("#dialog-drop-cours").dialog({
	    autoOpen: false,
		    resizable: false,
		    height:160,
		    modal: true,
		    buttons: {
			    'Copier': dropCopier,
			    'Déplacer': dropDeplacer,
			'Annuler': function() {
			$(this).dialog('close');
			}
		}
	    });

	$("#dialog-drop-tranche").dialog({
	    autoOpen: false,
		    resizable: false,
		    height:160,
		    modal: true,
		    buttons: {
	//		    'Copier': dropCopier,
			    'Déplacer': dropDeplacer,
			'Annuler': function() {
			$(this).dialog('close');
			}
		}
	    });


	$("#panier").dialog({
	    autoOpen: false,
		    title: "Choix de cours",
		    resizable: true,
/*		    height:400,*/
		    width: 550, 
		    modal: false
/*		    buttons: {
		    'Copier': dropCopier,
			'Déplacer': dropDeplacer,
			Cancel: function() {
			$(this).dialog('close');
			}
		    }
*/
	    });

	if (superuser()) {
	  $('#menu').after('<div class="bouton-SU"><span style="color:red;font-weight:bold">Rôle admin:</span><label class="switch"><input id="bouton-SU" type="checkbox"/><span class="slider round"></span></label></div>');
	  $('#bouton-SU').prop("checked",true);
	  $('#bouton-SU').change(function() {
		$('#user > .su').text( 1- $('#user > .su').text());
		if ($('#user > .su').text() == 1)
			location.reload();
	  });
	}

	$('#menu').after('<div class="bouton-panier"><button id="bouton-panier">Panier</button></div>');

	$('#bouton-panier').button(
		    {text: true,
			    icons: {
			primary: "ui-icon-cart"
				}
		    });
	$('#bouton-panier').bind('click', togglePanier);
    });

$(document).ajaxComplete(function(){   
	var myid_sf=getParameterByName('sf');
	var myid_f=getParameterByName('f');
	var myid_c=getParameterByName('c');
			if (myid_sf.length && myid_f.length && myid_c.length) {
    			var sf=$('#basculesformation_'+myid_sf);
			var f=$('#basculeformation_'+myid_f); 
			var c=$('#basculecours_'+myid_c);
    			if (sf.length && sf.hasClass('basculeOff')) {
			sf.click();}
			if (f.length && f.hasClass('basculeOff')) {f.click();}
			if (c.length && c.hasClass('basculeOff')) {c.click();}
			setTimeout(function(){
			var monobj={foo:'bar'};
			history.pushState(monobj,"retour normal","index.php")},2000);
			}
}); 
