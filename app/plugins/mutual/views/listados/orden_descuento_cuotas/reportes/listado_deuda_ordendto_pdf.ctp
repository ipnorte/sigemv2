<?php 
//debug($tipo_producto);
//debug($datos);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF("L");

$PDF->SetTitle("LISTADO DE DEUDA");
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo3'] = "LISTADO DE DEUDA".(!empty($tipo_producto) ? " - ".$util->globalDato($tipo_producto)."" : "");
$PDF->titulo['titulo2'] = (!empty($codigo_organismo) ? "ORGANISMO: ".$util->globalDato($codigo_organismo)." - " : "") . " PERIODO CORTE: " .$util->periodo($periodo_corte,true,'/');
$PDF->titulo['titulo1'] = (!empty($proveedor) ? "PROVEEDOR: ".$proveedor : "");



// TIPO_DOCUMENTO | APENOM | TIPO_NRO | REF.PROV. | PROVEEDOR_PRODUCTO | CUOTA | PERIODO | LIQUIDADO | IMPUTADO | SALDO
// 277
$W1 = array(35,75,167);
$L1 = $PDF->armaAnchoColumnas($W1);
//137
$W2 = array(30,25,20,85,12,15,30,30,30);
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
			'texto' => 'ORGANISMO',
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
			'texto' => 'TIPO / NUMERO',
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
			'texto' => 'REF.PROV.',
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
			'texto' => 'PRODUCTO - CONCEPTO',
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
			'texto' => 'CUOTAS',
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
			'texto' => 'LIQUIDADO',
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
			'texto' => 'COBRADO',
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
			'texto' => 'SALDO',
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

$DECIMAL_1 = 0;
$DECIMAL_2 = 0;
$DECIMAL_3 = 0;

if(!empty($datos)):

	$PDF->AddPage();
	$PDF->Reset();

	$socio_id = 0;
	$primero = true;
	$fontSizeBody = 8;
	
	foreach($datos as $dato):
	
		if($socio_id != $dato['entero_2']):
		
			$socio_id = $dato['entero_2'];
		
			if($primero):
			
				$primero = false;
				
			else:
			
				$PDF->linea[0] = array(
							'posx' => $L2[0],
							'ancho' => $W2[0] + $W2[1] + $W2[2] + $W2[3] + $W2[4] + $W2[5],
							'texto' => "TOTAL SOCIO",
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
							'texto' => "",//$util->nf($DECIMAL_1),
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
							'texto' => "",//$util->nf($DECIMAL_2),
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
							'texto' => $util->nf($DECIMAL_3),
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
			
			
			$PDF->Imprimir_linea();
			
		endif;	
		
		#ACUMULO
		$DECIMAL_1 += $dato['decimal_1'];
		$DECIMAL_2 += $dato['decimal_2'];
		$DECIMAL_3 += $dato['decimal_3'];
		
		$DECIMAL_1T += $dato['decimal_1'];
		$DECIMAL_2T += $dato['decimal_2'];
		$DECIMAL_3T += $dato['decimal_3'];
		
		$PDF->linea[0] = array(
					'posx' => $L2[0],
					'ancho' => $W2[0],
					'texto' => $dato['texto_10'],
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
					'texto' => $dato['texto_3'],
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
					'texto' => $dato['texto_6'],
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
					'texto' => substr($dato['texto_4'],0,37),
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
					'texto' => $dato['texto_7'],
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
					'texto' => $dato['texto_5'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[6] = array(
					'posx' => $L2[6],
					'ancho' => $W2[6],
					'texto' => "",//$util->nf($dato['decimal_1']),
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
					'texto' => "",//$util->nf($dato['decimal_2']),
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
					'texto' => $util->nf($dato['decimal_3']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);				
																		
		
		$PDF->Imprimir_linea();		
	
	endforeach;
	#IMPRIMO EL TOTAL DEL ULTIMO
	$PDF->linea[0] = array(
				'posx' => $L2[0],
				'ancho' => $W2[0] + $W2[1] + $W2[2] + $W2[3] + $W2[4] + $W2[5],
				'texto' => "TOTAL SOCIO",
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
				'texto' => "",//$util->nf($DECIMAL_1),
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
				'texto' => "",//$util->nf($DECIMAL_2),
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
				'texto' => $util->nf($DECIMAL_3),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->Imprimir_linea();	
	
	$PDF->ln(2);
	#IMPRIMO EL TOTAL GENERAL
	$PDF->linea[0] = array(
				'posx' => $L2[0],
				'ancho' => $W2[0] + $W2[1] + $W2[2] + $W2[3] + $W2[4] + $W2[5],
				'texto' => "TOTAL GENERAL",
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
				'texto' => "",//$util->nf($DECIMAL_1T),
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
				'texto' => "",//$util->nf($DECIMAL_2T),
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
				'texto' => $util->nf($DECIMAL_3T),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->Imprimir_linea();		
	

endif;

$PDF->Output("ListadoDeuda.pdf");
exit;

?>