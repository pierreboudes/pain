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
