<?php 
//debug($reintegros);
//exit;
App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF("L");

$PDF->SetTitle("Reintegros ". $util->periodo($liquidacion['Liquidacion']['periodo'],true));
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo1'] = "";
$PDF->titulo['titulo3'] = "REINTEGROS";
$PDF->titulo['titulo2'] = "#".$liquidacion['Liquidacion']['id']."-".$util->periodo($liquidacion['Liquidacion']['periodo'],true) . ' | ' . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']);

//DNI | APENOM| BENEFICIO | IMPORTE LIQUIDADO | IMPORTE DEBITADO | IMPUTADO | REINTEGRO
//277
$W1 = array(15,42,101,17,17,17,17,17,17,17);
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
			'texto' => 'DEBITADO',
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
			'texto' => 'REINTEGRO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][7] = array(
			'posx' => $L1[7],
			'ancho' => $W1[7],
			'texto' => 'ANTICIPOS',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][8] = array(
			'posx' => $L1[8],
			'ancho' => $W1[8],
			'texto' => 'PAGOS',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][9] = array(
			'posx' => $L1[9],
			'ancho' => $W1[9],
			'texto' => 'SALDO',
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
$ACU_LIQUIDADO 		= 0;
$ACU_NOENVIADO 		= 0;
$ACU_ADEBITAR 		= 0;
$ACU_DEBITADO 		= 0;
$ACU_REINTEGRO		= 0;
$ACU_IMPUTADO		= 0;
$ACU_ANTICIPO		= 0;
$ACU_PAGOS			= 0;
$ACU_SALDO			= 0;

foreach($reintegros as $socio):

	$ACU_CANTIDAD++;
	$ACU_LIQUIDADO 	+= $socio['LiquidacionSocio']['saldo_actual'];
	$ACU_NOENVIADO 	+= $socio['LiquidacionSocio']['importe_noenviado'];
	$ACU_ADEBITAR 	+= $socio['LiquidacionSocio']['importe_adebitar'];
	$ACU_DEBITADO 	+= $socio['LiquidacionSocio']['importe_debitado'];
	$ACU_REINTEGRO 	+= $socio['LiquidacionSocio']['importe_reintegro'];
	$ACU_IMPUTADO 	+= $socio['LiquidacionSocio']['importe_imputado'];
	$ACU_ANTICIPO 	+= $socio['LiquidacionSocio']['importe_anticipado'];
	$ACU_PAGOS 	+= $socio['LiquidacionSocio']['importe_pagado_socio'];
	$ACU_SALDO 	+= $socio['LiquidacionSocio']['saldo_reintegro'];

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
		'texto' => substr($socio['LiquidacionSocio']['apenom'],0,28),
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
		'texto' => substr($socio['LiquidacionSocio']['beneficio_str'],0,65),
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
		'texto' => $util->nf($socio['LiquidacionSocio']['saldo_actual']),
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
		'texto' => $util->nf($socio['LiquidacionSocio']['importe_reintegro']),
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
		'texto' => $util->nf($socio['LiquidacionSocio']['importe_anticipado']),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);
	$PDF->linea[8] = array(
		'posx' => $L1[8],
		'ancho' => $W1[8],
		'texto' => $util->nf($socio['LiquidacionSocio']['importe_pagado_socio']),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);	
	$PDF->linea[9] = array(
		'posx' => $L1[9],
		'ancho' => $W1[9],
		'texto' => $util->nf($socio['LiquidacionSocio']['saldo_reintegro']),
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
	'texto' => $util->nf($ACU_REINTEGRO),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#F5f7f7',
	'size' => $size
);
$PDF->linea[7] = array(
	'posx' => $L1[7],
	'ancho' => $W1[7],
	'texto' => $util->nf($ACU_ANTICIPO),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#F5f7f7',
	'size' => $size
);
$PDF->linea[8] = array(
	'posx' => $L1[8],
	'ancho' => $W1[8],
	'texto' => $util->nf($ACU_PAGOS),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#F5f7f7',
	'size' => $size
);
$PDF->linea[9] = array(
	'posx' => $L1[9],
	'ancho' => $W1[9],
	'texto' => $util->nf($ACU_SALDO),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#F5f7f7',
	'size' => $size
);
$PDF->Imprimir_linea();

#IMPRIMO LOS ANTICIPOS 
if(!empty($anticipos)):

	$PDF->AddPage();
	$PDF->reset();
	
	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0],
		'texto' => "DETALLE DE LOS REINTEGROS ANTICIPADOS EMITIDOS",
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => 10
	);	
	$PDF->Imprimir_linea();
	
	$size = 7;
	
	$ACU_CANTIDAD 		= 0;
	$ACU_LIQUIDADO 		= 0;
	$ACU_NOENVIADO 		= 0;
	$ACU_ADEBITAR 		= 0;
	$ACU_DEBITADO 		= 0;
	$ACU_REINTEGRO		= 0;
	$ACU_IMPUTADO		= 0;
	$ACU_ANTICIPO		= 0;
	$ACU_SALDO			= 0;
	
	foreach($anticipos as $socio):
	
		$ACU_CANTIDAD++;
		$ACU_LIQUIDADO 	+= $socio['LiquidacionSocio']['saldo_actual'];
		$ACU_NOENVIADO 	+= $socio['LiquidacionSocio']['importe_noenviado'];
		$ACU_ADEBITAR 	+= $socio['LiquidacionSocio']['importe_adebitar'];
		$ACU_DEBITADO 	+= $socio['LiquidacionSocio']['importe_debitado'];
		$ACU_REINTEGRO 	+= $socio['LiquidacionSocio']['importe_reintegro'];
		$ACU_IMPUTADO 	+= $socio['LiquidacionSocio']['importe_imputado'];
		$ACU_ANTICIPO 	+= $socio['LiquidacionSocio']['importe_anticipado'];
		$ACU_SALDO 	+= $socio['LiquidacionSocio']['saldo_reintegro'];
	
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
			'texto' => substr($socio['LiquidacionSocio']['apenom'],0,28),
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
			'texto' => substr($socio['LiquidacionSocio']['beneficio_str'],0,65),
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
			'texto' => $util->nf($socio['LiquidacionSocio']['saldo_actual']),
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
			'texto' => $util->nf($socio['LiquidacionSocio']['importe_reintegro']),
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
			'texto' => $util->nf($socio['LiquidacionSocio']['importe_anticipado']),
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $size
		);
		$PDF->linea[8] = array(
			'posx' => $L1[8],
			'ancho' => $W1[8],
			'texto' => $util->nf($socio['LiquidacionSocio']['saldo_reintegro']),
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
		'texto' => $util->nf($ACU_REINTEGRO),
		'borde' => 'T',
		'align' => 'R',
		'fondo' => 1,
		'style' => 'B',
		'colorf' => '#F5f7f7',
		'size' => $size
	);
	$PDF->linea[7] = array(
		'posx' => $L1[7],
		'ancho' => $W1[7],
		'texto' => $util->nf($ACU_ANTICIPO),
		'borde' => 'T',
		'align' => 'R',
		'fondo' => 1,
		'style' => 'B',
		'colorf' => '#F5f7f7',
		'size' => $size
	);
	$PDF->linea[8] = array(
		'posx' => $L1[8],
		'ancho' => $W1[8],
		'texto' => $util->nf($ACU_SALDO),
		'borde' => 'T',
		'align' => 'R',
		'fondo' => 1,
		'style' => 'B',
		'colorf' => '#F5f7f7',
		'size' => $size
	);
	$PDF->Imprimir_linea();

endif;

$PDF->Output("reintegros_pdf_".$liquidacion['Liquidacion']['periodo'].".pdf");

?>