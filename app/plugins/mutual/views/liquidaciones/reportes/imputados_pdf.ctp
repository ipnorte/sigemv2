<?php 
//debug($datos);
//exit;

Configure::write('debug',0);

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("Imputados ". $util->periodo($liquidacion['Liquidacion']['periodo'],true));
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo1'] = "";
$PDF->titulo['titulo3'] = "LISTADO CONTROL DE IMPUTACION";
$PDF->titulo['titulo2'] = "#".$liquidacion['Liquidacion']['id']."-".$util->periodo($liquidacion['Liquidacion']['periodo'],true) . ' | ' . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']);

$W1 = array(20,45,25,25,25,25,25);
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
			'texto' => 'A DEBITAR',
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
$ACU_ADEBITAR 		= 0;
$ACU_DEBITADO 		= 0;
$ACU_REINTEGRO		= 0;
$ACU_IMPUTADO		= 0;


foreach($datos as $dato):

	$ACU_CANTIDAD++;
	$ACU_LIQUIDADO 	+= $dato['decimal_1'];
	$ACU_ADEBITAR 	+= $dato['decimal_2'];
	$ACU_DEBITADO 	+= $dato['decimal_3'];
	$ACU_IMPUTADO 	+= $dato['decimal_4'];	
	$ACU_REINTEGRO 	+= $dato['decimal_5'];

	
	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0],
		'texto' => $dato['texto_1'],
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
		'texto' => $dato['texto_2'],
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
		'texto' => $util->nf($dato['decimal_1']),
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
		'texto' => $util->nf($dato['decimal_2']),
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
		'texto' => $util->nf($dato['decimal_3']),
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
		'texto' => $util->nf($dato['decimal_4']),
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
		'texto' => $util->nf($dato['decimal_5']),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => "",
		'colorf' => '#ccc',
		'size' => $size
	);
	$PDF->Imprimir_linea();	

endforeach;

$PDF->linea[1] = array(
	'posx' => $L1[1],
	'ancho' => $W1[1],
	'texto' => "TOTAL ($ACU_CANTIDAD SOCIOS)",
	'borde' => '',
	'align' => 'R',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size
);

$PDF->linea[2] = array(
	'posx' => $L1[2],
	'ancho' => $W1[2],
	'texto' => $util->nf($ACU_LIQUIDADO),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#F5f7f7',
	'size' => $size
);
$PDF->linea[3] = array(
	'posx' => $L1[3],
	'ancho' => $W1[3],
	'texto' => $util->nf($ACU_ADEBITAR),
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
$PDF->Imprimir_linea();


if(!empty($datos2)):

	$W1 = array(165,25);
	$L1 = $PDF->armaAnchoColumnas($W1);


	$fontSizeHeader = 7;
	
	$PDF->encabezado = array();
	$PDF->encabezado[0] = array();	
	
	$PDF->encabezado[0][0] = array(
				'posx' => $L1[0],
				'ancho' => $W1[0],
				'texto' => 'PROVEEDOR',
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
	
	$size = 8;
	
	$ACU_CANTIDAD 		= 0;
	$ACU_LIQUIDADO 		= 0;
	$ACU_SALDOACTUAL 	= 0;
	$ACU_DEBITADO 		= 0;
	$ACU_SALDO			= 0;
	
	
	foreach($datos2 as $dato):
	
		$saldo = $dato['decimal_2'] - $dato['decimal_3'];
		$saldo = ($saldo < 0 ? 0 : $saldo);
		
		$ACU_CANTIDAD++;
		$ACU_LIQUIDADO 		+= $dato['decimal_1'];
		$ACU_SALDOACTUAL 	+= $dato['decimal_2'];
		$ACU_DEBITADO 		+= $dato['decimal_3'];
		$ACU_SALDO			+= $saldo;
	
		$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => $dato['texto_1'],
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
			'texto' => $util->nf($dato['decimal_3']),
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $size
		);			
		$PDF->Imprimir_linea();	
	
	endforeach;
	
	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0],
		'texto' => "TOTAL",
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $size
	);
	$PDF->linea[1] = array(
		'posx' => $L1[1],
		'ancho' => $W1[1],
		'texto' => $util->nf($ACU_DEBITADO),
		'borde' => 'T',
		'align' => 'R',
		'fondo' => 1,
		'style' => 'B',
		'colorf' => '#F5f7f7',
		'size' => $size
	);			
	$PDF->Imprimir_linea();	
	
	

endif;


$PDF->Output("imputados_pdf_".$liquidacion['Liquidacion']['periodo'].".pdf");

?>