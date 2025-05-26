<?php 
//echo $libro . " - " . $hoja_desde . " - " . $hoja_hasta . " - " . $fillNroLibro . " - " . $fillNroHoja;
//exit;

App::import('Vendor','foliopdf');
$PDF = new FOLIOPDF();


$PDF->nombreLibro = $nombreLibro;
$PDF->nroLibro = $libro;
$PDF->hojaDesde = $hoja_desde;
$PDF->hojaHasta = $hoja_hasta;
$PDF->fillNroLibro = $fillNroLibro;
$PDF->fillNroHoja = $fillNroHoja;
//$PDF->showHeader = false;

$PDF->SetTitle(strtoupper($nombreLibro)." " . str_pad($libro,$fillNroLibro,"0",STR_PAD_LEFT));


$PDF->Open();

//$PDF->AddPage();
if($PDF->showHeader):
	for($hoja = $hoja_desde; $hoja <= $hoja_hasta; $hoja++){
		$PDF->hojaActual = $hoja;
		$PDF->AddPage();
	}
else:
	$PDF->AddPage();
	for($hoja = 1; $hoja <= 100; $hoja++){
		$PDF->Cell(0,5,"Hoja Nro: " . $hoja,0,0,"L");
		$PDF->ln(5);
	}
endif;


$PDF->Output("libro_foliado_".str_pad($libro,$fillNroLibro,"0",STR_PAD_LEFT).".pdf");
exit;

?>