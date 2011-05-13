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

$(document).ready(function(){
	/* masqer certaines colonnes et les squelettes de lignes */
	$('#skelcours').children('th.code_geisha, th.credits, th.mcc, th.fin, th.tirage').fadeOut(0); // th.alt, th.inscrits, th.presents,
	$('#skelchoix').children('th.cm, th.td, th.tp, th.alt, th.choix').fadeOut(0);
	$('#skellongchoix').children('th.cm, th.td, th.tp, th.alt, th.choix, th.semestre').fadeOut(0);
	$('#skel').fadeOut(0);
	
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
		      return false;
		  });



/* dialogues */
	$("#dialog-drop-cours").dialog({
	    autoOpen: false,
		    resizable: false,
		    height:160,
		    modal: true,
		    buttons: {
			'Annuler': function() {
			$(this).dialog('close');
			},
			    'Copier': dropCopier,
			    'Déplacer': dropDeplacer
		}
	    });
	
	$("#panier").dialog({
	    autoOpen: false,
		    title: "Choix de cours",
		    resizable: true,
/*		    height:400,
		    width: 875, */
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

	$('#bouton-panier').button(
		    {text: true,
			    icons: {
			primary: "ui-icon-cart"
				}
		    });
	$('#bouton-panier').bind('click', togglePanier);
    
    });
