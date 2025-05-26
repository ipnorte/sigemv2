<?php 
App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("Resumen Cruce de Información de Liquidacion CJPC - " . $util->periodo($liquidacion['Liquidacion']['periodo'],true));
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo1'] = "RESUMEN PROCESO DE CRUCE DE INFORMACION";
$PDF->titulo['titulo2'] = "ESTADO LIQUIDACION: " . ($liquidacion['Liquidacion']['cerrada'] == 1 ? 'CERRADA' : 'ABIERTA') ." | ".($liquidacion['Liquidacion']['imputada'] == 1 ? ' *** IMPUTADA ***':'');
$PDF->titulo['titulo3'] = "#".$liquidacion['Liquidacion']['id']."-".$util->periodo($liquidacion['Liquidacion']['periodo'],true) . ' | ' . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']);

//TITULO | CANTIDAD | LIQUIDADO | DEBITADO | IMPUTADO 

$W1 = array(90,25,25,25,25);
$L1 = $PDF->armaAnchoColumnas($W1);

$PDF->bMargen = 10;

$fontSizeHeader = 7;

$PDF->encabezado = array();
$PDF->encabezado[0] = array();	

$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => 'ANALISIS DEL CRUCE DE INFORMACION',
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
			'texto' => 'REGISTROS',
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
			'texto' => 'LIQUIDADO',
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
			'texto' => 'DEBITADO',
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
			'texto' => 'IMPUTADO',
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

$PDF->linea[0] = array(
	'posx' => $L1[0],
	'ancho' => $W1[0],
	'texto' => 'LIQUIDADOS ENVIADO EN ARCHIVOS DE RENDICION',
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
	'texto' => $resumenes[0]['detalle'][0]['cantidad'],
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
	'texto' => $util->nf($resumenes[0]['detalle'][0]['importe_dto']),
	'borde' => '',
	'align' => 'R',
	'fondo' => 0,
	'style' => '',
	'colorf' => '#ccc',
	'size' => $size
);
$PDF->linea[3] = array(
	'posx' => $L1[3],
	'ancho' => $W1[3],
	'texto' => $util->nf($resumenes[0]['detalle'][0]['importe_debitado']),
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
	'texto' => $util->nf($resumenes[0]['detalle'][0]['importe_imputado']),
	'borde' => '',
	'align' => 'R',
	'fondo' => 0,
	'style' => '',
	'colorf' => '#ccc',
	'size' => $size
);

$PDF->Imprimir_linea();

$PDF->linea[0] = array(
	'posx' => $L1[0],
	'ancho' => $W1[0],
	'texto' => 'LIQUIDADOS NO ENCONTRADOS EN LOS ARCHIVOS DE RENDICION',
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
	'texto' => $resumenes_noencontrados[0]['detalle'][0]['cantidad'],
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
	'texto' => $util->nf($resumenes_noencontrados[0]['detalle'][0]['importe_dto']),
	'borde' => '',
	'align' => 'R',
	'fondo' => 0,
	'style' => '',
	'colorf' => '#ccc',
	'size' => $size
);
$PDF->linea[3] = array(
	'posx' => $L1[3],
	'ancho' => $W1[3],
	'texto' => $util->nf($resumenes_noencontrados[0]['detalle'][0]['importe_debitado']),
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
	'texto' => $util->nf($resumenes_noencontrados[0]['detalle'][0]['importe_imputado']),
	'borde' => '',
	'align' => 'R',
	'fondo' => 0,
	'style' => '',
	'colorf' => '#ccc',
	'size' => $size
);

$PDF->Imprimir_linea();


$PDF->linea[0] = array(
	'posx' => $L1[0],
	'ancho' => $W1[0],
	'texto' => 'TOTAL LIQUIDACION',
	'borde' => 'T',
	'align' => 'L',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size
);
$PDF->linea[1] = array(
	'posx' => $L1[1],
	'ancho' => $W1[1],
	'texto' => $resumenes[0]['detalle'][0]['cantidad'] + $resumenes_noencontrados[0]['detalle'][0]['cantidad'],
	'borde' => 'T',
	'align' => 'C',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size
);

$PDF->linea[2] = array(
	'posx' => $L1[2],
	'ancho' => $W1[2],
	'texto' => $util->nf($resumenes[0]['detalle'][0]['importe_dto'] + $resumenes_noencontrados[0]['detalle'][0]['importe_dto']),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size
);
$PDF->linea[3] = array(
	'posx' => $L1[3],
	'ancho' => $W1[3],
	'texto' => $util->nf($resumenes[0]['detalle'][0]['importe_debitado'] + $resumenes_noencontrados[0]['detalle'][0]['importe_debitado']),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size
);			
$PDF->linea[4] = array(
	'posx' => $L1[4],
	'ancho' => $W1[4],
	'texto' => $util->nf($resumenes[0]['detalle'][0]['importe_imputado'] + $resumenes_noencontrados[0]['detalle'][0]['importe_imputado']),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size
);

$PDF->Imprimir_linea();

// RENDICION

$PDF->linea[0] = array(
	'posx' => $L1[0],
	'ancho' => $W1[0],
	'texto' => 'ENVIADOS EN ARCHIVO ENCONTRADOS EN LIQUIDACION',
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
	'texto' => $resumenes[0]['detalle'][0]['cantidad'],
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
	'texto' => '',
	'borde' => '',
	'align' => 'R',
	'fondo' => 0,
	'style' => '',
	'colorf' => '#ccc',
	'size' => $size
);
$PDF->linea[3] = array(
	'posx' => $L1[3],
	'ancho' => $W1[3],
	'texto' => $util->nf($resumenes[0]['detalle'][0]['importe_debitado']),
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
	'texto' => $util->nf($resumenes[0]['detalle'][0]['importe_imputado']),
	'borde' => '',
	'align' => 'R',
	'fondo' => 0,
	'style' => '',
	'colorf' => '#ccc',
	'size' => $size
);

$PDF->Imprimir_linea();

$PDF->linea[0] = array(
	'posx' => $L1[0],
	'ancho' => $W1[0],
	'texto' => 'ENVIADOS EN ARCHIVO NO ENCONTRADOS EN LIQUIDACION',
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
	'texto' => $total_enviado_no_liquidado['cantidad'],
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
	'texto' => '',
	'borde' => '',
	'align' => 'R',
	'fondo' => 0,
	'style' => '',
	'colorf' => '#ccc',
	'size' => $size
);
$PDF->linea[3] = array(
	'posx' => $L1[3],
	'ancho' => $W1[3],
	'texto' => $util->nf($total_enviado_no_liquidado['total']),
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
	'texto' => '',
	'borde' => '',
	'align' => 'R',
	'fondo' => 0,
	'style' => '',
	'colorf' => '#ccc',
	'size' => $size
);

$PDF->Imprimir_linea();


$PDF->linea[0] = array(
	'posx' => $L1[0],
	'ancho' => $W1[0],
	'texto' => 'TOTAL RENDICION',
	'borde' => 'T',
	'align' => 'L',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size
);
$PDF->linea[1] = array(
	'posx' => $L1[1],
	'ancho' => $W1[1],
	'texto' => $resumenes[0]['detalle'][0]['cantidad'] + $total_enviado_no_liquidado['cantidad'],
	'borde' => 'T',
	'align' => 'C',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size
);

$PDF->linea[2] = array(
	'posx' => $L1[2],
	'ancho' => $W1[2],
	'texto' => '',
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size
);
$PDF->linea[3] = array(
	'posx' => $L1[3],
	'ancho' => $W1[3],
	'texto' => $util->nf($resumenes[0]['detalle'][0]['importe_debitado'] + $total_enviado_no_liquidado['total']),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size
);			
$PDF->linea[4] = array(
	'posx' => $L1[4],
	'ancho' => $W1[4],
	'texto' => '',
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size
);

$PDF->Imprimir_linea();

if(!empty($total_nocobrados)):
	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0],
		'texto' => 'ALTAS NO COBRADAS',
		'borde' => 'T',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $size
	);
	$PDF->linea[1] = array(
		'posx' => $L1[1],
		'ancho' => $W1[1],
		'texto' => $total_nocobrados['cantidad'],
		'borde' => 'T',
		'align' => 'C',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $size
	);		
	$PDF->linea[2] = array(
		'posx' => $L1[2],
		'ancho' => $W1[2],
		'texto' => '',
		'borde' => 'T',
		'align' => 'R',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $size
	);
	$PDF->linea[3] = array(
		'posx' => $L1[3],
		'ancho' => $W1[3],
		'texto' => '',
		'borde' => 'T',
		'align' => 'R',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $size
	);	
	$PDF->linea[4] = array(
		'posx' => $L1[4],
		'ancho' => $W1[4],
		'texto' => $util->nf($total_nocobrados['total']),
		'borde' => 'T',
		'align' => 'R',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $size
	);
	$PDF->Imprimir_linea();
endif;

// REINTEGROS
if(!empty($total_reintegros)):
	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0],
		'texto' => 'REINTEGROS DETECTADOS',
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $size
	);
	$PDF->linea[1] = array(
		'posx' => $L1[1],
		'ancho' => $W1[1],
		'texto' => $total_reintegros['cantidad'],
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $size
	);
	
	$PDF->linea[2] = array(
		'posx' => $L1[2],
		'ancho' => $W1[2],
		'texto' => '',
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
		'texto' => '',
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $size
	);			
	$PDF->linea[4] = array(
		'posx' => $L1[4],
		'ancho' => $W1[4],
		'texto' => $util->nf($total_reintegros['total']),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $size
	);
	
	$PDF->Imprimir_linea();
endif;
$PDF->Output("resumen_cruce_liquidacion_deuda_cjp_".$liquidacion['Liquidacion']['periodo'].".pdf");

?>