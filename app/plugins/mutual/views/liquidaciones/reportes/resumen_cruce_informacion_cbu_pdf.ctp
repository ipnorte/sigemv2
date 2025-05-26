<?php 
//debug($total_reintegros);
//exit;
//debug($resumenes);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("Resumen Cruce de Información de Liquidacion CBU - " . $util->periodo($liquidacion['Liquidacion']['periodo'],true));
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo1'] = "RESUMEN PROCESO DE CRUCE DE INFORMACION";
$PDF->titulo['titulo2'] = "ESTADO LIQUIDACION: " . ($liquidacion['Liquidacion']['cerrada'] == 1 ? 'CERRADA' : 'ABIERTA') ." | ".($liquidacion['Liquidacion']['imputada'] == 1 ? ' *** IMPUTADA ***':'');
$PDF->titulo['titulo3'] = "#".$liquidacion['Liquidacion']['id']."-".$util->periodo($liquidacion['Liquidacion']['periodo'],true) . ' | ' . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']);

//COD | STATUS | REGISTROS | IMPORTE LIQUIDADO | NO ENVIADO | IMPORTE A DEBITAR | IMPORTE DEBITADO | IMPORTE IMPUTADO

$W1 = array(10,60,30,30,30,30);
$L1 = $PDF->armaAnchoColumnas($W1);

$PDF->bMargen = 10;

$fontSizeHeader = 7;

$PDF->encabezado = array();
$PDF->encabezado[0] = array();	

$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => 'COD',
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
			'texto' => 'STATUS - DESCRIPCION',
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
			'texto' => 'REGISTROS',
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
			'texto' => 'REGISTROS (%)',
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
			'texto' => 'IMPORTE',
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
			'texto' => 'IMPORTE (%)',
			'borde' => 'TBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);

		
$PDF->AddPage();
$PDF->reset();

$ACU_CANTIDAD = 0;
$ACU_CANTIDADP = 0;
$ACU_CANTIDADT = 0;

$ACU_IMPO = 0;
$ACU_IMPOP = 0;
$ACU_IMPOT = 0;

$ACU_COBRADO = 0;
$ACU_NOCOBRADO = 0;

$ACU_COBRADO_R = 0;
$ACU_NOCOBRADO_R = 0;	

$ACU_COBRADO_P = 0;
$ACU_NOCOBRADO_P = 0;

$ACU_COBRADO_P_R = 0;
$ACU_NOCOBRADO_P_R = 0;		

$ACU_COBRADO_T = 0;
$ACU_NOCOBRADO_T = 0;

$ACU_COBRADO_R_T = 0;
$ACU_NOCOBRADO_R_T = 0;	

$banco = null;
$primero = true;

$size = 8;



foreach($resumenes['detalle'] as $resumen):

		$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => $resumen['nombre'],
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 9
		);
		$PDF->Imprimir_linea();
		
		foreach($resumen['codigos'] as $codigo):
		
			$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => $W1[0],
				'texto' => $codigo['status'],
				'borde' => '',
				'align' => 'L',
				'fondo' => ($codigo['indica_pago'] == 1 ? 1 : 0),
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
			);
			$PDF->linea[1] = array(
				'posx' => $L1[1],
				'ancho' => $W1[1],
				'texto' => substr($codigo['descripcion'],0,40),
				'borde' => '',
				'align' => 'L',
				'fondo' => ($codigo['indica_pago'] == 1 ? 1 : 0),
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
			);
			$PDF->linea[2] = array(
				'posx' => $L1[2],
				'ancho' => $W1[2],
				'texto' => $codigo['cantidad_recibida'],
				'borde' => '',
				'align' => 'C',
				'fondo' => ($codigo['indica_pago'] == 1 ? 1 : 0),
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
			);
			$PDF->linea[3] = array(
				'posx' => $L1[3],
				'ancho' => $W1[3],
				'texto' => $util->nf($codigo['cantidad_recibida_porc']),
				'borde' => '',
				'align' => 'R',
				'fondo' => ($codigo['indica_pago'] == 1 ? 1 : 0),
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
			);			
		
			$PDF->linea[4] = array(
				'posx' => $L1[4],
				'ancho' => $W1[4],
				'texto' => $util->nf($codigo['importe_debitado']),
				'borde' => '',
				'align' => 'R',
				'fondo' => ($codigo['indica_pago'] == 1 ? 1 : 0),
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
			);
			$PDF->linea[5] = array(
				'posx' => $L1[5],
				'ancho' => $W1[5],
				'texto' => $util->nf($codigo['importe_debitado_porc']),
				'borde' => '',
				'align' => 'R',
				'fondo' => ($codigo['indica_pago'] == 1 ? 1 : 0),
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
			);		
			
			$PDF->Imprimir_linea();		
		
		
		endforeach;
		
		#COBRADO
		$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => "",
			'borde' => 'T',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
		);		
		$PDF->linea[1] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => "TOTAL COBRADO",
			'borde' => 'T',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
		);
		$PDF->linea[2] = array(
			'posx' => $L1[2],
			'ancho' => $W1[2],
			'texto' => $resumen['registros_cobrados'],
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
		);
		$PDF->linea[3] = array(
			'posx' => $L1[3],
			'ancho' => $W1[3],
			'texto' => $util->nf($resumen['registros_cobrados_porc']),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
		);
		$PDF->linea[4] = array(
			'posx' => $L1[4],
			'ancho' => $W1[4],
			'texto' => $util->nf($resumen['total_cobrado']),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
		);											
		$PDF->linea[5] = array(
			'posx' => $L1[5],
			'ancho' => $W1[5],
			'texto' => $util->nf($resumen['total_cobrado_porc']),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
		);		
		$PDF->Imprimir_linea();	
		
		#NOCOBRADO
		$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => "",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
		);		
		$PDF->linea[1] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => "TOTAL NO COBRADO",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
		);
		$PDF->linea[2] = array(
			'posx' => $L1[2],
			'ancho' => $W1[2],
			'texto' => $resumen['registros_nocobrados'],
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
		);
		$PDF->linea[3] = array(
			'posx' => $L1[3],
			'ancho' => $W1[3],
			'texto' => $util->nf($resumen['registros_nocobrados_porc']),
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
		);
		$PDF->linea[4] = array(
			'posx' => $L1[4],
			'ancho' => $W1[4],
			'texto' => $util->nf($resumen['total_no_cobrado']),
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
		);											
		$PDF->linea[5] = array(
			'posx' => $L1[5],
			'ancho' => $W1[5],
			'texto' => $util->nf($resumen['total_no_cobrado_porc']),
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
		);		
		$PDF->Imprimir_linea();		
		

endforeach;

$PDF->ln(3);

#COBRADO
$PDF->linea[0] = array(
	'posx' => $L1[0],
	'ancho' => $W1[0],
	'texto' => "",
	'borde' => 'TL',
	'align' => 'L',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#D8DBD4',
	'size' => $size
);		
$PDF->linea[1] = array(
	'posx' => $L1[1],
	'ancho' => $W1[1],
	'texto' => "TOTAL GENERAL ACREDITADO EN BANCOS",
	'borde' => 'T',
	'align' => 'L',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#D8DBD4',
	'size' => $size
);
$PDF->linea[2] = array(
	'posx' => $L1[2],
	'ancho' => $W1[2],
	'texto' => $resumenes['total_registros_cob'],
	'borde' => 'T',
	'align' => 'C',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#D8DBD4',
	'size' => $size
);
$PDF->linea[3] = array(
	'posx' => $L1[3],
	'ancho' => $W1[3],
	'texto' => $util->nf($resumenes['total_registros_cob_porc']),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#D8DBD4',
	'size' => $size
);
$PDF->linea[4] = array(
	'posx' => $L1[4],
	'ancho' => $W1[4],
	'texto' => $util->nf($resumenes['total_cobrado']),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#D8DBD4',
	'size' => $size
);											
$PDF->linea[5] = array(
	'posx' => $L1[5],
	'ancho' => $W1[5],
	'texto' => $util->nf($resumenes['total_cobrado_porc']),
	'borde' => 'TR',
	'align' => 'R',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#D8DBD4',
	'size' => $size
);		
$PDF->Imprimir_linea();	

#NOCOBRADO
$PDF->linea[0] = array(
	'posx' => $L1[0],
	'ancho' => $W1[0],
	'texto' => "",
	'borde' => 'LB',
	'align' => 'L',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#D8DBD4',
	'size' => $size
);		
$PDF->linea[1] = array(
	'posx' => $L1[1],
	'ancho' => $W1[1],
	'texto' => "TOTAL GENERAL NO COBRADO",
	'borde' => 'B',
	'align' => 'L',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#D8DBD4',
	'size' => $size
);
$PDF->linea[2] = array(
	'posx' => $L1[2],
	'ancho' => $W1[2],
	'texto' => $resumenes['total_registros_nocob'],
	'borde' => 'B',
	'align' => 'C',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#D8DBD4',
	'size' => $size
);
$PDF->linea[3] = array(
	'posx' => $L1[3],
	'ancho' => $W1[3],
	'texto' => $util->nf($resumenes['total_registros_nocob_porc']),
	'borde' => 'B',
	'align' => 'R',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#D8DBD4',
	'size' => $size
);
$PDF->linea[4] = array(
	'posx' => $L1[4],
	'ancho' => $W1[4],
	'texto' => $util->nf($resumenes['total_no_cobrado']),
	'borde' => 'B',
	'align' => 'R',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#D8DBD4',
	'size' => $size
);											
$PDF->linea[5] = array(
	'posx' => $L1[5],
	'ancho' => $W1[5],
	'texto' => $util->nf($resumenes['total_no_cobrado_porc']),
	'borde' => 'RB',
	'align' => 'R',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#D8DBD4',
	'size' => $size
);		
$PDF->Imprimir_linea();


$PDF->ln(5);


//INFO DE IMPUTACION
$PDF->linea[0] = array(
	'posx' => $L1[0],
	'ancho' => $W1[0],
	'texto' => "RESUMEN DE IMPUTACION",
	'borde' => '',
	'align' => 'L',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#D8DBD4',
	'size' => $size
);
$PDF->Imprimir_linea();

$PDF->linea[0] = array(
	'posx' => $L1[0],
	'ancho' => $W1[0],
	'texto' => ($liquidacion['Liquidacion']['imputada'] == 1 ? 'IMPUTADO EN CUENTA CORRIENTE':'A IMPUTAR EN CUENTA CORRIENTE'),
	'borde' => '',
	'align' => 'L',
	'fondo' => 0,
	'style' => '',
	'colorf' => '#D8DBD4',
	'size' => $size
);		
$PDF->linea[2] = array(
	'posx' => $L1[2],
	'ancho' => $W1[2],
	'texto' => "",
	'borde' => '',
	'align' => 'C',
	'fondo' => 0,
	'style' => '',
	'colorf' => '#D8DBD4',
	'size' => $size
);
$PDF->linea[3] = array(
	'posx' => $L1[3],
	'ancho' => $W1[3],
	'texto' => $util->nf($total_imputado),
	'borde' => '',
	'align' => 'R',
	'fondo' => 0,
	'style' => '',
	'colorf' => '#D8DBD4',
	'size' => $size
);
$PDF->Imprimir_linea();

$PDF->linea[0] = array(
	'posx' => $L1[0],
	'ancho' => $W1[0],
	'texto' => "TOTAL REINTEGROS " . ($liquidacion['Liquidacion']['imputada'] == 1 ? 'EMITIDOS':'A EMITIR'),
	'borde' => '',
	'align' => 'L',
	'fondo' => 0,
	'style' => '',
	'colorf' => '#D8DBD4',
	'size' => $size
);		
$PDF->linea[2] = array(
	'posx' => $L1[2],
	'ancho' => $W1[2],
	'texto' => (isset($total_reintegros['cantidad']) ? $total_reintegros['cantidad'] : 0),
	'borde' => '',
	'align' => 'C',
	'fondo' => 0,
	'style' => '',
	'colorf' => '#D8DBD4',
	'size' => $size
);
$PDF->linea[3] = array(
	'posx' => $L1[3],
	'ancho' => $W1[3],
	'texto' => $util->nf((isset($total_reintegros['total']) ? $total_reintegros['total'] : 0)),
	'borde' => '',
	'align' => 'R',
	'fondo' => 0,
	'style' => '',
	'colorf' => '#D8DBD4',
	'size' => $size
);
$PDF->Imprimir_linea();


$PDF->linea[0] = array(
	'posx' => $L1[0],
	'ancho' => $W1[0],
	'texto' => "TOTAL REINTEGROS ANTICIPADOS",
	'borde' => '',
	'align' => 'L',
	'fondo' => 0,
	'style' => '',
	'colorf' => '#D8DBD4',
	'size' => $size
);		
$PDF->linea[2] = array(
	'posx' => $L1[2],
	'ancho' => $W1[2],
	'texto' => (isset($total_reintegros['cantidad_anticipos']) ? $total_reintegros['cantidad_anticipos'] : 0),
	'borde' => '',
	'align' => 'C',
	'fondo' => 0,
	'style' => '',
	'colorf' => '#D8DBD4',
	'size' => $size
);
$PDF->linea[3] = array(
	'posx' => $L1[3],
	'ancho' => $W1[3],
	'texto' => $util->nf((isset($total_reintegros['total_anticipos']) ? $total_reintegros['total_anticipos'] : 0)),
	'borde' => '',
	'align' => 'R',
	'fondo' => 0,
	'style' => '',
	'colorf' => '#D8DBD4',
	'size' => $size
);
$PDF->Imprimir_linea();


$PDF->linea[0] = array(
	'posx' => $L1[0],
	'ancho' => $W1[0],
	'texto' => "",
	'borde' => 'T',
	'align' => 'L',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#D8DBD4',
	'size' => $size
);
$PDF->linea[1] = array(
	'posx' => $L1[1],
	'ancho' => $W1[1],
	'texto' => "TOTAL ACREDITADO EN BANCOS",
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#D8DBD4',
	'size' => $size
);		
$PDF->linea[2] = array(
	'posx' => $L1[2],
	'ancho' => $W1[2],
	'texto' => "",
	'borde' => 'T',
	'align' => 'C',
	'fondo' => 1,
	'style' => '',
	'colorf' => '#D8DBD4',
	'size' => $size
);
$PDF->linea[3] = array(
	'posx' => $L1[3],
	'ancho' => $W1[3],
	'texto' => $util->nf((isset($total_reintegros['total']) ? $total_reintegros['total'] : 0) + (isset($total_reintegros['total_anticipos']) ? $total_reintegros['total_anticipos'] : 0) + $total_imputado),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#D8DBD4',
	'size' => $size
);
$PDF->Imprimir_linea();


$PDF->ln(3);


if(!empty($total_nocobrados)):

	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0],
		'texto' => "ALTAS NO COBRADAS",
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $size
	);		
	$PDF->linea[2] = array(
		'posx' => $L1[2],
		'ancho' => $W1[2],
		'texto' => $total_nocobrados['cantidad'],
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $size
	);
	$PDF->linea[3] = array(
		'posx' => $L1[3],
		'ancho' => $W1[3],
		'texto' => $util->nf($total_nocobrados['total']),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $size
	);
	$PDF->Imprimir_linea();

endif;

if(!empty($total_liquidados_no_rendidos)):

	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0],
		'texto' => "LIQUIDACIONES NO ENVIADAS A DEBITAR",
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $size
	);		
	$PDF->linea[2] = array(
		'posx' => $L1[2],
		'ancho' => $W1[2],
		'texto' => $total_liquidados_no_rendidos['cantidad'],
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $size
	);
	$PDF->linea[3] = array(
		'posx' => $L1[3],
		'ancho' => $W1[3],
		'texto' => $util->nf($total_liquidados_no_rendidos['total']),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $size
	);
	$PDF->Imprimir_linea();

endif;


$PDF->Output("resumen_cruce_liquidacion_deuda_cbu_".$liquidacion['Liquidacion']['periodo'].".pdf");

?>