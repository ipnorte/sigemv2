<?php 

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF('L');

$PDF->SetTitle("PLANILLA CAJA");
$PDF->titulo['titulo1'] = "";
if($dias > 1):
//	$PDF->titulo['titulo2'] = "DESDE FECHA: " . $util->armaFecha($cuenta['BancoCuenta']['fecha_conciliacion']) . " - HASTA FECHA: " . $util->armaFecha($cuenta['BancoCuenta']['fecha_extracto']);
	$PDF->titulo['titulo2'] = "DESDE FECHA: " . $temporal[0]['AsincronoTemporal']['texto_1'] . " - HASTA FECHA: " . $util->armaFecha($cuenta['BancoCuenta']['fecha_extracto']);

else:
	$PDF->titulo['titulo2'] = "FECHA: " . $util->armaFecha($cuenta['BancoCuenta']['fecha_extracto']);
endif;
$PDF->titulo['titulo3'] = "PLANILLA CAJA";

$PDF->SetFontSizeConf(9);

$PDF->textoHeader = '';
$PDF->PIEUser = true;

$PDF->Open();

$W0 = array(69, 69, 69, 70);
$L0 = $PDF->armaAnchoColumnas($W0);

if($dias > 1):
	$W1 = array(25, 177, 25, 25, 25);
else:
	$W1 = array(202, 25, 25, 25);
endif;
$L1 = $PDF->armaAnchoColumnas($W1);

$PDF->encabezado = array();
$fontSizeHeader = 9;
$fontSizeBody = 9;

	$PDF->AddPage();
	$PDF->Reset();
	
$i = 0;

if($dias > 1):
$PDF->encabezado[0][$i] = array(
			'posx' => $L1[$i],
			'ancho' => $W1[$i],
			'texto' => 'FECHA',
			'borde' => 'LTB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
	$i++;
endif;
$PDF->encabezado[0][$i] = array(
			'posx' => $L1[$i],
			'ancho' => $W1[$i],
			'texto' => 'CONCEPTO',
			'borde' => ($dias > 1 ? 'TB' : 'LTB'),
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	$i++;
	
$PDF->encabezado[0][$i] = array(
			'posx' => $L1[$i],
			'ancho' => $W1[$i],
			'texto' => 'INGRESO',
			'borde' => 'TB',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	$i++;
	
$PDF->encabezado[0][$i] = array(
			'posx' => $L1[$i],
			'ancho' => $W1[$i],
			'texto' => 'EGRESO',
			'borde' => 'TB',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	$i++;
		
$PDF->encabezado[0][$i] = array(
			'posx' => $L1[$i],
			'ancho' => $W1[$i],
			'texto' => 'TOTAL',
			'borde' => 'TB',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	$i++;
		
	///////	

	
	$TOTAL_INGRESO = $TOTAL_EGRESO = 0;

	$PDF->linea[0] = array(
			'posx' => $L0[0],
			'ancho' => $W0[0],
			'texto' => 'SALDO AL INICIO',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 12
	);
						
	$PDF->linea[1] = array(
			'posx' => $L0[1],
			'ancho' => $W0[1],
			'texto' => 'I N G R E S O',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 12
	);
						
	$PDF->linea[2] = array(
			'posx' => $L0[2],
			'ancho' => $W0[2],
			'texto' => 'E G R E S O',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 12
	);
				
	$PDF->linea[3] = array(
			'posx' => $L0[3],
			'ancho' => $W0[3],
			'texto' => 'SALDO AL FINAL',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 12
	);
	
	$PDF->Imprimir_linea();
				

	$PDF->linea[0] = array(
			'posx' => $L0[0],
			'ancho' => $W0[0],
			'texto' => number_format($cuenta['BancoCuenta']['importe_conciliacion'],2),
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 12
	);
						
	$PDF->linea[1] = array(
			'posx' => $L0[1],
			'ancho' => $W0[1],
			'texto' => number_format($ingreso + $ingresoCheque,2),
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 12
	);
						
	$PDF->linea[2] = array(
			'posx' => $L0[2],
			'ancho' => $W0[2],
			'texto' => number_format($egreso + $ingresoCheque,2),
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 12
	);
				
	$PDF->linea[3] = array(
			'posx' => $L0[3],
			'ancho' => $W0[3],
			'texto' => number_format($cuenta['BancoCuenta']['importe_conciliacion'] + $ingreso - $egreso,2),
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 12
	);
	
	$PDF->Imprimir_linea();
				
	$PDF->Ln(0.5);

	foreach($PDF->encabezado as $encabezado){
    			
		$PDF->linea = $encabezado;
		$PDF->imprimir_linea();
    			
	}
	$PDF->Ln(0.5);
	$PDF->Reset();	

	$PDF->linea[0] = array(
				'posx' => ($dias > 1 ? $L1[1] : $L1[0]),
				'ancho' => ($dias > 1 ? $W1[1] : $W1[0]),
				'texto' => 'SALDO AL ' . date('d/m/Y',strtotime($cuenta['BancoCuenta']['fecha_conciliacion'])),
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
	);
	
	$PDF->linea[1] = array(
				'posx' => ($dias > 1 ? $L1[4] : $L1[3]),
				'ancho' => ($dias > 1 ? $W1[4] : $W1[3]),
				'texto' => number_format($cuenta['BancoCuenta']['importe_conciliacion'],2),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
	);
	
	$PDF->Imprimir_linea();
		
	$saldo = $cuenta['BancoCuenta']['importe_conciliacion'];
	$i = 0;
	$ingreso = 0;
	$egreso = 0;
	$primeraVez = 0;
	foreach ($temporal as $renglon):
		if($renglon['AsincronoTemporal']['entero_2'] == 0):
			$TOTAL_INGRESO += $renglon['AsincronoTemporal']['decimal_1'];
		else:
			if($primeraVez == 0 && $TOTAL_INGRESO > 0):
				$primeraVez = 1;
				$i = 0;

				if($ingresoCheque > 0):
					if($dias > 1):
						$PDF->linea[$i] = array(
								'posx' => $L1[$i],
								'ancho' => $W1[$i],
								'texto' => '',
								'borde' => '',
								'align' => 'C',
								'fondo' => 1,
								'style' => 'B',
								'colorf' => '#ccc',
								'size' => 10
						);
						$i++;
					endif;
							
					$PDF->linea[$i] = array(
								'posx' => $L1[$i],
								'ancho' => $W1[$i],
								'texto' => 'INGRESO',
								'borde' => '',
								'align' => 'R',
								'fondo' => 1,
								'style' => 'B',
								'colorf' => '#ccc',
								'size' => 10
					);
					$i++;
							
					$PDF->linea[$i] = array(
								'posx' => $L1[$i],
								'ancho' => $W1[$i],
								'texto' => '',
								'borde' => '',
								'align' => 'R',
								'fondo' => 1,
								'style' => 'B',
								'colorf' => '#ccc',
								'size' => 10
					);
					$i++;
					
					$PDF->linea[$i] = array(
							'posx' => $L1[$i],
							'ancho' => $W1[$i],
							'texto' => '',
							'borde' => '',
							'align' => 'C',
							'fondo' => 1,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => 10
					);
					$i++;
					
					$PDF->linea[$i] = array(
							'posx' => $L1[$i],
							'ancho' => $W1[$i],
							'texto' => number_format($TOTAL_INGRESO,2),
							'borde' => '',
							'align' => 'R',
							'fondo' => 1,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => 10
					);
					$i++;
					
					$PDF->linea[$i] = array(
							'posx' => $L1[$i],
							'ancho' => $W1[$i],
							'texto' => '',
							'borde' => '',
							'align' => 'C',
							'fondo' => 1,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => 10
					);
					
					$PDF->Imprimir_linea();


					$i = 0;
					if($dias > 1):
						$PDF->linea[$i] = array(
								'posx' => $L1[$i],
								'ancho' => $W1[$i],
								'texto' => '',
								'borde' => '',
								'align' => 'C',
								'fondo' => 1,
								'style' => 'B',
								'colorf' => '#ccc',
								'size' => 10
						);
						$i++;
					endif;
							
					$PDF->linea[$i] = array(
								'posx' => $L1[$i],
								'ancho' => $W1[$i],
								'texto' => 'EFECTIVIZACION DE CHEQUES',
								'borde' => '',
								'align' => 'R',
								'fondo' => 1,
								'style' => 'B',
								'colorf' => '#ccc',
								'size' => 10
					);
					$i++;
							
					$PDF->linea[$i] = array(
								'posx' => $L1[$i],
								'ancho' => $W1[$i],
								'texto' => '',
								'borde' => '',
								'align' => 'R',
								'fondo' => 1,
								'style' => 'B',
								'colorf' => '#ccc',
								'size' => 10
					);
					$i++;
					
					$PDF->linea[$i] = array(
							'posx' => $L1[$i],
							'ancho' => $W1[$i],
							'texto' => '',
							'borde' => '',
							'align' => 'C',
							'fondo' => 1,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => 10
					);
					$i++;
					
					$PDF->linea[$i] = array(
							'posx' => $L1[$i],
							'ancho' => $W1[$i],
							'texto' => number_format($ingresoCheque,2),
							'borde' => '',
							'align' => 'R',
							'fondo' => 1,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => 10
					);
					$i++;
					
					$PDF->linea[$i] = array(
							'posx' => $L1[$i],
							'ancho' => $W1[$i],
							'texto' => '',
							'borde' => '',
							'align' => 'C',
							'fondo' => 1,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => 10
					);
					
					$PDF->Imprimir_linea();

					$i = 0;

				endif;				



				if($dias > 1):
					$PDF->linea[$i] = array(
							'posx' => $L1[$i],
							'ancho' => $W1[$i],
							'texto' => '',
							'borde' => '',
							'align' => 'C',
							'fondo' => 1,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => 10
					);
					$i++;
				endif;
						
				$PDF->linea[$i] = array(
							'posx' => $L1[$i],
							'ancho' => $W1[$i],
							'texto' => 'TOTAL DE INGRESO',
							'borde' => '',
							'align' => 'R',
							'fondo' => 1,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => 10
				);
				$i++;
						
				$PDF->linea[$i] = array(
							'posx' => $L1[$i],
							'ancho' => $W1[$i],
							'texto' => '',
							'borde' => '',
							'align' => 'R',
							'fondo' => 1,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => 10
				);
				$i++;
				
				$PDF->linea[$i] = array(
						'posx' => $L1[$i],
						'ancho' => $W1[$i],
						'texto' => '',
						'borde' => '',
						'align' => 'C',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
				);
				$i++;
				
				$PDF->linea[$i] = array(
						'posx' => $L1[$i],
						'ancho' => $W1[$i],
						'texto' => number_format($TOTAL_INGRESO + $ingresoCheque,2),
						'borde' => '',
						'align' => 'R',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
				);
				$i++;
				
				$PDF->linea[$i] = array(
						'posx' => $L1[$i],
						'ancho' => $W1[$i],
						'texto' => '',
						'borde' => '',
						'align' => 'C',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
				);
				$i++;
				
				$PDF->Imprimir_linea();
		
			endif;
			$TOTAL_EGRESO += $renglon['AsincronoTemporal']['decimal_1'];
		endif;
		$i = 0;
		
		if($dias > 1):
			$PDF->linea[$i] = array(
					'posx' => $L1[$i],
					'ancho' => $W1[$i],
					'texto' => $renglon['AsincronoTemporal']['texto_1'],
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
			$i++;
		endif;
				
		$PDF->linea[$i] = array(
					'posx' => $L1[$i],
					'ancho' => $W1[$i],
					'texto' => $renglon['AsincronoTemporal']['texto_3'] . ' - ' . $renglon['AsincronoTemporal']['texto_4'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => (strlen($renglon['AsincronoTemporal']['texto_3'] . ' - ' . $renglon['AsincronoTemporal']['texto_4']) > 82 ? $fontSizeBody-2: $fontSizeBody)
		);
		$i++;
				
		if($renglon['AsincronoTemporal']['entero_2'] == 0):
			$PDF->linea[$i] = array(
						'posx' => $L1[$i],
						'ancho' => $W1[$i],
						'texto' => number_format($renglon['AsincronoTemporal']['decimal_1'],2),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
			);
			$i += 3;
		else:		
			$i++;
			$PDF->linea[$i] = array(
						'posx' => $L1[$i],
						'ancho' => $W1[$i],
						'texto' => number_format($renglon['AsincronoTemporal']['decimal_1'],2),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
			);
			$i += 2;
		endif;		

		if($renglon['AsincronoTemporal']['entero_3'] == 1): 
			$error = 'ERROR EN DOCUMENTO';
			$PDF->linea[$i] = array(
						'posx' => $L1[$i],
						'ancho' => $W1[$i],
						'texto' => 'ERROR EN DOCUMENTO',
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
			);
		endif;
		
		$PDF->Imprimir_linea();
		
		
		if(!empty($renglon['AsincronoTemporal']['texto_6']) && $renglon['AsincronoTemporal']['texto_6'] != $renglon['AsincronoTemporal']['texto_3']):
			$PDF->linea[0] = array(
						'posx' => ($dias > 1 ? $L1[1] : $L1[0]),
						'ancho' => ($dias > 1 ? $W1[1] : $W1[0]),
						'texto' => $renglon['AsincronoTemporal']['texto_6'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
			);
				
			$PDF->Imprimir_linea();
		
		endif;
		
		if($renglon['AsincronoTemporal']['texto_7'] == 'CT' || $renglon['AsincronoTemporal']['texto_7'] == 'DB'):
			$PDF->linea[0] = array(
						'posx' => ($dias > 1 ? $L1[1] : $L1[0]),
						'ancho' => ($dias > 1 ? $W1[1] : $W1[0]),
						'texto' => $renglon['AsincronoTemporal']['texto_5'] . ' ' . $renglon['AsincronoTemporal']['texto_8'],
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
	
	if($primeraVez == 0 && $TOTAL_INGRESO > 0):
		$primeraVez = 1;
		$i = 0;
				
		if($ingresoCheque > 0):
			if($dias > 1):
				$PDF->linea[$i] = array(
						'posx' => $L1[$i],
						'ancho' => $W1[$i],
						'texto' => '',
						'borde' => '',
						'align' => 'C',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
				);
				$i++;
			endif;
						
			$PDF->linea[$i] = array(
						'posx' => $L1[$i],
						'ancho' => $W1[$i],
						'texto' => 'INGRESO',
						'borde' => '',
						'align' => 'R',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
			);
			$i++;
							
			$PDF->linea[$i] = array(
						'posx' => $L1[$i],
						'ancho' => $W1[$i],
						'texto' => '',
						'borde' => '',
						'align' => 'R',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
			);
			$i++;
					
			$PDF->linea[$i] = array(
					'posx' => $L1[$i],
					'ancho' => $W1[$i],
					'texto' => '',
					'borde' => '',
					'align' => 'C',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
			);
			$i++;
					
			$PDF->linea[$i] = array(
					'posx' => $L1[$i],
					'ancho' => $W1[$i],
					'texto' => number_format($TOTAL_INGRESO,2),
					'borde' => '',
					'align' => 'R',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
			);
			$i++;
					
			$PDF->linea[$i] = array(
					'posx' => $L1[$i],
					'ancho' => $W1[$i],
					'texto' => '',
					'borde' => '',
					'align' => 'C',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
			);
			
			$PDF->Imprimir_linea();


			$i = 0;
			if($dias > 1):
				$PDF->linea[$i] = array(
						'posx' => $L1[$i],
						'ancho' => $W1[$i],
						'texto' => '',
						'borde' => '',
						'align' => 'C',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
				);
				$i++;
			endif;
							
			$PDF->linea[$i] = array(
						'posx' => $L1[$i],
						'ancho' => $W1[$i],
						'texto' => 'EFECTIVIZACION DE CHEQUES',
						'borde' => '',
						'align' => 'R',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
			);
			$i++;
							
			$PDF->linea[$i] = array(
						'posx' => $L1[$i],
						'ancho' => $W1[$i],
						'texto' => '',
						'borde' => '',
						'align' => 'R',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
			);
			$i++;
					
			$PDF->linea[$i] = array(
					'posx' => $L1[$i],
					'ancho' => $W1[$i],
					'texto' => '',
					'borde' => '',
					'align' => 'C',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
			);
			$i++;
					
			$PDF->linea[$i] = array(
					'posx' => $L1[$i],
					'ancho' => $W1[$i],
					'texto' => number_format($ingresoCheque,2),
					'borde' => '',
					'align' => 'R',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
			);
			$i++;
					
			$PDF->linea[$i] = array(
					'posx' => $L1[$i],
					'ancho' => $W1[$i],
					'texto' => '',
					'borde' => '',
					'align' => 'C',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
			);
					
			$PDF->Imprimir_linea();

			$i = 0;
		endif;				


		if($dias > 1):
			$PDF->linea[$i] = array(
					'posx' => $L1[$i],
					'ancho' => $W1[$i],
					'texto' => '',
					'borde' => '',
					'align' => 'C',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
			);
			$i++;
		endif;
						
		$PDF->linea[$i] = array(
					'posx' => $L1[$i],
					'ancho' => $W1[$i],
					'texto' => 'TOTAL DE INGRESO ',
					'borde' => '',
					'align' => 'R',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
		);
		$i++;
						
		$PDF->linea[$i] = array(
					'posx' => $L1[$i],
					'ancho' => $W1[$i],
					'texto' => '',
					'borde' => '',
					'align' => 'R',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
		);
		$i++;
				
		$PDF->linea[$i] = array(
				'posx' => $L1[$i],
				'ancho' => $W1[$i],
				'texto' => '',
				'borde' => '',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
		$i++;
				
		$PDF->linea[$i] = array(
				'posx' => $L1[$i],
				'ancho' => $W1[$i],
				'texto' => number_format($TOTAL_INGRESO,2),
				'borde' => '',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
		$i++;
				
		$PDF->linea[$i] = array(
				'posx' => $L1[$i],
				'ancho' => $W1[$i],
				'texto' => '',
				'borde' => '',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
		$i++;
				
		$PDF->Imprimir_linea();
	endif;
	
	if($TOTAL_EGRESO > 0):
		$i = 0;

		if($ingresoCheque > 0):
			if($dias > 1):
				$PDF->linea[$i] = array(
						'posx' => $L1[$i],
						'ancho' => $W1[$i],
						'texto' => '',
						'borde' => '',
						'align' => 'C',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
				);
				$i++;
			endif;
							
			$PDF->linea[$i] = array(
						'posx' => $L1[$i],
						'ancho' => $W1[$i],
						'texto' => 'EGRESO ',
						'borde' => '',
						'align' => 'R',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
			);
			$i++;
							
			$PDF->linea[$i] = array(
						'posx' => $L1[$i],
						'ancho' => $W1[$i],
						'texto' => '',
						'borde' => '',
						'align' => 'R',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
			);
			$i++;
					
			$PDF->linea[$i] = array(
					'posx' => $L1[$i],
					'ancho' => $W1[$i],
					'texto' => '',
					'borde' => '',
					'align' => 'C',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
			);
			$i++;
					
			$PDF->linea[$i] = array(
					'posx' => $L1[$i],
					'ancho' => $W1[$i],
					'texto' => number_format($TOTAL_EGRESO,2),
					'borde' => '',
					'align' => 'R',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
			);
			$i++;
					
			$PDF->linea[$i] = array(
					'posx' => $L1[$i],
					'ancho' => $W1[$i],
					'texto' => '',
					'borde' => '',
					'align' => 'C',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
			);
					
			$PDF->Imprimir_linea();

			$i = 0;
			if($dias > 1):
				$PDF->linea[$i] = array(
						'posx' => $L1[$i],
						'ancho' => $W1[$i],
						'texto' => '',
						'borde' => '',
						'align' => 'C',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
				);
				$i++;
			endif;
							
			$PDF->linea[$i] = array(
						'posx' => $L1[$i],
						'ancho' => $W1[$i],
						'texto' => 'EFECTIVIZACION DE CHEQUES ',
						'borde' => '',
						'align' => 'R',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
			);
			$i++;
							
			$PDF->linea[$i] = array(
						'posx' => $L1[$i],
						'ancho' => $W1[$i],
						'texto' => '',
						'borde' => '',
						'align' => 'R',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
			);
			$i++;
					
			$PDF->linea[$i] = array(
					'posx' => $L1[$i],
					'ancho' => $W1[$i],
					'texto' => '',
					'borde' => '',
					'align' => 'C',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
			);
			$i++;
					
			$PDF->linea[$i] = array(
					'posx' => $L1[$i],
					'ancho' => $W1[$i],
					'texto' => number_format($ingresoCheque,2),
					'borde' => '',
					'align' => 'R',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
			);
			$i++;
					
			$PDF->linea[$i] = array(
					'posx' => $L1[$i],
					'ancho' => $W1[$i],
					'texto' => '',
					'borde' => '',
					'align' => 'C',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
			);
					
			$PDF->Imprimir_linea();

			$i = 0;

		endif;
		
						
		if($dias > 1):
			$PDF->linea[$i] = array(
					'posx' => $L1[$i],
					'ancho' => $W1[$i],
					'texto' => '',
					'borde' => '',
					'align' => 'C',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
			);
			$i++;
		endif;
						
		$PDF->linea[$i] = array(
					'posx' => $L1[$i],
					'ancho' => $W1[$i],
					'texto' => 'TOTAL DE EGRESO ',
					'borde' => '',
					'align' => 'R',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
		);
		$i++;
						
		$PDF->linea[$i] = array(
					'posx' => $L1[$i],
					'ancho' => $W1[$i],
					'texto' => '',
					'borde' => '',
					'align' => 'R',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
		);
		$i++;
				
		$PDF->linea[$i] = array(
				'posx' => $L1[$i],
				'ancho' => $W1[$i],
				'texto' => '',
				'borde' => '',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
		$i++;
				
		$PDF->linea[$i] = array(
				'posx' => $L1[$i],
				'ancho' => $W1[$i],
				'texto' => number_format($TOTAL_EGRESO + $ingresoCheque,2),
				'borde' => '',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
		$i++;
				
		$PDF->linea[$i] = array(
				'posx' => $L1[$i],
				'ancho' => $W1[$i],
				'texto' => '',
				'borde' => '',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
		$i++;
				
		$PDF->Imprimir_linea();
	endif;

	$i = 0;
				
	if($dias > 1):
		$PDF->linea[$i] = array(
				'posx' => $L1[$i],
				'ancho' => $W1[$i],
				'texto' => '',
				'borde' => '',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
		$i++;
	endif;
						
	$PDF->linea[$i] = array(
				'posx' => $L1[$i],
				'ancho' => $W1[$i],
				'texto' => 'SALDO AL ' . date('d/m/Y',strtotime($cuenta['BancoCuenta']['fecha_extracto'])),
				'borde' => '',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
	);
	$i++;
						
	$PDF->linea[$i] = array(
				'posx' => $L1[$i],
				'ancho' => $W1[$i],
				'texto' => '',
				'borde' => '',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
	);
	$i++;
				
	$PDF->linea[$i] = array(
			'posx' => $L1[$i],
			'ancho' => $W1[$i],
			'texto' => '',
			'borde' => '',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);
	$i++;
				
	$PDF->linea[$i] = array(
			'posx' => $L1[$i],
			'ancho' => $W1[$i],
			'texto' => number_format($cuenta['BancoCuenta']['importe_conciliacion'] + $TOTAL_INGRESO - $TOTAL_EGRESO,2),
			'borde' => '',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);
	$i++;
				
	$PDF->linea[$i] = array(
			'posx' => $L1[$i],
			'ancho' => $W1[$i],
			'texto' => '',
			'borde' => '',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);
	$i++;
				
	$PDF->Imprimir_linea();
	$PDF->Ln();	
	
	
	// CHEQUES EN CARTERA
//	$W2 = array(34, 34, 34, 39, 34, 34, 34, 34);
	$W2 = array(20, 20, 40, 73, 20, 64, 20, 20);
	$L2 = $PDF->armaAnchoColumnas($W2);

	$PDF->titulo['titulo3'] = "CHEQUES DE TERCEROS";
	
	$PDF->encabezado[0][0] = array(
				'posx' => $L2[0],
				'ancho' => $W2[0],
				'texto' => 'INGRESO',
				'borde' => 'LTB',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);
		
	$PDF->encabezado[0][1] = array(
				'posx' => $L2[1],
				'ancho' => $W2[1],
				'texto' => 'VENCIMIENTO',
				'borde' => 'TB',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);	
		
	$PDF->encabezado[0][2] = array(
				'posx' => $L2[2],
				'ancho' => $W2[2],
				'texto' => 'LIBRADOR',
				'borde' => 'TB',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);
		
			
	$PDF->encabezado[0][3] = array(
				'posx' => $L2[3],
				'ancho' => $W2[3],
				'texto' => 'BANCO NRO.CHEQUE',
				'borde' => 'TB',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);	
			
	$PDF->encabezado[0][4] = array(
				'posx' => $L2[4],
				'ancho' => $W2[4],
				'texto' => 'ENTREGADO',
				'borde' => 'TB',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);	
			
	$PDF->encabezado[0][5] = array(
				'posx' => $L2[5],
				'ancho' => $W2[5],
				'texto' => 'DESTINATARIO',
				'borde' => 'TB',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);	
			
	$PDF->encabezado[0][6] = array(
				'posx' => $L2[6],
				'ancho' => $W2[6],
				'texto' => 'INGRESO',
				'borde' => 'TB',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);	
			
	$PDF->encabezado[0][7] = array(
				'posx' => $L2[7],
				'ancho' => $W2[7],
				'texto' => 'EGRESO',
				'borde' => 'TBR',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);	
			
	///////	
	
	$PDF->AddPage();
	$PDF->Reset();
	
	$i = 0;
	$primeraVez = 0;
	$ingreso_cheque = 0;
	$egreso_cheque = 0;
	$caja_cheque = 0;
	$linea_totales = 0;
	$fontSizeBody = 7;
	foreach($detalle as $cheque):
			if($cheque['AsincronoTemporalDetalle']['entero_2'] == 0):
				$ingreso_cheque += $cheque['AsincronoTemporalDetalle']['decimal_1'];
				$linea_totales = 1;
			else:
				if($linea_totales == 1):
					
					$PDF->linea[0] = array(
							'posx' => $L2[0],
							'ancho' => $W2[0],
							'texto' => '',
							'borde' => '',
							'align' => 'C',
							'fondo' => 1,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => 10
					);
							
					$PDF->linea[1] = array(
								'posx' => $L2[1],
								'ancho' => $W2[1],
								'texto' => '',
								'borde' => '',
								'align' => 'R',
								'fondo' => 1,
								'style' => 'B',
								'colorf' => '#ccc',
								'size' => 10
					);
							
					$PDF->linea[2] = array(
								'posx' => $L2[2],
								'ancho' => $W2[2],
								'texto' => '',
								'borde' => '',
								'align' => 'R',
								'fondo' => 1,
								'style' => 'B',
								'colorf' => '#ccc',
								'size' => 10
					);
					
					$PDF->linea[3] = array(
							'posx' => $L2[3],
							'ancho' => $W2[3],
							'texto' => 'TOTAL INGRESO CHEQUE',
							'borde' => '',
							'align' => 'C',
							'fondo' => 1,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => 10
					);
					
					$PDF->linea[4] = array(
							'posx' => $L2[4],
							'ancho' => $W2[4],
							'texto' => '',
							'borde' => '',
							'align' => 'C',
							'fondo' => 1,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => 10
					);
					
					$PDF->linea[5] = array(
							'posx' => $L2[5],
							'ancho' => $W2[5],
							'texto' => '',
							'borde' => '',
							'align' => 'C',
							'fondo' => 1,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => 10
					);
					
					$PDF->linea[6] = array(
							'posx' => $L2[6],
							'ancho' => $W2[6],
							'texto' => number_format($ingreso_cheque,2),
							'borde' => '',
							'align' => 'C',
							'fondo' => 1,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => 10
					);
					
					$PDF->linea[7] = array(
							'posx' => $L2[7],
							'ancho' => $W2[7],
							'texto' => '',
							'borde' => '',
							'align' => 'C',
							'fondo' => 1,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => 10
					);
					
					$PDF->Imprimir_linea();
			
				endif;
				$egreso_cheque += $cheque['AsincronoTemporalDetalle']['decimal_1'];
				$linea_totales = 2;
			endif;
		
		$PDF->linea[0] = array(
				'posx' => $L2[0],
				'ancho' => $W2[0],
				'texto' => $cheque['AsincronoTemporalDetalle']['texto_1'],
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
				'texto' => $cheque['AsincronoTemporalDetalle']['texto_2'],
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
				'texto' => $cheque['AsincronoTemporalDetalle']['texto_3'],
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
				'texto' => $cheque['AsincronoTemporalDetalle']['texto_5'] . 'CH. ' . $cheque['AsincronoTemporalDetalle']['texto_7'],
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
				'texto' => ($cheque['AsincronoTemporalDetalle']['entero_2'] == 1 ? $cheque['AsincronoTemporalDetalle']['texto_8'] : ''),
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
				'texto' => ($cheque['AsincronoTemporalDetalle']['entero_2'] == 1 ? $cheque['AsincronoTemporalDetalle']['texto_4'] : ''),
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
				'texto' => ($cheque['AsincronoTemporalDetalle']['entero_2'] == 0 ? number_format($cheque['AsincronoTemporalDetalle']['decimal_1'],2) : ''),
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
				'texto' => ($cheque['AsincronoTemporalDetalle']['entero_2'] == 1 ? number_format($cheque['AsincronoTemporalDetalle']['decimal_1'],2) : ''),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
				
		$PDF->Imprimir_linea();
				
	endforeach;	
	
	if($linea_totales == 1):
				
		$PDF->linea[0] = array(
				'posx' => $L2[0],
				'ancho' => $W2[0],
				'texto' => '',
				'borde' => '',
				'align' => '',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
					
		$PDF->linea[1] = array(
				'posx' => $L2[1],
				'ancho' => $W2[1],
				'texto' => '',
				'borde' => '',
				'align' => '',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
						
		$PDF->linea[2] = array(
				'posx' => $L2[2],
				'ancho' => $W2[2],
				'texto' => '',
				'borde' => '',
				'align' => '',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
				
		$PDF->linea[3] = array(
				'posx' => $L2[3],
				'ancho' => $W2[3],
				'texto' => 'TOTAL INGRESO CHEQUE',
				'borde' => '',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
				
		$PDF->linea[4] = array(
				'posx' => $L2[4],
				'ancho' => $W2[4],
				'texto' => '',
				'borde' => '',
				'align' => '',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
				
		$PDF->linea[5] = array(
				'posx' => $L2[5],
				'ancho' => $W2[5],
				'texto' => '',
				'borde' => '',
				'align' => '',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
				
		$PDF->linea[6] = array(
				'posx' => $L2[6],
				'ancho' => $W2[6],
				'texto' => number_format($ingreso_cheque,2),
				'borde' => '',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
				
		$PDF->linea[7] = array(
				'posx' => $L2[7],
				'ancho' => $W2[7],
				'texto' => '',
				'borde' => '',
				'align' => '',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
				
		$PDF->Imprimir_linea();
	endif;
	
	if($linea_totales == 2):
				
		$PDF->linea[0] = array(
				'posx' => $L2[0],
				'ancho' => $W2[0],
				'texto' => '',
				'borde' => '',
				'align' => '',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
					
		$PDF->linea[1] = array(
				'posx' => $L2[1],
				'ancho' => $W2[1],
				'texto' => '',
				'borde' => '',
				'align' => '',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
						
		$PDF->linea[2] = array(
				'posx' => $L2[2],
				'ancho' => $W2[2],
				'texto' => '',
				'borde' => '',
				'align' => '',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
				
		$PDF->linea[3] = array(
				'posx' => $L2[3],
				'ancho' => $W2[3],
				'texto' => 'TOTAL EGRESO CHEQUE',
				'borde' => '',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
				
		$PDF->linea[4] = array(
				'posx' => $L2[4],
				'ancho' => $W2[4],
				'texto' => '',
				'borde' => '',
				'align' => '',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
				
		$PDF->linea[5] = array(
				'posx' => $L2[5],
				'ancho' => $W2[5],
				'texto' => '',
				'borde' => '',
				'align' => '',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
				
		$PDF->linea[6] = array(
				'posx' => $L2[6],
				'ancho' => $W2[6],
				'texto' => '',
				'borde' => '',
				'align' => '',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
				
		$PDF->linea[7] = array(
				'posx' => $L2[7],
				'ancho' => $W2[7],
				'texto' => number_format($egreso_cheque,2),
				'borde' => '',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
		
		$PDF->Imprimir_linea();
	endif;
	
	if($linea_totales == 3):
				
		$PDF->linea[0] = array(
				'posx' => $L2[0],
				'ancho' => $W2[0],
				'texto' => '',
				'borde' => '',
				'align' => '',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
					
		$PDF->linea[1] = array(
				'posx' => $L2[1],
				'ancho' => $W2[1],
				'texto' => '',
				'borde' => '',
				'align' => '',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
						
		$PDF->linea[2] = array(
				'posx' => $L2[2],
				'ancho' => $W2[2],
				'texto' => '',
				'borde' => '',
				'align' => '',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
				
		$PDF->linea[3] = array(
				'posx' => $L2[3],
				'ancho' => $W2[3],
				'texto' => 'TOTAL CAJA CHEQUE',
				'borde' => '',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
				
		$PDF->linea[4] = array(
				'posx' => $L2[4],
				'ancho' => $W2[4],
				'texto' => '',
				'borde' => '',
				'align' => '',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
				
		$PDF->linea[5] = array(
				'posx' => $L2[5],
				'ancho' => $W2[5],
				'texto' => '',
				'borde' => '',
				'align' => '',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
				
		$PDF->linea[6] = array(
				'posx' => $L2[6],
				'ancho' => $W2[6],
				'texto' => '',
				'borde' => '',
				'align' => '',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
				
		$PDF->linea[7] = array(
				'posx' => $L2[7],
				'ancho' => $W2[7],
				'texto' => number_format($caja_cheque,2),
				'borde' => '',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);
		
		$PDF->Imprimir_linea();
	endif;
		
//	$W2 = array(35, 35, 35, 34, 34, 34, 35, 35);
	$W3 = array(34.6, 34.6, 34.6, 34.6, 34.6, 34.6, 34.6, 34.7);
	$L3 = $PDF->armaAnchoColumnas($W3);
	
	$PDF->Ln();
	$PDF->Ln();
	
	$PDF->linea[0] = array(
				'posx' => $L3[0],
				'ancho' => 277,
				'texto' => 'RESUMEN FINAL',
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 20
		);

		$PDF->Imprimir_linea();
		
		
		$PDF->linea[0] = array(
				'posx' => $L3[0],
				'ancho' => 69.2,
				'texto' => 'SALDO AL INICIO',
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);

		
		$PDF->linea[1] = array(
				'posx' => $L3[2],
				'ancho' => 69.2,
				'texto' => 'INGRESO',
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);

		
		$PDF->linea[2] = array(
				'posx' => $L3[4],
				'ancho' => 69.2,
				'texto' => 'EGRESO',
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);

		
		$PDF->linea[3] = array(
				'posx' => $L3[6],
				'ancho' => 69.3,
				'texto' => 'SALDO AL FINAL',
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);

		$PDF->Imprimir_linea();
		
		
		$PDF->linea[0] = array(
				'posx' => $L3[0],
				'ancho' => $W3[0],
				'texto' => 'EFECTIVO',
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => 10
		);

		
		$PDF->linea[1] = array(
				'posx' => $L3[1],
				'ancho' => $W3[1],
				'texto' => 'CHEQUE',
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => 10
		);

		
		$PDF->linea[2] = array(
				'posx' => $L3[2],
				'ancho' => $W3[2],
				'texto' => 'EFECTIVO',
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => 10
		);

		
		$PDF->linea[3] = array(
				'posx' => $L3[3],
				'ancho' => $W3[3],
				'texto' => 'CHEQUE',
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => 10
		);

		
		$PDF->linea[4] = array(
				'posx' => $L3[4],
				'ancho' => $W3[4],
				'texto' => 'EFECTIVO',
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => 10
		);

		
		$PDF->linea[5] = array(
				'posx' => $L3[5],
				'ancho' => $W3[5],
				'texto' => 'CHEQUE',
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => 10
		);
		
		
		$PDF->linea[6] = array(
				'posx' => $L3[6],
				'ancho' => $W3[6],
				'texto' => 'EFECTIVO',
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => 10
		);

		
		$PDF->linea[7] = array(
				'posx' => $L3[7],
				'ancho' => $W3[7],
				'texto' => 'CHEQUE',
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => 10
		);
		
		$PDF->Imprimir_linea();
		
		
		$PDF->linea[0] = array(
				'posx' => $L3[0],
				'ancho' => $W3[0],
				'texto' => number_format($cuenta['BancoCuenta']['importe_conciliacion'] - $saldo_cheque_inicial,2),
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => 10
		);

		
		$PDF->linea[1] = array(
				'posx' => $L3[1],
				'ancho' => $W3[1],
				'texto' => number_format($saldo_cheque_inicial,2),
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => 10
		);

		
		$PDF->linea[2] = array(
				'posx' => $L3[2],
				'ancho' => $W3[2],
				'texto' => number_format($TOTAL_INGRESO + $ingresoCheque - $ingreso_cheque,2),
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => 10
		);

		
		$PDF->linea[3] = array(
				'posx' => $L3[3],
				'ancho' => $W3[3],
				'texto' => number_format($ingreso_cheque,2),
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => 10
		);

		
		$PDF->linea[4] = array(
				'posx' => $L3[4],
				'ancho' => $W3[4],
				'texto' => number_format($TOTAL_EGRESO + $ingresoCheque - $egreso_cheque,2),
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => 10
		);

		
		$PDF->linea[5] = array(
				'posx' => $L3[5],
				'ancho' => $W3[5],
				'texto' => number_format($egreso_cheque,2),
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => 10
		);
		
		
		$PDF->linea[6] = array(
				'posx' => $L3[6],
				'ancho' => $W3[6],
				'texto' => number_format($cuenta['BancoCuenta']['importe_conciliacion'] - $saldo_cheque_inicial + $TOTAL_INGRESO - $ingreso_cheque - $TOTAL_EGRESO + $egreso_cheque,2),
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => 10
		);

		
		$PDF->linea[7] = array(
				'posx' => $L3[7],
				'ancho' => $W3[7],
				'texto' => number_format($saldo_cheque_inicial + $ingreso_cheque - $egreso_cheque,2),
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => 10
		);
		
		$PDF->Imprimir_linea();
		
		
		$PDF->linea[0] = array(
				'posx' => $L3[0],
				'ancho' => 69.2,
				'texto' => number_format($cuenta['BancoCuenta']['importe_conciliacion'],2),
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);

		
		$PDF->linea[1] = array(
				'posx' => $L3[2],
				'ancho' => 69.2,
				'texto' => number_format($TOTAL_INGRESO + $ingresoCheque,2),
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);

		
		$PDF->linea[2] = array(
				'posx' => $L3[4],
				'ancho' => 69.2,
				'texto' => number_format($TOTAL_EGRESO + $ingresoCheque,2),
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);

		
		$PDF->linea[3] = array(
				'posx' => $L3[6],
				'ancho' => 69.3,
				'texto' => number_format($cuenta['BancoCuenta']['importe_conciliacion'] + $TOTAL_INGRESO - $TOTAL_EGRESO,2),
				'borde' => 'LTBR',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
		);

		$PDF->Imprimir_linea();

		$PDF->Ln();
		$PDF->Ln();		
		$PDF->Ln();
		$PDF->Ln();		
		// Imprimir linea final
		$W4 = array(15, 72.33, 15, 72.33, 15, 72.34, 15);
		$L4 = $PDF->armaAnchoColumnas($W4);
//		$PDF->linea[0] = array(
//				'posx' => $L4[0],
//				'ancho' => $W4[0],
//				'texto' => '',
//				'borde' => '',
//				'align' => '',
//				'fondo' => 0,
//				'style' => 'B',
//				'colorf' => '#ccc',
//				'size' => 12
//		);
							
		$PDF->linea[0] = array(
				'posx' => $L4[1],
				'ancho' => $W4[1],
				'texto' => 'C O N F E C C I O N O',
				'borde' => 'T',
				'align' => 'C',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 12
		);
							
//		$PDF->linea[2] = array(
//				'posx' => $L4[2],
//				'ancho' => $W4[2],
//				'texto' => '',
//				'borde' => '',
//				'align' => '',
//				'fondo' => 0,
//				'style' => 'B',
//				'colorf' => '#ccc',
//				'size' => 12
//		);
							
		$PDF->linea[2] = array(
				'posx' => $L4[3],
				'ancho' => $W4[3],
				'texto' => 'A R Q U E O',
				'borde' => 'T',
				'align' => 'C',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 12
		);
							
//		$PDF->linea[4] = array(
//				'posx' => $L4[4],
//				'ancho' => $W4[4],
//				'texto' => '',
//				'borde' => '',
//				'align' => '',
//				'fondo' => 0,
//				'style' => 'B',
//				'colorf' => '#ccc',
//				'size' => 12
//		);
							
		$PDF->linea[3] = array(
				'posx' => $L4[5],
				'ancho' => $W4[5],
				'texto' => 'CONTROL TESORERIA',
				'borde' => 'T',
				'align' => 'C',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 12
		);
				
		$PDF->Imprimir_linea();
		
		
		
		$PDF->Output("listado_planilla_caja.pdf");
exit;
?>
