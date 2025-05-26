<?php 
//debug($reintegros);
//exit;
App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("Altas No Cobradas ". $util->periodo($liquidacion['Liquidacion']['periodo'],true));
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo1'] = "";
$PDF->titulo['titulo3'] = "ALTAS NO COBRADAS";
$PDF->titulo['titulo2'] = "#".$liquidacion['Liquidacion']['id']."-".$util->periodo($liquidacion['Liquidacion']['periodo'],true) . ' | ' . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']);

//DNI | APENOM| BENEFICIO | IMPORTE LIQUIDADO | IMPORTE DEBITADO | IMPUTADO | REINTEGRO

$W1 = array(15,35,80,15,15,15,15);
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
			'texto' => 'IMPUTADO',
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
			'texto' => 'STATUS',
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

$ACU_CANTIDAD 		= 0;
$ACU_LIQUIDADO 		= 0;
$ACU_NOENVIADO 		= 0;
$ACU_ADEBITAR 		= 0;
$ACU_DEBITADO 		= 0;
$ACU_REINTEGRO		= 0;
$ACU_IMPUTADO		= 0;

foreach($reintegros as $socio):

	$ACU_CANTIDAD++;
	$ACU_LIQUIDADO 	+= $socio['LiquidacionSocio']['importe_dto'];
	$ACU_NOENVIADO 	+= $socio['LiquidacionSocio']['importe_noenviado'];
	$ACU_ADEBITAR 	+= $socio['LiquidacionSocio']['importe_adebitar'];
	$ACU_DEBITADO 	+= $socio['LiquidacionSocio']['importe_debitado'];
	$ACU_REINTEGRO 	+= $socio['LiquidacionSocio']['importe_reintegro'];
	$ACU_IMPUTADO 	+= $socio['LiquidacionSocio']['importe_imputado'];

	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0],
		'texto' => $socio['LiquidacionSocio']['documento'],
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
		'texto' => $socio['LiquidacionSocio']['apenom'],
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size - 1
	);
	$PDF->linea[2] = array(
		'posx' => $L1[2],
		'ancho' => $W1[2],
		'texto' => $socio['LiquidacionSocio']['beneficio_str'],
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size - 1
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
		'texto' => $util->nf($socio['LiquidacionSocio']['importe_debitado']),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);			
	$PDF->linea[5] = array(
		'posx' => $L1[5],
		'ancho' => $W1[5],
		'texto' => $util->nf($socio['LiquidacionSocio']['importe_imputado']),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);			
	$PDF->linea[6] = array(
		'posx' => $L1[6],
		'ancho' => $W1[6],
		'texto' => $socio['LiquidacionSocio']['status'],
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
	'texto' => $util->nf($ACU_DEBITADO),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#F5f7f7',
	'size' => $size
);			
$PDF->linea[5] = array(
	'posx' => $L1[5],
	'ancho' => $W1[5],
	'texto' => $util->nf($ACU_IMPUTADO),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#F5f7f7',
	'size' => $size
);			
$PDF->linea[6] = array(
	'posx' => $L1[6],
	'ancho' => $W1[6],
	'texto' => "",
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#F5f7f7',
	'size' => $size
);
$PDF->Imprimir_linea();

#IMPRIMIR ANALISIS POR PROVEEDOR

$PDF->Output("altas_no_cobradas_pdf_".$liquidacion['Liquidacion']['periodo'].".pdf");

?>