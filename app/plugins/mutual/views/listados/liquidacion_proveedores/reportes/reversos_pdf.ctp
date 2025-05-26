<?php
//debug($reversos);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF("L");

$title = "Listado de Reversos :: ";
$title .= $proveedor['Proveedor']['razon_social'];
$title .= " - " . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']) . " | PERIODO: " . $util->periodo($liquidacion['Liquidacion']['periodo'],true);

$PDF->SetTitle($title);
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo3'] =  $proveedor['Proveedor']['razon_social'];
$PDF->titulo['titulo2'] =  "LISTADO DE REVERSOS";
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
			'texto' => 'COBRADO',
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

if(!empty($reversos)):

	$PDF->AddPage();
	$PDF->Reset();

	$socio_id = 0;
	$primero = true;
	$fontSizeBody = 8;
	
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
		
		
			$ACU_REVERSO += $reverso['OrdenDescuentoCobroCuota']['importe_reversado'];
			$ACU_COMISION += $reverso['OrdenDescuentoCobroCuota']['comision_cobranza'];
			
			$PROVEEDOR_REVERSO += $reverso['OrdenDescuentoCobroCuota']['importe_reversado'] - $reverso['OrdenDescuentoCobroCuota']['comision_cobranza'];
			
			$PROVEEDOR_REVERSO_P += $reverso['OrdenDescuentoCobroCuota']['importe_reversado'] - $reverso['OrdenDescuentoCobroCuota']['comision_cobranza'];
			$ACU_COMISION_P += $reverso['OrdenDescuentoCobroCuota']['comision_cobranza'];
			$ACU_REVERSO_P += $reverso['OrdenDescuentoCobroCuota']['importe_reversado'];			
			
			
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
	
	

endif;

$file = "reversos_";
$file .= (isset($pid) ? $pid."_" : "");
$file .= $proveedor['Proveedor']['razon_social'];
if(!empty($tipo_cuota)) $file .= "_" . $util->globalDato($tipo_cuota);
$file .= "_" . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']) . "_" . $liquidacion['Liquidacion']['periodo'];
$file .= ".pdf";


$PDF->Output($file);
exit;

?>