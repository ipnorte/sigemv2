<?php 

//debug($cancelacion);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("ORDEN DE CANCELACION #".$cancelacion['CancelacionOrden']['id']);
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->textoHeader = "ORDEN DE CANCELACION #" . $cancelacion['CancelacionOrden']['id'];
$PDF->titulo['titulo3'] = $cancelacion['CancelacionOrden']['beneficiario'];
$PDF->titulo['titulo2'] = $cancelacion['CancelacionOrden']['forma_cancelacion_desc'] .' ('.$cancelacion['CancelacionOrden']['tipo_cancelacion_desc'].') ' . ($cancelacion['CancelacionOrden']['estado'] == 'P' ? ' *** PROCESADA ***' : '*** EMITIDA ***');
$PDF->titulo['titulo1'] = 'VTO: '.$util->armaFecha($cancelacion['CancelacionOrden']['fecha_vto']) . ' - IMPORTE: '. number_format($cancelacion['CancelacionOrden']['importe_proveedor'],2);


$PDF->AddPage();
$PDF->reset();
$fontSize = 8;
$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 20,
			'texto' => "ORDEN DTO:",
			'borde' => 'TL',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $fontSize
	);
$PDF->linea[1] = array(
			'posx' => 30,
			'ancho' => 40,
			'texto' => $cancelacion['CancelacionOrden']['orden_descuento_id'] .' '.$cancelacion['CancelacionOrden']['tipo_nro_odto'],
			'borde' => 'T',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $fontSize
	);	
	
$PDF->linea[2] = array(
			'posx' => 70,
			'ancho' => 35,
			'texto' => "PROVEEDOR/PRODUCTO:",
			'borde' => 'T',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $fontSize
	);	
$PDF->linea[3] = array(
			'posx' => 105,
			'ancho' => 95,
			'texto' => $cancelacion['CancelacionOrden']['proveedor_producto_odto'],
			'borde' => 'TR',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $fontSize
	);
		
$PDF->Imprimir_linea();
$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 30,
			'texto' => "SALDO ORDEN DTO:",
			'borde' => 'L',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $fontSize
	);
$PDF->linea[1] = array(
			'posx' => 40,
			'ancho' => 20,
			'texto' => '$ '.$util->nf($cancelacion['CancelacionOrden']['saldo_orden_dto']),
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $fontSize
	);	
$PDF->linea[2] = array(
			'posx' => 60,
			'ancho' => 30,
			'texto' => 'CREDITO / DEBITO:',
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $fontSize
	);	
	
$PDF->linea[3] = array(
			'posx' => 90,
			'ancho' => 110,
			'texto' => $cancelacion['CancelacionOrden']['nc_nd_str'],
			'borde' => 'R',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $fontSize
	);	

	
$PDF->Imprimir_linea();
$PDF->linea[1] = array(
			'posx' => 10,
			'ancho' => 50,
			'texto' => 'TOTAL CANCELACION: $ ' . $util->nf($cancelacion['CancelacionOrden']['total_orden']),
			'borde' => 'L',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#F5f7f7',
			'size' => $fontSize
	);
$PDF->linea[2] = array(
			'posx' => 60,
			'ancho' => 30,
			'texto' => "A LA ORDEN DE:",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $fontSize
	);
$PDF->linea[3] = array(
			'posx' => 90,
			'ancho' => 70,
			'texto' => $cancelacion['CancelacionOrden']['a_la_orden_de'],
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#F5f7f7',
			'size' => $fontSize
	);
$PDF->linea[4] = array(
			'posx' => 160,
			'ancho' => 20,
			'texto' => "VENCIMIENTO:",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#F5f7f7',
			'size' => $fontSize
	);	
$PDF->linea[5] = array(
			'posx' => 180,
			'ancho' => 20,
			'texto' => $util->armaFecha($cancelacion['CancelacionOrden']['fecha_vto']),
			'borde' => 'R',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#F5f7f7',
			'size' => $fontSize
	);

$PDF->Imprimir_linea();	

$PDF->linea[1] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => 'CONCEPTO: ' . $cancelacion['CancelacionOrden']['concepto'],
			'borde' => 'LBR',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#F5f7f7',
			'size' => $fontSize - 1
	);

$PDF->Imprimir_linea();	
	
if(!empty($cancelacion['CancelacionOrden']['observaciones'])):


	$PDF->SetFontSizeConf(6);
	$PDF->Cell(25,4,'OBSERVACIONES:',0);
	$PDF->SetX(29);
	$PDF->MultiCell(0,0,str_replace("\n","",substr($cancelacion['CancelacionOrden']['observaciones'],0,250)),0,'L');
	$PDF->Ln(1);	
	

endif;
	
$PDF->Imprimir_linea();
if($cancelacion['CancelacionOrden']['nueva_orden_dto_id']!= 0):
	$PDF->ln(3);
	$PDF->linea[1] = array(
				'posx' => 10,
				'ancho' => 190,
				'texto' => "CANCELA CON: ORDEN DTO. #". $cancelacion['CancelacionOrden']['nueva_orden_dto_id']." | " . $cancelacion['CancelacionOrden']['norden_tipo_nro_odto'] ." | ". $cancelacion['CancelacionOrden']['norden_proveedor_producto_odto'],
				'borde' => 'LBTR',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#F5f7f7',
				'size' => 8
		);
	$PDF->Imprimir_linea();
endif;
$PDF->ln(3);

$PDF->linea[1] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => "DETALLE DE CUOTAS INCLUIDAS EN LA ORDEN DE CANCELACION",
			'borde' => 'LBTR',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#F5f7f7',
			'size' => 10
	);
$PDF->Imprimir_linea();

//PERIODO	ORDEN	TIPO / NUMERO	PROVEEDOR / PRODUCTO	CUOTA	CONCEPTO	VTO	IMPORTE
$PDF->ln(3);

$W1 = array(0,20,20,50,10,30,15,20);
$L1 = $PDF->armaAnchoColumnas($W1);

$PDF->linea[1] = array(
			'posx' => 10,
			'ancho' => 20,
			'texto' => "ORGANISMO",
			'borde' => 'LBT',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
$PDF->linea[2] = array(
			'posx' => 30,
			'ancho' => 15,
			'texto' => "PERIODO",
			'borde' => 'BT',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
$PDF->linea[3] = array(
			'posx' => 45,
			'ancho' => 55,
			'texto' => "PROVEEDOR / PRODUCTO",
			'borde' => 'BT',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
$PDF->linea[4] = array(
			'posx' => 100,
			'ancho' => 10,
			'texto' => "CUOTA",
			'borde' => 'BT',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);	
$PDF->linea[5] = array(
			'posx' => 110,
			'ancho' => 50,
			'texto' => "CONCEPTO",
			'borde' => 'BT',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
$PDF->linea[6] = array(
			'posx' => 160,
			'ancho' => 20,
			'texto' => "VENCIMIENTO",
			'borde' => 'BT',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
$PDF->linea[7] = array(
			'posx' => 180,
			'ancho' => 20,
			'texto' => "IMPORTE",
			'borde' => 'BTR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);						
$PDF->Imprimir_linea();

if(!empty($cancelacion['CancelacionOrdenCuota'])):

	$ACU = 0;
	$fsize = 7;

	foreach($cancelacion['CancelacionOrdenCuota'] as $cuota){
		
		$ACU += $cuota['importe'];
		
		$PDF->linea[1] = array(
					'posx' => 10,
					'ancho' => 20,
					'texto' => substr($cuota['OrdenDescuentoCuota']['organismo'],0,12),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => $fsize
			);
		$PDF->linea[2] = array(
					'posx' => 30,
					'ancho' => 15,
					'texto' => $util->periodo($cuota['OrdenDescuentoCuota']['periodo']),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => $fsize
			);
		$PDF->linea[3] = array(
					'posx' => 45,
					'ancho' => 55,
					'texto' => substr($cuota['OrdenDescuentoCuota']['proveedor_producto'],0,36),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => $fsize
			);
		$PDF->linea[4] = array(
					'posx' => 100,
					'ancho' => 10,
					'texto' => $cuota['OrdenDescuentoCuota']['cuota'],
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => $fsize
			);	
		$PDF->linea[5] = array(
					'posx' => 110,
					'ancho' => 50,
					'texto' => $cuota['OrdenDescuentoCuota']['tipo_cuota_desc'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => $fsize
			);
		$PDF->linea[6] = array(
					'posx' => 160,
					'ancho' => 20,
					'texto' => $util->armaFecha($cuota['OrdenDescuentoCuota']['vencimiento']),
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => $fsize
			);
		$PDF->linea[7] = array(
					'posx' => 180,
					'ancho' => 20,
					'texto' => $util->nf($cuota['importe']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => $fsize
			);						
		$PDF->Imprimir_linea();		
	}
	
	$PDF->linea[0] = array(
				'posx' => 10,
				'ancho' => 170,
				'texto' => "TOTAL CUOTAS",
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => 7
		);
	$PDF->linea[1] = array(
				'posx' => 180,
				'ancho' => 20,
				'texto' => $util->nf($ACU),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => 7
		);		
	$PDF->Imprimir_linea();	

	if($cancelacion['CancelacionOrden']['importe_diferencia'] != 0):
	
		$PDF->linea[0] = array(
					'posx' => 10,
					'ancho' => 170,
					'texto' => $util->globalDato($cancelacion['CancelacionOrden']['tipo_cuota_diferencia']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#D8DBD4',
					'size' => 7
			);
		$PDF->linea[1] = array(
					'posx' => 180,
					'ancho' => 20,
					'texto' => $util->nf($cancelacion['CancelacionOrden']['importe_diferencia']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#D8DBD4',
					'size' => 7
			);		
		$PDF->Imprimir_linea();		
	
	endif;
	
	$PDF->linea[0] = array(
				'posx' => 10,
				'ancho' => 170,
				'texto' => "TOTAL CANCELACION",
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => 7
		);
	$PDF->linea[1] = array(
				'posx' => 180,
				'ancho' => 20,
				'texto' => $util->nf($ACU + $cancelacion['CancelacionOrden']['importe_diferencia']),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => 7
		);		
	$PDF->Imprimir_linea();		
	
endif;


$PDF->Output("orden_cancelacion_".$cancelacion['CancelacionOrden']['id'].".pdf");


?>