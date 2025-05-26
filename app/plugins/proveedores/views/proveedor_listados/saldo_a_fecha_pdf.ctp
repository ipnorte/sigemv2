<?php 

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF('L');

$PDF->SetTitle("SALDO A FECHA");
$PDF->titulo['titulo1'] = "";
$PDF->titulo['titulo2'] = "DESDE FECHA: " . $util->armaFecha($desdeFecha) . " - HASTA FECHA: " . $util->armaFecha($hastaFecha);
$PDF->titulo['titulo3'] = "SALDO A FECHA";

$PDF->SetFontSizeConf(9);


$PDF->Open();

$W0 = array(20, 82, 37, 23, 23, 23, 23, 23, 23);
$L0 = $PDF->armaAnchoColumnas($W0);

$PDF->encabezado = array();
$fontSizeHeader = 8;
$fontSizeBody = 7;


$PDF->encabezado[0][0] = array(
			'posx' => $L0[0],
			'ancho' => $W0[0],
			'texto' => '',
			'borde' => 'LTR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[0][1] = array(
			'posx' => $L0[1],
			'ancho' => $W0[1],
			'texto' => '',
			'borde' => 'LTR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	
$PDF->encabezado[0][2] = array(
			'posx' => $L0[2],
			'ancho' => $W0[2],
			'texto' => '',
			'borde' => 'LTR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[0][3] = array(
			'posx' => $L0[3],
			'ancho' => $W0[3],
			'texto' => 'SALDO AL',
			'borde' => 'LTR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[0][4] = array(
			'posx' => $L0[4],
			'ancho' => $W0[4],
			'texto' => '',
			'borde' => 'LTR',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->encabezado[0][5] = array(
			'posx' => $L0[5],
			'ancho' => $W0[5],
			'texto' => '',
			'borde' => 'LTR',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->encabezado[0][6] = array(
			'posx' => $L0[6],
			'ancho' => $W0[6],
			'texto' => '',
			'borde' => 'LTR',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->encabezado[0][7] = array(
			'posx' => $L0[7],
			'ancho' => $W0[7],
			'texto' => 'SALDO DEL',
			'borde' => 'LTR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->encabezado[0][8] = array(
			'posx' => $L0[8],
			'ancho' => $W0[8],
			'texto' => 'SALDO AL',
			'borde' => 'LTR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
	///////	
	


$PDF->encabezado[1][0] = array(
			'posx' => $L0[0],
			'ancho' => $W0[0],
			'texto' => 'CUIT-CUIL',
			'borde' => 'LBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[1][1] = array(
			'posx' => $L0[1],
			'ancho' => $W0[1],
			'texto' => 'RAZON SOCIAL',
			'borde' => 'LBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	
$PDF->encabezado[1][2] = array(
			'posx' => $L0[2],
			'ancho' => $W0[2],
			'texto' => 'CONDICION IVA',
			'borde' => 'LBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[1][3] = array(
			'posx' => $L0[3],
			'ancho' => $W0[3],
			'texto' => $util->armaFecha($fecha_saldo_anterior),
			'borde' => 'LBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[1][4] = array(
			'posx' => $L0[4],
			'ancho' => $W0[4],
			'texto' => 'P A G O S',
			'borde' => 'LBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->encabezado[1][5] = array(
			'posx' => $L0[5],
			'ancho' => $W0[5],
			'texto' => 'N. CREDITOS',
			'borde' => 'LBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->encabezado[1][6] = array(
			'posx' => $L0[6],
			'ancho' => $W0[6],
			'texto' => 'FACTURAS',
			'borde' => 'LBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->encabezado[1][7] = array(
			'posx' => $L0[7],
			'ancho' => $W0[7],
			'texto' => 'PERIODO',
			'borde' => 'LBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->encabezado[1][8] = array(
			'posx' => $L0[8],
			'ancho' => $W0[8],
			'texto' => $util->armaFecha($hastaFecha),
			'borde' => 'LBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
	///////	
	
$PDF->AddPage();
$PDF->Reset();

	foreach ($saldos as $saldo):
				
		$PDF->linea[0] = array(
			'posx' => $L0[0],
			'ancho' => $W0[0],
			'texto' => $saldo['Proveedor']['cuit'],
			'borde' => 'LR',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
						
		$PDF->linea[1] = array(
			'posx' => $L0[1],
			'ancho' => $W0[1],
			'texto' => $saldo['Proveedor']['razon_social'],
			'borde' => 'LR',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
						
		$PDF->linea[2] = array(
			'posx' => $L0[2],
			'ancho' => $W0[2],
			'texto' => $saldo['GlobalDato']['concepto_1'],
			'borde' => 'LR',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
						
		$PDF->linea[3] = array(
			'posx' => $L0[3],
			'ancho' => $W0[3],
			'texto' => number_format($saldo['0']['saldo_anterior'],2),
			'borde' => 'LR',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
						
		$PDF->linea[4] = array(
			'posx' => $L0[4],
			'ancho' => $W0[4],
			'texto' => number_format($saldo['0']['pagos'],2),
			'borde' => 'LR',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
						
		$PDF->linea[5] = array(
			'posx' => $L0[5],
			'ancho' => $W0[5],
			'texto' => number_format($saldo['0']['credito'],2),
			'borde' => 'LR',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
						
		$PDF->linea[6] = array(
			'posx' => $L0[6],
			'ancho' => $W0[6],
			'texto' => number_format($saldo['0']['debito'],2),
			'borde' => 'LR',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
						
		$PDF->linea[7] = array(
			'posx' => $L0[7],
			'ancho' => $W0[7],
			'texto' => number_format($saldo['0']['saldo'],2),
			'borde' => 'LR',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
						
		$PDF->linea[8] = array(
			'posx' => $L0[8],
			'ancho' => $W0[8],
			'texto' => number_format($saldo['0']['saldo_actual'],2),
			'borde' => 'LR',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
						
		$PDF->Imprimir_linea();
		
	endforeach;

$PDF->Output("saldo-fecha.pdf");
exit;

?>
