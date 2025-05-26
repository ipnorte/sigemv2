<?php 

// debug($orden);
// exit;

App::import('Vendor','solicitud_credito_general_pdf');

$PDF = new SolicitudCreditoGeneralPDF();

$PDF->SetTitle("SOLICITUD DE CREDITO #".$id);
$PDF->SetFontSizeConf(8.5);

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

$PDF->AddPage();
$PDF->PIE = false;

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
$PDF->imprimir_liquidacion($orden,false);


#############################################################################################
# FIRMAS
#############################################################################################
$PDF->ln(25);
$PDF->firmaSocio();
$PDF->ln(4);
$PDF->barCode($orden['MutualProductoSolicitud']['barcode']);

#############################################################################################
# CONTRATO
#############################################################################################

if(!empty($orden['MutualProductoSolicitud']['detalle_calculo_plan'])){
    // debug($orden);
    $objetoCalculado = json_decode($orden['MutualProductoSolicitud']['detalle_calculo_plan']);
    
    $cuotas = (array) $objetoCalculado->detalleCuotas;
    
    $primeraCuota = array_shift($cuotas);
    $ultimaCuota = array_pop($cuotas);
    
    App::import('Model', 'mutual.MutualProductoSolicitud');
    $oSOL = new MutualProductoSolicitud(null);
    $interes = $objetoCalculado->liquidacion->interesesDevengados;
    $totalRetiene = $objetoCalculado->liquidacion->gastoAdminstrativo->importe + $objetoCalculado->liquidacion->sellado->importe;
    
    $variables = array(
        'proveedor_razon_social' => $orden['MutualProductoSolicitud']['proveedor_full_name'],
        'proveedor_cuit' => $orden['MutualProductoSolicitud']['proveedor_cuit'],
        'proveedor_domicilio'  => $orden['MutualProductoSolicitud']['proveedor_domicilio']." - CP " . $orden['MutualProductoSolicitud']['proveedor_localidad'],
        'beneficiario_apenom' => $orden['MutualProductoSolicitud']['beneficiario_apenom'],
        'beneficiario_ndoc' => $orden['MutualProductoSolicitud']['beneficiario_ndoc'],
        'beneficiario_domicilio' => $orden['MutualProductoSolicitud']['beneficiario_domicilio'],
        'fecha_emision_str' => $orden['MutualProductoSolicitud']['fecha_emision_str']['dia']['numero']. " días del mes de ".$orden['MutualProductoSolicitud']['fecha_emision_str']['mes']['string']." del año " . $orden['MutualProductoSolicitud']['fecha_emision_str']['anio']['numero'],
        'total_importe_solicitado_letras' => $orden['MutualProductoSolicitud']['total_importe_solicitado_letras'],
        'importe_solicitado' => number_format($orden['MutualProductoSolicitud']['importe_solicitado'],2),
        'beneficio_cuenta' => $orden['MutualProductoSolicitud']['beneficio_cuenta'],
        'beneficio_cbu' => $orden['MutualProductoSolicitud']['beneficio_cbu'],
        'beneficio_banco' => $orden['MutualProductoSolicitud']['beneficio_banco'],
        'beneficio_sucursal' => $orden['MutualProductoSolicitud']['beneficio_sucursal'],
        'cantidad_cuota_letras' => $orden['MutualProductoSolicitud']['cantidad_cuota_letras'],
        'cuotas' => $orden['MutualProductoSolicitud']['cuotas'],
        'primer_vto_cuota' => date('d/m/Y',strtotime($primeraCuota->vtoSocio)),
        'ultimo_vto_cuota' => date('d/m/Y',strtotime($ultimaCuota->vtoSocio)),
        'tna' => number_format($orden['MutualProductoSolicitud']['tna'],2),
        'tea' => number_format($objetoCalculado->tea,2),
        'tem' => number_format($orden['MutualProductoSolicitud']['tnm'],2),
        'sistema_amortizacion' => $objetoCalculado->metodoCalculoFormula,
        'intereses_letra' => $oSOL->num2letras($interes),
        'interes' => number_format($interes,2),
        'total_letras' => $orden['MutualProductoSolicitud']['total_letras'],
        'importe_total' => number_format($orden['MutualProductoSolicitud']['importe_total'],2),
        'total_cuota_letras' => $orden['MutualProductoSolicitud']['total_cuota_letras'],
        'importe_cuota' => number_format($orden['MutualProductoSolicitud']['importe_cuota'],2),
        'total_retiene_letras' => $oSOL->num2letras($totalRetiene),
        'total_retiene' => number_format($totalRetiene,2),
        'sellado_alicuota' => number_format($objetoCalculado->liquidacion->sellado->porcentaje,2),
        'gto_admin_alicuota' => number_format($objetoCalculado->liquidacion->gastoAdminstrativo->porcentaje,2),
        'costo_cancelacion_anticipada' => number_format($objetoCalculado->liquidacion->costoCancelacionAnticipada,2),
        'costo_cancelacion_anticipada_letras' => trim($oSOL->num2letras($objetoCalculado->liquidacion->costoCancelacionAnticipada)),
        'interes_moratorio' => number_format($objetoCalculado->liquidacion->interesMoratorio,2),
        'beneficiario_e_mail' => (!empty($orden['MutualProductoSolicitud']['beneficiario_e_mail']) ? $orden['MutualProductoSolicitud']['beneficiario_e_mail'] : "________________________________"),
        'beneficiario_telefono_movil' => $orden['MutualProductoSolicitud']['beneficiario_telefono_movil'],
        'domi_fiscal_localidad' => strtoupper($PDF->INI_FILE['general']['domi_fiscal_localidad']),
    );
    
    $TEXTO = getPlantillaTextoContrato($variables);
    $PDF->ocom_imprime_mutuo_ryvsa($orden,$TEXTO,true,true);
}

// $TEXTO = getPlantillaTextoContrato();
// $PDF->ocom_imprime_mutuo_ryvsa($orden,$TEXTO,false);





#############################################################################################
# PAGARE
#############################################################################################
$PDF->HEADER = false;
$PDF->PIE = false;
$PDF->AddPage();
$PDF->reset();
$PDF->imprimirPagare($orden,false,false,true);


#############################################################################################
# CROQUIS
#############################################################################################
$PDF->imprime_croquis_ubicacion($orden);

#############################################################################################
# AUTORIZA DEBITO COBRANZA DIRECTO 
#############################################################################################

$PDF->autorizacionCobranzaDebitoDirectoGeneral($orden);




// $TEXTO = getPlantillaTextoContrato($variables);
// $PDF->ocom_imprime_mutuo_ryvsa($orden,$TEXTO,false);

///////////////////////////////////////////////////////////////// 
// RECIBO
///////////////////////////////////////////////////////////////// 
$PDF->imprimirRecibo($orden);



$PDF->Output("solicitud_credito_".$orden['MutualProductoSolicitud']['id'].".pdf");



// PLANTILLA DE TEXTO CONTRATO
function getPlantillaTextoContrato($variables = array()){

    if(empty($variables)){

        $variables = array(
            'proveedor_razon_social' => '_______________________________',
            'proveedor_cuit' => '_______________',
            'proveedor_domicilio' => '_____________________________________________',
            'beneficiario_apenom' => '_____________________________________________',
            'beneficiario_ndoc' => '____________',
            'beneficiario_domicilio' => '_____________________________________________',
            'fecha_emision_str' => "___ días del mes de _______________ del año ____",
            'total_importe_solicitado_letras' => '_____________________________________________',
            'importe_solicitado' => '___________',
            'beneficio_cuenta' => '____________________',
            'beneficio_cbu' => '__________________________________________',
            'beneficio_banco' => '__________________________________________',
            'beneficio_sucursal' => '___________',
            'cantidad_cuota_letras' => '___________________',
            'cuotas' => '____',
            'primer_vto_cuota' => '___/___/_____',
            'ultimo_vto_cuota' => '___/___/_____',
            'tna' => '_____',
            'tea' => '_____',
            'tem' => '_____',
            'sistema_amortizacion' => '____________',
            'intereses_letra' => '__________________________________________',
            'interes' => '___________',
            'total_letras' => '__________________________________________',
            'importe_total' => '___________',
            'total_cuota_letras' => '__________________________________________',
            'importe_cuota' => '___________',
            'total_retiene_letras' => '__________________________________________',
            'total_retiene' => '___________',
            'sellado_alicuota' => '_____',
            'gto_admin_alicuota' => '_____',
            'costo_cancelacion_anticipada' => '_____',
            'costo_cancelacion_anticipada_letras' => '_______________',
            'interes_moratorio' => '_____',
            'beneficiario_e_mail' => '_____________________________________',
            'beneficiario_telefono_movil' => '____________________________',
            'domi_fiscal_localidad' => '____________________________',
        );
    }



    $TEXTO = "";
    $TEXTO .= "En la Ciudad de CORDOBA, Provincia de CORDOBA, a los @fecha_emision_str@, @proveedor_razon_social@ C.U.I.T.  @proveedor_cuit@, con domicilio en @proveedor_domicilio@, en adelante denominado el \"MUTUANTE/ACREEDOR\" por una parte, representado en este acto por el abajo firmante en su carácter de apoderado, con facultades suficientes y vigentes para ello, y por la otra el Sr/a. @beneficiario_apenom@, DNI @beneficiario_ndoc@, con domicilio en la calle @beneficiario_domicilio@, en adelante denominado el \"MUTUARIO/DEUDOR\", acuerdan celebrar el presente CONTRATO DE PRESTAMO DE DINERO, sujetándose a las siguientes cláusulas:";
    $TEXTO .= "\n";
    $TEXTO .= "PRIMERA: CRÉDITO. El MUTUARIO recibe en este acto, a su petición y a su entera satisfacción, de parte del MUTUANTE, la suma solicitada de PESOS @total_importe_solicitado_letras@ ($ @importe_solicitado@) -con menos los gastos detallados en la clausula quinta- en efectivo o mediante la acreditación, simultanea con la firma del presente, en la cuenta de Caja de Ahorro Nro. @beneficio_cuenta@ CBU @beneficio_cbu@ abierta en el @beneficio_banco@ Sucursal @beneficio_sucursal@, a elección del solicitante, sirviendo el presente de suficiente recibo.-";
    $TEXTO .= "\n";
    $TEXTO .= "SEGUNDA: DEVOLUCIÓN DEL CRÉDITO. En base a los requerimientos y posibilidades del solicitante, el préstamo será restituido al MUTUANTE en @cantidad_cuota_letras@ (@cuotas@) cuotas mensuales, iguales y consecutivas. El vencimiento de las mismas se producirá el primer día de cada mes, pudiendo el deudor abonarla sin cargo hasta el décimo (10) día de cada mes; la primer cuota vence el día @primer_vto_cuota@ y la ultima el día @ultimo_vto_cuota@.-";
    $TEXTO .= "\n";
    $TEXTO .= "TERCERA: INTERESES COMPENSATORIOS. El crédito devengará una tasa de interés nominal anual – T.N.A. – del @tna@%, equivalente a una tasa efectiva de interés mensual – T.E.M. – del @tem@% lo que hace un total de intereses a pagar de PESOS @intereses_letra@ ($ @interes@) conforme al sistema de amortización @sistema_amortizacion@. Por lo que la suma total adeudada y a restituir es de PESOS @total_letras@ ($ @importe_total@) dicho monto incluye el componente impositivo IVA.-";
    $TEXTO .= "\n";
    $TEXTO .= "CUARTA: CUOTA MENSUAL. El valor de la cuota mensual a pagar mensualmente es de PESOS @total_cuota_letras@ ($ @importe_cuota@). El pago de la cuota cuyo vencimiento coincidiera con un día inhábil, sábado o domingo, se producirá indefectiblemente el día hábil inmediato siguiente.-";
    $TEXTO .= "\n";
    $TEXTO .= "QUINTA: GASTOS. Todo gasto, sellado e importe que grave la operación estarán a cargo del DEUDOR. Los gastos imprescindibles y obligatorios para la realización de la presente operación solicitada por el deudor arrojan un valor total de @total_retiene_letras@ ($ @total_retiene@); son: A) Impuesto de sellado del presente contrato más el Impuesto de sellado del pagare (@sellado_alicuota@% de la suma total adeudada) B) Gastos de comisión,otorgamiento y análisis de calificación crediticia (@gto_admin_alicuota@% sobre la suma solicitada).- ";
    $TEXTO .= "\n";
    $TEXTO .= "SEXTA: EL PAGO. El pago debe realizarlo en efectivo hasta el vencimiento de la cuota, en el domicilio del MUTUANTE o en el lugar en que éste oportunamente lo indique, dentro de la misma plaza y del horario apertura al público. Las cuotas se imputarán a todos los valores por igual como se ha detallado. El pago parcial no significará espera, quita, transacción o novación de la cuota, siendo imputable dicha suma previamente a intereses, y el saldo se acumulará a la del mes siguiente con más los intereses moratorios que devengue lo no abonado. Se tomaran como validos todos los pagos hechos por el deudor en cualquiera de las bocas de pagos habilitadas por el MUTUANTE, tales como Rapipago, Pago Facil, Cobro Express, MercadoPago, Pagos con tarjetas de debito de forma presencial o virtual, PagosMisCuentas y todas aquellas que el Mutuante habilite en un futuro, los cuales serán informados al Mutuario.-";
    $TEXTO .= "\n";
    $TEXTO .= "SÉPTIMA: GARANTIA – RENUNCIA A LA INEMBARGABILIDAD: El MUTUARIO/DEUDOR declara expresamente renunciar al derecho de inembargabilidad sobre los haberes, jubilaciones, pensiones, indemnizaciones u otros beneficios patrimoniales nacionales o provinciales, que percibe actualmente o en un futuro, los cuales afecta voluntariamente a constituirlos como garantía de cumplimiento de la presente obligación hasta un máximo de un 20% mensual de los mismos, por embargo judicial (ley 8024 Art. 45 inc. C; Ley 24.241 Art. 14 Inc. C; Decreto Ley 6.754; LEY N° 22.919 art. 22; entre otros).-";
    $TEXTO .= "\n";
    $TEXTO .= "OCTAVA: LIBRAMIENTO DE PAGARE: El Mutuario acepta expresamente documentar la deuda en un pagare el cual se librará por el monto total adeudado, con la misma fecha de libramiento del presente y la fecha de vencimiento quedará en blanco a los fines de ser completada con el primer periodo adeudado o en mora – conforme clausula decimoprimera-. El Mutuante podrá a su sola opción, iniciar la ejecución con cualquiera de los documentos que prefiera, accionar contra el Mutuante/Deudor y/o sus garantes, avalistas o codeudores, en forma individual o conjunta. Una vez cancelado el crédito, a requerimiento del Mutuario, se devolverá el documento pagare, siempre que lo requiera dentro de los noventa días contados a partir de la fecha de cancelación del mismo. Vencido este plazo, el acreedor procederá a su destrucción, sin que ello genere reclamo alguno por parte del Mutuario.-";
    $TEXTO .= "\n";
    $TEXTO .= "NOVENA: CANCELACIÓN ANTICIPADA: Los plazos se presumen establecidos en beneficio de ambas partes, según lo que de común acuerdo han establecido, dejando a salvo el derecho del DEUDOR de ejercer la cancelación anticipada total o parcial del crédito. Con la cancelación anticipada solo se quitarán los intereses de las cuotas no vencidas. Para optar por la cancelación anticipada, el deudor no deberá encontrarse en mora, haber transcurrido la mitad del plazo del préstamo y deberá abonar el total del capital bruto en efectivo con más un @costo_cancelacion_anticipada_letras@ por ciento (@costo_cancelacion_anticipada@%) del capital remanente, lo que el MUTUANTE acepta como compensación razonable por el otorgamiento del crédito, como también todos los gastos de gestión, impuestos y costos. Podrá optar por la cancelación anticipada en cualquier momento de la relación contractual. -";
    $TEXTO .= "\n";
    $TEXTO .= "DÉCIMA: MORA: La mora se producirá de pleno derecho y sin necesidad de requerimiento o interpelación judicial o extrajudicial alguna, por el simple incumplimiento del MUTUARIO en los plazos pactados de cualquiera de las obligaciones; por la falta de pago de una cuota, o el pago insuficiente o parcial de una de ellas. -";
    $TEXTO .= "\n";
    $TEXTO .= "DÉCIMO PRIMERA: CONSECUENCIAS DE LA MORA – CADUCIDAD DE LOS PLAZOS: Al acaecimiento del supuesto de la cláusula anterior sin regularizar su situación, producirá de pleno derecho la caducidad de todos los plazos, haciéndose exigible la inmediata e íntegra devolución y reembolso del capital desembolsado por el MUTUANTE, con más los intereses compensatorios y moratorios pactados hasta la total devolución del capital adeudado con más los intereses judiciales, honorarios y costos que se originen como consecuencia del procedimiento de ejecución (1088, 1089 y 1529 CCCN).-";
    $TEXTO .= "\n";
    $TEXTO .= "DÉCIMO SEGUNDA: INTERÉS MORATORIO - CAPITALIZACIÓN: En todos los casos de mora, sobre el saldo del capital debido, se calculará un interés moratorio del @interes_moratorio@% mensual a contabilizar desde el primer día de cada mes. Se pacta expresamente que en estos casos, tanto el interés compensatorio como el moratorio, se capitalizarán a partir de los seis meses de mora, en los términos del art. 770 del C. Civil y Comercial de la Nación. ";
    $TEXTO .= "\n";
    $TEXTO .= "DÉCIMO TERCERA: CESIÓN DEL CRÉDITO. El MUTUANTE podrá transferir el presente, por cualquiera de los medios previstos en la ley, adquiriendo el o los cesionarios los mismos beneficios y/o derechos y/o acciones del ACREEDOR bajo el presente contrato. De optar por la cesión prevista en los artículos 70 a 72 de la Ley 24.441, la cesión del crédito y su garantía podrá hacerse sin notificación al DEUDOR y tendrá validez desde su fecha de formalización, en un todo de acuerdo con lo establecido por el artículo 72 de la ley precitada. El MUTUARIO expresamente manifiesta que, tal como lo prevé la mencionada ley, la cesión tendrá efecto desde la fecha en que se opere la misma y que sólo podrá oponer contra el cesionario las excepciones previstas en el mencionado artículo. No obstante, en el supuesto que la cesión implique modificación del domicilio de pago, el nuevo domicilio de pago deberá notificarse en forma fehaciente a la parte deudora. Habiendo mediado modificación del domicilio de pago, no podrá oponerse excepción de pago documentado, en relación a pagos practicados a anteriores cedentes con posterioridad a la notificación del nuevo domicilio de pago. -";
    $TEXTO .= "\n";
    $TEXTO .= "DÉCIMO CUARTA: INFORMACIÓN. El DEUDOR reconoce que ha sido debidamente informado sobre todas las condiciones establecidas para el otorgamiento del Crédito, tanto en la FICHA INFORMATIVA DEL PRÉSTAMO PERSONAL SOLICITADO como en el CONTRATO PARA PRÉSTAMOS PERSONALES, aceptando conocer su contenido.- El deudor denuncia el siguiente correo electrónico @beneficiario_e_mail@ y número de teléfono celular @beneficiario_telefono_movil@, donde opta de forma expresa recibir las notificaciones e intimaciones por medio de mail y/o wathsapp y/o SMS, considerando las mismas validas y suficientes.-";
    $TEXTO .= "\n";
    $TEXTO .= "DÉCIMO QUINTA: DE FORMA. En caso de controversia las partes se someterán a los Tribunales Ordinarios de la Provincia de CORDOBA, renunciado al fuero federal. De conformidad las partes suscriben dos (2) ejemplares en un mismo tenor y a un sólo efecto, en la ciudad de @domi_fiscal_localidad@, a los @fecha_emision_str@.-";
    $TEXTO .= "\n";
    $TEXTO .= "\n";
    
    
    
    foreach($variables as $key => $value){
        $search = "/(@)($key?)(@)/";
        $TEXTO = preg_replace($search,$value,$TEXTO);
    }

    return $TEXTO;

}

?>
