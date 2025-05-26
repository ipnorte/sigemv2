<?php
//debug($stops);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF("L");

$title = "Liquidacion Proveedores :: " . ($procesarSobrePreImputacion == 1 ? "PRE-IMPUTACION ::" : "");
$title .= $proveedor['Proveedor']['razon_social'];
if(!empty($tipo_cuota)) $title .= " - " . $util->globalDato($tipo_cuota);
$title .= " - " . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']) . " | PERIODO: " . $util->periodo($liquidacion['Liquidacion']['periodo'],true);

$PDF->SetTitle($title);
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo3'] =  $proveedor['Proveedor']['razon_social'] . ($procesarSobrePreImputacion == 0 ? "" : " *** PRE-IMPUTACION ***");
$PDF->titulo['titulo2'] =  (empty($tipo_producto) ? "REPORTE GENERAL" : $util->globalDato($tipo_producto)." - ". $util->globalDato($tipo_cuota));
$PDF->titulo['titulo1'] = "LIQUIDACION: ".$util->globalDato($liquidacion['Liquidacion']['codigo_organismo']) . " | PERIODO: " . $util->periodo($liquidacion['Liquidacion']['periodo'],true);



// TIPO_DOCUMENTO | APENOM | TIPO_NRO | REF.PROV. | PROVEEDOR_PRODUCTO | CUOTA | PERIODO | LIQUIDADO | IMPUTADO | SALDO
// 277
$W1 = array(35,75,167);
$L1 = $PDF->armaAnchoColumnas($W1);
//137
$W2 = array(25,20,65,12,15,28,28,28,10,18,28);
$L2 = $PDF->armaAnchoColumnas($W2);

$PDF->encabezado = array();
$PDF->encabezado[0] = array();	
$fontSizeHeader = 7;
//imprimo la primera linea del encabezado
$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => 'DOCUMENTO',
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
			'texto' => 'SOCIO',
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
			'texto' => '',
			'borde' => 'TR',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
//2da linea del encabezado		
$PDF->encabezado[1][0] = array(
			'posx' => $L2[0],
			'ancho' => $W2[0],
			'texto' => 'TIPO / NUMERO',
			'borde' => 'LB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	
$PDF->encabezado[1][1] = array(
			'posx' => $L2[1],
			'ancho' => $W2[1],
			'texto' => 'REF.PROV.',
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	
$PDF->encabezado[1][2] = array(
			'posx' => $L2[2],
			'ancho' => $W2[2],
			'texto' => 'PRODUCTO - CONCEPTO',
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[1][3] = array(
			'posx' => $L2[3],
			'ancho' => $W2[3],
			'texto' => 'CUOTA',
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);										
$PDF->encabezado[1][4] = array(
			'posx' => $L2[4],
			'ancho' => $W2[4],
			'texto' => 'PERIODO',
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[1][5] = array(
			'posx' => $L2[5],
			'ancho' => $W2[5],
			'texto' => 'LIQUIDADO',
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
$PDF->encabezado[1][6] = array(
			'posx' => $L2[6],
			'ancho' => $W2[6],
			'texto' => ($procesarSobrePreImputacion == 1 ? "PRE-IMPUTADO" : "IMPUTADO"),
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[1][7] = array(
			'posx' => $L2[7],
			'ancho' => $W2[7],
			'texto' => 'SALDO',
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
$PDF->encabezado[1][8] = array(
			'posx' => $L2[8],
			'ancho' => $W2[8],
			'texto' => '% COM.',
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[1][9] = array(
			'posx' => $L2[9],
			'ancho' => $W2[9],
			'texto' => 'COMISION',
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[1][10] = array(
			'posx' => $L2[10],
			'ancho' => $W2[10],
			'texto' => 'NETO PROVEEDOR',
			'borde' => 'RB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);			

$DECIMAL_1T = 0;
$DECIMAL_2T = 0;
$DECIMAL_3T = 0;
$DECIMAL_4T = 0;
$DECIMAL_5T = 0;
$DECIMAL_6T = 0;

$DECIMAL_1 = 0;
$DECIMAL_2 = 0;
$DECIMAL_3 = 0;
$DECIMAL_4 = 0;
$DECIMAL_5 = 0;
$DECIMAL_6 = 0;

if(!empty($cuotas)):

	$PDF->AddPage();
	$PDF->Reset();

	$socio_id = 0;
	$primero = true;
	$fontSizeBody = 8;
	
	foreach($cuotas as $dato):
	
		if($socio_id != $dato['entero_2']):
		
			$socio_id = $dato['entero_2'];
		
			if($primero):
			
				$primero = false;
				
			else:
			
				$PDF->linea[4] = array(
							'posx' => $L2[3],
							'ancho' => $W2[3] + $W2[4],
							'texto' => "TOTAL SOCIO",
							'borde' => 'T',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);			
			
				$PDF->linea[5] = array(
							'posx' => $L2[5],
							'ancho' => $W2[5],
							'texto' => $util->nf($DECIMAL_1),
							'borde' => 'T',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);
				$PDF->linea[6] = array(
							'posx' => $L2[6],
							'ancho' => $W2[6],
							'texto' => $util->nf($DECIMAL_2),
							'borde' => 'T',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);
				$PDF->linea[7] = array(
							'posx' => $L2[7],
							'ancho' => $W2[7],
							'texto' => $util->nf($DECIMAL_3),
							'borde' => 'T',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);
				$PDF->linea[8] = array(
							'posx' => $L2[8],
							'ancho' => $W2[8],
							'texto' => "",
							'borde' => 'T',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);					
				$PDF->linea[9] = array(
							'posx' => $L2[9],
							'ancho' => $W2[9],
							'texto' => $util->nf($DECIMAL_5),
							'borde' => 'T',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);
				$PDF->linea[10] = array(
							'posx' => $L2[10],
							'ancho' => $W2[10],
							'texto' => $util->nf($DECIMAL_6),
							'borde' => 'T',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);																				
			
				$PDF->Imprimir_linea();
			
//				$PDF->ln(1);
				
				$DECIMAL_1 = $DECIMAL_2 = $DECIMAL_3 = $DECIMAL_4 = $DECIMAL_5 = $DECIMAL_6 = 0;
			
			endif;
			
			
			//imprimo los datos del socio
			$PDF->linea[0] = array(
						'posx' => $L1[0],
						'ancho' => $W1[0],
						'texto' => $dato['texto_1'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody + 2
				);
			$PDF->linea[1] = array(
						'posx' => $L1[1],
						'ancho' => $W1[1],
						'texto' => $dato['texto_2'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody + 2
				);
			$PDF->linea[2] = array(
						'posx' => $L1[2],
						'ancho' => $W1[2],
						'texto' => (!empty($dato['texto_13']) ? "*** ".$dato['texto_13']. " | " . (!empty($dato['texto_14']) ? $dato['texto_14'] : ""). " ***" : ""),
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);				
			
			
			$PDF->Imprimir_linea();
			
		
		endif;
		
		#ACUMULO
		$DECIMAL_1 += $dato['decimal_1'];
		$DECIMAL_2 += $dato['decimal_2'];
		$DECIMAL_3 += $dato['decimal_3'];
		$DECIMAL_4 += $dato['decimal_4'];
		$DECIMAL_5 += $dato['decimal_5'];
		$DECIMAL_6 += $dato['decimal_6'];
		
		$DECIMAL_1T += $dato['decimal_1'];
		$DECIMAL_2T += $dato['decimal_2'];
		$DECIMAL_3T += $dato['decimal_3'];
		$DECIMAL_4T += $dato['decimal_4'];
		$DECIMAL_5T += $dato['decimal_5'];
		$DECIMAL_6T += $dato['decimal_6'];		
		
		
//		debug($dato);
		
		#IMPRIMO LAS CUOTAS
		if($dato['entero_3'] == 1):
			$PDF->linea[0] = array(
						'posx' => $L2[0],
						'ancho' => $W2[0],
						'texto' => $dato['texto_3'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
				
			$PDF->linea[1] = array(
						'posx' => $L2[1],
						'ancho' => $W2[1],
						'texto' => $dato['texto_9'],
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
						'texto' => substr($dato['texto_4'],0,37),
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
			$PDF->linea[3] = array(
						'posx' => $L2[3],
						'ancho' => $W2[3],
						'texto' => $dato['texto_6'],
						'borde' => '',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
			$PDF->linea[4] = array(
						'posx' => $L2[4],
						'ancho' => $W2[4],
						'texto' => $util->periodo($dato['texto_7']),
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
						'texto' => $util->nf($dato['decimal_1']),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
			$PDF->linea[6] = array(
						'posx' => $L2[6],
						'ancho' => $W2[6],
						'texto' => $util->nf($dato['decimal_2']),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
			$PDF->linea[7] = array(
						'posx' => $L2[7],
						'ancho' => $W2[7],
						'texto' => $util->nf($dato['decimal_3']),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);				
			$PDF->linea[8] = array(
						'posx' => $L2[8],
						'ancho' => $W2[8],
						'texto' => $util->nf($dato['decimal_4']),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
			$PDF->linea[9] = array(
						'posx' => $L2[9],
						'ancho' => $W2[9],
						'texto' => $util->nf($dato['decimal_5']),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
			$PDF->linea[10] = array(
						'posx' => $L2[10],
						'ancho' => $W2[10],
						'texto' => $util->nf($dato['decimal_6']),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);																				
			
			$PDF->Imprimir_linea();
			
		endif;
		
	endforeach;
	
	
//	exit;
	
	
	#IMPRIMO EL TOTAL DEL ULTIMO
	$PDF->linea[4] = array(
				'posx' => $L2[3],
				'ancho' => $W2[3] + $W2[4],
				'texto' => "TOTAL SOCIO",
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);			

	$PDF->linea[5] = array(
				'posx' => $L2[5],
				'ancho' => $W2[5],
				'texto' => $util->nf($DECIMAL_1),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[6] = array(
				'posx' => $L2[6],
				'ancho' => $W2[6],
				'texto' => $util->nf($DECIMAL_2),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[7] = array(
				'posx' => $L2[7],
				'ancho' => $W2[7],
				'texto' => $util->nf($DECIMAL_3),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[8] = array(
				'posx' => $L2[8],
				'ancho' => $W2[8],
				'texto' => "",
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);		
	$PDF->linea[9] = array(
				'posx' => $L2[9],
				'ancho' => $W2[9],
				'texto' => $util->nf($DECIMAL_5),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[10] = array(
				'posx' => $L2[10],
				'ancho' => $W2[10],
				'texto' => $util->nf($DECIMAL_6),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);																				

	$PDF->Imprimir_linea();

	$PDF->ln(2);
	
	$DECIMAL_1 = $DECIMAL_2 = $DECIMAL_3 = $DECIMAL_4 = $DECIMAL_5 = $DECIMAL_6 = 0;	
	
	
	#IMPRIMO EL NETO
	$PDF->linea[4] = array(
				'posx' => $L2[0],
				'ancho' => $W2[0] + $W2[1] + $W2[2] + $W2[3] + $W2[4],
				'texto' => "SUBTOTAL (NETO LIQUIDACION / PERIODO)",
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);			

	$PDF->linea[5] = array(
				'posx' => $L2[5],
				'ancho' => $W2[5],
				'texto' => $util->nf($DECIMAL_1T),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[6] = array(
				'posx' => $L2[6],
				'ancho' => $W2[6],
				'texto' => $util->nf($DECIMAL_2T),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[7] = array(
				'posx' => $L2[7],
				'ancho' => $W2[7],
				'texto' => $util->nf($DECIMAL_3T),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[8] = array(
				'posx' => $L2[8],
				'ancho' => $W2[8],
				'texto' => "",
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);		
	$PDF->linea[9] = array(
				'posx' => $L2[9],
				'ancho' => $W2[9],
				'texto' => $util->nf($DECIMAL_5T),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[10] = array(
				'posx' => $L2[10],
				'ancho' => $W2[10],
				'texto' => $util->nf($DECIMAL_6T),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);																				

	$PDF->Imprimir_linea();

	$PDF->ln(2);	
	
	#IMPRIMO EL TOTAL DE REVERSOS
	
	$PROVEEDOR_REVERSO = 0;
	$ACU_COMISION = 0;
	$ACU_REVERSO = 0;	
	
	if(!empty($reversos)):
	
		$PDF->ln(2);
	
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => $W1[0] + $W1[1],
					'texto' => "DETALLE DE CUOTAS REVERSADAS PARA LA LIQUIDACION / PERIODO",
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);	
		$PDF->Imprimir_linea();
		$PDF->ln(2);
		
		$socio_id = 0;
		$primero = true;
		
		$PROVEEDOR_REVERSO_P = 0;
		$ACU_COMISION_P = 0;
		$ACU_REVERSO_P = 0;		
		
		foreach($reversos as $reverso):
		
			if($socio_id != $reverso['OrdenDescuentoCobroCuota']['socio_id']):
			
				$socio_id = $reverso['OrdenDescuentoCobroCuota']['socio_id'];
				
				if($primero):
				
					$primero = false;
					
				else:
				
					#IMPRIMO EL TOTAL DEL SOCIO
					$PDF->linea[4] = array(
								'posx' => $L2[0],
								'ancho' => $W2[0] + $W2[1] + $W2[2] + $W2[3] + $W2[4],
								'texto' => "TOTAL SOCIO",
								'borde' => 'T',
								'align' => 'R',
								'fondo' => 0,
								'style' => 'B',
								'colorf' => '#ccc',
								'size' => $fontSizeBody
						);			
				
					$PDF->linea[5] = array(
								'posx' => $L2[5],
								'ancho' => $W2[5],
								'texto' => "",
								'borde' => 'T',
								'align' => 'R',
								'fondo' => 0,
								'style' => 'B',
								'colorf' => '#ccc',
								'size' => $fontSizeBody
						);
					$PDF->linea[6] = array(
								'posx' => $L2[6],
								'ancho' => $W2[6],
								'texto' => $util->nf($ACU_REVERSO_P),
								'borde' => 'T',
								'align' => 'R',
								'fondo' => 0,
								'style' => 'B',
								'colorf' => '#ccc',
								'size' => $fontSizeBody
						);
					$PDF->linea[7] = array(
								'posx' => $L2[7],
								'ancho' => $W2[7],
								'texto' => "",
								'borde' => 'T',
								'align' => 'R',
								'fondo' => 0,
								'style' => 'B',
								'colorf' => '#ccc',
								'size' => $fontSizeBody
						);
					$PDF->linea[8] = array(
								'posx' => $L2[8],
								'ancho' => $W2[8],
								'texto' => "",
								'borde' => 'T',
								'align' => 'R',
								'fondo' => 0,
								'style' => 'B',
								'colorf' => '#ccc',
								'size' => $fontSizeBody
						);						
					$PDF->linea[9] = array(
								'posx' => $L2[9],
								'ancho' => $W2[9],
								'texto' => $util->nf($ACU_COMISION_P),
								'borde' => 'T',
								'align' => 'R',
								'fondo' => 0,
								'style' => 'B',
								'colorf' => '#ccc',
								'size' => $fontSizeBody
						);
					$PDF->linea[10] = array(
								'posx' => $L2[10],
								'ancho' => $W2[10],
								'texto' => $util->nf($PROVEEDOR_REVERSO_P),
								'borde' => 'T',
								'align' => 'R',
								'fondo' => 0,
								'style' => 'B',
								'colorf' => '#ccc',
								'size' => $fontSizeBody
						);																				
				
					$PDF->Imprimir_linea();
				
//					$PDF->ln(2);	

					$PROVEEDOR_REVERSO_P = $ACU_COMISION_P = $ACU_REVERSO_P = 0;					
				
				
				endif;
				
				$PDF->linea[0] = array(
							'posx' => $L1[0],
							'ancho' => $W1[0] + $W1[1],
							'texto' => $reverso['OrdenDescuentoCobroCuota']['socio'],
							'borde' => '',
							'align' => 'L',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => $fontSizeBody,
					);
				$PDF->Imprimir_linea();				
				
			
			endif;
		
		
//			$ACU_REVERSO += $reverso['OrdenDescuentoCobroCuota']['importe'];
			$ACU_REVERSO += $reverso['OrdenDescuentoCobroCuota']['importe_reversado'];
			$ACU_COMISION += $reverso['OrdenDescuentoCobroCuota']['comision_cobranza'];
			
//			$PROVEEDOR_REVERSO += $reverso['OrdenDescuentoCobroCuota']['importe'] - $reverso['OrdenDescuentoCobroCuota']['comision'];
			$PROVEEDOR_REVERSO += $reverso['OrdenDescuentoCobroCuota']['importe_reversado'] - $reverso['OrdenDescuentoCobroCuota']['comision_cobranza'];
			
//			$PROVEEDOR_REVERSO_P += $reverso['OrdenDescuentoCobroCuota']['importe'] - $reverso['OrdenDescuentoCobroCuota']['comision'];
			$PROVEEDOR_REVERSO_P += $reverso['OrdenDescuentoCobroCuota']['importe_reversado'] - $reverso['OrdenDescuentoCobroCuota']['comision_cobranza'];
			$ACU_COMISION_P += $reverso['OrdenDescuentoCobroCuota']['comision_cobranza'];
//			$ACU_REVERSO_P += $reverso['OrdenDescuentoCobroCuota']['importe'];
			$ACU_REVERSO_P += $reverso['OrdenDescuentoCobroCuota']['importe_reversado'];			
			
			
//			$PDF->linea[0] = array(
//						'posx' => $L1[0],
//						'ancho' => $W1[0] + $W1[1],
//						'texto' => $reverso['OrdenDescuentoCobroCuota']['socio'],
//						'borde' => '',
//						'align' => 'L',
//						'fondo' => 0,
//						'style' => 'B',
//						'colorf' => '#ccc',
//						'size' => $fontSizeBody,
//				);
//			$PDF->Imprimir_linea();
				
			$PDF->linea[0] = array(
						'posx' => $L2[0],
						'ancho' => $W2[0],
						'texto' => $reverso['OrdenDescuentoCobroCuota']['cuota']['tipo_nro'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody,
				);
			$PDF->linea[1] = array(
						'posx' => $L2[1],
						'ancho' => $W2[1],
						'texto' => $reverso['OrdenDescuentoCobroCuota']['cuota']['nro_referencia_proveedor'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody,
				);
			$PDF->linea[2] = array(
						'posx' => $L2[2],
						'ancho' => $W2[2],
						'texto' => substr($reverso['OrdenDescuentoCobroCuota']['cuota']['producto_cuota'],0,30),
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody,
				);
			$PDF->linea[3] = array(
						'posx' => $L2[3],
						'ancho' => $W2[3],
						'texto' => $reverso['OrdenDescuentoCobroCuota']['cuota']['cuota'],
						'borde' => '',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody,
				);
			$PDF->linea[4] = array(
						'posx' => $L2[4],
						'ancho' => $W2[4],
						'texto' => $util->periodo($reverso['OrdenDescuentoCobroCuota']['cuota']['periodo']),
						'borde' => '',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody,
				);
			$PDF->linea[6] = array(
						'posx' => $L2[6],
						'ancho' => $W2[6],
						'texto' => $util->nf($reverso['OrdenDescuentoCobroCuota']['importe_reversado']),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody,
				);
			$PDF->linea[7] = array(
						'posx' => $L2[7],
						'ancho' => $W2[7],
						'texto' => "",
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody,
				);				
			$PDF->linea[8] = array(
						'posx' => $L2[8],
						'ancho' => $W2[8],
						'texto' => $util->nf($reverso['OrdenDescuentoCobroCuota']['alicuota_comision_cobranza']),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody,
				);
			$PDF->linea[9] = array(
						'posx' => $L2[9],
						'ancho' => $W2[9],
						'texto' => $util->nf($reverso['OrdenDescuentoCobroCuota']['comision_cobranza']),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody,
				);
				
			$PDF->linea[10] = array(
						'posx' => $L2[10],
						'ancho' => $W2[10],
						'texto' => $util->nf($reverso['OrdenDescuentoCobroCuota']['importe_reversado'] - $reverso['OrdenDescuentoCobroCuota']['comision_cobranza']),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody,
				);				
																																			
			$PDF->Imprimir_linea();				
		
		endforeach;
		
		#IMPRIMO EL TOTAL DEL ULTIMO SOCIO
		$PDF->linea[4] = array(
					'posx' => $L2[0],
					'ancho' => $W2[0] + $W2[1] + $W2[2] + $W2[3] + $W2[4],
					'texto' => "TOTAL SOCIO",
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);			
	
		$PDF->linea[5] = array(
					'posx' => $L2[5],
					'ancho' => $W2[5],
					'texto' => "",
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[6] = array(
					'posx' => $L2[6],
					'ancho' => $W2[6],
					'texto' => $util->nf($ACU_REVERSO_P),
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[7] = array(
					'posx' => $L2[7],
					'ancho' => $W2[7],
					'texto' => "",
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[8] = array(
					'posx' => $L2[8],
					'ancho' => $W2[8],
					'texto' => "",
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);			
		$PDF->linea[9] = array(
					'posx' => $L2[9],
					'ancho' => $W2[9],
					'texto' => $util->nf($ACU_COMISION_P),
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[10] = array(
					'posx' => $L2[10],
					'ancho' => $W2[10],
					'texto' => $util->nf($PROVEEDOR_REVERSO_P),
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);																				
	
		$PDF->Imprimir_linea();
	
		$PDF->ln(2);	
	
		$PROVEEDOR_REVERSO_P = $ACU_COMISION_P = $ACU_REVERSO_P = 0;		
		
		
		#IMPRIMO EL TOTAL REVERSADO
		$PDF->linea[4] = array(
					'posx' => $L2[0],
					'ancho' => $W2[0] + $W2[1] + $W2[2] + $W2[3] + $W2[4],
					'texto' => "TOTAL REVERSADO",
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);			
	
		$PDF->linea[5] = array(
					'posx' => $L2[5],
					'ancho' => $W2[5],
					'texto' => "",
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[6] = array(
					'posx' => $L2[6],
					'ancho' => $W2[6],
					'texto' => $util->nf($ACU_REVERSO),
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[7] = array(
					'posx' => $L2[7],
					'ancho' => $W2[7],
					'texto' => "",
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[8] = array(
					'posx' => $L2[8],
					'ancho' => $W2[8],
					'texto' => "",
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);			
		$PDF->linea[9] = array(
					'posx' => $L2[9],
					'ancho' => $W2[9],
					'texto' => $util->nf($ACU_COMISION),
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[10] = array(
					'posx' => $L2[10],
					'ancho' => $W2[10],
					'texto' => $util->nf($PROVEEDOR_REVERSO),
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);																				
	
		$PDF->Imprimir_linea();
	
		$PDF->ln(2);		
		
		
		$PDF->ln(2);
	
	endif;
	
	#IMPRIMO EL NETO DEL PROVEEDOR
	$fontSizeBody += 2;
	
	$PDF->linea[4] = array(
				'posx' => $L2[0],
				'ancho' => $W2[0] + $W2[1] + $W2[2] + $W2[3] + $W2[4],
				'texto' => "TOTAL GENERAL",
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);			

	$PDF->linea[5] = array(
				'posx' => $L2[5],
				'ancho' => $W2[5],
				'texto' => $util->nf($DECIMAL_1T),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[6] = array(
				'posx' => $L2[6],
				'ancho' => $W2[6],
				'texto' => $util->nf($DECIMAL_2T),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[7] = array(
				'posx' => $L2[7],
				'ancho' => $W2[7],
				'texto' => "",
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[8] = array(
				'posx' => $L2[8],
				'ancho' => $W2[8],
				'texto' => "",
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);		
	$PDF->linea[9] = array(
				'posx' => $L2[9],
				'ancho' => $W2[9],
				'texto' => $util->nf($DECIMAL_5T - $ACU_COMISION),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[10] = array(
				'posx' => $L2[10],
				'ancho' => $W2[10],
				'texto' => $util->nf($DECIMAL_6T - $PROVEEDOR_REVERSO),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);																				

	$PDF->Imprimir_linea();

	$PDF->ln(2);
	
	$DECIMAL_1 = $DECIMAL_2 = $DECIMAL_3 = $DECIMAL_4 = $DECIMAL_5 = $DECIMAL_6 = 0;
	$DECIMAL_1T = $DECIMAL_2T = $DECIMAL_3T = $DECIMAL_4T = $DECIMAL_5T = $DECIMAL_6T = 0;	
	
	
	#IMPRIMO EL DETALLE DE LOS NO COBRO
	
	

endif;





//debug($noCobradosBanco);
//exit;

if(!empty($noCobradosBanco)):

	$W1 = array(35,75,167);
	$L1 = $PDF->armaAnchoColumnas($W1);
	//137
	$W2 = array(25,20,65,12,15,28,10,102);
	$L2 = $PDF->armaAnchoColumnas($W2);
	
	$PDF->encabezado = array();
	$PDF->encabezado[0] = array();	
	$fontSizeHeader = 7;
	//imprimo la primera linea del encabezado
	$PDF->encabezado[0][0] = array(
				'posx' => $L1[0],
				'ancho' => $W1[0],
				'texto' => 'DOCUMENTO',
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
				'texto' => 'SOCIO',
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
				'texto' => '',
				'borde' => 'TR',
				'align' => 'L',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);	
	//2da linea del encabezado		
	$PDF->encabezado[1][0] = array(
				'posx' => $L2[0],
				'ancho' => $W2[0],
				'texto' => 'TIPO / NUMERO',
				'borde' => 'LB',
				'align' => 'C',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);	
		
	$PDF->encabezado[1][1] = array(
				'posx' => $L2[1],
				'ancho' => $W2[1],
				'texto' => 'REF.PROV.',
				'borde' => 'B',
				'align' => 'C',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);	
		
	$PDF->encabezado[1][2] = array(
				'posx' => $L2[2],
				'ancho' => $W2[2],
				'texto' => 'PRODUCTO - CONCEPTO',
				'borde' => 'B',
				'align' => 'C',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);
	$PDF->encabezado[1][3] = array(
				'posx' => $L2[3],
				'ancho' => $W2[3],
				'texto' => 'CUOTA',
				'borde' => 'B',
				'align' => 'C',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);										
	$PDF->encabezado[1][4] = array(
				'posx' => $L2[4],
				'ancho' => $W2[4],
				'texto' => 'PERIODO',
				'borde' => 'B',
				'align' => 'C',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);
	$PDF->encabezado[1][5] = array(
				'posx' => $L2[5],
				'ancho' => $W2[5],
				'texto' => 'LIQUIDADO',
				'borde' => 'B',
				'align' => 'C',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);	
	$PDF->encabezado[1][6] = array(
				'posx' => $L2[6],
				'ancho' => $W2[6],
				'texto' => 'CODIGO',
				'borde' => 'B',
				'align' => 'C',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);
	$PDF->encabezado[1][7] = array(
				'posx' => $L2[7],
				'ancho' => $W2[7],
				'texto' => 'DESCRIPCION - CAUSA',
				'borde' => 'BR',
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

	
	$socio_id = 0;
	$primero = true;
	$fontSizeBody = 8;
	
	$DECIMAL_1T = 0;
	
	$DECIMAL_1 = 0;
	
	foreach($noCobradosBanco as $dato):
	
		if($socio_id != $dato['entero_2']):
		
			$socio_id = $dato['entero_2'];
		
			if($primero):
			
				$primero = false;
				
			else:
			
				$PDF->linea[4] = array(
							'posx' => $L2[3],
							'ancho' => $W2[3] + $W2[4],
							'texto' => "TOTAL",
							'borde' => 'T',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);			
			
				$PDF->linea[5] = array(
							'posx' => $L2[5],
							'ancho' => $W2[5],
							'texto' => $util->nf($DECIMAL_1),
							'borde' => 'T',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);
			
				$PDF->linea[6] = array(
							'posx' => $L2[6],
							'ancho' => $W2[6] + $W2[7],
							'texto' => "",
							'borde' => 'T',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);					
					
				$PDF->Imprimir_linea();
			
//				$PDF->ln(1);
				
				$DECIMAL_1 = $DECIMAL_2 = $DECIMAL_3 = $DECIMAL_4 = $DECIMAL_5 = $DECIMAL_6 = 0;
			
			endif;
			
			
			//imprimo los datos del socio
			$PDF->linea[0] = array(
						'posx' => $L1[0],
						'ancho' => $W1[0],
						'texto' => $dato['texto_1'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody + 2
				);
			$PDF->linea[1] = array(
						'posx' => $L1[1],
						'ancho' => $W1[1],
						'texto' => $dato['texto_2'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody + 2
				);
			$PDF->linea[2] = array(
						'posx' => $L1[2],
						'ancho' => $W1[2],
						'texto' => (!empty($dato['texto_13']) ? "*** ".$dato['texto_13']. " | " . (!empty($dato['texto_14']) ? $dato['texto_14'] : ""). " ***" : ""),
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);				
			
			
			$PDF->Imprimir_linea();
			
		
		endif;
		
		#ACUMULO
		$DECIMAL_1 += $dato['decimal_1'];
		$DECIMAL_1T += $dato['decimal_1'];
		
		
//		debug($dato);
		
		#IMPRIMO LAS CUOTAS
		if($dato['entero_3'] == 1):
		
			$PDF->linea[0] = array(
						'posx' => $L2[0],
						'ancho' => $W2[0],
						'texto' => $dato['texto_3'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
				
			$PDF->linea[1] = array(
						'posx' => $L2[1],
						'ancho' => $W2[1],
						'texto' => $dato['texto_9'],
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
						'texto' => substr($dato['texto_4'],0,37),
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
			$PDF->linea[3] = array(
						'posx' => $L2[3],
						'ancho' => $W2[3],
						'texto' => $dato['texto_6'],
						'borde' => '',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
			$PDF->linea[4] = array(
						'posx' => $L2[4],
						'ancho' => $W2[4],
						'texto' => $util->periodo($dato['texto_7']),
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
						'texto' => $util->nf($dato['decimal_1']),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);			
			$PDF->linea[6] = array(
						'posx' => $L2[6],
						'ancho' => $W2[6],
						'texto' => $dato['texto_11'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
			$PDF->linea[7] = array(
						'posx' => $L2[7],
						'ancho' => $W2[7],
						'texto' => $dato['texto_12'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);

			$PDF->Imprimir_linea();
			
		endif;
		
	endforeach;
	
	
//	exit;
	
	
	#IMPRIMO EL TOTAL DEL ULTIMO
	$PDF->linea[4] = array(
				'posx' => $L2[3],
				'ancho' => $W2[3] + $W2[4],
				'texto' => "TOTAL",
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);			

	$PDF->linea[5] = array(
				'posx' => $L2[5],
				'ancho' => $W2[5],
				'texto' => $util->nf($DECIMAL_1),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
		$PDF->linea[6] = array(
					'posx' => $L2[6],
					'ancho' => $W2[6] + $W2[7],
					'texto' => "",
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);	
					
																		

	$PDF->Imprimir_linea();

	$PDF->ln(2);
	
	$DECIMAL_1 = 0;	
	
	
	#IMPRIMO EL NETO
	$PDF->linea[4] = array(
				'posx' => $L2[0],
				'ancho' => $W2[0] + $W2[1] + $W2[2] + $W2[3] + $W2[4],
				'texto' => "TOTAL GENERAL NO COBRADO",
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);			

	$PDF->linea[5] = array(
				'posx' => $L2[5],
				'ancho' => $W2[5],
				'texto' => $util->nf($DECIMAL_1T),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[6] = array(
				'posx' => $L2[6],
				'ancho' => $W2[6] + $W2[7],
				'texto' => "",
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);

	$PDF->Imprimir_linea();	
	

endif;

if(!empty($cobrosByCaja)):


	// columnas
	// TIPO Y NRO DE DOCUMENTO | APENOM | FECHA | CONCEPTO | TIPO / NRO | REF PROVEEDOR | PRODUCTO - CONCEPTO | CUOTA | IMPORTE

	//277
	$W1 = array(30,247);
	$L1 = $PDF->armaAnchoColumnas($W1);
	
	$W2 = array(15,70,20,101,15,28,28);
	$L2 = $PDF->armaAnchoColumnas($W2);	
	
	$PDF->encabezado = array();
	
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
				'borde' => 'TBR',
				'align' => 'L',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);
		
		///////////////////////////////////////////////////
	
		$PDF->encabezado[1][0] = array(
					'posx' => $L2[0],
					'ancho' => $W2[0],
					'texto' => 'FECHA',
					'borde' => 'LB',
					'align' => 'C',
					'fondo' => 1,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeHeader
			);	
			
		$PDF->encabezado[1][1] = array(
					'posx' => $L2[1],
					'ancho' => $W2[1],
					'texto' => 'FORMA COBRO',
					'borde' => 'B',
					'align' => 'C',
					'fondo' => 1,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeHeader
			);	
			
		$PDF->encabezado[1][2] = array(
					'posx' => $L2[2],
					'ancho' => $W2[2],
					'texto' => 'TIPO / NUMERO',
					'borde' => 'B',
					'align' => 'C',
					'fondo' => 1,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeHeader
			);
		$PDF->encabezado[1][3] = array(
					'posx' => $L2[3],
					'ancho' => $W2[3],
					'texto' => 'PROVEEDOR - CONCEPTO',
					'borde' => 'B',
					'align' => 'C',
					'fondo' => 1,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeHeader
			);										
		$PDF->encabezado[1][4] = array(
					'posx' => $L2[4],
					'ancho' => $W2[4],
					'texto' => 'CUOTA',
					'borde' => 'B',
					'align' => 'C',
					'fondo' => 1,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeHeader
			);
		$PDF->encabezado[1][5] = array(
					'posx' => $L2[5],
					'ancho' => $W2[5],
					'texto' => 'PERIODO',
					'borde' => 'B',
					'align' => 'C',
					'fondo' => 1,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeHeader
			);	
		$PDF->encabezado[1][6] = array(
					'posx' => $L2[6],
					'ancho' => $W2[6],
					'texto' => 'COBRADO',
					'borde' => 'BR',
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
			'texto' => "INFORME DE COBRANZAS NO EFECTUADAS POR RECIBO DE SUELDO",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);	
	$PDF->Imprimir_linea();
	$PDF->ln(2);	

	$fontSizeBody = 7;
	
	
	$socio_id = 0;
	$primerSocio = true;
	$cobroID = 0;
	$primerCobro = true;	
	
	foreach($cobrosByCaja as $dato):
	
	
		if($socio_id != $dato['entero_3']):
		
			$socio_id = $dato['entero_3'];
			
			if($primerSocio):
			
				$primerSocio = false;
			
			else:
			
				//CORTE CONTROL TOTAL SOCIO
				
				//CORTE DE CONTROL COBRO
//				if($cobroID != $dato['entero_1']):
//				
//					$cobroID = $dato['entero_1'];
//				
//					if($primerCobro):
//					
//						$primerCobro = false;	
//					
//					else:
//					
//							
//					
//					endif;
//					
//					$PDF->linea[5] = array(
//								'posx' => $L2[5],
//								'ancho' => $W2[5],
//								'texto' => "TOTAL COBRO",
//								'borde' => 'T',
//								'align' => 'L',
//								'fondo' => 0,
//								'style' => '',
//								'colorf' => '#ccc',
//								'size' => $fontSizeBody
//						);
//					$PDF->Imprimir_linea();			
//				
//				endif;			
			
				$PDF->ln(3);
			
			endif;
		
			$fontSizeBody1 = 10;
			$PDF->linea[0] = array(
						'posx' => $L1[0],
						'ancho' => $W1[0],
						'texto' => $dato['texto_1'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody1
				);
			$PDF->linea[1] = array(
						'posx' => $L1[1],
						'ancho' => $W1[1],
						'texto' => $dato['texto_2'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody1
				);
			$PDF->ln(2);						
			$PDF->Imprimir_linea();
		
		
		endif;
		
		

		
		
		$PDF->linea[0] = array(
					'posx' => $L2[0],
					'ancho' => $W2[0],
					'texto' => $util->armaFecha($dato['texto_5']),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[1] = array(
					'posx' => $L2[1],
					'ancho' => $W2[1],
					'texto' => $dato['texto_4']. (!empty($dato['texto_12']) ? " (".$dato['texto_12']. (!empty($dato['texto_13']) ? " - " . $dato['texto_13'] : "") .")" : ""),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);	
		$PDF->linea[2] = array(
					'posx' => $L2[2],
					'ancho' => $W2[2],
					'texto' => $dato['texto_8'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[3] = array(
					'posx' => $L2[3],
					'ancho' => $W2[3],
					'texto' => $dato['texto_9'],
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
					'texto' => $dato['texto_11'],
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[5] = array(
					'posx' => $L2[5],
					'ancho' => $W2[5],
					'texto' => $util->periodo($dato['texto_6']),
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);

		$PDF->linea[6] = array(
					'posx' => $L2[6],
					'ancho' => $W2[6],
					'texto' => $util->nf($dato['decimal_1']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);			
			
		$PDF->Imprimir_linea();
		
		
	
	endforeach;



endif;	


if(!empty($bajas)):




	//277
	$W1 = array(20,50,25,10,15,80,20,57);
	$L1 = $PDF->armaAnchoColumnas($W1);
	
	
	$PDF->encabezado = array();

	$fontSizeHeader = 7;
	
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
				'align' => 'L',
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
				'align' => 'L',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);		
	$PDF->encabezado[0][3] = array(
				'posx' => $L1[3],
				'ancho' => $W1[3],
				'texto' => 'CUOTA',
				'borde' => 'TB',
				'align' => 'L',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);
	$PDF->encabezado[0][4] = array(
				'posx' => $L1[4],
				'ancho' => $W1[4],
				'texto' => 'PERIODO',
				'borde' => 'TB',
				'align' => 'L',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);
	$PDF->encabezado[0][5] = array(
				'posx' => $L1[5],
				'ancho' => $W1[5],
				'texto' => 'PRODUCTO-CONCEPTO',
				'borde' => 'TB',
				'align' => 'L',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);
	$PDF->encabezado[0][6] = array(
				'posx' => $L1[6],
				'ancho' => $W1[6],
				'texto' => 'IMPORTE',
				'borde' => 'TB',
				'align' => 'L',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);
	$PDF->encabezado[0][7] = array(
				'posx' => $L1[7],
				'ancho' => $W1[7],
				'texto' => 'MOTIVO DE LA BAJA',
				'borde' => 'TBR',
				'align' => 'L',
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
			'texto' => "INFORME DE CUOTAS CORRESPONDIENTES AL PERIODO DADAS DE BAJA",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);	
	$PDF->Imprimir_linea();
	$PDF->ln(2);	

	$fontSizeBody = 7;
	
	foreach($bajas as $dato):
	
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
					'texto' => substr($dato['texto_2'],0,35),
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
					'texto' => $dato['texto_4'],
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
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[4] = array(
					'posx' => $L1[4],
					'ancho' => $W1[4],
					'texto' => $dato['texto_8'],
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
					'texto' => $dato['texto_11'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[6] = array(
					'posx' => $L1[6],
					'ancho' => $W1[6],
					'texto' => $util->nf($dato['decimal_1']),
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
					'texto' => $dato['texto_7'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);																
		$PDF->Imprimir_linea();	
	
	endforeach;
	

endif;


if(!empty($stops)):

	//277
	$W1 = array(20,80,25,25,25,50,52);
	$L1 = $PDF->armaAnchoColumnas($W1);
	
	
	$PDF->encabezado = array();

	$fontSizeHeader = 7;
	
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
				'texto' => 'ORDEN',
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
				'texto' => 'TIPO NRO',
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
				'texto' => 'SALDO',
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
				'texto' => 'PERIODO',
				'borde' => 'TB',
				'align' => 'L',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);	
	$PDF->encabezado[0][6] = array(
				'posx' => $L1[6],
				'ancho' => $W1[6],
				'texto' => '',
				'borde' => 'TBR',
				'align' => 'L',
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
			'texto' => "REGISTRO DE STOP DEBIT ANTERIORES",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);	
	$PDF->Imprimir_linea();
	$PDF->ln(2);		

	$TOTAL = 0;
	
	foreach($stops as $dato):
	
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
					'texto' => $dato['texto_2'],
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
					'texto' => $dato['texto_4'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[4] = array(
					'posx' => $L1[4],
					'ancho' => $W1[4],
					'texto' => $util->nf($dato['decimal_1']),
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
					'texto' => $dato['texto_5'],
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);        
		$PDF->Imprimir_linea();
		
		$TOTAL += $dato['decimal_1'];
	
	endforeach;
	
//	$PDF->linea[4] = array(
//				'posx' => $L1[4],
//				'ancho' => $W1[4],
//				'texto' => $util->nf($TOTAL),
//				'borde' => 'T',
//				'align' => 'R',
//				'fondo' => 0,
//				'style' => 'B',
//				'colorf' => '#ccc',
//				'size' => $fontSizeBody
//		);									
//	$PDF->Imprimir_linea();	
	
endif;


$file = "liquidacion_proveedor_";
$file .= (isset($pid) ? $pid."_" : "");
$file .= $proveedor['Proveedor']['razon_social'];
if(!empty($tipo_cuota)) $file .= "_" . $util->globalDato($tipo_cuota);
$file .= "_" . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']) . "_" . $liquidacion['Liquidacion']['periodo'];
$file .= ($procesarSobrePreImputacion == 1 ? "_preimputacion" : "_imputado") . ".pdf";


$PDF->Output($file);
exit;

?>