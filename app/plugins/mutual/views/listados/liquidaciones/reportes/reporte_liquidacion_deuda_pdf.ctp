<?php 

// debug($debitos);
// exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("CONTROL DE LIQUIDACION :: " . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']) . " - PERIODO " . $util->periodo($liquidacion['Liquidacion']['periodo']));
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo3'] =  "CONTROL DE LIQUIDACION";
$PDF->titulo['titulo2'] = "";
$PDF->titulo['titulo1'] = $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']) . " | PERIODO: " . $util->periodo($liquidacion['Liquidacion']['periodo']);


$W1 = array(170,20);
$L1 = $PDF->armaAnchoColumnas($W1);




$PDF->encabezado = array();
$PDF->encabezado[0] = array();	
$fontSizeHeader = 10;

$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => 'PRODUCTO - CONCEPTO / SOCIO',
			'borde' => 'LTB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);

$PDF->encabezado[0][3] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => 'LIQUIDADO',
			'borde' => 'TBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);										


$PDF->AddPage();
$PDF->Reset();

$DECIMAL_1T = 0;
$DECIMAL_2T = 0;
$DECIMAL_3T = 0;

$DECIMAL_1 = 0;
$DECIMAL_2 = 0;
$DECIMAL_3 = 0;

$fontSizeBody = 7;

$primero = TRUE;
$proveedorActual = 0;
$proveedorActualNombre = "";

foreach($datos as $dato){
	
	$DECIMAL_1T += $dato['AsincronoTemporal']['decimal_7'];
	$DECIMAL_2T += $dato['AsincronoTemporal']['decimal_8'];
	$DECIMAL_3T += $dato['AsincronoTemporal']['decimal_9'];	
	
	$PDF->Reset();
    
    
	
	if($proveedorActual != $dato['AsincronoTemporal']['entero_1']){
		
		$proveedorActual = $dato['AsincronoTemporal']['entero_1'];
		
		if($primero){

			$primero = false;
            $proveedorActualNombre = trim($dato['AsincronoTemporal']['texto_1']);

		}else{

			$PDF->linea[0] = array(
						'posx' => $L1[0],
						'ancho' => $W1[0],
						'texto' => "TOTAL *** $proveedorActualNombre ***",
						'borde' => 'T',
						'align' => 'L',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody + 2
				);
				
			$PDF->linea[1] = array(
						'posx' => $L1[1],
						'ancho' => $W1[1],
						'texto' => $util->nf($DECIMAL_3),
						'borde' => 'T',
						'align' => 'R',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody + 2
				);		
				
// 			$PDF->linea[2] = array(
// 						'posx' => $L1[2],
// 						'ancho' => $W1[2],
// 						'texto' => $util->nf($DECIMAL_2),
// 						'borde' => 'T',
// 						'align' => 'R',
// 						'fondo' => 0,
// 						'style' => 'B',
// 						'colorf' => '#ccc',
// 						'size' => $fontSizeBody
// 				);		
// 			$PDF->linea[3] = array(
// 						'posx' => $L1[3],
// 						'ancho' => $W1[3],
// 						'texto' => $util->nf($DECIMAL_3),
// 						'borde' => 'T',
// 						'align' => 'R',
// 						'fondo' => 0,
// 						'style' => 'B',
// 						'colorf' => '#ccc',
// 						'size' => $fontSizeBody
// 				);
			
			$PDF->Imprimir_linea();
			$PDF->Ln(2);

			$DECIMAL_1 = 0;
			$DECIMAL_2 = 0;
			$DECIMAL_3 = 0;			
			
            $proveedorActualNombre = trim($dato['AsincronoTemporal']['texto_1']);
            
		}
		
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => 190,
					'texto' => $dato['AsincronoTemporal']['texto_1'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody + 4
			);
		$PDF->Imprimir_linea();			
//		$PDF->ln(2);
	}
	

	$DECIMAL_1 += $dato['AsincronoTemporal']['decimal_7'];
	$DECIMAL_2 += $dato['AsincronoTemporal']['decimal_8'];
	$DECIMAL_3 += $dato['AsincronoTemporal']['decimal_9'];	
	
	if($dato['AsincronoTemporal']['clave_3'] == 'REPORTE_1')$txt = substr($dato['AsincronoTemporal']['texto_4']." - ".$dato['AsincronoTemporal']['texto_5'],0,55);
	else $txt = substr($dato['AsincronoTemporal']['texto_1']." ".$dato['AsincronoTemporal']['texto_2']." - ".$dato['AsincronoTemporal']['texto_3'],0,55);
    $txt = strtoupper($txt);
	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => $W1[0],
				'texto' => $txt,
				'borde' => ($dato['AsincronoTemporal']['clave_3'] == 'REPORTE_1' ? "" : ""),
				'align' => 'L',
				'fondo' => ($dato['AsincronoTemporal']['clave_3'] == 'REPORTE_1' ? 0 : 0),
				'style' => ($dato['AsincronoTemporal']['clave_3'] == 'REPORTE_1' ? "B" : ""),
				'colorf' => '#ccc',
				'size' => ($dato['AsincronoTemporal']['clave_3'] == 'REPORTE_1' ? $fontSizeBody + 2 : $fontSizeBody)
		);

	$PDF->linea[1] = array(
				'posx' => $L1[1],
				'ancho' => $W1[1],
				'texto' => ($dato['AsincronoTemporal']['clave_3'] == 'REPORTE_1' ? $util->nf($dato['AsincronoTemporal']['decimal_9']) : $util->nf($dato['AsincronoTemporal']['decimal_2'])),
				'borde' => ($dato['AsincronoTemporal']['clave_3'] == 'REPORTE_1' ? "" : ""),
				'align' => 'R',
				'fondo' => ($dato['AsincronoTemporal']['clave_3'] == 'REPORTE_1' ? 0 : 0),
				'style' => ($dato['AsincronoTemporal']['clave_3'] == 'REPORTE_1' ? "B" : ""),
				'colorf' => '#ccc',
				'size' => ($dato['AsincronoTemporal']['clave_3'] == 'REPORTE_1' ? $fontSizeBody + 2 : $fontSizeBody)
		);		
		
// 	$PDF->linea[2] = array(
// 				'posx' => $L1[2],
// 				'ancho' => $W1[2],
// 				'texto' => ($dato['AsincronoTemporal']['clave_3'] == 'REPORTE_1' ? $util->nf($dato['AsincronoTemporal']['decimal_2']) : ""),
// 				'borde' => '',
// 				'align' => 'R',
// 				'fondo' => ($dato['AsincronoTemporal']['clave_3'] == 'REPORTE_1' ? 1 : 0),
// 				'style' => ($dato['AsincronoTemporal']['clave_3'] == 'REPORTE_1' ? "B" : ""),
// 				'colorf' => '#ccc',
// 				'size' => ($dato['AsincronoTemporal']['clave_3'] == 'REPORTE_1' ? $fontSizeBody + 1 : $fontSizeBody)
// 		);		
// 	$PDF->linea[3] = array(
// 				'posx' => $L1[3],
// 				'ancho' => $W1[3],
// 				'texto' => ($dato['AsincronoTemporal']['clave_3'] == 'REPORTE_1' ? $util->nf($dato['AsincronoTemporal']['decimal_3']) : $util->nf($dato['AsincronoTemporal']['decimal_2'])),
// 				'borde' => '',
// 				'align' => 'R',
// 				'fondo' => ($dato['AsincronoTemporal']['clave_3'] == 'REPORTE_1' ? 1 : 0),
// 				'style' => ($dato['AsincronoTemporal']['clave_3'] == 'REPORTE_1' ? "B" : ""),
// 				'colorf' => '#ccc',
// 				'size' => ($dato['AsincronoTemporal']['clave_3'] == 'REPORTE_1' ? $fontSizeBody + 1 : $fontSizeBody)
// 		);

	
    if($dato['AsincronoTemporal']['clave_3'] == 'REPORTE_1') $PDF->ln(2);
    $PDF->Imprimir_linea();	
}


$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => "TOTAL *** $proveedorActualNombre ***",
			'borde' => 'T',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody + 2
	);
	
$PDF->linea[1] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => $util->nf($DECIMAL_3),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody + 2
	);		
	
// $PDF->linea[2] = array(
// 			'posx' => $L1[2],
// 			'ancho' => $W1[2],
// 			'texto' => $util->nf($DECIMAL_2),
// 			'borde' => 'T',
// 			'align' => 'R',
// 			'fondo' => 0,
// 			'style' => 'B',
// 			'colorf' => '#ccc',
// 			'size' => $fontSizeBody
// 	);		
// $PDF->linea[3] = array(
// 			'posx' => $L1[3],
// 			'ancho' => $W1[3],
// 			'texto' => $util->nf($DECIMAL_3),
// 			'borde' => 'T',
// 			'align' => 'R',
// 			'fondo' => 0,
// 			'style' => 'B',
// 			'colorf' => '#ccc',
// 			'size' => $fontSizeBody
// 	);

$PDF->Imprimir_linea();
$PDF->Ln(3);


$fontSizeBody = 7;
$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => "TOTAL GENERAL",
			'borde' => 'T',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody + 4
	);
	
$PDF->linea[1] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => $util->nf($DECIMAL_3T),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody + 4
	);		
	
// $PDF->linea[2] = array(
// 			'posx' => $L1[2],
// 			'ancho' => $W1[2],
// 			'texto' => $util->nf($DECIMAL_2T),
// 			'borde' => 'T',
// 			'align' => 'R',
// 			'fondo' => 1,
// 			'style' => 'B',
// 			'colorf' => '#ccc',
// 			'size' => $fontSizeBody
// 	);		
// $PDF->linea[3] = array(
// 			'posx' => $L1[3],
// 			'ancho' => $W1[3],
// 			'texto' => $util->nf($DECIMAL_3T),
// 			'borde' => 'T',
// 			'align' => 'R',
// 			'fondo' => 1,
// 			'style' => 'B',
// 			'colorf' => '#ccc',
// 			'size' => $fontSizeBody
// 	);

$PDF->Imprimir_linea();	

if(!empty($altas)):

	$PDF->AddPage();
	$PDF->Reset();

	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => $W1[0],
				'texto' => "CONTROL DE ALTAS DEL PERIODO",
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody + 4
		);
	
	$PDF->Imprimir_linea();
	$PDF->Ln(3);
	
	$PDF->linea[0] = array(
				'posx' => 10,
				'ancho' => 110,
				'texto' => "PROVEEDOR - PRODUCTO / SOCIO",
				'borde' => 'TBL',
				'align' => 'L',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[1] = array(
				'posx' => 120,
				'ancho' => 25,
				'texto' => "CANTIDAD",
				'borde' => 'TB',
				'align' => 'C',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[2] = array(
				'posx' => 145,
				'ancho' => 25,
				'texto' => "IMPORTE",
				'borde' => 'TBR',
				'align' => 'R',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);									
	
	$PDF->Imprimir_linea();	

	$ACU_CANTIDAD = 0;
	$ACU_IMPORTE = 0;

	foreach($altas as $alta):

		$ACU_CANTIDAD += $alta['entero_2'];
		$ACU_IMPORTE += $alta['decimal_2'];	
		$txtAlta = "";
		if($alta['clave_3'] == 'REPORTE_3') $txtAlta = substr($alta['texto_1']." - ".$alta['texto_3'],0,55);
		else $txtAlta = substr($alta['texto_1']." ".$alta['texto_2']." - ".$alta['texto_3'],0,55);
		
	
		$PDF->linea[0] = array(
					'posx' => 10,
					'ancho' => 110,
					'texto' => $txtAlta,
					'borde' => ($alta['clave_3'] == 'REPORTE_3' ? "T" : ""),
					'align' => 'L',
					'fondo' => 0,
					'style' => ($alta['clave_3'] == 'REPORTE_3' ? "B" : ""),
					'colorf' => '#ccc',
					'size' => ($alta['clave_3'] == 'REPORTE_3' ? $fontSizeBody + 1 : $fontSizeBody)
			);
		$PDF->linea[1] = array(
					'posx' => 120,
					'ancho' => 25,
					'texto' => ($alta['clave_3'] == 'REPORTE_3' ? $alta['entero_2'] : ""),
					'borde' => ($alta['clave_3'] == 'REPORTE_3' ? "T" : ""),
					'align' => 'C',
					'fondo' => 0,
					'style' => ($alta['clave_3'] == 'REPORTE_3' ? "B" : ""),
					'colorf' => '#ccc',
					'size' => ($alta['clave_3'] == 'REPORTE_3' ? $fontSizeBody + 1 : $fontSizeBody)
			);
		$PDF->linea[2] = array(
					'posx' => 145,
					'ancho' => 25,
					'texto' => $util->nf($alta['decimal_2'],2),
					'borde' => ($alta['clave_3'] == 'REPORTE_3' ? "T" : ""),
					'align' => 'R',
					'fondo' => 0,
					'style' => ($alta['clave_3'] == 'REPORTE_3' ? "B" : ""),
					'colorf' => '#ccc',
					'size' => ($alta['clave_3'] == 'REPORTE_3' ? $fontSizeBody + 1 : $fontSizeBody)
			);									
		
		$PDF->Imprimir_linea();	
	
	
	endforeach;
	
	$PDF->linea[0] = array(
				'posx' => 10,
				'ancho' => 110,
				'texto' => "TOTAL",
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody + 4
		);
	$PDF->linea[1] = array(
				'posx' => 120,
				'ancho' => 25,
				'texto' => $util->nf($ACU_CANTIDAD,0),
				'borde' => 'T',
				'align' => 'C',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody + 4
		);
	$PDF->linea[2] = array(
				'posx' => 145,
				'ancho' => 25,
				'texto' => $util->nf($ACU_IMPORTE,2),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody + 4
		);									
	
	$PDF->Imprimir_linea();		
	
	
endif;


if(!empty($debitos)):

	$W1 = array(170,20);
	$L1 = $PDF->armaAnchoColumnas($W1);
	
	
	
	
	$PDF->encabezado = array();
	$PDF->encabezado[0] = array();
	$fontSizeHeader = 10;
	
	$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => 'SOCIO',
			'borde' => 'LTB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
	$PDF->encabezado[0][3] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => 'IMPORTE',
			'borde' => 'TBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);

	$PDF->AddPage();
	$PDF->Reset();
	
	$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => "LISTADO DE ORDENES DE DEBITO EMITIDAS",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody + 4
	);
	
	$PDF->Imprimir_linea();
	$PDF->Ln(3);
	$TOTAL = 0;
	$SUBTOTAL = 0;
	
	$primero = true;
	$socioActual = null;
	
	foreach($debitos as $debito):
	
	
		if($socioActual != $debito['clave_2']){
			$socioActual = $debito['clave_2'];
			if($primero){
				
				$primero = false;
				
			}else{
				
				$PDF->linea[0] = array(
						'posx' => $L1[0],
						'ancho' => $W1[0],
						'texto' => "",
						'borde' => 'T',
						'align' => 'L',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
				
				$PDF->linea[1] = array(
						'posx' => $L1[1],
						'ancho' => $W1[1],
						'texto' => $util->nf($SUBTOTAL),
						'borde' => 'T',
						'align' => 'R',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody + 1
				);
				$PDF->Imprimir_linea();
				$SUBTOTAL = 0;
// 				$PDF->ln(2);
			}
			
		}
	
		$txt = substr($debito['texto_1']." ".$debito['texto_2']." - ".$debito['texto_3'] ." | CBU " . $debito['texto_6'],0,70);
	
		$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => $W1[0],
				'texto' => $txt,
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
		$PDF->linea[1] = array(
				'posx' => $L1[1],
				'ancho' => $W1[1],
				'texto' => $util->nf($debito['decimal_1'],2),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);		
		
		$PDF->Imprimir_linea();	
		
		$TOTAL += $debito['decimal_1'];
		$SUBTOTAL += $debito['decimal_1'];
	
	endforeach;
	
	$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => "",
			'borde' => 'T',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);
	
	$PDF->linea[1] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => $util->nf($SUBTOTAL),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody + 1
	);
	$PDF->Imprimir_linea();
	$SUBTOTAL = 0;	
	
	$fontSizeBody = 7;
	$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => "TOTAL GENERAL",
			'borde' => 'T',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody + 4
	);
	
	$PDF->linea[1] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => $util->nf($TOTAL),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody + 4
	);	
	$PDF->Imprimir_linea();

endif;


$PDF->Output("control_liquidacion.pdf");
exit;


?>
