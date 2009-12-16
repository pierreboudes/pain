$(document).ready(function(){
	/* un effet visuel pour signaler les actions disponibles (obsolete)*/
//	$("a.action").hover(function(){$(this).fadeOut(100);$(this).fadeIn(500);});
       flobu = new flower_bubble ({
	base_obj: $('body'),
		block_mode: 'page',
		base_dir: 'img',
		background: { css: 'white', opacity: 0.78 },
		bubble: { image: 'bubble.png', width: 130, height: 98 },
		flower: { image: 'flower.gif', width: 32, height: 32 }
	}) ;
        flobu.enable();
	$('select.autocomplete').select_autocomplete({autoFill: true,mustMatch: true});
        /* sympa mais pose quelques soucis d'affichage ... */
	toutBasculer();
//	$("table.super").draggable({ revert: true });
	flobu.disable();
});
