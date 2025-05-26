<?php 

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF('L');

$PDF->SetTitle("LISTADO CONCEPTO DE TESORERIA");
$PDF->titulo['titulo1'] = "LISTADO CONCEPTO DE TESORERIA";
$PDF->titulo['titulo2'] = "DESDE FECHA: " . $util->armaFecha($fecha_desde) . " - HASTA FECHA: " . $util->armaFecha($fecha_hasta);

$PDF->SetFontSizeConf(6);

$PDF->Open();


$W1 = array(7, 14, 14, 81, 30, 77, 18, 18, 18);
$L1 = $PDF->armaAnchoColumnas($W1);

$PDF->encabezado = array();
$fontSizeHeader = 6.5;
$fontSizeBody = 6;

$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => 'MOVIM.',
			'borde' => 'LTB',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][1] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => 'FECHA',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
$PDF->encabezado[0][2] = array(
			'posx' => $L1[2],
			'ancho' => $W1[2],
			'texto' => 'F.VENC.',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][3] = array(
			'posx' => $L1[3],
			'ancho' => $W1[3],
			'texto' => 'A LA ORDEN DE',
			'borde' => 'TB',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
$PDF->encabezado[0][4] = array(
			'posx' => $L1[4],
			'ancho' => $W1[4],
			'texto' => 'T.DOCUMENTO',
			'borde' => 'TB',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
$PDF->encabezado[0][5] = array(
			'posx' => $L1[5],
			'ancho' => $W1[5],
			'texto' => 'DESCRIPCION',
			'borde' => 'TB',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][6] = array(
			'posx' => $L1[6],
			'ancho' => $W1[6],
			'texto' => 'NRO.OPERACION',
			'borde' => 'TB',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][7] = array(
			'posx' => $L1[7],
			'ancho' => $W1[7],
			'texto' => 'DEBE',
			'borde' => 'TB',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][8] = array(
			'posx' => $L1[8],
			'ancho' => $W1[8],
			'texto' => 'HABER',
			'borde' => 'TRB',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	///////	

	
	$TOTAL_DEBE = $TOTAL_HABER = 0;
	
	$i=0;
	while($i < count($aDatos)):
	  	$concepto = $aDatos[$i]['AsincronoTemporal']['clave_1'];
		$PDF->titulo['titulo3'] = $aDatos[$i]['AsincronoTemporal']['texto_1'];
		$PDF->AddPage();
		$PDF->Reset();
		
		while($concepto == $aDatos[$i]['AsincronoTemporal']['clave_1'] && $i < count($aDatos)):
			$banco = $aDatos[$i]['AsincronoTemporal']['clave_2'];
			$PDF->linea[0] = array(
						'posx' => $L1[0],
						'ancho' => 277,
						'texto' => $aDatos[$i]['AsincronoTemporal']['texto_2'],
						'borde' => 'B',
						'align' => 'L',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
			);

			$PDF->Imprimir_linea();
			$PDF->ln(2);
			
			$sTOTAL_DEBE = $sTOTAL_HABER = 0;
			
			while($banco == $aDatos[$i]['AsincronoTemporal']['clave_2'] && $concepto == $aDatos[$i]['AsincronoTemporal']['clave_1'] && $i < count($aDatos)):
				$anulado = '';
				if($aDatos[$i]['AsincronoTemporal']['texto_3'] == '1'):
					$anulado = ' (ANULADO)';
				endif;

				$PDF->linea[0] = array(
						'posx' => $L1[0],
						'ancho' => $W1[0],
						'texto' => $aDatos[$i]['AsincronoTemporal']['entero_1'],
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
				
				$PDF->linea[1] = array(
							'posx' => $L1[1],
							'ancho' => $W1[1],
							'texto' => $util->armaFecha($aDatos[$i]['AsincronoTemporal']['texto_8']),
							'borde' => '',
							'align' => 'C',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
				);
				
				$PDF->linea[2] = array(
							'posx' => $L1[2],
							'ancho' => $W1[2],
							'texto' => $util->armaFecha($aDatos[$i]['AsincronoTemporal']['texto_9']),
							'borde' => '',
							'align' => 'C',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
				);
				
				$PDF->linea[3] = array(
							'posx' => $L1[3],
							'ancho' => $W1[3],
							'texto' => $aDatos[$i]['AsincronoTemporal']['texto_4'] . $anulado,
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
							'texto' => $aDatos[$i]['AsincronoTemporal']['texto_5'],
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
							'texto' => $aDatos[$i]['AsincronoTemporal']['texto_7'] . '  ' . $aDatos[$i]['AsincronoTemporal']['texto_13'],
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
							'texto' => $aDatos[$i]['AsincronoTemporal']['texto_10'],
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
							'texto' => number_format($aDatos[$i]['AsincronoTemporal']['decimal_1'],2),
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
							'texto' => number_format($aDatos[$i]['AsincronoTemporal']['decimal_2'],2),
							'borde' => '',
							'align' => 'R',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
				);
				
				$PDF->Imprimir_linea();
				
				//ACUMULO
				$sTOTAL_DEBE += $aDatos[$i]['AsincronoTemporal']['decimal_1'];
				$sTOTAL_HABER += $aDatos[$i]['AsincronoTemporal']['decimal_2'];

				$TOTAL_DEBE += $aDatos[$i]['AsincronoTemporal']['decimal_1'];
				$TOTAL_HABER += $aDatos[$i]['AsincronoTemporal']['decimal_2'];
				
				
				$i++;
			endwhile;
			
			//MANDO EL TOTAL DEL CONCEPTO
			$PDF->linea[7] = array(
					'posx' => $L1[7],
					'ancho' => $W1[7],
					'texto' => $util->nf($sTOTAL_DEBE),
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
					'texto' => $util->nf($sTOTAL_HABER),
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);						
			$PDF->Imprimir_linea();
			$PDF->Ln();
		endwhile;
	endwhile;

	//MANDO EL TOTAL GENERAL
	$PDF->linea[6] = array(
				'posx' => $L1[6],
				'ancho' => $W1[6],
				'texto' => "TOTAL GENERAL",
				'borde' => 'T',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
	);	
	$PDF->linea[7] = array(
			'posx' => $L1[7],
			'ancho' => $W1[7],
			'texto' => $util->nf($TOTAL_DEBE),
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
			'texto' => $util->nf($TOTAL_HABER),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);						
	$PDF->Imprimir_linea();
	$PDF->Ln();	
	
	
	
$PDF->Output("listado_concepto_tesoreria.pdf");
exit;
?>
