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
$PDF->SetY(10);
$nroSolicitud = $orden['MutualProductoSolicitud']['nro_print'];
$fecha = $util->armaFecha($orden['MutualProductoSolicitud']['fecha']);
$usuario = $orden['MutualProductoSolicitud']['user_created'];
$vendedor = (isset($orden['MutualProductoSolicitud']['vendedor_nombre_min']) ? $orden['MutualProductoSolicitud']['vendedor_nombre_min'] : "");
$PDF->imprimirDatosGenerales($nroSolicitud,$fecha,$usuario,$vendedor,'Solicitud de Préstamo', TRUE);
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
$PDF->imprimirDatosTitular($orden, FALSE, TRUE);
#############################################################################################
# PRODUCTO
#############################################################################################
$PDF->ln(4);
$PDF->imprimir_producto_solicitado($orden, TRUE);

#############################################################################################
# LIQUIDACION
#############################################################################################
$PDF->ln(4);
$PDF->imprimir_liquidacion($orden, TRUE, TRUE);

#############################################################################################
# CUENTA PARA DEBITO
#############################################################################################
$PDF->imprimirDatosCuentaDebito($orden, FALSE, TRUE);

#############################################################################################
# TABLA CON PARAMETROS DE CALCULO
#############################################################################################
// $PDF->ln(3);
// $PDF->imprimeTablaCalculo($orden);

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

/*
$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',14);
$PDF->Cell(0,5,$membrete['L1'],0);
$PDF->Ln(5);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',8);
$PDF->Cell(0,5,$membrete['L2'],0);
$PDF->Ln(3);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',8);
$PDF->Cell(0,5,$membrete['L3'],0);
$PDF->Ln(4);
*/
$PDF->SetY(10);
$PDF->imprimirDatosGenerales($nroSolicitud,$fecha,$usuario,$vendedor,'Solicitud de Préstamo', true);
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
$TEXTO = "Por la presente, AUTORIZO a ________________________________________, a debitar de mi Cuenta Corriente / Caja de ahorros detallada en la presente, los montos que correspondan según el plan comercial al que adhiero, en forma mensual y consecutiva a partir de la fecha de aprobación de la operación solicitada, en un todo de acuerdo con los datos consignados en esta autorización.\n";
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
                'texto' => "",
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
                'texto' => "",
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
		'texto' => "",
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
		'texto' => "",
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
		'texto' => "",
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
$TEXTO = "Dejo Expresa constancia que en caso de no poderse realizar la cobranza de la forma pactada, AUTORIZO en forma expresa a ________________________________________ o a la empresa proveedora, a seguir realizando los descuentos correspondientes a la total cancelación de las obligaciones por mi contraídas en este acto, con mas los intereses y gastos por mora que pudieren corresponder.-\n\n";
$TEXTO .= "Asimismo, AUTORIZO en forma expresa a ________________________________________ a que en caso de no poseer fondos de manera consecutiva en la cuenta indicada, o de cambiar la cuenta en la que se acreditan mis haberes, la cobranza sea direccionada a la cuenta de mi titularidad que posea fondos para la cancelación de las obligaciones contraídas.-\n";
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
    $TEXTO = "AUTORIZO en forma expresa a ________________________________________ a debitar de mi Cuenta Bancaria el gasto administrativo mensual que se genera por la presente cobranza, el cual tendrá vigencia mientras exista saldo deudor de la operación detallada en el presente, y la cobranza de dicho saldo sea gestionado por ________________________________________.-\n";
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
$PDF->imprimirInstruccionDePago($orden, TRUE);
/***********************************************************************
 * IMPRESION DEL PAGARE
 ***********************************************************************/
$PDF->HEADER = false;
$PDF->PIE = false;
$PDF->AddPage();
$PDF->reset();
$PDF->imprimirPagare($orden,false,false,true,false);

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
$PDF->imprimirRecibo($orden, TRUE);


imprimeCBASERVICIOS_BLANK($orden, $PDF);



// http://localhost/sigemv2/mutual/mutual_producto_solicitudes/imprimir_credito_mutual_pdf/72126


$PDF->Output("solicitud_credito_".$orden['MutualProductoSolicitud']['id'].".pdf");


function imprimeCBASERVICIOS_BLANK($orden, $PDF) {
    
   // ------------------------------------------------------------
   // Contrato mutuo
   // ------------------------------------------------------------
    
   $PDF->AddPage();
   $PDF->reset();
   // $PDF->SetFont(PDF_FONT_NAME_MAIN,'B',20);
   $size = 11;
   $PDF->linea[1] = array(
       'posx' => 10,
       'ancho' => 140,
       'texto' => utf8_decode("CONTRATO DE MUTUO"),
       'borde' => '',
       'align' => 'C',
       'fondo' => 0,
       'style' => 'B',
       'colorf' => '#D8DBD4',
       'size' => $size + 2
   );
   $PDF->linea[2] = array(
       'posx' => 150,
       'ancho' => 50,
       'texto' => utf8_decode("Préstamo Nro: "),
       'borde' => '',
       'align' => 'L',
       'fondo' => 0,
       'style' => '',
       'colorf' => '#D8DBD4',
       'size' => $size
   );        
   $PDF->Imprimir_linea();
   // $PDF->SetFont(PDF_FONT_NAME_MAIN,'B',20);
   $PDF->linea[1] = array(
       'posx' => 10,
       'ancho' => 140,
       'texto' => "",
       'borde' => 'B',
       'align' => 'C',
       'fondo' => 0,
       'style' => 'B',
       'colorf' => '#D8DBD4',
       'size' => $size
   );
   $PDF->linea[2] = array(
       'posx' => 150,
       'ancho' => 50,
       'texto' => utf8_decode("Cliente Nro:"),
       'borde' => 'B',
       'align' => 'L',
       'fondo' => 0,
       'style' => '',
       'colorf' => '#D8DBD4',
       'size' => $size
   );        
   $PDF->Imprimir_linea();        

   $PDF->ln(3);
   $PDF->SetFont(PDF_FONT_NAME_MAIN,'',10);
   $apenom = utf8_decode($orden['MutualProductoSolicitud']['beneficiario_apenom']);
   $dni = utf8_decode($orden['MutualProductoSolicitud']['beneficiario_ndoc']);
   $domicilioDeudor = utf8_decode($orden['MutualProductoSolicitud']['beneficiario_domicilio']);
   $domicilioDeudor = ($domicilioDeudor == ' ' ? '________________________' : $domicilioDeudor);

$TEXTO = <<<EOD
Condiciones Generales: La presente operación se realizará de acuerdo a las siguientes condiciones.

Primera: El mutuo documentado en esta solicitud se integra con el pagaré a la vista sin protesto Art. 50 Decreto Ley 5965/63 que el Sr./Sra. $apenom, DNI $dni, con domicilio en $domicilioDeudor, suscribe a favor de ________________________ por el total del préstamo solicitado con más sus intereses. Las partes convienen que dicho pagaré podrá ser ejecutado judicialmente, sin perjuicio de las acciones emergentes del presente contrato de mutuo, en el caso de producirse el incumplimiento de las condiciones a que se sujeta el otorgamiento del préstamo.

Segunda: Asimismo, las partes convienen que NO será necesaria interpelación previa judicial o extrajudicial para la constitución de la Mora del deudor, es decir que la Mora se producirá en forma automática de pleno derecho, ante la falta de pago de cualesquiera de las cuotas, lo que implicará la caducidad de todos los plazos, considerándose en tal caso la deuda como íntegramente vencida, quedando facultado _______________ a reclamar la totalidad del crédito con más los intereses punitorios y gastos correspondientes.

Tercera: En caso de incurrir en mora, el deudor se obliga a pagar, además del saldo adeudado, un interés adicional en carácter de punitorio, equivalente al 50% del interés compensatorio, mientras dure la mora y hasta la cancelación total de la deuda.

Cuarta: Para el supuesto que ________________________ no pudiese debitar las CUOTAS de la Caja de Ahorro y/o Cuenta Corriente a nombre del deudor, éste se compromete a cancelar las mismas del 1 al 10 de cada mes en el domicilio de ________________________ o cuenta bancaria que éste le indique al efecto.

Quinta: Si el deudor optara por realizar la Cancelación Total en forma Anticipada del crédito otorgado mediante la presente, deberá abonar además del saldo del capital adeudado, un recargo de hasta el 30% (treinta por ciento), de dicho saldo de capital, en concepto de compensación.

Sexta: El deudor toma a su cargo el pago de todos los impuestos presentes o futuros, costos, costas, comisiones, tasas de cualquier naturaleza que existan o fuesen creadas en el futuro por el Gobierno Nacional, Provincial o Municipal.

Séptima: Todas y cada una de las cuotas se calcularán bajo el régimen de amortización Francés. El deudor abonará sobre el capital adeudado una Tasa de interés fija del ____% Nominal Anual (T.N.A.), Tasa Efectiva Mensual ____% (T.E.M.), Tasa Efectiva Anual ____% (T.E.A.), Costo Financiero Total ____% (C.F.T.).

Octava: El deudor declara bajo juramento que ________________________ le ha informado previamente, que en cumplimiento de la Ley de Habeas Data y reglamentarias, los datos personales y patrimoniales relacionados con la operación crediticia que contrata por el presente podrán ser inmediatamente informados y registrados en la base de datos de las organizaciones de información crediticia, públicas y/o privadas.

Novena: El deudor cuando lo considere necesario podrá exigir a $apenom el detalle de cuotas canceladas y a cancelar.

Décima: Mediante el presente el deudor presta expresa conformidad a que ________________________ ceda y/o transfiera el presente crédito y/o los derechos emanados del mismo, sin necesidad que dicha cesión y/o transferencia le sea notificada, ello, de conformidad con lo establecido por los Art. 70 y 72 de la Ley 24.441.

Undécima: El deudor declara bajo juramento que el destino de los fondos otorgados por ________________será: Consumo---------------------------------------.
EOD;

    $PDF->MultiCell(0, 6, $TEXTO, 0, 'J');  

    $PDF->ln(35);
    $PDF->firmaSocio();    
    
    // ------------------------------------------------------------
    // ----- DEBITO DIRECTO ---
    // ------------------------------------------------------------
    $PDF->AddPage();
    $PDF->reset();
   
    $PDF->SetFont(PDF_FONT_NAME_MAIN,'',10);
    
    $PDF->linea[1] = array(
        'posx' => 10,
        'ancho' => 190,
        'texto' => utf8_decode("________________, ____ de ______________________ de ________.-"),
        'borde' => '',
        'align' => 'R',
        'fondo' => 0,
        'style' => '',
        'colorf' => '#D8DBD4',
        'size' => 10
    );
    $PDF->Imprimir_linea();
    $PDF->ln(1);   
    
    $PDF->SetFont(PDF_FONT_NAME_MAIN,'',10);
    
    $TEXTO = "________________________________\n\nDe mi mayor consideración:\n".
              "En mi carácter de Titular del Préstamo Nº ________, me dirijo a Uds. a fin de autorizar y prestar mi entera ".
              "conformidad en forma irrevocable a descontar de mis haberes por débito automático de la Caja de Ahorro / Cta. ".
              "Cte. (tachar lo que no corresponda) Nº  ______________________ que poseo radicada en el Banco ______________________ ".
              "Sucursal ______________________, ".
              "correspondiente a la Clave Bancaria Uniforme (C.B.U.) ___________________________  o ".
              "que poseyere en el futuro, adhiriéndome al sistema de PAGO DIRECTO normado por el B.C.R.A., suscribiendo ".
              "además el formulario emitido por el banco con el que usted mantiene convenio de pago directo, el que además ".
              "declaro conocer en todos sus términos en mi calidad de Titular del crédito mencionado precedentemente, para ".
              "abonar la suma de $ ___________________________ (Pesos ________________________________) ".
              "durante ______ (___________________________) períodos mensuales, iguales y consecutivos a ".
              "partir de mis haberes correspondientes al mes de ________ de 20___ con vencimiento la primera ".
              "de ellas el día __/_______/_______ y las siguientes en igual fecha de los meses sucesivos. También por este ".
              "instrumento autorizo, en idénticos términos que los mencionados, se me descuente el arancel que percibe el Ente ".
              "Administrador a los fines de procesamiento de los descuentos que le fueran remitidos por ________________________ ".
              "y cualquier gravamen, tasa o tributo de cualquier índole y carácter y/o del tipo que fuere, actual o".
              "que se implementare en cabeza y/o bajo la responsabilidad de ________________________ ".
              "como consecuencia de la gestión y/o tramitación de la gestoría de pagos de las obligaciones de mi parte que ".
              "autorizara su descuento por el presente instrumento. Recibo en este acto una copia de la presente autorización.\n";

    $PDF->MultiCell(0, 6, $TEXTO, 0, 'J');
 

    $PDF->ln(15);
    $PDF->firmaSocio();      
    
    // ------------------------------------------------------------
    // ----- DEBITO EN CUENTA BANCARIA ---
    // ------------------------------------------------------------
    $PDF->AddPage();
    
    //$PDF->ln(15);
    
    
    $PDF->reset();    
    
    $PDF->linea[2] = array(
        'posx' => 10,
        'ancho' => 190,
        'texto' => utf8_decode("AUTORIZACION DE DEBITO AUTOMATICO EN CUENTA BANCARIA"),
        'borde' => '',
        'align' => 'C',
        'fondo' => 0,
        'style' => 'B',
        'colorf' => '#D8DBD4',
        'size' => 14
    );        
    $PDF->Imprimir_linea();   
    $PDF->ln(5);
    $PDF->SetFont(PDF_FONT_NAME_MAIN,'',8);
    $PDF->linea[1] = array(
        'posx' => 10,
        'ancho' => 190,
        'texto' => utf8_decode("________________, ____ de ______________________ de ________.-"),
        'borde' => '',
        'align' => 'R',
        'fondo' => 0,
        'style' => '',
        'colorf' => '#D8DBD4',
        'size' => 10
    );
    $PDF->Imprimir_linea();
    $PDF->ln(1);       
    $PDF->SetFont(PDF_FONT_NAME_MAIN,'',10);

    $TEXTO = "Por la presente, quien suscribe, ".utf8_decode($orden['MutualProductoSolicitud']['beneficiario_apenom']).", en ".
         "mi carácter de Titular de la Caja de Ahorro / Cuenta Corriente (tachar lo que no corresponda) Nº ".
         "______________________, radicada en el Banco ______________________, ".
         "Sucursal ______________________, CBU Nº _________________________________ presto ".
         "autorización en forma expresa e irrevocable a ________________________ a debitar ".
         "de dicha cuenta la suma total de pesos ______________________ ".
         "($ ______________), en ____ (______________________) cuotas iguales, mensuales y ".
         "consecutivas de pesos ______________________ ($ ______________) ".
         "cada una, venciendo la primera el día ____/_______/________ y las siguientes en igual fecha de los meses ".
         "sucesivos. Me comprometo a mantener en la mencionada cuenta los fondos disponibles necesarios para ".
         "satisfacer el pago de cada una de las cuotas con más sus intereses y demás accesorios, obligándome, ".
         "asimismo, a no cerrar la misma durante la vigencia del préstamo antes identificado y a la cancelación total ".
         "de todas y cada una de las obligaciones emanadas del préstamo, como así también a no dar contraorden ".
         "a la entidad financiera impidiendo el débito que por el presente se autoriza (stop debit, orden no pagar o ".
         "cualquier otro de similar efecto).\n".
         "Manifiesto conocer que los fondos deberán estar disponibles en la mencionada cuenta con una ".
         "anticipación mínima de 24 horas respecto del día en que deba efectivizarse el débito correspondiente. La entidad ".
         "donde se encuentra radicada la cuenta no será responsable por la falta de débito en caso de inexistencia de ".
         "fondos, por lo cual no se efectuarán débitos parciales. Ante la insuficiencia de fondos al vencimiento de ".
         "cualquiera de las cuotas, todo depósito posterior efectuado en la cuenta antes referenciada, carecerá de toda ".
         "posibilidad de imputación a la/las cuota/s vencida/s impaga/s. En tal sentido, autorizo irrevocablemente a ".
         "________________________ a compensar, en la oportunidad que considere conveniente y sin ".
         "previa notificación, el importe adeudado que en cualquier momento mantenga en esa entidad con los saldos ".
         "acreedores de cualquier otra cuenta o con el importe de los créditos de cualquier naturaleza que existiesen a mi ".
         "nombre y/u orden, aún cuando dichos saldos se encuentren expresados en otra moneda o valor. La presente ".
         "autorización de débito se otorga a los fines de cancelar los pagos a mi cargo, en virtud de las obligaciones por mí ".
         "asumidas con relación al préstamo antes referido, y será válida tanto para los débitos ordenados por ".
         "________________________ en tal concepto, como para los que ordenare cualquier otra entidad ".
         "que ________________________ eventualmente designare como agente de cobranza.\n".
         "Declaro bajo juramento que la cuenta indicada anteriormente es de mi exclusiva titularidad, ".
         "relevando a esa entidad de toda responsabilidad por la información brindada. ".
         "Asimismo, y para el supuesto que la cuenta identificada en el presente fuera cerrada por cualquier ".
         "causa, me comprometo a sustituir la misma por cualquier otra cuenta similar abierta en la misma entidad ".
         "financiera o en otra distinta y notificar dicha situación de inmediato a ________________________, ".
         "bajo apercibimiento de que ésta, ante la imposibilidad de continuar con los débitos periódicos de las cuotas del ".
         "préstamo, me constituya en mora de pleno derecho generando el decaimiento de todas las cuotas pendientes, y ".
         "pudiendo reclamar la totalidad de los saldos acreedores con más sus intereses y demás accesorios.\n".
         "Por la presente, renuncio expresa e irrevocablemente a la utilización de mecanismos de ".
         "reversión, stop debit, orden de no pagar y cualquier otro de similar efecto, mientras mantenga vigente mi ".
         "préstamo con ________________________ y/o adeude a ésta suma alguna, constituyendo ésta ".
         "una obligación frente a la entidad.\n".
         "Tomo conocimiento que es obligación de ________________________ ".
         "informarme la suma a ser descontada con una antelación mínima de 5 días hábiles respecto de la fecha fijada ".
         "para el débito que autorizo en este acto. Me notifico que cuento con las siguientes facultades: I) Solicitar la ".
         "suspensión de un débito hasta el día hábil anterior -inclusive- a la fecha de vencimiento; y II) Solicitar al Banco".
         "mencionado precedentemente revierta los débitos por el total de cada operación, dentro de los 30 días corridos ".
         "contados desde la fecha de débito. En tal caso, la devolución será efectuada dentro de las 72 horas hábiles ".
         "siguientes a la fecha en que lo solicite, pudiendo ________________________ oponerse a la reversión cuando se trate de ".
         "un importe mayor a $ ______ y subsanaren la forma de facturación directa.\n";

        $PDF->MultiCell(0, 6, $TEXTO, 0, 'J');
    
        $PDF->ln(15);  
       
                $PDF->linea[1] = array(
					'posx' => 20,
					'ancho' => 50,
					'texto' => "Firma del Solicitante",
					'borde' => 'T',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => 8
			);
		$PDF->linea[2] = array(
					'posx' => 80,
					'ancho' => 50,
                                        'texto' => utf8_decode("Aclaración"),
					'borde' => 'T',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => 8
			);
		$PDF->linea[3] = array(
					'posx' => 140,
					'ancho' => 50,
					'texto' => "Firma y Sello del Resp. Comercial",
					'borde' => 'T',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => 8
			);		
		$PDF->Imprimir_linea();
       
    
    // ------------------------------------------------------------
    // --- AUTORIZACION BANCO NACION
    // ------------------------------------------------------------
    $PDF->imprimirAutorizacionBancoNacion(__DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "bna2.png",$orden);

}



?>


