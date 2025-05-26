<?
//debug($socios_error_cbu);
//debug($resumenTurnos);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("LISTADO SOPORTE DISKETTE CBU :: $banco_intercambio :: ". $util->periodo($liquidacion['Liquidacion']['periodo'],true));
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo1'] = "$banco_intercambio";
$PDF->titulo['titulo2'] = 'LIQUIDACION ' . $util->periodo($liquidacion['Liquidacion']['periodo'],true) ." | FECHA DEBITO: ".$util->armaFecha($fechaDebito);
$PDF->titulo['titulo3'] = 'LISTADO DE SOPORTE DE DISKETTE';

//#  	SOCIO  	REG  	IMPORTE A DEBITAR  	
// # | DNI - APELLIDO Y NOMBRE | REG |  EMPRESA | CBU | IMPORTE


$W1 = array(7,48,5,60,27,23,20);
//$W1 = array(5,60,5,70,30,20);
$L1 = $PDF->armaAnchoColumnas($W1);

$fontSizeHeader = 5;

$PDF->encabezado = array();
$PDF->encabezado[0] = array();	

$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => '#',
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
			'texto' => 'DNI - APELLIDO Y NOMBRE',
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
			'texto' => 'REG',
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
			'texto' => 'EMPRESA',
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
			'texto' => 'SUCURSAL - CUENTA',
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
			'texto' => 'CBU',
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
			'texto' => 'IMPORTE',
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

$ACUM_IMPORTE = 0;

$PDF->linea[5] = array(
	'posx' => $L1[0],
	'ancho' => 170,
	'texto' => "DETALLE DE REGISTROS ENVIADOS EN DISKETTE",
	'borde' => '',
	'align' => 'L',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => 9
);	

$PDF->Imprimir_linea();
$PDF->ln(2);	

foreach($socios as $socio):

	$reg++;
	
	$ACUM_IMPORTE += $socio['LiquidacionSocio']['importe_adebitar'];

	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0],
		'texto' => $reg,
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
		'texto' => substr($socio['LiquidacionSocio']['documento'] ." - ". $socio['LiquidacionSocio']['apenom'],0,37),
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
		'texto' => $socio['LiquidacionSocio']['registro'],
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);

	$PDF->linea[3] = array(
		'posx' => $L1[3],
		'ancho' => $W1[3],
		'texto' => substr($socio['LiquidacionSocio']['descripcion'],0,45),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);
	$PDF->linea[4] = array(
		'posx' => $L1[4],
		'ancho' => $W1[4],
		'texto' => $socio['LiquidacionSocio']['sucursal']."-".$socio['LiquidacionSocio']['nro_cta_bco'],
		'borde' => '',
		'align' => '',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);
	$PDF->linea[5] = array(
		'posx' => $L1[5],
		'ancho' => $W1[5],
		'texto' => $socio['LiquidacionSocio']['cbu'],
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);				
	$PDF->linea[6] = array(
		'posx' => $L1[6],
		'ancho' => $W1[6],
		'texto' => $util->nf($socio['LiquidacionSocio']['importe_adebitar']),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);
	
	$PDF->Imprimir_linea();	

endforeach;

$size = 8;

$PDF->linea[5] = array(
	'posx' => $L1[0],
	'ancho' => 170,
	'texto' => "TOTAL ($reg REGISTROS) ",
	'borde' => '',
	'align' => 'L',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size
);				
$PDF->linea[6] = array(
	'posx' => $L1[6],
	'ancho' => $W1[6],
	'texto' => $util->nf($ACUM_IMPORTE),
	'borde' => '',
	'align' => 'R',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size
);

$PDF->Imprimir_linea();	

if(!empty($socios_error_cbu)):

	$PDF->AddPage();
	$PDF->reset();
	$size = 6;
	$reg = 0;
	
	$ACUM_IMPORTE = 0;
	
	$PDF->linea[5] = array(
		'posx' => $L1[0],
		'ancho' => 170,
		'texto' => "DETALLE DE REGISTROS NO ENVIADOS EN DISKETTE",
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => 9
	);	
	
	$PDF->Imprimir_linea();
	$PDF->ln(2);
	
	foreach($socios_error_cbu as $socio):
	
		$reg++;
		
		$ACUM_IMPORTE += $socio['LiquidacionSocio']['importe_adebitar'];
	
		$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => $reg,
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
			'texto' => substr($socio['LiquidacionSocio']['documento'] ." - ". $socio['LiquidacionSocio']['apenom'],0,70),
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
			'texto' => $socio['LiquidacionSocio']['registro'],
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $size
		);
	
		$PDF->linea[3] = array(
			'posx' => $L1[3],
			'ancho' => $W1[3],
			'texto' => $util->globalDato($socio['LiquidacionSocio']['codigo_empresa'],'concepto_1') . (!empty($socio['LiquidacionSocio']['codigo_reparticion']) ? " - " . $socio['LiquidacionSocio']['codigo_reparticion'] : ""),
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $size
		);
		$PDF->linea[4] = array(
			'posx' => $L1[4],
			'ancho' => $W1[4],
			'texto' => $socio['LiquidacionSocio']['sucursal']."-".$socio['LiquidacionSocio']['nro_cta_bco'],
			'borde' => '',
			'align' => '',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $size
		);
		$PDF->linea[5] = array(
			'posx' => $L1[5],
			'ancho' => $W1[5],
			'texto' => $socio['LiquidacionSocio']['cbu'],
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $size
		);				
		$PDF->linea[6] = array(
			'posx' => $L1[6],
			'ancho' => $W1[6],
			'texto' => $util->nf($socio['LiquidacionSocio']['importe_adebitar']),
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $size
		);
		
		$PDF->Imprimir_linea();	
	
	endforeach;
	
	$size = 8;
	
	$PDF->linea[5] = array(
		'posx' => $L1[0],
		'ancho' => 170,
		'texto' => "TOTAL ($reg REGISTROS) ",
		'borde' => '',
		'align' => 'L',
		'fondo' => 1,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $size
	);				
	$PDF->linea[6] = array(
		'posx' => $L1[6],
		'ancho' => $W1[6],
		'texto' => $util->nf($ACUM_IMPORTE),
		'borde' => '',
		'align' => 'R',
		'fondo' => 1,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $size
	);
	
	$PDF->Imprimir_linea();
		
endif;

if(!empty($resumenTurnos)):

	$PDF->AddPage();
	$PDF->reset();
	$size = 7;
	$reg = 0;
	
	$ACUM_IMPORTE = 0;
	
	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => 170,
		'texto' => "RESUMEN DE CONTROL POR TURNO ENVIADO EN DISKETTE",
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => 9
	);	
	
	$PDF->Imprimir_linea();
	$PDF->ln(2);

	$ACU_IMPORTE = 0;
	$ACU_CANTIDAD = 0;
	
	foreach($resumenTurnos as $turno):

		$ACU_IMPORTE += $turno['LiquidacionSocio']['importe_adebitar'];
		$ACU_CANTIDAD += $turno['LiquidacionSocio']['cantidad'];	
	
		$PDF->linea[0] = array(
			'posx' => 10,
			'ancho' => 130,
			'texto' => $turno['LiquidacionSocio']['descripcion'],
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => 7
		);
		$PDF->linea[1] = array(
			'posx' => 140,
			'ancho' => 5,
			'texto' => $turno['LiquidacionSocio']['cantidad'],
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => 7
		);
		$PDF->linea[2] = array(
			'posx' => 145,
			'ancho' => 30,
			'texto' => $util->nf($turno['LiquidacionSocio']['importe_adebitar']),
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => 7
		);		
		$PDF->Imprimir_linea();
	
	endforeach;
	$PDF->linea[0] = array(
		'posx' => 10,
		'ancho' => 130,
		'texto' => "TOTALES",
		'borde' => 'T',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => 7
	);	
	$PDF->linea[1] = array(
		'posx' => 140,
		'ancho' => 5,
		'texto' => $ACU_CANTIDAD,
		'borde' => 'T',
		'align' => 'C',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => 7
	);
	$PDF->linea[2] = array(
		'posx' => 145,
		'ancho' => 30,
		'texto' => $util->nf($ACU_IMPORTE),
		'borde' => 'T',
		'align' => 'R',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => 7
	);		
	$PDF->Imprimir_linea();	
	
endif;

$PDF->Output("listado_diskette.pdf");


?>
