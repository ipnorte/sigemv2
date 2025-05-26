<?php
// debug($orden);
// exit;

App::import('Vendor','solicitud_credito_general_pdf');

$PDF = new SolicitudCreditoGeneralPDF();

$PDF->SetTitle("SOLICITUD DE CREDITO #".$id);
$PDF->SetFontSizeConf(8.5);
$PDF->PIE = false;

$DEFAULT_FONT_SIZE = 10;
$TEXTO_PIE = "Gorostiaga 1664 1°B - C1426CTB - Cap. Fed. Buenos Aires - Tel. (5411) 4773-4020 - email: idun@idun.com.ar";
    
define('TEXTO_PIE', $TEXTO_PIE);


$PDF->Open();

// $membrete = array(
// 		'L1' => Configure::read('APLICACION.nombre_fantasia'),
// 		'L2' => Configure::read('APLICACION.domi_fiscal'),
// 		'L3' => "TEL: " . Configure::read('APLICACION.telefonos') ." - email: ".Configure::read('APLICACION.email')
// );

$membrete = array(
		'L1' => $orden['MutualProductoSolicitud']['proveedor_full_name'],
		'L2' => $orden['MutualProductoSolicitud']['proveedor_domicilio'],
		'L3' => $orden['MutualProductoSolicitud']['proveedor_localidad'] ." ".$orden['MutualProductoSolicitud']['proveedor_telefono']
);

########################################################################################################################
# ENCABEZADO IUTUM
########################################################################################################################
$PDF->AddPage();
function encabezado($PDF){
    $PDF->SetY(10);
    $PDF->SetFillColor(220,220,220);

    // $PDF->SetFont('courier','',12);
    $PDF->image(__DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "IutumWordColor.jpg",10,5,40);
    $PDF->SetY(5);
    $PDF->SetX(100);
    $PDF->Cell(0,5,"Lat. Colaborar y ayudar. Matrícula: B.A.-1.896.",0,0,'R');
    $PDF->SetY(8);
    $PDF->SetX(100);
    $PDF->Cell(0,5,"Montes de Oca 2448 Piso 1 Depto 2",0,0,'R');
    $PDF->SetY(11);
    $PDF->SetX(100);
    $PDF->Cell(0,5,"C.P. 1708, Castelar, Provincia de Buenos Aires.",0,0,'R');
    $PDF->SetY(14);
    $PDF->SetX(100);
    $PDF->Cell(0,5,"Tel/Fax: (011) 4781-1220. Email: ayudaiutum@gmail.com",0,0,'R');
    $PDF->SetY(17);
    $PDF->SetX(100);
    $PDF->Cell(0,5,"Inscripción en AFIP. - CUIT: 30-71073580-4",0,0,'R');    
    $PDF->line(10,35,200,35);
    
}

function pie($PDF,$DEFAULT_FONT_SIZE){
    $TEXTO_PIE = "Gorostiaga 1664 1°B - C1426CTB - Cap. Fed. Buenos Aires - Tel. (5411) 4773-4020 - email: idun@idun.com.ar";
    $PDF->SetY(-10);
    $PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE);
    $PDF->Cell(190,$PDF->H,$TEXTO_PIE,'T',0,'C');    
}

function condicionesGenerales($PDF,$DEFAULT_FONT_SIZE,$orden,$util){
    $PDF->SetY(10);
    $PDF->SetX(10);


    $PDF->SetFont(PDF_FONT_NAME_MAIN,'B',$DEFAULT_FONT_SIZE);
    $PDF->Cell(190,$PDF->H,"CONDICIONES GENERALES DE LA SOLICITUD DE AYUDA FINANCIERA MUTUAL:",0,0,'L');


    $PDF->INI_Y = 20;
    $PDF->SetLinea(1);


    $PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE-1);
    $TEXTO = "EL PLAN, otorgado en mi condición de socio de la entidad, será abonado en cuotas mensuales a través del descuento directo en la cuenta bancaria donde percibo mis haberes mensuales o en efectivo en depósito en la cuenta bancaria de la Mutual que me ha sido informada en esta fecha, o donde la Mutual lo indique en el futuro, y se regirá por las siguientes condiciones:";
    $TEXTO .= "\n";
    $TEXTO .= "1.- El préstamo se integra con PAGARE A LA VISTA, y autorización de descuento/ cobranza por C.B.U. a favor de la MUTUAL.";
    $TEXTO .= "\n";
    $TEXTO .= "2.- El PLAN devengará un interés compensatorio nominal anual (T.N.A.) más cuota societaria mensual más Gastos de Otorgamiento y cargo de cobranza bancaria, conformando el Costo Financiero Total (C.F.T.E.A.).- En todo caso la T.N.A. y el C.F.T.E.A. estarán dentro de los parámetros previstos por la normativa vigente.- El valor de cada cuota, separando el interés y la cuota de amortización de cada una, se consignan en el plan de cuotas cuya copia se entrega al socio al adherirse al Plan.";
    $TEXTO .= "\n";
    $TEXTO .= "3.- El socio podrá cancelar anticipadamente el Plan solicitado, descontando todos los intereses no vencidos al momento de la cancelación y sin ningún costo ni recargo por la pre-cancelación.-";
    $TEXTO .= "\n";
    $TEXTO .= "4.- En caso de amortización parcial de las cuotas la mora será automática por el solo vencimiento de los plazos, y devengará un interés moratorio equivalente al compensatorio, con más un interés punitorio igual al 50% de aquel, hasta la total cancelación de las sumas adeudadas. Los DEUDORES renuncian a hacer valer la presunción del art.746 C.C";
    $TEXTO .= "\n";
    $TEXTO .= "5.- El socio presta irrevocable conformidad para que la MUTUAL ceda la cobranza y/o el PLAN o constituya garantías sobre ellos, a través de cualquier procedimiento legalmente permitido, incluido el previsto en los arts. 70, 71 y 72, Ley 24.441. No se requerirá notificación fehaciente de la cesión al DEUDOR, la que será válida desde su celebración. El DEUDOR no puede ceder derechos y obligaciones del PLAN, bajo ningún concepto y forma.";
    $TEXTO .= "\n";
    $TEXTO .= "6.- El socio acuerda someterse a la jurisdicción de los Tribunales Ordinarios de la ciudad de Buenos Aires, constituyendo el domicilio indicado en la solicitud y acepta que será notificación fehaciente por Acta Notarial, Carta Documento o Telegrama.";
    $TEXTO .= "\n";
    $TEXTO .= "7.- Los pagos serán válidos en el domicilio de la MUTUAL o donde ésta lo designe por medio fehaciente. En caso de pagos por descuento en el CBU de haberes, las cuotas no se tendrán por canceladas y el deudor no quedará válidamente liberado hasta tanto el importe descontado sea recibido por la MUTUAL, no asumiendo ninguna responsabilidad al respecto.";
    $TEXTO .= "\n";
//    $TEXTO .= "9.- Reconozco y acepto que el único documento válido oponible a IDUN INVERSIONES S.A. para acreditar el pago del crédito y/o sus cuotas será el recibo auténtico emitido por la misma. Asimismo me comprometo a no solicitar a la Entidad Intermedia u Organismo autorizado a la retención sobre mis haberes, el cese o la suspensión de los descuentos a favor de IDUN INVERSIONES S.A., en tanto tenga deudas pendientes de pago por compromisos voluntariamente contraídos por mí.";
//    $TEXTO .= "\n";
//    $TEXTO .= "11.- Por la presente presto conformidad a que IDUN INVERSIONES S.A. venda/ceda y/o de cualquier forma transfiera el presente y/o los derechos emanados de este crédito, sin necesidad de que dicha venta, cesión y/o transferencia me fuera notificada de conformidad con lo establecido por los art. 70 y 72 de la ley 24.441.";
//    $TEXTO .= "\n";
//    $TEXTO .= "12.- Declaro bajo juramento que IDUN INVERSIONES S.A. me ha informado previamente que en cumplimiento con la Ley de Habeas Data y reglamentarias, mis datos personales y patrimoniales relacionados con la operación crediticia que contrato, podrán ser inmediatamente informados y registrados en la base de datos de las organizaciones de información crediticia, públicas y/o privadas. Por estos motivos, renuncio a realizar cualquier reclamo contra IDUN INVERSIONES S.A. con fundamento en las comunicaciones que ésta pudiese realizar / solicitar a las centrales de riesgo crediticio y/o al Banco Central de la República Argentina y puedan ser cedidos a los asociados abonados a éstas. Sin perjuicio de lo expuesto, como interesado podré ejercer mis derechos de acceso, rectificación y/o supresión de los datos aquí suministrados cuando los mismos no se correspondan con la realidad.";
//    $TEXTO .= "\n";
//    $TEXTO .= "13.- En forma expresa declaro someterme a la Jurisdicción y Competencia Judicial de los Tribunales Ordinarios de la Ciudad Autónoma de Buenos Aires, renunciando a cualquier otro Fuero o Jurisdicción.";
//    $TEXTO .= "\n";
//    $TEXTO .= "14.- Declaro expresamente haber recibido copia de la presente Solicitud de Crédito.";
    $TEXTO .= "\n";
    $TEXTO .= "\n";
    $TEXTO .= date('d', strtotime($orden['MutualProductoSolicitud']['fecha']))." de ".  $util->mesToStr(date('m', strtotime($orden['MutualProductoSolicitud']['fecha'])),true)." de ". date('Y', strtotime($orden['MutualProductoSolicitud']['fecha']));
    $TEXTO .= "\n";

    $PDF->MultiCell(0,0,$TEXTO,0,'J');

//    $PDF->SetLinea(24);
//    $PDF->SetFont(PDF_FONT_NAME_MAIN,'B',$DEFAULT_FONT_SIZE);
//    $PDF->Cell(190,$PDF->H,"DECLARACIÓN JURADA.",0,0,'L');
//
//    $PDF->SetLinea(25);
//    $PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE);
//    $TEXTO = "Manifiesto en carácter de Declaración Jurada que toda la información suministrada precedentemente es exacta. Certificamos que las firmas del solicitante consignadas en la presente corresponden a la persona cuyos datos se individualizan en la Solicitud, debidamente constatados con su documento y fueron realizadas en presencia de personas autorizadas.";
//    $TEXTO .= "\n";
//    $PDF->MultiCell(0,0,$TEXTO,0,'J');


    $PDF->SetLinea(17);
    $PDF->SetFont(PDF_FONT_NAME_MAIN,'',8);
    $PDF->Cell(50,$PDF->H,"Firma y sello IUTUM",'T',0,'C');
    $PDF->Cell(20,$PDF->H,"");
    $PDF->Cell(50,$PDF->H,"Firma y sello solicitante",'T',0,'C');
    $PDF->Cell(20,$PDF->H,"");
    $PDF->Cell(50,$PDF->H,"Aclaración del solicitante",'T',0,'C');    
}

function pagare($PDF,$DEFAULT_FONT_SIZE,$orden,$util){
//    $PDF->reset();
    $PDF->SetFillColor(220,220,220);
    $PDF->SetFont(PDF_FONT_NAME_MAIN,'B',16);
    $PDF->SetY(50);
    //$PDF->SetX(90);
    $PDF->Cell(190,5,"PAGARE",0,0,'C');


    $PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE  + 1);
    $PDF->SetX(10);

    $PDF->H = 8;
    $PDF->INI_Y = 60;

    $PDF->SetLinea(1);
    $PDF->Cell(0,5,"Ciudad Autónoma de Buenos Aires, ".date('d', strtotime($orden['MutualProductoSolicitud']['fecha']))." de ".  $util->mesToStr(date('m', strtotime($orden['MutualProductoSolicitud']['fecha'])),true)." de ". date('Y', strtotime($orden['MutualProductoSolicitud']['fecha'])),0,0,'R');

    $PDF->SetLinea(3);
    //$PDF->SetX(120);
    $PDF->Cell(150,$PDF->H,"Crédito N°",'',0,'R');
    $PDF->SetFont(PDF_FONT_NAME_MAIN,'B',$DEFAULT_FONT_SIZE  + 1);
    $PDF->Cell(40,$PDF->H,$orden['MutualProductoSolicitud']['id'],'TBLR',0,'C',1);

    $PDF->SetLinea(4);
    $PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE  + 1);
    $PDF->Cell(150,$PDF->H,"Por $",'',0,'R');
    $PDF->SetFont(PDF_FONT_NAME_MAIN,'B',$DEFAULT_FONT_SIZE  + 1);
    $PDF->Cell(40,$PDF->H,$orden['MutualProductoSolicitud']['importe_total'],'TBLR',0,'C',1);

    $PDF->SetLinea(6);
    $PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE  + 1);

    $TEXTO = "A la vista pagaré sin protesto (artículo 50 decreto ley 5965/63), a IUTUM o a su orden, la cantidad de pesos ".$orden['MutualProductoSolicitud']['total_letras']." ($ ".$orden['MutualProductoSolicitud']['importe_total']."), por igual valor recibido en efectivo a mi entera satisfacción, pagadero en la misma moneda.";
    $TEXTO .= "\n";
    $TEXTO .= "En mi carácter de librador, dejo expresa constancia de que amplío el plazo de presentación de este pagaré hasta 5 (cinco) años desde la fecha de libramiento del mismo (conf. Art. 36 decreto ley 5965/63).";
    $TEXTO .= "\n";
    $TEXTO .= "Acepto que, en caso de mora, la suma indicada devengará un interés punitorio mensual del 3% (tres por ciento), desde la fecha de la mora, y hasta su efectivo pago.";
    $TEXTO .= "\n";
    
    $TXT_PAGARE = "El ____ de _______________ de ______ PAGARE SIN PROTESTO (Art. 50 Decreto ";
    $TXT_PAGARE .= "Ley 5965/63) a ____________________________________ ";
    $TXT_PAGARE .= "o a su órden la cantidad de PESOS ".$orden['MutualProductoSolicitud']['total_letras']." por igual valor recibido ";
    $TXT_PAGARE .= "en efectivo a mi entera satisfacción, pagadero en _______________________________________.-\n";
    
    
    $PDF->MultiCell(0,0,$TXT_PAGARE,0,'J');


    $PDF->SetLinea(12);
    $PDF->SetX(20);
    $PDF->Cell(50,$PDF->H,"Firma Deudor",'T',0,'C');

    //$PDF->SetX(10);

    $PDF->SetLinea(14);
    //$PDF->SetX(10);
    $PDF->Cell(20,0,"Aclaración",0,0,'L');
    $PDF->Cell(100,0,"",'B',0);

    $PDF->SetLinea(15);
    $PDF->Cell(40,0,"Tipo y N° de documento",0,0,'L');
    $PDF->Cell(80,0,"",'B',0);


    $PDF->SetLinea(16);
    $PDF->Cell(17,0,"Domicilio",0,0,'L');
    $PDF->Cell(103,0,"",'B',0);

    $PDF->SetLinea(17);
    $PDF->Cell(17,0,"Localidad",0,0,'L');
    $PDF->Cell(103,0,"",'B',0);

    $PDF->SetLinea(20);
    $PDF->MultiCell(95,0,"Certifico que las firmas de este documento son auténticas y han sido puestas en mi presencia, teniendo a la vista documentos de identidad del firmante.\n",0,0,'J');
    $PDF->SetX(110);
    $PDF->Cell(80,$PDF->H,"Firma y sello del certificante",'T',0,'C');    
}


function cartaDeInstruccion($PDF,$DEFAULT_FONT_SIZE,$orden,$util){
    $PDF->reset();
    $PDF->SetFillColor(220,220,220);

    $PDF->SetFont(PDF_FONT_NAME_MAIN,'B',16);
    $PDF->SetY(40);
    //$PDF->SetX(90);
    $PDF->Cell(190,$PDF->H,"CARTA INSTRUCCIÓN y RECIBO DE LIQUIDACION",0,0,'C');

    $PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE);
    $PDF->SetX(15);

    $PDF->H = 8;
    $PDF->INI_Y = 50;

    $PDF->SetLinea(1);
    $PDF->Cell(0,5,"Ciudad Autónoma de Buenos Aires, ".date('d', strtotime($orden['MutualProductoSolicitud']['fecha']))." de ".  $util->mesToStr(date('m', strtotime($orden['MutualProductoSolicitud']['fecha'])),true)." de ". date('Y', strtotime($orden['MutualProductoSolicitud']['fecha'])),0,0,'R');

    $PDF->SetLinea(3);
    //$PDF->Cell(190,0,"Señores de \nIDUN INVERSIONES S.A.\nPresente",0,0,'L');

    $PDF->MultiCell(0,11,"Señores de \nIUTUM\nPresente\n");

    $PDF->SetLinea(5);

    $TEXTO = "Solicito por la presente que el neto de la ayuda financiera Mutual solicitada por su intermedio por el suscripto, me sea abonada de la siguiente manera:";
    $TEXTO .= "\n";
    $TEXTO .= "\n";

    if(!isset($orden['MutualProductoSolicitudInstruccionPago']) || empty($orden['MutualProductoSolicitudInstruccionPago'])){

        $TEXTO .= "a) A mi orden personal, el importe de $ ________________ (Pesos _______________________________________), mediante cheque o depósito en la cuenta bancaria cuyos comprobantes se acompañan.";
        $TEXTO .= "\n";
        $TEXTO .= "b) A la orden de ____________________________________________________________________________ sin importar causa ni motivo, la suma de $ ________________ (Pesos _______________________________________).";
        $TEXTO .= "\n";
        $TEXTO .= "c) Al momento de liquidar el Crédito autorizo a IUTUM a retener el monto suficiente para la cancelación de los gastos de comisiones, sellado, seguros de vida y desempleo, administración e impuestos.-";

    }else{
        $n = 1;
//        $TEXTO = "";
        foreach($orden['MutualProductoSolicitudInstruccionPago'] as $instruccion):
                $TEXTO .= "$n) " . trim($instruccion['a_la_orden_de']) . ", en concepto de " . $instruccion['concepto'] . " por un importe de PESOS $ " . $util->nf($instruccion['importe']) . ".-\n";
                $n++;
        endforeach;
        $TEXTO .= "$n) Al momento de liquidar el Crédito autorizo a IUTUM a retener el monto suficiente para la cancelación de los gastos de comisiones, sellado, seguros de vida y desempleo, administración e impuestos.-";
    }


    $TEXTO .= "\n";
    $TEXTO .= "\n";
    $TEXTO .= "Salúdales respetuosamente,";
    $TEXTO .= "\n";

    $PDF->MultiCell(0,11,$TEXTO,0,'J');



    $PDF->SetLinea(15);
    $PDF->SetFont(PDF_FONT_NAME_MAIN,'',8);
    $PDF->Cell(70,$PDF->H,"");
    $PDF->Cell(50,$PDF->H,"Firma del Solicitante",'T',0,'C');
    $PDF->Cell(20,$PDF->H,"");
    $PDF->Cell(50,$PDF->H,"Aclaración de Firma",'T',0,'C');


    $PDF->SetLinea(17);

    $PDF->SetFont(PDF_FONT_NAME_MAIN,'B',16);
    //$PDF->SetX(90);
    $PDF->Cell(190,$PDF->H,"RECIBO DEL IMPORTE NETO SOLICITADO",0,0,'C');

    $PDF->SetLinea(19);
    $PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE);
    $TEXTO = "Recibo de conformidad en este acto, la Liquidación Neta de Fondos a mi orden de la ayuda financiera Mutual solicitada, abonada por IUTUM en cheque, o a través de depósito o giro o transferencia bancaria (marcar opción elegida).-";
    $TEXTO .= "\n";

    $PDF->MultiCell(0,11,$TEXTO,0,'J');


    $PDF->SetLinea(23);
    $PDF->Cell(50,0,"Firma del Socio/Solicitante:",0,0,'L');
    $PDF->Cell(100,0,"",'B',0);

    $PDF->SetLinea(24);
    $PDF->Cell(15,0,"D.N.I.:",0,0,'L');
    $PDF->Cell(25,0,"",'B',0);
    $PDF->Cell(20,0,"Aclaración:",0,0,'L');
    $PDF->Cell(90,0,"",'B',0);    
}

function inscripcionSocio($PDF,$DEFAULT_FONT_SIZE,$orden,$util){
    
    $PDF->reset();
    
    $PDF->SetFont(PDF_FONT_NAME_MAIN,'B',16);
    $PDF->SetY(40);
    $PDF->SetX(10);
    $PDF->Cell(0,5,"SOLICITUD DE INSCRIPCION DE SOCIO",0,0,'C');


    $PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE);


    //$PDF->SetY(40);
    $PDF->SetX(10);

    $PDF->H = 8;
    $PDF->INI_Y = 35;

    $PDF->SetLinea(3);
    $PDF->Cell(0,5,"N° ". (!empty($orden['MutualProductoSolicitud']['socio_id']) ? $orden['MutualProductoSolicitud']['socio_id'] : "________"),0,0,'R');

    $PDF->SetLinea(4);
    //$PDF->Cell(190,0,"Señores de \nIDUN INVERSIONES S.A.\nPresente",0,0,'L');

    $PDF->MultiCell(0,11,"Al Señor Presidente del \nH.C.D. de IUTUM – S/D.\n");

    $PDF->SetLinea(6);

    $TEXTO = "SOLICITO por la presente mi inscripción como SOCIO de la Asociación, a la vez que autorizo a la Entidad a realizar los pertinentes descuentos de cuotas y servicios a través de la clave bancaria única (C.B.U.) de la cuenta donde percibo mis haberes.- Declaro asimismo conocer los Estatutos y Reglamentos de Servicios, aceptar lo dispuesto en los mismos, comprometiéndome a no renunciar a mi calidad de asociado hasta tanto haya cancelado todos los saldos con la Entidad, de cualquier índole, contraída con o por intermedio de la Asociación.-";
    $TEXTO .= "\n";
    $TEXTO .= "\n";
    $PDF->MultiCell(0,11,$TEXTO,0,'J');


    $PDF->SetLinea(10);
    $PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE);
    $PDF->Cell(35,$PDF->H,"Apellido y Nombre",'LTB');
    $PDF->Cell(155,$PDF->H,substr($orden['MutualProductoSolicitud']['beneficiario_apenom'],0,130),'BTR',0,'L');


    $PDF->SetLinea(11);
    $PDF->Cell(35,$PDF->H,"Fecha de Nacimiento",'LB');
    $PDF->Cell(20,$PDF->H,date('d/m/Y',strtotime($orden['MutualProductoSolicitud']['beneficiario_fecha_nacimiento'])),'B',0,'C');
    $PDF->Cell(20,$PDF->H,"DNI N°",'B',0,'L');
    $PDF->Cell(25,$PDF->H,$orden['MutualProductoSolicitud']['beneficiario_ndoc'],'B',0,'C');
    $PDF->Cell(42,$PDF->H,"Nacionalidad",'B',0,'L');
    $PDF->Cell(48,$PDF->H,"",'RB',0,'L');


    $PDF->SetLinea(12);
    $PDF->Cell(20,$PDF->H,"Domicilio",'LB');
    $PDF->Cell(80,$PDF->H,$orden['MutualProductoSolicitud']['beneficiario_calle'] . " " . $orden['MutualProductoSolicitud']['beneficiario_numero_calle'],'B');
    $PDF->Cell(10,$PDF->H,"CP",'B',0,'L');
    $PDF->Cell(80,$PDF->H,$orden['MutualProductoSolicitud']['beneficiario_cp'],'RB',0,'L');


    $PDF->SetLinea(13);
    $PDF->Cell(20,$PDF->H,"Localidad",'LB');
    $PDF->Cell(80,$PDF->H,$orden['MutualProductoSolicitud']['beneficiario_localidad'],'B');
    $PDF->Cell(20,$PDF->H,"Provincia",'B',0,'L');
    $PDF->Cell(70,$PDF->H,$orden['MutualProductoSolicitud']['beneficiario_provincia'],'RB',0,'L');


    $PDF->SetLinea(14);
    $PDF->Cell(20,$PDF->H,"Teléfono",'LB');
    $PDF->Cell(80,$PDF->H,$orden['MutualProductoSolicitud']['beneficiario_telefono_movil']." / " . $orden['MutualProductoSolicitud']['beneficiario_telefono_fijo'],'B');
    $PDF->Cell(25,$PDF->H,"CUIT / CUIL",'B',0,'L');
    $PDF->Cell(65,$PDF->H,$orden['MutualProductoSolicitud']['beneficiario_cuit_cuil'],'RB',0,'L');

    $PDF->SetLinea(18);
    $PDF->Cell(0,5,"Ciudad Autónoma de Buenos Aires, ".date('d', strtotime($orden['MutualProductoSolicitud']['fecha']))." de ".  $util->mesToStr(date('m', strtotime($orden['MutualProductoSolicitud']['fecha'])),true)." de ". date('Y', strtotime($orden['MutualProductoSolicitud']['fecha'])),0,0,'L');


    $PDF->SetLinea(20);
    $PDF->SetFont(PDF_FONT_NAME_MAIN,'',8);
    //$PDF->Cell(70,$PDF->H,"");
    //$PDF->Cell(50,$PDF->H,"Firma del Socio",'T',0,'C');
    $PDF->SetX(120);
    $PDF->Cell(20,$PDF->H,"");
    $PDF->Cell(50,$PDF->H,"Firma del Asociado",'T',0,'C');

    $PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE);



    $PDF->SetLinea(23);
    $PDF->MultiCell(0,$PDF->H,"CERTIFICO que la firma que antecede pertenece a: ".$orden['MutualProductoSolicitud']['beneficiario_apenom'] . " DNI N° " . $orden['MutualProductoSolicitud']['beneficiario_ndoc'] .".\n(Firmado y Sellado - día ___/___/______)\n",0,0,'J');
    $PDF->SetLinea(26);
    $PDF->SetX(120);
    $PDF->Cell(20,$PDF->H,"");
    $PDF->Cell(50,$PDF->H,"CERTIFICANTE",'T',0,'C');    
}

########################################################################################################################


encabezado($PDF);


$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',16);
$PDF->SetY(40);
$PDF->SetX(10);
$PDF->Cell(0,5,"SOLICITUD DE AYUDA FINANCIERA MUTUAL",0,0,'C');


$PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE);


//$PDF->SetY(40);
$PDF->SetX(10);

$PDF->H = 8;
$PDF->INI_Y = 35;

$PDF->SetLinea(3);
$PDF->Cell(0,5,"Ciudad Autónoma de Buenos Aires, ".date('d', strtotime($orden['MutualProductoSolicitud']['fecha']))." de ".  $util->mesToStr(date('m', strtotime($orden['MutualProductoSolicitud']['fecha'])),true)." de ". date('Y', strtotime($orden['MutualProductoSolicitud']['fecha'])),0,0,'R');

$PDF->SetLinea(4);
$PDF->Cell(25,$PDF->H,"N° CREDITO",'LTB');
$PDF->Cell(30,$PDF->H,$orden['MutualProductoSolicitud']['id'],'TB',0,'C');
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE);
$PDF->Cell(25,$PDF->H,"N° LEGAJO",'TB');
$PDF->Cell(20,$PDF->H,"",'TB');
$PDF->Cell(42,$PDF->H,"",'TB',0,'L',0);
//$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',$DEFAULT_FONT_SIZE);
$PDF->Cell(48,$PDF->H,"",'RTB',0,'L',0);

$PDF->SetLinea(5);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE);
$PDF->Cell(30,$PDF->H,"Capital Solicitado",'LB');
$PDF->Cell(25,$PDF->H,$orden['MutualProductoSolicitud']['importe_solicitado'],'B',0,'C');
$PDF->Cell(33,$PDF->H,"Cantidad de Cuotas",'B');
$PDF->Cell(12,$PDF->H,$orden['MutualProductoSolicitud']['cuotas'],'B',0,'C');
$PDF->Cell(25,$PDF->H,"Importe cuota",'B',0,'L',0);
$PDF->Cell(65,$PDF->H,$orden['MutualProductoSolicitud']['importe_cuota'],'BR',0,'L');

$PDF->SetLinea(6);
$PDF->Cell(45,$PDF->H,"Beneficio/Legajo/Clave N°",'LB');
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',7);
$PDF->Cell(145,$PDF->H,substr($orden['MutualProductoSolicitud']['beneficio_str'],0,104),'BR',0,'L');

$PDF->SetLinea(7);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE);
$PDF->Cell(35,$PDF->H,"Apellido y Nombre",'LB');
$PDF->Cell(155,$PDF->H,substr($orden['MutualProductoSolicitud']['beneficiario_apenom'],0,130),'BR',0,'L');


$PDF->SetLinea(8);
$PDF->Cell(35,$PDF->H,"Fecha de Nacimiento",'LB');
$PDF->Cell(20,$PDF->H,date('d/m/Y',strtotime($orden['MutualProductoSolicitud']['beneficiario_fecha_nacimiento'])),'B',0,'C');
$PDF->Cell(20,$PDF->H,"DNI N°",'B',0,'L');
$PDF->Cell(25,$PDF->H,$orden['MutualProductoSolicitud']['beneficiario_ndoc'],'B',0,'C');
$PDF->Cell(42,$PDF->H,"Nacionalidad",'B',0,'L');
$PDF->Cell(48,$PDF->H,"",'RB',0,'L');


$PDF->SetLinea(9);
$PDF->Cell(20,$PDF->H,"Domicilio",'LB');
$PDF->Cell(80,$PDF->H,$orden['MutualProductoSolicitud']['beneficiario_calle'] . " " . $orden['MutualProductoSolicitud']['beneficiario_numero_calle'],'B');
$PDF->Cell(10,$PDF->H,"CP",'B',0,'L');
$PDF->Cell(80,$PDF->H,$orden['MutualProductoSolicitud']['beneficiario_cp'],'RB',0,'L');


$PDF->SetLinea(10);
$PDF->Cell(20,$PDF->H,"Localidad",'LB');
$PDF->Cell(80,$PDF->H,$orden['MutualProductoSolicitud']['beneficiario_localidad'],'B');
$PDF->Cell(20,$PDF->H,"Provincia",'B',0,'L');
$PDF->Cell(70,$PDF->H,$orden['MutualProductoSolicitud']['beneficiario_provincia'],'RB',0,'L');


$PDF->SetLinea(11);
$PDF->Cell(20,$PDF->H,"Teléfono",'LB');
$PDF->Cell(80,$PDF->H,$orden['MutualProductoSolicitud']['beneficiario_telefono_movil']." / " . $orden['MutualProductoSolicitud']['beneficiario_telefono_fijo'],'B');
$PDF->Cell(25,$PDF->H,"CUIT / CUIL",'B',0,'L');
$PDF->Cell(65,$PDF->H,$orden['MutualProductoSolicitud']['beneficiario_cuit_cuil'],'RB',0,'L');


$PDF->SetLinea(13);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',$DEFAULT_FONT_SIZE);
$PDF->Cell(190,$PDF->H,"DATOS LABORALES",0,0,'C');

$PDF->SetLinea(14);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE);
$PDF->Cell(25,$PDF->H,"Cargo/Grado",'LTB');
$PDF->Cell(50,$PDF->H,"",'TB');
$PDF->Cell(35,$PDF->H,"Empresa/Organismo",'TB');
$PDF->Cell(80,$PDF->H,substr($orden['MutualProductoSolicitud']['turno_desc'],0,40),'RTB');


$PDF->SetLinea(15);
$PDF->Cell(30,$PDF->H,"Fecha de Ingreso",'LTB');
$PDF->Cell(45,$PDF->H,date('d/m/Y', strtotime($orden['MutualProductoSolicitud']['beneficio_ingreso'])),'TB');
$PDF->Cell(35,$PDF->H,"Teléfono Laboral",'TB');
$PDF->Cell(80,$PDF->H,"",'RTB');
        
$PDF->SetLinea(16);
$PDF->Cell(190,$PDF->H,"Ingresos Mensuales: " . ( isset($orden['MutualProductoSolicitud']['sueldo_neto']) ? "$ " . $util->nf($orden['MutualProductoSolicitud']['sueldo_neto']) : "" ),'LRTB');

$PDF->SetLinea(18);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',12);
$TEXTO = "Por la presente en mi carácter de socio de la Mutual solicito un PLAN DE AYUDA FINANCIERA de $ ".$orden['MutualProductoSolicitud']['importe_solicitado']." (pesos ".$orden['MutualProductoSolicitud']['total_importe_solicitado_letras']."). Obligándome en este acto a restituirlo en ".$orden['MutualProductoSolicitud']['cuotas']." (".$orden['MutualProductoSolicitud']['cantidad_cuota_letras'].") cuotas iguales, mensuales y consecutivas, en adelante las \"CUOTAS\" de $ ".$orden['MutualProductoSolicitud']['importe_cuota']." (pesos ".$orden['MutualProductoSolicitud']['total_cuota_letras'].") cada una, con vencimiento la primera de ellas con los haberes del mes de ".(!empty($orden['MutualProductoSolicitud']['inicia_en']) ? $orden['MutualProductoSolicitud']['inicia_en'] : "________/_____")." y las restantes con los haberes de los meses subsiguientes. Todas y cada una de las cuotas se calcularán bajo el régimen de amortización Francés y se cancelarán por sistema de descuento de haberes. .";
$TEXTO .= "\n";
$TEXTO .= "Sobre el Capital adeudado abonaré una tasa de interés fija del ".(!empty($orden['MutualProductoSolicitud']['tna']) ? $orden['MutualProductoSolicitud']['tna'] : "______")."% nominal anual (tasa efectiva mensual ".(!empty($orden['MutualProductoSolicitud']['tna']) ? $orden['MutualProductoSolicitud']['tnm'] : "______")."%).";
$TEXTO .= "\n";
$TEXTO .= "Así mismo declaro conocer que al momento de la liquidación se me deducirán las sumas para cancelar los siguientes conceptos: Gastos Administrativos y Comerciales más IVA, sellados, impuestos y un Seguro de Vida (En caso de fallecimiento mis derechohabientes quedarán liberados de la obligación contraída con IUTUM, una vez entregado a ésta el correspondiente Certificado de Defunción).-";
$TEXTO .= "\n";
$PDF->MultiCell(0,0,$TEXTO,0,'J');


$PDF->SetLinea(30);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',8);
$PDF->Cell(50,$PDF->H,"Firma y sello IUTUM",'T',0,'C');
$PDF->Cell(20,$PDF->H,"");
$PDF->Cell(50,$PDF->H,"Firma y sello solicitante",'T',0,'C');
$PDF->Cell(20,$PDF->H,"");
$PDF->Cell(50,$PDF->H,"Aclaración del solicitante",'T',0,'C');


//pie($PDF, $DEFAULT_FONT_SIZE);

//$PDF->SetY(-10);
//$PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE);
//$PDF->Cell(190,$PDF->H,TEXTO_PIE,'T',0,'C');

################################################################################################################
# CONDICIONES GENERALES
################################################################################################################

$PDF->AddPage();
condicionesGenerales($PDF,$DEFAULT_FONT_SIZE,$orden,$util);


################################################################################################################
# SOLICITUD DE AFILIACION
################################################################################################################

$PDF->AddPage();
encabezado($PDF);
inscripcionSocio($PDF,$DEFAULT_FONT_SIZE,$orden,$util);

################################################################################################################
# PAGARE IUTUM
################################################################################################################
$PDF->AddPage();
encabezado($PDF);
pagare($PDF,$DEFAULT_FONT_SIZE,$orden,$util);


################################################################################################################
# CARTA INSTRUCCION IDUN
################################################################################################################
$PDF->AddPage();
encabezado($PDF);
cartaDeInstruccion($PDF,$DEFAULT_FONT_SIZE,$orden,$util);


################################################################################################################
# Sujeto Obligado conforme Ley 25.246 y modificatorias
################################################################################################################
$PDF->AddPage();
$PDF->reset();
encabezado($PDF);
$PDF->SetFillColor(220,220,220);

//$PDF->image(__DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "idun.png",5,5,80);
//$PDF->SetY(25);
//$PDF->Cell(0,5,"Matrícula 1361 SF-INACyM",0);
//$PDF->SetX(10);
//$PDF->SetY(28);
//$PDF->Cell(0,5,"(Actualmente INAES)",0);

$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',16);
$PDF->SetY(35);
//$PDF->SetX(90);
$PDF->Cell(190,$PDF->H,"Sujeto Obligado conforme Ley 25.246 y modificatorias",0,0,'C');

$PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE);
$PDF->SetX(15);

$PDF->H = 8;
$PDF->INI_Y = 45;
$DEFAULT_FONT_SIZE = 11;
$PDF->SetLinea(1);

$PDF->Cell(0,$PDF->H,"De acuerdo a lo establecido en el art. 20 de la ley 25.246 2 declaro lo siguiente:",0,0,'L');
$PDF->SetLinea(2);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',$DEFAULT_FONT_SIZE);
$PDF->Cell(190,$PDF->H,"La actividad principal por la cual obtengo mis ingresos es:",'LTR',0,'C');
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE);
$PDF->SetLinea(3);
$PDF->Cell(190,$PDF->H,"AGENTE ESTATAL ACTIVO / PASIVO",'LBR',0,'C');
$PDF->SetLinea(4);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',$DEFAULT_FONT_SIZE);
$PDF->Cell(95,$PDF->H*2,"Por lo tanto,",'L',0,'C');
$PDF->Cell(95,$PDF->H*2,"SI ( ) soy SUJETO OBLIGADO",'LR',0,'C');
$PDF->SetLinea(5);
$PDF->Cell(95,$PDF->H*2,"",'LB',0,'C');
$PDF->Cell(95,$PDF->H*2,"NO (X) soy SUJETO OBLIGADO",'LBR',0,'C');

$PDF->SetLinea(8);
$PDF->Cell(0,5,"Solo para aquellos que son Sujetos Obligados:",0,0,'L');


$PDF->SetLinea(9);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE);

$TEXTO = "En cumplimiento con lo establecido en el artículo 21, inciso k, de la Resolución 121/2011 de la Unidad de Información Financiera, declaro bajo juramento que doy debida observancia a las disposiciones vigentes en materia de Prevención del Lavado de Activos y Financiación del Terrorismo, por lo cual:";
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "* Tengo conocimiento del alcance y propósitos establecidos por la Ley 25.246, sus normas modificatorias y complementarias, en las resoluciones emitidas por la Unidad de Información Financiera y demás disposiciones vigentes en materia de Prevención de Lavado de Dinero y Financiación del Terrorismo, y que cumplo con la mencionada normativa.";
$TEXTO .= "\n";
$TEXTO .= "* Tengo conocimiento de la responsabilidad, como Sujeto Obligado, de informar a la Unidad de Información Financiera la existencia de Operaciones Sospechosas.";
$TEXTO .= "\n";
$TEXTO .= "* Tengo conocimiento de la obligación de presentarles la correspondiente constancia de inscripción ante la UIF (Resol. 3/14 UIF)";
$TEXTO .= "\n";

$PDF->MultiCell(0,11,$TEXTO,0,'J');

$PDF->SetLinea(17);

$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',$DEFAULT_FONT_SIZE);
$PDF->Cell(0,$PDF->H,"ADJUNTO CONSTANCIA DE INSCRIPCION ANTE LA UIF:",'LRTB',0,'C');
$PDF->SetLinea(18);
$PDF->Cell(95,$PDF->H,"SI ( )",'LB',0,'C');
$PDF->Cell(95,$PDF->H,"NO (X)",'LBR',0,'C');

$PDF->SetLinea(20);
$PDF->Cell(0,$PDF->H,"1) Declaración Jurada Personas Expuestas Políticamente (PEP)",0,0,'L');

$PDF->SetLinea(21);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE);

$TEXTO = "Declaro bajo juramento que NO me encuentro incluido/a y/o alcanzado/a dentro de la “Nómina de Funciones de Personas Expuestas Políticamente” aprobada por la Unidad de Información Financiera, cuyo texto he leído y suscripto.";
$TEXTO .= "\n";
$TEXTO .= "En caso afirmativo, indicar detalladamente el motivo (Cargo/ Función/ Jerarquía, o relación con la Persona Expuesta Políticamente).";
$TEXTO .= "\n";
$TEXTO .= "_______________________________________________________________________________";

$PDF->MultiCell(0,11,$TEXTO,0,'J');

$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',$DEFAULT_FONT_SIZE);
$PDF->SetLinea(25);
$TEXTO = "Los campos Familiares Directos y Personas Jurídicas Vinculadas deben ser completados solamente por aquellos clientes titulares del Cargo o función.";
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "Familiares directos (Nombre y DNI) 3";
$TEXTO .= "\n";
$TEXTO .= "_______________________________________________________________________________";
$TEXTO .= "\n";
$TEXTO .= "_______________________________________________________________________________";
$TEXTO .= "\n";
$TEXTO .= "_______________________________________________________________________________";
$TEXTO .= "\n";
$TEXTO .= "_______________________________________________________________________________";
$TEXTO .= "\n";
$TEXTO .= "_______________________________________________________________________________";
$TEXTO .= "\n";
$TEXTO .= "_______________________________________________________________________________";
$TEXTO .= "\n";
$TEXTO .= "_______________________________________________________________________________";
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "Personas jurídicas vinculadas (Nombre, CUIT y % de mi participación)";
$TEXTO .= "\n";
$TEXTO .= "_______________________________________________________________________________";
$TEXTO .= "\n";
$TEXTO .= "_______________________________________________________________________________";
$TEXTO .= "\n";
$TEXTO .= "_______________________________________________________________________________";
$TEXTO .= "\n";
$TEXTO .= "_______________________________________________________________________________";
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "Además, asumo el compromiso de informar cualquier modificación que se produzca a este respecto, dentro de los 30 días de ocurrida, mediante la presentación de una nueva declaración jurada.";
$TEXTO .= "\n";

$PDF->MultiCell(0,11,$TEXTO,0,'J');


$PDF->SetFont(PDF_FONT_NAME_MAIN,'',$DEFAULT_FONT_SIZE);
//$PDF->SetLinea(20);
$TEXTO = "DECLARO BAJO JURAMENTO QUE LOS DATOS CONSIGNADOS EN ESTA SOLICITUD SON CORRECTOS Y CIERTOS Y ME OBLIGO A NOTIFICAR AL BANCO POR ESCRITO CUALQUIER MODIFICACION DE LOS MISMOS.";
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "Nombre y Apellido: " . substr($orden['MutualProductoSolicitud']['beneficiario_apenom'],0,130);
$TEXTO .= "\n";
$TEXTO .= "CUIT / CUIL / CDI N°: ".$orden['MutualProductoSolicitud']['beneficiario_cuit_cuil'];
$TEXTO .= "\n";
$TEXTO .= "Documento: Tipo: " . $orden['MutualProductoSolicitud']['beneficiario_tdoc'] . " N°: " . $orden['MutualProductoSolicitud']['beneficiario_ndoc'];
$TEXTO .= "\n";
$TEXTO .= "Lugar y Fecha: _________________________ , " .date('d', strtotime($orden['MutualProductoSolicitud']['fecha']))." de ".  $util->mesToStr(date('m', strtotime($orden['MutualProductoSolicitud']['fecha'])),true)." de ". date('Y', strtotime($orden['MutualProductoSolicitud']['fecha']));
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "Firma y Aclaración_________________________________________________________";
$TEXTO .= "\n";


$PDF->MultiCell(0,11,$TEXTO,0,'J');

$PDF->AddPage();
$PDF->reset();

$PDF->H = 8;
$PDF->INI_Y = 10;

$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',7);
$TEXTO = "2* Artículo 20 - Ley 25246, modificado por Artículo 15 – Ley 26.683 :"; 
$TEXTO .= "\n";
$PDF->MultiCell(0,0,$TEXTO,0,'J');


$PDF->SetFont(PDF_FONT_NAME_MAIN,'',7);

$TEXTO = "Están obligados a informar a la Unidad de Información Financiera (UIF), en los términos del artículo 21 de la presente ley: 1. Las entidades financieras sujetas al régimen de la ley 21.526 y modificatorias. 2. Las entidades sujetas al régimen de la ley 18.924 y modificatorias y las personas físicas o jurídicas autorizadas por el Banco Central de la República Argentina para operar en la compraventa de divisas bajo forma de dinero o de cheques extendidos en divisas o mediante el uso de tarjetas de crédito o pago, o en la transmisión de fondos dentro y fuera del territorio nacional. 3. Las personas físicas o jurídicas que como actividad habitual exploten juegos de azar. 4. Los agentes y sociedades de bolsa, sociedades gerente de fondos comunes de inversión, agentes de mercado abierto electrónico, y todos aquellos intermediarios en la compra, alquiler o préstamo de títulos valores que operen bajo la órbita de bolsas de comercio con o sin mercados adheridos. 5. Los agentes intermediarios inscriptos en los mercados de futuros y opciones cualquiera sea su objeto. 6. Los registros públicos de comercio, los organismos representativos de fiscalización y control de personas jurídicas, los registros de la propiedad inmueble, los registros de la propiedad automotor, los registros prendarios, los registros de embarcaciones de todo tipo y los registros de aeronaves. 7. Las personas físicas o jurídicas dedicadas a la compraventa de obras de arte, antigüedades u otros bienes suntuarios, inversión filatélica o numismática, o a la exportación, importación, elaboración o industrialización de joyas o bienes con metales o piedras preciosas. 8. Las empresas aseguradoras. 9. Las empresas emisoras de cheques de viajero u operadoras de tarjetas de crédito o de compra. 10. Las empresas dedicadas al transporte de caudales. 11. Las empresas prestatarias o concesionarias de servicios postales que realicen operaciones de giros de divisas o de traslado de distintos tipos de moneda o billete. 12. Los escribanos públicos. 13. Las entidades comprendidas en el artículo 9o de la ley 22.315. 14. Los despachantes de aduana definidos en el artículo 36 y concordantes del Código Aduanero (ley 22.415 y modificatorias). 15. Los organismos de la Administración Pública y entidades descentralizadas y/o autárquicas que ejercen funciones regulatorias, de control, supervisión y/o superintendencia sobre actividades económicas y/o negocios jurídicos y/o sobre sujetos de derecho, individuales o colectivos: el Banco Central de la República Argentina, la Administración Federal de Ingresos Públicos, la Superintendencia de Seguros de la Nación, la Comisión Nacional de Valores, la Inspección General de Justicia, el Instituto Nacional de Asociativismo y Economía Social y el Tribunal Nacional de Defensa de la Competencia; 16. Los productores, asesores de seguros, agentes, intermediarios, peritos y liquidadores de seguros cuyas actividades estén regidas por las leyes 20.091 y 22.400, sus modificatorias, concordantes y complementarias; 17. Los profesionales matriculados cuyas actividades estén reguladas por los consejos profesionales de ciencias económicas; 18. Igualmente están obligados al deber de informar todas las personas jurídicas que reciben donaciones o aportes de terceros; 19. Los agentes o corredores inmobiliarios matriculados y las sociedades de cualquier tipo que tengan por objeto el corretaje inmobiliario, integradas y/o administradas exclusivamente por agentes o corredores inmobiliarios matriculados; 20. Las asociaciones mutuales y cooperativas reguladas por las leyes 20.321 y 20.337 respectivamente; 21. Las personas físicas o jurídicas cuya actividad habitual sea la compraventa de automóviles, camiones, motos, ómnibus y microómnibus, tractores, maquinaria agrícola y vial, naves, yates y similares, aeronaves y aerodinos. 22. Las personas físicas o jurídicas que actúen como fiduciarios, en cualquier tipo de fideicomiso y las personas físicas o jurídicas titulares de o vinculadas, directa o indirectamente, con cuentas de fideicomisos, fiduciantes y fiduciarios en virtud de contratos de fideicomiso. 23. Las personas jurídicas que cumplen funciones de organización y regulación de los deportes profesionales."; 
$TEXTO .= "\n";
$PDF->MultiCell(0,0,$TEXTO,0,'J');


$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',7);
$TEXTO = "Resolución 121/2011 de la Unidad de Información Financiera - Art. 21.:"; 
$TEXTO .= "\n";
$PDF->MultiCell(0,0,$TEXTO,0,'J');


$PDF->SetFont(PDF_FONT_NAME_MAIN,'',7);

$TEXTO = "Los Sujetos Obligados deberán: a) En todos los casos adoptar medidas adicionales razonables, a fin de identificar al beneficiario final y verificar su identidad. Asimismo, se deberá verificar que los clientes no se encuentren incluidos en los listados de terroristas y/u organizaciones terroristas de conformidad con lo prescripto en la Resolución UIF vigente en la materia. b) Cuando existan elementos que lleven a suponer que los clientes no actúan por cuenta propia, obtener información adicional sobre la verdadera identidad de la persona (titular/cliente final o real) por cuenta de la cual actúa y tomar medidas razonables para verificar su identidad. c) Prestar atención para evitar que las personas físicas utilicen personas de existencia ideal como un método para realizar sus operaciones. d) Evitar operar con personas de existencia ideal que simulen desarrollar una actividad comercial o una actividad sin fines de lucro. e) En los casos de Fideicomisos identificar a los fiduciarios, fiduciantes, beneficiarios y fideicomisarios, aplicándose los requisitos de identificación previstos en los artículos que anteceden. Cuando se trate de fideicomisos que no sean financieros y/o no cuenten con autorización para la oferta pública, deberá adicionalmente determinarse el origen de los bienes fideicomitidos y de los fondos de los beneficiarios. f) Los Sujetos Obligados sólo podrán realizar transacciones a distancia con personas previamente incorporadas como clientes. En esos casos deberán dar cumplimiento a las medidas específicas establecidas por el BANCO CENTRAL DE LA REPUBLICA ARGENTINA en la materia. g) En las transferencias electrónicas, ya sean nacionales o extranjeras, los Sujetos Obligados deberán  recabar información precisa del remitente y receptor de la operación y de los mensajes relacionados. La información deberá permanecer con la transferencia, a través de la cadena de pagos";  
$TEXTO .= "\n";
$TEXTO .= "En el supuesto de tratarse de fondos provenientes de otro Sujeto Obligado alcanzado por la presente normativa se presume que se verificó el principio de “conozca a su cliente”.En el caso de fondos provenientes de una entidad financiera del exterior que se acrediten en las cuentas de corresponsalía de la entidad local, se presume que aquella entidad verificó el principio de “conozca a su cliente”.Dichas presunciones no relevan al Sujeto Obligado de cumplimentar los requisitos de identificación y conocimiento del cliente, establecidos en estas normas, respecto de los clientes destinatarios de los fondos. Las transferencias y los giros desde y hacia el exterior así como las transferencias locales deberán ajustarse a la normativa emitida por el BANCO CENTRAL DE LA REPUBLICA ARGENTINA en esa materia."; 
$TEXTO .= "\n";
$TEXTO .= "El Sujeto Obligado deberá adoptar todos los recaudos necesarios al momento de incorporar los datos del ordenante de las transferencias de fondos, para asegurarse que la información sea completa y exacta.";
$TEXTO .= "\n";
$TEXTO .= str_replace("\n","","h) Prestar especial atención al riesgo que implican las relaciones comerciales y operaciones relacionadas con países o territorios
donde no se aplican, o no se aplican suficientemente, las Recomendaciones del Grupo de Acción Financiera Internacional. A estos
efectos se deberá considerar como países o territorios declarados no cooperantes a los catalogados por el Grupo de Acción
Financiera Internacional (www.fatf-gafi.org). En igual sentido deberán tomarse en consideración las relaciones comerciales y
operaciones relacionadas con países o territorios calificados como de baja o nula tributación (“paraísos fiscales”) según los términos
del Decreto No 1037/00 y sus modificatorios, respecto de las cuales deben aplicarse medidas de debida diligencia reforzadas. i)
Observar los requisitos establecidos en las normas sobre “Cuentas de corresponsalía”, emitidas por el BANCO CENTRAL DE LA
REPUBLICA ARGENTINA. j) Establecer un seguimiento reforzado sobre los depósitos en efectivo que reciban, evaluando que se
ajusten al perfil de riesgo del/los titulares de la cuenta y en función de la política de “conozca a su cliente” que hayan implementado.
En los casos de depósitos en efectivo por importes iguales o superiores a la suma de pesos cuarenta mil ($ 40.000) o su equivalente
en otras monedas, deberán identificar a la persona que efectúe el depósito, mediante la presentación de su documento (según lo
establecido en el artículo 13 inciso e) de esta Resolución) e ingresar nombre, tipo y número de documento en el registro respectivo
del depósito. El Sujeto Obligado interviniente deberá dejar constancia, a base de la declaración del presentante y conforme al
procedimiento que determine, si el depósito es realizado por sí o por cuenta de un tercero. En este último caso se deberá indicar el
nombre y/o denominación social por cuenta de quien se efectúa el depósito y su tipo y número de documento o clave de
identificación fiscal (CUIT, CUIL o CDI), según corresponda. .La responsabilidad del Sujeto Obligado en relación con la
identificación a que se refiere el párrafo precedente se limita a identificar a la persona interviniente en el depósito, a recibir lainformación sobre por cuenta de quien es efectuado el depósito y a obtener los datos requeridos, según lo establecido
anteriormente. .Aquellos depósitos que se realicen utilizando algún medio de identificación con clave provisto previamente por el
Sujeto Obligado al depositante, tales como tarjetas magnéticas, o los efectuados en cuentas recaudadoras, quedarán exceptuados del
procedimiento de identificación de la persona que lo efectúa, debiendo no obstante registrarse por cuenta de quien es efectuado
dicho depósito, en los casos que sea aplicable. k) Al operar con otros Sujetos Obligados deberán solicitar a los mismos una
declaración jurada sobre el cumplimiento de las disposiciones vigentes en materia de prevención del Lavado de Activo y
Financiación del Terrorismo. l) En caso de divergencia entre las normas vigentes en nuestro país, respecto de las de otros países, con
relación a la aplicación de las medidas a que se refiere la presente Resolución, las sucursales y subsidiarias en el exterior deberán
aplicar el estándar más alto.");
$TEXTO .= "\n";



$PDF->MultiCell(0,0,$TEXTO,0,'J');


$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',7);
$TEXTO = "3 Resolución 11/2011 de la Unidad de Información Financiera, según texto de la Resolución 52/2012 Unidad Información Financiera:"; 
$TEXTO .= "\n";
$TEXTO .= "Artículo 1o — Son personas políticamente expuestas las siguientes:";
$TEXTO .= "\n";
$PDF->MultiCell(0,0,$TEXTO,0,'J');


$PDF->SetFont(PDF_FONT_NAME_MAIN,'',7);

$TEXTO = str_replace("\n","","a) Los funcionarios públicos extranjeros: quedan comprendidas las personas que desempeñen o hayan desempeñado dichas
funciones hasta dos años anteriores a la fecha en que fue realizada la operatoria, ocupando alguno de los siguientes cargos: 1. Jefes
de Estado, jefes de Gobierno, gobernadores, intendentes, ministros, secretarios y subsecretarios de Estado y otros cargos
gubernamentales equivalentes; 2. Miembros del Parlamento/Poder Legislativo; 3. Jueces, miembros superiores de tribunales y otras
altas instancias judiciales y administrativas de ese ámbito del Poder Judicial; 4. Embajadores y cónsules; 5. Oficiales de alto rango
de las fuerzas armadas (a partir de coronel o grado equivalente en la fuerza y/o país de que se trate) y de las fuerzas de seguridad
pública (a partir de comisario o rango equivalente según la fuerza y/o país de que se trate); 6. Miembros de los órganos de dirección
y control de empresas de propiedad estatal; 7. Directores, gobernadores, consejeros, síndicos o autoridades equivalentes de bancos
centrales y otros organismos estatales de regulación y/o supervisión; b) Los cónyuges, o convivientes reconocidos legalmente,
familiares en línea ascendiente o descendiente hasta el primer grado de consanguinidad y allegados cercanos de las personas a que
se refieren los puntos 1 a 7 del artículo 1o, inciso a), durante el plazo indicado. A estos efectos, debe entenderse como allegado
cercano a aquella persona pública y comúnmente conocida por su íntima asociación a la persona definida como Persona Expuesta
Políticamente en los puntos precedentes, incluyendo a quienes están en posición de realizar operaciones por grandes sumas de
dinero en nombre de la referida persona. c) Los funcionarios públicos nacionales que a continuación se señalan que se desempeñen
o hayan desempeñado hasta dos años anteriores a la fecha en que fue realizada la operatoria: 1. El Presidente y Vicepresidente de la
Nación; 2. Los Senadores y Diputados de la Nación; 3. Los magistrados del Poder Judicial de la Nación; 4. Los magistrados del
Ministerio Público de la Nación; 5. El Defensor del Pueblo dela Nación y los adjuntos del Defensor del Pueblo; 6. El Jefe de
Gabinete de Ministros, los Ministros, Secretarios y Subsecretarios del Poder Ejecutivo Nacional; 7. Los interventores federales; 8-
El Síndico General de la Nación y los Síndicos Generales Adjuntos de la Sindicatura General de la Nación, el presidente y los
auditores generales de la Auditoría General de la Nación, las autoridades superiores de los entes reguladores y los demás órganos
que integran los sistemas de control del sector público nacional, y los miembros de organismos jurisdiccionales administrativos; 9.
Los miembros del Consejo de la Magistratura y del Jurado de Enjuiciamiento; 10. Los Embajadores y Cónsules; 11. El personal de
las Fuerzas Armadas, de la Policía Federal Argentina, de Gendarmería Nacional, de la Prefectura Naval Argentina, del Servicio
Penitenciario Federal y de la Policía de Seguridad Aeroportuaria con jerarquía no menor de coronel o grado equivalente según la
fuerza; 12. Los Rectores, Decanos y Secretarios de las Universidades Nacionales; 13. Los funcionarios o empleados con categoría o
función no inferior a la de director general o nacional, que presten servicio en la Administración Pública Nacional, centralizada o
descentralizada, las entidades autárquicas, los bancos y entidades financieras del sistema oficial, las obras sociales administradas por
el Estado, las empresas del Estado, las sociedades del Estado y el personal con similar categoría o función, designado a propuesta
del Estado en las sociedades de economía mixta, en las sociedades anónimas con participación estatal y en otros entes del sector
público; 14. Todo funcionario o empleado público encargado de otorgar habilitaciones administrativas para el ejercicio de cualquier
actividad, como también todo funcionario o empleado público encargado de controlar el funcionamiento de dichas actividades o de
ejercer cualquier otro control en virtud de un poder de policía; 15. Los funcionarios que integran los organismos de control de los
servicios públicos privatizados, con categoría no inferior a la de director general o nacional; 16. El personal que se desempeña en el
Poder Legislativo de la Nación, con categoría no inferior a la de director; 17. El personal que cumpla servicios en el Poder Judicial
de la Nación y en el Ministerio Público de la Nación, con categoría no inferior a Secretario; 18. Todo funcionario o empleado
público que integre comisiones de adjudicación de licitaciones, de compra o de recepción de bienes, o participe en la toma de
decisiones de licitaciones o compras; 19. Todo funcionario público que tenga por función administrar un patrimonio público o
privado, o controlar o fiscalizar los ingresos públicos cualquiera fuera su naturaleza; 20. Los directores y administradores de las
entidades sometidas al control externo del Honorable Congreso de la Nación, de conformidad con lo dispuesto en el artículo 120 de
la Ley No 24.156. d) Los funcionarios públicos provinciales, municipales y de la Ciudad Autónoma de Buenos Aires que a
continuación se señalan, que se desempeñen o hayan desempeñado hasta dos años anteriores a la fecha en que fue realizada la
operatoria: 1. Gobernadores, Intendentes y Jefe de Gobierno de la Ciudad Autónoma de Buenos Aires; 2. Ministros de Gobierno,
Secretarios y Subsecretarios; Ministros de los Tribunales Superiores de Justicia de las provincias y de la Ciudad Autónoma de
Buenos Aires; 3. Jueces y Secretarios de los Poderes Judiciales Provinciales y de la Ciudad Autónoma de Buenos Aires;4.
Legisladores provinciales, municipales y de la Ciudad Autónoma de Buenos Aires; 5. Los miembros del Consejo de la Magistratura
y del Jurado de Enjuiciamiento; 6. Máxima autoridad de los Organismos de Control y de los entes autárquicos provinciales,
municipales y de la Ciudad Autónoma de Buenos Aires; 7 Máxima autoridad de las sociedades de propiedad de los estados
provinciales, municipales y de la Ciudad Autónoma de Buenos Aires. e) Las autoridades y apoderados de partidos políticos a nivel
nacional, provincial y de la Ciudad Autónoma de Buenos Aires, que se desempeñen o hayan desempeñado hasta dos años anteriores
a la fecha en que fue realizada la operatoria. f) Las autoridades y representantes legales de organizaciones sindicales y empresariales
(cámaras, asociaciones y otras formas de agrupación corporativa con excepción de aquéllas que únicamente administren las
contribuciones o participaciones efectuadas por sus socios, asociados, miembros asociados, miembros adherentes y/o las que surgen
de acuerdos destinados a cumplir con sus objetivos estatutarios) que desempeñen o hayan desempeñado dichas funciones hasta dos
años anteriores a la fecha en que fue realizada la operatoria. El alcance establecido se limita a aquellos rangos, jerarquías o
categorías con facultades de decisión resolutiva, por lo tanto se excluye a los funcionarios de niveles intermedios o inferiores. g) Las
autoridades y representantes legales de las obras sociales contempladas en la Ley No 23.660, que desempeñen o hayan desempeñado
dichas funciones hasta dos años anteriores a la fecha en que fue realizada la operatoria. El alcance establecido se limita a aquellos
rangos, jerarquías o categorías con facultades de decisión resolutiva, por lo tanto se excluye a los funcionarios de niveles
intermedios o inferiores. h) Las personas que desempeñen o que hayan desempeñado hasta dos años anteriores a la fecha en que fue
realizada la operatoria, funciones superiores en una organización internacional y sean miembros de la alta gerencia, es decir,
directores, subdirectores y miembros de la Junta o funciones equivalentes excluyéndose a los funcionarios de niveles intermedios o
inferiores. i) Los cónyuges, o convivientes reconocidos legalmente, y familiares en línea ascendiente o descendiente hasta el primer
grado de consanguinidad, de las personas a que se refieren los puntos c), d), e), f), g), y h) durante los plazos que para ellas se 
indican”.");
$TEXTO .= "\n";
$PDF->MultiCell(0,0,$TEXTO,0,'J');

################################################################################################################
# AUTORIZACION DE COBRANZA POR DEBITO DIRECTO
################################################################################################################

$nroSolicitud = $orden['MutualProductoSolicitud']['nro_print'];
$fecha = $util->armaFecha($orden['MutualProductoSolicitud']['fecha']);
$usuario = $orden['MutualProductoSolicitud']['user_created'];
$vendedor = (isset($orden['MutualProductoSolicitud']['vendedor_nombre_min']) ? $orden['MutualProductoSolicitud']['vendedor_nombre_min'] : "");


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

$PDF->ln(10);
$PDF->firmaSocio();
$PDF->ln(4);
$PDF->barCode($orden['MutualProductoSolicitud']['barcode']);


//if(isset($orden['MutualProductoSolicitud']['proveedor_plan_anexos']) && !empty($orden['MutualProductoSolicitud']['proveedor_plan_anexos'])){
//    
//    foreach($orden['MutualProductoSolicitud']['proveedor_plan_anexos'] as $anexo){
//        
//        if($anexo == "ocom_imprime_auto_debito_nacion") $PDF->imprimirAutorizacionBancoNacion(__DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "bco_nacion.jpg",$orden);
//        if($anexo == "ocom_imprime_auto_debito_bcocba") $PDF->imprimirAutorizacionDebitoBcoCordoba($orden); 
//        if($anexo == "ocom_imprime_auto_debito_margen") $PDF->imprimirAutorizacionDebitoMargenComercial($orden); 
//        if($anexo == "ocom_imprime_pago_directo_rio") $PDF->imprimeAutoPagoDirectoSantanderRio($orden);
//        if($anexo == "ocom_imprime_mutuo_minuta") $PDF->imprimeMinutaMutuo($orden);
//        if($anexo == "ocom_imprime_mutuo") $PDF->imprimeContratoMutuo($orden);
//        if($anexo == "ocom_imprime_pago_directo_bco_pcia_bsas") $PDF->imprimeAutoPagoDirectoBancoPciaBsAs(__DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "logo_bco_pcia_bsas.png",$orden);   
//        if($anexo == "ocom_modelo_liquidacion") $PDF->imprime_modelo_liquidacion_cuotas($orden);
//        if($anexo == "ocom_imprime_auto_debito_cuenca") $PDF->imprime_autorizacion_debito_cuenca($orden);
//    }
//}


if(isset($orden['MutualProductoSolicitud']['proveedor_plan_anexos']) && !empty($orden['MutualProductoSolicitud']['proveedor_plan_anexos'])){
    
    foreach($orden['MutualProductoSolicitud']['proveedor_plan_anexos'] as $anexo){
        
        if ($anexo == "ocom_imprime_auto_debito_nacion") {
            $PDF->imprimirAutorizacionBancoNacion(__DIR__ . DIRECTORY_SEPARATOR . "logotipo" . DIRECTORY_SEPARATOR . "bna2.png", $orden);
        }
        if ($anexo == "ocom_imprime_auto_debito_bcocba") {
            $PDF->imprimirAutorizacionDebitoBcoCordoba($orden);
        }
        if ($anexo == "ocom_imprime_auto_debito_margen") {
            $PDF->imprimirAutorizacionDebitoMargenComercial($orden);
        }
        if($anexo == "ocom_imprime_pago_directo_rio") {
            $PDF->imprimeAutoPagoDirectoSantanderRio($orden);
        }
        if ($anexo == "ocom_imprime_mutuo_minuta") {
            $PDF->imprimeMinutaMutuo($orden);
        }
        if ($anexo == "ocom_imprime_mutuo") {
            $PDF->imprimeContratoMutuo($orden);
        }
        if ($anexo == "ocom_imprime_pago_directo_bco_pcia_bsas") {
            $PDF->imprimeAutoPagoDirectoBancoPciaBsAs(__DIR__ . DIRECTORY_SEPARATOR . "logotipo" . DIRECTORY_SEPARATOR . "logo_bco_pcia_bsas.png", $orden);
        }
        if ($anexo == "ocom_modelo_liquidacion") {
            $PDF->imprime_modelo_liquidacion_cuotas($orden);
        }
        if ($anexo == "ocom_imprime_auto_debito_cuenca") {
            $PDF->imprime_autorizacion_debito_cuenca($orden);
        }
        if ($anexo == "ocom_imprime_afiliacion_isar") {
            $PDF->imprime_solicitud_afiliacion_isar($orden);
		} 
		
        if ($anexo == "ocom_imprime_auto_debito_lext") {
            $PDF->imprime_autorizacion_debito_lextsrl($orden,__DIR__ . DIRECTORY_SEPARATOR . "logotipo" . DIRECTORY_SEPARATOR . "AteZhJ.png",__DIR__ . DIRECTORY_SEPARATOR . "logotipo" . DIRECTORY_SEPARATOR . "lext_srl.png");
		}

    }
    
//}else{
//
//    $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
//    $PDF->INI_FILE = $INI_FILE;
//    if(isset($INI_FILE['general']['ocom_imprime_auto_debito_nacion']) && $INI_FILE['general']['ocom_imprime_auto_debito_nacion'] == 1) {
//        $PDF->imprimirAutorizacionBancoNacion(__DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "bco_nacion.jpg",$orden);
//    }
//    if(isset($INI_FILE['general']['ocom_imprime_auto_debito_bcocba']) && $INI_FILE['general']['ocom_imprime_auto_debito_bcocba'] == 1){
//        $PDF->imprimirAutorizacionDebitoBcoCordoba($orden);    
//    }
//    if(isset($INI_FILE['general']['ocom_imprime_auto_debito_margen']) && $INI_FILE['general']['ocom_imprime_auto_debito_margen'] == 1){
//        $PDF->imprimirAutorizacionDebitoMargenComercial($orden);
//    }
//    if(isset($INI_FILE['general']['ocom_imprime_pago_directo_rio']) && $INI_FILE['general']['ocom_imprime_pago_directo_rio'] == 1){
//        $PDF->imprimeAutoPagoDirectoSantanderRio($orden);
//    }
//
//    if(isset($INI_FILE['general']['ocom_imprime_mutuo']) && $INI_FILE['general']['ocom_imprime_mutuo'] == 1){
//        $PDF->imprimeMinutaMutuo($orden);
//        $PDF->imprimeContratoMutuo($orden);
//    }
//    if(isset($INI_FILE['general']['ocom_imprime_pago_directo_bco_pcia_bsas']) && $INI_FILE['general']['ocom_imprime_pago_directo_bco_pcia_bsas'] == 1){
//        $PDF->imprimeAutoPagoDirectoBancoPciaBsAs(__DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "logo_bco_pcia_bsas.png",$orden);
//    }
    
}


$PDF->Output("solicitud_credito_".$orden['MutualProductoSolicitud']['id'].".pdf");

?>
