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

function contientERREUR(str) {
    var patt=/ERREUR/g;
    return patt.test(str);
}


function supprimerCours(id) {
    $('#cours'+id).parent().css('background-color','yellow');
    if (confirm('Voulez vous vraiment supprimer ce cours ?')) {
	jQuery.post("act_supprimercours.php", {id_cours: id}, function (data) {
		if (contientERREUR(data)) {
		    alert(data);
		    $('#cours'+id).parent().css('background-color','');
		}
		else {
		    masquerTranchesCours(id);
		    totauxCoursChanged(id);
		    $('#cours'+id).parent('tr.cours').remove();
		    $('#imgcours'+id).parent().parent('tr.imgcours').remove();

		}
	    }, 'text');
    } else {
	$('#cours'+id).parent().css('background-color','');
    }
    return false;
}

function existsjQuery(jQ) {
    if ( undefined == jQ.length ) {
	return false; // safari ?
    }
    else if (0 == jQ.length) {
	return false; // firefox
    }
    return true;
}

function modifierCours(id) {
    $('#boutonmodifiercours'+id).attr('disabled','disabled');
    /*  $('#cours'+id).parent().css('background-color','yellow'); */
    flobu = new flower_bubble ({
	base_obj: $('#cours'+id).parent(),
		block_mode: 'base_obj',
		base_dir: 'img',
		background: { css: 'white', opacity: 0.78 },
		bubble: { image: 'bubble.png', width: 130, height: 98 },
		flower: { image: 'flower.gif', width: 32, height: 32 }
	}) ;
    flobu.enable();
    var bascule =  $('#basculecours'+id);
    if (bascule.hasClass('basculeOn')) {
	basculerCours(id);    
    }	
    if (!existsjQuery($('#tcours'+id))) {
	$('#cours' + id).parent().after('<tr class="cours" ondblclick="modifierCours('+id+')" id="tcours'+id+'" style="display:none;"><td colspan="11">invisible</td></tr>');
    }
    jQuery.post("act_editercours.php", {id_cours: id}, function (data) {
	    if (contientERREUR(data)) {
		alert(data);
		$('#cours'+id).parent().css('background-color','');
	    }
	    else {
		$('#tcours'+id).before(data);
		$('#formeditcours'+id).show();
		/* pour le traitement du formulaire */
		
		var options = {
		target: '#tcours' + id, /* trop dynamique pour safari ? */
		beforeSubmit: function (d, f, opt) {
			beforeModifierCours(d, f, opt, id);
		    },
		success: function (resp, stat) {
			afterModifierCours(resp, stat, id);
		    },
		clearForm: false,
		resetForm: false,
		url: "act_updatecours.php",
		timeout:   3000
		};
		
		/* armer les callback du traitement du formulaire */
		$('#feditcours'+id).ajaxForm(options);
		/* activer l'autocomplete du formulaire */
		$('#feditcours'+id+' select.autocomplete').select_autocomplete({autoFill: true,mustMatch: true});
		$('#cours'+id).parent().hide();
	    }
	    flobu.disable();
	}, 'text');
    return false;
}

function beforeModifierCours(formData, jqForm, options, id) { 
//    $('#tcours'+id).show();
    return true;
}

function afterModifierCours(responseText, statusText, id)  {
    if ( existsjQuery($('#coursnew'+id)) )  { 
        /* on a bien la nouvelle ligne pour ce cours */
        /* effacer le formulaire d'edition */
	$('#formeditcours'+id).remove();
	/* nettoyer la nouvelle ligne */
        /* effacer l'ancienne ligne  */
	$('#cours'+id).parent().remove();
	$('#tcours'+id).removeAttr('id');
        /* remplacer par la nouvelle ligne */
	$('#coursnew'+id).attr('id','cours'+id);
	$('#cours'+id).parent().show();	
    } else {
    /* Sinon on laisse l'ancienne ligne, le formulaire et la cible en place */
	alert(responseText.replace(/<[^>]+>/ig,"").replace("ERREUR","Erreur : "));
    }
} 


function annulerModifierCours(id) {
    $('#cours'+id).parent().css('background-color','');
    $('#cours'+id).parent().show();
    $('#tcours'+id).remove();
    $('#formeditcours'+id).remove();
    $('#boutonmodifiercours'+id).attr('disabled','');
    return false;
}


function popFormCours(element, id) {    
    /* afficher le formulaire */
    $('#formcours' + id).show();
    if (!existsjQuery($('#tformation'+id))) {
	    $('#formcours'+id).after('<tr class="cours" id="tformation'+id+'"  style="display: none;"><td colspan="11"></td></tr>');
    }
    /* pour le traitement du formulaire */
    var options = {
    target: '#tformation' + id,
    beforeSubmit: function (data, form, opt) {
	    beforeAjouterCours(data, form, opt, id);
	},
    success: function (resp, stat) {
	    afterAjouterCours(resp, stat, id);
	},
    clearForm: false,
    resetForm: false,
    url: "act_ajoutercours.php",
    timeout:   3000
    };

    /* armer les callback du traitement du formulaire */
    $("#fformation"+id).ajaxForm(options);
    return false;
}

function beforeAjouterCours(formData, jqForm, options, id) {  
    return true;
}

function afterAjouterCours(responseText, statusText, id)  {
    if (existsjQuery($('#tformation'+id+' > td.action')) ) {
	/* le cours a ete crée, on note son id dans un coin et on le range */
	var idcours = $('#tformation' + id).contents('td.action').attr('id').replace('cours','');
	$('#tformation' + id).attr('ondblclick','modifierCours('+idcours+')');
	$('#tformation' + id).show();
	$('#tformation' + id).removeAttr('id');
       /* créer une cible fraîche pour plus tard */
	if (!existsjQuery($('#tformation'+id))) {
	    $('#formcours'+id).after('<tr class="cours" id="tformation'+id+'"  style="display: none;"><td colspan="11"></td></tr>');
	}
       /* On déplie la vue des tranches de ce nouveau cours pour que l'utilisateur pense à
	* les renseigner */
	basculerCours(idcours);
    } else {
	alert(responseText.replace(/<[^>]+>/ig,"").replace("ERREUR","Erreur : "));
    }
} 


function annulerAjouterCours(id) {
    $('#formcours' + id).hide();
    return false;
}

function tranchesCours(id) {
    /* detruire l'ancienne presentation des tranches */
    masquerTranchesCours(id);

    /* preparer les options pour le formulaire de creation de tranches */
    var options = {
    target: null,
    beforeSubmit: function (data, form, opt) {
	    beforeAjouterTranche(data, form, opt, id);
	},
    success: function (resp, stat) {
	    afterAjouterTranche(resp, stat, id);	    
	},
    clearForm: false,
    resetForm: true,
    url: "act_ajoutertranche.php",
    timeout:   3000
    };   

    /* demander les nouvelles tranches et les afficher lorsqu'elles sont dispo */
    jQuery.post("act_affichertranches.php", {id_cours: id}, function (resultat) {
	    if(contientERREUR(resultat)) {
		alert(resultat);
	    } else {
		$('#cours'+id).parent().after(resultat);
	    
		/* armer les callback du traitement du formulaire */
		$('#formtranche'+id).ajaxForm(options);
		/* activer l'autocomplete du formulaire */
		$('#formtranche'+id+' select.autocomplete').select_autocomplete({autoFill: true, mustMatch: true});
		/* activer quelques bulles d'aide */
		/* bullesaide_tranches(id); bof pas ici, ca va créer des détritus plein la page à la longue */
	    }
	}, 'text');
    return false;
}

function masquerTranchesCours(id) {
    $('#tranchesducours'+id).remove();
    return false;
}



function beforeAjouterTranche(formData, jqForm, options, id) {
    return true;
}

function afterAjouterTranche(responseText, statusText, id)  {
    if (!contientERREUR(responseText)) {
	$('#tranchesducours' + id + ' table.tranches > tbody > tr:last').before(responseText);
	totauxCoursChanged(id);
    } else {
	alert(responseText.replace(/<[^>]+>/ig,"").replace("ERREUR","Erreur : "));
    }
}

function supprimerTranche(id) {    
    $('#tranche'+id).parent().css('background-color','yellow');
    if (confirm('Voulez vous vraiment supprimer cette intervention ?')) {
	jQuery.post("act_supprimertranche.php", {id_tranche: id}, function (data) {
		if (contientERREUR(data)) {
		    alert(data);
		    $('#tranche'+id).parent().css('background-color','');
		}
		else {
		    var id_cours = coursDeLaTranche(id);
		    $('#tranche'+id).parent().remove();
		    totauxCoursChanged(id_cours);
		}
	    }, 'text');
    } else {
	$('#tranche'+id).parent().css('background-color','');
    } 
    return false;
}

function modifierTranche(id) {
    $('#boutonmodifiertranche'+id).attr('disabled','disabled');
    flobu = new flower_bubble ({
	base_obj: $('#tranche'+id).parent(),
		block_mode: 'base_obj',
		base_dir: 'img',
		background: { css: 'white', opacity: 0.78 },
		bubble: { image: 'bubble.png', width: 130, height: 98 },
		flower: { image: 'flower.gif', width: 32, height: 32 }
	});
    flobu.enable();
    jQuery.post("act_editertranche.php", {id_tranche: id}, function (data) {
	    if (contientERREUR(data)) {
		alert(data);
	    }
	    else {
		$('#tranche'+id).parent().after(data);
		$('#formedittranche'+id).show();
		$('#tranche'+id).parent().hide();
		/* pour le traitement du formulaire */
		var options = {
		target: null, /* trop dynamique pour safari ? */
		beforeSubmit: function (d, f, opt) {
			beforeModifierTranche(d, f, opt, id);
		    },
		success: function (resp, stat) {
			afterModifierTranche(resp, stat, id);
		    },
		clearForm: false,
		resetForm: false,
		url: "act_updatetranche.php",
		timeout:   3000
		};
		
		/* armer les callback du traitement du formulaire */
		$('#fedittranche'+id).ajaxForm(options); 
		/* activer l'autocomplete du formulaire */
		$('#fedittranche'+id+' select.autocomplete').select_autocomplete({autoFill: true,mustMatch: true});
	    }
	    flobu.disable();
	}, 'text');
    return false;
}

function beforeModifierTranche(formData, jqForm, options, id) { 
    return true;
}

function afterModifierTranche(responseText, statusText, id)  {
    if ( !contientERREUR(responseText) )  {
	$('#tranche'+id).parent().after(responseText);	
        /* on a bien la nouvelle ligne pour ce cours */
	/* effacer l'ancienne ligne  */
	$('#tranche'+id).parent().remove();
        /* effacer le formulaire d'edition */
	$('#formedittranche'+id).remove();
	/* nettoyer la nouvelle ligne */
	$('#tranchenew'+id).attr('id','tranche'+id);
	 totauxCoursChanged(coursDeLaTranche(id));
    } else {
    /* Sinon on laisse l'ancienne ligne, le formulaire et la cible en place */
	alert(responseText.replace(/<[^>]+>/ig,"").replace("ERREUR","Erreur : "));
    }
} 


function annulerModifierTranche(id) {
    $('#tranche'+id).parent().css('background-color','');
    $('#tranche'+id).parent().show();
    $('#ttranche'+id).remove();
    $('#formedittranche'+id).remove();
    $('#boutonmodifiertranche'+id).attr('disabled','');
    return false;
}
/*****  Les bascules *********/


function basculerCours(id) {
    var bascule =  $('#basculecours'+id);
    bascule.toggleClass('basculeOff');
    bascule.toggleClass('basculeOn');
    if (bascule.hasClass('basculeOn')) {
	tranchesCours(id);
    } else {
	masquerTranchesCours(id);
    }
    return false;
}

/* sympa mais quelques soucis d'affichage */
function basculerFormation(id) {
    var bascule =  $('#basculeformation'+id);
    bascule.toggleClass('basculeOff');
    bascule.toggleClass('basculeOn');
    if (bascule.hasClass('basculeOff')) {
	$('#tableformation'+id+' tr.legende').fadeOut("slow");
	$('#tableformation'+id+' tr.formcours').fadeOut("slow");
	$('#tableformation'+id+' tr.cours div.basculeOn').trigger("click");
	$('#tableformation'+id+' tr.cours').fadeOut("slow");
	$('#tableformation'+id+' tr.imgcours').fadeOut("slow");	
    } else {
	$('#tableformation'+id+' tr.legende').fadeIn("slow");
	$('#tableformation'+id+' tr.cours').fadeIn("slow");
	$('#tableformation'+id+' tr.imgcours').fadeIn("slow");
    }
    return false;
}


function basculerSuperFormation(id) {
    var bascule =  $('#basculesuper'+id);
    bascule.toggleClass('basculeOff');
    bascule.toggleClass('basculeOn');
    if (bascule.hasClass('basculeOff')) {
	$('#tablesuper'+id+' tr.formation div.basculeOn').trigger("click");
	$('#tablesuper'+id+' tr.sousformations').fadeOut("slow");
    } else {
	$('#tablesuper'+id+' tr.sousformations').fadeIn("slow");
    }
    return false;
}


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
		$('#imgcours'+id).html('');
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

function htdSuperFormation(id) {
    jQuery.post("act_totauxsuper.php", {id_sformation: id}, function (data) {
	    if (!contientERREUR(data)) {
        // DEBUG       alert('htdFormation('+id+') : data = '+data);
		data = trim(data);
		$('#imgsformation'+id).html(data);
		var totaux = $('#imgsformation'+id+' img').attr('title');
		$('#sformation'+id+' td.intitule span.totaux').text(totaux);
	    } else {
		$('#imgsformation'+id).html('');
	    }
	}, 'html');
    return false;
}

function htdTotaux() {
    jQuery.post("act_totaux.php", {annee_universitaire: "2009"}, function (data) {
	    if (!contientERREUR(data)) {
		data = trim(data);
		$('#imgentete').html(data);
		var totaux = $('#imgentete img').attr('title');
		$('#entete td span.totaux').text(totaux);		
	    } else {
		$('#imgentete').html('');
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
    s = $('#tableformation'+id_formation).parents('table.super').attr('id');
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

function histoDesFormations() {
    var bascule = $('#globalHistoDesFormations');
    bascule.toggleClass('globalHistoOff');
    bascule.toggleClass('globalHistoOn');
    if (bascule.hasClass('globalHistoOn')) {
	$('div.imgformation').show();
	$('table.formations').each(function (i) {
		var tag = this.id;
		if (tag != undefined) {
		    var id = tag.replace('tableformation','');
		    htdFormation(id);
		}
	    });
	$('table.super').each(function (i) {
		var tag = this.id;
		if (tag != undefined) {
		    var id = tag.replace('tablesuper','');
		    htdSuperFormation(id);
		}
	    });
	htdTotaux();
    } else {
	$('div.imgformation').hide();
    }
    return false;
}


function histoDesCours(id) {
    var bascule = $('#histoDesCoursFormation'+id);
    bascule.toggleClass('histoOff');
    bascule.toggleClass('histoOn');
    if (bascule.hasClass('histoOn')) {
	$('#tableformation'+id+' div.imgcours').show();
	$('#tableformation'+id+' tr.cours td.action').each(function (i) {
		var tag = this.id;
		if (tag != undefined) {
		    var id = tag.replace('cours','');
		    htdCours(id);
		}
	    });
    } else {
	$('#tableformation'+id+' div.imgcours').hide();
    }
    return false;
}

function logsFormation(id) {
    var bascule = $('#logsFormation'+id);
    bascule.toggleClass('logOff');
    bascule.toggleClass('logOn');
    if (bascule.hasClass('logOn')) {
	var titre = 'Logs '+$('#nomformation'+id).text();
	jQuery.post("act_historique.php", {id_formation: id}, function (data) {
	    if (!contientERREUR(data)) {
		$('#formation'+id+' > td.intitule').append('<div class="logsformation" id="logF'+id+'">'+data+'</div>');
		$('#logF'+id).dialog({autopen: true, 
			    draggable: true, 
			    resizable: true, 
			    width: 700,
			    height: 300,
			    close: function (event,ui) {logsFormation(id);},
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
