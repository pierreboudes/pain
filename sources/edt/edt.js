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
$(document).ready(function(){initdragndrop();});

function initdragndrop() {
    $(".cours").draggable({ revert: true });
    $(".creneau").droppable({
      drop: function(event, ui) {
      if (ui.draggable.parent() != $(this)) {
          $(this).append(ui.draggable);
      } else { alert('Oh oh...'); }
     }
    });
}

function contientERREUR(str) {
    var patt=/ERREUR/g;
    return patt.test(str);
}

function enregistrer() {
    var contenu = $('#edt').html();
    var msg = $('#enregistrer input[name=message]').val();
    var login = $('#enregistrer input[name=login]').val();
    if (msg.length < 4) {
	alert('Message trop court: '+msg);
    } else {    
	if (confirm("Sauver l'emploi du temps en l'état ?")) {
	    jQuery.post("act_sauveredt.php", {login: login, message: msg, content: contenu}, function (data) {
		    if (contientERREUR(data)) {
			alert('Miserable failure : '+data);
		    } else {
			historique();    
		    }
		}, 'html');
	}
    }
}

function historique() {
    jQuery.post("act_historique.php", {id: "rien"}, function (data) {
	    if (contientERREUR(data)) {
		alert('Miserable failure : '+data);
	    } else {
		$('#historique').html(data);
	    }                     
	}, 'html');
}
	
function charger(id) {
    jQuery.post("act_charger.php", {id: id}, function (data) {
	    if (contientERREUR(data)) {
		alert('Miserable failure : '+data);
	    } else {
		$('#edt').html(data);
		initdragndrop();
		historique();
	    }                     
	}, 'html');
}
