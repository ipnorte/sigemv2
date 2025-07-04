<?php

//debug($socios);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("Resumen de Liquidacion");
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#

$titulo1 = $util->globalDato($liquidacion['Liquidacion']['codigo_organismo'],'concepto_1');
$titulo1 .= " - " . ($codDto == 0 ? ($cuotaSocialAB == 'A' ? "CUOTA SOCIAL - ALTAS" : "CUOTA SOCIAL - BAJAS") : " CONSUMOS " . ($cuotaSocialAB == 'A' ? "ALTAS" : ($cuotaSocialAB == 'B' ? "BAJAS" : "TODOS (ALTAS Y VIGENTES)")));

$PDF->titulo['titulo1'] = 'LISTADO DE SOPORTE DE DISKETTE';
$PDF->titulo['titulo2'] = 'LIQUIDACION ' . $util->periodo($liquidacion['Liquidacion']['periodo'],true);
$PDF->titulo['titulo3'] = $titulo1;

//#  	SOCIO  	REG  	IMPORTE A DEBITAR  	
// # | DNI - APELLIDO Y NOMBRE | REG |  BANCO | SUCURSAL - CUENTA | CBU | IMPORTE
//# | DNI | APELLIDO Y NOMBRE | TIPO LEY BENEFICIO SUB-BENEFICIO | CODIGO DTO | IMPORTE | OPERACION


//$W1 = array(10,70,10,10,30,20,20,20);
$W1 = array(85,10,10,25,20,20,20);
$L1 = $PDF->armaAnchoColumnas($W1);

$fontSizeHeader = 6;

$PDF->encabezado = array();
$PDF->encabezado[0] = array();	

$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => 'DNI - APELLIDO Y NOMBRE',
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
			'texto' => 'TIPO',
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
			'texto' => 'LEY',
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
			'texto' => 'BENEFICIO',
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
			'texto' => 'SUB-BENEFICIO',
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
			'texto' => 'CODIGO DTO',
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
			'texto' => 'A DEBITAR',
			'borde' => 'TBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->AddPage();
$PDF->reset();
$size = 7;
$reg = 0;

$ACUM_IMPORTE = 0;

foreach($socios as $socio):

	$reg++;
	
	$ACUM_IMPORTE += $socio['LiquidacionSocio']['importe_adebitar'];

//	$PDF->linea[0] = array(
//		'posx' => $L1[0],
//		'ancho' => $W1[0],
//		'texto' => $reg,
//		'borde' => '',
//		'align' => 'L',
//		'fondo' => 0,
//		'style' => '',
//		'colorf' => '#ccc',
//		'size' => $size
//	);
	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0],
		'texto' => substr($socio['LiquidacionSocio']['documento'] ." - ". $socio['LiquidacionSocio']['apenom'],0,70),
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
		'texto' => $socio['LiquidacionSocio']['tipo'],
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);

	$PDF->linea[2] = array(
		'posx' => $L1[2],
		'ancho' => $W1[2],
		'texto' => $socio['LiquidacionSocio']['nro_ley'],
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);
//	$PDF->linea[4] = array(
//		'posx' => $L1[4],
//		'ancho' => $W1[4],
//		'texto' => $socio['LiquidacionSocio']['nro_beneficio'],
//		'borde' => '',
//		'align' => 'C',
//		'fondo' => 0,
//		'style' => '',
//		'colorf' => '#ccc',
//		'size' => $size
//	);
	$PDF->linea[3] = array(
		'posx' => $L1[3],
		'ancho' => $W1[3],
		'texto' => substr(str_pad(trim($socio['LiquidacionSocio']['nro_beneficio']), 6, '0', STR_PAD_LEFT),-6),
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);	
	$PDF->linea[4] = array(
		'posx' => $L1[4],
		'ancho' => $W1[4],
		'texto' => $socio['LiquidacionSocio']['sub_beneficio'],
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);
	$PDF->linea[5] = array(
		'posx' => $L1[5],
		'ancho' => $W1[5],
		'texto' => $socio['LiquidacionSocio']['codigo_dto'] ." - ". $socio['LiquidacionSocio']['sub_codigo'],
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

if(!empty($errores)):
	$PDF->ln(5);
	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => 190,
		'texto' => "REGISTROS CON ERRORES (NO SE ENVIAN EN DISKETTE)",
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $size
	);
	$PDF->Imprimir_linea();
	$PDF->ln(3);
	$reg = 0;
	$size = 6;
	$ACUM_IMPORTE = 0;
	
	foreach($errores as $socio):
	
		$reg++;
		
		$ACUM_IMPORTE += $socio['LiquidacionSocio']['importe_adebitar'];
	
//		$PDF->linea[0] = array(
//			'posx' => $L1[0],
//			'ancho' => $W1[0],
//			'texto' => $reg,
//			'borde' => '',
//			'align' => 'L',
//			'fondo' => 0,
//			'style' => '',
//			'colorf' => '#ccc',
//			'size' => $size
//		);
		$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => substr($socio['LiquidacionSocio']['documento'] ." - ". $socio['LiquidacionSocio']['apenom'],1,70),
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
			'texto' => $socio['LiquidacionSocio']['tipo'],
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $size
		);
	
		$PDF->linea[2] = array(
			'posx' => $L1[2],
			'ancho' => $W1[2],
			'texto' => $socio['LiquidacionSocio']['nro_ley'],
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
			'texto' => $socio['LiquidacionSocio']['nro_beneficio'],
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $size
		);
		$PDF->linea[4] = array(
			'posx' => $L1[4],
			'ancho' => $W1[4],
			'texto' => $socio['LiquidacionSocio']['sub_beneficio'],
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $size
		);
		$PDF->linea[5] = array(
			'posx' => $L1[5],
			'ancho' => $W1[5],
			'texto' => $socio['LiquidacionSocio']['codigo_dto'] ." - ". $socio['LiquidacionSocio']['sub_codigo'],
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
		$PDF->linea[7] = array(
			'posx' => $L1[7],
			'ancho' => $W1[7],
			'texto' => $socio['LiquidacionSocio']['orden_descuento_id'],
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $size
		);		
		
		$PDF->Imprimir_linea();	
	
	endforeach;
	
	$size = 6;
	
	$PDF->linea[5] = array(
		'posx' => $L1[0],
		'ancho' => 155,
		'texto' => "TOTAL ($reg REGISTROS) ",
		'borde' => 'T',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $size
	);				
	$PDF->linea[6] = array(
		'posx' => $L1[6],
		'ancho' => $W1[6],
		'texto' => $util->nf($ACUM_IMPORTE),
		'borde' => 'T',
		'align' => 'R',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $size
	);
	$PDF->linea[7] = array(
		'posx' => $L1[7],
		'ancho' => $W1[7],
		'texto' => "",
		'borde' => 'T',
		'align' => 'R',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $size
	);
	
	$PDF->Imprimir_linea();		
	

endif;


$PDF->Output("listado_diskette.pdf");


?>
