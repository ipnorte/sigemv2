<?php 
//debug($aRecibo);

App::import('Vendor','factura_afip');
// debug($aFacturaAfip);
// exit;

$PDF = new FacturaAfip();

$PDF->razon_social = $aFacturaAfip['Documento']['razon_social'];
$PDF->CdcnIva = $aFacturaAfip['Documento']['iva_responsable'];
$PDF->CbteLetra = $aFacturaAfip['Documento']['letra'];
$PDF->CbteTipo = $aFacturaAfip['ClienteFactura']['Afip_CbteTipo'];
$PDF->CbteDscr = $aFacturaAfip['Documento']['descripcion'];
$PDF->CbteNro = $aFacturaAfip['ClienteFactura']['Afip_NroCbte'];
$PDF->CbtePVta = $aFacturaAfip['ClienteFactura']['Afip_PtoVta'];
$PDF->FchEmi = $aFacturaAfip['ClienteFactura']['Afip_CbteFch'];
$PDF->IniAct = $aFacturaAfip['Documento']['inicio_actividad'];
$PDF->CuitEmi = $aFacturaAfip['AfipDato']['cuit'];
$PDF->IngBrutos = $aFacturaAfip['Documento']['ingreso_bruto'];
$PDF->ImpTotal = $util->nf($aFacturaAfip['ClienteFactura']['total_comprobante']);
$PDF->CodBarra = $aFacturaAfip['ClienteFactura']['Afip_CodBarra'];
$PDF->cae = $aFacturaAfip['ClienteFactura']['Afip_CodAutorizacion'];
$PDF->FchVto = $aFacturaAfip['ClienteFactura']['Afip_FchVto'];

$PDF->SetFontSizeConf(9);

$PDF->Open();
$PDF->textoHeader = '';  //. $util->armaFecha($aRecibo['Recibo']['fecha_comprobante']);
$PDF->copias = 'ORIGINAL';

$PDF->bMargen = 0;
// $PDF->SetAutoPageBreak(true, 95);
//  7 = 3.1818
//  8 = 3.6363
//  9 = 4.0909
// 10 = 4.5454
// 11 = 5.0000
$fontSize = 9;
$fontSize_titulo = 20;
$PDF->fontSizeTitulo1 = 15;

for($nCopias = 1; $nCopias < 4; $nCopias++){
    if($nCopias == 2) $PDF->copias = 'DUPLICADO';
    if($nCopias == 3) $PDF->copias = 'TRIPLICADO';

    $PDF->AddPage();
//   $PDF->reset();
    $ancho = 26.25;
    $medio = 105;

    $PDF->linea[0] = array(
                            'posx' => 5,
                            'ancho' => 80,
                            'texto' => 'Periodo Facturado Desde: ' . date('d/m/Y',strtotime($aFacturaAfip['ClienteFactura']['Afip_FchServDesde'])),
                            'borde' => 'BLT',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => 'B',
                            'colorf' => '',
                            'size' => $fontSize+2,
                            'family' => 'helvetica'
            );
    $PDF->linea[1] = array(
                            'posx' => 85,
                            'ancho' => 55,
                            'texto' => 'Hasta: ' . date('d/m/Y',strtotime($aFacturaAfip['ClienteFactura']['Afip_FchServHasta'])),
                            'borde' => 'BT',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => 'B',
                            'colorf' => '',
                            'size' => $fontSize+2,
                            'family' => 'helvetica'
            );
    $PDF->linea[2] = array(
                            'posx' => 145,
                            'ancho' => 60,
                            'texto' => 'Fecha de Vto. para el pago: ' . date('d/m/Y',strtotime($aFacturaAfip['ClienteFactura']['Afip_FchVtoPago'])),
                            'borde' => 'BTR',
                            'align' => 'R',
                            'fondo' => 0,
                            'style' => 'B',
                            'colorf' => '',
                            'size' => $fontSize+2,
                            'family' => 'helvetica'
            );
    $PDF->Imprimir_linea();

    $PDF->linea[0] = array(
                            'posx' => 5,
                            'ancho' => 85,
                            'texto' => 'CUIT: ' . $aFacturaAfip['Cliente']['cuit'],
                            'borde' => 'LT',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => 'B',
                            'colorf' => '',
                            'size' => $fontSize,
                            'family' => 'helvetica'
            );
    $PDF->linea[1] = array(
                            'posx' => 85,
                            'ancho' => 120,
                            'texto' => 'Apellido y Nombre/ Razon Social: ' . $aFacturaAfip['Cliente']['razon_social_resumida'],
                            'borde' => 'TR',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => 'B',
                            'colorf' => '',
                            'size' => $fontSize,
                            'family' => 'helvetica'
            );
    $PDF->Imprimir_linea();

    $PDF->linea[0] = array(
                            'posx' => 5,
                            'ancho' => 200,
                            'texto' => '',
                            'borde' => 'LR',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => 'B',
                            'colorf' => '',
                            'size' => $fontSize-2,
                            'family' => 'helvetica'
            );
    $PDF->Imprimir_linea();


    $PDF->linea[0] = array(
                            'posx' => 5,
                            'ancho' => 90,
                            'texto' => 'Condicion frente al IVA: ' . $aFacturaAfip['Cliente']['iva_concepto'],
                            'borde' => 'L',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => 'B',
                            'colorf' => '',
                            'size' => $fontSize,
                            'family' => 'helvetica'
            );
    $PDF->linea[1] = array(
                            'posx' => 95,
                            'ancho' => 110,
                            'texto' => 'Domicilio Comercial: ' . $aFacturaAfip['Cliente']['domicilio'],
                            'borde' => 'R',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => 'B',
                            'colorf' => '',
                            'size' => $fontSize-1,
                            'family' => 'helvetica'
            );
    $PDF->Imprimir_linea();


    $PDF->linea[0] = array(
                            'posx' => 5,
                            'ancho' => 200,
                            'texto' => '',
                            'borde' => 'LR',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => 'B',
                            'colorf' => '',
                            'size' => $fontSize-2,
                            'family' => 'helvetica'
            );
    $PDF->Imprimir_linea();

    $PDF->linea[0] = array(
                            'posx' => 5,
                            'ancho' => 200,
                            'texto' => 'Condicion de Venta: Cta. Cte.',
                            'borde' => 'LRB',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => 'B',
                            'colorf' => '',
                            'size' => $fontSize,
                            'family' => 'helvetica'
            );
    $PDF->Imprimir_linea();

    
    foreach($aFacturaAfip['ClienteFacturaDetalle'] as $aDetalle){
        $PDF->linea[0] = array(
                                'posx' => 8,
                                'ancho' => $ancho * 6,
                                'texto' => $aDetalle['producto'],
                                'borde' => '',
                                'align' => 'L',
                                'fondo' => 0,
                                'style' => '',
                                'colorf' => '',
                                'size' => 8,
                                'family' => 'helvetica'
        );
        $PDF->linea[1] = array(
                                'posx' => ($ancho * 6) - 7,
                                'ancho' => $ancho,
                                'texto' => '$',
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 0,
                                'style' => '',
                                'colorf' => '',
                                'size' => 9,
                                'family' => 'helvetica'
        );
        $PDF->linea[2] = array(
                                'posx' => $ancho * 7,
                                'ancho' => $ancho - 6,
                                'texto' => $util->nf($aDetalle['precio_total']),
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 0,
                                'style' => '',
                                'colorf' => '',
                                'size' => 9,
                                'family' => 'helvetica'
        );
        $PDF->Imprimir_linea();
        
    }
    
    /*
    for($i = 1; $i <= 34; $i++){
        if($PDF->GetY() >= 230) break;

        $PDF->linea[0] = array(
                                'posx' => 8,
                                'ancho' => $ancho * 6,
                                'texto' => 'Linea Nro.:',
                                'borde' => '',
                                'align' => 'L',
                                'fondo' => 0,
                                'style' => '',
                                'colorf' => '',
                                'size' => 8,
                                'family' => 'helvetica'
        );
        $PDF->linea[1] = array(
                                'posx' => ($ancho * 6) - 7,
                                'ancho' => $ancho,
                                'texto' => '$',
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 0,
                                'style' => '',
                                'colorf' => '',
                                'size' => 9,
                                'family' => 'helvetica'
        );
        $PDF->linea[2] = array(
                                'posx' => $ancho * 7,
                                'ancho' => $ancho - 6,
                                'texto' => $PDF->GetY(),
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 0,
                                'style' => '',
                                'colorf' => '',
                                'size' => 9,
                                'family' => 'helvetica'
        );
        $PDF->Imprimir_linea();
    }
     * 
     */
}

$PDF->Ln();
$PDF->Output("FacturaNro" . str_pad($aFacturaAfip['ClienteFactura']['Afip_NroCbte'],8,0, STR_PAD_LEFT) . ".pdf");

?>