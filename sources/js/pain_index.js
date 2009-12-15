$(document).ready(function(){
	/* un effet visuel pour signaler les actions disponibles (obsolete)*/
//	$("a.action").hover(function(){$(this).fadeOut(100);$(this).fadeIn(500);});
	$('select.autocomplete').select_autocomplete({autoFill: true,mustMatch: true});
        /* sympa mais pose quelques soucis d'affichage ... */
	toutBasculer();
//	$("table.super").draggable({ revert: true });
});
