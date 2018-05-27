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

function myparseFloat(str) {
     var s = str.split(' ');
     var val = parseFloat(s[0]);
     if (isNaN(val)) val = 0.0;
     return val;
}

function celluleTableau(tab, ligne, colonne) {
    return tab.find('tr').slice(ligne - 1, ligne).find('td, th').slice(colonne - 1,colonne);
}



$(document).ready(function(){
	attachDrawGraphCat();
	attachDrawGraphCatForm();

 $( "select" ).change(function () {
                $(this).children("option:selected" ).each(function() {
                    location.assign("statsCSV.php?"
                        + "&annee_menu=" + $("#choixannee select option:selected").val());
                });
        });

    });

function attachDrawGraphCat() {
	var td =  $("#tableCat > tbody > tr:first > th:first");
	td.find("micropalette").remove();
	td.prepend( '<div class="micropalette"><div id="drawGraphCat" class="globalHistoOff"></div></div>');
	$('#drawGraphCat').bind("click",{rien: "rien"},graphCat);
}

function attachDrawGraphCatForm() {
	var td =  $("#tableCatForm > tbody > tr:first > th:first");
	td.find("micropalette").remove();
	td.prepend( '<div class="micropalette"><div id="drawGraphCatForm" class="globalHistoOff"></div></div>');
	$('#drawGraphCatForm').bind("click",{rien: "rien"},graphCatForm);
}


function graphCat() {
    var noms = ['réductions de services', 'permanents', 'heures sup', 'non-permanents', 'autres'];
    var valeurs = [];
    var tab = $("#tableCat");
    var perm;
    var red;
/*
    $("#tableCat tr").slice(2,5).each(function () {
	    noms.push($(this).find('th:first').text());
	    valeurs.push(myparseFloat($(this).find(':last').text()));
	});
*/

    red = myparseFloat(celluleTableau($("#tableCatForm"),-1,2).text());
    valeurs.push(red);
    perm = myparseFloat(celluleTableau(tab,3,4).text());
    valeurs.push(perm - red);
    valeurs.push(myparseFloat(celluleTableau(tab,3,6).text()) - perm);
    valeurs.push(myparseFloat(celluleTableau(tab,4,6).text()));
    valeurs.push(myparseFloat(celluleTableau(tab,5,5).text()));
    
    
    var serie = $.gchart.series('', valeurs
/*, ['gray','green','green', 'orange', 'purple']*/);
    $("#graphCat").gchart({type: 'pie', //'barVert', 
		dataLabels: noms,
		series: [serie]
		});
}

function graphCatForm() {
	var noms = [];
	var permanents = [];
	var nonperms = [];
	var autres = [];
	var max = 0.0;
	$("#tableCatForm tr + tr > th").slice(0, -1).each(function () {
		var td = $(this).next('td');
		var somme = 0.0;
		noms.push($(this).text());
		somme = myparseFloat(td.text());
		permanents.push(somme);
		if (somme  > max) max = somme;
		td = td.next('td');
		somme = myparseFloat(td.text());
		nonperms.push(somme);
		if (somme  > max) max = somme;
		td = td.next('td');
		somme = myparseFloat(td.text());
		td = td.next('td');
		somme += myparseFloat(td.text());
		td = td.next('td');
		somme += myparseFloat(td.text());
		autres.push(somme);
		if (somme  > max) max = somme;
	    });

	$("#graphCatForm").gchart({type: 'barVertGrouped', //'barVert', 
		    dataLabels: noms, legend: 'right',
		    axes: [$.gchart.axis('left',0,max)],
		    minValue: 0,
		    maxValue:  max,	    
		    series: [$.gchart.series('permanents', permanents, 'green'), 
			     $.gchart.series('non-perm.', nonperms, 'orange'), 
                     /*	     $.gchart.series('IG', galilee, 'orange'),
                	     $.gchart.series('univ', univ, 'blue'), */
			     $.gchart.series('autres', autres, 'purple')]		   
		    });
}
