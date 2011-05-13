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
/* masquage des squelettes */
	$('#skel').fadeOut(0);

       $("#vueadmin").append('<table class="super" id="tableannees"><tbody></tbody></table>');
       appendList({type: "annee"}, /* ajouter quoi ? */
		  $('#tableannees > tbody'),   /* ou ? */
		  function(){ /* fonction de post-traitement */
		      return false;
		  });


/* dialogues */
	$("#dialog-attendre").dialog({
	    autoOpen: false,
		    resizable: false,
		    modal: true
	    });
	$("#dialog-drop").dialog({
	    autoOpen: false,
		    resizable: false,
		    width: 600,
		    //height:240,
		    modal: true,
		    buttons: {
		    'Annuler': function() {
			$(this).dialog('close');
		    },
			'4. Tout': copierFormationsCoursInterventions,
			    '3. Cours': copierFormationsCours,
			    '2. Cycle(s)': copierFormations,
			    '1. Titre(s)': copierSformation
		}
	    });
	    
    });
