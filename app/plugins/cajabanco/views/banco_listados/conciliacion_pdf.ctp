<?php 

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF('L');

$PDF->SetTitle("CONCILIACION BANCO");

$PDF->titulo['titulo1'] = "IDENTIFICACION: " . $saldos['BancoCuentaSaldo']['numero_extracto'];
$PDF->titulo['titulo2'] = "FECHA: " . $util->armaFecha($saldos['BancoCuentaSaldo']['fecha_cierre']);
$PDF->titulo['titulo3'] = $cuenta['BancoCuenta']['banco'] . ' - ' . $cuenta['BancoCuenta']['numero'];

$PDF->SetFontSizeConf(9);

$PDF->textoHeader = "CONCILIACION BANCARIA";

$PDF->Open();

$W0 = array(69, 69, 69, 70);
$L0 = $PDF->armaAnchoColumnas($W0);

//$W1 = array(30, 30, 30, 30, 37, 30, 30, 30, 30);
$W1 = array(15, 15, 50, 20, 60, 57, 20, 20, 20);
$L1 = $PDF->armaAnchoColumnas($W1);

$PDF->encabezado = array();
$fontSizeHeader = 7;
$fontSizeBody = 6.5;

$PDF->AddPage();
$PDF->Reset();
	
$i = 0;

$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => 'FECHA',
			'borde' => 'LTB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[0][1] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => 'VENCIM.',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	
$PDF->encabezado[0][2] = array(
			'posx' => $L1[2],
			'ancho' => $W1[2],
			'texto' => 'CONCEPTO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
	
$PDF->encabezado[0][3] = array(
			'posx' => $L1[3],
			'ancho' => $W1[3],
			'texto' => 'NRO.OPER.',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->encabezado[0][4] = array(
			'posx' => $L1[4],
			'ancho' => $W1[4],
			'texto' => 'R E F E R E N C I A',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
		
$PDF->encabezado[0][5] = array(
			'posx' => $L1[5],
			'ancho' => $W1[5],
			'texto' => 'D E S C R I P C I O N',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
		
$PDF->encabezado[0][6] = array(
			'posx' => $L1[6],
			'ancho' => $W1[6],
			'texto' => 'DEBE',
			'borde' => 'TB',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
		
$PDF->encabezado[0][7] = array(
			'posx' => $L1[7],
			'ancho' => $W1[7],
			'texto' => 'HABER',
			'borde' => 'TB',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
		
$PDF->encabezado[0][8] = array(
			'posx' => $L1[8],
			'ancho' => $W1[8],
			'texto' => 'SALDO',
			'borde' => 'TBR',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
	///////	

	

	$PDF->linea[0] = array(
			'posx' => $L0[0],
			'ancho' => 135,
			'texto' => 'LIBRO BANCO',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 12
	);
						
	$PDF->linea[1] = array(
			'posx' => $L0[2],
			'ancho' => 139,
			'texto' => 'EXTRACTO BANCO',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 12
	);
						
	$PDF->Imprimir_linea();
				
	// CUADRO DE SALDOS
	$PDF->linea[0] = array(
			'posx' => $L0[0],
			'ancho' => $W0[0],
			'texto' => 'SALDO ANTERIOR:',
			'borde' => 'L',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);
				
	$PDF->linea[1] = array(
			'posx' => $L0[1],
			'ancho' => 66,
			'texto' => number_format($saldos['BancoCuentaSaldo']['saldo_anterior'],2),
			'borde' => 'R',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);
						
	$PDF->linea[2] = array(
			'posx' => $L0[2],
			'ancho' => $W0[2],
			'texto' => 'SALDO AL INICIO:',
			'borde' => 'L',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);
	
	$PDF->linea[3] = array(
			'posx' => $L0[3],
			'ancho' => $W0[3],
			'texto' => number_format($saldos['BancoCuentaSaldo']['saldo_anterior'],2),
			'borde' => 'R',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);
						
	$PDF->Imprimir_linea();
				

	// CUADRO DE SALDOS LINEA 2
	$PDF->linea[0] = array(
			'posx' => $L0[0],
			'ancho' => $W0[0],
			'texto' => 'SALDO LIBRO BANCO:',
			'borde' => 'L',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);
				
	$PDF->linea[1] = array(
			'posx' => $L0[1],
			'ancho' => 66,
			'texto' => number_format($saldos['BancoCuentaSaldo']['saldo_referencia_1'],2),
			'borde' => 'R',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);
						
	$PDF->linea[2] = array(
			'posx' => $L0[2],
			'ancho' => $W0[2],
			'texto' => 'DEBITOS:',
			'borde' => 'L',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);
	
	$PDF->linea[3] = array(
			'posx' => $L0[3],
			'ancho' => $W0[3],
			'texto' => number_format($saldos['BancoCuentaSaldo']['debe'],2),
			'borde' => 'R',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);
						
	$PDF->Imprimir_linea();
				
	
	// CUADRO DE SALDOS LINEA 3
	$PDF->linea[0] = array(
			'posx' => $L0[0],
			'ancho' => $W0[0],
			'texto' => 'SALDO NO CONCILIADO:',
			'borde' => 'L',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);
				
	$PDF->linea[1] = array(
			'posx' => $L0[1],
			'ancho' => 66,
			'texto' => number_format($saldos['BancoCuentaSaldo']['saldo_referencia_2'],2),
			'borde' => 'R',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);
						
	$PDF->linea[2] = array(
			'posx' => $L0[2],
			'ancho' => $W0[2],
			'texto' => 'CREDITOS:',
			'borde' => 'L',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);
	
	$PDF->linea[3] = array(
			'posx' => $L0[3],
			'ancho' => $W0[3],
			'texto' => number_format($saldos['BancoCuentaSaldo']['haber'],2),
			'borde' => 'R',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);
						
	$PDF->Imprimir_linea();
				
	
	
	// CUADRO DE SALDOS LINEA 4
	$PDF->linea[0] = array(
			'posx' => $L0[0],
			'ancho' => $W0[0],
			'texto' => 'SALDO BANCO:',
			'borde' => 'LTB',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);
				
	$PDF->linea[1] = array(
			'posx' => $L0[1],
			'ancho' => 66,
			'texto' => number_format($saldos['BancoCuentaSaldo']['saldo_conciliacion'],2),
			'borde' => 'TBR',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);
						
	$PDF->linea[2] = array(
			'posx' => $L0[2],
			'ancho' => $W0[2],
			'texto' => 'SALDO AL FINAL:',
			'borde' => 'LTB',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);
	
	$PDF->linea[3] = array(
			'posx' => $L0[3],
			'ancho' => $W0[3],
			'texto' => number_format($saldos['BancoCuentaSaldo']['saldo_conciliacion'],2),
			'borde' => 'TBR',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 10
	);
						
	$PDF->Imprimir_linea();
				
	
	$PDF->Ln(0.5);

	foreach($PDF->encabezado as $encabezado){
    			
		$PDF->linea = $encabezado;
		$PDF->imprimir_linea();
    			
	}
	$PDF->Ln(0.5);
	$PDF->Reset();	

	$saldo = $cuenta['BancoCuenta']['importe_conciliacion'];
	if($cuenta['BancoCuenta']['tipo_conciliacion'] == 1) $saldo *= -1;
	
	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => $W1[0],
				'texto' => 'SALDO ANTERIOR',
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
	);
	
	$PDF->linea[1] = array(
				'posx' => $L1[6],
				'ancho' => $W1[6],
				'texto' => ($cuenta['BancoCuenta']['tipo_conciliacion'] == 0 ? number_format($cuenta['BancoCuenta']['importe_conciliacion'],2) : ''),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
	);
	
	$PDF->linea[2] = array(
				'posx' => $L1[7],
				'ancho' => $W1[7],
				'texto' => ($cuenta['BancoCuenta']['tipo_conciliacion'] == 1 ? number_format($cuenta['BancoCuenta']['importe_conciliacion'],2) : ''),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
	);
	
	$PDF->linea[3] = array(
				'posx' => $L1[8],
				'ancho' => $W1[8],
				'texto' => number_format($saldo,2),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
	);
	
	$PDF->Imprimir_linea();
		
	foreach ($movimientos as $renglon):
		if($renglon['BancoCuentaMovimiento']['anulado'] == 0):
			$saldo += $renglon['BancoCuentaMovimiento']['debe'] - $renglon['BancoCuentaMovimiento']['haber'];
		endif; 
	
		$descripcion = ''; // $renglon['BancoCuentaMovimiento']['descripcion'];
		if($renglon['BancoCuentaMovimiento']['reemplazar'] == 1):
			if($renglon['BancoCuentaMovimiento']['reemplazado'][0]['BancoCuentaMovimiento']['tipo'] == 7):
				$descripcion = ' - REEM. ' . $renglon['BancoCuentaMovimiento']['reemplazado'][0]['BancoCuentaMovimiento']['banco_cuenta'] . ' (' . $renglon['BancoCuentaMovimiento']['reemplazado'][0]['BancoCuentaMovimiento']['concepto'] . ')';
			else:
				$descripcion = ' - REEM. ' . $renglon['BancoCuentaMovimiento']['reemplazado'][0]['BancoCuentaMovimiento']['banco_str'] . '-' . $renglon['BancoCuentaMovimiento']['reemplazado'][0]['BancoCuentaMovimiento']['cuenta_str'] . '- CH.NRO. ' . $renglon['BancoCuentaMovimiento']['reemplazado'][0]['BancoCuentaMovimiento']['numero_operacion'];
			endif;
		endif;
		
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => $W1[0],
					'texto' => $util->armaFecha($renglon['BancoCuentaMovimiento']['fecha_operacion']),
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		
		$PDF->linea[1] = array(
					'posx' => $L1[1],
					'ancho' => $W1[1],
					'texto' => $util->armaFecha($renglon['BancoCuentaMovimiento']['fecha_vencimiento']),
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		
		$PDF->linea[2] = array(
					'posx' => $L1[2],
					'ancho' => $W1[2],
					'texto' => $renglon['BancoCuentaMovimiento']['concepto'] . ($renglon['BancoCuentaMovimiento']['anulado'] == 1 ? ' (ANULADO)' : ''),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		
		$PDF->linea[3] = array(
					'posx' => $L1[3],
					'ancho' => $W1[3],
					'texto' => $renglon['BancoCuentaMovimiento']['numero_operacion'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		
		$PDF->linea[4] = array(
					'posx' => $L1[4],
					'ancho' => $W1[4],
					'texto' => substr(trim($renglon['BancoCuentaMovimiento']['destinatario']),0,43),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		
		$PDF->linea[5] = array(
					'posx' => $L1[5],
					'ancho' => $W1[5],
					'texto' => substr(trim($renglon['BancoCuentaMovimiento']['descripcion']) . trim($descripcion),0,40),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		
		$PDF->linea[6] = array(
					'posx' => $L1[6],
					'ancho' => $W1[6],
					'texto' => ($renglon['BancoCuentaMovimiento']['debe'] == 0  ? '' : number_format($renglon['BancoCuentaMovimiento']['debe'],2)),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		
		$PDF->linea[7] = array(
					'posx' => $L1[7],
					'ancho' => $W1[7],
					'texto' => ($renglon['BancoCuentaMovimiento']['haber'] == 0 ? '' : number_format($renglon['BancoCuentaMovimiento']['haber'],2)),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		
		$PDF->linea[8] = array(
					'posx' => $L1[8],
					'ancho' => $W1[8],
					'texto' => number_format($saldo,2),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
		);
		
		$PDF->Imprimir_linea();
		
		
		
	endforeach;	
	
	$PDF->Cell(0,1,'', 'T');
	$PDF->linea[0] = array(
				'posx' => $L1[6],
				'ancho' => $W1[6],
				'texto' => 'SALDO AL FINAL',
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
	);
	
	$PDF->linea[2] = array(
				'posx' => $L1[8],
				'ancho' => $W1[8],
				'texto' => number_format($saldo,2),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
	);
	
	$PDF->Imprimir_linea();
		
	$PDF->Ln();	
		
		
		
	$PDF->Output("conciliacion_banco.pdf");
	exit;
?>


