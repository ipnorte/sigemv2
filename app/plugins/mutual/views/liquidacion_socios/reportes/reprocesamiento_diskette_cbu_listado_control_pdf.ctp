<?php 

//debug($liquidacion);
//debug($archivo);
//debug($params);
//debug($sociosOK);
//debug($sociosERROR);
//debug($diskette);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("LISTADO SOPORTE DISKETTE CBU :: ".$params['banco_intercambio_nombre']." :: ". $liquidacion['Liquidacion']['periodo_desc_amp']);
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo1'] = $params['banco_intercambio_nombre'];
$PDF->titulo['titulo2'] = 'LIQUIDACION ' . $liquidacion['Liquidacion']['periodo_desc_amp'] ." | FECHA DEBITO: ".$util->armaFecha($params['fecha_debito']);
$PDF->titulo['titulo3'] = 'DEBITO ESPECIAL' . (!empty($params['proveedor_razon_social_resumida']) ? " [".$params['proveedor_razon_social_resumida']."]" : "");


$W1 = array(7,48,5,60,27,23,20);
//$W1 = array(5,60,5,70,30,20);
$L1 = $PDF->armaAnchoColumnas($W1);

$fontSizeHeader = 5;

$PDF->encabezado = array();
$PDF->encabezado[0] = array();	

$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => '#',
			'borde' => 'LTB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][1] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => 'DNI - APELLIDO Y NOMBRE',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][2] = array(
			'posx' => $L1[2],
			'ancho' => $W1[2],
			'texto' => 'REG',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
$PDF->encabezado[0][3] = array(
			'posx' => $L1[3],
			'ancho' => $W1[3],
			'texto' => 'EMPRESA',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][4] = array(
			'posx' => $L1[4],
			'ancho' => $W1[4],
			'texto' => 'SUCURSAL - CUENTA',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
$PDF->encabezado[0][5] = array(
			'posx' => $L1[5],
			'ancho' => $W1[5],
			'texto' => 'CBU',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
$PDF->encabezado[0][6] = array(
			'posx' => $L1[6],
			'ancho' => $W1[6],
			'texto' => 'IMPORTE',
			'borde' => 'TBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);

$PDF->AddPage();
$PDF->reset();

$size = 6;

$ACUM_IMPORTE = 0;

foreach($sociosOK as $socio):

	$reg++;
	
	$ACUM_IMPORTE += $socio['LiquidacionSocio']['importe_adebitar'];

	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0],
		'texto' => $reg,
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);
	$PDF->linea[1] = array(
		'posx' => $L1[1],
		'ancho' => $W1[1],
		'texto' => substr($socio['LiquidacionSocio']['documento'] ." - ". $socio['LiquidacionSocio']['apenom'],0,37),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);
	$PDF->linea[2] = array(
		'posx' => $L1[2],
		'ancho' => $W1[2],
		'texto' => $socio['LiquidacionSocio']['registro'],
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);

	$PDF->linea[3] = array(
		'posx' => $L1[3],
		'ancho' => $W1[3],
		'texto' => substr($socio['LiquidacionSocio']['empresa'],0,45),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);
	$PDF->linea[4] = array(
		'posx' => $L1[4],
		'ancho' => $W1[4],
		'texto' => $socio['LiquidacionSocio']['sucursal']."-".$socio['LiquidacionSocio']['nro_cta_bco'],
		'borde' => '',
		'align' => '',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);
	$PDF->linea[5] = array(
		'posx' => $L1[5],
		'ancho' => $W1[5],
		'texto' => $socio['LiquidacionSocio']['cbu'],
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);				
	$PDF->linea[6] = array(
		'posx' => $L1[6],
		'ancho' => $W1[6],
		'texto' => $util->nf($socio['LiquidacionSocio']['importe_adebitar']),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);
	
	$PDF->Imprimir_linea();	

endforeach;

$size = 8;

$PDF->linea[5] = array(
	'posx' => $L1[0],
	'ancho' => 170,
	'texto' => "TOTAL ($reg REGISTROS) ",
	'borde' => '',
	'align' => 'L',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size
);				
$PDF->linea[6] = array(
	'posx' => $L1[6],
	'ancho' => $W1[6],
	'texto' => $util->nf($ACUM_IMPORTE),
	'borde' => '',
	'align' => 'R',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size
);

$PDF->Imprimir_linea();	

//if(isset($params['proveedor_razon_social'])){
//	$PDF->ln(2);
//	$PDF->linea[0] = array(
//		'posx' => $L1[0],
//		'ancho' => 170,
//		'texto' => "**** DEBITO ESPECIAL PARA SER IMPUTADO A " . $params['proveedor_razon_social'] . " ****",
//		'borde' => '',
//		'align' => 'L',
//		'fondo' => 0,
//		'style' => 'B',
//		'colorf' => '#ccc',
//		'size' => $size + 2
//	);
//	$PDF->Imprimir_linea();		
//		
//}



$PDF->Output("listado_diskette_".$params['banco_intercambio']."_" . date("Ymd",strtotime($params['fecha_debito'])).".pdf");

?>