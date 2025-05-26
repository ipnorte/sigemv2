<?php 
//debug($asientos);
//exit;

App::import('Vendor','listado_pdf');
App::import('Model', 'contabilidad.MutualAsiento');

$oAsiento = new MutualAsiento();

$sql = "SELECT MutualAsiento.*, MutualAsientoRenglon.*
		FROM mutual_asientos MutualAsiento
		INNER JOIN mutual_asiento_renglones MutualAsientoRenglon
		ON MutualAsiento.id = MutualAsientoRenglon.mutual_asiento_id
		WHERE MutualAsiento.mutual_proceso_asiento_id = $procesoId
		ORDER BY MutualAsiento.fecha, MutualAsiento.id, MutualAsientoRenglon.id
		LIMIT 1
";

		$asientos = $oAsiento->query($sql);

//debug($asientos);
//exit;

		


$PDF = new ListadoPDF('L');

$PDF->SetTitle("LIBRO DIARIO BORRADOR");
$PDF->titulo['titulo1'] = "";
$PDF->titulo['titulo2'] = "DESDE FECHA: " . $util->armaFecha($asientos[0]['MutualAsiento']['fecha']) . " - HASTA FECHA: " . $util->armaFecha($fecha_hasta);
$PDF->titulo['titulo3'] = "LIBRO DIARIO BORRADOR";

$PDF->SetFontSizeConf(9);


$PDF->Open();

$W0 = array(20, 59, 58, 30, 25, 25, 58, 2);
$L0 = $PDF->armaAnchoColumnas($W0);

$PDF->encabezado = array();
$fontSizeHeader = 9;
$fontSizeBody = 9;

	

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
			'ancho' => 117,
			'texto' => 'DESCRIPCION',
			'borde' => 'LTBR',
			'align' => 'C',
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

$PDF->encabezado[0][5] = array(
			'posx' => $L0[6],
			'ancho' => 60,
			'texto' => 'COMENTARIO',
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
		
	$aCabecera = array('MutualAsiento' => array('id' => 0));
	$pieAsiento = false;
	$aPie = array();

	$LIMIT = 500;
    $OFFSET = 0;
    $INCREMET = 500;
	
	while(true):
		$sql = "SELECT MutualAsiento.*, MutualAsientoRenglon.*
				FROM mutual_asientos MutualAsiento
				INNER JOIN mutual_asiento_renglones MutualAsientoRenglon
				ON MutualAsiento.id = MutualAsientoRenglon.mutual_asiento_id
				WHERE MutualAsiento.mutual_proceso_asiento_id = $procesoId
				ORDER BY MutualAsiento.fecha, MutualAsiento.id, MutualAsientoRenglon.id
				LIMIT $OFFSET, $LIMIT
		";

		$asientos = $oAsiento->query($sql);
		if(empty($asientos)) break;
		
		$OFFSET += $INCREMET;
		
		foreach ($asientos as $asiento):
			if($aCabecera['MutualAsiento']['id'] != $asiento['MutualAsiento']['id']):
				$aCabecera = $asiento;
				if($pieAsiento):
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
						'texto' => $aPie['MutualAsiento']['referencia'],
						'borde' => 'L',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => (strlen($aPie['MutualAsiento']['referencia']) < 61 ? $fontSizeBody : $fontSizeBody - 1)
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
						'texto' => '',
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
						'texto' => '',
						'borde' => 'LR',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
					);
									
					$PDF->linea[6] = array(
						'posx' => $L0[6],
						'ancho' => 60,
						'texto' => '',
						'borde' => 'LR',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
					);
									
					$PDF->Imprimir_linea();
								
								
					$PDF->linea[0] = array(
						'posx' => $L0[0],
						'ancho' => $W0[0],
						'texto' => ($aPie['MutualAsiento']['debe'] != $aPie['MutualAsiento']['haber'] ? 'ERROR' : ''),
						'borde' => 'LBR',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
					);
								
					$PDF->linea[1] = array(
						'posx' => $L0[1],
						'ancho' => $W0[1],
						'texto' => $aPie['MutualAsiento']['tipo_documento'] . ' ' . $aPie['MutualAsiento']['nro_documento'],
						'borde' => 'LB',
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
						'borde' => 'BR',
						'align' => '',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
					);
								
					$PDF->linea[3] = array(
						'posx' => $L0[3],
						'ancho' => $W0[3],
						'texto' => '',
						'borde' => 'LBR',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
					);
									
					$PDF->linea[4] = array(
						'posx' => $L0[4],
						'ancho' => $W0[4],
						'texto' => number_format($aPie['MutualAsiento']['debe'],2),
						'borde' => 'LTBR',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
					);
									
					$PDF->linea[5] = array(
						'posx' => $L0[5],
						'ancho' => $W0[5],
						'texto' => number_format($aPie['MutualAsiento']['haber'],2),
						'borde' => 'LTBR',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
					);
									
					$PDF->linea[6] = array(
						'posx' => $L0[6],
						'ancho' => 60,
						'texto' => '',
						'borde' => 'LBR',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
					);
									
					$PDF->Imprimir_linea();
					$aPie = array();
				endif;
				
				$nro_asiento_id = $asiento['MutualAsiento']['id'];
				$pieAsiento = true;
				$aPie = $asiento;
				
				$PDF->linea[0] = array(
					'posx' => $L0[0],
					'ancho' => $W0[0],
					'texto' => '',
					'borde' => 'LTR',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);
							
				$PDF->linea[1] = array(
					'posx' => $L0[1],
					'ancho' => 117,
					'texto' => str_pad('  Nro.Int. ' . $asiento['MutualAsiento']['id'] . '  ', 54, '-', STR_PAD_BOTH),
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
							
				$PDF->linea[5] = array(
					'posx' => $L0[6],
					'ancho' => 60,
					'texto' => '',
					'borde' => 'LTR',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
				);
							
				$PDF->Imprimir_linea();
				$fechaPrimera = true; 
			endif;
			
			$PDF->linea[0] = array(
				'posx' => $L0[0],
				'ancho' => $W0[0],
				'texto' => ($fechaPrimera ? date('d/m/Y',strtotime($asiento['MutualAsiento']['fecha'])) : ''),
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
				'texto' => ($asiento['MutualAsientoRenglon']['debe'] > 0 ? $asiento['MutualAsientoRenglon']['descripcion'] : ''),
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
				'texto' => ($asiento['MutualAsientoRenglon']['haber'] > 0 ? $asiento['MutualAsientoRenglon']['descripcion'] : ''),
				'borde' => 'R',
				'align' => '',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => (strlen($asiento['MutualAsientoRenglon']['descripcion']) < 31 ? $fontSizeBody : $fontSizeBody - 2)
			);
					
	
			$PDF->linea[3] = array(
				'posx' => $L0[3],
				'ancho' => $W0[3],
				'texto' => $asiento['MutualAsientoRenglon']['cuenta'],
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
				'texto' => ($asiento['MutualAsientoRenglon']['debe'] > 0 ? number_format($asiento['MutualAsientoRenglon']['debe'],2) : ''),
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
				'texto' => ($asiento['MutualAsientoRenglon']['haber'] > 0 ? number_format($asiento['MutualAsientoRenglon']['haber'],2) : ''),
				'borde' => 'LR',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);
						
			$PDF->linea[6] = array(
				'posx' => $L0[6],
				'ancho' => $W0[6],
				'texto' => $asiento['MutualAsientoRenglon']['error_descripcion'],
				'borde' => 'L',
				'align' => '',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => 7
			);
						
			$PDF->linea[7] = array(
				'posx' => $L0[7],
				'ancho' => $W0[7],
				'texto' => '',
				'borde' => 'R',
				'align' => '',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);
						
			$PDF->Imprimir_linea();
					
		endforeach;
	endwhile;				
		
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
			'texto' => $aPie['MutualAsiento']['referencia'],
			'borde' => 'L',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => (strlen($aPie['MutualAsiento']['referencia']) < 61 ? $fontSizeBody : $fontSizeBody - 1)
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
			'texto' => '',
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
			'texto' => '',
			'borde' => 'LR',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
						
		$PDF->linea[6] = array(
			'posx' => $L0[6],
			'ancho' => 60,
			'texto' => '',
			'borde' => 'LR',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
						
		$PDF->Imprimir_linea();
					
					
		$PDF->linea[0] = array(
			'posx' => $L0[0],
			'ancho' => $W0[0],
			'texto' => ($aPie['MutualAsiento']['debe'] != $aPie['MutualAsiento']['haber'] ? 'ERROR' : ''),
			'borde' => 'LBR',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
					
		$PDF->linea[1] = array(
			'posx' => $L0[1],
			'ancho' => $W0[1],
			'texto' => $aPie['MutualAsiento']['tipo_documento'] . ' ' . $aPie['MutualAsiento']['nro_documento'],
			'borde' => 'LB',
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
			'borde' => 'BR',
			'align' => '',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
					
		$PDF->linea[3] = array(
			'posx' => $L0[3],
			'ancho' => $W0[3],
			'texto' => '',
			'borde' => 'LBR',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
						
		$PDF->linea[4] = array(
			'posx' => $L0[4],
			'ancho' => $W0[4],
			'texto' => number_format($aPie['MutualAsiento']['debe'],2),
			'borde' => 'LTBR',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
						
		$PDF->linea[5] = array(
			'posx' => $L0[5],
			'ancho' => $W0[5],
			'texto' => number_format($aPie['MutualAsiento']['haber'],2),
			'borde' => 'LTBR',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
						
		$PDF->linea[6] = array(
			'posx' => $L0[6],
			'ancho' => 60,
			'texto' => '',
			'borde' => 'LBR',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
						
		$PDF->Imprimir_linea();
	
	




$PDF->Output("libro_diario_borrador.pdf");
exit;

?>