/* -*- coding: utf-8 -*-*/

/* prototypes nécessaires (jslint.com) ?? */
function beforeModifierCrours(formData, jqForm, options, id){}
function afterModifierCours(responseText, statusText, id){}
function beforeAjouterCours(data, form, opt, id){}
function afterAjouterCours(responseText, statusText, id){}
function beforeAjouterTranche(formData, jqForm, options, id){}
function afterAjouterTranche(responseText, statusText, id){}
function masquerTranchesCours(id){}
function htdCours(id){}
function htdFormation(id){}
function coursDeLaTranche(id_tranche){}
function totauxCoursChanged(id_cours){}


$(document).ready(function(){
	/* un effet visuel pour signaler les actions disponibles (obsolete)*/
	$("a.action").hover(function(){$(this).fadeOut(100);$(this).fadeIn(500);});
        /* sympa mais pose quelques soucis d'affichage ... */
	$("tr.formation div.basculeOn").trigger("click");
});


function supprimerCours(id) {
    $('#cours'+id).parent().css('background-color','yellow');
    if (confirm('Voulez vous vraiment supprimer ce cours ?')) {
	jQuery.post("act_supprimercours.php", {id_cours: id}, function (data) {
		if (4 < data.length) {
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
    $('#cours'+id).parent().css('background-color','yellow');   
    if (!existsjQuery($('#tcours'+id))) {
	$('#cours' + id).parent().after('<tr class="cours" ondblclick="modifierCours('+id+')" id="tcours'+id+'" style="display:none;"><td colspan="11">invisible</td></tr>');
    }
    jQuery.post("act_editercours.php", {id_cours: id}, function (data) {
	    if (20 > data.length) {
		alert(data);
		$('#cours'+id).parent().css('background-color','');
	    }
	    else {
		$('#tcours'+id).before(data);
		$('#formeditcours'+id).show();
		$('#cours'+id).parent().hide();
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
	    }
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
	    $('#cours'+id).parent().after(resultat);
	    
            /* armer les callback du traitement du formulaire */
	    $('#formtranche'+id).ajaxForm(options);
	    /* activer quelques bulles d'aide */
	    /* bullesaide_tranches(id); bof pas ici, ca va créer des détritus plein la page à la longue */
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
    if (responseText.length > 40) {
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
		if (4 < data.length) {
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
    $('#tranche'+id).parent().css('background-color','yellow');   
    jQuery.post("act_editertranche.php", {id_tranche: id}, function (data) {
	    if (20 > data.length) {
		alert(data);
		$('#tranche'+id).parent().css('background-color','');
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
	    }
	}, 'text');
    return false;
}

function beforeModifierTranche(formData, jqForm, options, id) { 
    return true;
}

function afterModifierTranche(responseText, statusText, id)  {
    if ( responseText.length > 40 )  {
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
/******************/


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

function trim(str) 
{ 
    return str.replace(/^\s+/g,'').replace(/\s+$/g,'');
}

/* Calculer les couts */
function htdCours(id) {
    jQuery.post("act_totauxcours.php", {id_cours: id}, function (data) {
        // DEBUG alert('htdCours('+id+') : data = '+data);
	    if (data.length > 10) {
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
	    if (data.length > 10) {
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


function htdTotaux() {
    jQuery.post("act_totaux.php", {annee_universitaire: "2009"}, function (data) {
	    if (data.length > 10) {
		data = trim(data);
		$('#entete td span.totaux').text(data);		
	    } else {
		$('#imgformation'+id).html('');
	    }
	}, 'text');
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

function totauxCoursChanged(id_cours) {
    var id_formation = 0;
    id_formation = formationDuCours(id_cours);
    htdCours(id_cours);
    htdFormation(id_formation);
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
