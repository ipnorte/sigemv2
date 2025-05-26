<?php 

App::import('Vendor','libro_pdf');
App::import('Model', 'contabilidad.AsientoRenglon');
App::import('Model', 'contabilidad.PlanCuenta');

$oPlanCuenta = new PlanCuenta();
$oAsientoRenglon = new AsientoRenglon();

$sql = "SELECT Asiento.*
		FROM co_asientos Asiento
		WHERE Asiento.co_ejercicio_id = $ejercicio_id AND Asiento.fecha >= '$fecha_desde' AND Asiento.fecha <= '$fecha_hasta' AND Asiento.borrado = 0
		ORDER BY Asiento.fecha, Asiento.nro_asiento
		LIMIT 1
";

$asientos = $oAsientoRenglon->query($sql);
// $util->armaFecha($asientos[0]['Asiento']['fecha']


$PDF = new LibroPDF();

$PDF->SetTitle("LIBRO DIARIO");
$PDF->titulo['titulo1'] = "";
$PDF->titulo['titulo2'] = "DESDE FECHA: " . $util->armaFecha($asientos[0]['Asiento']['fecha']) . " - HASTA FECHA: " . $util->armaFecha($fecha_hasta);
$PDF->titulo['titulo3'] = "LIBRO DIARIO";

$PDF->SetFontSizeConf(9);

$PDF->Open();

// $W0 = array(29, 70, 69, 39, 34, 34);
$W0 = array(17, 10, 90, 25, 24, 24);
$L0 = $PDF->armaAnchoColumnas($W0);

$PDF->encabezado = array();
$fontSizeHeader = 9;
$fontSizeBody = 7.5;

	

$PDF->encabezado[0][0] = array(
			'posx' => $L0[0],
			'ancho' => $W0[0],
			'texto' => 'FECHA',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$ancho = $W0[1] + $W0[2];
$PDF->encabezado[0][1] = array(
			'posx' => $L0[1],
			'ancho' => $ancho,
			'texto' => 'DESCRIPCION',
			'borde' => 'LTBR',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	
$PDF->encabezado[0][2] = array(
			'posx' => $L0[3],
			'ancho' => $W0[3],
			'texto' => 'REFERENCIA',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[0][3] = array(
			'posx' => $L0[4],
			'ancho' => $W0[4],
			'texto' => 'DEBE',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->encabezado[0][4] = array(
			'posx' => $L0[5],
			'ancho' => $W0[5],
			'texto' => 'HABER',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	

	///////	


	$PDF->AddPage();
	$PDF->Reset();
		

	$LIMIT = 500;
    $OFFSET = 0;
    $INCREMET = 500;
	
	while(true):
		$sql = "SELECT Asiento.*
				FROM co_asientos Asiento
				WHERE Asiento.co_ejercicio_id = $ejercicio_id AND Asiento.fecha >= '$fecha_desde' AND Asiento.fecha <= '$fecha_hasta' AND Asiento.borrado = 0
				ORDER BY Asiento.fecha, Asiento.nro_asiento
				LIMIT $OFFSET, $LIMIT
		";

		$asientos = $oAsientoRenglon->query($sql);

		if(empty($asientos)) break;
		
		$OFFSET += $INCREMET;
		
		foreach ($asientos as $asiento):
		
				$PDF->aFooter = array();
				
				if($PDF->GetY() >= $PDF->getPageHeight() - 24) $PDF->setY(300);
				$PDF->linea[0] = array(
					'posx' => $L0[0],
					'ancho' => $W0[0],
					'texto' => '',
					'borde' => 'LTR',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);
							
				$PDF->linea[1] = array(
					'posx' => $L0[1],
					'ancho' => $ancho,
					'texto' => str_pad(' Asiento Nro.: ' . $asiento['Asiento']['nro_asiento'] . '  ', 56, '-', STR_PAD_BOTH),
					'borde' => 'LTR',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);
							
				$PDF->linea[2] = array(
					'posx' => $L0[3],
					'ancho' => $W0[3],
					'texto' => '',
					'borde' => 'LTR',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);
							
				$PDF->linea[3] = array(
					'posx' => $L0[4],
					'ancho' => $W0[4],
					'texto' => '',
					'borde' => 'LTR',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);
							
				$PDF->linea[4] = array(
					'posx' => $L0[5],
					'ancho' => $W0[5],
					'texto' => '',
					'borde' => 'LTR',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);
							
				$PDF->Imprimir_linea();


				// Configuro lo que va a ser el Transporte del Asiento
				$PDF->aFooter[0] = array(
					'posx' => $L0[0],
					'ancho' => $W0[0],
					'texto' => '',
					'borde' => 'LTB',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);
							
				$PDF->aFooter[1] = array(
					'posx' => $L0[1],
					'ancho' => $ancho,
					'texto' => '',
					'borde' => 'TB',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);
							
				$PDF->aFooter[2] = array(
					'posx' => $L0[3],
					'ancho' => $W0[3],
					'texto' => 'TRANSPORTE: ',
					'borde' => 'TBR',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);
							
				$PDF->aFooter[3] = array(
					'posx' => $L0[4],
					'ancho' => $W0[4],
					'texto' => 0.00,
					'borde' => 'LTBR',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);
							
				$PDF->aFooter[4] = array(
					'posx' => $L0[5],
					'ancho' => $W0[5],
					'texto' => 0.00,
					'borde' => 'LTBR',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);



				$fechaPrimera = true; 
				$nAsientoId = $asiento['Asiento']['id'];
				
				// Primero traigo lo que tiene el debe
				$sql = "SELECT	PlanCuenta.cuenta, PlanCuenta.descripcion, AsientoRenglon.*
						FROM	co_asiento_renglones AsientoRenglon
						INNER JOIN co_plan_cuentas PlanCuenta
						ON AsientoRenglon.co_plan_cuenta_id = PlanCuenta.id
						WHERE	AsientoRenglon.co_asiento_id = '$nAsientoId' AND AsientoRenglon.debe > 0
				";
				
				$debe_renglones = $oAsientoRenglon->query($sql);
				$nTransporteDebe = 0.00;
				foreach($debe_renglones as $renglon):
				
						$PDF->linea[0] = array(
							'posx' => $L0[0],
							'ancho' => $W0[0],
							'texto' => ($fechaPrimera ? date('d/m/Y',strtotime($asiento['Asiento']['fecha'])) : ''),
							'borde' => 'LR',
							'align' => 'C',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
						);
								
						if($fechaPrimera):
							$fechaPrimera = false;
						endif;
									
						$PDF->linea[1] = array(
							'posx' => $L0[1],
							'ancho' => $W0[1],
							'texto' => $renglon['PlanCuenta']['descripcion'],
							'borde' => 'L',
							'align' => 'L',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
						);
										
						$PDF->linea[2] = array(
							'posx' => $L0[2],
							'ancho' => $W0[2],
							'texto' => '',
							'borde' => 'R',
							'align' => '',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
						);
								
				
						$PDF->linea[3] = array(
							'posx' => $L0[3],
							'ancho' => $W0[3],
							'texto' => $oPlanCuenta->formato_cuenta($renglon['PlanCuenta']['cuenta'], $ejercicio),
							'borde' => 'LR',
							'align' => 'L',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
						);
									
						$PDF->linea[4] = array(
							'posx' => $L0[4],
							'ancho' => $W0[4],
							'texto' => number_format($renglon['AsientoRenglon']['debe'],2),
							'borde' => 'LR',
							'align' => 'R',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
						);
									
						$PDF->linea[5] = array(
							'posx' => $L0[5],
							'ancho' => $W0[5],
							'texto' => '',
							'borde' => 'LR',
							'align' => 'R',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
						);
									
						$PDF->Imprimir_linea();
				
						$nTransporteDebe += $renglon['AsientoRenglon']['debe'];
						$PDF->aFooter[3]['texto'] = number_format($nTransporteDebe,2);
				endforeach;
				
			
			
				// Aca traigo lo que tiene el haber
				$sql = "SELECT	PlanCuenta.cuenta, PlanCuenta.descripcion, AsientoRenglon.*
						FROM	co_asiento_renglones AsientoRenglon
						INNER JOIN co_plan_cuentas PlanCuenta
						ON AsientoRenglon.co_plan_cuenta_id = PlanCuenta.id
						WHERE	AsientoRenglon.co_asiento_id = '$nAsientoId' AND AsientoRenglon.haber > 0
				";
				
				$haber_renglones = $oAsientoRenglon->query($sql);
				$nTransporteHaber = 0.00;
				foreach($haber_renglones as $renglon):

						$PDF->linea[0] = array(
							'posx' => $L0[0],
							'ancho' => $W0[0],
							'texto' => '',
							'borde' => 'LR',
							'align' => 'C',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
						);
								
						$PDF->linea[1] = array(
							'posx' => $L0[1],
							'ancho' => $W0[1],
							'texto' => '',
							'borde' => 'L',
							'align' => 'L',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
						);
										
						$PDF->linea[2] = array(
							'posx' => $L0[2],
							'ancho' => $W0[2],
							'texto' => $renglon['PlanCuenta']['descripcion'],
							'borde' => 'R',
							'align' => 'L',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
						);
								
				
						$PDF->linea[3] = array(
							'posx' => $L0[3],
							'ancho' => $W0[3],
							'texto' => $oPlanCuenta->formato_cuenta($renglon['PlanCuenta']['cuenta'], $ejercicio),
							'borde' => 'LR',
							'align' => 'L',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
						);
									
						$PDF->linea[4] = array(
							'posx' => $L0[4],
							'ancho' => $W0[4],
							'texto' => '',
							'borde' => 'LR',
							'align' => 'R',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
						);
									
						$PDF->linea[5] = array(
							'posx' => $L0[5],
							'ancho' => $W0[5],
							'texto' => number_format($renglon['AsientoRenglon']['haber'],2),
							'borde' => 'LR',
							'align' => 'R',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
						);
									
						$PDF->Imprimir_linea();
						
						$nTransporteHaber += $renglon['AsientoRenglon']['haber'];
						$PDF->aFooter[4]['texto'] = number_format($nTransporteHaber,2);
				
				endforeach;
			
				$PDF->linea[0] = array(
					'posx' => $L0[0],
					'ancho' => $W0[0],
					'texto' => '',
					'borde' => 'LR',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);
								
				$PDF->linea[1] = array(
					'posx' => $L0[1],
					'ancho' => $ancho,
					'texto' => $asiento['Asiento']['referencia'],
					'borde' => 'LR',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);
										
				$PDF->linea[2] = array(
					'posx' => $L0[3],
					'ancho' => $W0[3],
					'texto' => '',
					'borde' => 'R',
					'align' => '',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);
								
				$PDF->linea[3] = array(
					'posx' => $L0[4],
					'ancho' => $W0[4],
					'texto' => '',
					'borde' => 'LR',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);
									
				$PDF->linea[4] = array(
					'posx' => $L0[5],
					'ancho' => $W0[5],
					'texto' => '',
					'borde' => 'LR',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);
									
				$PDF->Imprimir_linea();
								
								
				$PDF->linea[0] = array(
					'posx' => $L0[0],
					'ancho' => $W0[0],
					'texto' => '',
					'borde' => 'LBR',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);
								
				$PDF->linea[1] = array(
					'posx' => $L0[1],
					'ancho' => $ancho,
					'texto' => $asiento['Asiento']['tipo_documento'] . ' ' . $asiento['Asiento']['nro_documento'],
					'borde' => 'LBR',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);
										
				$PDF->linea[2] = array(
					'posx' => $L0[3],
					'ancho' => $W0[3],
					'texto' => '',
					'borde' => 'BR',
					'align' => '',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);
								
				$PDF->linea[3] = array(
					'posx' => $L0[4],
					'ancho' => $W0[4],
					'texto' => number_format($asiento['Asiento']['debe'],2),
					'borde' => 'LTBR',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);
									
				$PDF->linea[4] = array(
					'posx' => $L0[5],
					'ancho' => $W0[5],
					'texto' => number_format($asiento['Asiento']['haber'],2),
					'borde' => 'LTBR',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);
									
				$PDF->Imprimir_linea();
				
		

			endforeach; 
	endwhile;
	
	$PDF->aFooter = array();
	
$PDF->Output("libro_diario.pdf");
exit;
?>
