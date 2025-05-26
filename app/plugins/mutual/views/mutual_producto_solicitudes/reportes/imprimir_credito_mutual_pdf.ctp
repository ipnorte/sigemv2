<?php
// debug($orden);
// exit;

App::import('Vendor','solicitud_credito_general_pdf');

$PDF = new SolicitudCreditoGeneralPDF();

$PDF->SetTitle("SOLICITUD DE CREDITO #".$id);
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

$membrete = array(
		'L1' => $orden['MutualProductoSolicitud']['proveedor_full_name'],
		'L2' => $orden['MutualProductoSolicitud']['proveedor_domicilio'],
		'L3' => $orden['MutualProductoSolicitud']['proveedor_localidad'] ." ".$orden['MutualProductoSolicitud']['proveedor_telefono']
);

$PDF->AddPage();
/////////////////////////////////////////////////////////////////////////////////////////////////////////
// TALON DE CONTROL
///////////////////////////////////////////////////////////////////////////////////////////////////////
if($orden['MutualProductoSolicitud']['aprobada'] == 1 && $orden['MutualProductoSolicitud']['orden_descuento_id'] != 0 && $imprime_talon_control):
	$PDF->PIE = false;
	$Y = $PDF->GetY();
	$PDF->SetY(-55);
	$size = 8;
	$PDF->linea[1] = array(
			'posx' => 10,
			'ancho' => 100,
			'texto' => utf8_decode("*** VALIDACION DE CARGA EN SISTEMA DE COBRANZA ***"),
			'borde' => 'LT',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$PDF->linea[2] = array(
			'posx' => 110,
			'ancho' => 65,
			'texto' => utf8_decode("Solicitud de Préstamo N°"),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$PDF->linea[3] = array(
			'posx' => 175,
			'ancho' => 25,
			'texto' => $orden['MutualProductoSolicitud']['nro_print'],
			'borde' => 'TR',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$PDF->Imprimir_linea();

	$PDF->linea[1] = array(
			'posx' => 10,
			'ancho' => 30,
			'texto' => utf8_decode("Fecha de proceso:"),
			'borde' => 'L',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$PDF->linea[2] = array(
			'posx' => 40,
			'ancho' => 20,
			'texto' => $util->armaFecha($orden['MutualProductoSolicitud']['aprobada_el']),
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$PDF->linea[3] = array(
			'posx' => 60,
			'ancho' => 20,
			'texto' => utf8_decode("Organismo:"),
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$PDF->linea[4] = array(
			'posx' => 80,
			'ancho' => 40,
			'texto' => $orden['MutualProductoSolicitud']['organismo_desc'],
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$PDF->linea[5] = array(
			'posx' => 120,
			'ancho' => 15,
			'texto' => utf8_decode("N° CBU:"),
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$PDF->linea[6] = array(
			'posx' => 135,
			'ancho' => 65,
			'texto' => $orden['MutualProductoSolicitud']['beneficio_cbu'],
			'borde' => 'R',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$PDF->Imprimir_linea();

	$PDF->linea[1] = array(
			'posx' => 10,
			'ancho' => 50,
			'texto' => utf8_decode("Mes de Inicio de Descuento:"),
			'borde' => 'L',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$PDF->linea[2] = array(
			'posx' => 60,
			'ancho' => 20,
			'texto' => $orden['MutualProductoSolicitud']['inicia_en'],
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$PDF->linea[3] = array(
			'posx' => 80,
			'ancho' => 25,
			'texto' => "Aprobada por:",
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$PDF->linea[4] = array(
			'posx' => 105,
			'ancho' => 95,
			'texto' => $orden['MutualProductoSolicitud']['aprobada_por'] . " " . $orden['MutualProductoSolicitud']['modified'],
			'borde' => 'R',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$PDF->Imprimir_linea();

	$PDF->linea[1] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => utf8_decode("ORDEN DE DEBITO EMITIDA"),
			'borde' => 'LR',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$PDF->Imprimir_linea();

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
			'texto' => 'NRO',
			'borde' => 'LRBT',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
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
			'style' => 'B',
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
			'style' => 'B',
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
			'style' => 'B',
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
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
	$PDF->linea[5] = array(
			'posx' => $GOC[5]['x'],
			'ancho' => $GOC[5]['w'],
			'texto' => 'TOTAL',
			'borde' => 'LRBT',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
	$PDF->linea[6] = array(
			'posx' => $GOC[6]['x'],
			'ancho' => $GOC[6]['w'],
			'texto' => 'CUOTAS',
			'borde' => 'LRBT',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
	$PDF->linea[7] = array(
			'posx' => $GOC[7]['x'],
			'ancho' => $GOC[7]['w'],
			'texto' => 'IMPORTE',
			'borde' => 'LRBT',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
	$PDF->Imprimir_linea();


	$PDF->linea[0] = array(
			'posx' => $GOC[0]['x'],
			'ancho' => $GOC[0]['w'],
			'texto' => $orden['MutualProductoSolicitud']['OrdenDescuento']['id'],
			'borde' => 'LBT',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
	$PDF->linea[1] = array(
			'posx' => $GOC[1]['x'],
			'ancho' => $GOC[1]['w'],
			'texto' => $orden['MutualProductoSolicitud']['OrdenDescuento']['inicia_en'],
			'borde' => 'BT',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
	$PDF->linea[2] = array(
			'posx' => $GOC[2]['x'],
			'ancho' => $GOC[2]['w'],
			'texto' => $util->armaFecha($orden['MutualProductoSolicitud']['OrdenDescuento']['primer_vto_socio']),
			'borde' => 'BT',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
	$PDF->linea[3] = array(
			'posx' => $GOC[3]['x'],
			'ancho' => $GOC[3]['w'],
			'texto' => $orden['MutualProductoSolicitud']['OrdenDescuento']['tipo_nro'],
			'borde' => 'BT',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
	$PDF->linea[4] = array(
			'posx' => $GOC[4]['x'],
			'ancho' => $GOC[4]['w'],
			'texto' => $orden['MutualProductoSolicitud']['OrdenDescuento']['proveedor_producto'],
			'borde' => 'BT',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);
	$PDF->linea[5] = array(
			'posx' => $GOC[5]['x'],
			'ancho' => $GOC[5]['w'],
			'texto' => $util->nf($orden['MutualProductoSolicitud']['OrdenDescuento']['importe_total']),
			'borde' => 'BT',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);

	$PDF->linea[6] = array(
			'posx' => $GOC[6]['x'],
			'ancho' => $GOC[6]['w'],
			'texto' => $orden['MutualProductoSolicitud']['OrdenDescuento']['cuotas'],
			'borde' => 'BT',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);

	$PDF->linea[7] = array(
			'posx' => $GOC[7]['x'],
			'ancho' => $GOC[7]['w'],
			'texto' => $util->nf($orden['MutualProductoSolicitud']['OrdenDescuento']['importe_cuota']),
			'borde' => 'BRT',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 7
	);

	$PDF->Imprimir_linea();
	$PDF->Output("solicitud_credito_".$orden['MutualProductoSolicitud']['id']."_CONTROL.pdf");
	exit;
endif;
/////////////////////////////////////////////////////////////////////////////////////////////////////////
// FIN TALON DE CONTROL
///////////////////////////////////////////////////////////////////////////////////////////////////////



$PDF->SetY(10);


$PDF->SetFont('courier','',12);


$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',14);
$PDF->Cell(0,5,$membrete['L1'],0);
$PDF->Ln(5);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',8);
$PDF->Cell(0,5,$membrete['L2'],0);
$PDF->Ln(3);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',8);
$PDF->Cell(0,5,$membrete['L3'],0);
$PDF->Ln(4);

$PDF->SetY(10);
$nroSolicitud = $orden['MutualProductoSolicitud']['nro_print'];
$fecha = $util->armaFecha($orden['MutualProductoSolicitud']['fecha']);
$usuario = $orden['MutualProductoSolicitud']['user_created'];
$vendedor = (isset($orden['MutualProductoSolicitud']['vendedor_nombre_min']) ? $orden['MutualProductoSolicitud']['vendedor_nombre_min'] : "");
$PDF->imprimirDatosGenerales($nroSolicitud,$fecha,$usuario,$vendedor);
$size = 16;
$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 190,
		'texto' => "SOLICITUD DE PRESTAMO EN PESOS",
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->Imprimir_linea();
// $PDF->ln(8);
$PDF->SetFontSize(10);
$PDF->MultiCell(0,11,"Por la presente, solicito a Uds. Un préstamo en pesos, de acuerdo a las siguientes condiciones:\n");

$PDF->SetY(50);

$size = 10;
$sized = 13;



#############################################################################################
# DATOS PERSONALES
#############################################################################################
$PDF->imprimirDatosTitular($orden);
#############################################################################################
# PRODUCTO
#############################################################################################
$PDF->ln(4);
$PDF->imprimir_producto_solicitado($orden);

#############################################################################################
# LIQUIDACION
#############################################################################################
$PDF->ln(4);
$PDF->imprimir_liquidacion($orden);

#############################################################################################
# CUENTA PARA DEBITO
#############################################################################################
$PDF->imprimirDatosCuentaDebito($orden);


#############################################################################################
# FIRMAS
#############################################################################################

$PDF->ln(25);

$PDF->firmaSocio();
$PDF->ln(4);
$PDF->barCode($orden['MutualProductoSolicitud']['barcode']);

#############################################################################################
# AUTORIZACION COBRO POR DEBITO DIRECTO
#############################################################################################

$membrete = array(
		'L1' => Configure::read('APLICACION.nombre_fantasia'),
		'L2' => Configure::read('APLICACION.domi_fiscal'),
		'L3' => "TEL: " . Configure::read('APLICACION.telefonos') ." - email: ".Configure::read('APLICACION.email')
);

$PDF->AddPage();
$PDF->reset();

$PDF->SetFont('courier','',12);


$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',14);
$PDF->Cell(0,5,$membrete['L1'],0);
$PDF->Ln(5);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',8);
$PDF->Cell(0,5,$membrete['L2'],0);
$PDF->Ln(3);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',8);
$PDF->Cell(0,5,$membrete['L3'],0);
$PDF->Ln(4);

$PDF->SetY(10);
$PDF->imprimirDatosGenerales($nroSolicitud,$fecha,$usuario,$vendedor);
$size = 16;
$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 190,
		'texto' => "AUTORIZACION DE COBRANZA POR DEBITO DIRECTO",
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->Imprimir_linea();


$PDF->SetFontSize(10);
$TEXTO = "Por la presente, AUTORIZO a ".Configure::read('APLICACION.nombre_fantasia').", a debitar de mi Cuenta Corriente / Caja de ahorros detallada en la presente, los montos que correspondan según el plan comercial al que adhiero, en forma mensual y consecutiva a partir de la fecha de aprobación de la operación solicitada, en un todo de acuerdo con los datos consignados en esta autorización.\n";
$PDF->MultiCell(0,11,$TEXTO);

$PDF->SetY(62);
$PDF->imprimirDatosTitular($orden);
$size = 10;
$sized = 13;

#############################################################################################
# CUENTA PARA DEBITO
#############################################################################################
$PDF->imprimirDatosCuentaDebito($orden);



#############################################################################################
# DATOS PLAN COMERCIAL
#############################################################################################
$PDF->ln(4);
$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 190,
		'texto' => utf8_decode("Datos del Plan Comercial"),
		'borde' => 'B',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->Imprimir_linea();
$PDF->ln(4);


$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 55,
		'texto' => utf8_decode("EMPRESA PROVEEDORA:"),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $sized
);
$PDF->linea[2] = array(
		'posx' => 65,
		'ancho' => 90,
		'texto' => utf8_decode($orden['MutualProductoSolicitud']['proveedor_full_name']),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $sized
);
$PDF->linea[3] = array(
		'posx' => 155,
		'ancho' => 15,
		'texto' => utf8_decode("CUIT:"),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $sized
);
$PDF->linea[4] = array(
		'posx' => 170,
		'ancho' => 30,
		'texto' => utf8_decode($orden['MutualProductoSolicitud']['proveedor_cuit']),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $sized
);
$PDF->Imprimir_linea();


$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 55,
		'texto' => utf8_decode("Monto Total Autorizado: $"),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->linea[2] = array(
		'posx' => 65,
		'ancho' => 22,
		'texto' => $util->nf($orden['MutualProductoSolicitud']['importe_total']),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->linea[3] = array(
		'posx' => 87,
		'ancho' => 43,
		'texto' => utf8_decode("Cantidad de Cuotas:"),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->linea[4] = array(
		'posx' => 130,
		'ancho' => 7,
		'texto' => utf8_decode($orden['MutualProductoSolicitud']['cuotas_print']),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->linea[5] = array(
		'posx' => 137,
		'ancho' => 40,
		'texto' => utf8_decode("Monto de Cuota: $"),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->linea[6] = array(
		'posx' => 177,
		'ancho' => 23,
		'texto' => $util->nf($orden['MutualProductoSolicitud']['importe_cuota']),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->Imprimir_linea();

#############################################################################################
# TEXTO
#############################################################################################
$PDF->ln(4);
$PDF->SetFontSize(9);
$TEXTO = "Dejo Expresa constancia que en caso de no poderse realizar la cobranza de la forma pactada, AUTORIZO en forma expresa a ".Configure::read('APLICACION.nombre_fantasia')." o a la empresa proveedora, a seguir realizando los descuentos correspondientes a la total cancelación de las obligaciones por mi contraídas en este acto, con mas los intereses y gastos por mora que pudieren corresponder.-\n\n";
$TEXTO .= "Asimismo, AUTORIZO en forma expresa a ".Configure::read('APLICACION.nombre_fantasia')." a que en caso de no poseer fondos de manera consecutiva en la cuenta indicada, o de cambiar la cuenta en la que se acreditan mis haberes, la cobranza sea direccionada a la cuenta de mi titularidad que posea fondos para la cancelación de las obligaciones contraídas.-\n";
$PDF->MultiCell(0,11,$TEXTO);


//FONDO DE ASISTENCIA
$PDF->imprimirFdoAs($orden);

//if(!isset($orden['MutualProductoSolicitud']['OrdenDescuentoSeguro']) && !empty($orden['MutualProductoSolicitud']['OrdenDescuentoSeguro']['importe_total'])):
//	$PDF->ln(2);
//	$PDF->linea[1] = array(
//			'posx' => 10,
//			'ancho' => 190,
//			'texto' => "COBERTURA POR RIESGO CONTINGENTE",
//			'borde' => '',
//			'align' => 'C',
//			'fondo' => 0,
//			'style' => 'B',
//			'colorf' => '#D8DBD4',
//			'size' => 9
//	);
//	$PDF->Imprimir_linea();
//	$PDF->SetFontSize(9);
//	$TEXTO = "AUTORIZO en forma expresa a ".Configure::read('APLICACION.nombre_fantasia')." ";
//	$TEXTO .= "a descontar de mis haberes la cantidad de ".$orden['MutualProductoSolicitud']['OrdenDescuentoSeguro']['cuotas']." (".$orden['MutualProductoSolicitud']['OrdenDescuentoSeguro']['total_cuota_cantidad_letras'].") cuotas mensuales y consecutivas de $ ".$orden['MutualProductoSolicitud']['OrdenDescuentoSeguro']['importe_cuota']." (PESOS ".$orden['MutualProductoSolicitud']['OrdenDescuentoSeguro']['total_cuota_letras'].") cada una a favor de ______________________ en concepto de pago de la cobertura por Riesgo Contingente a partir del ___/_____.-\n";
//	$PDF->MultiCell(0,11,$TEXTO);
//elseif($orden['MutualProductoSolicitud']['fdoas']):
//	$PDF->ln(2);
//	$PDF->linea[1] = array(
//			'posx' => 10,
//			'ancho' => 190,
//			'texto' => "COBERTURA POR RIESGO CONTINGENTE",
//			'borde' => '',
//			'align' => 'C',
//			'fondo' => 0,
//			'style' => 'B',
//			'colorf' => '#D8DBD4',
//			'size' => 9
//	);
//	$PDF->Imprimir_linea();
//	$PDF->SetFontSize(9);
//	$TEXTO = "AUTORIZO en forma expresa a ".Configure::read('APLICACION.nombre_fantasia')." ";
//	$TEXTO .= "a descontar de mis haberes la cantidad de ".$orden['MutualProductoSolicitud']['fdoas_total_cuota_cantidad']." (".$orden['MutualProductoSolicitud']['fdoas_total_cuota_cantidad_letras'].") cuotas mensuales y consecutivas de $ ".$orden['MutualProductoSolicitud']['fdoas_total_cuota']." (PESOS ".$orden['MutualProductoSolicitud']['fdoas_total_cuota_letras'].") cada una a favor de ______________________ en concepto de pago de la cobertura por Riesgo Contingente a partir del ___/_____.-\n";
//	$PDF->MultiCell(0,11,$TEXTO);
//
//endif;
$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
if(isset($INI_FILE['general']['cuota_social_permanente']) && $INI_FILE['general']['cuota_social_permanente'] == 0):
    $PDF->ln(2);
    $PDF->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => "GASTOS ADMINISTRATIVOS",
            'borde' => '',
            'align' => 'C',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => 9
    );
    $PDF->Imprimir_linea();
    $PDF->SetFontSize(9);
    $TEXTO = "AUTORIZO en forma expresa a ".Configure::read('APLICACION.nombre_fantasia')." a debitar de mi Cuenta Bancaria el gasto administrativo mensual que se genera por la presente cobranza, el cual tendrá vigencia mientras exista saldo deudor de la operación detallada en el presente, y la cobranza de dicho saldo sea gestionado por ".Configure::read('APLICACION.nombre_fantasia').".-\n";
    $PDF->MultiCell(0,9,$TEXTO);
endif;

$PDF->ln(5);
$PDF->firmaSocio();
$PDF->ln(4);
$PDF->barCode($orden['MutualProductoSolicitud']['barcode']);


// $PDF->ln(40);

// $PDF->firmaSocio();

############################
# INSTRUCCION DE PAGO
############################
$PDF->imprimirInstruccionDePago($orden);
// $PDF->AddPage();
// $PDF->reset();
// $PDF->ln(4);
// $PDF->imprimirDatosGenerales($nroSolicitud,$fecha,$usuario,$vendedor);
// $size = 10;
// $size = 16;
// $PDF->linea[1] = array(
// 		'posx' => 10,
// 		'ancho' => 190,
// 		'texto' => "INSTRUCCION DE PAGO",
// 		'borde' => '',
// 		'align' => 'C',
// 		'fondo' => 0,
// 		'style' => 'B',
// 		'colorf' => '#D8DBD4',
// 		'size' => $size
// );
// $PDF->Imprimir_linea();
// $PDF->ln(4);
// $size = 11;
// $PDF->SetFontSizeConf($size);
// $TEXTO = "Por medio de la presente, la que suscribe ".$orden['MutualProductoSolicitud']['beneficiario_apenom'].", con ".$orden['MutualProductoSolicitud']['beneficiario_tdocndoc'].", como solicitante y adjudicatario del crédito según Solicitud Nº ".$orden['MutualProductoSolicitud']['nro_print'].", INSTRUYO y ORDENO irrevocablemente a _______________________, para que los fondos netos resultantes del mismo sean pagados de la siguiente manera:\n";
// $PDF->MultiCell(0,11,$TEXTO);

// //$orden['MutualProductoSolicitudInstruccionPago'] = null;

// if(!isset($orden['MutualProductoSolicitudInstruccionPago']) || empty($orden['MutualProductoSolicitudInstruccionPago'])):
// 	$PDF->linea[1] = array(
// 			'posx' => 10,
// 			'ancho' => 102,
// 			'texto' => "1) A mi orden personal, el importe de pesos",
// 			'borde' => '',
// 			'align' => 'L',
// 			'fondo' => 0,
// 			'style' => '',
// 			'colorf' => '#D8DBD4',
// 			'size' => $size
// 	);
// 	$PDF->linea[2] = array(
// 			'posx' => 112,
// 			'ancho' => 88,
// 			'texto' => "",
// 			'borde' => 'B',
// 			'align' => 'L',
// 			'fondo' => 0,
// 			'style' => '',
// 			'colorf' => '#D8DBD4',
// 			'size' => $size
// 	);
// 	$PDF->Imprimir_linea();

// 	$PDF->linea[1] = array(
// 			'posx' => 10,
// 			'ancho' => 40,
// 			'texto' => "2) A la orden de",
// 			'borde' => '',
// 			'align' => 'L',
// 			'fondo' => 0,
// 			'style' => '',
// 			'colorf' => '#D8DBD4',
// 			'size' => $size
// 	);
// 	$PDF->linea[2] = array(
// 			'posx' => 50,
// 			'ancho' => 150,
// 			'texto' => "",
// 			'borde' => 'B',
// 			'align' => 'L',
// 			'fondo' => 0,
// 			'style' => '',
// 			'colorf' => '#D8DBD4',
// 			'size' => $size
// 	);
// 	$PDF->Imprimir_linea();


// 	$PDF->linea[1] = array(
// 			'posx' => 10,
// 			'ancho' => 40,
// 			'texto' => "3) A la orden de",
// 			'borde' => '',
// 			'align' => 'L',
// 			'fondo' => 0,
// 			'style' => '',
// 			'colorf' => '#D8DBD4',
// 			'size' => $size
// 	);
// 	$PDF->linea[2] = array(
// 			'posx' => 50,
// 			'ancho' => 150,
// 			'texto' => "",
// 			'borde' => 'B',
// 			'align' => 'L',
// 			'fondo' => 0,
// 			'style' => '',
// 			'colorf' => '#D8DBD4',
// 			'size' => $size
// 	);
// 	$PDF->Imprimir_linea();


// 	$PDF->linea[1] = array(
// 			'posx' => 10,
// 			'ancho' => 40,
// 			'texto' => "4) A la orden de",
// 			'borde' => '',
// 			'align' => 'L',
// 			'fondo' => 0,
// 			'style' => '',
// 			'colorf' => '#D8DBD4',
// 			'size' => $size
// 	);
// 	$PDF->linea[2] = array(
// 			'posx' => 50,
// 			'ancho' => 150,
// 			'texto' => "",
// 			'borde' => 'B',
// 			'align' => 'L',
// 			'fondo' => 0,
// 			'style' => '',
// 			'colorf' => '#D8DBD4',
// 			'size' => $size
// 	);
// 	$PDF->Imprimir_linea();
// else:
// 	$n = 1;
// 	$TEXTO = "";
// 	foreach($orden['MutualProductoSolicitudInstruccionPago'] as $instruccion):
// 		$TEXTO .= "$n)" . $instruccion['a_la_orden_de'] . ", en concepto de " . $instruccion['concepto'] . " por un importe de PESOS $ " . $util->nf($instruccion['importe']) . ".-\n";
// 		$n++;
// 	endforeach;
// 	$PDF->MultiCell(0,11,$TEXTO);
// endif;
// $TEXTO = "Sin mas, saludo a Uds. muy atentamente.\n";
// $PDF->MultiCell(0,11,$TEXTO);

// $PDF->ln(20);

// $PDF->firmaSocio();

// $PDF->ln(4);
// $PDF->barCode($orden['MutualProductoSolicitud']['barcode']);

// $PDF->ln(20);

// $PDF->linea[1] = array(
// 		'posx' => 10,
// 		'ancho' => 190,
// 		'texto' => "",
// 		'borde' => 'T',
// 		'align' => 'L',
// 		'fondo' => 0,
// 		'style' => 'B',
// 		'colorf' => '#D8DBD4',
// 		'size' => $size
// );
// $PDF->Imprimir_linea();
// $PDF->ln(4);
/***********************************************************************
 * IMPRESION DEL PAGARE
 ***********************************************************************/
$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
if(isset($INI_FILE['general']['ocom_imprime_pagare_new_page']) && $INI_FILE['general']['ocom_imprime_pagare_new_page'] == 1){
    $PDF->HEADER = false;
    $PDF->PIE = false;
    $PDF->AddPage();
    $PDF->reset();
    $PDF->imprimirPagare($orden,false);
}else{
$PDF->imprimirPagare($orden);
}

////////////////////////////////////
// IMPRIMIR SOLICITUD DE AFILIACION
///////////////////////////////////
if(1 == 1 || !isset($orden['Socio']['id']) || empty($orden['Socio']['id']) || $orden['Socio']['activo'] == '0'):
	$membrete = array(
			'L1' => Configure::read('APLICACION.nombre_fantasia'),
			'L2' => Configure::read('APLICACION.domi_fiscal'),
			'L3' => Configure::read('APLICACION.telefonos')." - INAES ".Configure::read('APLICACION.matricula_inaes')
	);

	$PDF->imprimirSolicitudAfiliacion($membrete,$orden);
endif;



if(isset($orden['MutualProductoSolicitud']['proveedor_plan_anexos']) && !empty($orden['MutualProductoSolicitud']['proveedor_plan_anexos'])){

    foreach($orden['MutualProductoSolicitud']['proveedor_plan_anexos'] as $anexo){

        if($anexo == "ocom_imprime_auto_debito_nacion") $PDF->imprimirAutorizacionBancoNacion(__DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "bna2.png",$orden);
        if($anexo == "ocom_imprime_auto_debito_bcocba") $PDF->imprimirAutorizacionDebitoBcoCordoba($orden);
        if($anexo == "ocom_imprime_auto_debito_margen") $PDF->imprimirAutorizacionDebitoMargenComercial($orden);
        if($anexo == "ocom_imprime_pago_directo_rio") $PDF->imprimeAutoPagoDirectoSantanderRio($orden);
        if($anexo == "ocom_imprime_mutuo_minuta") $PDF->imprimeMinutaMutuo($orden);
        if($anexo == "ocom_imprime_mutuo") $PDF->imprimeContratoMutuo($orden);
        if($anexo == "ocom_imprime_pago_directo_bco_pcia_bsas") $PDF->imprimeAutoPagoDirectoBancoPciaBsAs(__DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "logo_bco_pcia_bsas.png",$orden);
        if($anexo == "ocom_modelo_liquidacion") $PDF->imprime_modelo_liquidacion_cuotas($orden);
		if($anexo == "ocom_imprime_auto_debito_cuenca") $PDF->imprime_autorizacion_debito_cuenca($orden);
        if($anexo == "ocom_imprime_afiliacion_isar") {$PDF->imprime_solicitud_afiliacion_isar($orden);} 		
        if($anexo == "ocom_imprime_auto_debito_lext") {$PDF->imprime_autorizacion_debito_lextsrl($orden,__DIR__ . DIRECTORY_SEPARATOR . "logotipo" . DIRECTORY_SEPARATOR . "AteZhJ.png",__DIR__ . DIRECTORY_SEPARATOR . "logotipo" . DIRECTORY_SEPARATOR . "lext_srl.png");}
        if($anexo == "ocom_imprime_acta_reununcia_cjpc" && $orden['MutualProductoSolicitud']['organismo'] == 'MUTUCORG7701'){$PDF->imprime_acta_reununcia_cjpc($orden);}	
        if($anexo == "ocom_imprime_contrato_descontar") {$PDF->imprime_contrato_descontar($orden);}
        if($anexo == "ocom_imprime_contrato_descontar_2") {$PDF->imprime_contrato_descontar_2($orden);}						
        if($anexo == "ocom_imprime_croquis_ubicacion") {$PDF->imprime_croquis_ubicacion($orden);}
        if($anexo == "ocom_imprime_auto_tarjeta_debito") {$PDF->imprimeAutoDebitoTarjeta($orden);}
        if($anexo == "ocom_imprime_auto_tarjeta_debito_ii") {$PDF->imprimeAutoDebitoTarjetaModeloII($orden);}
        if($anexo == "ocom_imprime_recibo") {$PDF->imprimirRecibo($orden);}
        if($anexo == "ocom_imprime_solicitud_amtec") {$PDF->ocom_imprime_solicitud_amtec($orden);}
        if($anexo == "ocom_imprime_auto_insredsa") {$PDF->ocom_imprime_auto_insredsa($orden);}
        if($anexo == "ocom_imprime_anexo_ddjj_cjpc") {$PDF->imprime_anexo_ddjj_cjpc($orden, __DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "cjpc_banner.png");}
        if($anexo == "ocom_imprime_cancela_stop") {$PDF->imprimeCancelacionStop($orden);}
        if($anexo == "ocom_imprime_mutuo_cjpc") { $PDF->imprime_mutuo_cjpc($orden, __DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "cjpc_banner.png");}
			
	}
	
}




$PDF->Output("solicitud_credito_".$orden['MutualProductoSolicitud']['id'].".pdf");

?>
