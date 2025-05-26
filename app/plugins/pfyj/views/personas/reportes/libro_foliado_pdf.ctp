<?php 
//echo $libro . " - " . $hoja_desde . " - " . $hoja_hasta . " - " . $fillNroLibro . " - " . $fillNroHoja;
//debug($datos);
//exit;

App::import('Vendor','foliopdf');
$PDF = new FOLIOPDF();

$PDF->SetTitle("LIBRO DE ASOCIADOS NRO " . str_pad($libro,$fillNroLibro,"0",STR_PAD_LEFT));

$PDF->nroLibro = $libro;
$PDF->hojaDesde = $hoja_desde;
$PDF->hojaHasta = $hoja_hasta;
$PDF->fillNroLibro = $fillNroLibro;
$PDF->fillNroHoja = $fillNroHoja;
$PDF->showHeader = false;

if(!isset($libro)) $libro = intval(mt_rand());

$PDF->Open();

$PDF->AddPage();

if(!empty($datos)):

	
	$PDF->SetFont(PDF_FONT_NAME_MAIN,'',7);
	
	foreach($datos as $dato):
	
        
		$PDF->Cell(20,5,$dato['texto_1'],0,0,"L");
		$PDF->Cell(50,5,substr(strtoupper($dato['texto_2']),0,31),0,0,"L");
		$PDF->SetFont(PDF_FONT_NAME_MAIN,'',5);
        $PDF->Cell(85,5,substr($dato['texto_3'],0,85),0,0,"L");
        $PDF->SetFont(PDF_FONT_NAME_MAIN,'',7);
		$PDF->Cell(20,5,$dato['texto_10'],0,0,"C");
		$PDF->Cell(15,5,$dato['texto_7'],0,0,"C");
		$PDF->ln(4);
	
	endforeach;

endif;


$PDF->Output("libro_asociados_".str_pad($libro,$fillNroLibro,"0",STR_PAD_LEFT).".pdf");
exit;

?>