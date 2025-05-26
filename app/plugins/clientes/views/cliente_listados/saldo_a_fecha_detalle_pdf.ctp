<?php

App::import('Vendor','listado_pdf');

App::import('Model', 'clientes.ClienteListado');
App::import('Model', 'clientes.Cliente');

$PDF = new ListadoPDF('L');

$oClienteListado = new ClienteListado();
$oCliente = new Cliente();


$PDF->SetTitle("SALDO A FECHA");
$PDF->titulo['titulo1'] = "CUENTA CORRIENTE";
$PDF->titulo['titulo2'] = "DESDE FECHA: " . $util->armaFecha($desdeFecha) . " - HASTA FECHA: " . $util->armaFecha($hastaFecha);
$PDF->titulo['titulo3'] = "";

$PDF->SetFontSizeConf(9);


$PDF->Open();

$W0 = array(18, 49, 144, 22, 22, 22);
$L0 = $PDF->armaAnchoColumnas($W0);

$PDF->encabezado = array();
$fontSizeHeader = 8;
$fontSizeBody = 7;


$PDF->encabezado[0][0] = array(
			'posx' => $L0[0],
			'ancho' => $W0[0],
			'texto' => 'FECHA',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[0][1] = array(
			'posx' => $L0[1],
			'ancho' => $W0[1],
			'texto' => 'CONCEPTO',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	
$PDF->encabezado[0][2] = array(
			'posx' => $L0[2],
			'ancho' => $W0[2],
			'texto' => 'REFERENCIA',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[0][3] = array(
			'posx' => $L0[3],
			'ancho' => $W0[3],
			'texto' => 'D E B E',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[0][4] = array(
			'posx' => $L0[4],
			'ancho' => $W0[4],
			'texto' => 'H A B E R',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->encabezado[0][5] = array(
			'posx' => $L0[5],
			'ancho' => $W0[5],
			'texto' => 'S A L D O',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
		
	///////	
	
foreach($saldos as $saldo):

	if($saldo[0]['saldo_anterior'] != 0 || $saldo[0]['cobro'] != 0 || $saldo[0]['credito'] != 0 || $saldo[0]['debito'] != 0 ||
	   $saldo[0]['saldo'] != 0 || $saldo[0]['saldo_actual'] != 0):
		$cliente = $oCliente->getCliente($saldo['Cliente']['id']);

		$ctaCte = $oClienteListado->ctaCteFecha($saldo['Cliente']['id'], $desdeFecha, $hastaFecha);
		$PDF->titulo['titulo3'] = $saldo['Cliente']['cuit'] . ' - ' . ($saldo['Cliente']['razon_social_resumida'] == "" ? $saldo['Cliente']['razon_social'] : $saldo['Cliente']['razon_social_resumida']);
	
		$PDF->AddPage();
		$PDF->Reset();

		$saldo_anterior = $saldo[0]['saldo_anterior'];

		$PDF->linea[0] = array(
			'posx' => $L0[0],
			'ancho' => $W0[0],
			'texto' => '',
			'borde' => 'LR',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
							
		$PDF->linea[1] = array(
			'posx' => $L0[1],
			'ancho' => $W0[1],
			'texto' => 'SALDO AL ' . $util->armaFecha($fecha_saldo_anterior),
			'borde' => 'LR',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
							
		$PDF->linea[2] = array(
			'posx' => $L0[2],
			'ancho' => $W0[2],
			'texto' => '',
			'borde' => 'LR',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
							
		$PDF->linea[3] = array(
			'posx' => $L0[3],
			'ancho' => $W0[3],
			'texto' => '',
			'borde' => 'LR',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
							
		$PDF->linea[4] = array(
			'posx' => $L0[4],
			'ancho' => $W0[4],
			'texto' => '',
			'borde' => 'LR',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
							
		$PDF->linea[5] = array(
			'posx' => $L0[5],
			'ancho' => $W0[5],
			'texto' => number_format($saldo_anterior,2, ',','.'),
			'borde' => 'LR',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeBody
		);
							
							
		$PDF->Imprimir_linea();
		
		foreach ($ctaCte as $renglon):
			$saldo_anterior += $renglon['debe'] - $renglon['haber'];

			$PDF->linea[0] = array(
				'posx' => $L0[0],
				'ancho' => $W0[0],
				'texto' => $util->armaFecha($renglon['fecha']),
				'borde' => 'LR',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);
							
			$PDF->linea[1] = array(
				'posx' => $L0[1],
				'ancho' => $W0[1],
				'texto' => $renglon['concepto'],
				'borde' => 'LR',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);
							
			$PDF->linea[2] = array(
				'posx' => $L0[2],
				'ancho' => $W0[2],
				'texto' => substr($renglon['comentario'],0,95),
				'borde' => 'LR',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);
							
			$PDF->linea[3] = array(
				'posx' => $L0[3],
				'ancho' => $W0[3],
				'texto' => ($renglon['debe'] == 0  ? '' : number_format($renglon['debe'],2, ',','.')),
				'borde' => 'LR',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);
							
			$PDF->linea[4] = array(
				'posx' => $L0[4],
				'ancho' => $W0[4],
				'texto' => ($renglon['haber'] == 0 ? '' : number_format($renglon['haber'],2, ',','.')),
				'borde' => 'LR',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);
							
			$PDF->linea[5] = array(
				'posx' => $L0[5],
				'ancho' => $W0[5],
				'texto' => number_format($saldo_anterior,2, ',','.'),
				'borde' => 'LR',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);
							
							
			$PDF->Imprimir_linea();
		
			
		endforeach;	
	endif;
endforeach; 

$PDF->Output("cta-cte-detalle.pdf");
exit;

?>