$(document).ready(function(){
	/* masqer certaines colonnes */
	$('th.code_geisha, th.alt, th.inscrits, th.presents, th.mcc, th.fin, th.tirage').fadeOut('fast');
	$('#skel').fadeOut('fast');

	/* histogrammes */
	$('tr.entete > td').each( function() {
		addHistoGlobal($(this));
	    });
	// toutBasculer();
//	$("table.super").draggable({ revert: true }); // js manquant ?
//	$("a.enseignant").click(function(){window.open(this.href);return false;});


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
