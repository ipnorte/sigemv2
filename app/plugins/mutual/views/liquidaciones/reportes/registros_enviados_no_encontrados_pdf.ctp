<?php 

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("Registros enviados no Liquidados - " . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']) . " - " . $util->periodo($liquidacion['Liquidacion']['periodo'],true));
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo1'] = "";
$PDF->titulo['titulo3'] = "ENVIADOS NO LIQUIDADOS";
$PDF->titulo['titulo2'] = "#".$liquidacion['Liquidacion']['id']."-".$util->periodo($liquidacion['Liquidacion']['periodo'],true) . ' | ' . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']);

//DNI | APENOM| BANCO INTERCAMBIO | IMPORTE DEBITADO

$W1 = array(15,50,80,10,20,15);
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
			'texto' => 'STATUS',
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
			'texto' => 'DESCRIPCION',
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
			'texto' => 'DEBITADO',
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
$ACU_DEBITADO 		= 0;

$ACU_PARCIAL_CANTIDAD = 0;
$ACU_PARCIAL_DEBITADO = 0;

$CODIGO_ACTUAL = '';
$PRIMERO = TRUE;

foreach($enviado_no_liquidado as $registro):


	if($CODIGO_ACTUAL != $registro['LiquidacionSocioRendicion']['status']){

		$CODIGO_ACTUAL = $registro['LiquidacionSocioRendicion']['status'];
		
		if($PRIMERO){

			$PRIMERO = false;
		
		}else{

			$PDF->linea[3] = array(
				'posx' => $L1[3],
				'ancho' => $W1[3],
				'texto' => "TOTAL ($ACU_PARCIAL_CANTIDAD REGISTROS)",
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
				'texto' => '',
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
				'texto' => $util->nf($ACU_PARCIAL_DEBITADO),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $size
			);
			
			$PDF->Imprimir_linea();
			$ACU_PARCIAL_CANTIDAD = 0;
			$ACU_PARCIAL_DEBITADO = 0;									
			
		}
		
	}


	$ACU_CANTIDAD++;
	$ACU_DEBITADO 	+= $registro['LiquidacionSocioRendicion']['importe_debitado'];
	
	$ACU_PARCIAL_CANTIDAD++;
	$ACU_PARCIAL_DEBITADO 	+= $registro['LiquidacionSocioRendicion']['importe_debitado'];		
	
	
	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0],
		'texto' => $registro['LiquidacionSocioRendicion']['documento'],
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);
	
	if(empty($registro['Persona']['apellido']) && empty($registro['Persona']['nombre'])){
		$persona = "*** NO EXISTENTE EN EL PADRON ***";
	}else{
		$persona = $registro['Persona']['apellido'].', '.$registro['Persona']['nombre'];
	}
	
	$PDF->linea[1] = array(
		'posx' => $L1[1],
		'ancho' => $W1[1],
		'texto' => $persona,
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
		'texto' => $registro['LiquidacionSocioRendicion']['identificacion'],
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
		'texto' => $registro['LiquidacionSocioRendicion']['status'],
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
		'texto' => $registro['BancoRendicionCodigo']['descripcion'],
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);	
	$PDF->linea[5] = array(
		'posx' => $L1[5],
		'ancho' => $W1[5],
		'texto' => $util->nf($registro['LiquidacionSocioRendicion']['importe_debitado']),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);			

	$PDF->Imprimir_linea();	

endforeach;


$PDF->linea[3] = array(
	'posx' => $L1[3],
	'ancho' => $W1[3],
	'texto' => "TOTAL ($ACU_PARCIAL_CANTIDAD REGISTROS)",
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
	'texto' => '',
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
	'texto' => $util->nf($ACU_PARCIAL_DEBITADO),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#F5f7f7',
	'size' => $size
);

$PDF->Imprimir_linea();
$ACU_PARCIAL_CANTIDAD = 0;
$ACU_PARCIAL_DEBITADO = 0;						



$PDF->linea[3] = array(
	'posx' => $L1[3],
	'ancho' => $W1[3],
	'texto' => "TOTAL GENERAL ($ACU_CANTIDAD REGISTROS)",
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
	'texto' => '',
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

$PDF->Output("registros_enviados_no_encontrados_pdf_".$liquidacion['Liquidacion']['periodo'].".pdf");

?>