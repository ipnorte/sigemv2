<?php

//debug($ordenes);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("Ordenes de Descuentos");
$PDF->SetFontSizeConf(8.5);

//$PDF->AddPage();

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo3'] = 'ORDENES DE DESCUENTO ' . ($estadoActual == 0 ? "CON SALDO" : ($estadoActual == 2 ? "PAGADAS TOTALMENTE" : ($estadoActual == 3 ? "ANULADAS" : "")));
$PDF->titulo['titulo2'] = $this->requestAction('/config/global_datos/valor/'.$socio['Persona']['tipo_documento'].'/concepto_1') .' '. $socio['Persona']['documento'] . ' - ' . $socio['Persona']['apellido'] .', '.$socio['Persona']['nombre'];
$PDF->titulo['titulo1'] = 'SOCIO #'.$socio['Socio']['id'];



#SETEO LAS COLUMNAS#
//$PDF->encabezado = array();
//
$cero = 0;
$W1 = array(15,25,60,5,23,23,20,19);
$L1 = $PDF->armaAnchoColumnas($W1);

$W2 = array(5,15,15,30,15,110);
$L2 = $PDF->armaAnchoColumnas($W2);
//
//$L1 = array(	
//					$PDF->columna(0), 
//					$PDF->columna(15), 
//					$PDF->columna(40),
//					$PDF->columna(100),
//					$PDF->columna(105),
//					$PDF->columna(127),
//					$PDF->columna(150),
//					$PDF->columna(170),
//					$PDF->columna(190),													
//				);
//$L2 = array(	
//					$PDF->columna(0), 
//					$PDF->columna(5), 
//					$PDF->columna(20),
//					$PDF->columna(35),
//					$PDF->columna(65),
//					$PDF->columna(110),
//					$PDF->columna(190),
//				);
														


$PDF->bMargen = 10;

$fontSizeHeader = 7;

$PDF->encabezado = array();
$PDF->encabezado[0] = array();	

//imprimo la primera linea del encabezado
$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => 'ORDEN',
			'borde' => 'LT',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][1] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => 'TIPO / NUMERO',
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[0][2] = array(
			'posx' => $L1[2],
			'ancho' => $W1[2],
			'texto' => 'PROVEEDOR - PRODUCTO',
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][3] = array(
			'posx' => $L1[3],
			'ancho' => $W1[3],
			'texto' => 'PER.',
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
$PDF->encabezado[0][4] = array(
			'posx' => $L1[4],
			'ancho' => $W1[4],
			'texto' => 'TOTAL',
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][5] = array(
			'posx' => $L1[5],
			'ancho' => $W1[5],
			'texto' => 'CUOTAS',
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
	
$PDF->encabezado[0][6] = array(
			'posx' => $L1[6],
			'ancho' => $W1[6],
			'texto' => 'IMPORTE',
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	
$PDF->encabezado[0][7] = array(
			'posx' => $L1[7],
			'ancho' => $W1[7],
			'texto' => 'SALDO',
			'borde' => 'TR',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);				

// IMPRIMO LA SEGUNDA LINEA DE LOS ENCABEZADOS
$PDF->encabezado[1] = array();	
$PDF->encabezado[1][0] = array(
			'posx' => $L2[0],
			'ancho' => $W2[0],
			'texto' => '',
			'borde' => 'LB',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[1][1] = array(
			'posx' => $L2[1],
			'ancho' => $W2[1],
			'texto' => 'INICIA',
			'borde' => 'B',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
$PDF->encabezado[1][2] = array(
			'posx' => $L2[2],
			'ancho' => $W2[2],
			'texto' => '1er VTO',
			'borde' => 'B',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);		

$PDF->encabezado[1][3] = array(
			'posx' => $L2[3],
			'ancho' => $W2[3],
			'texto' => 'Nro.REF.PROVEEDOR',
			'borde' => 'B',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	

$PDF->encabezado[1][4] = array(
			'posx' => $L2[4],
			'ancho' => $W2[4],
			'texto' => 'Vto.PROV.',
			'borde' => 'B',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	
$PDF->encabezado[1][5] = array(
			'posx' => $L2[5],
			'ancho' => $W2[5],
			'texto' => 'BENEFICIO',
			'borde' => 'BR',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	
	
	
$PDF->AddPage();	
	

$fsize = 10;
$fStyle = '';

$fontSizeBody = 8;

$ACUM_DEVENGADO = 0;
$ACUM_VENCIDO = 0;
$ACUM_AVENCER = 0;
$ACUM_PAGADO = 0;

$SALDO = 0;

$PDF->Reset();	

foreach($ordenes as $orden){
	
//	$ACUM_DEVENGADO += $orden['OrdenDescuento']['importe_devengado'];
//	$ACUM_VENCIDO += $orden['OrdenDescuento']['importe_vencido'];
//	$ACUM_AVENCER += $orden['OrdenDescuento']['importe_avencer'];
//	$ACUM_PAGADO += $orden['OrdenDescuento']['importe_pagado'];
    
        $SALDO += $orden['OrdenDescuento']['saldo'];
    
	$fontSizeBody = 8;
	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => $W1[0],
				'texto' => '#'.$orden['OrdenDescuento']['id'] . ($orden['OrdenDescuento']['reprogramada'] == 1 ? '(R)' : ''),
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
		
		$PDF->linea[1] = array(
					'posx' => $L1[1],
					'ancho' => $W1[1],
					'texto' => $orden['OrdenDescuento']['tipo_numero'],
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
					'texto' => substr($orden['OrdenDescuento']['proveedor_producto'],0,35),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
			
		$PDF->linea[3] = array(
				'posx' => $L1[3],
				'ancho' => $W1[3],
				'texto' => ($orden['OrdenDescuento']['permanente'] == 1 ? 'si' : ''),
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
		
		$PDF->linea[4] = array(
					'posx' => $L1[4],
					'ancho' => $W1[4],
					'texto' => number_format($orden['OrdenDescuento']['importe_total'],2),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);						
			
		$PDF->linea[5] = array(
					'posx' => $L1[5],
					'ancho' => $W1[5],
					'texto' => $orden['OrdenDescuento']['cuotas'],
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);			
			
		$PDF->linea[6] = array(
					'posx' => $L1[6],
					'ancho' => $W1[6],
					'texto' => number_format($orden['OrdenDescuento']['importe_cuota'],2),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);

		$PDF->linea[7] = array(
					'posx' => $L1[7],
					'ancho' => $W1[7],
					'texto' => number_format($orden['OrdenDescuento']['saldo'],2),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);								

	$PDF->Imprimir_linea();	
	
	$fontSizeBody = 6;
	$PDF->linea[0] = array(
				'posx' => $L2[0],
				'ancho' => $W2[0],
				'texto' => '',
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	
	$PDF->linea[1] = array(
				'posx' => $L2[1],
				'ancho' => $W2[1],
				'texto' => $util->periodo($orden['OrdenDescuento']['periodo_ini']),
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
		

		$PDF->linea[2] = array(
					'posx' => $L2[2],
					'ancho' => $W2[2],
					'texto' => $util->armaFecha($orden['OrdenDescuento']['primer_vto_socio']),
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[3] = array(
					'posx' => $L2[3],
					'ancho' => $W2[3],
					'texto' => $orden['OrdenDescuento']['nro_referencia_proveedor'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);

		$PDF->linea[4] = array(
					'posx' => $L2[4],
					'ancho' => $W2[4],
					'texto' => $util->armaFecha($orden['OrdenDescuento']['primer_vto_proveedor']),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
			
		$PDF->linea[5] = array(
					'posx' => $L2[5],
					'ancho' => $W2[5],
					'texto' => $orden['OrdenDescuento']['beneficio_str'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody - 1
			);			
			


	$PDF->Imprimir_linea();			
	$PDF->ln(3);		

}

$PDF->ln(2);

//$fontSizeHeader = 8;
$fontSizeBody = 8;

$PDF->linea[3] = array(
			'posx' => $L1[3],
			'ancho' => $W1[3] + $W1[4] + $W1[5] + $W1[6],
			'texto' => "TOTAL ADEUDADO ".$util->periodo(date('Ym'),true),
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);						
		
//$PDF->linea[5] = array(
//			'posx' => $L1[5],
//			'ancho' => $W1[5],
//			'texto' => "",
//			'borde' => '',
//			'align' => 'R',
//			'fondo' => 1,
//			'style' => 'B',
//			'colorf' => '#ccc',
//			'size' => $fontSizeBody
//	);			
//		
//$PDF->linea[6] = array(
//			'posx' => $L1[6],
//			'ancho' => $W1[6],
//			'texto' => "",
//			'borde' => '',
//			'align' => 'R',
//			'fondo' => 1,
//			'style' => 'B',
//			'colorf' => '#ccc',
//			'size' => $fontSizeBody
//	);

$PDF->linea[7] = array(
			'posx' => $L1[7],
			'ancho' => $W1[7],
			'texto' => number_format($SALDO,2),
			'borde' => '',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);								

$PDF->Imprimir_linea();
$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => '*** S.E.U.O. (SALVO ERROR U OMISION) ***',
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => 6
	);

$PDF->Imprimir_linea();

$PDF->Output("ordenes_descuento_by_socio.pdf");

?>