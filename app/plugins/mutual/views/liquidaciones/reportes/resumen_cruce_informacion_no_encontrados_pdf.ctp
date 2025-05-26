<?php 
// debug($socios);
// exit;

App::import('Vendor','listado_pdf');


//$PDF = new ListadoPDF(($soloEnviadosEnDiskette==0) ? "P" : "L");
$PDF = new ListadoPDF();

$PDF->SetTitle("Resumen Cruce de Informacion de Liquidacion");
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

$titulo2 = "#".$liquidacion['Liquidacion']['id']."-".$util->periodo($liquidacion['Liquidacion']['periodo'],true) . ' | ' . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']);;

if($isCjp == 1 && $valSubCodCjp == 0) $titulo2 .= " (C.SOCIAL)";
if($isCjp == 1 && $valSubCodCjp == 1) $titulo2 .= " (CONSUMOS)";

#TITULO DEL REPORTE#
$PDF->titulo['titulo1'] = "";
$PDF->titulo['titulo3'] = $titulo_reporte;//"LIQUIDADOS NO ENCONTRADOS EN RENDICION";
$PDF->titulo['titulo2'] = $titulo2;

//DNI | APENOM| BENEFICIO | IMPORTE LIQUIDADO |IMPORTE A DEBITAR 

$W1 = array(15,35,110,15,15);

if($isCjp == 1 && $valSubCodCjp == 1) $W1 = array(15,35,95,15,15,15);

$L1 = $PDF->armaAnchoColumnas($W1);

$PDF->bMargen = 10;

$fontSizeHeader = 5;

$PDF->encabezado = array();
$PDF->encabezado[0] = array();	

$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => 'DOCUMENTO',
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
			'texto' => 'APELLIDO Y NOMBRE',
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
			'texto' => 'BENEFICIO',
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
			'texto' => 'LIQUIDADO',
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
			'texto' => 'A DEBITAR',
			'borde' => ($isCjp == 1 && $valSubCodCjp == 1 ? 'TB' : 'TBR'),
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
if($isCjp == 1 && $valSubCodCjp == 1):	
	$PDF->encabezado[0][5] = array(
				'posx' => $L1[5],
				'ancho' => $W1[5],
				'texto' => 'COMPROBANTE',
				'borde' => 'TBR',
				'align' => 'C',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);	
endif;

$PDF->AddPage();
$PDF->reset();

$size = 5;

$ACU_CANTIDAD 		= 0;
$ACU_LIQUIDADO 		= 0;
$ACU_ADEBITAR 		= 0;

foreach($socios as $socio):

	$ACU_CANTIDAD++;
	$ACU_LIQUIDADO 	+= $socio['LiquidacionSocio']['importe_dto'];
	$ACU_ADEBITAR 	+= $socio['LiquidacionSocio']['importe_adebitar'];

	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0],
		'texto' => $socio['LiquidacionSocio']['tipo_documento_desc'].' '.$socio['LiquidacionSocio']['documento'],
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
	    'texto' => utf8_decode($socio['LiquidacionSocio']['apenom']),
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
	    'texto' => substr($socio['LiquidacionSocio']['beneficio_str'],0,100),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);

	$PDF->linea[3] = array(
		'posx' => $L1[3],
		'ancho' => $W1[3],
		'texto' => $util->nf($socio['LiquidacionSocio']['importe_dto']),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);
	$PDF->linea[4] = array(
		'posx' => $L1[4],
		'ancho' => $W1[4],
		'texto' => $util->nf($socio['LiquidacionSocio']['importe_adebitar']),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);			

	if($isCjp == 1 && $valSubCodCjp == 1):
	
		$PDF->linea[5] = array(
			'posx' => $L1[5],
			'ancho' => $W1[5],
			'texto' => $socio['LiquidacionSocio']['orden_descuento_id'],
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $size
		);		
	endif;
	
	$PDF->Imprimir_linea();	

endforeach;

$PDF->linea[2] = array(
	'posx' => $L1[2],
	'ancho' => $W1[2],
	'texto' => "TOTAL ($ACU_CANTIDAD SOCIOS)",
	'borde' => '',
	'align' => 'R',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size
);

$PDF->linea[3] = array(
	'posx' => $L1[3],
	'ancho' => $W1[3],
	'texto' => $util->nf($ACU_LIQUIDADO),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#F5f7f7',
	'size' => $size
);
$PDF->linea[4] = array(
	'posx' => $L1[4],
	'ancho' => $W1[4],
	'texto' => $util->nf($ACU_ADEBITAR),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#F5f7f7',
	'size' => $size
);			

$PDF->Imprimir_linea();

#IMPRIMIR ANALISIS POR PROVEEDOR

$PDF->Output("resumen_cruce_informacion_no_encontrados.pdf");

?>