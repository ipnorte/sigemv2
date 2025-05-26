<?php 
//debug($datos);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF('L');

$PDF->SetTitle("Ordenes de Cancelacion entre Fechas");
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo3'] = 'ORDENES DE CANCELACIONES PROCESADAS' . (!empty($forma_cancelacion_desc) ? " :: $forma_cancelacion_desc" : "") ;
$PDF->titulo['titulo2'] = "ENTRE EL " . $util->armaFecha($fecha_desde) ." Y EL ". $util->armaFecha($fecha_hasta) ." (".$criterio_desc.")";
$PDF->titulo['titulo1'] = "";

//ORDEN | EMITIDA | VENCIMIENTO | ESTADO  | BENEFICIARIO | TIPO CANCELACION | F.PAGO | IMPORTE | DEB.CRED.
$W1 = array(10,17,17,17,55,101,20,20,20);
$L1 = $PDF->armaAnchoColumnas($W1);
$W2 = array(10,25,25,72,45,15,15,20,50);
$L2 = $PDF->armaAnchoColumnas($W2);

$PDF->encabezado = array();
$PDF->encabezado[0] = array();	
$fontSizeHeader = 6;
//imprimo la primera linea del encabezado
$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => '#',
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
			'texto' => 'VENCIMIENTO',
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
			'texto' => 'IMPUTADA',
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
			'texto' => 'FORMA',
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
			'texto' => 'BENEFICIARIO',
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
			'texto' => 'CANCELA CON',
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
			'texto' => 'TOTAL',
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
			'texto' => 'CRED.DEB.',
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][8] = array(
			'posx' => $L1[8],
			'ancho' => $W1[8],
			'texto' => 'CANCELA',
			'borde' => 'TR',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	

//SEGUNDA LINEA DE COLUMNAS
$PDF->encabezado[1][0] = array(
			'posx' => $L2[0],
			'ancho' => $W2[0],
			'texto' => '',
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
			'texto' => 'ORD.DTO.',
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
			'texto' => 'TIPO / NRO',
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
			'texto' => 'PROVEEDOR / PRODUCTO',
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
			'texto' => 'CONCEPTO',
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
			'texto' => 'CUOTA',
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
			'texto' => 'IMPORTE',
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
			'texto' => '',
			'borde' => 'BR',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	
	
$PDF->AddPage();
$PDF->Reset();
	
$fontSizeBody = 9;

$proveedorActual = "";

$primero = true;
$ACU_TOTAL = 0;
$ACU_CANCELA = 0;
$ACU_CRED = 0;

$ACU_TOTAL_P = 0;
$ACU_CANCELA_P = 0;
$ACU_CRED_P = 0;

if(!empty($datos)):

	foreach($datos as $dato){
		
		$fontSizeBody = 6;
		
		$PDF->Reset();
		
		$ACU_TOTAL += $dato['AsincronoTemporal']['decimal_1'];
		$ACU_CANCELA += $dato['AsincronoTemporal']['decimal_2'];
		$ACU_CRED += $dato['AsincronoTemporal']['decimal_3'];	
		
		if($proveedorActual != trim($dato['AsincronoTemporal']['clave_1'])){
			
			$proveedorActual = trim($dato['AsincronoTemporal']['clave_1']);
			
			if($primero){
				
				$primero = false;
				
			}else{
				
				$PDF->linea[5] = array(
							'posx' => $L1[5],
							'ancho' => $W1[5],
							'texto' => 'SUBTOTAL',
							'borde' => '',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);
				$PDF->linea[6] = array(
							'posx' => $L1[6],
							'ancho' => $W1[6],
							'texto' => $util->nf($ACU_TOTAL_P),
							'borde' => 'T',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);
				$PDF->linea[7] = array(
							'posx' => $L1[7],
							'ancho' => $W1[7],
							'texto' => $util->nf($ACU_CRED_P),
							'borde' => 'T',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);
				$PDF->linea[8] = array(
							'posx' => $L1[8],
							'ancho' => $W1[8],
							'texto' => $util->nf($ACU_CANCELA_P),
							'borde' => 'T',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);																		
				$PDF->Imprimir_linea();			
				
				
				$ACU_TOTAL_P = 0;
				$ACU_CANCELA_P = 0;
				$ACU_CRED_P = 0;			
				
			}
			
			
			$PDF->ln(3);
			$PDF->linea[0] = array(
						'posx' => $L1[0],
						'ancho' => 277,
						'texto' => $dato['AsincronoTemporal']['texto_6'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#D8DBD4',
						'size' => $fontSizeBody + 3
				);
			$PDF->Imprimir_linea();		
			
		}
	
		
		$ACU_TOTAL_P += $dato['AsincronoTemporal']['decimal_1'];
		$ACU_CANCELA_P += $dato['AsincronoTemporal']['decimal_2'];
		$ACU_CRED_P += $dato['AsincronoTemporal']['decimal_3'];	
		
		
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => $W1[0],
					'texto' => '#'.$dato['AsincronoTemporal']['clave_2'],
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[1] = array(
					'posx' => $L1[1],
					'ancho' => $W1[1],
					'texto' => $util->armaFecha($dato['AsincronoTemporal']['texto_11']),
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[2] = array(
					'posx' => $L1[2],
					'ancho' => $W1[2],
					'texto' => $util->armaFecha($dato['AsincronoTemporal']['texto_12']),
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
					'texto' => substr($dato['AsincronoTemporal']['texto_7'],0,12),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);		
		$PDF->linea[4] = array(
					'posx' => $L1[4],
					'ancho' => $W1[4],
					'texto' => substr($dato['AsincronoTemporal']['texto_2']. " " .$dato['AsincronoTemporal']['texto_3'],0,43),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[5] = array(
					'posx' => $L1[5],
					'ancho' => $W1[5],
					'texto' => $dato['AsincronoTemporal']['texto_8'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
			
//		$PDF->linea[5] = array(
//					'posx' => $L1[5],
//					'ancho' => $W1[5],
//					'texto' => $dato['AsincronoTemporal']['texto_8'] . (!empty($dato['AsincronoTemporal']['texto_14']) ? " [".$dato['AsincronoTemporal']['texto_14']."]" : "") . (!empty($dato['AsincronoTemporal']['texto_15']) ? " [".$dato['AsincronoTemporal']['texto_15']."]" : ""),
//					'borde' => '',
//					'align' => 'L',
//					'fondo' => 0,
//					'style' => 'B',
//					'colorf' => '#ccc',
//					'size' => $fontSizeBody
//			);			
	
		$PDF->linea[6] = array(
					'posx' => $L1[6],
					'ancho' => $W1[6],
					'texto' => $util->nf($dato['AsincronoTemporal']['decimal_1']),
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
					'texto' => $util->nf($dato['AsincronoTemporal']['decimal_3']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[8] = array(
					'posx' => $L1[8],
					'ancho' => $W1[8],
					'texto' => $util->nf($dato['AsincronoTemporal']['decimal_2']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);																		
		$PDF->Imprimir_linea();	
	
		//PROCESO DEL DETALLE DE CUOTAS
		if(!empty($dato['AsincronoTemporalDetalle'])):
			
			$fontSizeBody = 6;
			
			foreach($dato['AsincronoTemporalDetalle'] as $detalle):
			
				$PDF->linea[1] = array(
							'posx' => $L2[1],
							'ancho' => $W2[1],
							'texto' => $detalle['texto_1'],
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
							'texto' => $detalle['texto_2'],
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
							'texto' => $detalle['texto_6'],
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
							'texto' => $detalle['texto_7'],
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
							'texto' => $util->periodo($detalle['texto_4']),
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
							'texto' => $detalle['texto_5'],
							'borde' => '',
							'align' => 'C',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);
				$PDF->linea[7] = array(
							'posx' => $L2[7],
							'ancho' => $W2[7],
							'texto' => $util->nf($detalle['decimal_1']),
							'borde' => '',
							'align' => 'R',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
					);										
				$PDF->Imprimir_linea();
			
			endforeach;
			
			$PDF->ln(2);
			
		endif;
		
		$PDF->linea[5] = array(
					'posx' => $L1[5],
					'ancho' => $W1[5],
					'texto' => (!empty($dato['AsincronoTemporal']['texto_14']) ? " [".$dato['AsincronoTemporal']['texto_14']."]" : "") . (!empty($dato['AsincronoTemporal']['texto_15']) ? " [".$dato['AsincronoTemporal']['texto_15']."]" : ""),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);			
		$PDF->Imprimir_linea();		
		
	}


	
	//mando el total del ultimo proveedor
	$PDF->linea[5] = array(
				'posx' => $L1[5],
				'ancho' => $W1[5],
				'texto' => 'TOTAL ' . $dato['AsincronoTemporal']['texto_6'],
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[6] = array(
				'posx' => $L1[6],
				'ancho' => $W1[6],
				'texto' => $util->nf($ACU_TOTAL_P),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[7] = array(
				'posx' => $L1[7],
				'ancho' => $W1[7],
				'texto' => $util->nf($ACU_CRED_P),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[8] = array(
				'posx' => $L1[8],
				'ancho' => $W1[8],
				'texto' => $util->nf($ACU_CANCELA_P),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);																		
	$PDF->Imprimir_linea();	
	
	$fontSizeBody = 7;
	$PDF->ln(3);
	//$PDF->linea[4] = array(
	//			'posx' => $L1[4],
	//			'ancho' => $W1[4],
	//			'texto' => 'TOTAL GENERAL',
	//			'borde' => '',
	//			'align' => 'R',
	//			'fondo' => 0,
	//			'style' => 'B',
	//			'colorf' => '#ccc',
	//			'size' => $fontSizeBody
	//	);
	$PDF->linea[5] = array(
				'posx' => $L1[5],
				'ancho' => $W1[5],
				'texto' => 'TOTAL GENERAL',
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[6] = array(
				'posx' => $L1[6],
				'ancho' => $W1[6],
				'texto' => $util->nf($ACU_TOTAL),
				'borde' => '',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[7] = array(
				'posx' => $L1[7],
				'ancho' => $W1[7],
				'texto' => $util->nf($ACU_CRED),
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
				'texto' => $util->nf($ACU_CANCELA),
				'borde' => '',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);																		
	$PDF->Imprimir_linea();	

	
endif;	

//debug($datos);
$PDF->Output("ordenes_cancelacion.pdf");
?>