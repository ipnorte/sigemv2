<?
App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("LISTADO CONTROL TURNO");
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo1'] = (empty($descripcion_turno) ? "*** SIN TURNO DE PAGO ***" : $descripcion_turno);
$PDF->titulo['titulo2'] = 'LIQUIDACION ' . $util->periodo($liquidacion['Liquidacion']['periodo'],true);
$PDF->titulo['titulo3'] = 'LISTADO CONTROL TURNO';

//#  	SOCIO  	REG  	IMPORTE A DEBITAR  	
// # | DNI - APELLIDO Y NOMBRE | REG |  BANCO | SUCURSAL - CUENTA | CBU | IMPORTE


//$W1 = array(5,60,5,35,25,40,20);
$W1 = array(45,5,45,25,30,20,20);
$L1 = $PDF->armaAnchoColumnas($W1);
//generarEncabezado($PDF,$L1,$W1);
$fontSizeHeader = 5;

$PDF->encabezado = array();
$PDF->encabezado[0] = array();	

$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => 'DNI - APELLIDO Y NOMBRE',
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
			'texto' => 'REG',
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
			'texto' => 'BANCO',
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
			'texto' => 'SUCURSAL - CUENTA',
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
			'texto' => 'CBU',
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
			'texto' => 'SALDO',
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
			'texto' => 'DEBITO',
			'borde' => 'TBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);

$PDF->AddPage();
$PDF->reset();
$size = 6;
$reg = 0;

$PDF->linea[5] = array(
	'posx' => $L1[0],
	'ancho' => 170,
	'texto' => "DETALLE DE REGISTROS A PROCESAR",
	'borde' => '',
	'align' => 'L',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => 9
);	

$PDF->Imprimir_linea();
$PDF->ln(2);	
$ACUM_IMPORTE = $ACUM_REG = $ACUM_SALDO = 0;


foreach($socios as $socio):
	$reg++;
	$ACUM_IMPORTE += $socio['LiquidacionSocio']['importe_adebitar'];
    $ACUM_SALDO += $socio['LiquidacionSocio']['saldo_actual'];
    $ACUM_REG  += $socio['LiquidacionSocio']['registro'];
    imprimirRenglon($PDF,$L1,$W1,$socio);
endforeach;
imprimirTotal($PDF,$L1,$W1,$ACUM_REG,$ACUM_IMPORTE,$ACUM_SALDO);
	

if(!empty($socios_error_cbu)):

	$PDF->AddPage();
	$PDF->reset();
	$size = 6;
	$reg = 0;
	
	
	$PDF->linea[5] = array(
		'posx' => $L1[0],
		'ancho' => 170,
		'texto' => "DETALLE DE REGISTROS CON CBU INCORRECTO (NO SE PROCESAN)",
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => 9
	);	
	
	$PDF->Imprimir_linea();
	$PDF->ln(2);

    $ACUM_IMPORTE = $ACUM_REG = $ACUM_SALDO = 0;
	
	foreach($socios_error_cbu as $socio):
		$reg++;
		$ACUM_IMPORTE += $socio['LiquidacionSocio']['importe_adebitar'];
        $ACUM_REG  += $socio['LiquidacionSocio']['registro'];
        $ACUM_SALDO += $socio['LiquidacionSocio']['saldo_actual'];
        imprimirRenglon($PDF,$L1,$W1,$socio);
	endforeach;
	imprimirTotal($PDF,$L1,$W1,$ACUM_REG,$ACUM_IMPORTE,$ACUM_SALDO);

	
endif;

$PDF->Output("listado_diskette.pdf");


function imprimirRenglon($PDF,$L1,$W1,$socio){
    $size = 6;
    $PDF->linea[0] = array(
        'posx' => $L1[0],
        'ancho' => $W1[0],
        'texto' => substr($socio['LiquidacionSocio']['documento'] ." - ". $socio['LiquidacionSocio']['apenom'],0,35),
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
        'texto' => $socio['LiquidacionSocio']['registro'],
        'borde' => '',
        'align' => 'C',
        'fondo' => 0,
        'style' => '',
        'colorf' => '#ccc',
        'size' => $size
    );

    $PDF->linea[2] = array(
        'posx' => $L1[2],
        'ancho' => $W1[2],
        'texto' => substr($socio['LiquidacionSocio']['banco'],0,33),
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
        'texto' => $socio['LiquidacionSocio']['sucursal']."-".$socio['LiquidacionSocio']['nro_cta_bco'],
        'borde' => '',
        'align' => '',
        'fondo' => 0,
        'style' => '',
        'colorf' => '#ccc',
        'size' => $size
    );
    $PDF->linea[4] = array(
        'posx' => $L1[4],
        'ancho' => $W1[4],
        'texto' => $socio['LiquidacionSocio']['cbu'],
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
        'texto' => number_format($socio['LiquidacionSocio']['saldo_actual'],2),
        'borde' => '',
        'align' => 'R',
        'fondo' => 0,
        'style' => '',
        'colorf' => '#ccc',
        'size' => $size
    );
    $PDF->linea[6] = array(
        'posx' => $L1[6],
        'ancho' => $W1[6],
        'texto' => number_format($socio['LiquidacionSocio']['importe_adebitar'],2),
        'borde' => '',
        'align' => 'R',
        'fondo' => 0,
        'style' => '',
        'colorf' => '#ccc',
        'size' => $size
    );    
    $PDF->Imprimir_linea();    
}


function imprimirTotal($PDF,$L1,$W1,$ACUM_REG,$ACUM_IMPORTE,$ACUM_SALDO){
    $size = 8;

    $PDF->linea[4] = array(
        'posx' => $L1[0],
        'ancho' => 177,
        'texto' => "TOTAL ($ACUM_REG REGISTROS) ",
        'borde' => '',
        'align' => 'L',
        'fondo' => 1,
        'style' => 'B',
        'colorf' => '#ccc',
        'size' => $size
    );				
    $PDF->linea[5] = array(
        'posx' => $L1[5],
        'ancho' => $W1[5],
        'texto' => number_format($ACUM_SALDO,2),
        'borde' => '',
        'align' => 'R',
        'fondo' => 1,
        'style' => 'B',
        'colorf' => '#ccc',
        'size' => $size
    );
    $PDF->linea[6] = array(
        'posx' => $L1[6],
        'ancho' => $W1[6],
        'texto' => number_format($ACUM_IMPORTE,2),
        'borde' => '',
        'align' => 'R',
        'fondo' => 1,
        'style' => 'B',
        'colorf' => '#ccc',
        'size' => $size
    );
    $PDF->Imprimir_linea();    
}

?>
