
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
		    $('#cours'+id).parent().remove();
		    masquerTranchesCours(id);
		}
	    }, 'text');
    } else {
	$('#cours'+id).parent().css('background-color','');
    }  
}

function modifierCours(element,id) {
    element.attr('disabled','disabled');
    $('#cours'+id).parent().css('background-color','yellow');
    jQuery.post("act_editercours.php", {id_cours: id}, function (data) {
	    if (20 > data.length) {
		alert(data);
		$('#cours'+id).parent().css('background-color','');
	    }
	    else {
		$('#cours'+id).parent().after(data);
		$('#formeditcours'+id).show();
		/* pour le traitement du formulaire */
		var options = {
		target: '#tcours' + id,
		beforeSubmit: function (d, f, opt) {
			beforeModifierCours(d, f, opt, id);
		    },
		success: function (resp, stat) {
			afterModifierCours(resp, stat, id);
		    },
		clearForm: false,
		resetForm: true,
		url: "act_updatecours.php",
		timeout:   3000
		};
		
		/* armer les callback du traitement du formulaire */
		$("#feditcours"+id).ajaxForm(options); 
	    }
	}, 'text');
}

function beforeModifierCours(formData, jqForm, options, id) {
    $('#cours' + id).parent().after('<tr class="cours" id="tcours'+id+'></tr>');
    $('#cours' + id).parent().remove();
    return true;
}

function afterModifierCours(responseText, statusText, id)  { 
    $('#formeditcours'+id).remove();
    $('#tcours'+id).removeAttr('id');
} 


function annulerModifierCours(id) {
    $('#cours'+id).parent().css('background-color','');
    $('#formeditcours'+id).remove();
    $('#boutonmodifiercours'+id).attr('disabled','');
}


function popFormCours(element, id) {    
    /* afficher le formulaire */
    $('#formcours' + id).show();

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
    resetForm: true,
    url: "act_ajoutercours.php",
    timeout:   3000
    };

    /* armer les callback du traitement du formulaire */
    $("#fformation"+id).ajaxForm(options); 
}

function beforeAjouterCours(formData, jqForm, options, id) {  
    $('#formcours'+id).after('<tr class="cours" id="tformation'+id+'"><td colspan="11" style="display: none;"></td></tr>');
    return true;
}

function afterAjouterCours(responseText, statusText, id)  {
    $('#tformation' + id).removeAttr('id');
} 


function annulerAjouterCours(id) {
    $('#formcours' + id).hide();
}

function tranchesCours(id) {
    /* detruire l'ancienne presentation des tranches */
    masquerTranchesCours(id);

    /* preparer les options pour le formulaire de creation de tranches */
    var options = {
    target: '#ttranche' + id,
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
	}
	, 'text');
}

function masquerTranchesCours(id) {
    $('#tranchesducours'+id).remove();
}



function beforeAjouterTranche(formData, jqForm, options, id) {
    $('#formtranche'+id).after('<div class="tranche" id="ttranche'+id+'" style="display: block;">nothing</div>');
    return true;
}

function afterAjouterTranche(responseText, statusText, id)  {
    $('#formtranche' + id + ' table.tranches tr:last').after(responseText);
    $('#ttranche'+id).remove();
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
		    $('#tranche'+id).parent().remove();
		}
	    }, 'text');
    } else {
	$('#tranche'+id).parent().css('background-color','');
    } 
}

function editerTranche(id) {
    $('#tranche'+id).parent().css('background-color','yellow');
    alert('Indisponible');
    $('#tranche'+id).parent().css('background-color','');
}

function basculerCours(id) {
    var bascule =  $('#basculecours'+id);
    bascule.toggleClass('basculeOff');
    bascule.toggleClass('basculeOn');
    if (bascule.hasClass('basculeOn')) {
	tranchesCours(id);
    } else {
	masquerTranchesCours(id);
    }
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
    } else {
	$('#tableformation'+id+' tr.legende').fadeIn("slow");
	$('#tableformation'+id+' tr.cours').fadeIn("slow");
    }
}
