<?php 

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("Ordenes de Cobros");
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo3'] = 'LISTADO DE COBROS';
$PDF->titulo['titulo2'] = "PERIODO: ". $util->periodo($periodo_cobro,true);
$PDF->titulo['titulo1'] = $util->globalDato($tipo_cobro);

# PROVEEDOR | CONCEPTO | IMPORTE
$W1 = array(80,70,40);
$L1 = $PDF->armaAnchoColumnas($W1);

$PDF->encabezado = array();
$PDF->encabezado[0] = array();	
$fontSizeHeader = 7;
//imprimo la primera linea del encabezado
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
			'texto' => 'CONCEPTO',
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
			'texto' => 'IMPORTE',
			'borde' => 'TBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);

$PDF->AddPage();
$PDF->Reset();

$fontSizeBody = 9;
$proveedorActual = 0;
$primero = true;

$ACU_TOTAL = 0;

$ACU_TOTAL_P = 0;

foreach($datos as $dato):

	$PDF->Reset();
	
	$ACU_TOTAL += $dato['AsincronoTemporal']['decimal_1'];
	
	if($proveedorActual != $dato['AsincronoTemporal']['entero_1']):
		$proveedorActual = $dato['AsincronoTemporal']['entero_1'];
		if($primero):
			$primero = false;
		else:
			//mando totales
			$PDF->linea[1] = array(
						'posx' => $L1[1],
						'ancho' => $W1[1],
						'texto' => 'SUBTOTAL',
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
			$PDF->linea[2] = array(
						'posx' => $L1[2],
						'ancho' => $W1[2],
						'texto' => $util->nf($ACU_TOTAL_P),
						'borde' => 'T',
						'align' => 'R',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
			$PDF->Imprimir_linea();		
			$ACU_TOTAL_P = 0;				
		endif;
		//imprimo el proveedor
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => 190,
					'texto' => $dato['AsincronoTemporal']['texto_3'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);		
		$PDF->Imprimir_linea();	
	endif;

	$ACU_TOTAL_P += $dato['AsincronoTemporal']['decimal_1'];

	$PDF->linea[1] = array(
				'posx' => $L1[1],
				'ancho' => $W1[1],
				'texto' => $dato['AsincronoTemporal']['texto_2'],
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[2] = array(
				'posx' => $L1[2],
				'ancho' => $W1[2],
				'texto' => $util->nf($dato['AsincronoTemporal']['decimal_1']),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->Imprimir_linea();	
endforeach;
///subtotal del ultimo
$PDF->linea[1] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => 'SUBTOTAL',
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);
$PDF->linea[2] = array(
			'posx' => $L1[2],
			'ancho' => $W1[2],
			'texto' => $util->nf($ACU_TOTAL_P),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);
$PDF->Imprimir_linea();		
$ACU_TOTAL_P = 0;
$PDF->ln(3);
//total general
$PDF->linea[1] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => 'TOTAL GENERAL',
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);
$PDF->linea[2] = array(
			'posx' => $L1[2],
			'ancho' => $W1[2],
			'texto' => $util->nf($ACU_TOTAL),
			'borde' => '',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);
$PDF->Imprimir_linea();	

$PDF->ln(3);
$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => 190,
			'texto' => 'TOTALES POR CONCEPTO',
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);		
$PDF->Imprimir_linea();
$ACU = 0;

foreach($resumen as $tipo_cobro => $importe){
	$ACU += $importe;
	$PDF->linea[1] = array(
				'posx' => $L1[1],
				'ancho' => $W1[1],
				'texto' => $util->globalDato($tipo_cobro),
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[2] = array(
				'posx' => $L1[2],
				'ancho' => $W1[2],
				'texto' => $util->nf($importe),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->Imprimir_linea();		
}
$PDF->ln(3);
//total general
$PDF->linea[1] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => 'TOTAL GENERAL',
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);
$PDF->linea[2] = array(
			'posx' => $L1[2],
			'ancho' => $W1[2],
			'texto' => $util->nf($ACU),
			'borde' => '',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);
$PDF->Imprimir_linea();	

$PDF->Output("cobros_".$periodo_cobro.".pdf");


?>