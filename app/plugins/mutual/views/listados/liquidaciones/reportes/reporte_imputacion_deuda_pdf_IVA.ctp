<?php 
// debug($liquidacion);
//debug($socios);
// exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF('L');

$PDF->SetTitle("CONTROL DE ". ($procesarSobrePreImputacion == 1 ? "PRE-IMPUTACION" : "IMPUTACION") ." :: " . (isset($liquidacion['Liquidacion']['codigo_organismo']) ? $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']) : ' ** GENERAL **') . " | PERIODO: " . (isset($liquidacion['Liquidacion']['periodo']) ? $util->periodo($liquidacion['Liquidacion']['periodo']) : $util->periodo($periodo)));
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo3'] =  "LIQUIDACION DE PROVEEDORES " . ($procesarSobrePreImputacion == 1 ? "[*** PRE-IMPUTACION ***]" : "[*** IMPUTADO ***]");
$PDF->titulo['titulo2'] = "CONTROL DE ". ($procesarSobrePreImputacion == 1 ? "PRE-IMPUTACION" : "IMPUTACION") ." DE COBRANZA POR RECIBO DE SUELDO";
$PDF->titulo['titulo1'] =  (isset($liquidacion['Liquidacion']['codigo_organismo']) ? $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']) : ' ** GENERAL **') . " | PERIODO: " . (isset($liquidacion['Liquidacion']['periodo']) ? $util->periodo($liquidacion['Liquidacion']['periodo']) : $util->periodo($periodo)) ;

//$W1 = array(71,51,51,17);
$W1 = array(37,60,60,20,60,20,20);
$L1 = $PDF->armaAnchoColumnas($W1);

//$W2 = array(71,17,17,17,17,17,17,17);
$W2 = array(37,20,20,20,20,20,20,10,20,10,20,20,20,20);
$L2 = $PDF->armaAnchoColumnas($W2);

$PDF->encabezado = array();
$fontSizeHeader = 7;

$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => ' PROVEEDOR',
			'borde' => 'LT',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][1] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => 'LIQUIDACION',
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
$PDF->encabezado[0][2] = array(
			'posx' => $L1[2],
			'ancho' => $W1[2],
			'texto' => ($procesarSobrePreImputacion == 1 ? "PRE-IMPUTADO" : "IMPUTADO"),
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][3] = array(
			'posx' => $L1[3],
			'ancho' => $W1[3],
			'texto' => '',
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
$PDF->encabezado[0][4] = array(
			'posx' => $L1[4],
			'ancho' => $W1[4],
			'texto' => 'COMISION COBRANZA',
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
$PDF->encabezado[0][5] = array(
			'posx' => $L1[5],
			'ancho' => $W1[5],
			'texto' => '',
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
			'texto' => '',
			'borde' => 'TR',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
///////	
	
$PDF->encabezado[1][0] = array(
			'posx' => $L2[0],
			'ancho' => $W2[0],
			'texto' => ' PRODUCTO - CONCEPTO',
			'borde' => 'LB',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[1][1] = array(
			'posx' => $L2[1],
			'ancho' => $W2[1],
			'texto' => 'PERIODO',
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
			'texto' => 'DEUDA',
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
			'texto' => 'TOTAL',
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	

$PDF->encabezado[1][4] = array(
			'posx' => $L2[4],
			'ancho' => $W2[4],
			'texto' => 'PERIODO',
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
			'texto' => 'DEUDA',
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
			'texto' => 'TOTAL',
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[1][7] = array(
			'posx' => $L2[7],
			'ancho' => $W2[7],
			'texto' => 'SOCIOS',
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[1][8] = array(
			'posx' => $L2[8],
			'ancho' => $W2[8],
			'texto' => 'REVERSADO',
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
$PDF->encabezado[1][9] = array(
			'posx' => $L2[9],
			'ancho' => $W2[9],
			'texto' => 'ALICUOTA %',
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[1][10] = array(
			'posx' => $L2[10],
			'ancho' => $W2[10],
			'texto' => 'IMPORTE',
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[1][11] = array(
		'posx' => $L2[11],
		'ancho' => $W2[11],
		'texto' => 'I.V.A.',
		'borde' => 'B',
		'align' => 'C',
		'fondo' => 1,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $fontSizeHeader
);
$PDF->encabezado[1][12] = array(
			'posx' => $L2[12],
			'ancho' => $W2[12],
			'texto' => 'NETO PROV.',
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);					
$PDF->encabezado[1][13] = array(
			'posx' => $L2[13],
			'ancho' => $W2[13],
			'texto' => 'LIQ. NO COB.',
			'borde' => 'BR',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
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

$DECIMAL_4T = 0;
$DECIMAL_5T = 0;
$DECIMAL_6T = 0;

$DECIMAL_4 = 0;
$DECIMAL_5 = 0;
$DECIMAL_6 = 0;


$DECIMAL_7T = 0;
$DECIMAL_8T = 0;
$DECIMAL_9T = 0;

$DECIMAL_7 = 0;
$DECIMAL_8 = 0;
$DECIMAL_9 = 0;

$DECIMAL_11 = 0;
$DECIMAL_11T = 0;

$DECIMAL_13 = 0;
$DECIMAL_13T = 0;

$DECIMAL_15 = 0;
$DECIMAL_15T = 0;

$ENTERO_3 = 0;
$ENTERO_3T = 0;


$NETO_PROVEEDOR = 0;
$TNETO_PROVEEDOR = 0;

$REVERSO = 0;
$TREVERSO = 0;

$SALDO = 0;
$SALDO_T = 0;

$fontSizeBody = 7;

$primero = TRUE;
$proveedorActual = 0;

foreach($datos as $dato):

	$DECIMAL_1T += $dato['AsincronoTemporal']['decimal_1'];
	$DECIMAL_2T += $dato['AsincronoTemporal']['decimal_2'];
	$DECIMAL_3T += $dato['AsincronoTemporal']['decimal_3'];	
	
	$DECIMAL_4T += $dato['AsincronoTemporal']['decimal_4'];
	$DECIMAL_5T += $dato['AsincronoTemporal']['decimal_5'];
	$DECIMAL_6T += $dato['AsincronoTemporal']['decimal_6'];		
	
	$DECIMAL_7T += $dato['AsincronoTemporal']['decimal_7'];
	$DECIMAL_8T += $dato['AsincronoTemporal']['decimal_8'];
	$DECIMAL_9T += $dato['AsincronoTemporal']['decimal_9'];
	
	$DECIMAL_11T += $dato['AsincronoTemporal']['decimal_11'];
	$TNETO_PROVEEDOR += $dato['AsincronoTemporal']['decimal_12'];
	$DECIMAL_13T += $dato['AsincronoTemporal']['decimal_13'];
	$DECIMAL_15T += $dato['AsincronoTemporal']['decimal_15'];
    
    $ENTERO_3T += $dato['AsincronoTemporal']['entero_3'];

	$TREVERSO += $dato['AsincronoTemporal']['decimal_14'];
	
	$PDF->Reset();
	
	if($proveedorActual != $dato['AsincronoTemporal']['entero_1']){
		
		$proveedorActual = $dato['AsincronoTemporal']['entero_1'];
		
		if($primero){

			$primero = false;

		}else{

			$PDF->linea[0] = array(
						'posx' => $L2[0],
						'ancho' => $W2[0],
						'texto' => "SUB-TOTAL",
						'borde' => 'T',
						'align' => 'L',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
				
			$PDF->linea[1] = array(
						'posx' => $L2[1],
						'ancho' => $W2[1],
						'texto' => $util->nf($DECIMAL_7),
						'borde' => 'T',
						'align' => 'R',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);		
				
			$PDF->linea[2] = array(
						'posx' => $L2[2],
						'ancho' => $W2[2],
						'texto' => $util->nf($DECIMAL_8),
						'borde' => 'T',
						'align' => 'R',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);		
			$PDF->linea[3] = array(
						'posx' => $L2[3],
						'ancho' => $W2[3],
						'texto' => $util->nf($DECIMAL_9),
						'borde' => 'T',
						'align' => 'R',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);

			$PDF->linea[4] = array(
						'posx' => $L2[4],
						'ancho' => $W2[4],
						'texto' => $util->nf($DECIMAL_4),
						'borde' => 'T',
						'align' => 'R',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);		
				
			$PDF->linea[5] = array(
						'posx' => $L2[5],
						'ancho' => $W2[5],
						'texto' => $util->nf($DECIMAL_5),
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
						'texto' => $util->nf($DECIMAL_6),
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
						'texto' => $ENTERO_3,
						'borde' => 'T',
						'align' => 'C',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);            
            
			$PDF->linea[8] = array(
						'posx' => $L2[8],
						'ancho' => $W2[8],
						'texto' => $util->nf($REVERSO),
						'borde' => 'T',
						'align' => 'R',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);				
			$PDF->linea[9] = array(
						'posx' => $L2[9],
						'ancho' => $W2[9],
						'texto' => '',
						'borde' => 'T',
						'align' => 'R',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);				
			$PDF->linea[10] = array(
						'posx' => $L2[10],
						'ancho' => $W2[10],
						'texto' => $util->nf($DECIMAL_11),
						'borde' => 'T',
						'align' => 'R',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);	
			$PDF->linea[11] = array(
					'posx' => $L2[11],
					'ancho' => $W2[11],
					'texto' => $util->nf($DECIMAL_15),
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);						

			$PDF->linea[12] = array(
						'posx' => $L2[12],
						'ancho' => $W2[12],
						'texto' => $util->nf($NETO_PROVEEDOR),
						'borde' => 'T',
						'align' => 'R',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
			$PDF->linea[13] = array(
						'posx' => $L2[13],
						'ancho' => $W2[13],
						'texto' => $util->nf($DECIMAL_13),
						'borde' => 'T',
						'align' => 'R',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);								
				
			$PDF->Imprimir_linea();
			$PDF->Ln(3);

			$DECIMAL_1 = 0;
			$DECIMAL_2 = 0;
			$DECIMAL_3 = 0;
			$DECIMAL_4 = 0;
			$DECIMAL_5 = 0;
			$DECIMAL_6 = 0;
			$DECIMAL_7 = 0;
			$DECIMAL_8 = 0;
			$DECIMAL_9 = 0;
			$DECIMAL_11 = 0;
			$DECIMAL_13 = 0;
			$DECIMAL_15 = 0;
            $ENTERO_3 = 0;
			$NETO_PROVEEDOR = 0;
			$REVERSO = 0;									
			
		}
		
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => 277,
					'texto' => $dato['AsincronoTemporal']['texto_1'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#D8DBD4',
					'size' => $fontSizeBody + 1
			);
		$PDF->Imprimir_linea();			
//		$PDF->ln(3);
	}
	

	$DECIMAL_1 += $dato['AsincronoTemporal']['decimal_1'];
	$DECIMAL_2 += $dato['AsincronoTemporal']['decimal_2'];
	$DECIMAL_3 += $dato['AsincronoTemporal']['decimal_3'];
	$DECIMAL_4 += $dato['AsincronoTemporal']['decimal_4'];
	$DECIMAL_5 += $dato['AsincronoTemporal']['decimal_5'];
	$DECIMAL_6 += $dato['AsincronoTemporal']['decimal_6'];
	$DECIMAL_7 += $dato['AsincronoTemporal']['decimal_7'];
	$DECIMAL_8 += $dato['AsincronoTemporal']['decimal_8'];
	$DECIMAL_9 += $dato['AsincronoTemporal']['decimal_9'];	
	$DECIMAL_11 += $dato['AsincronoTemporal']['decimal_11'];
	$DECIMAL_13 += $dato['AsincronoTemporal']['decimal_13'];
	$DECIMAL_15 += $dato['AsincronoTemporal']['decimal_15'];
    
    $ENTERO_3 += $dato['AsincronoTemporal']['entero_3'];
    
	$NETO_PROVEEDOR += $dato['AsincronoTemporal']['decimal_12'];	
	$REVERSO += $dato['AsincronoTemporal']['decimal_14'];		

	$PDF->linea[0] = array(
				'posx' => $L2[0],
				'ancho' => $W2[0],
				'texto' => substr($dato['AsincronoTemporal']['texto_4']." - ".$dato['AsincronoTemporal']['texto_5'],0,25),
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
				'texto' => $util->nf($dato['AsincronoTemporal']['decimal_7']),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);		
		
	$PDF->linea[2] = array(
				'posx' => $L2[2],
				'ancho' => $W2[2],
				'texto' => $util->nf($dato['AsincronoTemporal']['decimal_8']),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);		
	$PDF->linea[3] = array(
				'posx' => $L2[3],
				'ancho' => $W2[3],
				'texto' => $util->nf($dato['AsincronoTemporal']['decimal_9']),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[4] = array(
				'posx' => $L2[4],
				'ancho' => $W2[4],
				'texto' => $util->nf($dato['AsincronoTemporal']['decimal_4']),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);		
		
	$PDF->linea[5] = array(
				'posx' => $L2[5],
				'ancho' => $W2[5],
				'texto' => $util->nf($dato['AsincronoTemporal']['decimal_5']),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);		
	$PDF->linea[6] = array(
				'posx' => $L2[6],
				'ancho' => $W2[6],
				'texto' => $util->nf($dato['AsincronoTemporal']['decimal_6']),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[7] = array(
				'posx' => $L2[7],
				'ancho' => $W2[7],
				'texto' => $dato['AsincronoTemporal']['entero_3'],
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[8] = array(
				'posx' => $L2[8],
				'ancho' => $W2[8],
				'texto' => $util->nf($dato['AsincronoTemporal']['decimal_14']),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);    
	$PDF->linea[9] = array(
				'posx' => $L2[9],
				'ancho' => $W2[9],
				'texto' => $util->nf($dato['AsincronoTemporal']['decimal_10']),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[10] = array(
				'posx' => $L2[10],
				'ancho' => $W2[10],
				'texto' => $util->nf($dato['AsincronoTemporal']['decimal_11']),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[11] = array(
			'posx' => $L2[11],
			'ancho' => $W2[11],
			'texto' => $util->nf($dato['AsincronoTemporal']['decimal_15']),
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);	
	$PDF->linea[12] = array(
				'posx' => $L2[12],
				'ancho' => $W2[12],
				'texto' => $util->nf($dato['AsincronoTemporal']['decimal_12']),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);						
	$PDF->linea[13] = array(
				'posx' => $L2[13],
				'ancho' => $W2[13],
				'texto' => $util->nf($dato['AsincronoTemporal']['decimal_13']),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);					
	
	$PDF->Imprimir_linea();
	
endforeach;


$PDF->linea[0] = array(
			'posx' => $L2[0],
			'ancho' => $W2[0],
			'texto' => "SUB-TOTAL",
			'borde' => 'T',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);
	
$PDF->linea[1] = array(
			'posx' => $L2[1],
			'ancho' => $W2[1],
			'texto' => $util->nf($DECIMAL_7),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);		
	
$PDF->linea[2] = array(
			'posx' => $L2[2],
			'ancho' => $W2[2],
			'texto' => $util->nf($DECIMAL_8),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);		
$PDF->linea[3] = array(
			'posx' => $L2[3],
			'ancho' => $W2[3],
			'texto' => $util->nf($DECIMAL_9),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);

$PDF->linea[4] = array(
			'posx' => $L2[4],
			'ancho' => $W2[4],
			'texto' => $util->nf($DECIMAL_4),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);		
	
$PDF->linea[5] = array(
			'posx' => $L2[5],
			'ancho' => $W2[5],
			'texto' => $util->nf($DECIMAL_5),
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
			'texto' => $util->nf($DECIMAL_6),
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
			'texto' => $ENTERO_3,
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);
$PDF->linea[8] = array(
			'posx' => $L2[8],
			'ancho' => $W2[8],
			'texto' => $util->nf($REVERSO),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);				
$PDF->linea[9] = array(
			'posx' => $L2[9],
			'ancho' => $W2[9],
			'texto' => '',
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);				
$PDF->linea[10] = array(
			'posx' => $L2[10],
			'ancho' => $W2[10],
			'texto' => $util->nf($DECIMAL_11),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);				
$PDF->linea[11] = array(
		'posx' => $L2[11],
		'ancho' => $W2[11],
		'texto' => $util->nf($DECIMAL_15),
		'borde' => 'T',
		'align' => 'R',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $fontSizeBody
);
$PDF->linea[12] = array(
			'posx' => $L2[12],
			'ancho' => $W2[12],
			'texto' => $util->nf($NETO_PROVEEDOR),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);
$PDF->linea[13] = array(
			'posx' => $L2[13],
			'ancho' => $W2[13],
			'texto' => $util->nf($DECIMAL_13),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
);					
	
$PDF->Imprimir_linea();
$PDF->Ln(3);

//////////////////
$PDF->linea[0] = array(
			'posx' => $L2[0],
			'ancho' => $W2[0],
			'texto' => "TOTAL GENERAL",
			'borde' => 'T',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);
	
$PDF->linea[1] = array(
			'posx' => $L2[1],
			'ancho' => $W2[1],
			'texto' => $util->nf($DECIMAL_7T),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);		
	
$PDF->linea[2] = array(
			'posx' => $L2[2],
			'ancho' => $W2[2],
			'texto' => $util->nf($DECIMAL_8T),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);		
$PDF->linea[3] = array(
			'posx' => $L2[3],
			'ancho' => $W2[3],
			'texto' => $util->nf($DECIMAL_9T),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);

$PDF->linea[4] = array(
			'posx' => $L2[4],
			'ancho' => $W2[4],
			'texto' => $util->nf($DECIMAL_4T),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);		
	
$PDF->linea[5] = array(
			'posx' => $L2[5],
			'ancho' => $W2[5],
			'texto' => $util->nf($DECIMAL_5T),
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
			'texto' => $util->nf($DECIMAL_6T),
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
			'texto' => $ENTERO_3T,
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);
$PDF->linea[8] = array(
			'posx' => $L2[8],
			'ancho' => $W2[8],
			'texto' => $util->nf($TREVERSO),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);	
$PDF->linea[9] = array(
			'posx' => $L2[9],
			'ancho' => $W2[9],
			'texto' => "",
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);
$PDF->linea[10] = array(
			'posx' => $L2[10],
			'ancho' => $W2[10],
			'texto' => $util->nf($DECIMAL_11T),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);		
$PDF->linea[11] = array(
		'posx' => $L2[11],
		'ancho' => $W2[11],
		'texto' => $util->nf($DECIMAL_15T),
		'borde' => 'T',
		'align' => 'R',
		'fondo' => 1,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $fontSizeBody
);
$PDF->linea[12] = array(
			'posx' => $L2[12],
			'ancho' => $W2[12],
			'texto' => $util->nf($TNETO_PROVEEDOR),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);
$PDF->linea[13] = array(
			'posx' => $L2[13],
			'ancho' => $W2[13],
			'texto' => $util->nf($DECIMAL_13T),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);					
	
$PDF->Imprimir_linea();

if(!empty($socios)){
    

    
    $PDF->titulo['titulo3'] =  "SOCIOS " . ($procesarSobrePreImputacion == 1 ? "[*** PRE-IMPUTACION ***]" : "[*** IMPUTADO ***]");
    $PDF->titulo['titulo2'] = "CARGO ADICIONAL";
    $PDF->titulo['titulo1'] = $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']) . " | PERIODO: " . $util->periodo($liquidacion['Liquidacion']['periodo']);
    
    
    $W1 = array(12,15,63,20,20,20,20,20);
    $L1 = $PDF->armaAnchoColumnas($W1);

    $PDF->encabezado = array();
    $fontSizeHeader = 7;    
    
    
    $PDF->encabezado[0][0] = array(
                'posx' => $L1[0],
                'ancho' => $W1[0],
                'texto' => 'SOCIO',
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
                'texto' => 'DOCUMENTO',
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
                'texto' => 'NOMBRE',
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
                'texto' => 'LIQUIDADO',
                'borde' => 'TB',
                'align' => 'C',
                'fondo' => 1,
                'style' => 'B',
                'colorf' => '#ccc',
                'size' => $fontSizeHeader
        ); 
    $PDF->encabezado[0][4] = array(
                'posx' => $L1[4],
                'ancho' => $W1[4],
                'texto' => ($procesarSobrePreImputacion == 1 ? "PRE-IMPUTADO" : "IMPUTADO"),
                'borde' => 'TB',
                'align' => 'C',
                'fondo' => 1,
                'style' => 'B',
                'colorf' => '#ccc',
                'size' => $fontSizeHeader
        ); 
    $PDF->encabezado[0][5] = array(
                'posx' => $L1[5],
                'ancho' => $W1[5],
                'texto' => 'NETO',
                'borde' => 'TB',
                'align' => 'C',
                'fondo' => 1,
                'style' => 'B',
                'colorf' => '#ccc',
                'size' => $fontSizeHeader
        );   
    $PDF->encabezado[0][6] = array(
                'posx' => $L1[6],
                'ancho' => $W1[6],
                'texto' => 'IVA[%]',
                'borde' => 'TB',
                'align' => 'C',
                'fondo' => 1,
                'style' => 'B',
                'colorf' => '#ccc',
                'size' => $fontSizeHeader
        ); 
    $PDF->encabezado[0][7] = array(
                'posx' => $L1[7],
                'ancho' => $W1[7],
                'texto' => 'TOTAL',
                'borde' => 'TBR',
                'align' => 'C',
                'fondo' => 1,
                'style' => 'B',
                'colorf' => '#ccc',
                'size' => $fontSizeHeader
        );    
    
    $PDF->AddPage('P');
    $PDF->Reset();   
    
    $CANT = $TOTAL_LIQUI = $TOTAL_IMPU = $TOTAL_NETO = $TOTAL_IVA = $TOTAL_TOTAL = 0;
    
    foreach($socios as $socio){
        
        $PDF->linea[0] = array(
                    'posx' => $L1[0],
                    'ancho' => $W1[0],
                    'texto' => $socio['entero_1'],
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
                    'texto' => $socio['texto_1'],
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
                    'texto' => $socio['texto_2'],
                    'borde' => '',
                    'align' => 'L',
                    'fondo' => 0,
                    'style' => '',
                    'colorf' => '#ccc',
                    'size' => $fontSizeBody
            );  
        
        $PDF->linea[3] = array(
                    'posx' => $L1[3],
                    'ancho' => $W1[3],
                    'texto' => $util->nf($socio['decimal_2']),
                    'borde' => '',
                    'align' => 'R',
                    'fondo' => 0,
                    'style' => '',
                    'colorf' => '#ccc',
                    'size' => $fontSizeBody
            );  
        $PDF->linea[4] = array(
                    'posx' => $L1[4],
                    'ancho' => $W1[4],
                    'texto' => $util->nf($socio['decimal_3']),
                    'borde' => '',
                    'align' => 'R',
                    'fondo' => 0,
                    'style' => '',
                    'colorf' => '#ccc',
                    'size' => $fontSizeBody
            ); 
        $PDF->linea[5] = array(
                    'posx' => $L1[5],
                    'ancho' => $W1[5],
                    'texto' => $util->nf($socio['decimal_4']),
                    'borde' => '',
                    'align' => 'R',
                    'fondo' => 0,
                    'style' => '',
                    'colorf' => '#ccc',
                    'size' => $fontSizeBody
            );
        $PDF->linea[6] = array(
                    'posx' => $L1[6],
                    'ancho' => $W1[6],
                    'texto' => $util->nf($socio['decimal_5']),
                    'borde' => '',
                    'align' => 'R',
                    'fondo' => 0,
                    'style' => '',
                    'colorf' => '#ccc',
                    'size' => $fontSizeBody
            );
        $PDF->linea[7] = array(
                    'posx' => $L1[7],
                    'ancho' => $W1[7],
                    'texto' => $util->nf($socio['decimal_6']),
                    'borde' => '',
                    'align' => 'R',
                    'fondo' => 0,
                    'style' => '',
                    'colorf' => '#ccc',
                    'size' => $fontSizeBody
            );         
        $PDF->Imprimir_linea();
        
        $TOTAL_LIQUI += $socio['decimal_2'];
        $TOTAL_IMPU += $socio['decimal_3'];
        $TOTAL_NETO += $socio['decimal_4'];
        $TOTAL_IVA += $socio['decimal_5']; 
        $TOTAL_TOTAL += $socio['decimal_6'];
        $CANT++;
        
        
    }
    
     $PDF->linea[0] = array(
                'posx' => $L1[0],
                'ancho' => $W1[0] + $W1[1] + $W1[2],
                'texto' => 'TOTALES [ '.$CANT.' SOCIOS ]',
                'borde' => 'T',
                'align' => 'L',
                'fondo' => 1,
                'style' => 'B',
                'colorf' => '#ccc',
                'size' => $fontSizeBody
        );    
    
    
     $PDF->linea[3] = array(
                'posx' => $L1[3],
                'ancho' => $W1[3],
                'texto' => $util->nf($TOTAL_LIQUI),
                'borde' => 'T',
                'align' => 'R',
                'fondo' => 1,
                'style' => 'B',
                'colorf' => '#ccc',
                'size' => $fontSizeBody
        );		

    $PDF->linea[4] = array(
                'posx' => $L1[4],
                'ancho' => $W1[4],
                'texto' => $util->nf($TOTAL_IMPU),
                'borde' => 'T',
                'align' => 'R',
                'fondo' => 1,
                'style' => 'B',
                'colorf' => '#ccc',
                'size' => $fontSizeBody
        );		
    $PDF->linea[5] = array(
                'posx' => $L1[5],
                'ancho' => $W1[5],
                'texto' => $util->nf($TOTAL_NETO),
                'borde' => 'T',
                'align' => 'R',
                'fondo' => 1,
                'style' => 'B',
                'colorf' => '#ccc',
                'size' => $fontSizeBody
        );

    $PDF->linea[6] = array(
                'posx' => $L1[6],
                'ancho' => $W1[6],
                'texto' => $util->nf($TOTAL_IVA),
                'borde' => 'T',
                'align' => 'R',
                'fondo' => 1,
                'style' => 'B',
                'colorf' => '#ccc',
                'size' => $fontSizeBody
        );		

    $PDF->linea[7] = array(
                'posx' => $L1[7],
                'ancho' => $W1[7],
                'texto' => $util->nf($TOTAL_TOTAL),
                'borde' => 'T',
                'align' => 'R',
                'fondo' => 1,
                'style' => 'B',
                'colorf' => '#ccc',
                'size' => $fontSizeBody
        );		    
    $PDF->Imprimir_linea();
}



$PDF->Output("control_".($procesarSobrePreImputacion == 1 ? "pre_imputado" : "imputado").".pdf");
exit;

?>