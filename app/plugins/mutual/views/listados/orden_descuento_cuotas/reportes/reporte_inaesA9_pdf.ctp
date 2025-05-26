<?php 

//debug($datos);
//exit;


App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("INAES Art #9 - " . $util->periodo($periodoAnalisis,true));
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->textoHeader =  "INAES Art #9 - ".$util->periodo($periodoAnalisis)." (S.E.U.O.)";
$PDF->titulo['titulo1'] = "";
$PDF->titulo['titulo3'] = "INAES ARTICULO Nro.9 - " . $util->periodo($periodoAnalisis,true);
$PDF->titulo['titulo2'] = "";

$PDF->AddPage();
$PDF->Reset();

if(!empty($datos)):

//	$ACU_CANT = $ACU_IMPO = 0;
//	$ACU_CATEGORIA_CANT = $ACU_CATEGORIA_IMPO = 0;
//	$ACU_ORG_CANT = $ACU_ORG_IMPO = 0;
//	$ACU_COB_CANT = $ACU_COB_IMPO = 0;
    
        $ACU_CANT = $ACU_IMPO = $ACU_IMPO_TPROM = $ACU_COB_TPROM = 0;

        $ACU_CATEGORIA_CANT = $ACU_CATEGORIA_IMPO = $ACU_CATEGORIA_IMPO_PROM = $ACU_CATEGORIA_COB_PROM = 0;
        $ACU_ORG_CANT = $ACU_ORG_IMPO = $ACU_ORG_IMPO_PROM = $ACU_ORG_COB_PROM = 0;
        $ACU_COB_CANT = $ACU_COB_IMPO = 0;

        $ACU_IMPO_PROM = $ACU_COB_PROM = 0; 
        
        if(!empty($datos['PADRON'])):

            $PDF->linea[0] = array(
                                    'posx' => 10,
                                    'ancho' => 190,
                                    'texto' => 'INFORME PADRON SOCIOS',
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => 'B',
                                    'colorf' => '#ccc',
                                    'size' => 10
                    );           
            $PDF->Imprimir_linea();
            
            $PDF->Ln(2);
            
            $PDF->linea[0] = array(
                                'posx' => 10,
                                'ancho' => 115,
                                'texto' => 'CATEGORIA',
                                'borde' => 'TBL',
                                'align' => 'L',
                                'fondo' => 1,
                                'style' => 'B',
                                'colorf' => '#ccc',
                                'size' => 8
                ); 
            $PDF->linea[1] = array(
                                    'posx' => 125,
                                    'ancho' => 25,
                                    'texto' => "Altas Periodo",
                                    'borde' => 'TBLR',
                                    'align' => 'C',
                                    'fondo' => 1,
                                    'style' => 'B',
                                    'colorf' => '#ccc',
                                    'size' => 8
                    );
            $PDF->linea[2] = array(
                                    'posx' => 150,
                                    'ancho' => 25,
                                    'texto' => "Bajas Periodo",
                                    'borde' => 'TBLR',
                                    'align' => 'C',
                                    'fondo' => 1,
                                    'style' => 'B',
                                    'colorf' => '#ccc',
                                    'size' => 8
                    );
            $PDF->linea[3] = array(
                                    'posx' => 175,
                                    'ancho' => 25,
                                    'texto' => "Padron Vigente",
                                    'borde' => 'TBLR',
                                    'align' => 'C',
                                    'fondo' => 1,
                                    'style' => 'B',
                                    'colorf' => '#ccc',
                                    'size' => 8
                    );                 
            $PDF->Imprimir_linea();        
            foreach($datos['PADRON'] as $periodo => $socios):
                $TOT_ALTAS = $TOT_BAJAS = $TOT_PADRON = 0;
                foreach($socios as $socio):
                    
                    $TOT_ALTAS += $socio[0]['cantidad_altas'];
                    $TOT_BAJAS += $socio[0]['cantidad_bajas'];
                    $TOT_PADRON += $socio['t1']['cantidad_total'];                    
                    
                    $PDF->linea[0] = array(
                                    'posx' => 10,
                                    'ancho' => 115,
                                    'texto' => $socio['t1']['concepto_1'],
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#ccc',
                                    'size' => 10
                    );
                    $PDF->linea[1] = array(
                                    'posx' => 125,
                                    'ancho' => 25,
                                    'texto' => $socio[0]['cantidad_altas'],
                                    'borde' => '',
                                    'align' => 'C',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#ccc',
                                    'size' => 10
                    );
                    $PDF->linea[2] = array(
                                    'posx' => 150,
                                    'ancho' => 25,
                                    'texto' => $socio[0]['cantidad_bajas'],
                                    'borde' => '',
                                    'align' => 'C',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#ccc',
                                    'size' => 10
                    );
                    $PDF->linea[3] = array(
                                    'posx' => 175,
                                    'ancho' => 25,
                                    'texto' => $socio['t1']['cantidad_total'],
                                    'borde' => '',
                                    'align' => 'C',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#ccc',
                                    'size' => 10
                    );                    
                    $PDF->Imprimir_linea();
                endforeach;

                $PDF->linea[0] = array(
                                'posx' => 10,
                                'ancho' => 115,
                                'texto' => 'TOTALES ' . $util->periodo($periodoAnalisis,true),
                                'borde' => 'T',
                                'align' => 'L',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '#ccc',
                                'size' => 10
                );
                $PDF->linea[1] = array(
                                'posx' => 125,
                                'ancho' => 25,
                                'texto' => $TOT_ALTAS,
                                'borde' => 'T',
                                'align' => 'C',
                                'fondo' => 0,
                                'style' => '',
                                'colorf' => '#ccc',
                                'size' => 10
                );
                $PDF->linea[2] = array(
                                'posx' => 150,
                                'ancho' => 25,
                                'texto' => $TOT_BAJAS,
                                'borde' => 'T',
                                'align' => 'C',
                                'fondo' => 0,
                                'style' => '',
                                'colorf' => '#ccc',
                                'size' => 10
                );
                $PDF->linea[3] = array(
                                'posx' => 175,
                                'ancho' => 25,
                                'texto' => $TOT_PADRON,
                                'borde' => 'T',
                                'align' => 'C',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '#ccc',
                                'size' => 10
                );                    
                $PDF->Imprimir_linea();                
                
            endforeach;
            
            $PDF->Ln(5);
            
        endif;
        
        
        $PDF->linea[0] = array(
                                'posx' => 10,
                                'ancho' => 190,
                                'texto' => 'VALORES CALCULADOS PARA CUOTA SOCIAL COBRADA',
                                'borde' => '',
                                'align' => 'L',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '#ccc',
                                'size' => 10
                );           
        $PDF->Imprimir_linea();        
        
        $PDF->Ln(2);

	foreach($datos['COBRANZA'] as $periodo => $dato):
            
            $count_0 = count($dato);
	
            
            
		foreach($dato as $categoria => $valores):
                    
                    $count_1 = count($valores);
	
			$PDF->linea[0] = array(
						'posx' => 10,
						'ancho' => 90,
						'texto' => $util->globalDato($categoria),
						'borde' => 'TBLR',
						'align' => 'L',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
				);
			$PDF->linea[1] = array(
						'posx' => 100,
						'ancho' => 25,
						'texto' => "SOCIOS",
						'borde' => 'TBLR',
						'align' => 'C',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
				);
			$PDF->linea[2] = array(
						'posx' => 125,
						'ancho' => 25,
						'texto' => "CUOTA PROM",
						'borde' => 'TBLR',
						'align' => 'C',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
				);
			$PDF->linea[3] = array(
						'posx' => 150,
						'ancho' => 25,
						'texto' => "COBRADO",
						'borde' => 'TBLR',
						'align' => 'C',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
				);
			$PDF->linea[4] = array(
						'posx' => 175,
						'ancho' => 25,
						'texto' => "COB.PROM",
						'borde' => 'TBLR',
						'align' => 'C',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
				);                        
			$PDF->Imprimir_linea();

			foreach($valores as $codOrg => $valores_1):
                            
                                

				$PDF->linea[0] = array(
							'posx' => 10,
							'ancho' => 190,
							'texto' => $util->globalDato($codOrg),
							'borde' => '',
							'align' => 'L',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => 10
					);
				$PDF->Imprimir_linea();			
			
                                $count_2 = count($valores_1);
                                
				foreach($valores_1 as $codCobr => $valores_2):

					$PDF->linea[0] = array(
								'posx' => 10,
								'ancho' => 90,
								'texto' => $util->globalDato($codCobr),
								'borde' => '',
								'align' => 'L',
								'fondo' => 0,
								'style' => '',
								'colorf' => '#ccc',
								'size' => 10
						);
					$PDF->linea[1] = array(
								'posx' => 100,
								'ancho' => 25,
								'texto' => $valores_2['cantidad_socios'],
								'borde' => '',
								'align' => 'C',
								'fondo' => 0,
								'style' => '',
								'colorf' => '#ccc',
								'size' => 10
						);
					$PDF->linea[2] = array(
								'posx' => 125,
								'ancho' => 25,
								'texto' => $util->nf($valores_2['importe_promedio']),
								'borde' => '',
								'align' => 'R',
								'fondo' => 0,
								'style' => '',
								'colorf' => '#ccc',
								'size' => 10
						);
					$PDF->linea[3] = array(
								'posx' => 150,
								'ancho' => 25,
								'texto' => $util->nf($valores_2['cobrado']),
								'borde' => '',
								'align' => 'R',
								'fondo' => 0,
								'style' => '',
								'colorf' => '#ccc',
								'size' => 10
						);
					$PDF->linea[4] = array(
								'posx' => 175,
								'ancho' => 25,
								'texto' => $util->nf($valores_2['cobrado'] / $valores_2['cantidad_socios']),
								'borde' => '',
								'align' => 'R',
								'fondo' => 0,
								'style' => '',
								'colorf' => '#ccc',
								'size' => 10
						);                                        
					$PDF->Imprimir_linea();	

                                        $ACU_CANT += $valores_2['cantidad_socios'];
                                        $ACU_IMPO += $valores_2['cobrado'];

                                        $ACU_CATEGORIA_CANT += $valores_2['cantidad_socios'];
                                        $ACU_CATEGORIA_IMPO += $valores_2['cobrado'];

                                        $ACU_COB_CANT += $valores_2['cantidad_socios'];
                                        $ACU_COB_IMPO += $valores_2['cobrado'];

                                        $ACU_IMPO_PROM += $valores_2['importe_promedio'];
                                        $ACU_COB_PROM += $valores_2['cobrado_promedio'];                                       
					
			
				endforeach;
                                
                                $ACU_IMPO_PROM = $ACU_IMPO_PROM / $count_2;
                                $ACU_COB_PROM = $ACU_COB_PROM / $count_2;

                                $ACU_CATEGORIA_IMPO_PROM += $ACU_IMPO_PROM;
                                $ACU_CATEGORIA_COB_PROM += $ACU_COB_PROM;                                  
				
				$PDF->linea[0] = array(
							'posx' => 10,
							'ancho' => 90,
							'texto' => "SUBTOTAL",
							'borde' => '',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => 10
					);
				$PDF->linea[1] = array(
							'posx' => 100,
							'ancho' => 25,
							'texto' => $ACU_COB_CANT,
							'borde' => 'T',
							'align' => 'C',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => 10
					);
				$PDF->linea[2] = array(
							'posx' => 125,
							'ancho' => 25,
							'texto' => $util->nf($ACU_IMPO_PROM),
							'borde' => 'T',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => 10
					);
				$PDF->linea[3] = array(
							'posx' => 150,
							'ancho' => 25,
							'texto' => $util->nf($ACU_COB_IMPO),
							'borde' => 'T',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => 10
					);
				$PDF->linea[4] = array(
							'posx' => 175,
							'ancho' => 25,
							'texto' => $util->nf($ACU_COB_IMPO / $ACU_COB_CANT),
							'borde' => 'T',
							'align' => 'R',
							'fondo' => 0,
							'style' => 'B',
							'colorf' => '#ccc',
							'size' => 10
					);                                
				$PDF->Imprimir_linea();	
				
				$ACU_COB_CANT = $ACU_COB_IMPO = $ACU_IMPO_PROM = $ACU_COB_PROM = 0;
				
				$PDF->ln(5);
				
			endforeach;
                        
                        $ACU_CATEGORIA_IMPO_PROM = $ACU_CATEGORIA_IMPO_PROM / $count_1;
                        $ACU_CATEGORIA_COB_PROM = $ACU_CATEGORIA_COB_PROM / $count_1;

                        $ACU_IMPO_TPROM += $ACU_CATEGORIA_IMPO_PROM;
                        $ACU_COB_TPROM += $ACU_CATEGORIA_COB_PROM;                        
			
			$PDF->linea[0] = array(
						'posx' => 10,
						'ancho' => 90,
						'texto' => "TOTAL " . $util->globalDato($categoria),
						'borde' => 'TLB',
						'align' => 'R',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
				);
			$PDF->linea[1] = array(
						'posx' => 100,
						'ancho' => 25,
						'texto' => $ACU_CATEGORIA_CANT,
						'borde' => 'TB',
						'align' => 'C',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
				);
			$PDF->linea[2] = array(
						'posx' => 125,
						'ancho' => 25,
						'texto' => $util->nf($ACU_CATEGORIA_IMPO_PROM),
						'borde' => 'TB',
						'align' => 'R',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
				);
			$PDF->linea[3] = array(
						'posx' => 150,
						'ancho' => 25,
						'texto' => $util->nf($ACU_CATEGORIA_IMPO),
						'borde' => 'TB',
						'align' => 'R',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
				);
			$PDF->linea[4] = array(
						'posx' => 175,
						'ancho' => 25,
						'texto' => $util->nf($ACU_CATEGORIA_IMPO / $ACU_CATEGORIA_CANT),
						'borde' => 'TBR',
						'align' => 'R',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => 10
				);                        
			$PDF->Imprimir_linea();	
			
			$ACU_CATEGORIA_CANT = $ACU_CATEGORIA_IMPO = $ACU_CATEGORIA_IMPO_PROM = $ACU_CATEGORIA_COB_PROM = 0;			
			
			$PDF->ln(5);

		endforeach;
		
		
		$PDF->linea[0] = array(
					'posx' => 10,
					'ancho' => 90,
					'texto' => "TOTAL GENERAL " . $util->periodo($periodoAnalisis,true),
					'borde' => 'TBL',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
			);
		$PDF->linea[1] = array(
					'posx' => 100,
					'ancho' => 25,
					'texto' => $ACU_CANT,
					'borde' => 'TB',
					'align' => 'C',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
			);
		$PDF->linea[2] = array(
					'posx' => 125,
					'ancho' => 25,
					'texto' => $util->nf($ACU_IMPO_TPROM / $count_0),
					'borde' => 'TB',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
			);
		$PDF->linea[3] = array(
					'posx' => 150,
					'ancho' => 25,
					'texto' => $util->nf($ACU_IMPO),
					'borde' => 'TB',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
			);
		$PDF->linea[4] = array(
					'posx' => 175,
					'ancho' => 25,
					'texto' => $util->nf($ACU_IMPO/ $ACU_CANT),
					'borde' => 'TBR',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
			);                
		$PDF->Imprimir_linea();	

		$ACU_CANT = $ACU_IMPO = 0;	
	
		$PDF->ln(10);
	
	endforeach;


endif;


$PDF->Output("inaes_articulo_9_periodo_$periodoAnalisis.pdf");
//$PDF->Output("ListadoDeuda.pdf");
exit;
?>