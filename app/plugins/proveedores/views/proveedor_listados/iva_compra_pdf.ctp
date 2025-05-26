<?php 

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF('L');

$PDF->SetTitle("IVA COMPRAS");
$PDF->titulo['titulo1'] = "DESDE FECHA: " . $util->armaFecha($fecha_desde) . " - HASTA FECHA: " . $util->armaFecha($fecha_hasta);
//$PDF->titulo['titulo2'] = "";
$PDF->titulo['titulo2'] = "";
$PDF->titulo['titulo3'] = "IVA COMPRAS";

$PDF->SetFontSizeConf(9);


//$PDF->textoHeader = 'LIBRO DIARIO BORRADOR';

$PDF->Open();

$W0 = array(14, 26, 87, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15);
//$W0 = array(30, 40, 177, 30);
$L0 = $PDF->armaAnchoColumnas($W0);

$PDF->encabezado = array();
$fontSizeHeader = 6;
$fontSizeBody = 5;

	

$PDF->encabezado[0][0] = array(
			'posx' => $L0[0],
			'ancho' => $W0[0],
			'texto' => 'FECHA',
			'borde' => '',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[0][1] = array(
			'posx' => $L0[1],
			'ancho' => $W0[1],
			'texto' => 'COMPROBANTE',
			'borde' => '',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	
$PDF->encabezado[0][2] = array(
			'posx' => $L0[2],
			'ancho' => $W0[2],
			'texto' => 'CONCEPTO',
			'borde' => '',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[0][3] = array(
			'posx' => $L0[3],
			'ancho' => $W0[3],
			'texto' => 'C.U.I.T.',
			'borde' => '',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->encabezado[0][4] = array(
			'posx' => $L0[4],
			'ancho' => $W0[4],
			'texto' => 'NO GRAVADO',
			'borde' => '',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[0][5] = array(
			'posx' => $L0[5],
			'ancho' => $W0[5],
			'texto' => 'GRAVADO',
			'borde' => '',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	
$PDF->encabezado[0][6] = array(
			'posx' => $L0[6],
			'ancho' => $W0[6],
			'texto' => 'IVA',
			'borde' => '',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[0][7] = array(
			'posx' => $L0[7],
			'ancho' => $W0[7],
			'texto' => 'PERCEPCION',
			'borde' => '',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->encabezado[0][8] = array(
			'posx' => $L0[8],
			'ancho' => $W0[8],
			'texto' => 'RETENCION',
			'borde' => '',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[0][9] = array(
			'posx' => $L0[9],
			'ancho' => $W0[9],
			'texto' => 'IMP.INTERNO',
			'borde' => '',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	
$PDF->encabezado[0][10] = array(
			'posx' => $L0[10],
			'ancho' => $W0[10],
			'texto' => 'ING.BRUTO',
			'borde' => '',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[0][11] = array(
			'posx' => $L0[11],
			'ancho' => $W0[11],
			'texto' => 'OTROS IMP.',
			'borde' => '',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->encabezado[0][12] = array(
			'posx' => $L0[12],
			'ancho' => $W0[12],
			'texto' => 'TOTAL',
			'borde' => '',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
	
	///////	


$PDF->AddPage();
$PDF->Reset();
		
$nInoGra = 0; $nIGrava = 0; $nImpIva = 0; $nIPerce = 0; $nIReten = 0; $nIImInt = 0; $nIInBru = 0; $nIOImpu = 0; $nITotCo = 0;
foreach ($facturas as $renglon):
				
	if($renglon['ProveedorFactura']['tipo'] == 'NC'):
		$nInoGra -= $renglon['ProveedorFactura']['importe_no_gravado'];
		$nIGrava -= $renglon['ProveedorFactura']['importe_gravado'];
		$nImpIva -= $renglon['ProveedorFactura']['importe_iva'];
		$nIPerce -= $renglon['ProveedorFactura']['percepcion'];
		$nIReten -= $renglon['ProveedorFactura']['retencion'];
		$nIImInt -= $renglon['ProveedorFactura']['impuesto_interno'];
		$nIInBru -= $renglon['ProveedorFactura']['ingreso_bruto'];
		$nIOImpu -= $renglon['ProveedorFactura']['otro_impuesto'];
		$nITotCo -= $renglon['ProveedorFactura']['total_comprobante'];
	else:
		$nInoGra += $renglon['ProveedorFactura']['importe_no_gravado'];
		$nIGrava += $renglon['ProveedorFactura']['importe_gravado'];
		$nImpIva += $renglon['ProveedorFactura']['importe_iva'];
		$nIPerce += $renglon['ProveedorFactura']['percepcion'];
		$nIReten += $renglon['ProveedorFactura']['retencion'];
		$nIImInt += $renglon['ProveedorFactura']['impuesto_interno'];
		$nIInBru += $renglon['ProveedorFactura']['ingreso_bruto'];
		$nIOImpu += $renglon['ProveedorFactura']['otro_impuesto'];
		$nITotCo += $renglon['ProveedorFactura']['total_comprobante'];
	endif;

$PDF->linea[0] = array(
			'posx' => $L0[0],
			'ancho' => $W0[0],
			'texto' => date('d/m/Y',strtotime($renglon['ProveedorFactura']['fecha_comprobante'])),
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->linea[1] = array(
			'posx' => $L0[1],
			'ancho' => $W0[1],
			'texto' => $renglon['ProveedorFactura']['comprobante_libro'],
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	
$PDF->linea[2] = array(
			'posx' => $L0[2],
			'ancho' => $W0[2],
			'texto' => $renglon['ProveedorFactura']['razon_social'],
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->linea[3] = array(
			'posx' => $L0[3],
			'ancho' => $W0[3],
			'texto' => $renglon['ProveedorFactura']['cuit'],
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->linea[4] = array(
			'posx' => $L0[4],
			'ancho' => $W0[4],
			'texto' => number_format($renglon['ProveedorFactura']['importe_no_gravado'] * ($renglon['ProveedorFactura']['tipo'] == 'NC' ? -1 : 1),2),
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->linea[5] = array(
			'posx' => $L0[5],
			'ancho' => $W0[5],
			'texto' => number_format($renglon['ProveedorFactura']['importe_gravado'] * ($renglon['ProveedorFactura']['tipo'] == 'NC' ? -1 : 1),2),
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	
$PDF->linea[6] = array(
			'posx' => $L0[6],
			'ancho' => $W0[6],
			'texto' => number_format($renglon['ProveedorFactura']['importe_iva'] * ($renglon['ProveedorFactura']['tipo'] == 'NC' ? -1 : 1),2),
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->linea[7] = array(
			'posx' => $L0[7],
			'ancho' => $W0[7],
			'texto' => number_format($renglon['ProveedorFactura']['percepcion'] * ($renglon['ProveedorFactura']['tipo'] == 'NC' ? -1 : 1),2),
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->linea[8] = array(
			'posx' => $L0[8],
			'ancho' => $W0[8],
			'texto' => number_format($renglon['ProveedorFactura']['retencion'] * ($renglon['ProveedorFactura']['tipo'] == 'NC' ? -1 : 1),2),
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->linea[9] = array(
			'posx' => $L0[9],
			'ancho' => $W0[9],
			'texto' => number_format($renglon['ProveedorFactura']['impuesto_interno'] * ($renglon['ProveedorFactura']['tipo'] == 'NC' ? -1 : 1),2),
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	
$PDF->linea[10] = array(
			'posx' => $L0[10],
			'ancho' => $W0[10],
			'texto' => number_format($renglon['ProveedorFactura']['ingreso_bruto'] * ($renglon['ProveedorFactura']['tipo'] == 'NC' ? -1 : 1),2),
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->linea[11] = array(
			'posx' => $L0[11],
			'ancho' => $W0[11],
			'texto' => number_format($renglon['ProveedorFactura']['otro_impuesto'] * ($renglon['ProveedorFactura']['tipo'] == 'NC' ? -1 : 1),2),
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->linea[12] = array(
			'posx' => $L0[12],
			'ancho' => $W0[12],
			'texto' => number_format($renglon['ProveedorFactura']['total_comprobante'] * ($renglon['ProveedorFactura']['tipo'] == 'NC' ? -1 : 1),2),
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
		
	$PDF->Imprimir_linea();

	
	
endforeach;


$PDF->linea[0] = array(
			'posx' => $L0[0],
			'ancho' => 142,
			'texto' => 'TOTAL:',
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->linea[1] = array(
			'posx' => $L0[4],
			'ancho' => $W0[4],
			'texto' => number_format($nInoGra, 2),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	
$PDF->linea[2] = array(
			'posx' => $L0[5],
			'ancho' => $W0[5],
			'texto' => number_format($nIGrava, 2),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->linea[3] = array(
			'posx' => $L0[6],
			'ancho' => $W0[6],
			'texto' => number_format($nImpIva, 2),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->linea[4] = array(
			'posx' => $L0[7],
			'ancho' => $W0[7],
			'texto' => number_format($nIPerce, 2),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->linea[5] = array(
			'posx' => $L0[8],
			'ancho' => $W0[8],
			'texto' => number_format($nIReten, 2),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	
$PDF->linea[6] = array(
			'posx' => $L0[9],
			'ancho' => $W0[9],
			'texto' => number_format($nIImInt, 2),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->linea[7] = array(
			'posx' => $L0[10],
			'ancho' => $W0[10],
			'texto' => number_format($nIInBru, 2),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->linea[8] = array(
			'posx' => $L0[11],
			'ancho' => $W0[11],
			'texto' => number_format($nIOImpu, 2),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->linea[9] = array(
			'posx' => $L0[12],
			'ancho' => $W0[12],
			'texto' => number_format($nITotCo, 2),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
		
	$PDF->Imprimir_linea();



$PDF->Output("iva_compra.pdf");
exit;

?>
