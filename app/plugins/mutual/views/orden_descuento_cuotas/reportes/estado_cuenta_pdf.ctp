<?php 
App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

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
$W = array(10,18,52,8,22,10,15,10,15,15,15);
$L1 = $PDF->armaAnchoColumnas($W);


$PDF->bMargen = 10;
//$PDF->AddPage();	

$fontSize = 6;
$PDF->encabezado = array();

#imprimo los datos del socio
$PDF->encabezado[0] = array();
$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => 20,
			'texto' => 'SOCIO #'.$socio['Socio']['id'],
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
$PDF->encabezado[0][1] = array(
			'posx' => $L1[0] + 20,
			'ancho' => 30,
			'texto' => 'ESTADO: '. ($socio['Socio']['activo'] == 1 ? 'VIGENTE' : 'NO VIGENTE'),
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
$PDF->encabezado[0][2] = array(
			'posx' => $L1[0] + 50,
			'ancho' => 140,
			'texto' => 'ULTIMA CALIFICACION: '. $util->globalDato($socio['Socio']['calificacion']) . ' | FECHA CALIFICACION: ' . $util->armaFecha($socio['Socio']['fecha_calificacion']),
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);	
$PDF->ln(5);
#imprimo los titulos de las columnas
$PDF->encabezado[1] = array();	

$PDF->encabezado[1][0] = array(
			'posx' => $L1[0],
			'ancho' => $W[0],
			'texto' => 'ORD.DTO.',
			'borde' => 'LTB',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);	
$PDF->encabezado[1][1] = array(
			'posx' => $L1[1],
			'ancho' => $W[1],
			'texto' => 'TIPO #NUMERO',
			'borde' => 'TB',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);
$PDF->encabezado[1][2] = array(
			'posx' => $L1[2],
			'ancho' => $W[2],
			'texto' => 'PROVEEDOR / PRODUCTO',
			'borde' => 'TB',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);			
$PDF->encabezado[1][3] = array(
			'posx' => $L1[3],
			'ancho' => $W[3],
			'texto' => 'CUOTA',
			'borde' => 'TB',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);
$PDF->encabezado[1][4] = array(
			'posx' => $L1[4],
			'ancho' => $W[4],
			'texto' => 'CONCEPTO',
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
			'texto' => 'VTO',
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
			'texto' => 'ESTADO',
			'borde' => 'TB',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);	
	
$PDF->encabezado[1][7] = array(
			'posx' => $L1[7],
			'ancho' => $W[7],
			'texto' => 'SITUACION',
			'borde' => 'TB',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);
$PDF->encabezado[1][8] = array(
			'posx' => $L1[8],
			'ancho' => $W[8],
			'texto' => 'IMPORTE',
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
			'texto' => 'PAGADO',
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
$fontSize = 6;

foreach($cuotas as $periodo => $detalle):

	//imprimo el periodo
	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => 190,
				'texto' => $util->periodo($periodo,true,'/'),
				'borde' => '',
				'align' => 'L',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => 9
		);

	$PDF->Imprimir_linea();	
	
	// imprimo si tiene atraso periodos anteriores
	if($detalle['atraso'] != 0){
		
		$PDF->linea[9] = array(
					'posx' => $L1[9],
					'ancho' => $W[9],
					'texto' => 'SALDO ANTERIOR',
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSize
			);
		$PDF->linea[10] = array(
					'posx' => $L1[10],
					'ancho' => $W[10],
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
	$fontSize = 6;

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
					'texto' => $cuota['OrdenDescuentoCuota']['tipo_nro'],
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
					'texto' => substr($cuota['OrdenDescuentoCuota']['proveedor_producto'],0,39),
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
					'texto' => ($cuota['OrdenDescuentoCuota']['tipo_orden_dto'] != 'MUTUTPROCFIJ' ? $cuota['OrdenDescuentoCuota']['cuota']: ''),
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			); 
		$PDF->linea[4] = array(
					'posx' => $L1[4],
					'ancho' => $W[4],
					'texto' => substr($cuota['OrdenDescuentoCuota']['tipo_cuota_desc'],0,15),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);
		
		$PDF->linea[5] = array(
					'posx' => $L1[5],
					'ancho' => $W[5],
					'texto' => $util->armaFecha(( $cuota['OrdenDescuentoCuota']['estado'] != 'P' ? $cuota['OrdenDescuentoCuota']['vencimiento'] : $cuota['OrdenDescuentoCuota']['fecha_ultimo_pago'])),
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);	
			
		$PDF->linea[6] = array(
					'posx' => $L1[6],
					'ancho' => $W[6],
					'texto' => $cuota['OrdenDescuentoCuota']['estado_desc'],
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);	
			
		$PDF->linea[7] = array(
					'posx' => $L1[7],
					'ancho' => $W[7],
					'texto' => $cuota['OrdenDescuentoCuota']['situacion_desc'],
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize - 1
			);
		$PDF->linea[8] = array(
					'posx' => $L1[8],
					'ancho' => $W[8],
					'texto' => number_format($cuota['OrdenDescuentoCuota']['importe'],2) . ( $cuota['OrdenDescuentoCuota']['vencida'] == 1 ? '*' : ''),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);
		$PDF->linea[9] = array(
					'posx' => $L1[9],
					'ancho' => $W[9],
					'texto' => number_format($cuota['OrdenDescuentoCuota']['pagado'],2),
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
					'texto' => number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);	
	
		$PDF->Imprimir_linea();	
	endforeach;
	
	//IMPRIMO EL TOTAL DEL PERIODO
	$PDF->linea[7] = array(
				'posx' => $L1[7],
				'ancho' => $W[7],
				'texto' => 'TOTAL PERIODO',
				'borde' => 'T',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSize
		);
	$PDF->linea[8] = array(
				'posx' => $L1[8],
				'ancho' => $W[8],
				'texto' => number_format($ACU_IMPO_CUOTA,2),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSize
		);
	$PDF->linea[9] = array(
				'posx' => $L1[9],
				'ancho' => $W[9],
				'texto' => number_format($ACU_PAGO_CUOTA,2),
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
	$PDF->linea[7] = array(
				'posx' => $L1[7],
				'ancho' => $W[7],
				'texto' => 'SALDO ACUMULADO A ' . $util->periodo($periodo,true,'/'),
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSize
		);
	$PDF->linea[8] = array(
				'posx' => $L1[8],
				'ancho' => $W[8],
				'texto' => '',
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
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
				'texto' => number_format($ACU_SALDO_CUOTA_ACUM,2),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSize
		);		
	$PDF->Imprimir_linea();	

	//IMPRIMO LO ENVIADO A DESCUENTO Y DESCONTADO

	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => $W[0],
				'texto' => 'ENVIADO A DESCUENTO: ' . number_format($detalle['adebitar'],2) .' | DESCONTADO: ' . number_format($detalle['debitado'],2) ,
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '0',
				'colorf' => '#ccc',
				'size' => $fontSize
		);

	$PDF->Imprimir_linea();		
	
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