<?php 

App::import('Vendor','listado_pdf');

App::import('Model', 'clientes.ClienteListado');

$PDF = new ListadoPDF();

$oClienteListado = new ClienteListado();

$PDF->SetTitle("LISTADO POR TIPO DE ASIENTO");
$PDF->titulo['titulo1'] = "LISTADO POR TIPO DE ASIENTO";
$PDF->titulo['titulo2'] = "DESDE FECHA: " . $util->armaFecha($desdeFecha) . " - HASTA FECHA: " . $util->armaFecha($hastaFecha);
$PDF->titulo['titulo3'] = "";

$PDF->SetFontSizeConf(9);


$PDF->textoHeader = '';

$PDF->Open();

$W0 = array(18, 35, 92, 20, 25);
$L0 = $PDF->armaAnchoColumnas($W0);

$PDF->encabezado = array();
$fontSizeHeader = 9;
$fontSizeBody = 8;

	

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
			'texto' => 'TOTAL',
			'borde' => '',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	
	
	
	///////	



foreach($aTipoAsiento as $tipoAsiento):

	if($tipoAsiento[0]['facturado'] > 0 || $tipoAsiento[0]['credito'] > 0):
		$facturas = $oClienteListado->factura_tipo_asiento_detalle($tipoAsiento['ClienteTipoAsiento']['id'], $desdeFecha, $hastaFecha);
		$PDF->titulo['titulo3'] = $tipoAsiento['ClienteTipoAsiento']['concepto'];
		
		$PDF->AddPage();
		$PDF->Reset();
				
		$nTotalComprobante = 0;
		foreach ($facturas as $renglon):
						
			if($renglon['ClienteFactura']['tipo'] == 'NC'):
				$nTotalComprobante -= $renglon['ClienteFactura']['total_comprobante']; 
			else:
				$nTotalComprobante += $renglon['ClienteFactura']['total_comprobante']; 
			endif;
		
			$PDF->linea[0] = array(
						'posx' => $L0[0],
						'ancho' => $W0[0],
						'texto' => date('d/m/Y',strtotime($renglon['ClienteFactura']['fecha_comprobante'])),
						'borde' => '',
						'align' => 'C',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
				
			$PDF->linea[1] = array(
						'posx' => $L0[1],
						'ancho' => $W0[1],
						'texto' => $renglon['ClienteFactura']['comprobante_libro'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);	
				
			$PDF->linea[2] = array(
						'posx' => $L0[2],
						'ancho' => $W0[2],
						'texto' => $renglon['Cliente']['razon_social'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
				
			$PDF->linea[3] = array(
						'posx' => $L0[3],
						'ancho' => $W0[3],
						'texto' => $renglon['Cliente']['cuit'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);
					
			$PDF->linea[4] = array(
						'posx' => $L0[4],
						'ancho' => $W0[4],
						'texto' => number_format($renglon['ClienteFactura']['total_comprobante'] * ($renglon['ClienteFactura']['tipo'] == 'NC' ? -1 : 1),2),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSizeBody
				);	
				
			
				
			$PDF->Imprimir_linea();
		
			
			
		endforeach;
		
		
		$PDF->linea[0] = array(
					'posx' => $L0[0],
					'ancho' => 165,
					'texto' => 'TOTAL:',
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
			
		$PDF->linea[1] = array(
					'posx' => $L0[4],
					'ancho' => $W0[4],
					'texto' => number_format($nTotalComprobante, 2),
					'borde' => 'T',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);	
			
			
				
		$PDF->Imprimir_linea();

	endif;
endforeach;

$PDF->Output("tipo-asiento.pdf");
exit;

?>
