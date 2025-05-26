<?php 

App::import('Vendor','factura_afip');

// debug($aFactura);

$PDF = new FacturaAfip();

$PDF->razon_social = $aFactura['Documento']['razon_social'];
$PDF->CdcnIva = $aFactura['Documento']['iva_responsable'];
$PDF->CbteLetra = $aFactura['Factura']['letra_comprobante'];
$PDF->CbteTipo = $aFactura['Factura']['tipo_comprobante'];
$PDF->CbteDscr = $aFactura['Factura']['nombre_comprobante'];
$PDF->CbteNro = $aFactura['Factura']['numero_comprobante'];
$PDF->CbtePVta = $aFactura['Factura']['punto_venta'];
$PDF->FchEmi = $aFactura['Factura']['fecha_comprobante'];
$PDF->IniAct = $aFactura['Documento']['inicio_actividad'];
$PDF->CuitEmi = $aFactura['AfipDato']['cuit'];
$PDF->IngBrutos = $aFactura['Documento']['ingreso_bruto'];
$PDF->ImpTotal = $util->nf($aFactura['Factura']['importe_total']);
$PDF->CodBarra = $aFactura['Factura']['Afip_CodBarra'];
$PDF->cae = $aFactura['Factura']['codigo_autorizacion'];
$PDF->FchVto = $aFactura['Factura']['cae_fecha_vto'];

$PDF->ImpNeto = $util->nf($aFactura['Factura']['importe_neto']);
$PDF->ImpIVA = $util->nf($aFactura['Factura']['importe_iva']);

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
                            'texto' => 'Periodo Facturado Desde: ' . date('d/m/Y',strtotime($aFactura['Factura']['fecha_desde'])),
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
                            'texto' => 'Hasta: ' . date('d/m/Y',strtotime($aFactura['Factura']['fecha_hasta'])),
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
                            'texto' => 'Fecha de Vto. para el pago: ' . date('d/m/Y',strtotime($aFactura['Factura']['fecha_vto_pago'])),
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
                            'ancho' => 60,
                            'texto' => 'CUIT: ' . $aFactura['Factura']['numero_documento'],
                            'borde' => 'LT',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => 'B',
                            'colorf' => '',
                            'size' => $fontSize,
                            'family' => 'helvetica'
            );
    $PDF->linea[1] = array(
                            'posx' => 60,
                            'ancho' => 145,
                            'texto' => 'Apellido y Nombre/ Razon Social: ' . $aFactura['Factura']['nom_apel'],
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
                            'texto' => 'Condicion frente al IVA: Consumidor Final',
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
                            'texto' => 'Domicilio Comercial: ' . $aFactura['Factura']['domicilio'],
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
                            'texto' => 'Condicion de Venta: Contado',
                            'borde' => 'LRB',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => 'B',
                            'colorf' => '#ccc',
                            'size' => $fontSize,
                            'family' => 'helvetica'
            );
    $PDF->Imprimir_linea();

    $PDF->ln(3);
    $PDF->linea[0] = array(
                            'posx' => 5,
                            'ancho' => 165,
                            'texto' => 'CONCEPTO',
                            'borde' => 'LTBR',
                            'align' => 'C',
                            'fondo' => 1,
                            'style' => '',
                            'colorf' => '#ccc',
                            'size' => 8,
                            'family' => 'helvetica'
    );
    $PDF->linea[1] = array(
                            'posx' => 170,
                            'ancho' => 35,
                            'texto' => 'IMPORTE NETO',
                            'borde' => 'LTBR',
                            'align' => 'C',
                            'fondo' => 1,
                            'style' => '',
                            'colorf' => '#ccc',
                            'size' => 8,
                            'family' => 'helvetica'
    ); 
    $PDF->Imprimir_linea();
    $PDF->ln(1);
    
    foreach($aFactura['Detalle'] as $aDetalle){
        $PDF->linea[0] = array(
                                'posx' => 8,
                                'ancho' => $ancho * 6,
                                'texto' => $aDetalle[0]['descripcion'],
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
                                'texto' => $util->nf($aDetalle[0]['neto']),
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
$PDF->Output("FacturaNro-" . str_pad($aFactura['Factura']['numero_comprobante'],8,0, STR_PAD_LEFT) . ".pdf");

?>