<?php 
//debug($liquidacion);
//debug($datos);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF('L');

if($tipo == 'FECHA'):
	$PDF->SetTitle("INFORME RECIBO ENTRE FECHA DESDE: " . $util->armaFecha($fecha_desde) . " HASTA: " . $util->armaFecha($fecha_hasta));
	$PDF->titulo['titulo2'] = "CONTROL ENTRE FECHA";
	$PDF->titulo['titulo1'] = "DESDE: " . $util->armaFecha($fecha_desde) . " HASTA: " . $util->armaFecha($fecha_hasta);
else:
	$PDF->SetTitle("INFORME RECIBO POR NUMERO DESDE: " . $letra . '-' . $sucursal . '-' . $numero_desde . " - HASTA: " . $letra . '-' . $sucursal . '-' . $numero_hasta);
	$PDF->titulo['titulo2'] = "CONTROL POR NUMERO";
	$PDF->titulo['titulo1'] = "DESDE: " . $letra . "-" . $sucursal . "-" . $numero_desde . "HASTA: " . $letra . "-" . $sucursal . "-" . $numero_hasta;
endif;
$PDF->SetFontSizeConf(10);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo3'] =  "INFORME DE RECIBOS";

$W1 = array(25,35,90,50,47,30);
$L1 = $PDF->armaAnchoColumnas($W1);

$PDF->encabezado = array();
$fontSizeHeader = 10;

$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => ' FECHA',
			'borde' => 'LT',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][1] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => 'NUMERO COMP.',
			'borde' => 'T',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
$PDF->encabezado[0][2] = array(
			'posx' => $L1[2],
			'ancho' => $W1[2],
			'texto' => 'RAZON SOCIAL',
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][3] = array(
			'posx' => $L1[3],
			'ancho' => $W1[3],
			'texto' => 'CUIT / DOCUMENTO',
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
$PDF->encabezado[0][4] = array(
			'posx' => $L1[4],
			'ancho' => $W1[4],
			'texto' => 'I.V.A.',
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
$PDF->encabezado[0][5] = array(
			'posx' => $L1[5],
			'ancho' => $W1[5],
			'texto' => 'IMPORTE',
			'borde' => 'TR',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
///////	
$PDF->AddPage();
$PDF->Reset();
	
	foreach ($aReciboInforme as $aRecibo):
		$PDF->Reset();
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => $W1[0],
					'texto' => $util->armaFecha($aRecibo['Recibo']['fecha_comprobante']),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		
		$PDF->linea[1] = array(
					'posx' => $L1[1],
					'ancho' => $W1[1],
					'texto' => $aRecibo['Recibo']['letra'] . '-' . $aRecibo['Recibo']['sucursal'] . '-' . $aRecibo['Recibo']['nro_recibo'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		
		$PDF->linea[2] = array(
					'posx' => $L1[2],
					'ancho' => $W1[2],
					'texto' => $aRecibo['Recibo']['razon_social'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		
		$PDF->linea[3] = array(
					'posx' => $L1[3],
					'ancho' => $W1[3],
					'texto' => $aRecibo['Recibo']['cuit'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		
		$PDF->linea[4] = array(
					'posx' => $L1[4],
					'ancho' => $W1[4],
					'texto' => $aRecibo['Recibo']['iva_concepto'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		
		$PDF->linea[5] = array(
					'posx' => $L1[5],
					'ancho' => $W1[5],
					'texto' => number_format($aRecibo['Recibo']['importe'],2,',','.'),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		
		$PDF->Imprimir_linea();
		$PDF->Ln();
		
	endforeach;	

$PDF->Output("informe_recibo.pdf");
exit;
?>