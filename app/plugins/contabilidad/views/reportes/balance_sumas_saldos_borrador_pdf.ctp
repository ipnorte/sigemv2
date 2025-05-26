<?php
/*=============================================================================================*/	
/* SUMA Y SALDOS */
App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->titulo['titulo1'] = "";
$PDF->titulo['titulo2'] = "DESDE FECHA: " . $util->armaFecha($fecha_desde) . " - HASTA FECHA: " . $util->armaFecha($fecha_hasta);
$PDF->titulo['titulo3'] = "BALANCE DE SUMAS Y SALDOS";

$PDF->SetFontSizeConf(9);

$PDF->Open();

//$W0 = array(55, 112, 55, 55);
$W0 = array(25, 77, 22, 22, 22, 22);
$L0 = $PDF->armaAnchoColumnas($W0);

$PDF->encabezado = array();
$fontSizeHeader = 10;
$fontSizeBody = 8;


$PDF->encabezado[0][0] = array(
			'posx' => $L0[0],
			'ancho' => $W0[0],
			'texto' => 'CUENTA',
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
			'texto' => 'DESCRIPCION',
			'borde' => 'LTR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	

$ancho = $W0[2] + $W0[3];
$PDF->encabezado[0][2] = array(
			'posx' => $L0[2],
			'ancho' => $ancho,
			'texto' => 'S U M A S',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	

$ancho = $W0[4] + $W0[5];
$PDF->encabezado[0][3] = array(
			'posx' => $L0[4],
			'ancho' => $ancho,
			'texto' => 'S A L D O S',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	
	///////////////
	
	
$PDF->encabezado[1][0] = array(
			'posx' => $L0[0],
			'ancho' => $W0[0],
			'texto' => '',
			'borde' => 'LB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[1][1] = array(
			'posx' => $L0[1],
			'ancho' => $W0[1],
			'texto' => '',
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
			'texto' => 'D E B E ',
			'borde' => 'LTBR',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[1][3] = array(
			'posx' => $L0[3],
			'ancho' => $W0[3],
			'texto' => 'H A B E R ',
			'borde' => 'LTBR',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->encabezado[1][4] = array(
			'posx' => $L0[4],
			'ancho' => $W0[4],
			'texto' => 'D E B E ',
			'borde' => 'LTBR',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[1][5] = array(
			'posx' => $L0[5],
			'ancho' => $W0[5],
			'texto' => 'H A B E R ',
			'borde' => 'LTBR',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
	///////	


$PDF->AddPage();
$PDF->Reset();
		
$suma_debe_total = 0;
$suma_haber_total = 0;
$saldo_debe_total = 0;
$saldo_haber_total = 0;

foreach ($aMayor as $mayor):
	$saldo_debe = 0;
	$saldo_haber = 0;
	if($mayor[0]['debe_mayor'] > $mayor[0]['haber_mayor']):
		$saldo_debe = $mayor[0]['debe_mayor'] - $mayor[0]['haber_mayor'];
	else:
		$saldo_haber = $mayor[0]['haber_mayor'] - $mayor[0]['debe_mayor'];
	endif;
	
	$PDF->linea[0] = array(
		'posx' => $L0[0],
		'ancho' => $W0[0],
		'texto' => $mayor['MutualAsientoRenglon']['cuenta'],
		'borde' => 'LR',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $fontSizeBody
	);
				
	$PDF->linea[1] = array(
		'posx' => $L0[1],
		'ancho' => $W0[1],
		'texto' => $mayor['MutualAsientoRenglon']['descripcion'],
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
		'texto' => number_format($mayor[0]['debe_mayor'],2),
		'borde' => 'LR',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $fontSizeBody
	);
				
	$PDF->linea[3] = array(
		'posx' => $L0[3],
		'ancho' => $W0[3],
		'texto' => number_format($mayor[0]['haber_mayor'],2),
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
		'texto' => number_format($saldo_debe,2),
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
		'texto' => number_format($saldo_haber,2),
		'borde' => 'LR',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $fontSizeBody
	);
				
	$PDF->Imprimir_linea();

	$suma_debe_total += $mayor[0]['debe_mayor'];
	$suma_haber_total += $mayor[0]['haber_mayor'];
	$saldo_debe_total += $saldo_debe;
	$saldo_haber_total += $saldo_haber;
	
endforeach;

$ancho = $W0[0] + $W0[1];
$PDF->linea[0] = array(
		'posx' => $L0[0],
		'ancho' => $ancho,
		'texto' => 'TOTAL GENERAL:',
		'borde' => 'LTBR',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $fontSizeBody
	);
				
$PDF->linea[1] = array(
		'posx' => $L0[2],
		'ancho' => $W0[2],
		'texto' => number_format($suma_debe_total,2),
		'borde' => 'LTRB',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $fontSizeBody-1
	);
				
$PDF->linea[2] = array(
		'posx' => $L0[3],
		'ancho' => $W0[3],
		'texto' => number_format($suma_haber_total,2),
		'borde' => 'LTRB',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $fontSizeBody-1
	);
				
$PDF->linea[3] = array(
		'posx' => $L0[4],
		'ancho' => $W0[4],
		'texto' => number_format($saldo_debe_total,2),
		'borde' => 'LTRB',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $fontSizeBody-1
	);
				
$PDF->linea[4] = array(
		'posx' => $L0[5],
		'ancho' => $W0[5],
		'texto' => number_format($saldo_haber_total,2),
		'borde' => 'LTRB',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $fontSizeBody-1
	);
				
	$PDF->Imprimir_linea();
	
$PDF->Output("balance_sumas_saldos.pdf");
exit;

?>