<?php
/** Include PHPExcel */
require_once '../Classes/PHPExcel.php';
/** PHPExcel_IOFactory */
require_once '../Classes/PHPExcel/IOFactory.php';

function init_excel($NOM, $GRADE, $STATUTAIRE, $ANNEE) {
	global $objPHPExcel;

	/* début remplissage fichier excel avec comme template declaration-services.xls */
	$objReader = PHPExcel_IOFactory::createReader('Excel5');
	//$objReader->setLoadAllSheets();
	$objPHPExcel = $objReader->load("declaration-services.xls");
	//$objPHPExcel->getActiveSheet()->setCellValue('C1', date('Y').'-'.(date('Y')+1));
	$objPHPExcel->getSheet(0)->setCellValue('C2',$NOM);
	$objPHPExcel->getSheet(0)->setCellValue('C3',$GRADE);
	$objPHPExcel->getSheet(0)->setCellValue('C5',$STATUTAIRE);
	$objPHPExcel->getSheet(0)->setCellValue('C6',$STATUTAIRE);
	$objPHPExcel->getSheet(0)->getStyle('D3')->getNumberFormat()->applyFromArray(
		            array('code' => PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY));
	$objPHPExcel->getSheet(0)->setCellValue('D3',"=TODAY()");

	// Gestion du titre
	$titre='Déclaration Services '.$ANNEE.'-'.($ANNEE+1);

	$objPHPExcel->getProperties()->setTitle($titre);

		// en font 16px
	$titre='&C&16'.$titre;

	$marge=1.5/2.54; // 1,5 cm en pouces
	for ($i=0; $i<=3;$i++) {
		$sheet=$objPHPExcel->getSheet($i);
		$sheet->getHeaderFooter()->setOddHeader($titre);
		// rectification des marges, semble utile pour libreoffice
		$sheet->getPageMargins()->setLeft($marge);
		$sheet->getPageMargins()->setRight($marge);
	}


	/* refaire les totaux des semestres */
	$objPHPExcel->getSheet(0)->setCellValue('G24','=SUM(G13:G23)');
	$objPHPExcel->getSheet(1)->setCellValue('G16','=SUM(G5:G15)');

		/* recapitulatif reprend la premiere page */
	$objPHPExcel->getSheet(3)->setCellValue('C1',$NOM);
	$objPHPExcel->getSheet(3)->setCellValue('C2',$GRADE);
	$objPHPExcel->getSheet(3)->setCellValue('C3',"='1er semestre'!C5");
	$objPHPExcel->getSheet(3)->setCellValue('C4',"='1er semestre'!C6");
	$objPHPExcel->getSheet(3)->setCellValue('F5',"='1er semestre'!F7");
	$objPHPExcel->getSheet(3)->setCellValue('C6',"='1er semestre'!C8");
	$objPHPExcel->getSheet(3)->setCellValue('F6',"='1er semestre'!F8");
}

function ajouteCoursExcel($sem, $row, $maxrow, $codeEtape, $codeMatiere, $nomMatiere, $type, $eqTD) {
	global $objPHPExcel;

	if ($row>=$maxrow) {
		$objPHPExcel->getSheet($sem-1)->insertNewRowBefore($maxrow,1);
		$maxrow++;
	}

	$objPHPExcel->getSheet($sem-1)->setCellValue('A'.$row, $codeEtape);
	$objPHPExcel->getSheet($sem-1)->setCellValue('B'.$row, $codeMatiere);
	$objPHPExcel->getSheet($sem-1)->setCellValue('C'.$row, $nomMatiere);
	$objPHPExcel->getSheet($sem-1)->setCellValue('D'.$row, $type);
	$objPHPExcel->getSheet($sem-1)->setCellValue('G'.$row, $eqTD);
	return($maxrow);
}

function ajouteRespExcel($row, $maxrow, $matiere, $formation, $eqTD) {
	global $objPHPExcel;

	if ($row>=$maxrow) {
		$objPHPExcel->getSheet($sem-1)->insertNewRowBefore($maxrow,1);
		$maxrow++;
	}

	$objPHPExcel->getSheet(3)->setCellValue('A'.$row, $formation.':'.$matiere);
	$objPHPExcel->getSheet(3)->setCellValue('G'.$row, $eqTD);
	return($maxrow);
}

/* obligé car bug de PHPExcel sur le calcul de l'offset de cellules deplacées dans d'autres sheets */
function finaliseExcel($maxrow) {
	global $objPHPExcel;

	/* recapitulatif */
	$objPHPExcel->getSheet(3)
		->setCellValue('G9',"='1er semestre'!G".$maxrow['Initiale1']."+'2e semestre'!G".$maxrow['Initiale2']);

	//aïe il faut revoir cela: calcul de diff par rapport au precedent maxrow ! 
	$objPHPExcel->getSheet(3)
		->setCellValue('G10',"=SUM('1er semestre'!G".($maxrow['Initiale1']+2).":G31)+SUM('1er semestre'!G33:G38)+SUM('2e semestre'!G".($maxrow['Initiale2']+2).":G23)+SUM('2e semestre'!G25:G30)");
	$objPHPExcel->getSheet(3)->setCellValue('G11',"=SUM('Autres composantes'!G4:G11)+SUM('Autres composantes'!G13:G20)");
	/* TOTAL devant etudiants */
	$objPHPExcel->getSheet(3)->setCellValue('G12',"=SUM(G9:G11)");
	/* TOTAL réf/PRP */
	$objPHPExcel->getSheet(3)->setCellValue('G23',"=SUM(G15:G22)");
}
	
?>
