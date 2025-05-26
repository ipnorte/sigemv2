<?php

//debug($mora_cuota_uno);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF('L');

$PDF->SetTitle("MORA PRIMER CUOTA");
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo1'] = "";
$PDF->titulo['titulo2'] = 'LIQUIDACION ' . $liquidacion['Liquidacion']['organismo'] . " | " . $liquidacion['Liquidacion']['periodo_desc'];
$PDF->titulo['titulo3'] = 'MORA PRIMER CUOTA';


$W1 = array(15,50,55,15,20,30,30,30,12,20);
$L1 = $PDF->armaAnchoColumnas($W1);

$PDF->bMargen = 10;

$fontSizeHeader = 7;

$PDF->encabezado = array();
$PDF->encabezado[0] = array();	
$PDF->encabezado[0][0] = array(
    'posx' => $L1[0],
    'ancho' => $W1[0],
    'texto' => 'DOCUMENTO',
    'borde' => 'LTB',
    'align' => 'C',
    'fondo' => 1,
    'style' => '',
    'colorf' => '#ccc',
    'size' => $fontSizeHeader
);
	
$PDF->encabezado[0][1] = array(
    'posx' => $L1[1],
    'ancho' => $W1[1],
    'texto' => 'APELLIDO Y NOMBRE',
    'borde' => 'TB',
    'align' => 'C',
    'fondo' => 1,
    'style' => '',
    'colorf' => '#ccc',
    'size' => $fontSizeHeader
);
$PDF->encabezado[0][2] = array(
    'posx' => $L1[2],
    'ancho' => $W1[2],
    'texto' => 'EMPRESA/REPARTICION',
    'borde' => 'TB',
    'align' => 'C',
    'fondo' => 1,
    'style' => '',
    'colorf' => '#ccc',
    'size' => $fontSizeHeader
);
$PDF->encabezado[0][3] = array(
    'posx' => $L1[3],
    'ancho' => $W1[3],
    'texto' => 'ORDEN DTO',
    'borde' => 'TB',
    'align' => 'C',
    'fondo' => 1,
    'style' => '',
    'colorf' => '#ccc',
    'size' => $fontSizeHeader
);
$PDF->encabezado[0][4] = array(
    'posx' => $L1[4],
    'ancho' => $W1[4],
    'texto' => 'TIPO NRO',
    'borde' => 'TB',
    'align' => 'C',
    'fondo' => 1,
    'style' => '',
    'colorf' => '#ccc',
    'size' => $fontSizeHeader
);
$PDF->encabezado[0][5] = array(
    'posx' => $L1[5],
    'ancho' => $W1[5],
    'texto' => 'PROVEEDOR',
    'borde' => 'TB',
    'align' => 'C',
    'fondo' => 1,
    'style' => '',
    'colorf' => '#ccc',
    'size' => $fontSizeHeader
);
$PDF->encabezado[0][6] = array(
    'posx' => $L1[6],
    'ancho' => $W1[6],
    'texto' => 'PRODUCTO',
    'borde' => 'TB',
    'align' => 'C',
    'fondo' => 1,
    'style' => '',
    'colorf' => '#ccc',
    'size' => $fontSizeHeader
);
$PDF->encabezado[0][7] = array(
    'posx' => $L1[7],
    'ancho' => $W1[7],
    'texto' => 'CONCEPTO',
    'borde' => 'TB',
    'align' => 'C',
    'fondo' => 1,
    'style' => '',
    'colorf' => '#ccc',
    'size' => $fontSizeHeader
);
$PDF->encabezado[0][8] = array(
    'posx' => $L1[8],
    'ancho' => $W1[8],
    'texto' => 'FEC.DEBITO',
    'borde' => 'TB',
    'align' => 'C',
    'fondo' => 1,
    'style' => '',
    'colorf' => '#ccc',
    'size' => $fontSizeHeader
);
$PDF->encabezado[0][9] = array(
    'posx' => $L1[9],
    'ancho' => $W1[9],
    'texto' => 'SALDO',
    'borde' => 'TBR',
    'align' => 'C',
    'fondo' => 1,
    'style' => '',
    'colorf' => '#ccc',
    'size' => $fontSizeHeader
);
$PDF->AddPage();
$PDF->reset();
$size = 7;

$TOTAL = 0;

if(!empty($mora_cuota_uno)){
    
    foreach ($mora_cuota_uno as $orden){

	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0],
		'texto' => $orden['p']['documento'],
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);

	$PDF->linea[1] = array(
		'posx' => $L1[1],
		'ancho' => $W1[1],
		'texto' => substr($orden['p']['apellido']." ".$orden['p']['nombre'],0,32),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);
        
	$PDF->linea[2] = array(
		'posx' => $L1[2],
		'ancho' => $W1[2],
		'texto' => substr($orden['e']['concepto_1'],0,37),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);        
        
	$PDF->linea[3] = array(
		'posx' => $L1[3],
		'ancho' => $W1[3],
		'texto' => $orden['o']['id'],
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	); 
        
	$PDF->linea[4] = array(
		'posx' => $L1[4],
		'ancho' => $W1[4],
		'texto' => $orden['o']['tipo_orden_dto']." #".$orden['o']['numero'],
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);        
        
	$PDF->linea[5] = array(
		'posx' => $L1[5],
		'ancho' => $W1[5],
		'texto' => substr($orden['pr']['razon_social_resumida'],0,20),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);          
        
	$PDF->linea[6] = array(
		'posx' => $L1[6],
		'ancho' => $W1[6],
		'texto' => substr($orden['tp']['concepto_1'],0,20),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	); 

	$PDF->linea[7] = array(
		'posx' => $L1[7],
		'ancho' => $W1[7],
		'texto' => substr($orden['tc']['concepto_1'],0,15),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);
        
	$PDF->linea[8] = array(
		'posx' => $L1[8],
		'ancho' => $W1[8],
		'texto' => $util->armaFecha($orden['lsr']['fecha_debito']),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);        

	$PDF->linea[9] = array(
		'posx' => $L1[9],
		'ancho' => $W1[9],
		'texto' => $util->nf($orden[0]['saldo_cuota']),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);         
        
        $PDF->Imprimir_linea();
        
        $TOTAL += $orden[0]['saldo_cuota'];
        
    }    
    
}

$PDF->linea[8] = array(
        'posx' => $L1[8],
        'ancho' => $W1[8],
        'texto' => "TOTAL",
        'borde' => '',
        'align' => 'R',
        'fondo' => 0,
        'style' => 'B',
        'colorf' => '#ccc',
        'size' => $size
);
$PDF->linea[9] = array(
        'posx' => $L1[9],
        'ancho' => $W1[9],
        'texto' => $util->nf($TOTAL),
        'borde' => 'T',
        'align' => 'R',
        'fondo' => 0,
        'style' => 'B',
        'colorf' => '#ccc',
        'size' => $size
);
$PDF->Imprimir_linea();
$PDF->Output("mora_cuota_uno_".$liquidacion['Liquidacion']['periodo']);

?>
