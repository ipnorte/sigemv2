<?php 

    App::import('Vendor','libro_pdf');
    App::import('Model', 'contabilidad.AsientoRenglon');

    $oAsientoRenglon = new AsientoRenglon();


    $PDF = new LibroPDF();

    $PDF->lEncabezado = ($encabezado === '0' ? 0 : 1);
    
    $PDF->SetTitle("LIBRO CAJA");
    $PDF->titulo['titulo1'] = "";
    $PDF->titulo['titulo3'] = "LIBRO CAJA";

    $PDF->SetFontSizeConf(9);

    $PDF->Open();

    // $W0 = array(29, 70, 69, 39, 34, 34);
    $W0 = array(140, 25, 25);
    $L0 = $PDF->armaAnchoColumnas($W0);

    $W1 = array(47.5, 47.5, 47.5, 47.5);
    $L1 = $PDF->armaAnchoColumnas($W1);

    $PDF->encabezado = array();
    $fontSizeHeader = 9;
    $fontSizeBody = 9;


    $PDF->encabezado[0][0] = array(
                            'posx' => $L0[0],
                            'ancho' => $W0[0],
                            'texto' => 'C O N C E P T O',
                            'borde' => 'LTB',
                            'align' => 'C',
                            'fondo' => 1,
                            'style' => 'B',
                            'colorf' => '#ccc',
                            'size' => $fontSizeHeader
    );	
	
    $PDF->encabezado[0][1] = array(
                            'posx' => $L0[1],
                            'ancho' => $W0[1],
                            'texto' => 'I M P O R T E',
                            'borde' => 'TB',
                            'align' => 'R',
                            'fondo' => 1,
                            'style' => 'B',
                            'colorf' => '#ccc',
                            'size' => $fontSizeHeader
    );
	
    $PDF->encabezado[0][2] = array(
                            'posx' => $L0[2],
                            'ancho' => $W0[2],
                            'texto' => 'T O T A L',
                            'borde' => 'TB',
                            'align' => 'R',
                            'fondo' => 1,
                            'style' => 'B',
                            'colorf' => '#ccc',
                            'size' => $fontSizeHeader
    );	
	///////	

	
    $TOTAL_INGRESO = $TOTAL_EGRESO = 0;
    $SALDO_INICIAL = $SALDO_FINAL = 0;
    
    $CuentaId = $ejercicio['co_plan_cuenta_id'];

    // AGRUPO LA CANTIDAD DE DIAS QUE TUVO LA CAJA EN EL EJERCICIO.
    $sqlFechas = "SELECT Asiento.* FROM co_asientos Asiento
                    WHERE Asiento.co_ejercicio_id = '$ejercicio_id' AND Asiento.tipo = 2 AND Asiento.id IN(
                    SELECT co_asiento_id FROM co_asiento_renglones WHERE co_plan_cuenta_id = '$CuentaId')
                    GROUP BY Asiento.fecha
                    ORDER BY fecha";
                
    $cajaDia = $oAsientoRenglon->query($sqlFechas);

    // TRAIGO EL SALDO AL INICIO DEL EJERCICIO.
    $sqlSaldoInicial = "SELECT	SaldoInicial.*
                        FROM co_asiento_renglones SaldoInicial
                        WHERE co_asiento_id IN (
                        SELECT id FROM co_asientos Asiento WHERE co_ejercicio_id = '$ejercicio_id' AND tipo = 1) AND co_plan_cuenta_id = '$CuentaId'";
    $SaldoInicio = $oAsientoRenglon->query($sqlSaldoInicial);
    
    $SALDO_INICIAL = $SaldoInicio[0]['SaldoInicial']['debe'] - $SaldoInicio[0]['SaldoInicial']['haber'];

    // RECORRO LOS DIAS CON LOS MOVIMIENTOS DE CAJA.
    foreach ($cajaDia as $renglones):
        $PDF->titulo['titulo3'] = "LIBRO CAJA DEL DIA: " . $util->armaFecha($renglones['Asiento']['fecha']);

        $PDF->AddPage();
	$PDF->Reset();

        $PDF->linea[0] = array(
				'posx' => $L0[0],
				'ancho' => $W0[0],
				'texto' => 'SALDO INICIAL',
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
	);
	
	$PDF->linea[1] = array(
				'posx' => $L0[1],
				'ancho' => $W0[1],
				'texto' => '',
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
	);
	
	$PDF->linea[2] = array(
				'posx' => $L0[2],
				'ancho' => $W0[2],
				'texto' => number_format($SALDO_INICIAL,2),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
	);
	
	$PDF->Imprimir_linea();
		
    
        // TRAIGO LOS INGRESOS DE LA FECHA EN CUESTION.
        $dFecha = $renglones['Asiento']['fecha'];
        $sqlDiaDebe = "SELECT Asiento.nro_asiento, Asiento.referencia, AsientoRenglon.*
                        FROM co_asiento_renglones AsientoRenglon, co_asientos Asiento
                        WHERE AsientoRenglon.fecha = '$dFecha' AND AsientoRenglon.co_plan_cuenta_id = '$CuentaId' AND AsientoRenglon.debe > 0 AND Asiento.id = AsientoRenglon.co_asiento_id";
        $cajaDiaDebe = $oAsientoRenglon->query($sqlDiaDebe);
        
        $TOTAL_INGRESO = $TOTAL_EGRESO = 0;
        foreach ($cajaDiaDebe as $ingreso) :
            $PDF->linea[0] = array(
                                    'posx' => $L0[0],
                                    'ancho' => $W0[0],
                                    'texto' => (empty($ingreso['AsientoRenglon']['referencia']) ? $ingreso['Asiento']['referencia'] : $ingreso['AsientoRenglon']['referencia']),
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#ccc',
                                    'size' => $fontSizeBody
            );

            $PDF->linea[1] = array(
                                    'posx' => $L0[1],
                                    'ancho' => $W0[1],
                                    'texto' => number_format($ingreso['AsientoRenglon']['debe'],2),
                                    'borde' => '',
                                    'align' => 'R',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#ccc',
                                    'size' => $fontSizeBody
            );

            $PDF->Imprimir_linea();
            
            $TOTAL_INGRESO += $ingreso['AsientoRenglon']['debe'];
        endforeach;
    
        $PDF->linea[0] = array(
                                'posx' => $L0[0],
                                'ancho' => $W0[0],
                                'texto' => 'TOTAL INGRESO: ',
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 1,
                                'style' => 'B',
                                'colorf' => '#ccc',
                                'size' => 10
        );

        $PDF->linea[1] = array(
                                'posx' => $L0[1],
                                'ancho' => $W0[1],
                                'texto' => '',
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 1,
                                'style' => 'B',
                                'colorf' => '#ccc',
                                'size' => 10
        );

        $PDF->linea[2] = array(
                                'posx' => $L0[2],
                                'ancho' => $W0[2],
                                'texto' => number_format($TOTAL_INGRESO,2),
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 1,
                                'style' => 'B',
                                'colorf' => '#ccc',
                                'size' => 10
        );

        $PDF->Imprimir_linea();

        // TRAIGO LOS EGRESO DE LA FECHA EN CUESTION.
        $sqlDiaHaber = "SELECT Asiento.nro_asiento, Asiento.referencia, AsientoRenglon.*
                        FROM co_asiento_renglones AsientoRenglon, co_asientos Asiento
                        WHERE AsientoRenglon.fecha = '$dFecha' AND AsientoRenglon.co_plan_cuenta_id = '$CuentaId' AND AsientoRenglon.haber > 0 AND Asiento.id = AsientoRenglon.co_asiento_id";
        $cajaDiaHaber = $oAsientoRenglon->query($sqlDiaHaber);

        foreach ($cajaDiaHaber as $egreso) :
            $PDF->linea[0] = array(
                                    'posx' => $L0[0],
                                    'ancho' => $W0[0],
                                    'texto' => (empty($egreso['AsientoRenglon']['referencia']) ? $egreso['Asiento']['referencia'] : $egreso['AsientoRenglon']['referencia']),
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#ccc',
                                    'size' => $fontSizeBody
            );

            $PDF->linea[1] = array(
                                    'posx' => $L0[1],
                                    'ancho' => $W0[1],
                                    'texto' => number_format($egreso['AsientoRenglon']['haber'],2),
                                    'borde' => '',
                                    'align' => 'R',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#ccc',
                                    'size' => $fontSizeBody
            );

            $PDF->Imprimir_linea();
            
            $TOTAL_EGRESO += $egreso['AsientoRenglon']['haber'];
        endforeach;
    
        $PDF->linea[0] = array(
                                'posx' => $L0[0],
                                'ancho' => $W0[0],
                                'texto' => 'TOTAL EGRESO: ',
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 1,
                                'style' => 'B',
                                'colorf' => '#ccc',
                                'size' => 10
        );

        $PDF->linea[1] = array(
                                'posx' => $L0[1],
                                'ancho' => $W0[1],
                                'texto' => '',
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 1,
                                'style' => 'B',
                                'colorf' => '#ccc',
                                'size' => 10
        );

        $PDF->linea[2] = array(
                                'posx' => $L0[2],
                                'ancho' => $W0[2],
                                'texto' => number_format($TOTAL_EGRESO,2),
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 1,
                                'style' => 'B',
                                'colorf' => '#ccc',
                                'size' => 10
        );

        $PDF->Imprimir_linea();
        
        $PDF->Ln();
        
        
        // IMPRIMO RESUMEN FINAL
	$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => 'SALDO AL INICIO',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 12
	);
						
	$PDF->linea[1] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => 'I N G R E S O',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 12
	);
						
	$PDF->linea[2] = array(
			'posx' => $L1[2],
			'ancho' => $W1[2],
			'texto' => 'E G R E S O',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 12
	);
				
	$PDF->linea[3] = array(
			'posx' => $L1[3],
			'ancho' => $W1[3],
			'texto' => 'SALDO AL ' . date('d/m/Y',strtotime($dFecha)),
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 12
	);
	
	$PDF->Imprimir_linea();
				

	$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => number_format($SALDO_INICIAL,2),
			'borde' => 'LTBR',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 12
	);
						
	$PDF->linea[1] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => number_format($TOTAL_INGRESO,2),
			'borde' => 'LTBR',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 12
	);
						
	$PDF->linea[2] = array(
			'posx' => $L1[2],
			'ancho' => $W1[2],
			'texto' => number_format($TOTAL_EGRESO,2),
			'borde' => 'LTBR',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 12
	);
				
	$PDF->linea[3] = array(
			'posx' => $L1[3],
			'ancho' => $W1[3],
			'texto' => number_format($SALDO_INICIAL + $TOTAL_INGRESO - $TOTAL_EGRESO,2),
			'borde' => 'LTBR',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 12
	);
	
	$PDF->Imprimir_linea();

        $SALDO_INICIAL = $SALDO_INICIAL + $TOTAL_INGRESO - $TOTAL_EGRESO;
                
    //    break;
    endforeach;

    $PDF->Output("libro_subdiario.pdf");
    exit;

?>