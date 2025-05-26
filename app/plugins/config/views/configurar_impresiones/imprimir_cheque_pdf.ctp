<?php 
//debug($aRecibo);

 App::import('Vendor','impresion_cheques');


$chqOrientacion = ($chqConfiguracion[0]['ConfigurarImpresion']['talonario'] == 2 ? 'L' :'P');

// $PDF = new ImpresionCheques($chqOrientacion, 'cm', array($chqConfiguracion['ConfigurarImpresion']['ancho'], $chqConfiguracion['ConfigurarImpresion']['alto']));



$PDF = new ImpresionCheques('P', 'mm', array($chqConfiguracion[0]['ConfigurarImpresion']['ancho']*10, $chqConfiguracion[0]['ConfigurarImpresion']['alto']*10));
$PDF->Open();


$PDF->AddPage();
$PDF->reset();
foreach($chqConfiguracion[0]['ConfigurarImpresionDetalle'] as $cuerpo):
//    $this->Text($x, $y, $txt);
    
        $PDF->linea[0] = array(
				'posx' =>  $cuerpo['izquierda']*10 - 10,
				'ancho' => $cuerpo['ancho'],
				'texto' => $cuerpo['variable'],
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '',
				'size' => $cuerpo['alto']*7,
				'family' => 'helvetica'
		);
        $PDF->SetFontSizeConf($cuerpo['alto']*10);

        $PDF->SetXY($cuerpo['izquierda']*10 - 10, $cuerpo['superior']*10 - 5);
	$PDF->Imprimir_linea();

endforeach;



$PDF->AutoPrint(true);
$PDF->Output("Cheque-Nro-" . $chqConfiguracion[0]['ConfigurarImpresion']['numero_cheque'] . ".pdf");

?>