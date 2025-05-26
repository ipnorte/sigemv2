<?php 

//debug($datos);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("LISTADO DE DEUDA");
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo3'] = "LISTADO DE DEUDA".(!empty($tipo_producto) ? " - ".$util->globalDato($tipo_producto)."" : "");
$PDF->titulo['titulo2'] = (!empty($codigo_organismo) ? "ORGANISMO: ".$util->globalDato($codigo_organismo)." - " : "") . " PERIODO CORTE: " .$util->periodo($periodo_corte,true,'/');
$PDF->titulo['titulo1'] = (!empty($proveedor) ? "PROVEEDOR: ".$proveedor : "");



// TIPO_DOCUMENTO | APENOM | TIPO_NRO | REF.PROV. | PROVEEDOR_PRODUCTO | CUOTA | PERIODO | LIQUIDADO | IMPUTADO | SALDO
// 277
$W1 = array(15,75,20,20,20,20,20);
$L1 = $PDF->armaAnchoColumnas($W1);


$PDF->encabezado = array();
$PDF->encabezado[0] = array();	
$fontSizeHeader = 7;
//imprimo la primera linea del encabezado
$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => 'DOCUMENTO',
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
			'texto' => 'SOCIO',
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
			'texto' => 'LIQUIDADO',
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
			'texto' => 'COBRADO',
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
			'texto' => 'SALDO CONC.',
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
			'texto' => 'PEND.ACR.',
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
			'texto' => 'SALDO',
			'borde' => 'TRB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);

$DECIMAL_1 = $DECIMAL_2 = $DECIMAL_3 = $DECIMAL_4 = $DECIMAL_5 = 0;

if(!empty($datos)):

	$PDF->AddPage();
	$PDF->Reset();

	$fontSizeBody = 8;
	
	foreach($datos as $dato):
	
		$DECIMAL_1 += $dato['decimal_1'];
		$DECIMAL_2 += $dato['decimal_2'];
		$DECIMAL_3 += $dato['decimal_3'];
        $DECIMAL_4 += $dato['decimal_4'];
        $DECIMAL_5 += $dato['decimal_5'];
			
		//imprimo los datos del socio
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => $W1[0],
					'texto' => $dato['texto_1'],
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
					'texto' => $dato['texto_2'],
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
					'texto' => $util->nf($dato['decimal_1']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[3] = array(
					'posx' => $L1[3],
					'ancho' => $W1[3],
					'texto' => $util->nf($dato['decimal_2']),
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
					'texto' => $util->nf($dato['decimal_3']),
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
					'texto' => $util->nf($dato['decimal_4']),
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
					'texto' => $util->nf($dato['decimal_5']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);         
																		
		
		$PDF->Imprimir_linea();		
	
	endforeach;
	#IMPRIMO EL TOTAL GENERAL
	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => $W1[0] + $W1[1],
				'texto' => "TOTAL GENERAL",
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);			

	$PDF->linea[2] = array(
				'posx' => $L1[2],
				'ancho' => $W1[2],
				'texto' => $util->nf($DECIMAL_1),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);
	$PDF->linea[3] = array(
				'posx' => $L1[3],
				'ancho' => $W1[3],
				'texto' => $util->nf($DECIMAL_2),
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
				'texto' => $util->nf($DECIMAL_3),
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
				'texto' => $util->nf($DECIMAL_4),
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
				'texto' => $util->nf($DECIMAL_5),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
		);    
	$PDF->Imprimir_linea();		
	

endif;

$PDF->Output("ListadoDeuda.pdf");
exit;

?>