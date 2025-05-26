<?php 

//debug($cancelaciones);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF('L');

$PDF->SetTitle("DETALLE DE CANCELACIONES");
$PDF->titulo['titulo2'] = $proveedores['Proveedor']['razon_social'];
$PDF->titulo['titulo1'] = "PERIODO DESDE EL " . $fecha_desde . " AL " . $fecha_hasta;
$PDF->titulo['titulo3'] = "DETALLE DE CANCELACIONES";

$PDF->Open();

//277
$W1 = array(82,35,70,90);
$L1 = $PDF->armaAnchoColumnas($W1);

$W2 = array(10,17,55,15,20,15,20,35,15,20,20,35);
$L2 = $PDF->armaAnchoColumnas($W2);


$PDF->encabezado = array();
$fontSizeHeader = 6;
$fontSizeBody = 7;

$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => '',
			'borde' => 'TL',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][1] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => 'OPERACION CANCELADA',
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
			'texto' => 'OPERACION GENERADA',
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
			'texto' => 'COBRO EMITIDO',
			'borde' => 'TR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
// segundo renglon del encabezado
$PDF->encabezado[1][0] = array(
			'posx' => $L2[0],
			'ancho' => $W2[0],
			'texto' => '#',
			'borde' => 'BL',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
$PDF->encabezado[1][1] = array(
			'posx' => $L2[1],
			'ancho' => $W2[1],
			'texto' => 'VTO',
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);		
$PDF->encabezado[1][2] = array(
			'posx' => $L2[2],
			'ancho' => $W2[2],
			'texto' => 'SOCIO',
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);

$PDF->encabezado[1][3] = array(
			'posx' => $L2[3],
			'ancho' => $W2[3],
			'texto' => 'ORDEN #',
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
			'texto' => 'TIPO/NRO',
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
$PDF->encabezado[1][5] = array(
			'posx' => $L2[5],
			'ancho' => $W2[5],
			'texto' => 'ORDEN #',
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[1][6] = array(
			'posx' => $L2[6],
			'ancho' => $W2[6],
			'texto' => 'TIPO/NRO',
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
			'texto' => 'PROVEEDOR',
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
			'texto' => '#',
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
			'texto' => 'FECHA',
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
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
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[1][11] = array(
			'posx' => $L2[11],
			'ancho' => $W2[11],
			'texto' => 'ORIGEN FONDO',
			'borde' => 'BR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	

$PDF->AddPage();
$PDF->Reset();	
	
if(!empty($cancelaciones['recibidas'])):

		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => $W1[0],
					'texto' => "DETALLE DE CANCELACIONES RECIBIDAS",
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => 10
		);
		$PDF->Imprimir_linea();
		$PDF->ln(1);
		
		$ACUM = 0;
		
		foreach ($cancelaciones['recibidas'] as $cancelacion):
		
			$PDF->linea[0] = array(
						'posx' => $L2[0],
						'ancho' => $W2[0],
						'texto' => $cancelacion['cancelacion_id'],
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
						'texto' => $util->armaFecha($cancelacion['cancelacion_vto']),
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
			);
			$PDF->linea[2] = array(
						'posx' => $L2[2],
						'ancho' => $W2[2],
						'texto' => substr($cancelacion['socio'],0,35),
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
			);
			$PDF->linea[3] = array(
						'posx' => $L2[3],
						'ancho' => $W2[3],
						'texto' => $cancelacion['cancela_orden_dto'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
			);
			$PDF->linea[4] = array(
						'posx' => $L2[4],
						'ancho' => $W2[4],
						'texto' => $cancelacion['cancela_expediente'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
			);
			$PDF->linea[5] = array(
						'posx' => $L2[5],
						'ancho' => $W2[5],
						'texto' => (!empty($cancelacion['nueva_orden_dto']) ? $cancelacion['nueva_orden_dto'] : "---"),
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
			);
			$PDF->linea[6] = array(
						'posx' => $L2[6],
						'ancho' => $W2[6],
						'texto' => (!empty($cancelacion['nueva_orden_dto']) ? $cancelacion['nuevo_expediente'] : "---"),
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
			);
			$PDF->linea[7] = array(
						'posx' => $L2[7],
						'ancho' => $W2[7],
						'texto' => (!empty($cancelacion['nueva_orden_dto']) ? substr($cancelacion['nuevo_expediente_proveedor'],0,25): "---"),
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
			);
			$PDF->linea[8] = array(
						'posx' => $L2[8],
						'ancho' => $W2[8],
						'texto' => $cancelacion['socio_orden_cobro_id'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
			);
			$PDF->linea[9] = array(
						'posx' => $L2[9],
						'ancho' => $W2[9],
						'texto' => $util->armaFecha($cancelacion['socio_orden_cobro_fecha']),
						'borde' => '',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
			);
			$PDF->linea[10] = array(
						'posx' => $L2[10],
						'ancho' => $W2[10],
						'texto' => $util->nf($cancelacion['socio_orden_cobro_importe']),
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
						'texto' => substr($cancelacion['origen_fondo'],0,25),
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
			);																																	
			$PDF->Imprimir_linea();	

			$ACUM += $cancelacion['socio_orden_cobro_importe'];
		
		endforeach;
		
		$PDF->linea[10] = array(
					'posx' => $L2[10],
					'ancho' => $W2[10],
					'texto' => $util->nf($ACUM),
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);		
		$PDF->Imprimir_linea();	
endif;
	

if(!empty($cancelaciones['realizadas'])):


	
	$PDF->encabezado[0][1] = array(
				'posx' => $L1[1],
				'ancho' => $W1[1],
				'texto' => 'OPERACION GENERADA',
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
				'texto' => 'OPERACION CANCELADA',
				'borde' => 'T',
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
				'texto' => "DETALLE DE CANCELACIONES EFECTUADAS",
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 10
	);
	$PDF->Imprimir_linea();
	$PDF->ln(1);
	
	$ACUM = 0;	
	foreach ($cancelaciones['recibidas'] as $cancelacion):
	
		$PDF->linea[0] = array(
					'posx' => $L2[0],
					'ancho' => $W2[0],
					'texto' => $cancelacion['cancelacion_id'],
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
					'texto' => $util->armaFecha($cancelacion['cancelacion_vto']),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		$PDF->linea[2] = array(
					'posx' => $L2[2],
					'ancho' => $W2[2],
					'texto' => substr($cancelacion['socio'],0,35),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		$PDF->linea[3] = array(
					'posx' => $L2[3],
					'ancho' => $W2[3],
					'texto' => (!empty($cancelacion['nueva_orden_dto']) ? $cancelacion['nueva_orden_dto'] : "---"),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		$PDF->linea[4] = array(
					'posx' => $L2[4],
					'ancho' => $W2[4],
					'texto' => (!empty($cancelacion['nueva_orden_dto']) ? $cancelacion['nuevo_expediente'] : "---"),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		$PDF->linea[5] = array(
					'posx' => $L2[5],
					'ancho' => $W2[5],
					'texto' => (!empty($cancelacion['cancela_orden_dto']) ? $cancelacion['cancela_orden_dto'] : "---"),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		$PDF->linea[6] = array(
					'posx' => $L2[6],
					'ancho' => $W2[6],
					'texto' => (!empty($cancelacion['cancela_orden_dto']) ? $cancelacion['cancela_expediente'] : "---"),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		$PDF->linea[7] = array(
					'posx' => $L2[7],
					'ancho' => $W2[7],
					'texto' => (!empty($cancelacion['cancela_orden_dto']) ? substr($cancelacion['cancela_comercio'],0,25): "---"),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		$PDF->linea[8] = array(
					'posx' => $L2[8],
					'ancho' => $W2[8],
					'texto' => $cancelacion['socio_orden_cobro_id'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		$PDF->linea[9] = array(
					'posx' => $L2[9],
					'ancho' => $W2[9],
					'texto' => $util->armaFecha($cancelacion['socio_orden_cobro_fecha']),
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		$PDF->linea[10] = array(
					'posx' => $L2[10],
					'ancho' => $W2[10],
					'texto' => $util->nf($cancelacion['socio_orden_cobro_importe']),
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
					'texto' => substr($cancelacion['origen_fondo'],0,25),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);																																	
		$PDF->Imprimir_linea();	

		$ACUM += $cancelacion['socio_orden_cobro_importe'];
	
	endforeach;
	
	$PDF->linea[10] = array(
				'posx' => $L2[10],
				'ancho' => $W2[10],
				'texto' => $util->nf($ACUM),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
	);		
	$PDF->Imprimir_linea();	
	
endif;
	
$PDF->Output("cancelaciones.pdf");
exit;

?>