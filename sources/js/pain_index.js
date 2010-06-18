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

$(document).ready(function(){
	/* masqer certaines colonnes et les squelettes de lignes */
	$('#skelcours').children('th.code_geisha, th.inscrits, th.presents, th.mcc, th.fin, th.tirage').fadeOut(0); // th.alt
	$('#skelchoix').children('th.cm, th.td, th.tp, th.alt, th.choix').fadeOut(0);
	$('#skellongchoix').children('th.cm, th.td, th.tp, th.alt, th.choix, th.semestre').fadeOut(0);
	$('#skel').fadeOut(0);
	
	/* histogrammes */
	$('tr.entete > td').each( function() {
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
	

/* dialogues */
	$("#dialog-drop").dialog({
	    autoOpen: false,
		    resizable: false,
		    height:160,
		    modal: true,
		    buttons: {
		    'Copier': dropCopier,
			'Déplacer': dropDeplacer,
			Cancel: function() {
			$(this).dialog('close');
		    }
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
    

/* Search box */

	$('#menu').append('<li id="menu-chercher"><form id="search-highlight" action="#" method="post"><input id="term" class="text" name="term" size="10" type="text"></input><input id="chercher" name="submit" type="submit" value="Chercher"></input></form></li>');
	$('#menu-chercher').fadeIn("slow");
	$('#menu').after('<div id="resultat-recherche"></div>');

//	$('#menu-chercher').fadeIn('slow');
	$("#chercher").click(function(){
		// start variables as empty
		var term = "";
		var n = "0";
//		toutBasculer();
		// hide the results at first
		$('#resultat-recherche').hide().empty();
		// grab the input value and store in variable
		term = $('#term').attr('value');
//		console.log("The value of term is: "+term);
		$('span.highlight').each(function(){
			$(this).after($(this).html()).remove();
		    });
		if($('#term').val() == ""){
		    $('#resultat-recherche').fadeIn().append('Entrer un texte à chercher');
		    return false;
		} else {
/*
		    $('td :contains("'+term+'")').each(function(){
			    var rexp = new RegExp(term,'g');
			    $(this).html($(this).html().replace(rexp, '<span class="highlight">'+term+'</span>'));
			    $(this).find('span.highlight').fadeIn();
			}); */
		    $('tr.cours > td').each(function(){
			    var rexp = new RegExp('('+term+')','ig');
			    if (rexp.test($(this).html())) {
//				alert($(this).html());
				$(this).html($(this).html().replace(rexp, '<span class="highlight">'+"$1</span>"));
				$(this).find('span.highlight').fadeIn();
			    }
			});
		    // how many did it find?
		    n = $("span.highlight").length;
		    //	    console.log("The there is a total of: "+n);
		    if(n == 0){
			$('#resultat-recherche').fadeIn().append("terme non trouvé");
		    } else {
			$('#resultat-recherche').fadeIn().append("<strong>Trouvé:</strong> "+n+" résultat(s) pour la recherche de: <em><strong>"+term+"</strong></em>.");
			$("span.highlight").each(function() {
				var s = $(this).parents('table.formations').attr('id');
				var id_formation  = parseInt(s.replace('tableformation_',''));
				montrerFormation(id_formation);
			    });
		    }
		    return false;
		}
	});
});
