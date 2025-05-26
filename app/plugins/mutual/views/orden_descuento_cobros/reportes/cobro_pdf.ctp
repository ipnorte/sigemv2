<?php 

//DEBUG($pago);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("ORDEN DE COBRO #".$pago['OrdenDescuentoCobro']['id']);
$PDF->SetFontSizeConf(8.5);

//$PDF->AddPage();

$PDF->Open();

#TITULO DEL REPORTE#
//$PDF->titulo['titulo3'] = "ORDEN DE COBRO #".$pago['OrdenDescuentoCobro']['id'] ." *** S.E.U.O. ***";
//$PDF->titulo['titulo2'] = 'TIPO ORDEN: '. $util->globalDato($pago['OrdenDescuentoCobro']['tipo_cobro']) . ' - RECIBO NRO: ' . $pago['OrdenDescuentoCobro']['nro_recibo'];
//$PDF->titulo['titulo1'] = 'FECHA: '.$util->armaFecha($pago['OrdenDescuentoCobro']['fecha']) . ' - IMPORTE: '. number_format($pago['OrdenDescuentoCobro']['importe'],2);

$PDF->titulo['titulo3'] = "ORDEN DE COBRO #".$pago['OrdenDescuentoCobro']['id'];
$PDF->titulo['titulo2'] = 'FECHA: '.$util->armaFecha($pago['OrdenDescuentoCobro']['fecha']) . ' - IMPORTE: '. number_format($pago['OrdenDescuentoCobro']['importe'],2);
$PDF->titulo['titulo1'] = "";

$PDF->textoHeader = "[*** S.E.U.O. ***]";


//PERIODO  	ORDEN  	TIPO / NUMERO  	PROVEEDOR / PRODUCTO  	CUOTA  	CONCEPTO  	COBRADO  	SALDO CUOTA
$W1 = array(20,10,25,50,10,25,25,25);
$L1 = $PDF->armaAnchoColumnas($W1);

$PDF->encabezado = array();
$PDF->encabezado[0] = array();	

$fontSizeHeader = 7;

$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => 190,
			'texto' => "CONCEPTO: " . $pago['OrdenDescuentoCobro']['tipo_cobro_desc'] . (!empty($pago['OrdenDescuentoCobro']['Recibo']) ? " | RECIBO: " . $pago['OrdenDescuentoCobro']['Recibo']['numero_string'] . " [".$pago['OrdenDescuentoCobro']['Recibo']['comentarios']."]" : ""),
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);
$PDF->ln(10);

$PDF->encabezado[1][0] = array(
			'posx' => $L1[0],
			'ancho' => 100,
			'texto' => $pago['Socio']['str'],
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);
	
$PDF->ln(10);	

	


$PDF->encabezado[2] = array();
$PDF->encabezado[2][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => 'PERIODO',
			'borde' => 'LTB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[2][1] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => 'ORDEN',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[2][2] = array(
			'posx' => $L1[2],
			'ancho' => $W1[2],
			'texto' => 'TIPO / NUMERO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[2][3] = array(
			'posx' => $L1[3],
			'ancho' => $W1[3],
			'texto' => 'PROVEEDOR / PRODUCTO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[2][4] = array(
			'posx' => $L1[4],
			'ancho' => $W1[4],
			'texto' => 'CUOTA',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[2][5] = array(
			'posx' => $L1[5],
			'ancho' => $W1[5],
			'texto' => 'CONCEPTO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[2][6] = array(
			'posx' => $L1[6],
			'ancho' => $W1[6],
			'texto' => 'COBRADO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[2][7] = array(
			'posx' => $L1[7],
			'ancho' => $W1[7],
			'texto' => 'SALDO CUOTA',
			'borde' => 'RTB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->AddPage();
$PDF->reset();	
	
$ACU_TOTAL_CUOTA = 0;
$ACU_TOTAL_SALDO = 0;

$fontSize = 7;

foreach($pago['OrdenDescuentoCobroCuota'] as $cuota):

//	$saldoCuota = $cuota['OrdenDescuentoCuota']['importe'] - $cuota['importe'];
    $saldoCuota = $cuota['OrdenDescuentoCuota']['saldo_cuota'];
	$ACU_TOTAL_CUOTA += $cuota['importe'];
	$ACU_TOTAL_SALDO += $saldoCuota;
	
	
	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0],
		'texto' => $util->periodo($cuota['OrdenDescuentoCuota']['periodo']),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $fontSize
	);
	$PDF->linea[1] = array(
		'posx' => $L1[1],
		'ancho' => $W1[1],
		'texto' => $cuota['OrdenDescuentoCuota']['orden_descuento_id'],
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $fontSize
	);
	$PDF->linea[2] = array(
		'posx' => $L1[2],
		'ancho' => $W1[2],
		'texto' => $cuota['OrdenDescuentoCuota']['tipo_nro'],
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $fontSize
	);	
	$PDF->linea[3] = array(
		'posx' => $L1[3],
		'ancho' => $W1[3],
		'texto' => substr($cuota['OrdenDescuentoCuota']['proveedor_producto'],0,35),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $fontSize - 1
	);
	$PDF->linea[4] = array(
		'posx' => $L1[4],
		'ancho' => $W1[4],
		'texto' => $cuota['OrdenDescuentoCuota']['cuota'],
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $fontSize
	);		
	
	$PDF->linea[5] = array(
		'posx' => $L1[5],
		'ancho' => $W1[5],
		'texto' => $cuota['OrdenDescuentoCuota']['tipo_cuota_desc'],
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $fontSize
	);

	$PDF->linea[6] = array(
		'posx' => $L1[6],
		'ancho' => $W1[6],
		'texto' => $util->nf($cuota['importe']).($cuota['reversado'] == 1 ? "*":""),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $fontSize
	);
	$PDF->linea[7] = array(
		'posx' => $L1[7],
		'ancho' => $W1[7],
		'texto' => $util->nf($saldoCuota),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $fontSize
	);				
	
	$PDF->Imprimir_linea();	

endforeach;


$PDF->linea[0] = array(
	'posx' => $L1[0],
	'ancho' => $W1[0] + $W1[1] + $W1[2] + $W1[3] + $W1[4] + $W1[5],
	'texto' => 'TOTAL ORDEN #' . $pago['OrdenDescuentoCobro']['id'],
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $fontSize + 1
);

$PDF->linea[6] = array(
	'posx' => $L1[6],
	'ancho' => $W1[6],
	'texto' => $util->nf($ACU_TOTAL_CUOTA),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $fontSize + 1
);

$PDF->linea[7] = array(
	'posx' => $L1[7],
	'ancho' => $W1[7],
	'texto' => "",
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $fontSize + 1
);

$PDF->Imprimir_linea();

$PDF->ln(3);

$PDF->linea[0] = array(
	'posx' => $L1[0],
	'ancho' => $W1[0],
	'texto' => "*** EMITIDA EL ".$pago['OrdenDescuentoCobro']['created']. (!empty($pago['OrdenDescuentoCobro']['user_created']) ? " [".$pago['OrdenDescuentoCobro']['user_created']."]" : "") . " ***",
	'borde' => '',
	'align' => 'L',
	'fondo' => 0,
	'style' => '',
	'colorf' => '#ccc',
	'size' => 5
);
$PDF->Imprimir_linea();

$PDF->ln(3);

$PDF->linea[0] = array(
	'posx' => $L1[0],
	'ancho' => $W1[0],
	'texto' => "*** S.E.U.O. *** SALVO ERROR U OMISION",
	'borde' => '',
	'align' => 'L',
	'fondo' => 0,
	'style' => '',
	'colorf' => '#ccc',
	'size' => 5
);
$PDF->Imprimir_linea();

if($pago['OrdenDescuentoCobro']['total_reversado'] != 0):

	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0],
		'texto' => "*) PAGO REVERSADO",
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => 5
	);
	$PDF->Imprimir_linea();

endif;

if(!empty($pago['reversos'])):
    
	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0],
		'texto' => "INFORME DE CUOTAS REVERSADAS",
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => 10
	);
	$PDF->Imprimir_linea();  
    $size_1 = 7;
    $size_2 = 6;
    foreach($pago['reversos'] as $reverso){

        
        $PDF->linea[0] = array(
            'posx' => $L1[0],
            'ancho' => $W1[0],
            'texto' => $reverso['reverso_proveedor_producto']." | ". $reverso['reverso_cuota'],
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#ccc',
            'size' => $size_1
        );
        $PDF->Imprimir_linea();

        $PDF->linea[0] = array(
            'posx' => $L1[0],
            'ancho' => $W1[0],
            'texto' => "IMPORTE REVERSADO: ". $reverso['reverso_importe'] . " | PERIODO INFORME: " . $util->periodo($reverso['reverso_periodo_proveedor']),
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#ccc',
            'size' => $size_2,
        );
        $PDF->Imprimir_linea();
        $PDF->linea[0] = array(
            'posx' => $L1[0],
            'ancho' => $W1[0],
            'texto' => "FECHA: " . $util->armaFecha($reverso['reverso_fecha']) . " | CUENTA: " .$reverso['reverso_cuenta'],
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#ccc',
            'size' => $size_2,
        );  
        $PDF->Imprimir_linea(); 
        $PDF->linea[0] = array(
            'posx' => $L1[0],
            'ancho' => 190,
            'texto' => "",
            'borde' => 'T',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#ccc',
            'size' => $size_2,
        );  
        $PDF->Imprimir_linea();         
      

        
    }
    
    
endif;



$PDF->Output("orden_cobro_".$pago['OrdenDescuentoCobro']['id'].".pdf");



?>