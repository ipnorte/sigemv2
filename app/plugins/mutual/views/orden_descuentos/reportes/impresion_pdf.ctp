<?php 
// debug($orden);
// debug($cuotas);
// exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("ORDEN DE DESCUENTO #".$orden['OrdenDescuento']['id']);
$PDF->SetFontSizeConf(8.5);

//$PDF->AddPage();

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->textoHeader = "";//"FECHA EMISION: " . $util->armaFecha($orden['OrdenDescuento']['fecha']);
$PDF->titulo['titulo3'] = "ORDEN DE DESCUENTO #".$orden['OrdenDescuento']['id'] . ($orden['OrdenDescuento']['activo'] == 0 ? " *** ANULADA ***" : "");
$PDF->titulo['titulo2'] = "";//'SOCIO: '. $orden['OrdenDescuento']['persona'];
$PDF->titulo['titulo1'] = "";//'PRODUCTO: '. $orden['OrdenDescuento']['proveedor_producto'];



$PDF->AddPage();
$PDF->reset();	

$fontSizeBody = 8;

$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 50,
			'texto' => utf8_decode($orden['OrdenDescuento']['beneficiario']),
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody + 2
);
$PDF->Imprimir_linea();

$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 25,
			'texto' => "FECHA EMISION: ",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
);
$PDF->linea[1] = array(
			'posx' => 35,
			'ancho' => 20,
			'texto' => $util->armaFecha($orden['OrdenDescuento']['fecha']),
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#E9E9E9',
			'size' => $fontSizeBody
);

$PDF->linea[2] = array(
			'posx' => 55,
			'ancho' => 20,
			'texto' => "REFERENCIA: ",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#E9E9E9',
			'size' => $fontSizeBody
);

$PDF->linea[3] = array(
			'posx' => 75,
			'ancho' => 40,
			'texto' => $orden['OrdenDescuento']['tipo_nro'],
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#E9E9E9',
			'size' => $fontSizeBody
);

$PDF->linea[4] = array(
			'posx' => 105,
			'ancho' => 20,
			'texto' => ($orden['OrdenDescuento']['permanente'] == 1 ? "PERMANENTE" : ($orden['OrdenDescuento']['reprogramada'] == 1 ? " - REPROGRAMADA":"")),
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#E9E9E9',
			'size' => $fontSizeBody
);

$PDF->Imprimir_linea();

$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 20,
			'texto' => "PRODUCTO:",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
);

$PDF->linea[1] = array(
			'posx' => 30,
			'ancho' => 160,
			'texto' => $orden['OrdenDescuento']['proveedor_producto'],
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#E9E9E9',
			'size' => $fontSizeBody
);

$PDF->Imprimir_linea();

$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 20,
			'texto' => "BENEFICIO:",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
);

$PDF->linea[1] = array(
			'posx' => 30,
			'ancho' => 160,
			'texto' => $orden['OrdenDescuento']['beneficio_str'],
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#E9E9E9',
			'size' => $fontSizeBody
);

$PDF->Imprimir_linea();


$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 20,
			'texto' => "INICIA EN:",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
);

$PDF->linea[1] = array(
			'posx' => 30,
			'ancho' => 20,
			'texto' => $util->periodo($orden['OrdenDescuento']['periodo_ini']),
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#E9E9E9',
			'size' => $fontSizeBody
);

$PDF->linea[2] = array(
			'posx' => 50,
			'ancho' => 30,
			'texto' => "1er VTO SOCIO:",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
);

$PDF->linea[3] = array(
			'posx' => 80,
			'ancho' => 20,
			'texto' => $util->armaFecha($orden['OrdenDescuento']['primer_vto_socio']),
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#E9E9E9',
			'size' => $fontSizeBody
);

$PDF->linea[4] = array(
			'posx' => 100,
			'ancho' => 30,
			'texto' => "1er VTO PROV.:",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
);

$PDF->linea[5] = array(
			'posx' => 130,
			'ancho' => 20,
			'texto' => $util->armaFecha($orden['OrdenDescuento']['primer_vto_proveedor']),
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#E9E9E9',
			'size' => $fontSizeBody
);

$PDF->Imprimir_linea();

$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 20,
			'texto' => "SOLICITADO:",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
);
$PDF->linea[1] = array(
			'posx' => 30,
			'ancho' => 20,
    'texto' => number_format((empty($orden['OrdenDescuento']['importe_solicitado']) || $orden['OrdenDescuento']['importe_solicitado'] == 0 ? $orden['OrdenDescuento']['importe_total'] : $orden['OrdenDescuento']['importe_solicitado']),2),
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#E9E9E9',
			'size' => $fontSizeBody
);

//$PDF->Imprimir_linea();


$PDF->linea[2] = array(
			'posx' => 50,
			'ancho' => 12,
			'texto' => "CUOTA:",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
);

$PDF->linea[3] = array(
			'posx' => 62,
			'ancho' => 20,
			'texto' => number_format($orden['OrdenDescuento']['importe_cuota'],2),
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#E9E9E9',
			'size' => $fontSizeBody
);

$PDF->linea[4] = array(
			'posx' => 82,
			'ancho' => 12,
			'texto' => "PLAZO:",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
);

$PDF->linea[5] = array(
			'posx' => 94,
			'ancho' => 30,
			'texto' => ($orden['OrdenDescuento']['permanente'] == 0 ? $orden['OrdenDescuento']['cuotas'] . " CUOTAS":"MENSUAL"),
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#E9E9E9',
			'size' => $fontSizeBody
);

$PDF->linea[6] = array(
			'posx' => 114,
			'ancho' => 30,
			'texto' => "IMPORTE TOTAL:",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
);

$PDF->linea[7] = array(
			'posx' => 144,
			'ancho' => 30,
			'texto' => number_format($orden['OrdenDescuento']['importe_total'],2),
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#E9E9E9',
			'size' => $fontSizeBody
);

$PDF->Imprimir_linea();

if($orden['OrdenDescuento']['permanente'] == 1 && $orden['OrdenDescuento']['activo'] == 0):

	$PDF->linea[0] = array(
				'posx' => 10,
				'ancho' => 30,
				'texto' => "*** ANULADA *** " . (!empty($orden['OrdenDescuento']['periodo_hasta']) ? "( LIQUIDAR HASTA ".$util->periodo($orden['OrdenDescuento']['periodo_hasta'],true).")" : ""),
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
	);
	$PDF->Imprimir_linea();

endif;
$PDF->Ln(1);
$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 30,
			'texto' => "DETALLE DE CUOTAS",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody + 2
);

$PDF->Imprimir_linea();
$PDF->Ln(1);
// CUOTA 	PERIODO 	VTO / PAGO 	CONCEPTO 	ESTADO 	SIT 	IMPORTE 	PAGADO 	SALDO
//$W1 = array(0,20,20,50,10,30,15,20);
//$L1 = $PDF->armaAnchoColumnas($W1);

$W = array(10,20,20,40,20,20,20,20,20);
$P = $PDF->armaAnchoColumnas($W);

$fontSizeBody = 7;

$PDF->linea[0] = array(
			'posx' => $P[0],
			'ancho' => $W[0],
			'texto' => "CUOTA",
			'borde' => 'LTB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
);
$PDF->linea[1] = array(
			'posx' => $P[1],
			'ancho' => $W[1],
			'texto' => "PERIODO",
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
);
$PDF->linea[2] = array(
			'posx' => $P[2],
			'ancho' => $W[2],
			'texto' => "VENCIMIENTO",
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
);
$PDF->linea[3] = array(
			'posx' => $P[3],
			'ancho' => $W[3],
			'texto' => "CONCEPTO",
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
);
$PDF->linea[4] = array(
			'posx' => $P[4],
			'ancho' => $W[4],
			'texto' => "ESTADO",
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
);

$PDF->linea[5] = array(
			'posx' => $P[5],
			'ancho' => $W[5],
			'texto' => "SITUACION",
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
);

$PDF->linea[6] = array(
			'posx' => $P[6],
			'ancho' => $W[6],
			'texto' => "IMPORTE",
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
);
$PDF->linea[7] = array(
			'posx' => $P[7],
			'ancho' => $W[7],
			'texto' => "PAGADO",
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
);

$PDF->linea[8] = array(
			'posx' => $P[8],
			'ancho' => $W[8],
			'texto' => "SALDO",
			'borde' => 'TBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
);

$PDF->Imprimir_linea();

if(!empty($cuotas)):

	$fontSizeBody = 7;
	
	$ACUM = 0;
	$ACU_IMPO_CUOTA = 0;
	$ACU_PAGO_CUOTA = 0;
	$ACU_SALDO_CUOTA = 0;	

	foreach($cuotas as $cuota):
	
		$fontSizeBody = 7;
	
		$ACUM += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
		$ACU_IMPO_CUOTA += $cuota['OrdenDescuentoCuota']['importe'];
		$ACU_PAGO_CUOTA += $cuota['OrdenDescuentoCuota']['pagado'];
		$ACU_SALDO_CUOTA += $cuota['OrdenDescuentoCuota']['saldo_cuota'];	
	
		$PDF->linea[0] = array(
					'posx' => $P[0],
					'ancho' => $W[0],
					'texto' => $cuota['OrdenDescuentoCuota']['cuota'],
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => ($cuota['OrdenDescuentoCuota']['saldo_cuota'] != $cuota['OrdenDescuentoCuota']['importe'] ? "B" : ""),
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);	
		
		$PDF->linea[1] = array(
					'posx' => $P[1],
					'ancho' => $W[1],
					'texto' => $util->periodo($cuota['OrdenDescuentoCuota']['periodo']),
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => ($cuota['OrdenDescuentoCuota']['saldo_cuota'] != $cuota['OrdenDescuentoCuota']['importe'] ? "B" : ""),
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);

		$PDF->linea[2] = array(
					'posx' => $P[2],
					'ancho' => $W[2],
					'texto' => $util->armaFecha($cuota['OrdenDescuentoCuota']['vencimiento']),
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => ($cuota['OrdenDescuentoCuota']['saldo_cuota'] != $cuota['OrdenDescuentoCuota']['importe'] ? "B" : ""),
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);	

		$PDF->linea[3] = array(
					'posx' => $P[3],
					'ancho' => $W[3],
					'texto' => substr($cuota['OrdenDescuentoCuota']['tipo_cuota_desc'],0,30),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => ($cuota['OrdenDescuentoCuota']['saldo_cuota'] != $cuota['OrdenDescuentoCuota']['importe'] ? "B" : ""),
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);

		$PDF->linea[4] = array(
					'posx' => $P[4],
					'ancho' => $W[4],
					'texto' => $cuota['OrdenDescuentoCuota']['estado_desc'],
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => ($cuota['OrdenDescuentoCuota']['saldo_cuota'] != $cuota['OrdenDescuentoCuota']['importe'] ? "B" : ""),
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);	

		$PDF->linea[5] = array(
					'posx' => $P[5],
					'ancho' => $W[5],
					'texto' => $cuota['OrdenDescuentoCuota']['situacion_desc'],
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => ($cuota['OrdenDescuentoCuota']['saldo_cuota'] != $cuota['OrdenDescuentoCuota']['importe'] ? "B" : ""),
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);

		$PDF->linea[6] = array(
					'posx' => $P[6],
					'ancho' => $W[6],
					'texto' => number_format($cuota['OrdenDescuentoCuota']['importe'],2),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => ($cuota['OrdenDescuentoCuota']['saldo_cuota'] != $cuota['OrdenDescuentoCuota']['importe'] ? "B" : ""),
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);

		$PDF->linea[7] = array(
					'posx' => $P[7],
					'ancho' => $W[7],
					'texto' => number_format($cuota['OrdenDescuentoCuota']['pagado'],2),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => ($cuota['OrdenDescuentoCuota']['saldo_cuota'] != $cuota['OrdenDescuentoCuota']['importe'] ? "B" : ""),
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		$PDF->linea[8] = array(
					'posx' => $P[8],
					'ancho' => $W[8],
					'texto' => number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => ($cuota['OrdenDescuentoCuota']['saldo_cuota'] != $cuota['OrdenDescuentoCuota']['importe'] ? "B" : ""),
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);				
	
		$PDF->Imprimir_linea();
		$PDF->Ln(1);
		
		if(!empty($cuota['OrdenDescuentoCuota']['cobros'])):
		
//			$fontSizeBody = 6;
		
			foreach($cuota['OrdenDescuentoCuota']['cobros'] as $cobro):
			
				$PDF->linea[3] = array(
							'posx' => $P[3],
							'ancho' => $W[3],
							'texto' => $util->armaFecha($cobro['OrdenDescuentoCobroCuota']['fecha_cobro']) . "|" . $util->periodo($cobro['OrdenDescuentoCobroCuota']['periodo_cobro']),
							'borde' => '',
							'align' => 'R',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
				);
				$PDF->linea[4] = array(
							'posx' => $P[4],
							'ancho' => $W[4] + $W[5],
							'texto' => $cobro['OrdenDescuentoCobroCuota']['tipo_cobro_desc'],
							'borde' => '',
							'align' => 'L',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
				);
				$PDF->linea[6] = array(
							'posx' => $P[6],
							'ancho' => $W[6],
							'texto' => number_format($cobro['OrdenDescuentoCobroCuota']['importe'],2),
							'borde' => '',
							'align' => 'R',
							'fondo' => 0,
							'style' => '',
							'colorf' => '#ccc',
							'size' => $fontSizeBody
				);	

				if($cobro['OrdenDescuentoCobroCuota']['cancelacion_orden_id'] != 0):
					$PDF->linea[7] = array(
								'posx' => $P[7],
								'ancho' => $W[7] + $W[8],
								'texto' => 'ORD.CANC. #'.$cobro['OrdenDescuentoCobroCuota']['cancelacion_orden_id'],
								'borde' => '',
								'align' => 'L',
								'fondo' => 0,
								'style' => '',
								'colorf' => '#ccc',
								'size' => $fontSizeBody
					);				
				endif;
				
				$PDF->Imprimir_linea();			
			
			endforeach;
			$PDF->linea[3] = array(
						'posx' => $P[3],
						'ancho' => $W[3] + $W[4] + $W[5] + $W[6] + $W[7] + $W[8],
						'texto' => "",
						'borde' => 'T',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
			);	
			$PDF->Imprimir_linea();			
		
		endif;
		
		
	endforeach;
	
	$PDF->linea[0] = array(
				'posx' => $P[0],
				'ancho' => $W[0] + $W[1] + $W[2] + $W[3] + $W[4] + $W[5],
				'texto' => "TOTALES",
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
	);

	$PDF->linea[6] = array(
				'posx' => $P[6],
				'ancho' => $W[6],
				'texto' => $util->nf($ACU_IMPO_CUOTA),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
	);
	$PDF->linea[7] = array(
				'posx' => $P[7],
				'ancho' => $W[7],
				'texto' => $util->nf($ACU_PAGO_CUOTA),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
	);
	$PDF->linea[8] = array(
				'posx' => $P[8],
				'ancho' => $W[8],
				'texto' => $util->nf($ACU_SALDO_CUOTA),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
	);			
	
	$PDF->Imprimir_linea();		
	

endif;


$PDF->Output("orden_descuento_".$orden['OrdenDescuento']['id'].".pdf");

?>