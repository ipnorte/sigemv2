<?php 
//debug($datos);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF('L');

$PDF->SetTitle("Ordenes de Descuentos");
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo3'] = 'LISTADO DE ORDENES DE DESCUENTO';
$PDF->titulo['titulo2'] = "EMITIDAS ENTRE EL " . $util->armaFecha($fecha_desde) ." Y EL ". $util->armaFecha($fecha_hasta) ." ";
$PDF->titulo['titulo1'] = "";

//ORDEN | TIPO / NUMERO  | BENEFICIARIO
$W1 = array(10,15,20,50,60,25,10,15,20,10,20,20,2);
$L1 = $PDF->armaAnchoColumnas($W1);


$PDF->encabezado = array();
$PDF->encabezado[0] = array();	
$fontSizeHeader = 7;
//imprimo la primera linea del encabezado
$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => 'ORDEN',
			'borde' => 'LTB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
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
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
$PDF->encabezado[0][2] = array(
			'posx' => $L1[2],
			'ancho' => $W1[2],
			'texto' => 'TIPO / NUMERO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[0][3] = array(
			'posx' => $L1[3],
			'ancho' => $W1[3],
			'texto' => 'BENEFICIARIO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][4] = array(
			'posx' => $L1[4],
			'ancho' => $W1[4],
			'texto' => 'PROVEEDOR / PRODUCTO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);

$PDF->encabezado[0][5] = array(
			'posx' => $L1[5],
			'ancho' => $W1[5],
			'texto' => 'ORGANISMO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][6] = array(
			'posx' => $L1[6],
			'ancho' => $W1[6],
			'texto' => 'INICIA',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][7] = array(
			'posx' => $L1[7],
			'ancho' => $W1[7],
			'texto' => '1er VTO.',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][8] = array(
			'posx' => $L1[8],
			'ancho' => $W1[8],
			'texto' => 'TOTAL',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][9] = array(
			'posx' => $L1[9],
			'ancho' => $W1[9],
			'texto' => 'CUOTAS',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][10] = array(
			'posx' => $L1[10],
			'ancho' => $W1[10],
			'texto' => 'IMPORTE',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][11] = array(
			'posx' => $L1[11],
			'ancho' => $W1[11],
			'texto' => 'SALDO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][12] = array(
			'posx' => $L1[12],
			'ancho' => $W1[12],
			'texto' => 'P',
			'borde' => 'TBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);								
	
$PDF->AddPage();
$PDF->Reset();
	
$fontSizeBody = 6;

$tipoActual = "";

$primero = true;
$ACU_TOTAL = $ACU_IMPORTE = $ACU_SALDO = 0;

foreach($datos as $dato){
	$PDF->Reset();
	
	if($tipoActual != trim($dato['AsincronoTemporal']['texto_1'])){
		
		$tipoActual = trim($dato['AsincronoTemporal']['texto_1']);
		
		if($primero){
			$primero = false;
		}else{
			$PDF->linea[6] = array(
						'posx' => $L1[6],
						'ancho' => $W1[6],
						'texto' => "TOTAL",
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
			$PDF->linea[8] = array(
						'posx' => $L1[8],
						'ancho' => $W1[8],
						'texto' => $util->nf($ACU_TOTAL),
						'borde' => 'T',
						'align' => 'R',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody + 1
				);
			$PDF->linea[9] = array(
						'posx' => $L1[9],
						'ancho' => $W1[9],
						'texto' => '',
						'borde' => 'T',
						'align' => 'R',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody + 1
				);
			$PDF->linea[10] = array(
						'posx' => $L1[10],
						'ancho' => $W1[10],
						'texto' => $util->nf($ACU_IMPORTE),
						'borde' => 'T',
						'align' => 'R',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody + 1
				);
			$PDF->linea[11] = array(
						'posx' => $L1[11],
						'ancho' => $W1[11],
						'texto' => $util->nf($ACU_SALDO),
						'borde' => 'T',
						'align' => 'R',
						'fondo' => 0,
						'style' => 'B',
						'colorf' => '#ccc',
						'size' => $fontSizeBody + 1
				);                        
			$PDF->Imprimir_linea();	
			$ACU_TOTAL = $ACU_IMPORTE = $ACU_SALDO = 0;
			$PDF->AddPage();		
		}
		
		$PDF->ln(3);
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => $W1[0],
					'texto' => $dato['AsincronoTemporal']['texto_12'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody + 3
			);
		$PDF->Imprimir_linea();
						
	}
	
	$ACU_TOTAL += $dato['AsincronoTemporal']['decimal_1'];
	$ACU_IMPORTE += $dato['AsincronoTemporal']['decimal_2'];
        $ACU_SALDO += $dato['AsincronoTemporal']['decimal_5'];
	
	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => $W1[0],
				'texto' => $dato['AsincronoTemporal']['texto_2'],
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
				'texto' => $util->armaFecha($dato['AsincronoTemporal']['texto_11']),
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);		
	$PDF->linea[2] = array(
				'posx' => $L1[2],
				'ancho' => $W1[2],
				'texto' => $dato['AsincronoTemporal']['texto_3'],
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
				'texto' => substr($dato['AsincronoTemporal']['texto_4'],0,45),
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
				'texto' => $dato['AsincronoTemporal']['texto_5'],
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
				'texto' => $dato['AsincronoTemporal']['texto_10'],
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[6] = array(
				'posx' => $L1[6],
				'ancho' => $W1[6],
				'texto' => $dato['AsincronoTemporal']['texto_6'],
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[7] = array(
				'posx' => $L1[7],
				'ancho' => $W1[7],
				'texto' => $util->armaFecha($dato['AsincronoTemporal']['texto_7']),
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[8] = array(
				'posx' => $L1[8],
				'ancho' => $W1[8],
				'texto' => $util->nf($dato['AsincronoTemporal']['decimal_1']),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[9] = array(
				'posx' => $L1[9],
				'ancho' => $W1[9],
				'texto' => $util->nf($dato['AsincronoTemporal']['decimal_3'],0),
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[10] = array(
				'posx' => $L1[10],
				'ancho' => $W1[10],
				'texto' => $util->nf($dato['AsincronoTemporal']['decimal_2']),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[11] = array(
				'posx' => $L1[11],
				'ancho' => $W1[11],
				'texto' => $util->nf($dato['AsincronoTemporal']['decimal_5']),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);        
	$PDF->linea[12] = array(
				'posx' => $L1[12],
				'ancho' => $W1[12],
				'texto' => (trim($dato['AsincronoTemporal']['texto_9']) == '1' ? '(*)':''),
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);																			
	$PDF->Imprimir_linea();			
}	

$PDF->linea[6] = array(
			'posx' => $L1[6],
			'ancho' => $W1[6],
			'texto' => "TOTALES",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
	);
$PDF->linea[8] = array(
			'posx' => $L1[8],
			'ancho' => $W1[8],
			'texto' => $util->nf($ACU_TOTAL),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody + 1
	);
$PDF->linea[9] = array(
			'posx' => $L1[9],
			'ancho' => $W1[9],
			'texto' => '',
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody + 1
	);
$PDF->linea[10] = array(
			'posx' => $L1[10],
			'ancho' => $W1[10],
			'texto' => $util->nf($ACU_IMPORTE),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody + 1
	);	
$PDF->linea[11] = array(
			'posx' => $L1[11],
			'ancho' => $W1[11],
			'texto' => $util->nf($ACU_SALDO),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody + 1
	);
$PDF->Imprimir_linea();	
	
//debug($datos);
$PDF->Output("ordenes_descuento_.pdf");
?>