<?php 

//debug($solicitud);
//exit;


App::import('Vendor','xtcpdf');

$PDF = new XTCPDF();
$PDF->SetTitle("CARATULA EXPEDIENTE");
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

####################################################################################################################
#	SETEO TITULOS
####################################################################################################################

$PDF->titulo['titulo1'] = 'FECHA DE EMISION: ' . $util->armaFecha($solicitud['Solicitud']['fecha_solicitud']);
$PDF->titulo['titulo2'] = 'PRODUCTOR: ' . $solicitud['Productor']['nombre_corto'];
$PDF->titulo['titulo3'] = 'NRO. SOLICITUD: ' . $solicitud['Solicitud']['nro_solicitud'];
$PDF->titulo['titulo4'] =  ($solicitud['Solicitud']['nro_credito_proveedor'] != '' ? 'NRO. CREDITO PROVEEDOR: ' . $solicitud['Solicitud']['nro_credito_proveedor'] : '');

$PDF->AddPage();

$PDF->ln(10);

####################################################################################################################
#	IMPRIMO EL NRO DE EXPEDIENTE
####################################################################################################################
$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => "EXPEDIENTE #" . $solicitud['Solicitud']['nro_solicitud'],
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 20
	);
$PDF->Imprimir_linea();	


####################################################################################################################
#	IMPRIMO DATOS DEL SOCIO
####################################################################################################################
$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => "SOCIO #" . $solicitud['PersonaV2']['Socio']['id'],
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 14
	);
$PDF->Imprimir_linea();
	
####################################################################################################################
#	IMPRIMO LOS DATOS DE LA PERSONA
####################################################################################################################
$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => $solicitud['PersonaV2']['Persona']['apellido'].', '.$solicitud['PersonaV2']['Persona']['nombre'] . ' - ' . $solicitud['PersonaV2']['Persona']['tipo_documento_desc'] .' '. $solicitud['PersonaV2']['Persona']['documento'],
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 10
	);
$PDF->Imprimir_linea();

####################################################################################################################
#	IMPRIMO EL BENEFICIO
####################################################################################################################
if(isset($solicitud['BeneficioV2']['PersonaBeneficio']['string'])):
	$PDF->linea[0] = array(
				'posx' => 10,
				'ancho' => 190,
				'texto' => $solicitud['BeneficioV2']['PersonaBeneficio']['string'],
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => 6
		);
	$PDF->Imprimir_linea();
else:
	$PDF->linea[0] = array(
				'posx' => 10,
				'ancho' => 190,
				'texto' => $solicitud['OrdenDescuento'][0]['OrdenDescuento']['beneficio_str'],
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => 6
		);
	$PDF->Imprimir_linea();
endif;

####################################################################################################################
#	IMPRIMO INICIO Y PRIMER VTO
####################################################################################################################
$inicio = Set::extract('/OrdenDescuento[tipo_producto=MUTUPROD0001]',$solicitud['OrdenDescuento']);
if(empty($inicio)) $inicio = Set::extract('/OrdenDescuento[tipo_producto=MUTUPROD0011]',$solicitud['OrdenDescuento']);
if(isset($inicio[0]['OrdenDescuento']['periodo_ini'])):
	$PDF->linea[0] = array(
				'posx' => 10,
				'ancho' => 190,
				'texto' => 'INICIA ' . $util->periodo($inicio[0]['OrdenDescuento']['periodo_ini'],true) . ' - VTO 1ra CUOTA EL ' . $util->armaFecha($inicio[0]['OrdenDescuento']['primer_vto_socio']),
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => 10
		);
	$PDF->Imprimir_linea();
endif;

####################################################################################################################
#	IMPRIMO EL PRODUCTO
####################################################################################################################
$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => "PRODUCTO",
			'borde' => 'B',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 12
	);
$PDF->Imprimir_linea();

$PDF->ln(3);

$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 180,
			'texto' => $solicitud['Solicitud']['proveedor_producto'],
			'borde' => 'LTRB',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 10
	);
$PDF->Imprimir_linea();

$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 40,
			'texto' => 'CAPITAL: $ ' . $util->nf($solicitud['Solicitud']['solicitado']) ,
			'borde' => 'LRB',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 8
	);
$PDF->linea[1] = array(
			'posx' => 50,
			'ancho' => 140,
			'texto' => 'SON PESOS ' . $util->num2letras($solicitud['Solicitud']['solicitado']) ,
			'borde' => 'LRB',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 8
	);	
$PDF->Imprimir_linea();

$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 60,
			'texto' => 'SOLICITADO: $ ' . $util->nf($solicitud['Solicitud']['en_mano']) ,
			'borde' => 'LRB',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 8
	);
$PDF->linea[1] = array(
			'posx' => 70,
			'ancho' => 60,
			'texto' => 'CANCELACIONES: $ ' . $util->nf($solicitud['Solicitud']['total_cancelado']) ,
			'borde' => 'LRB',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 8
	);
$PDF->linea[2] = array(
			'posx' => 130,
			'ancho' => 60,
			'texto' => 'NETO A COBRAR: $ ' . $util->nf($solicitud['Solicitud']['monto_a_percibir']) ,
			'borde' => 'LRB',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 8
	);			
	
$PDF->Imprimir_linea();


$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 60,
			'texto' => 'Cantidad de Cuotas: ' . $util->nf($solicitud['Solicitud']['cuotas'],0) ,
			'borde' => 'LRB',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 8
	);
$PDF->linea[1] = array(
			'posx' => 70,
			'ancho' => 60,
			'texto' => 'Cuota Pura: $ ' . $util->nf($solicitud['Solicitud']['monto_cuota']) ,
			'borde' => 'LRB',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 8
	);
$PDF->linea[2] = array(
			'posx' => 130,
			'ancho' => 60,
			'texto' => 'Monto Total: $ ' . $util->nf($solicitud['Solicitud']['total_cuota_pura']) ,
			'borde' => 'LRB',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 8
	);			
	
$PDF->Imprimir_linea();



$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 60,
			'texto' => '+ Seguro:'.$util->nf($solicitud['Solicitud']['monto_seguro']).' + Cuota Social:' .  $util->nf($solicitud['Solicitud']['monto_cuota_social']),
			'borde' => 'LRB',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 8
	);
$PDF->linea[1] = array(
			'posx' => 70,
			'ancho' => 60,
			'texto' => 'CUOTA TOTAL: $ ' . $util->nf($solicitud['Solicitud']['cuota_total']) ,
			'borde' => 'LRB',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 8
	);
$PDF->linea[2] = array(
			'posx' => 130,
			'ancho' => 60,
			'texto' => 'MONTO FINAL: $ ' . $util->nf($solicitud['Solicitud']['total_credito']) ,
			'borde' => 'LRB',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 8
	);			
	
$PDF->Imprimir_linea();

####################################################################################################################
#	IMPRIMO EL DETALLE DE LA LIQUIDACION
####################################################################################################################
$PDF->ln(5);
$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => "LIQUIDACION",
			'borde' => 'B',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 12
	);
$PDF->Imprimir_linea();

$PDF->ln(3);

$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 40,
			'texto' => 'FORMA DE PAGO' ,
			'borde' => 'LRBT',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 8
	);
$PDF->linea[1] = array(
			'posx' => 50,
			'ancho' => 130,
			'texto' => $solicitud['Solicitud']['forma_pago'],
			'borde' => 'LRBT',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 8
	);
$PDF->Imprimir_linea();

$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 40,
			'texto' => 'BANCO' ,
			'borde' => 'LRBT',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 8
	);
$PDF->linea[1] = array(
			'posx' => 50,
			'ancho' => 130,
			'texto' => ($solicitud['Solicitud']['codigo_fpago'] == '0003' ? $solicitud['Solicitud']['dato_giro'] : $solicitud['Solicitud']['banco']),
			'borde' => 'LRBT',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 8
	);
$PDF->Imprimir_linea();


$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 40,
			'texto' => 'NRO OPERACION' ,
			'borde' => 'LRBT',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 8
	);
$PDF->linea[1] = array(
			'posx' => 50,
			'ancho' => 130,
			'texto' => $solicitud['Solicitud']['nro_operacion_pago'],
			'borde' => 'LRBT',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 8
	);
$PDF->Imprimir_linea();


$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 40,
			'texto' => 'FECHA' ,
			'borde' => 'LRBT',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 8
	);
$PDF->linea[1] = array(
			'posx' => 50,
			'ancho' => 130,
			'texto' => $util->armaFecha($solicitud['Solicitud']['fecha_operacion_pago']),
			'borde' => 'LRBT',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 8
	);
$PDF->Imprimir_linea();


####################################################################################################################
#	IMPRIMO CANCELACIONES
####################################################################################################################
if(count($solicitud['Cancelaciones']) != 0):

	$PDF->ln(5);

	$PDF->linea[0] = array(
				'posx' => 10,
				'ancho' => 190,
				'texto' => "CANCELACIONES",
				'borde' => 'B',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => 12
		);
	$PDF->Imprimir_linea();
	
	$PDF->ln(3);
	
	foreach($solicitud['Cancelaciones'] as $cancelacion):
	
		$PDF->linea[0] = array(
				'posx' => 10,
				'ancho' => 120,
				'texto' => '#'.$cancelacion['SolicitudCancelaciones']['id_cancelacion'] .' - ' . up($cancelacion['SolicitudCancelaciones']['beneficiario']),
				'borde' => 'LRBT',
				'align' => 'L',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => 8
		);
	
		$PDF->Imprimir_linea();
		$PDF->linea[0] = array(
				'posx' => 10,
				'ancho' => 40,
				'texto' => 'IMPORTE: $' . $util->nf($cancelacion['SolicitudCancelaciones']['importe_deuda_cancela']),
				'borde' => 'LRBT',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => 8
		);
		$PDF->linea[1] = array(
				'posx' => 50,
				'ancho' => 80,
				'texto' => 'EN CONCEPTO DE: ' . $cancelacion['SolicitudCancelaciones']['concepto'],
				'borde' => 'LRBT',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => 8
		);	
		$PDF->Imprimir_linea();		
		
		//PROCESO EL DETALLE DE LA CANCELACION
		if(count($cancelacion['SolicitudCancelacionDetalle']) != 0):
		
			$GDC = array(
							array('x' => 10, 'w' => 15),//tipoOpe
							array('x' => 25, 'w' => 25),//proveedor
							array('x' => 50, 'w' => 15),//nroOcomp
							array('x' => 65, 'w' => 15),//cuotas
							array('x' => 80, 'w' => 15),//impocuota
							array('x' => 95, 'w' => 15),//fechaPago
							array('x' => 110, 'w' => 30),//detallePago
							array('x' => 140, 'w' => 40),//obs
							array('x' => 180, 'w' => 10),//pendiente
			);
		
			$PDF->ln(3);
			$PDF->linea[0] = array(
					'posx' => $GDC[0]['x'],
					'ancho' => $GDC[0]['w'],
					'texto' => 'Operación',
					'borde' => 'LRBT',
					'align' => 'C',
					'fondo' => 1,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => 8
			);
			$PDF->linea[1] = array(
					'posx' => $GDC[1]['x'],
					'ancho' => $GDC[1]['w'],
					'texto' => 'Proveedor',
					'borde' => 'LRBT',
					'align' => 'C',
					'fondo' => 1,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => 8
			);
			$PDF->linea[2] = array(
					'posx' => $GDC[2]['x'],
					'ancho' => $GDC[2]['w'],
					'texto' => 'O.Compra',
					'borde' => 'LRBT',
					'align' => 'C',
					'fondo' => 1,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => 8
			);			
			$PDF->linea[3] = array(
					'posx' => $GDC[3]['x'],
					'ancho' => $GDC[3]['w'],
					'texto' => 'Cuotas',
					'borde' => 'LRBT',
					'align' => 'C',
					'fondo' => 1,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => 8
			);
			$PDF->linea[4] = array(
					'posx' => $GDC[4]['x'],
					'ancho' => $GDC[4]['w'],
					'texto' => 'Imp.Cuota',
					'borde' => 'LRBT',
					'align' => 'C',
					'fondo' => 1,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => 8
			);
			$PDF->linea[5] = array(
					'posx' => $GDC[5]['x'],
					'ancho' => $GDC[5]['w'],		
					'texto' => 'Fecha',
					'borde' => 'LRBT',
					'align' => 'C',
					'fondo' => 1,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => 8
			);
			$PDF->linea[6] = array(
					'posx' => $GDC[6]['x'],
					'ancho' => $GDC[6]['w'],
					'texto' => 'Detalle',
					'borde' => 'LRBT',
					'align' => 'C',
					'fondo' => 1,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => 8
			);	
			$PDF->linea[7] = array(
					'posx' => $GDC[7]['x'],
					'ancho' => $GDC[7]['w'],
					'texto' => 'Observaciones',
					'borde' => 'LRBT',
					'align' => 'C',
					'fondo' => 1,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => 8
			);
			$PDF->linea[8] = array(
					'posx' => $GDC[8]['x'],
					'ancho' => $GDC[8]['w'],
					'texto' => 'Pend.',
					'borde' => 'LRBT',
					'align' => 'C',
					'fondo' => 1,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => 8
			);																				
			$PDF->Imprimir_linea();
		
			foreach($cancelacion['SolicitudCancelacionDetalle'] as $detalle):
			
			
				$PDF->linea[0] = array(
						'posx' => $GDC[0]['x'],
						'ancho' => $GDC[0]['w'],
						'texto' => $detalle['SolicitudCancelacionDetalle']['tipo_liquidacion_desc'],
						'borde' => '',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => 6
				);
				$PDF->linea[1] = array(
						'posx' => $GDC[1]['x'],
						'ancho' => $GDC[1]['w'],
						'texto' => $detalle['SolicitudCancelacionDetalle']['proveedor'],
						'borde' => '',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => 6
				);
				$PDF->linea[2] = array(
						'posx' => $GDC[2]['x'],
						'ancho' => $GDC[2]['w'],
						'texto' => $detalle['SolicitudCancelacionDetalle']['nro_orden_compra'],
						'borde' => '',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => 6
				);
				$PDF->linea[3] = array(
						'posx' => $GDC[3]['x'],
						'ancho' => $GDC[3]['w'],
						'texto' => $detalle['SolicitudCancelacionDetalle']['cuotas'],
						'borde' => '',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => 6
				);
				$PDF->linea[4] = array(
						'posx' => $GDC[4]['x'],
						'ancho' => $GDC[4]['w'],
						'texto' => $util->nf($detalle['SolicitudCancelacionDetalle']['importe_cuota']),
						'borde' => '',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => 6
				);
				$PDF->linea[5] = array(
						'posx' => $GDC[5]['x'],
						'ancho' => $GDC[5]['w'],
						'texto' => $util->armaFecha($detalle['SolicitudCancelacionDetalle']['fecha_operacion_pago']),
						'borde' => '',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => 6
				);
				$PDF->linea[6] = array(
						'posx' => $GDC[6]['x'],
						'ancho' => $GDC[6]['w'],
						'texto' => $detalle['SolicitudCancelacionDetalle']['detalle_ope_ban'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => 6
				);
				$PDF->linea[7] = array(
						'posx' => $GDC[7]['x'],
						'ancho' => $GDC[7]['w'],
						'texto' => $detalle['SolicitudCancelacionDetalle']['observaciones'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => 6
				);
				$PDF->linea[8] = array(
						'posx' => $GDC[8]['x'],
						'ancho' => $GDC[8]['w'],
						'texto' => ($detalle['SolicitudCancelacionDetalle']['pendiente'] == 1 ? 'SI' : 'NO'),
						'borde' => '',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#D8DBD4',
						'size' => 6
				);																																					
				$PDF->Imprimir_linea();
			
			endforeach;
		
		endif;
			
	endforeach;
	
elseif (count($solicitud['SolicitudCancelacionOrden']) != 0):

	$PDF->ln(5);

	$PDF->linea[0] = array(
				'posx' => 10,
				'ancho' => 190,
				'texto' => "ORDENES DE CANCELACION",
				'borde' => 'B',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => 12
	);
	
	$PDF->Imprimir_linea();
	
	$PDF->ln(3);
	
endif;

####################################################################################################################
#	IMPRIMO LAS ORDENES DE DESCUENTO
####################################################################################################################
$PDF->ln(5);
$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => "ORDENES DE DESCUENTO EMITIDAS",
			'borde' => 'B',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 12
	);
$PDF->Imprimir_linea();

$PDF->ln(3);

if(count($solicitud['OrdenDescuento']) != 0):

	$GOC = array(
					array('x' => 10, 'w' => 10),//OrdenDto
					array('x' => 20, 'w' => 15),//inicia
					array('x' => 35, 'w' => 15),//1vto
					array('x' => 50, 'w' => 25),//TIPO / NUMERO
					array('x' => 75, 'w' => 70),//PROVEEDOR - PRODUCTO
					array('x' => 145, 'w' => 20),//devengado
					array('x' => 165, 'w' => 15),//devengado
					array('x' => 180, 'w' => 20),//devengado
	);
	$PDF->linea[0] = array(
			'posx' => $GOC[0]['x'],
			'ancho' => $GOC[0]['w'],
			'texto' => 'ORDEN',
			'borde' => 'LRBT',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
	$PDF->linea[1] = array(
			'posx' => $GOC[1]['x'],
			'ancho' => $GOC[1]['w'],
			'texto' => 'INICIA',
			'borde' => 'LRBT',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
	$PDF->linea[2] = array(
			'posx' => $GOC[2]['x'],
			'ancho' => $GOC[2]['w'],
			'texto' => '1er VTO',
			'borde' => 'LRBT',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
	$PDF->linea[3] = array(
			'posx' => $GOC[3]['x'],
			'ancho' => $GOC[3]['w'],
			'texto' => 'TIPO - NUMERO',
			'borde' => 'LRBT',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
	$PDF->linea[4] = array(
			'posx' => $GOC[4]['x'],
			'ancho' => $GOC[4]['w'],
			'texto' => 'PROVEEDOR - PRODUCTO',
			'borde' => 'LRBT',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
	$PDF->linea[5] = array(
			'posx' => $GOC[5]['x'],
			'ancho' => $GOC[5]['w'],
			'texto' => 'TOTAL',
			'borde' => 'BT',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
	$PDF->linea[6] = array(
			'posx' => $GOC[6]['x'],
			'ancho' => $GOC[6]['w'],
			'texto' => 'CUOTAS',
			'borde' => 'BT',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
	$PDF->linea[7] = array(
			'posx' => $GOC[7]['x'],
			'ancho' => $GOC[7]['w'],
			'texto' => 'IMPORTE',
			'borde' => 'RBT',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 7
	);							
	$PDF->Imprimir_linea();	
	$ACU = 0;
	$ACU1 = 0;
	foreach($solicitud['OrdenDescuento'] as $od){
		$PDF->linea[0] = array(
				'posx' => $GOC[0]['x'],
				'ancho' => $GOC[0]['w'],
				'texto' => $od['OrdenDescuento']['id'],
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => 7
		);
		$PDF->linea[1] = array(
				'posx' => $GOC[1]['x'],
				'ancho' => $GOC[1]['w'],
				'texto' => $od['OrdenDescuento']['inicia_en'],
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => 7
		);
		$PDF->linea[2] = array(
				'posx' => $GOC[2]['x'],
				'ancho' => $GOC[2]['w'],
				'texto' => $util->armaFecha($od['OrdenDescuento']['primer_vto_socio']),
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => 7
		);
		$PDF->linea[3] = array(
				'posx' => $GOC[3]['x'],
				'ancho' => $GOC[3]['w'],
				'texto' => $od['OrdenDescuento']['tipo_nro'],
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => 7
		);
		$PDF->linea[4] = array(
				'posx' => $GOC[4]['x'],
				'ancho' => $GOC[4]['w'],
				'texto' => $od['OrdenDescuento']['proveedor_producto'],
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => 7
		);
		$PDF->linea[5] = array(
				'posx' => $GOC[5]['x'],
				'ancho' => $GOC[5]['w'],
				'texto' => $util->nf($od['OrdenDescuento']['importe_devengado']),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => 7
		);

		$PDF->linea[6] = array(
				'posx' => $GOC[6]['x'],
				'ancho' => $GOC[6]['w'],
				'texto' => $od['OrdenDescuento']['cuotas'],
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => 7
		);
		
		$PDF->linea[7] = array(
				'posx' => $GOC[7]['x'],
				'ancho' => $GOC[7]['w'],
				'texto' => $util->nf($od['OrdenDescuento']['importe_cuota']),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => 7
		);		
				
		$ACU += $od['OrdenDescuento']['importe_devengado'];	
		$ACU1 += $od['OrdenDescuento']['importe_cuota'];										
		$PDF->Imprimir_linea();		
		
	}
	$PDF->linea[5] = array(
			'posx' => $GOC[5]['x'],
			'ancho' => $GOC[5]['w'],
			'texto' => $util->nf($ACU),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
	$PDF->linea[6] = array(
			'posx' => $GOC[6]['x'],
			'ancho' => $GOC[6]['w'],
			'texto' => '',
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);	
	$PDF->linea[7] = array(
			'posx' => $GOC[7]['x'],
			'ancho' => $GOC[7]['w'],
			'texto' => $util->nf($ACU1),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);	
	$PDF->Imprimir_linea();	

endif;


####################################################################################################################
#	IMPRIMO LA REASIGNACION
####################################################################################################################
if($solicitud['Solicitud']['reasignar_proveedor_id'] != 0):

$PDF->ln(5);
$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => "*** REASIGNADA AL PROVEEDOR ".$solicitud['Solicitud']['reasignar_proveedor_razon_social']." EL " . date("d-m-Y", strtotime($solicitud['Solicitud']['reasigna_proveedor_fecha'])). " POR EL USUARIO: " . $solicitud['Solicitud']['reasigna_proveedor_user']. " ***",
			'borde' => 'TBLR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 8
	);
$PDF->Imprimir_linea();

$PDF->ln(3);

endif;


$PDF->Output("CARATULA_EXPEDIENTE_".$nro_solicitud.".pdf");


?>
