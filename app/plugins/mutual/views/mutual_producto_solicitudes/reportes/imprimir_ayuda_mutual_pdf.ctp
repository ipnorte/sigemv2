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
$PDF->imprimirDatosGenerales($nroSolicitud,$fecha,$usuario,$vendedor,"Solicitud de Ayuda Económica");

$size = 16;
$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 190,
		'texto' => "SOLICITUD DE AYUDA ECONOMICA MUTUAL",
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


#############################################################################################
# PRODUCTO
#############################################################################################
$PDF->imprimeCalculoAyudaEconomica($orden);
#############################################################################################

#############################################################################################
# LIQUIDACION
#############################################################################################
$PDF->imprimeLiquidacion($orden);
#############################################################################################


#############################################################################################
# CUENTA PARA DEBITO
#############################################################################################
$PDF->imprimirDatosCuentaDebito($orden);
#############################################################################################

#############################################################################################
# FIRMAS
#############################################################################################

$PDF->ln(25);

$PDF->firmaSocio();
$PDF->ln(4);
$PDF->barCode($orden['MutualProductoSolicitud']['barcode']);
#############################################################################################


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
$PDF->imprimirDatosGenerales($nroSolicitud,$fecha,$usuario,$vendedor,"Solicitud de Ayuda Económica");
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



$PDF->ln(10);
$PDF->firmaSocio();
$PDF->ln(4);
$PDF->barCode($orden['MutualProductoSolicitud']['barcode']);

$PDF->Output("solicitud_ayuda_".$orden['MutualProductoSolicitud']['id'].".pdf");

?>
