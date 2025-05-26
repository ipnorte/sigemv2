<?php 

//debug($liquidaciones['201010']['rendicion']);
//debug($socio_calificaciones);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("Resumen de Liquidacion");
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo1'] = $this->requestAction('/config/global_datos/valor/'.$socio['Persona']['tipo_documento'].'/concepto_1') .' '. $socio['Persona']['documento'] . ' - ' . $socio['Persona']['apellido'] .', '.$socio['Persona']['nombre'];;
$PDF->titulo['titulo2'] = '';
$PDF->titulo['titulo3'] = 'LIQUIDACION DE DEUDA *** S.E.U.O. *** ';


$W1 = array(10);
$L1 = $PDF->armaAnchoColumnas($W1);


$PDF->bMargen = 10;

$fontSize = 6;
$PDF->encabezado = array();

#imprimo los datos del socio
$PDF->encabezado[0] = array();

$fondo = "#F5f7f7";

$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => 20,
			'texto' => 'SOCIO #'.$socio['Socio']['id'],
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => $fondo,
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
			'colorf' => $fondo,
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
			'colorf' => $fondo,
			'size' => 7
	);
$PDF->encabezado[1][0] = array(
			'posx' => $L1[0],
			'ancho' => 25,
			'texto' => "CALIFICACIONES:",
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => $fondo,
			'size' => 7
	);
$PDF->encabezado[1][1] = array(
			'posx' => $L1[0] + 25,
			'ancho' => 165,
			'texto' => $socio_calificaciones,
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => $fondo,
			'size' => 7
	);			
$PDF->ln(5);

$PDF->AddPage();
$PDF->reset();


//seteo columnas para las cuotas
//ORD.DTO. | ORGANISMO | TIPO / NUMERO | PERIODO | PROVEEDOR / PRODUCTO | CUOTA  | CONCEPTO | A DEBITAR | DEBITADO | SALDO 
$Wc = array(10,15,15,15,40,10,25,15,15,15,15);
$Lc = $PDF->armaAnchoColumnas($Wc);

//seteo columnas para los adicionales
//ORD.DTO. | ORGANISMO | TIPO / NUMERO | PERIODO | PROVEEDOR / PRODUCTO | CONCEPTO | FORMULA | A DEBITAR | DEBITADO | SALDO
$Wa = array(10,15,15,15,35,20,35,15,15,15);
$La = $PDF->armaAnchoColumnas($Wa);

//seteo columnas del detalle de envio a descuento
//ORGANISMO | IDENTIFICACION | LIQUIDADO | ADEBITAR 
$Wd = array(20,140,15,15);
$Ld = $PDF->armaAnchoColumnas($Wd);

//seteo las columnas para la rendicion
//ORGANISMO  |	IDENTIFICACION  |	FECHA |	BANCO INTERCAMBIO |	CODIGO | DESCRIPCION | IMPORTE | O.COBRO
$Wd1 = array(15,77,10,35,8,15,15,15);
$Ld1 = $PDF->armaAnchoColumnas($Wd1);

//seteo las columnas para los reintegros
//#  |	LIQUIDACION  |	BENEFICIO | 	IMPORTE DEBITADO  |	IMPORTE IMPUTADO  |	IMPORTE REINTEGRO  	
$Wd2 = array(10,35,100,15,15,15);
$Ld2 = $PDF->armaAnchoColumnas($Wd2);


$fontSize_titulo = 10;
$fontSize_subtablas = 5;

foreach($liquidaciones as $periodo => $detalle):

	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => 190,
				'texto' => 'PERIODO '.$util->periodo($periodo,true),
				'borde' => 'LTBR',
				'align' => 'L',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => $fontSize_titulo,
				'family' => 'helvetica'
		);
	$PDF->Imprimir_linea();
	
	

	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => 190,
				'texto' => 'Estado: '. ($detalle['cabecera_liquidacion'][0]['Liquidacion']['cerrada']==1 ? '*** CERRADA ***' : '*** ABIERTA ***') .' | '.($detalle['cabecera_liquidacion'][0]['LiquidacionSocio']['imputada']==1 ? ' *** IMPUTADA ***' : ''),
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => 7
		);
	$PDF->Imprimir_linea();

	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => 190,
				'texto' => 'DETALLE DE CUOTAS INCLUIDAS EN LA LIQUIDACION',
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => 7
		);
	$PDF->Imprimir_linea();	

	$fondo = "#D8DBD4";
	$style="B";
	
	$PDF->linea[0] = array(
				'posx' => $Lc[0],
				'ancho' => $Wc[0],
				'texto' => 'ORD.DTO.',
				'borde' => '',
				'align' => 'L',
				'fondo' => 1,
				'style' => $style,
				'colorf' => $fondo,
				'size' => $fontSize_subtablas
		);
		
	$PDF->linea[1] = array(
				'posx' => $Lc[1],
				'ancho' => $Wc[1],
				'texto' => 'ORGANISMO',
				'borde' => '',
				'align' => 'L',
				'fondo' => 1,
				'style' => $style,
				'colorf' => $fondo,
				'size' => $fontSize_subtablas
		);

	$PDF->linea[2] = array(
				'posx' => $Lc[2],
				'ancho' => $Wc[2],
				'texto' => 'TIPO / NUMERO',
				'borde' => '',
				'align' => 'L',
				'fondo' => 1,
				'style' => $style,
				'colorf' => $fondo,
				'size' => $fontSize_subtablas
		);	

	$PDF->linea[3] = array(
				'posx' => $Lc[3],
				'ancho' => $Wc[3],
				'texto' => 'PERIODO',
				'borde' => '',
				'align' => 'L',
				'fondo' => 1,
				'style' => $style,
				'colorf' => $fondo,
				'size' => $fontSize_subtablas
		);

	$PDF->linea[4] = array(
				'posx' => $Lc[4],
				'ancho' => $Wc[4],
				'texto' => 'PROVEEDOR / PRODUCTO',
				'borde' => '',
				'align' => 'L',
				'fondo' => 1,
				'style' => $style,
				'colorf' => $fondo,
				'size' => $fontSize_subtablas
		);		

	$PDF->linea[5] = array(
				'posx' => $Lc[5],
				'ancho' => $Wc[5],
				'texto' => 'CUOTA',
				'borde' => '',
				'align' => 'L',
				'fondo' => 1,
				'style' => $style,
				'colorf' => $fondo,
				'size' => $fontSize_subtablas
		);	
	$PDF->linea[6] = array(
				'posx' => $Lc[6],
				'ancho' => $Wc[6],
				'texto' => 'CONCEPTO',
				'borde' => '',
				'align' => 'L',
				'fondo' => 1,
				'style' => $style,
				'colorf' => $fondo,
				'size' => $fontSize_subtablas
		);
	$PDF->linea[7] = array(
				'posx' => $Lc[7],
				'ancho' => $Wc[7],
				'texto' => 'IMPORTE',
				'borde' => '',
				'align' => 'R',
				'fondo' => 1,
				'style' => $style,
				'colorf' => $fondo,
				'size' => $fontSize_subtablas
		);	
	$PDF->linea[8] = array(
				'posx' => $Lc[8],
				'ancho' => $Wc[8],
				'texto' => 'LIQUIDADO',
				'borde' => '',
				'align' => 'R',
				'fondo' => 1,
				'style' => $style,
				'colorf' => $fondo,
				'size' => $fontSize_subtablas
		);
		
	$PDF->linea[9] = array(
				'posx' => $Lc[9],
				'ancho' => $Wc[9],
				'texto' => 'IMPUTADO',
				'borde' => '',
				'align' => 'R',
				'fondo' => 1,
				'style' => $style,
				'colorf' => $fondo,
				'size' => $fontSize_subtablas
		);
	$PDF->linea[10] = array(
				'posx' => $Lc[10],
				'ancho' => $Wc[10],
				'texto' => 'SALDO',
				'borde' => '',
				'align' => 'R',
				'fondo' => 1,
				'style' => $style,
				'colorf' => $fondo,
				'size' => $fontSize_subtablas
		);					
		
	$PDF->Imprimir_linea();	
	
	
	//////////////////////////////////////////////////////////////////////////////////////////////////
	// IMPRIMO EL DETALLE DE CUOTAS LIQUIDADAS
	//////////////////////////////////////////////////////////////////////////////////////////////////
	
	$TOTAL_ADEBITAR = 0;
	$TOTAL_DEBITADO = 0;
	$SALDO = 0;
	$SALDO_ATRASO = 0;
	$SALDO_PERIODO = 0;
	$TOTAL_ATRASO_ADEBITAR = 0;
	$TOTAL_ATRASO_DEBITADO = 0;
	$TOTAL_PERIODO_ADEBITAR =0;
	$TOTAL_PERIODO_DEBITADO =0;	
	
	$TOTAL_SALDO_ATRASO = 0;
	$TOTAL_SALDO_PERIODO = 0;
	$TOTAL_SALDO = 0;	
	
	foreach($detalle['cuotas'] as $cuota):
	
		if($cuota['LiquidacionCuota']['periodo_cuota'] != $periodo){
			$TOTAL_ATRASO_ADEBITAR += $cuota['LiquidacionCuota']['importe'];
			$TOTAL_ATRASO_DEBITADO += $cuota['LiquidacionCuota']['importe_debitado'];
			$TOTAL_SALDO_ATRASO += $cuota['LiquidacionCuota']['saldo_actual'];
		}else{
			$TOTAL_PERIODO_ADEBITAR += $cuota['LiquidacionCuota']['importe'];
			$TOTAL_PERIODO_DEBITADO += $cuota['LiquidacionCuota']['importe_debitado'];
			$TOTAL_SALDO_PERIODO += $cuota['LiquidacionCuota']['saldo_actual'];				
		}
		
		$TOTAL_ADEBITAR += $cuota['LiquidacionCuota']['importe'];
		$TOTAL_DEBITADO += $cuota['LiquidacionCuota']['importe_debitado'];	
		$TOTAL_SALDO += $cuota['LiquidacionCuota']['saldo_actual'];

		$SALDO_CUOTA = $cuota['LiquidacionCuota']['saldo_actual'] - $cuota['LiquidacionCuota']['importe_debitado'];
	
		$PDF->linea[0] = array(
					'posx' => $Lc[0],
					'ancho' => $Wc[0],
					'texto' => $cuota['OrdenDescuentoCuota']['orden_descuento_id'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#F5f7f7',
					'size' => $fontSize_subtablas
			);
			
		$PDF->linea[1] = array(
					'posx' => $Lc[1],
					'ancho' => $Wc[1],
					'texto' => substr($util->globalDato($cuota['LiquidacionCuota']['codigo_organismo']),0,20),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#F5f7f7',
					'size' => $fontSize_subtablas
			);
	
		$PDF->linea[2] = array(
					'posx' => $Lc[2],
					'ancho' => $Wc[2],
					'texto' => $cuota['OrdenDescuento']['tipo_orden_dto'].' #'.$cuota['OrdenDescuento']['numero'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#F5f7f7',
					'size' => $fontSize_subtablas
			);	
	
		$PDF->linea[3] = array(
					'posx' => $Lc[3],
					'ancho' => $Wc[3],
					'texto' => $util->periodo($cuota['LiquidacionCuota']['periodo_cuota']) . ($cuota['LiquidacionCuota']['periodo_cuota'] != $periodo ? '(*)':''),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#F5f7f7',
					'size' => $fontSize_subtablas
			);
	
		$PDF->linea[4] = array(
					'posx' => $Lc[4],
					'ancho' => $Wc[4],
					'texto' => substr($cuota['Proveedor']['razon_social_resumida'].' / '.$util->globalDato($cuota['LiquidacionCuota']['tipo_producto']),0,35),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#F5f7f7',
					'size' => $fontSize_subtablas
			);		
	
		$PDF->linea[5] = array(
					'posx' => $Lc[5],
					'ancho' => $Wc[5],
					'texto' => $cuota['OrdenDescuentoCuota']['nro_cuota'].'/'.$cuota['OrdenDescuento']['cuotas'],
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#F5f7f7',
					'size' => $fontSize_subtablas
			);	
		$PDF->linea[6] = array(
					'posx' => $Lc[6],
					'ancho' => $Wc[6],
					'texto' => $util->globalDato($cuota['LiquidacionCuota']['tipo_cuota']),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#F5f7f7',
					'size' => $fontSize_subtablas
			);
	
		$PDF->linea[7] = array(
					'posx' => $Lc[7],
					'ancho' => $Wc[7],
					'texto' => $util->nf($cuota['LiquidacionCuota']['importe']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#F5f7f7',
					'size' => $fontSize_subtablas
			);	
		$PDF->linea[8] = array(
					'posx' => $Lc[8],
					'ancho' => $Wc[8],
					'texto' => $util->nf($cuota['LiquidacionCuota']['saldo_actual']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#F5f7f7',
					'size' => $fontSize_subtablas
			);
			
		$PDF->linea[19] = array(
					'posx' => $Lc[9],
					'ancho' => $Wc[9],
					'texto' => $util->nf($cuota['LiquidacionCuota']['importe_debitado']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#F5f7f7',
					'size' => $fontSize_subtablas
			);
		$PDF->linea[10] = array(
					'posx' => $Lc[10],
					'ancho' => $Wc[10],
					'texto' => $util->nf($cuota['LiquidacionCuota']['saldo_actual'] - $cuota['LiquidacionCuota']['importe_debitado']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#F5f7f7',
					'size' => $fontSize_subtablas
			);					
			
		$PDF->Imprimir_linea();		
	
	
	endforeach;
	
	//////////////////////////////////////////////////////////////////////////////////////////////////
	//IMPRIMO EL TOTAL ATRASADO	
	//////////////////////////////////////////////////////////////////////////////////////////////////
	$PDF->linea[6] = array(
				'posx' => $Lc[6],
				'ancho' => $Wc[6],
				'texto' => '(*)ATRASO',
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $fontSize_subtablas
		);		
	
	$PDF->linea[7] = array(
				'posx' => $Lc[7],
				'ancho' => $Wc[7],
				'texto' => $util->nf($TOTAL_ATRASO_ADEBITAR),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $fontSize_subtablas
		);		
		
	$PDF->linea[8] = array(
				'posx' => $Lc[8],
				'ancho' => $Wc[8],
				'texto' => $util->nf($TOTAL_SALDO_ATRASO),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $fontSize_subtablas
		);
		
	$PDF->linea[9] = array(
				'posx' => $Lc[9],
				'ancho' => $Wc[9],
				'texto' => $util->nf($TOTAL_ATRASO_DEBITADO),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $fontSize_subtablas
		);
	$PDF->linea[10] = array(
				'posx' => $Lc[10],
				'ancho' => $Wc[10],
				'texto' => $util->nf($TOTAL_SALDO_ATRASO - $TOTAL_ATRASO_DEBITADO),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $fontSize_subtablas
		);				
		
	$PDF->Imprimir_linea();		
	
	//////////////////////////////////////////////////////////////////////////////////////////////////
	//IMPRIMO EL TOTAL DEL PERIODO
	//////////////////////////////////////////////////////////////////////////////////////////////////
	$PDF->linea[6] = array(
				'posx' => $Lc[6],
				'ancho' => $Wc[6],
				'texto' => 'PERIODO',
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $fontSize_subtablas
		);		
	
	$PDF->linea[7] = array(
				'posx' => $Lc[7],
				'ancho' => $Wc[7],
				'texto' => $util->nf($TOTAL_PERIODO_ADEBITAR),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $fontSize_subtablas
		);		
		
	$PDF->linea[8] = array(
				'posx' => $Lc[8],
				'ancho' => $Wc[8],
				'texto' => $util->nf($TOTAL_SALDO_PERIODO),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $fontSize_subtablas
		);
		
	$PDF->linea[9] = array(
				'posx' => $Lc[9],
				'ancho' => $Wc[9],
				'texto' => $util->nf($TOTAL_PERIODO_DEBITADO),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $fontSize_subtablas
		);
	$PDF->linea[10] = array(
				'posx' => $Lc[10],
				'ancho' => $Wc[10],
				'texto' => $util->nf($TOTAL_SALDO_PERIODO - $TOTAL_PERIODO_DEBITADO),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $fontSize_subtablas
		);				
		
	$PDF->Imprimir_linea();			
	
	//////////////////////////////////////////////////////////////////////////////////////////////////
	//IMPRIMO EL SUBTOTAL DE LAS CUOTAS
	//////////////////////////////////////////////////////////////////////////////////////////////////
	$PDF->linea[6] = array(
				'posx' => $Lc[6],
				'ancho' => $Wc[6],
				'texto' => 'SUB-TOTAL CUOTAS',
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $fontSize_subtablas
		);	
	$PDF->linea[7] = array(
				'posx' => $Lc[7],
				'ancho' => $Wc[7],
				'texto' => $util->nf($TOTAL_ADEBITAR),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $fontSize_subtablas
		);			
	
	$PDF->linea[8] = array(
				'posx' => $Lc[8],
				'ancho' => $Wc[8],
				'texto' => $util->nf($TOTAL_SALDO_ATRASO + $TOTAL_SALDO_PERIODO),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $fontSize_subtablas
		);
		
	$PDF->linea[9] = array(
				'posx' => $Lc[9],
				'ancho' => $Wc[9],
				'texto' => $util->nf($TOTAL_ATRASO_DEBITADO + $TOTAL_PERIODO_DEBITADO),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $fontSize_subtablas
		);
	$PDF->linea[10] = array(
				'posx' => $Lc[10],
				'ancho' => $Wc[10],
				'texto' => $util->nf($TOTAL_SALDO - $TOTAL_DEBITADO),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $fontSize_subtablas
		);				
		
	$PDF->Imprimir_linea();	

	//////////////////////////////////////////////////////////////////////////////////////////////////
	//IMPRIMO LOS ADICIONALES PENDIENTES
	//////////////////////////////////////////////////////////////////////////////////////////////////
	$TOTAL_ADICIONAL = 0;
	$TOTAL_ADICIONAL_DEBITADO = 0;
	$SALDO_ADICIONAL = 0;
	if(!empty($detalle['adicionales_pendientes'])):
	
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => 190,
					'texto' => 'ADICIONALES A DEVENGAR',
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#D8DBD4',
					'size' => 7
			);
		$PDF->Imprimir_linea();		
	
		#COLUMNAS
		$fondo = "#D8DBD4";
		$style = "B";
		$PDF->linea[0] = array(
					'posx' => $La[0],
					'ancho' => $Wa[0],
					'texto' => 'ORD.DTO.',
					'borde' => '',
					'align' => 'L',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);
			
		$PDF->linea[1] = array(
					'posx' => $La[1],
					'ancho' => $Wa[1],
					'texto' => 'ORGANISMO',
					'borde' => '',
					'align' => 'L',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);
	
		$PDF->linea[2] = array(
					'posx' => $La[2],
					'ancho' => $Wa[2],
					'texto' => 'TIPO / NUMERO',
					'borde' => '',
					'align' => 'L',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);	
	
		$PDF->linea[3] = array(
					'posx' => $La[3],
					'ancho' => $Wa[3],
					'texto' => 'PERIODO',
					'borde' => '',
					'align' => 'L',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);
	
		$PDF->linea[4] = array(
					'posx' => $La[4],
					'ancho' => $Wa[4],
					'texto' => 'PROVEEDOR / PRODUCTO',
					'borde' => '',
					'align' => 'L',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);		
	
		$PDF->linea[5] = array(
					'posx' => $La[5],
					'ancho' => $Wa[5],
					'texto' => 'CONCEPTO',
					'borde' => '',
					'align' => 'L',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);
		$PDF->linea[6] = array(
					'posx' => $La[6],
					'ancho' => $Wa[6],
					'texto' => 'FORMULA',
					'borde' => '',
					'align' => 'C',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);	
	
		$PDF->linea[7] = array(
					'posx' => $La[7],
					'ancho' => $Wa[7],
					'texto' => 'A DEBITAR',
					'borde' => '',
					'align' => 'R',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);
			
		$PDF->linea[8] = array(
					'posx' => $La[8],
					'ancho' => $Wa[8],
					'texto' => 'DEBITADO',
					'borde' => '',
					'align' => 'R',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);
		$PDF->linea[9] = array(
					'posx' => $La[9],
					'ancho' => $Wa[9],
					'texto' => 'SALDO',
					'borde' => '',
					'align' => 'R',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);					
			
		$PDF->Imprimir_linea();	
		
		foreach($detalle['adicionales_pendientes'] as $adicional):
		
			$TOTAL_ADICIONAL += $adicional['MutualAdicionalPendiente']['importe'];
			$TOTAL_ADICIONAL_DEBITADO += $adicional['LiquidacionCuota']['importe_debitado'];
			$SALDO_ADICIONAL = $adicional['MutualAdicionalPendiente']['importe'] - $adicional['LiquidacionCuota']['importe_debitado'];

			$PDF->linea[0] = array(
						'posx' => $La[0],
						'ancho' => $Wa[0],
						'texto' => $adicional['MutualAdicionalPendiente']['orden_descuento_id'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#F5f7f7',
						'size' => $fontSize_subtablas
				);
				
			$PDF->linea[1] = array(
						'posx' => $La[1],
						'ancho' => $Wa[1],
						'texto' => $util->globalDato($adicional['MutualAdicionalPendiente']['codigo_organismo']),
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#F5f7f7',
						'size' => $fontSize_subtablas
				);
		
			$PDF->linea[2] = array(
						'posx' => $La[2],
						'ancho' => $Wa[2],
						'texto' => $adicional['OrdenDescuento']['tipo_orden_dto'].'# '.$adicional['OrdenDescuento']['numero'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#F5f7f7',
						'size' => $fontSize_subtablas
				);	
		
			$PDF->linea[3] = array(
						'posx' => $La[3],
						'ancho' => $Wa[3],
						'texto' => $util->periodo($adicional['LiquidacionCuota']['periodo_cuota']),
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#F5f7f7',
						'size' => $fontSize_subtablas
				);
		
			$PDF->linea[4] = array(
						'posx' => $La[4],
						'ancho' => $Wa[4],
						'texto' => $adicional['Proveedor']['razon_social_resumida'].' / '.$util->globalDato($adicional['LiquidacionCuota']['tipo_producto']),
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#F5f7f7',
						'size' => $fontSize_subtablas
				);		
		
			$PDF->linea[5] = array(
						'posx' => $La[5],
						'ancho' => $Wa[5],
						'texto' => $util->globalDato($adicional['MutualAdicionalPendiente']['tipo_cuota']),
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#F5f7f7',
						'size' => $fontSize_subtablas
				);
				
			$strFormula = '';
			
			if($adicional['MutualAdicionalPendiente']['deuda_calcula'] == 1):
			
				$strFormula = 's/DEUDA TOTAL';
			
			elseif ($adicional['MutualAdicionalPendiente']['deuda_calcula'] == 2):

				$strFormula = 's/DEUDA VENCIDA';
				
			endif;

			if($adicional['MutualAdicionalPendiente']['tipo'] == 'P'):
			
				$strFormula .= ' -> ' . '$'.$adicional['MutualAdicionalPendiente']['total_deuda'] .' x '.$adicional['MutualAdicionalPendiente']['valor'] .'% = '.$adicional['MutualAdicionalPendiente']['importe'];
			
			else:
			
			endif;
			
				
			$PDF->linea[6] = array(
						'posx' => $La[6],
						'ancho' => $Wa[6],
						'texto' => $strFormula,
						'borde' => '',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#F5f7f7',
						'size' => $fontSize_subtablas - 1
				);	
		
			$PDF->linea[7] = array(
						'posx' => $La[7],
						'ancho' => $Wa[7],
						'texto' => $util->nf($adicional['MutualAdicionalPendiente']['importe']),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#F5f7f7',
						'size' => $fontSize_subtablas
				);
				
			$PDF->linea[8] = array(
						'posx' => $La[8],
						'ancho' => $Wa[8],
						'texto' => $util->nf($adicional['LiquidacionCuota']['importe_debitado']),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#F5f7f7',
						'size' => $fontSize_subtablas
				);
			$PDF->linea[9] = array(
						'posx' => $La[9],
						'ancho' => $Wa[9],
						'texto' => $util->nf($adicional['MutualAdicionalPendiente']['importe'] - $adicional['LiquidacionCuota']['importe_debitado']),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#F5f7f7',
						'size' => $fontSize_subtablas
				);						
				
			$PDF->Imprimir_linea();			
			
		
		endforeach;
		$PDF->linea[6] = array(
					'posx' => $Lc[6],
					'ancho' => $Wc[6],
					'texto' => 'ADICIONALES',
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#F5f7f7',
					'size' => $fontSize_subtablas
			);		
		
		$PDF->linea[8] = array(
					'posx' => $Lc[8],
					'ancho' => $Wc[8],
					'texto' => $util->nf($TOTAL_ADICIONAL),
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#F5f7f7',
					'size' => $fontSize_subtablas
			);
			
		$PDF->linea[9] = array(
					'posx' => $Lc[9],
					'ancho' => $Wc[9],
					'texto' => $util->nf($TOTAL_ADICIONAL_DEBITADO),
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#F5f7f7',
					'size' => $fontSize_subtablas
			);
		$PDF->linea[10] = array(
					'posx' => $Lc[10],
					'ancho' => $Wc[10],
					'texto' => $util->nf($SALDO_ADICIONAL),
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#F5f7f7',
					'size' => $fontSize_subtablas
			);					
			
		$PDF->Imprimir_linea();			
		
	
	
	endif;
	$PDF->ln(2);
	//////////////////////////////////////////////////////////////////////////////////////////////////
	//IMPRIMO EL TOTAL DE LA LIQUIDACION
	//////////////////////////////////////////////////////////////////////////////////////////////////
	$fondo = "#FFFFFF";
	$PDF->linea[0] = array(
				'posx' => $Lc[0],
				'ancho' => 130,
				'texto' => 'TOTAL LIQUIDADO',
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => $fondo,
				'size' => $fontSize_subtablas + 1
		);
				
	$PDF->linea[7] = array(
				'posx' => $Lc[7],
				'ancho' => $Wc[7],
				'texto' => $util->nf($TOTAL_ADEBITAR + $TOTAL_ADICIONAL),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => $fondo,
				'size' => $fontSize_subtablas + 1
		);
		
	$PDF->linea[8] = array(
				'posx' => $Lc[8],
				'ancho' => $Wc[8],
				'texto' => $util->nf($TOTAL_SALDO + $TOTAL_ADICIONAL),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => $fondo,
				'size' => $fontSize_subtablas + 1
		);
		
	$PDF->linea[9] = array(
				'posx' => $Lc[9],
				'ancho' => $Wc[9],
				'texto' => $util->nf($TOTAL_DEBITADO + $TOTAL_ADICIONAL_DEBITADO),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => $fondo,
				'size' => $fontSize_subtablas + 1
		);
		
	$TOTAL_SALDO = round($TOTAL_SALDO,2);
//	$TOTAL_ADICIONAL = round($TOTAL_ADICIONAL,2);
//	$TOTAL_DEBITADO = round($TOTAL_DEBITADO,2);
//	$TOTAL_ADICIONAL_DEBITADO = round($TOTAL_ADICIONAL_DEBITADO,2);
	
		
	$PDF->linea[10] = array(
				'posx' => $Lc[10],
				'ancho' => $Wc[10],
				'texto' => $util->nf($TOTAL_SALDO + $TOTAL_ADICIONAL - $TOTAL_DEBITADO - $TOTAL_ADICIONAL_DEBITADO),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => $fondo,
				'size' => $fontSize_subtablas + 1
		);				
		
	$PDF->Imprimir_linea();	
	
	$PDF->ln(3);
	
	//////////////////////////////////////////////////////////////////////////////////////////////////
	//IMPRIMO LO ENVIADO A DESCUENTO
	//////////////////////////////////////////////////////////////////////////////////////////////////
	
	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => 190,
				'texto' => 'DETALLE DE INFORMACION A ENVIAR PARA DESCUENTO',
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => 7
		);
	$PDF->Imprimir_linea();
	
	$fondo = "#D8DBD4";
	$style = "B";
	
	$PDF->linea[0] = array(
				'posx' => $Ld[0],
				'ancho' => $Wd[0],
				'texto' => 'ORGANISMO',
				'borde' => '',
				'align' => 'L',
				'fondo' => 1,
				'style' => $style,
				'colorf' => $fondo,
				'size' => $fontSize_subtablas
		);
	$PDF->linea[1] = array(
				'posx' => $Ld[1],
				'ancho' => $Wd[1],
				'texto' => 'IDENTIFICACION',
				'borde' => '',
				'align' => 'L',
				'fondo' => 1,
				'style' => $style,
				'colorf' => $fondo,
				'size' => $fontSize_subtablas
		);
	$PDF->linea[2] = array(
				'posx' => $Ld[2],
				'ancho' => $Wd[2],
				'texto' => 'LIQUIDADO',
				'borde' => '',
				'align' => 'R',
				'fondo' => 1,
				'style' => $style,
				'colorf' => $fondo,
				'size' => $fontSize_subtablas
		);	
	$PDF->linea[3] = array(
				'posx' => $Ld[3],
				'ancho' => $Wd[3],
				'texto' => 'A DEBITAR',
				'borde' => '',
				'align' => 'R',
				'fondo' => 1,
				'style' => $style,
				'colorf' => $fondo,
				'size' => $fontSize_subtablas
		);
					
	$PDF->Imprimir_linea();

	$TOTAL_LIQUIDACION = 0;
	$TOTAL_LIQUIDACION_DEBITADO = 0;
	$SALDO = 0;
	$TOTAL_LIQUIDACION_IMPUTADO = 0;
	$TOTAL_ADEBITAR = 0;
	
	foreach($detalle['cabecera_liquidacion'] as $liquidacion):

		$TOTAL_LIQUIDACION += $liquidacion['LiquidacionSocio']['importe_dto'];
		$TOTAL_LIQUIDACION_DEBITADO += $liquidacion['LiquidacionSocio']['importe_debitado'];
		$TOTAL_LIQUIDACION_IMPUTADO += $liquidacion['LiquidacionSocio']['importe_imputado'];
		$SALDO += $liquidacion['LiquidacionSocio']['importe_dto'] - $liquidacion['LiquidacionSocio']['importe_imputado'];
	
		$TOTAL_ADEBITAR += $liquidacion['LiquidacionSocio']['importe_adebitar'];
		
		$PDF->linea[0] = array(
					'posx' => $Ld[0],
					'ancho' => $Wd[0],
					'texto' => $util->globalDato($liquidacion['LiquidacionSocio']['codigo_organismo']),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#F5f7f7',
					'size' => $fontSize_subtablas
			);
		$PDF->linea[1] = array(
					'posx' => $Ld[1],
					'ancho' => $Wd[1],
					'texto' => $liquidacion['LiquidacionSocio']['beneficio_str'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#F5f7f7',
					'size' => $fontSize_subtablas
			);
		$PDF->linea[2] = array(
					'posx' => $Ld[2],
					'ancho' => $Wd[2],
					'texto' => $util->nf($liquidacion['LiquidacionSocio']['importe_dto']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#F5f7f7',
					'size' => $fontSize_subtablas
			);	
		$PDF->linea[3] = array(
					'posx' => $Ld[3],
					'ancho' => $Wd[3],
					'texto' => $util->nf($liquidacion['LiquidacionSocio']['importe_adebitar']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#F5f7f7',
					'size' => $fontSize_subtablas
			);
		$PDF->Imprimir_linea();		
		
	
	endforeach;
	
	$PDF->ln(2);
	
	$fondo = "#FFFFFF";
	
	$PDF->linea[0] = array(
				'posx' => $Ld[0],
				'ancho' => 160,
				'texto' => 'TOTAL A ENVIAR A DESCUENTO',
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => $fondo,
				'size' => $fontSize_subtablas + 1
		);
	$PDF->linea[2] = array(
				'posx' => $Ld[2],
				'ancho' => $Wd[2],
				'texto' => $util->nf($TOTAL_LIQUIDACION),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => $fondo,
				'size' => $fontSize_subtablas + 1
		);
	$PDF->linea[3] = array(
				'posx' => $Ld[3],
				'ancho' => $Wd[3],
				'texto' => $util->nf($TOTAL_ADEBITAR),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => $fondo,
				'size' => $fontSize_subtablas + 1
		);						
		
			
		
	$PDF->Imprimir_linea();	
	
	

//endforeach; //FIN PERIODOS

	//IMPRIMO LO RECIBIDO
	//ORGANISMO  |	IDENTIFICACION  |	FECHA |	BANCO INTERCAMBIO |	CODIGO | DESCRIPCION | IMPORTE | O.COBRO
	if(!empty($detalle['rendicion'])):
	
		$PDF->ln(3);
	
		$fondo = "#D8DBD4";
		$style = "B";
		
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => 190,
					'texto' => 'DETALLE DE LA RENDICION DE DATOS POR EL AGENTE DE RETENCION',
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => $fondo,
					'size' => 7
			);
		$PDF->Imprimir_linea();
		$PDF->linea[0] = array(
					'posx' => $Ld1[0],
					'ancho' => $Wd1[0],
					'texto' => 'ORGANISMO',
					'borde' => '',
					'align' => 'L',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);
		$PDF->linea[1] = array(
					'posx' => $Ld1[1],
					'ancho' => $Wd1[1],
					'texto' => 'IDENTIFICACION',
					'borde' => '',
					'align' => 'L',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);
		$PDF->linea[2] = array(
					'posx' => $Ld1[2],
					'ancho' => $Wd1[2],
					'texto' => 'FECHA',
					'borde' => '',
					'align' => 'C',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);	
		$PDF->linea[3] = array(
					'posx' => $Ld1[3],
					'ancho' => $Wd1[3],
					'texto' => 'BANCO INTERCAMBIO',
					'borde' => '',
					'align' => 'C',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);
	
		$PDF->linea[4] = array(
					'posx' => $Ld1[4],
					'ancho' => $Wd1[4],
					'texto' => 'CODIGO',
					'borde' => '',
					'align' => 'C',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);
	
		$PDF->linea[5] = array(
					'posx' => $Ld1[5],
					'ancho' => $Wd1[5],
					'texto' => 'DESCRIPCION',
					'borde' => '',
					'align' => 'C',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);
	
		$PDF->linea[6] = array(
					'posx' => $Ld1[6],
					'ancho' => $Wd1[6],
					'texto' => 'IMPORTE',
					'borde' => '',
					'align' => 'R',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);
	
		$PDF->linea[7] = array(
					'posx' => $Ld1[7],
					'ancho' => $Wd1[7],
					'texto' => 'ORD.COBRO',
					'borde' => '',
					'align' => 'C',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);		
			
		$PDF->Imprimir_linea();	
		$TOTAL_COBRADO=0;
		$TOTAL_NOCOBRADO=0;
		
		foreach($detalle['rendicion'] as $rendicion):
		
			if($rendicion['LiquidacionSocioRendicion']['indica_pago'] == 1) $TOTAL_COBRADO += $rendicion['LiquidacionSocioRendicion']['importe_debitado'];
			else $TOTAL_NOCOBRADO += $rendicion['LiquidacionSocioRendicion']['importe_debitado'];
			
			$PDF->linea[0] = array(
						'posx' => $Ld1[0],
						'ancho' => $Wd1[0],
						'texto' => $rendicion['LiquidacionSocioRendicion']['organismo'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => $fontSize_subtablas
				);
			$PDF->linea[1] = array(
						'posx' => $Ld1[1],
						'ancho' => $Wd1[1],
						'texto' => $rendicion['LiquidacionSocioRendicion']['identificacion'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => $fontSize_subtablas
				);
			$PDF->linea[2] = array(
						'posx' => $Ld1[2],
						'ancho' => $Wd1[2],
						'texto' => $util->armaFecha($rendicion['LiquidacionSocioRendicion']['fecha_debito']),
						'borde' => '',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => $fontSize_subtablas
				);	
			$PDF->linea[3] = array(
						'posx' => $Ld1[3],
						'ancho' => $Wd1[3],
						'texto' => substr($rendicion['LiquidacionSocioRendicion']['banco_intercambio_desc'],0,33),
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => $fontSize_subtablas
				);
		
			$PDF->linea[4] = array(
						'posx' => $Ld1[4],
						'ancho' => $Wd1[4],
						'texto' => $rendicion['LiquidacionSocioRendicion']['status'],
						'borde' => '',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => $fontSize_subtablas
				);
		
			$PDF->linea[5] = array(
						'posx' => $Ld1[5],
						'ancho' => $Wd1[5],
						'texto' => substr($rendicion['LiquidacionSocioRendicion']['status_desc'],0,20),
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => $fontSize_subtablas
				);
		
			$PDF->linea[6] = array(
						'posx' => $Ld1[6],
						'ancho' => $Wd1[6],
						'texto' => $util->nf($rendicion['LiquidacionSocioRendicion']['importe_debitado']),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => $fontSize_subtablas
				);
		
			$PDF->linea[7] = array(
						'posx' => $Ld1[7],
						'ancho' => $Wd1[7],
						'texto' => ($rendicion['LiquidacionSocioRendicion']['orden_descuento_cobro_id'] != 0 ? "#".$rendicion['LiquidacionSocioRendicion']['orden_descuento_cobro_id'] : ""),
						'borde' => '',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => $fontSize_subtablas
				);		
		
			$PDF->Imprimir_linea();		
		
		endforeach;
		
		$PDF->ln(2);
		
		$fondo = "#FFFFFF";
		
		$PDF->linea[0] = array(
					'posx' => $Ld1[0],
					'ancho' => 160,
					'texto' => "RENDICION - TOTAL COBRADO",
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => $fondo,
					'size' => $fontSize_subtablas + 1
			);
		$PDF->linea[6] = array(
					'posx' => $Ld1[6],
					'ancho' => $Wd1[6],
					'texto' => $util->nf($TOTAL_COBRADO),
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => $fondo,
					'size' => $fontSize_subtablas + 1
			);
		$PDF->linea[7] = array(
					'posx' => $Ld1[7],
					'ancho' => $Wd1[7],
					'texto' => "",
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => $fondo,
					'size' => $fontSize_subtablas + 1
			);		
		$PDF->Imprimir_linea();	
	
	endif;
	
	if(!empty($detalle['reintegros'])):
	
		//#  |	LIQUIDACION  |	BENEFICIO | 	IMPORTE DEBITADO  |	IMPORTE IMPUTADO  |	IMPORTE REINTEGRO 
	
		$PDF->ln(3);
		
		
	
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => 190,
					'texto' => 'DETALLE DE REINTEGROS EMITIDOS POR ESTA LIQUIDACION',
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#D8DBD4',
					'size' => 7
			);
		$PDF->Imprimir_linea();
		
		$fondo = "#D8DBD4";
		$style = "B";
		
		$PDF->linea[0] = array(
					'posx' => $Ld2[0],
					'ancho' => $Wd2[0],
					'texto' => '#',
					'borde' => '',
					'align' => 'L',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);
		$PDF->linea[1] = array(
					'posx' => $Ld2[1],
					'ancho' => $Wd2[1],
					'texto' => 'LIQUIDACION',
					'borde' => '',
					'align' => 'L',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);
		$PDF->linea[2] = array(
					'posx' => $Ld2[2],
					'ancho' => $Wd2[2],
					'texto' => 'BENEFICIO',
					'borde' => '',
					'align' => 'L',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);	
		$PDF->linea[3] = array(
					'posx' => $Ld2[3],
					'ancho' => $Wd2[3],
					'texto' => 'DEBITADO',
					'borde' => '',
					'align' => 'C',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);
	
		$PDF->linea[4] = array(
					'posx' => $Ld2[4],
					'ancho' => $Wd2[4],
					'texto' => 'IMPUTADO',
					'borde' => '',
					'align' => 'C',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);
	
		$PDF->linea[5] = array(
					'posx' => $Ld2[5],
					'ancho' => $Wd2[5],
					'texto' => 'REINTEGRO',
					'borde' => '',
					'align' => 'C',
					'fondo' => 1,
					'style' => $style,
					'colorf' => $fondo,
					'size' => $fontSize_subtablas
			);
	
		$PDF->Imprimir_linea();	
		
		$ACU_DEBITADO=0;
		$ACU_IMPUTADO=0;
		$ACU_REINTEGRO=0;
		
		foreach($detalle['reintegros'] as $reintegro):
	
			$ACU_DEBITADO += $reintegro['SocioReintegro']['importe_debitado'];
			$ACU_IMPUTADO += $reintegro['SocioReintegro']['importe_imputado'];
			$ACU_REINTEGRO += $reintegro['SocioReintegro']['importe_reintegro'];	
	
			$PDF->linea[0] = array(
						'posx' => $Ld2[0],
						'ancho' => $Wd2[0],
						'texto' => $reintegro['SocioReintegro']['id'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => $fontSize_subtablas
				);
			$PDF->linea[1] = array(
						'posx' => $Ld2[1],
						'ancho' => $Wd2[1],
						'texto' => $reintegro['SocioReintegro']['liquidacion_str'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => $fontSize_subtablas
				);
			$PDF->linea[2] = array(
						'posx' => $Ld2[2],
						'ancho' => $Wd2[2],
						'texto' => "",
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => $fontSize_subtablas
				);	
			$PDF->linea[3] = array(
						'posx' => $Ld2[3],
						'ancho' => $Wd2[3],
						'texto' => $util->nf($reintegro['SocioReintegro']['importe_debitado']),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => $fontSize_subtablas
				);
		
			$PDF->linea[4] = array(
						'posx' => $Ld2[4],
						'ancho' => $Wd2[4],
						'texto' => $util->nf($reintegro['SocioReintegro']['importe_imputado']),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => $fontSize_subtablas
				);
		
			$PDF->linea[5] = array(
						'posx' => $Ld2[5],
						'ancho' => $Wd2[5],
						'texto' => $util->nf($reintegro['SocioReintegro']['importe_reintegro']),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => $fontSize_subtablas
				);
		
			$PDF->Imprimir_linea();	
		
		endforeach;
		
		$PDF->ln(2);
	
		$fondo = "#FFFFFF";
		
		$PDF->linea[0] = array(
					'posx' => $Ld2[0],
					'ancho' => 145,
					'texto' => 'TOTAL REINTEGROS ' . $util->periodo($periodo,true),
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => $fondo,
					'size' => $fontSize_subtablas + 1
			);
		$PDF->linea[3] = array(
					'posx' => $Ld2[3],
					'ancho' => $Wd2[3],
					'texto' => $util->nf($ACU_DEBITADO),
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => $fondo,
					'size' => $fontSize_subtablas + 1
			);
		$PDF->linea[4] = array(
					'posx' => $Ld2[4],
					'ancho' => $Wd2[4],
					'texto' => $util->nf($ACU_IMPUTADO),
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => $fondo,
					'size' => $fontSize_subtablas + 1
			);
		$PDF->linea[5] = array(
					'posx' => $Ld2[5],
					'ancho' => $Wd2[5],
					'texto' => $util->nf($ACU_REINTEGRO),
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => $fondo,
					'size' => $fontSize_subtablas + 1
			);
			
		$PDF->Imprimir_linea();					
	
	endif;

$PDF->ln(5);

endforeach; //FIN PERIODOS

$PDF->linea[0] = array(
			'posx' => $Ld2[0],
			'ancho' => $Wd2[0],
			'texto' => '*** S.E.U.O. (SALVO ERROR U OMISION) ***',
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => 6
	);
	
$PDF->Imprimir_linea();		

$PDF->Output("liquidacion_deuda_$periodo.pdf");

?>