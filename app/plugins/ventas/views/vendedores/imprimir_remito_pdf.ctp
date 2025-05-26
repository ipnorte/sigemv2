<?php 

// debug($remito);
// exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("CONSTANCIA DE PRESENTACION DE SOLICITUDES #".$remito['VendedorRemito']['id']);
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

$PDF->textoHeader = "NRO: ".$remito['VendedorRemito']['id'];
$PDF->titulo['titulo3'] = "CONSTANCIA DE PRESENTACION";
// $PDF->titulo['titulo2'] = "FECHA : " . $remito['VendedorRemito']['created'];
// $PDF->titulo['titulo1'] = "EMITIDA POR : " . $remito['VendedorRemito']['user_created'];


$PDF->AddPage();
$PDF->reset();
$fontSize = 10;

$PDF->linea[0] = array(
		'posx' => 10,
		'ancho' => 30,
		'texto' => "NUMERO:",
		'borde' => 'TL',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $fontSize
);
$PDF->linea[1] = array(
		'posx' => 40,
		'ancho' => 160,
		'texto' => $remito['VendedorRemito']['id'],
		'borde' => 'TR',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $fontSize
);
$PDF->Imprimir_linea();

$PDF->linea[0] = array(
		'posx' => 10,
		'ancho' => 30,
		'texto' => "FECHA:",
		'borde' => 'L',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $fontSize
);
$PDF->linea[1] = array(
		'posx' => 40,
		'ancho' => 160,
		'texto' => date('d/m/Y h:i:s', strtotime($remito['VendedorRemito']['created'])),
		'borde' => 'R',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $fontSize
);
$PDF->Imprimir_linea();

$PDF->linea[0] = array(
		'posx' => 10,
		'ancho' => 30,
		'texto' => "EMITIDA POR:",
		'borde' => 'L',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $fontSize
);
$PDF->linea[1] = array(
		'posx' => 40,
		'ancho' => 160,
		'texto' => $remito['VendedorRemito']['user_created'],
		'borde' => 'R',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $fontSize
);
$PDF->Imprimir_linea();

$PDF->linea[0] = array(
		'posx' => 10,
		'ancho' => 30,
		'texto' => "VENDEDOR:",
		'borde' => 'LB',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $fontSize
);
$PDF->linea[1] = array(
		'posx' => 40,
		'ancho' => 160,
		'texto' => "#".$remito['Vendedor']['vendedor']['id']." - ".$remito['Vendedor']['Persona']['tdoc_ndoc_apenom'],
		'borde' => 'RB',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $fontSize
);
$PDF->Imprimir_linea();

$PDF->ln(5);

$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 190,
		'texto' => "DETALLE DE SOLICITUDES",
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#F5f7f7',
		'size' => 10
);
$PDF->Imprimir_linea();

$PDF->ln(3);

$W1 = array(0,20,20,50,10,30,15,20);
$L1 = $PDF->armaAnchoColumnas($W1);
// 277
// 200
$size = 8;
$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 15,
		'texto' => "NUMERO",
		'borde' => 'LBT',
		'align' => 'C',
		'fondo' => 1,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->linea[2] = array(
		'posx' => 25,
		'ancho' => 20,
		'texto' => "FECHA",
		'borde' => 'BT',
		'align' => 'C',
		'fondo' => 1,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->linea[3] = array(
		'posx' => 45,
		'ancho' => 80,
		'texto' => "SOLICITANTE",
		'borde' => 'BT',
		'align' => 'C',
		'fondo' => 1,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->linea[4] = array(
		'posx' => 125,
		'ancho' => 20,
		'texto' => "CAPITAL",
		'borde' => 'BT',
		'align' => 'C',
		'fondo' => 1,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->linea[5] = array(
		'posx' => 145,
		'ancho' => 20,
		'texto' => "SOLICITADO",
		'borde' => 'BT',
		'align' => 'C',
		'fondo' => 1,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->linea[6] = array(
		'posx' => 165,
		'ancho' => 15,
		'texto' => "CUOTAS",
		'borde' => 'BT',
		'align' => 'C',
		'fondo' => 1,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->linea[7] = array(
		'posx' => 180,
		'ancho' => 20,
		'texto' => "IMPORTE",
		'borde' => 'BTR',
		'align' => 'C',
		'fondo' => 1,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->Imprimir_linea();

if(!empty($remito['MutualProductoSolicitud'])):
	$size = 8;
	foreach($remito['MutualProductoSolicitud'] as $solicitud):
		$PDF->linea[1] = array(
				'posx' => 10,
				'ancho' => 15,
				'texto' => $solicitud['nro_print'],
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
		$PDF->linea[2] = array(
				'posx' => 25,
				'ancho' => 20,
				'texto' => $util->armaFecha($solicitud['fecha']),
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
		$PDF->linea[3] = array(
				'posx' => 45,
				'ancho' => 80,
				'texto' => substr($solicitud['beneficiario'],0,48),
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => $size
		);	
		$PDF->linea[4] = array(
				'posx' => 125,
				'ancho' => 20,
				'texto' => number_format($solicitud['importe_solicitado'],2),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
		$PDF->linea[5] = array(
				'posx' => 145,
				'ancho' => 20,
				'texto' => number_format($solicitud['importe_percibido'],2),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
		$PDF->linea[6] = array(
				'posx' => 165,
				'ancho' => 15,
				'texto' => $solicitud['cuotas'],
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
		$PDF->linea[7] = array(
				'posx' => 180,
				'ancho' => 20,
				'texto' => number_format($solicitud['importe_cuota'],2),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size - 1
		);				
		$PDF->Imprimir_linea();
		$PDF->linea[2] = array(
				'posx' => 10,
				'ancho' => 35,
				'texto' => "",
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size - 2
		);
		$PDF->linea[3] = array(
				'posx' => 45,
				'ancho' => 155,
				'texto' => $solicitud['beneficio_str'],
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size - 1
		);		
		$PDF->Imprimir_linea();
		$PDF->Ln(3);
	endforeach;

endif;

$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 190,
		'texto' => "",
		'borde' => 'T',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#F5f7f7',
		'size' => 10
);
$PDF->Imprimir_linea();

$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 190,
		'texto' => "OBSERVACIONES",
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#F5f7f7',
		'size' => 8
);
$PDF->Imprimir_linea();

$PDF->SetX(10);
$PDF->MultiCell(0,0,str_replace("\n","",substr($remito['VendedorRemito']['observaciones'],0,250)),1,'L');
$PDF->Ln(25);


$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 60,
		'texto' => "POR VENDEDOR",
		'borde' => 'T',
		'align' => 'C',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#F5f7f7',
		'size' => 6
);
$PDF->linea[2] = array(
		'posx' => 130,
		'ancho' => 60,
		'texto' => "POR MUTUAL",
		'borde' => 'T',
		'align' => 'C',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#F5f7f7',
		'size' => 6
);
$PDF->Imprimir_linea();

$PDF->Output("CONSTANCIA_PRESENTACION_SOLICITUDES_".$remito['VendedorRemito']['id'].".pdf");

?>