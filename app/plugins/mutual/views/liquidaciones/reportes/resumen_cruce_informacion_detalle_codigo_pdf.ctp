<?php 
//DEBUG($socios);
//EXIT;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("Resumen Cruce de Información de Liquidacion");
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->textoHeader = $titulo_opcion;
$PDF->titulo['titulo1'] = "$banco_nombre";
$PDF->titulo['titulo3'] = "$status ($status_descripcion)";
$PDF->titulo['titulo2'] = "#".$liquidacion['Liquidacion']['id']."-".$util->periodo($liquidacion['Liquidacion']['periodo'],true) . ' | ' . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']);

//DNI | APENOM| BENEFICIO | IMPORTE DEBITADO

$W1 = array(20,50,100,20);
$L1 = $PDF->armaAnchoColumnas($W1);

$PDF->bMargen = 10;

$fontSizeHeader = 7;

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
			'texto' => 'IDENTIFICACION',
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

$size = 7;

$ACU_CANTIDAD 		= 0;
//$ACU_LIQUIDADO 		= 0;
//$ACU_NOENVIADO 		= 0;
//$ACU_ADEBITAR 		= 0;
$ACU_DEBITADO 		= 0;
//$ACU_IMPUTADO 		= 0;

foreach($socios as $socio):

	$ACU_CANTIDAD++;
//	$ACU_LIQUIDADO 	+= $socio['LiquidacionSocioRendicion']['importe_dto'];
//	$ACU_NOENVIADO 	+= $socio['LiquidacionSocioRendicion']['importe_noenviado'];
//	$ACU_ADEBITAR 	+= $socio['LiquidacionSocioRendicion']['importe_adebitar'];
	$ACU_DEBITADO 	+= $socio['LiquidacionSocioRendicion']['importe_debitado'];
//	$ACU_IMPUTADO 	+= $socio['LiquidacionSocioRendicion']['importe_imputado'];

	if(empty($socio['Persona']['tipo_documento']))$tdoc = "DNI";
	else $tdoc = $util->globalDato($socio['Persona']['tipo_documento']);
	if(empty($socio['Persona']['documento'])) $ndoc =  $socio['LiquidacionSocioRendicion']['documento'];
	else $ndoc = $socio['Persona']['documento'];

	if(empty($socio['Persona']['apellido'])) $apenom = "*** NO EXISTE EN PADRON ***";
	else $apenom = $socio['Persona']['apellido'].", ".$socio['Persona']['nombre'];

	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0],
		'texto' => $tdoc.' '.$ndoc,
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
		'texto' => substr($apenom,0,32),
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
		'texto' => $socio['LiquidacionSocioRendicion']['identificacion'],
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
		'texto' => $util->nf($socio['LiquidacionSocioRendicion']['importe_debitado']),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);
	
	$PDF->Imprimir_linea();	

endforeach;

$PDF->linea[2] = array(
	'posx' => $L1[2],
	'ancho' => $W1[2],
	'texto' => "TOTAL ($ACU_CANTIDAD REGISTROS)",
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
	'texto' => $util->nf($ACU_DEBITADO),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#F5f7f7',
	'size' => $size
);


$PDF->Imprimir_linea();

#IMPRIMIR ANALISIS POR PROVEEDOR

$PDF->Output("resumen_cruce_liquidacion_deuda_codigo.pdf");

?>