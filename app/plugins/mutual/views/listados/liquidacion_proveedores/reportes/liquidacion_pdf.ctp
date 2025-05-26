<?php
//debug($proveedor);
//debug($comisiones);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF("L");

$PDF->SetTitle("Liquidacion Proveedores :: " . $proveedor['Proveedor']['razon_social'] . " - " . $util->globalDato($tipo_cuota));
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo3'] =  $proveedor['Proveedor']['razon_social'];
$PDF->titulo['titulo2'] =  (empty($tipo_producto) ? "REPORTE GENERAL" : $util->globalDato($tipo_producto)." - ". $util->globalDato($tipo_cuota));
$PDF->titulo['titulo1'] = "LIQUIDACION: ".$util->globalDato($liquidacion['Liquidacion']['codigo_organismo']) . " | PERIODO: " . $util->periodo($liquidacion['Liquidacion']['periodo']);



// TIPO_DOCUMENTO | APENOM | TIPO_NRO | REF.PROV. | PROVEEDOR_PRODUCTO | CUOTA | PERIODO | LIQUIDADO | IMPUTADO | SALDO
// 277
$W1 = array(25,45,25,20,51,12,15,28,28,28);
$L1 = $PDF->armaAnchoColumnas($W1);

$PDF->encabezado = array();
$PDF->encabezado[0] = array();	
$fontSizeHeader = 7;
//imprimo la primera linea del encabezado
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
			'texto' => 'SOCIO',
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
			'texto' => 'TIPO / NUMERO',
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
			'texto' => 'REF.PROV.',
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
			'texto' => 'PRODUCTO - CONCEPTO',
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
			'texto' => 'CUOTA',
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
			'texto' => 'PERIODO',
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
			'texto' => 'LIQUIDADO',
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
			'texto' => 'COBRADO',
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
			'borde' => 'TRB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);

$DECIMAL_1T = 0;
$DECIMAL_2T = 0;
$DECIMAL_3T = 0;

$DECIMAL_1 = 0;
$DECIMAL_2 = 0;
$DECIMAL_3 = 0;



if(!empty($cobrados)):	
	$PDF->AddPage();
	$PDF->Reset();
	
	
	
	
	$fontSizeBody = 8;
	

	
	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => $W1[0],
				'texto' => "DETALLE DE CUOTAS COBRADAS",
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody + 2
		);	
	$PDF->Imprimir_linea();
	$PDF->ln(2);
	
	
	foreach($cobrados as $dato){
		
		
		$DECIMAL_1 += $dato['decimal_1'];
		$DECIMAL_2 += $dato['decimal_2'];
		$DECIMAL_3 += $dato['decimal_3'];

		
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => $W1[0],
					'texto' => $dato['texto_1'],
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
					'texto' => substr($dato['texto_2'],0,25),
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
					'texto' => $dato['texto_3'],
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
					'texto' => $dato['texto_9'],
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
					'texto' => substr($dato['texto_4'],0,30),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);		
		$PDF->linea[5] = array(
					'posx' => $L1[5],
					'ancho' => $W1[5],
					'texto' => $dato['texto_6'],
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[6] = array(
					'posx' => $L1[6],
					'ancho' => $W1[6],
					'texto' => $util->periodo($dato['texto_7']),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
	
		$PDF->linea[7] = array(
					'posx' => $L1[7],
					'ancho' => $W1[7],
					'texto' => $util->nf($dato['decimal_1']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[8] = array(
					'posx' => $L1[8],
					'ancho' => $W1[8],
					'texto' => $util->nf($dato['decimal_2']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[9] = array(
					'posx' => $L1[9],
					'ancho' => $W1[9],
					'texto' => $util->nf($dato['decimal_3']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);																		
		$PDF->Imprimir_linea();		
		
	}
	
	
	$fontSizeBody = 9;
	
	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => 193,
				'texto' => 'SUBTOTAL COBRADO',
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[7] = array(
				'posx' => $L1[7],
				'ancho' => $W1[7],
				'texto' => $util->nf($DECIMAL_1),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[8] = array(
				'posx' => $L1[8],
				'ancho' => $W1[8],
				'texto' => $util->nf($DECIMAL_2),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[9] = array(
				'posx' => $L1[9],
				'ancho' => $W1[9],
				'texto' => $util->nf($DECIMAL_3),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);																		
	$PDF->Imprimir_linea();
	
	//IMPRIMO LAS COMISIONES
	if(!empty($comisiones)):
	
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => $W1[0] + $W1[1],
					'texto' => "LIQUIDACION DE COMISIONES ",
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody + 2
			);	
		$PDF->Imprimir_linea();
		$PDF->ln(2);
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => $W1[0] + $W1[1],
					'texto' => "TOTAL DESCONTADO POR PLANILLA",
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[3] = array(
					'posx' => $L1[3],
					'ancho' => $W1[3],
					'texto' => $util->nf($DECIMAL_2),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);				
		$PDF->Imprimir_linea();
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => $W1[0] + $W1[1],
					'texto' => "MENOS TOTAL REVERSADO",
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[3] = array(
					'posx' => $L1[3],
					'ancho' => $W1[3],
					'texto' => $util->nf($total_reverso),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);				
		$PDF->Imprimir_linea();						
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => $W1[0] + $W1[1],
					'texto' => "SUBTOTAL COBRADO",
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
			
		$SUBTOTAL = 0;
		
//		foreach($comisiones as $comision):
//			$SUBTOTAL += $comision[0]['decimal_2'];
//		endforeach;
		
		$SUBTOTAL = $DECIMAL_2 - $total_reverso;			
			
		$PDF->linea[3] = array(
					'posx' => $L1[3],
					'ancho' => $W1[3],
					'texto' => $util->nf($SUBTOTAL),
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);				
		$PDF->Imprimir_linea();	
		$ACU_COMISION = 0;
		

		$COMISION = 0;
		
		foreach($comisiones as $comision):
		
//			$COMISION = $SUBTOTAL * $comision['AsincronoTemporal']['decimal_4'] / 100;
//			$ACU_COMISION += $COMISION;
//			$ACU_COMISION += $comision[0]['decimal_5'] - $comision[0]['decimal_7'];
			
			$ACU_COMISION += $comision[0]['decimal_5'];
			
			if($comision['AsincronoTemporal']['decimal_4'] != 0):
				$PDF->linea[0] = array(
							'posx' => $L1[0],
							'ancho' => $W1[0] + $W1[1],
							'texto' => "(-) COMISION " .$util->nf($comision['AsincronoTemporal']['decimal_4']) ."%",
							'borde' => '',
							'align' => 'L',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);
	//			$PDF->linea[2] = array(
	//						'posx' => $L1[2],
	//						'ancho' => $W1[2],
	//						'texto' => $util->nf($comision[0]['decimal_5']  - $comision[0]['decimal_7']),
	//						'borde' => '',
	//						'align' => 'R',
	//						'fondo' => 0,
	//						'style' => 'B',
	//						'colorf' => '#ccc',
	//						'size' => $fontSizeBody
	//				);	
	
					
//				$PDF->linea[1] = array(
//							'posx' => $L1[1],
//							'ancho' => $W1[1],
//							'texto' => $util->nf($COMISION),
//							'borde' => '',
//							'align' => 'R',
//							'fondo' => 0,
//							'style' => 'B',
//							'colorf' => '#ccc',
//							'size' => $fontSizeBody
//					);
					
				$PDF->linea[2] = array(
							'posx' => $L1[2],
							'ancho' => $W1[2],
							'texto' => $util->nf($comision[0]['decimal_2']),
							'borde' => '',
							'align' => 'R',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);
				$PDF->linea[3] = array(
							'posx' => $L1[3],
							'ancho' => $W1[3],
							'texto' => $util->nf($comision[0]['decimal_5']),
							'borde' => '',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);					
					
				$PDF->Imprimir_linea();			
			endif;
			
		endforeach;
		
		$comision_reversada = ($comision_reversada < 0 ? $comision_reversada * (-1) : $comision_reversada);
		
		if($comision_reversada != 0):
		
			$PDF->linea[0] = array(
						'posx' => $L1[0],
						'ancho' => $W1[0] + $W1[1],
						'texto' => "(+) COMISIONES S/CUOTAS REVERSADAS",
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
				$PDF->linea[3] = array(
							'posx' => $L1[3],
							'ancho' => $W1[3],
							'texto' => $util->nf($comision_reversada),
							'borde' => '',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);	
				$PDF->Imprimir_linea();						
		endif;
		
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => $W1[0] + $W1[1],
					'texto' => "NETO PROVEEDOR",
					'borde' => '',
					'align' => 'L',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody + 1,
			);
		$PDF->linea[2] = array(
					'posx' => $L1[2],
					'ancho' => $W1[2] + $W1[1],
					'texto' => "",
					'borde' => '',
					'align' => 'L',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody + 1,
			);			
		$PDF->linea[3] = array(
					'posx' => $L1[3],
					'ancho' => $W1[3],
					'texto' => $util->nf($SUBTOTAL - $ACU_COMISION + $comision_reversada),
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody + 1
			);				
		$PDF->Imprimir_linea();		
	
	endif;
	
	if(!empty($reversos)):
	
		$PDF->ln(5);
		
		$fontSizeBody = 8;
		
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => $W1[0] + $W1[1],
					'texto' => "DETALLE DE CUOTAS REVERSADAS",
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody + 2
			);	
		$PDF->Imprimir_linea();
		$PDF->ln(2);		
	
		$ACU_REVERSO = 0;
		
		foreach($reversos as $reverso):
		
			$ACU_REVERSO += $reverso['OrdenDescuentoCobroCuota']['importe'];

			$PDF->linea[0] = array(
						'posx' => $L1[0],
						'ancho' => $W1[0] + $W1[1],
						'texto' => substr($reverso['OrdenDescuentoCobroCuota']['socio'],0,40),
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody,
				);
			$PDF->linea[2] = array(
						'posx' => $L1[2],
						'ancho' => $W1[2],
						'texto' => $reverso['OrdenDescuentoCobroCuota']['cuota']['tipo_nro'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody,
				);
			$PDF->linea[3] = array(
						'posx' => $L1[3],
						'ancho' => $W1[3],
						'texto' => $reverso['OrdenDescuentoCobroCuota']['cuota']['nro_referencia_proveedor'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody,
				);
			$PDF->linea[4] = array(
						'posx' => $L1[4],
						'ancho' => $W1[4],
						'texto' => substr($reverso['OrdenDescuentoCobroCuota']['cuota']['producto_cuota'],0,30),
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody,
				);
			$PDF->linea[5] = array(
						'posx' => $L1[5],
						'ancho' => $W1[5],
						'texto' => $reverso['OrdenDescuentoCobroCuota']['cuota']['cuota'],
						'borde' => '',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody,
				);
			$PDF->linea[6] = array(
						'posx' => $L1[6],
						'ancho' => $W1[6],
						'texto' => $util->periodo($reverso['OrdenDescuentoCobroCuota']['cuota']['periodo']),
						'borde' => '',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody,
				);
			$PDF->linea[7] = array(
						'posx' => $L1[7],
						'ancho' => $W1[7],
						'texto' => $util->nf($reverso['OrdenDescuentoCobroCuota']['importe']),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody,
				);																								
			$PDF->Imprimir_linea();			
		
		endforeach;
		
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => $W1[0] + $W1[1] + $W1[2] + $W1[3] + $W1[4] + $W1[5] + $W1[6],
					'texto' => "TOTAL REVERSADO",
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody,
			);		

		$PDF->linea[7] = array(
					'posx' => $L1[7],
					'ancho' => $W1[7],
					'texto' => $util->nf($ACU_REVERSO),
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody,
			);																								
		$PDF->Imprimir_linea();			
		
	endif;

endif;	

$DECIMAL_1T += $DECIMAL_1;
$DECIMAL_2T += $DECIMAL_2;
$DECIMAL_3T += $DECIMAL_3;

if(!empty($noCobrados)):

	$PDF->AddPage();
	$PDF->Reset();
	
	$fontSizeBody = 8;
	
	$DECIMAL_1 = 0;
	$DECIMAL_2 = 0;
	$DECIMAL_3 = 0;
	
	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => $W1[0],
				'texto' => "DETALLE DE CUOTAS NO COBRADAS",
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody + 2
		);	
	$PDF->Imprimir_linea();
	$PDF->ln(2);
	
	foreach($noCobrados as $dato){
		
		
		$DECIMAL_1 += $dato['decimal_1'];
		$DECIMAL_2 += $dato['decimal_2'];
		$DECIMAL_3 += $dato['decimal_3'];
		
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => $W1[0],
					'texto' => $dato['texto_1'],
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
					'texto' => substr($dato['texto_2'],0,25),
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
					'texto' => $dato['texto_3'],
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
					'texto' => $dato['texto_9'],
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
					'texto' => substr($dato['texto_4'],0,30),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);		
		$PDF->linea[5] = array(
					'posx' => $L1[5],
					'ancho' => $W1[5],
					'texto' => $dato['texto_6'],
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[6] = array(
					'posx' => $L1[6],
					'ancho' => $W1[6],
					'texto' => $util->periodo($dato['texto_7']),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
	
		$PDF->linea[7] = array(
					'posx' => $L1[7],
					'ancho' => $W1[7],
					'texto' => $util->nf($dato['decimal_1']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[8] = array(
					'posx' => $L1[8],
					'ancho' => $W1[8],
					'texto' => $util->nf($dato['decimal_2']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[9] = array(
					'posx' => $L1[9],
					'ancho' => $W1[9],
					'texto' => $util->nf($dato['decimal_3']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);																		
		$PDF->Imprimir_linea();		
		
	}
	
	$fontSizeBody = 9;
	
	
	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => 193,
				'texto' => 'SUBTOTAL NO COBRADO',
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[7] = array(
				'posx' => $L1[7],
				'ancho' => $W1[7],
				'texto' => $util->nf($DECIMAL_1),
				'borde' => '',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[8] = array(
				'posx' => $L1[8],
				'ancho' => $W1[8],
				'texto' => $util->nf($DECIMAL_2),
				'borde' => '',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[9] = array(
				'posx' => $L1[9],
				'ancho' => $W1[9],
				'texto' => $util->nf($DECIMAL_3),
				'borde' => '',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);																		
	$PDF->Imprimir_linea();

	$DECIMAL_1T += $DECIMAL_1;
	$DECIMAL_2T += $DECIMAL_2;
	$DECIMAL_3T += $DECIMAL_3;	
	
	$PDF->ln(3);

endif;



$PDF->linea[6] = array(
			'posx' => $L1[6],
			'ancho' => $W1[6],
			'texto' => 'TOTAL GENERAL',
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);
$PDF->linea[7] = array(
			'posx' => $L1[7],
			'ancho' => $W1[7],
			'texto' => $util->nf($DECIMAL_1T),
			'borde' => '',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);
$PDF->linea[8] = array(
			'posx' => $L1[8],
			'ancho' => $W1[8],
			'texto' => $util->nf($DECIMAL_2T),
			'borde' => '',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);
$PDF->linea[9] = array(
			'posx' => $L1[9],
			'ancho' => $W1[9],
			'texto' => $util->nf($DECIMAL_3T),
			'borde' => '',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);																		
$PDF->Imprimir_linea();

if(!empty($noCobradosBanco)):	

	
	// TIPO_DOCUMENTO | APENOM | BANCO INTRECAMBIO | CODIGO | DESCRIPCION | LIQUIDADO | A DEBITAR
	//277
	//$W1 = array(25,45,25,20,51,12,15,28,28,28);
	
	$W1 = array(25,106,80,10,28,28);
	$L1 = $PDF->armaAnchoColumnas($W1);
	
	$PDF->encabezado = array();
	$PDF->encabezado[0] = array();	
	$fontSizeHeader = 6;
	//imprimo la primera linea del encabezado
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
				'texto' => 'SOCIO',
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
				'texto' => 'BANCO INTERCAMBIO',
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
				'texto' => 'CODIGO',
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
	
	$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => "INFORME DE DEBITOS NO COBRADOS",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody + 2
	);	
	$PDF->Imprimir_linea();
	$PDF->ln(2);
	
	$DECIMAL_1 = 0;
	$DECIMAL_2 = 0;
	
	$parcial_1 = 0;
	$parcial_2 = 0;
	
	$fontSizeBody = 8;
	
	$codigo = null;
	$primero = true;
	
	foreach($noCobradosBanco as $dato){
		
		$DECIMAL_1 += $dato['decimal_1'];
		$DECIMAL_2 += $dato['decimal_2'];	

		
		if($codigo != trim($dato['texto_4'])):
		
			$codigo = trim($dato['texto_4']);
			
			if($primero):
			
				$primero = false;
				
			else:

				$PDF->linea[0] = array(
							'posx' => $L1[0],
							'ancho' => 250,
							'texto' => 'SUBTOTAL',
							'borde' => 'T',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);
				$PDF->linea[5] = array(
							'posx' => $L1[5],
							'ancho' => $W1[5],
							'texto' => $util->nf($parcial_2),
							'borde' => 'T',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);
																				
				$PDF->Imprimir_linea();	
				$parcial_1 = 0;
				$parcial_2 = 0;						
			
			endif;
		
		endif;

		$parcial_1 += $dato['decimal_1'];
		$parcial_2 += $dato['decimal_2'];
		
		
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => $W1[0],
					'texto' => $dato['texto_1'],
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
					'texto' => substr($dato['texto_2'],0,25),
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
					'texto' => $dato['texto_3'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
			
		//codigo	
		$PDF->linea[3] = array(
					'posx' => $L1[3],
					'ancho' => $W1[3],
					'texto' => $dato['texto_4'],
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
					'texto' => $dato['texto_5'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);		
		$PDF->linea[5] = array(
					'posx' => $L1[5],
					'ancho' => $W1[5],
					'texto' => $util->nf($dato['decimal_2']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
	
																		
		$PDF->Imprimir_linea();		
		
	}

	//ULTIMO PARCIAL
	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => 250,
				'texto' => 'SUBTOTAL',
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[5] = array(
				'posx' => $L1[5],
				'ancho' => $W1[5],
				'texto' => $util->nf($parcial_2),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
																	
	$PDF->Imprimir_linea();
					
	$PDF->ln(3);
	
	//TOTAL GENERAL
	$fontSizeBody = 9;
	
	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => 250,
				'texto' => 'TOTAL',
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[5] = array(
				'posx' => $L1[5],
				'ancho' => $W1[5],
				'texto' => $util->nf($DECIMAL_2),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
																	
	$PDF->Imprimir_linea();	
	
endif;	


$PDF->Output("liquidacion_proveedor".(isset($pid) ? "_".$pid : "").".pdf");
exit;

?>