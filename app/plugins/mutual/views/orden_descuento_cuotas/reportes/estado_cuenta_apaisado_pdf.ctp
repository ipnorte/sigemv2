<?php 

//debug($cuotas);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF("L");
$PDF->PIEUser=true;

$PDF->SetTitle("Estado de Cuenta Socio #".$socio['Socio']['id']);
$PDF->SetFontSizeConf(8.5);

//$PDF->AddPage();

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->textoHeader =  'ESTADO DE '.($solo_deuda == 1 ? 'DEUDA': 'CUENTA').' DEL SOCIO (S.E.U.O.)';
$PDF->titulo['titulo1'] = '';
$PDF->titulo['titulo3'] = $this->requestAction('/config/global_datos/valor/'.$socio['Persona']['tipo_documento'].'/concepto_1') .' '. $socio['Persona']['documento'] . '-' . $socio['Persona']['apellido'] .', '.$socio['Persona']['nombre'];
$PDF->titulo['titulo2'] = "PERIODO ".$util->periodo($periodo_d,true,'/')." A " . $util->periodo($periodo_h,true,'/');


$cero = 0;
// ORDEN  	TIPO / NUMERO  	PROVEEDOR / PRODUCTO  	CUOTA  	CONCEPTO  	VTO  	ESTADO  	SIT  	IMPORTE  	PAGADO  	SALDO CUOTA 
// anchos de columnas 
// 277
$W = array(11,20,25,70,12,35,20,13,20,17,17,17);
$L1 = $PDF->armaAnchoColumnas($W);


$PDF->bMargen = 10;
//$PDF->AddPage();	

$fontSize = 8;
$PDF->encabezado = array();

#imprimo los datos del socio

$backColor = "#D8DBD4";
$sizeSocio = 9;

$PDF->encabezado[0] = array();
$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => 15,
			'texto' => 'SOCIO ',
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => $backColor,
			'size' => $sizeSocio
	);
$PDF->encabezado[0][1] = array(
			'posx' => $L1[0] + 15,
			'ancho' => 15,
			'texto' => '#'.$socio['Socio']['id'],
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => $backColor,
			'size' => $sizeSocio
	);	
$PDF->encabezado[0][2] = array(
			'posx' => $L1[0] + 30,
			'ancho' => 20,
			'texto' => 'ESTADO: ',
			'borde' => '',
			'align' => 'R',
			'fondo' => 1,
			'style' => '',
			'colorf' => $backColor,
			'size' => $sizeSocio
	);
$PDF->encabezado[0][3] = array(
			'posx' => $L1[0] + 50,
			'ancho' => 20,
			'texto' => ($socio['Socio']['activo'] == 1 ? 'VIGENTE' : 'NO VIGENTE'),
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => $backColor,
			'size' => $sizeSocio
	);	
$PDF->encabezado[0][4] = array(
			'posx' => $L1[0] + 70,
			'ancho' => 40,
			'texto' => 'ULTIMA CALIFICACION: ',
			'borde' => '',
			'align' => 'R',
			'fondo' => 1,
			'style' => '',
			'colorf' => $backColor,
			'size' => $sizeSocio
	);
$PDF->encabezado[0][5] = array(
			'posx' => $L1[0] + 110,
			'ancho' => 50,
			'texto' => $util->globalDato($socio['Socio']['calificacion']),
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 9
	);
$PDF->encabezado[0][6] = array(
			'posx' => $L1[0] + 160,
			'ancho' => 40,
			'texto' => 'FECHA CALIFICACION: ',
			'borde' => '',
			'align' => 'R',
			'fondo' => 1,
			'style' => '',
			'colorf' => $backColor,
			'size' => $sizeSocio
	);
$PDF->encabezado[0][7] = array(
			'posx' => $L1[0] + 200,
			'ancho' => 77,
			'texto' => $util->armaFecha($socio['Socio']['fecha_calificacion']),
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => $backColor,
			'size' => $sizeSocio
	);						
$PDF->ln(5);
#imprimo los titulos de las columnas
$PDF->encabezado[1] = array();	

$PDF->encabezado[1][0] = array(
			'posx' => $L1[0],
			'ancho' => $W[0],
			'texto' => 'ORDEN',
			'borde' => 'LTB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);	
$PDF->encabezado[1][1] = array(
			'posx' => $L1[1],
			'ancho' => $W[1],
			'texto' => 'ORGANISMO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);	
$PDF->encabezado[1][2] = array(
			'posx' => $L1[2],
			'ancho' => $W[2],
			'texto' => 'TIPO #NUMERO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);
$PDF->encabezado[1][3] = array(
			'posx' => $L1[3],
			'ancho' => $W[3],
			'texto' => 'PROVEEDOR / PRODUCTO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);			
$PDF->encabezado[1][4] = array(
			'posx' => $L1[4],
			'ancho' => $W[4],
			'texto' => 'CUOTA',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);
$PDF->encabezado[1][5] = array(
			'posx' => $L1[5],
			'ancho' => $W[5],
			'texto' => 'CONCEPTO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);

$PDF->encabezado[1][6] = array(
			'posx' => $L1[6],
			'ancho' => $W[6],
			'texto' => 'VTO / PAGO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);	
	
$PDF->encabezado[1][7] = array(
			'posx' => $L1[7],
			'ancho' => $W[7],
			'texto' => 'ESTADO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);	
	
$PDF->encabezado[1][8] = array(
			'posx' => $L1[8],
			'ancho' => $W[8],
			'texto' => 'SITUACION',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);
$PDF->encabezado[1][9] = array(
			'posx' => $L1[9],
			'ancho' => $W[9],
			'texto' => 'IMPORTE',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);
$PDF->encabezado[1][10] = array(
			'posx' => $L1[10],
			'ancho' => $W[10],
			'texto' => 'PAGADO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);
$PDF->encabezado[1][11] = array(
			'posx' => $L1[11],
			'ancho' => $W[11],
			'texto' => 'SALDO',
			'borde' => 'TBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);						
$PDF->AddPage();	
//$PDF->Imprimir_linea();

$PDF->Reset();
$fontSize = 8;

if(!empty($proveedor_razon_social)):

	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => 25,
				'texto' => "PROVEEDOR: ",
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => $backColor,
				'size' => 10
		);

	$PDF->linea[1] = array(
				'posx' => $L1[0] + 25,
				'ancho' => 277 - 25,
				'texto' => $proveedor_razon_social,
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => $backColor,
				'size' => 10
		);
//
	$PDF->Imprimir_linea();	

endif;

if(!empty($codigo_organismo)):

	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => 25,
				'texto' => "ORGANISMO: ",
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => $backColor,
				'size' => 10
		);

	$PDF->linea[1] = array(
				'posx' => $L1[0] + 25,
				'ancho' => 277 - 25,
				'texto' => $util->globalDato($codigo_organismo),
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => $backColor,
				'size' => 10
		);
//
	$PDF->Imprimir_linea();	

endif;


foreach($cuotas as $periodo => $detalle):

	//imprimo el periodo
	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => 277,
				'texto' => $util->periodo($periodo,true,'/'),
				'borde' => '',
				'align' => 'L',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => $backColor,
				'size' => 10
		);

	$PDF->Imprimir_linea();	
	
	// imprimo si tiene atraso periodos anteriores
	if($detalle['atraso'] != 0){
		
		$PDF->linea[10] = array(
					'posx' => $L1[10],
					'ancho' => $W[10],
					'texto' => 'SALDO ANTERIOR',
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSize
			);
		$PDF->linea[11] = array(
					'posx' => $L1[11],
					'ancho' => $W[11],
					'texto' => number_format($detalle['atraso'],2),
					'borde' => 'B',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSize
			);			
		$PDF->Imprimir_linea();			
	}
	
	//imprimo el detalle del periodo
	$fontSize = 8;

	$ACU_IMPO_CUOTA = 0;
	$ACU_PAGO_CUOTA = 0;
	$ACU_SALDO_CUOTA = 0;
	$ACU_SALDO_CUOTA_ACUM = $detalle['atraso'];	
	
	foreach($detalle['detalle_cuotas'] as $cuota):
	
	
//		$ACU_IMPO_CUOTA += $cuota['OrdenDescuentoCuota']['importe'];
//		$ACU_PAGO_CUOTA += $cuota['OrdenDescuentoCuota']['pagado'];
//		$ACU_SALDO_CUOTA += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
//		$ACU_SALDO_CUOTA_ACUM += $cuota['OrdenDescuentoCuota']['saldo_cuota'];	
		
		
		$ACU_IMPO_CUOTA += ($cuota['OrdenDescuentoCuota']['estado'] != 'B' ? $cuota['OrdenDescuentoCuota']['importe'] : 0);
		$ACU_PAGO_CUOTA += ($cuota['OrdenDescuentoCuota']['estado'] != 'B' ? $cuota['OrdenDescuentoCuota']['pagado'] : 0);
		$ACU_SALDO_CUOTA += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
		$ACU_SALDO_CUOTA_ACUM += $cuota['OrdenDescuentoCuota']['saldo_cuota'];		
	
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => $W[0],
					'texto' => $cuota['OrdenDescuentoCuota']['orden_descuento_id'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);	
		$PDF->linea[1] = array(
					'posx' => $L1[1],
					'ancho' => $W[1],
					'texto' => substr($cuota['OrdenDescuentoCuota']['organismo'],0,11),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);
		$PDF->linea[2] = array(
					'posx' => $L1[2],
					'ancho' => $W[2],
					'texto' => $cuota['OrdenDescuentoCuota']['tipo_nro'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);			
		$PDF->linea[3] = array(
					'posx' => $L1[3],
					'ancho' => $W[3],
					'texto' => substr($cuota['OrdenDescuentoCuota']['proveedor_producto'],0,39),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);			
		$PDF->linea[4] = array(
					'posx' => $L1[4],
					'ancho' => $W[4],
					'texto' => ($cuota['OrdenDescuentoCuota']['tipo_orden_dto'] != 'MUTUTPROCFIJ' ? $cuota['OrdenDescuentoCuota']['cuota']: ''),
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			); 
		$PDF->linea[5] = array(
					'posx' => $L1[5],
					'ancho' => $W[5],
					'texto' => substr($cuota['OrdenDescuentoCuota']['tipo_cuota_desc'],0,15),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);
		
		$PDF->linea[6] = array(
					'posx' => $L1[6],
					'ancho' => $W[6],
					'texto' => $util->armaFecha(( $cuota['OrdenDescuentoCuota']['estado'] != 'P' ? $cuota['OrdenDescuentoCuota']['vencimiento'] : $cuota['OrdenDescuentoCuota']['fecha_ultimo_pago'])),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);	
			
		$PDF->linea[7] = array(
					'posx' => $L1[7],
					'ancho' => $W[7],
					'texto' => ($cuota['OrdenDescuentoCuota']['saldo_cuota'] == 0 ? 'P' : $cuota['OrdenDescuentoCuota']['estado']),
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);	
			
		$PDF->linea[8] = array(
					'posx' => $L1[8],
					'ancho' => $W[8],
					'texto' => substr($cuota['OrdenDescuentoCuota']['situacion_desc'],0,13),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize - 1
			);
		$PDF->linea[9] = array(
					'posx' => $L1[9],
					'ancho' => $W[9],
					'texto' => number_format($cuota['OrdenDescuentoCuota']['importe'],2) . ( $cuota['OrdenDescuentoCuota']['vencida'] == 1 ? '*' : ''),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);
		$PDF->linea[10] = array(
					'posx' => $L1[10],
					'ancho' => $W[10],
					'texto' => number_format($cuota['OrdenDescuentoCuota']['pagado'],2),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);
		$PDF->linea[11] = array(
					'posx' => $L1[11],
					'ancho' => $W[11],
					'texto' => number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);	
	
		$PDF->Imprimir_linea();

		if($discrimina_pagos == 1 && !empty($cuota['OrdenDescuentoCuota']['cobros'])):
			$sizePago = $fontSize - 2;
			$style = "";
			foreach($cuota['OrdenDescuentoCuota']['cobros'] as $cobro):
				$PDF->linea[6] = array(
							'posx' => $L1[6],
							'ancho' => $W[6],
							'texto' => $util->armaFecha($cobro['OrdenDescuentoCobroCuota']['fecha_cobro']),
							'borde' => '',
							'align' => 'L',
							'fondo' => 0,
							'style' => $style,
							'colorf' => '#ccc',
							'size' => $sizePago
					);
				$PDF->linea[7] = array(
							'posx' => $L1[7],
							'ancho' => $W[7],
							'texto' => $util->periodo($cobro['OrdenDescuentoCobroCuota']['periodo_cobro']),
							'borde' => '',
							'align' => 'L',
							'fondo' => 0,
							'style' => $style,
							'colorf' => '#ccc',
							'size' => $sizePago
					);	
				$PDF->linea[8] = array(
							'posx' => $L1[8],
							'ancho' => $W[8] + $W[9],
							'texto' => substr($cobro['OrdenDescuentoCobroCuota']['tipo_cobro_desc'],0,13),
							'borde' => '',
							'align' => 'L',
							'fondo' => 0,
							'style' => $style,
							'colorf' => '#ccc',
							'size' => $sizePago
					);
				$PDF->linea[10] = array(
							'posx' => $L1[10],
							'ancho' => $W[10],
							'texto' => $util->nf($cobro['OrdenDescuentoCobroCuota']['importe']),
							'borde' => '',
							'align' => 'R',
							'fondo' => 0,
							'style' => $style,
							'colorf' => '#ccc',
							'size' => $sizePago
					);
				if($cobro['OrdenDescuentoCobroCuota']['reversado'] == 1):
					$PDF->linea[11] = array(
								'posx' => $L1[11],
								'ancho' => $W[11],
								'texto' => "REVERSADO",
								'borde' => '',
								'align' => 'L',
								'fondo' => 0,
								'style' => $style,
								'colorf' => '#ccc',
								'size' => $sizePago
						);
				endif;
				$PDF->Imprimir_linea();			
			endforeach;
		endif;
		
	endforeach;
	
	//IMPRIMO EL TOTAL DEL PERIODO
	$PDF->linea[8] = array(
				'posx' => $L1[8],
				'ancho' => $W[8],
				'texto' => 'TOTAL PERIODO',
				'borde' => 'T',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSize
		);
	$PDF->linea[9] = array(
				'posx' => $L1[9],
				'ancho' => $W[9],
				'texto' => number_format($ACU_IMPO_CUOTA,2),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSize
		);
	$PDF->linea[10] = array(
				'posx' => $L1[10],
				'ancho' => $W[10],
				'texto' => number_format($ACU_PAGO_CUOTA,2),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSize
		);
	$PDF->linea[11] = array(
				'posx' => $L1[11],
				'ancho' => $W[11],
				'texto' => number_format($ACU_SALDO_CUOTA,2),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSize
		);		
	$PDF->Imprimir_linea();

	
	//IMPRIMO EL TOTAL ACUMULADO DEL PERIODO
	$PDF->linea[8] = array(
				'posx' => $L1[8],
				'ancho' => $W[8],
				'texto' => 'TOTAL ACUMULADO A ' . $util->periodo($periodo,true,'/'),
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSize
		);
	$PDF->linea[9] = array(
				'posx' => $L1[9],
				'ancho' => $W[9],
				'texto' => '',
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSize
		);
	$PDF->linea[10] = array(
				'posx' => $L1[10],
				'ancho' => $W[10],
				'texto' => '',
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSize
		);
	$PDF->linea[11] = array(
				'posx' => $L1[11],
				'ancho' => $W[11],
				'texto' => number_format($ACU_SALDO_CUOTA_ACUM,2),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSize
		);		
	$PDF->Imprimir_linea();	

	if($detalle['liquidado'] != 0 && $proveedor_id == 0):
	
		//IMPRIMO LO ENVIADO A DESCUENTO Y DESCONTADO
		$STR_DTO = "";
		$STR_DTO .= "LIQUIDACION: ". number_format($detalle['liquidado'],2);
		$STR_DTO .= " | A DEBITAR: " . number_format($detalle['adebitar'],2);
		$STR_DTO .= " | ACREDITADO PENDIENTE DE IMPUTAR: ". number_format($detalle['pendiente_imputar'],2);
		$STR_DTO .= " | IMPUTADO: " . number_format($detalle['imputado'],2);
		$STR_DTO .= " | SALDO: " . number_format($detalle['liquidado'] - $detalle['imputado'],2);
		
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => $W[0],
					'texto' => $STR_DTO,
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '0',
					'colorf' => '#ccc',
					'size' => $fontSize - 1
			);
	
		$PDF->Imprimir_linea();		
	
		$PDF->ln(3);	
	
	endif;
	
endforeach;
$PDF->Ln(10);
//IMPRIMO EL RESUMEN
if(isset($resumen)){
	
	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => 70,
				'texto' => 'RESUMEN GENERAL DE DEUDA *** S.E.U.O.***',
				'borde' => '',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => 7
		);

	$PDF->Imprimir_linea();
	

	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => 30,
				'texto' => 'SITUACION',
				'borde' => '',
				'align' => 'L',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => 6
		);
	$PDF->linea[1] = array(
				'posx' => $L1[0] + 30,
				'ancho' => 20,
				'texto' => 'VENCIDA',
				'borde' => '',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => 6
		);		
	$PDF->linea[2] = array(
				'posx' => $L1[0] + 50,
				'ancho' => 20,
				'texto' => 'A VENCER',
				'borde' => '',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => 6
		);
	$PDF->Imprimir_linea();		
	
	foreach($resumen as $item){
		
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => 30,
					'texto' => $item['descripcion_situacion'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => 6
			);
		$PDF->linea[1] = array(
					'posx' => $L1[0] + 30,
					'ancho' => 20,
					'texto' => $util->nf($item['total_adeudado_vencido']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => 6
			);		
		$PDF->linea[2] = array(
					'posx' => $L1[0] + 50,
					'ancho' => 20,
					'texto' => $util->nf($item['total_adeudado_avencer']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => 6
			);
		$PDF->Imprimir_linea();			
		
	}
}

$PDF->Ln(15);

// MANDO LAS LEYENDAS
$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W[0],
			'texto' => '*** S.E.U.O. (SALVO ERROR U OMISION) ***',
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);

$PDF->Imprimir_linea();
$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W[0],
			'texto' => '(*)Cuota Vencida',
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);

$PDF->Imprimir_linea();		

$PDF->Output("estado_cuenta_socio_#".$socio['Socio']['id']."_".$socio['Persona']['apellido']."_".$socio['Persona']['nombre'].".pdf");
?>