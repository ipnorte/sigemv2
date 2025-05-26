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

$imprimeDatosProveedor = false;
$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
if(isset($INI_FILE['general']['imprimir_credito_aman_pdf_proveedor']) && $INI_FILE['general']['imprimir_credito_aman_pdf_proveedor'] == 1){
    $imprimeDatosProveedor = true;
}

if($imprimeDatosProveedor) {
   
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
    
}




$PDF->SetY(10);
$PDF->SetFont('courier','',12);
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
# TABLA CON PARAMETROS DE CALCULO
#############################################################################################
$PDF->ln(3);
$PDF->imprimeTablaCalculo($orden);

#############################################################################################
# FIRMAS
#############################################################################################

$PDF->ln(20);

$PDF->firmaSocio();
$PDF->ln(4);

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

$empresaProveedoraNombre = (!$orden['MutualProductoSolicitud']['proveedor_pagare_blank'] ? $orden['MutualProductoSolicitud']['proveedor_full_name'] : '');
$empresaProveedoraCuit = (!$orden['MutualProductoSolicitud']['proveedor_pagare_blank'] ? $orden['MutualProductoSolicitud']['proveedor_cuit'] : '');

$PDF->linea[2] = array(
		'posx' => 65,
		'ancho' => 90,
    'texto' => utf8_decode($empresaProveedoraNombre),
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
    'texto' => utf8_decode($empresaProveedoraCuit),
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
$lineas = 20;
$PDF->imprimirFdoAs($orden);
if($orden['MutualProductoSolicitud']['fdoas']) { $lineas = 15; }


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
//     $lineas = 40;
endif;

#############################################################################################
# TABLA CON PARAMETROS DE CALCULO
#############################################################################################
$PDF->ln(3);
//$PDF->imprimeTablaCalculo($orden);

$PDF->ln($lineas);
$PDF->firmaSocio();
$PDF->ln(4);



// $PDF->ln(40);

// $PDF->firmaSocio();

############################
# INSTRUCCION DE PAGO
############################
// $PDF->HEADER = false;
// $PDF->PIE = true;
// $PDF->AddPage();
// $PDF->reset();
$PDF->imprimirInstruccionDePago($orden);
/***********************************************************************
 * IMPRESION DEL PAGARE
 ***********************************************************************/
$PDF->HEADER = false;
$PDF->PIE = false;
$PDF->AddPage();
$PDF->reset();
$PDF->imprimirPagare($orden,false,false,false);

////////////////////////////////////
// IMPRIMIR SOLICITUD DE AFILIACION
///////////////////////////////////
if( 1 == 1 || !isset($orden['Socio']['id']) || empty($orden['Socio']['id']) || !$orden['Socio']['activo']):

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
        if($anexo == "ocom_imprime_mutuo") $PDF->imprimeContratoMutuoAMAN($orden);
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
        if($anexo == "ocom_imprime_solicitud_amtec") {$PDF->ocom_imprime_solicitud_amtec($orden);}
        if($anexo == "ocom_imprime_anexo_ddjj_cjpc") {$PDF->imprime_anexo_ddjj_cjpc($orden, __DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "cjpc_banner.png");}
			
    }

}

/////////////////////////////////////////////////////////////////
// RECIBO
/////////////////////////////////////////////////////////////////
$PDF->imprimirRecibo($orden);

$PDF->Output("solicitud_credito_".$orden['MutualProductoSolicitud']['id'].".pdf");

?>
