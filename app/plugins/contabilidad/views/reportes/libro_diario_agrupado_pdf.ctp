<?php 

    App::import('Vendor','libro_pdf');
    App::import('Model', 'contabilidad.AsientoRenglon');
    App::import('Model', 'contabilidad.PlanCuenta');

    $oPlanCuenta = new PlanCuenta();
    $oAsientoRenglon = new AsientoRenglon();

    // $util->armaFecha($asientos[0]['Asiento']['fecha']

    $PDF = new LibroPDF();

    $PDF->lEncabezado = ($encabezado === '0' ? 0 : 1);
    $PDF->SetTitle("LIBRO DIARIO");
    $PDF->titulo['titulo1'] = "";
    $PDF->titulo['titulo2'] = "DESDE FECHA: " . $util->armaFecha($ejercicio['fecha_desde']) . " - HASTA FECHA: " . $util->armaFecha($ejercicio['fecha_hasta']);
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

    ///////	


	$PDF->AddPage();
	$PDF->Reset();
		

	$LIMIT = 500;
        $OFFSET = 0;
        $INCREMET = 500;
	
	$OFFSET += $INCREMET;
		
		$nTransporteDebe = 0.00;
		$nTransporteHaber = 0.00;

                $PDF->aFooter = array();

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
			'texto' => number_format($nTransporteDebe,2),
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
			'texto' => number_format($nTransporteHaber,2),
			'borde' => 'LTBR',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);

				
		foreach ($aTemporal as $asiento):
		
				if($PDF->GetY() >= $PDF->getPageHeight() - 15):
                                    $PDF->nBackPosY = $PDF->GetY();
                                    $PDF->setY(300);
                                endif;
                                
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
					'texto' => str_pad(' Asiento Nro.: ' . $asiento['AsincronoTemporal']['entero_1'] . '  ', 56, '-', STR_PAD_BOTH),
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



				$fechaPrimera = true; 
				
				foreach($asiento['AsincronoTemporalDetalle'] as $renglon):
                                    
                                    if($renglon['decimal_1'] > 0):
				
                			//	if($PDF->GetY() >= $PDF->getPageHeight() - 24) $PDF->setY(300);
						$PDF->linea[0] = array(
							'posx' => $L0[0],
							'ancho' => $W0[0],
							'texto' => ($fechaPrimera ? date('d/m/Y',strtotime($asiento['AsincronoTemporal']['texto_1'])) : ''),
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
							'texto' => $renglon['texto_2'],
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
							'texto' => $oPlanCuenta->formato_cuenta($renglon['texto_1'], $ejercicio),
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
							'texto' => number_format($renglon['decimal_1'],2),
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
				
						$nTransporteDebe += $renglon['decimal_1'];
						$PDF->aFooter[3]['texto'] = number_format($nTransporteDebe,2);
                                    endif;
				endforeach;
				
			
			
				foreach($asiento['AsincronoTemporalDetalle'] as $renglon):

                                    if($renglon['decimal_2'] > 0):
                			//	if($PDF->GetY() >= $PDF->getPageHeight() - 24) $PDF->setY(300);
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
							'texto' => $renglon['texto_2'],
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
							'texto' => $oPlanCuenta->formato_cuenta($renglon['texto_1'], $ejercicio),
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
							'texto' => number_format($renglon['decimal_2'],2),
							'borde' => 'LR',
							'align' => 'R',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
						);
									
						$PDF->Imprimir_linea();
						
						$nTransporteHaber += $renglon['decimal_2'];
						$PDF->aFooter[4]['texto'] = number_format($nTransporteHaber,2);
                                    endif;
				endforeach;
		

			endforeach; 
	
//	if($PDF->GetY() >= $PDF->getPageHeight() - 15):
//            $PDF->nBackPosY = $PDF->GetY();
//            $PDF->setY(300);
//            $PDF->aFooter[2]['texto'] = 'TOTAL GENERAL: ';
//            $PDF->Footer(5);
//        else:
	$PDF->aFooter = array();

	$PDF->linea[0] = array(
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
							
	$PDF->linea[1] = array(
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
							
	$PDF->linea[2] = array(
		'posx' => $L0[3],
		'ancho' => $W0[3],
		'texto' => 'TOTAL GENERAL: ',
		'borde' => 'TBR',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $fontSizeBody
	);
							
	$PDF->linea[3] = array(
		'posx' => $L0[4],
		'ancho' => $W0[4],
		'texto' => number_format($nTransporteDebe,2),
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
		'texto' => number_format($nTransporteHaber,2),
		'borde' => 'LTBR',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $fontSizeBody
	);

            $PDF->Imprimir_linea();
//        endif;
        

    $PDF->Output("libro_diario_agrupado.pdf");
exit;
?>
