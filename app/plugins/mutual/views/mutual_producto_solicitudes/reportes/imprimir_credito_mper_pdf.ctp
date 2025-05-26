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
# FIRMAS
#############################################################################################

$PDF->ln(20);

$PDF->firmaSocio();
$PDF->ln(4);



#############################################################################################
# COBRO DIGITAL
#############################################################################################
$PDF->imprimeCobroDigital(__DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "cobroDigital.jpg", $orden, FALSE);



#############################################################################################
# PAGARE
#############################################################################################
$PDF->AddPage();
$PDF->reset();
$size = 16;
$PDF->linea[1] = array(
    'posx' => 10,
    'ancho' => 190,
    'texto' => utf8_decode("PAGARÉ \"SIN PROTESTO\""),
    'borde' => '',
    'align' => 'C',
    'fondo' => 0,
    'style' => 'B',
    'colorf' => '#D8DBD4',
    'size' => $size
);
$PDF->Imprimir_linea();
$PDF->ln(5);

$PDF->SetFont(PDF_FONT_NAME_MAIN,'',12);
$TEXTO = "En la Ciudad de__________________, a los_____días del mes de______________ del año________.";
$TEXTO .= "\n";
$TEXTO .= "Por este PAGARÉ abonaré/mos solidariamente SIN PROTESTO (art. 50 Decreto-Ley N° 5965/63) a________________________________________________ o a su orden, la cantidad de PESOS_________________________________________________ ($ __________________), por igual valor recibido en la misma moneda, y a nuestra entera satisfacción. Asimismo, nos obligamos a:";
$TEXTO .= "\n";
$TEXTO .= "1) Que el lugar de pago será en calle__________________________, de esta Ciudad de_____________________, o donde el acreedor lo indicará en lo sucesivo. -";
$TEXTO .= "\n";
$TEXTO .= "2) Que el pago lo efectuare/mos exclusivamente en efectivo y en la moneda convenida (Pesos Argentinos).-";
$TEXTO .= "\n";
$TEXTO .= "3) Que ampliamos expresamente el plazo de presentación para el cobro del presente pagaré a la vista, al plazo de siete años desde la fecha de libramiento (art. 36 Decreto-Ley n° 5965/63).-";
$TEXTO .= "\n";
$TEXTO .= "4) Que, en caso de incurrir en mora, al ser exigido el presente pagaré, la suma adeudada, devengará un interés punitorio equivalente al que percibe la Administración Federal de Ingresos Públicos (A.F.I.P.) para los créditos a favor del disco, previsto en el artículo 52 de la Ley 11.683 y sus modificatorias (actuales y/o futuras) , el cual se aplicará desde el comienzo de la mora y hasta su efectivo cobro.";
$TEXTO .= "\n";
$TEXTO .= "5) La falta de cumplimiento en término por parte del deudor, de una o más cuotas, dará derecho al acreedor a considerar vencidos todos los plazos pendientes de pago, específicamente el plazo de vencimiento del pagaré, pudiendo entonces el acreedor reclamar el total adeudado, descontando los pagos parciales recibidos si existieran. En consecuencia, en caso de incumplimiento, caducará el plazo de vencimiento establecido en el pagaré y se considerará que su vencimiento operó el día del incumplimiento del deudor.";
$TEXTO .= "\n";
$TEXTO .= "6) CUMPLIMIENTO ARTÍCULO 36 LEY 24.240.";
$TEXTO .= "\n";
$TEXTO .= "El importe que se entrega es la suma de pesos__________________________________________ ______________________________________________________________________________";
$TEXTO .= "\n";
$TEXTO .= "La tasa de interés efectiva anual es del________________________________________________";
$TEXTO .= "\n";
$TEXTO .= "El total de los intereses moratorios anual a pagar es del_______________________________";
$TEXTO .= "\n";
$TEXTO .= "El costo financiero total anual es del__________________________________________________";
$TEXTO .= "\n";
$TEXTO .= "La cantidad, periodicidad y montos de los pagos a realizar es el siguiente: ____________ _________________________________________________________________";
$TEXTO .= "\n";
$TEXTO .= "Sistema utilizado de amortización de capital y cancelación de los intereses: ___________ _________________________________________________________________";
$TEXTO .= "\n";
$TEXTO .= "Los gastos extras, seguros o adicionales son los siguientes: __________________ __________________________________________________________";
$TEXTO .= "\n";
$TEXTO .= "El/los librador/es constituye/n el siguiente domicilio especial: _______________________ ___________________ de la Ciudad de ___________________.-";
$TEXTO .= "\n";
$PDF->MultiCell(0,11,$TEXTO);


$PDF->ln(20);
$size = 8;
$space = 7;
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',8);
$PDF->linea[1] = array(
    'posx' => 20,
    'ancho' => 50,
    'texto' => "FIRMA",
    'borde' => 'T',
    'align' => 'L',
    'fondo' => 0,
    'style' => '',
    'colorf' => '#D8DBD4',
    'size' => $size
);
$PDF->linea[2] = array(
    'posx' => 120,
    'ancho' => 50,
    'texto' => "FIRMA",
    'borde' => 'T',
    'align' => 'L',
    'fondo' => 0,
    'style' => '',
    'colorf' => '#D8DBD4',
    'size' => $size
);
$PDF->Imprimir_linea();



$PDF->SetFont(PDF_FONT_NAME_MAIN,'',8);
$PDF->ln($space);
$PDF->linea[1] = array(
    'posx' => 20,
    'ancho' => 50,
    'texto' => utf8_decode("ACLARACION"),
    'borde' => 'T',
    'align' => 'L',
    'fondo' => 0,
    'style' => '',
    'colorf' => '#D8DBD4',
    'size' => $size
);
$PDF->linea[2] = array(
    'posx' => 120,
    'ancho' => 50,
    'texto' => utf8_decode("ACLARACION"),
    'borde' => 'T',
    'align' => 'L',
    'fondo' => 0,
    'style' => '',
    'colorf' => '#D8DBD4',
    'size' => $size
);
$PDF->Imprimir_linea();

$PDF->ln($space);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',8);
$PDF->linea[1] = array(
    'posx' => 20,
    'ancho' => 50,
    'texto' => utf8_decode("DNI"),
    'borde' => 'T',
    'align' => 'L',
    'fondo' => 0,
    'style' => '',
    'colorf' => '#D8DBD4',
    'size' => $size
);
$PDF->linea[2] = array(
    'posx' => 120,
    'ancho' => 50,
    'texto' => utf8_decode("DNI"),
    'borde' => 'T',
    'align' => 'L',
    'fondo' => 0,
    'style' => '',
    'colorf' => '#D8DBD4',
    'size' => $size
);
$PDF->Imprimir_linea();
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',8);
$PDF->ln($space);
$PDF->linea[1] = array(
    'posx' => 20,
    'ancho' => 50,
    'texto' => utf8_decode("DOMICILIO"),
    'borde' => 'T',
    'align' => 'L',
    'fondo' => 0,
    'style' => '',
    'colorf' => '#D8DBD4',
    'size' => $size
);
$PDF->linea[2] = array(
    'posx' => 120,
    'ancho' => 50,
    'texto' => utf8_decode("DOMICILIO"),
    'borde' => 'T',
    'align' => 'L',
    'fondo' => 0,
    'style' => '',
    'colorf' => '#D8DBD4',
    'size' => $size
);
$PDF->Imprimir_linea();



#############################################################################################
# MUTUO
#############################################################################################
$PDF->AddPage();
$PDF->reset();

$size = 16;
$PDF->linea[1] = array(
    'posx' => 10,
    'ancho' => 190,
    'texto' => utf8_decode("CONTRATO DE MUTUO"),
    'borde' => '',
    'align' => 'C',
    'fondo' => 0,
    'style' => 'B',
    'colorf' => '#D8DBD4',
    'size' => $size
);
$PDF->Imprimir_linea();
$PDF->ln(5);

$PDF->SetFont(PDF_FONT_NAME_MAIN,'',11);

// debug($orden);

$TEXTO = "Entre el Sr ____________________________, DNI  Nº __________, con domicilio en ___________________________________________, en adelante denominado “el  Mutuante”, y el Sr ".$orden['MutualProductoSolicitud']['beneficiario_apenom']." DNI  N° ".$orden['MutualProductoSolicitud']['beneficiario_ndoc']." con domicilio en calle ".utf8_decode($orden['MutualProductoSolicitud']['beneficiario_domicilio']).", en adelante denominado “el Mutuario”, se celebra el presente contrato de mutuo o préstamo de dinero de acuerdo a las siguientes Cláusulas:";
$TEXTO .= "\n";
$TEXTO .= "1) El Mutuante otorga en préstamo al Mutuario la suma de PESOS _______________________________ en efectivo, recibiendo el mutuario dicha suma de dinero de conformidad y manifestando expresamente que la misma será destinada a _____________________________________";
$TEXTO .= "\n";
$TEXTO .= "2) Las Partes pactan que la devolución se hará efectiva en ___________ cuotas mensuales de pesos ______ _______________________________ ($ ____________) cada una, pagaderas del 1 al 10 de cada mes. Los pagos deberán efectuarse en el domicilio sito en calle ___________________________ Nro. __________, ciudad de Córdoba o en donde el Mutuante así lo comunicase en el futuro.";
$TEXTO .= "\n";
$TEXTO .= "3) Sobre los saldos en mora, se conviene que se habrá de aplicar un interés punitorio del ______% mensual proporcional al tiempo en mora.";
$TEXTO .= "\n";
$TEXTO .= "4) El Mutuante imputará los pagos del Mutuario, primero a cancelar los gastos originados en la mora, luego los intereses punitorios y compensatorios del monto en mora, y por último a la cancelación de la cuota atrasada.";
$TEXTO .= "\n";
$TEXTO .= "5) El incumplimiento de cualesquiera obligaciones contractuales, facultará al Mutuante a dar por caducados los plazos de las cuotas adeudadas, pudiendo reclamar el pago total de ellas como si fuesen vencidas y exigibles.";
$TEXTO .= "\n";
$TEXTO .= "6) En garantía de la restitución del préstamo, el Mutuario libra a favor del Mutuante ____________________________pagaré por la suma de pesos ____________________ $ ___________ el cual será restituido al cancelarse todas las cuotas adeudadas. En caso de ser necesario accionar judicialmente, el Mutuante se obliga a accionar por el pagaré o por el presente contrato, pero no por ambos.";
$TEXTO .= "\n";
$TEXTO .= "7) El/La Sr/a _________________________, con DNI Nº __________, domiciliado en ______________________________, se constituye en fiador y principal pagador de todas las obligaciones que surgen del presente contrato, durante la duración del mismo y aún después de vencido, hasta su total cumplimiento, haciéndose expresa  renuncia a los beneficios de excusión y división. En los supuestos de ausencia, muerte, incapacidad o falencia del fiador, acreditados estos extremos, el Mutuario deberá reemplazarlo,a satisfacción del Mutuante, en un término de diez días, bajo pena de darse por rescindido el contrato.";
$TEXTO .= "\n";
$TEXTO .= "8) Cumplimiento del Art. 36 de la Ley 24.240";
$TEXTO .= "\n";
$TEXTO .= "a) El importe que se entrega en efectivo es la suma de pesos argentinos ___________________________________ __________________________________________ ($ _______________) ";
$TEXTO .= "\n";
$TEXTO .= "b) El importe de la transferencia bancaria es el de la suma de pesos argentinos _____________________________ ___________________________________ ($ _______________) ";
$TEXTO .= "\n";
$TEXTO .= "c) La tasa de interés efectiva anual es del: __________________";
$TEXTO .= "\n";
$TEXTO .= "d) El total de los intereses moratorios anual a pagar es del:___________________________";
$TEXTO .= "\n";
$TEXTO .= "e) El costo financiero total anual es del:_________________";
$TEXTO .= "\n";
$TEXTO .= "f) La cantidad, periodicidad y montos de los pagos a realizar es el siguiente: _____________ cuotas mensuales y consecutivas de pesos ___________________________________________________ ($ _______________) cada una. ";
$TEXTO .= "\n";
$TEXTO .= "g) Sistema utilizado de amortización de capital y cancelación de intereses:______________";
$TEXTO .= "\n";
$TEXTO .= "h) Los gastos extra, seguros o adicionales, son los siguientes:_________________________";
$TEXTO .= "\n";
$TEXTO .= "9) A todos los efectos legales, las partes y el fiador se someten a los Tribunales ordinarios de la Provincia de Córdoba, y constituyen domicilio en los lugares arriba indicados, donde se considerarán válidas todas las notificaciones y emplazamientos judiciales o extrajudiciales que se hagan. Se firman en prueba de conformidad dos ejemplares de un mismo tenor y a un solo efecto en la ciudad de Córdoba, a los _____________ días del mes de ________ del año _______-";
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "                               FIRMA                                           ACLARACION                                           DNI";
$TEXTO .= "\n";

// $w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
$PDF->MultiCell(0,11,$TEXTO);










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
        
        
			
    }

}

$PDF->Output("solicitud_credito_".$orden['MutualProductoSolicitud']['id'].".pdf");

?>
