<?php 
//debug($aRecibo);

App::import('Vendor','recibo_a5');

$PDF = new ReciboA5();

// $PDF->SetTitle("Orden de Pago");
$PDF->SetFontSizeConf(8);

$PDF->responsable = $aRecibo['Recibo']['user_created'];
$PDF->Open();
$PDF->textoHeader = 'FECHA: ' . $util->armaFecha($aRecibo['Recibo']['fecha_comprobante']);

#TITULO DEL REPORTE#
$PDF->titulo['titulo3'] = 'RECIBO NRO.: ' . $aRecibo['Recibo']['letra'] . '-' . $aRecibo['Recibo']['sucursal'] . '-' . str_pad($aRecibo['Recibo']['nro_recibo'],8,0,STR_PAD_LEFT);
$PDF->titulo['titulo2'] = '';
// $PDF->titulo['titulo3'] = 'FECHA: ';

$PDF->bMargen = 0;

$fontSize = 7;
$fontSize_titulo = 7;

$PDF->AddPage();
$PDF->reset();

	$PDF->linea[0] = array(
				'posx' => 5,
				'ancho' => 138,
				'texto' => 'Señor/es: ' . $aRecibo['Recibo']['razon_social'],
				'borde' => 'LTR',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '',
				'size' => $fontSize_titulo,
				'family' => 'helvetica'
		);
	$PDF->Imprimir_linea();
	

	$PDF->linea[0] = array(
				'posx' => 5,
				'ancho' => 138,
				'texto' => 'Domicilio: ' . $aRecibo['Recibo']['domicilio'],
				'borde' => 'LBR',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '',
				'size' => $fontSize_titulo,
				'family' => 'helvetica'
		);
	$PDF->Imprimir_linea();
	$PDF->Ln();	

	$PDF->linea[0] = array(
				'posx' => 5,
				'ancho' => 69,
				'texto' => 'I.V.A.: ' . $aRecibo['Recibo']['iva_concepto'],
				'borde' => 'LTB',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '',
				'size' => $fontSize_titulo,
				'family' => 'helvetica'
		);
	
	$PDF->linea[1] = array(
				'posx' => 74,
				'ancho' => 69,
				'texto' => $aRecibo['Recibo']['cuit'],
				'borde' => 'TBR',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '',
				'size' => $fontSize_titulo,
				'family' => 'helvetica'
		);

//	$PDF->linea[1] = array(
//				'posx' => 74,
//				'ancho' => 69,
//				'texto' => 'Ingresos Brutos: ' . $aRecibo['Recibo']['nro_ingresos_brutos'],
//				'borde' => 'BR',
//				'align' => 'L',
//				'fondo' => 0,
//				'style' => 'B',
//				'colorf' => '',
//				'size' => $fontSize_titulo,
//				'family' => 'helvetica'
//		);
	
	$PDF->Imprimir_linea();
	
	$PDF->Ln();
	$PDF->linea[0] = array(
				'posx' => 5,
				'ancho' => 24,
				'texto' => $aRecibo['Recibo']['importe_letra'],
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '',
				'size' => $fontSize,
				'family' => 'helvetica'
		);
	$PDF->Imprimir_linea();
	$PDF->Ln();

	$PDF->linea[0] = array(
				'posx' => 5,
				'ancho' => 138,
				'texto' => 'C  O  N  C  E  P  T  O  S',
				'borde' => 'LTRB',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => $fontSize_titulo,
				'family' => 'helvetica'
		);
	$PDF->Imprimir_linea();
	$PDF->Ln();
	
	$nContadorLiquidacion = 0;
	foreach($aRecibo['Recibo']['detalle'] as $liquidacion){
		$nContadorLiquidacion += 1;
		$PDF->linea[0] = array(
					'posx' => 5,
					'ancho' => 113,
					'texto' => $liquidacion['concepto'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '',
					'size' => $fontSize,
					'family' => 'helvetica'
			);
	
		$PDF->linea[1] = array(
					'posx' => 118,
					'ancho' => 25,
					'texto' => $liquidacion['importe'],
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '',
					'size' => $fontSize,
					'family' => 'helvetica'
			);
		
		$PDF->Imprimir_linea();
	}
	
	for($linea = $nContadorLiquidacion; $linea < 16; $linea++):
		$PDF->Ln();
	endfor;

	$PDF->linea[0] = array(
				'posx' => 5,
				'ancho' => 138,
				'texto' => 'V    A    L    O    R    E    S',
				'borde' => 'LTRB',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => $fontSize_titulo,
				'family' => 'helvetica'
		);
	$PDF->Imprimir_linea();
	$PDF->Ln();
	
	foreach($aRecibo['Recibo']['forma'] as $forma){
		if($forma['forma_cobro'] != 'EF'):
			$PDF->linea[0] = array(
						'posx' => 5,
						'ancho' => 113,
						'texto' => $forma['concepto'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '',
						'size' => $fontSize,
						'family' => 'helvetica'
				);
		else:
			$PDF->linea[0] = array(
						'posx' => 5,
						'ancho' => 113,
						'texto' => $forma['concepto'] . ' ' . $forma['descripcion_cobro'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '',
						'size' => $fontSize,
						'family' => 'helvetica'
				);
		endif;	
		$PDF->linea[1] = array(
					'posx' => 118,
					'ancho' => 25,
					'texto' => $forma['importe'],
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '',
					'size' => $fontSize,
					'family' => 'helvetica'
			);
		
		$PDF->Imprimir_linea();
		if($forma['forma_cobro'] != 'EF'):
			$PDF->linea[0] = array(
						'posx' => 5,
						'ancho' => 113,
						'texto' => $forma['descripcion_cobro'] . ' - ' . $forma['numero_operacion'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '',
						'size' => $fontSize,
						'family' => 'helvetica'
				);
			$PDF->Imprimir_linea();
		endif;	
	}
		
	
	
	$PDF->Ln(10);
	// Total de la Orden de Pago
	$PDF->linea[0] = array(
				'posx' => 93,
				'ancho' => 25,
				'texto' => 'IMPORTE RECIBIDO',
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '',
				'size' => $fontSize,
				'family' => 'helvetica'
		);
	
	$PDF->linea[1] = array(
				'posx' => 118,
				'ancho' => 25,
				'texto' => '$ ' . $aRecibo['Recibo']['importe'],
				'borde' => 'LTRB',
				'align' => 'R',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $fontSize,
				'family' => 'helvetica'
		);
		
	$PDF->Imprimir_linea();
//	$PDF->Ln(20);
//	$PDF->linea[0] = array(
//				'posx' => 17,
//				'ancho' => 40,
//				'texto' => 'R E C I B I O',
//				'borde' => 'T',
//				'align' => 'C',
//				'fondo' => 0,
//				'style' => '',
//				'colorf' => '',
//				'size' => $fontSize,
//				'family' => 'helvetica'
//		);
//	
//	$PDF->linea[1] = array(
//				'posx' => 91,
//				'ancho' => 40,
//				'texto' => 'A U T O R I Z O',
//				'borde' => 'T',
//				'align' => 'C',
//				'fondo' => 0,
//				'style' => '',
//				'colorf' => '',
//				'size' => $fontSize,
//				'family' => 'helvetica'
//		);
//	$PDF->Imprimir_linea();
		
	
	$PDF->Ln();
	$PDF->Output("ReciboNro" . str_pad($aRecibo['Recibo']['id'],8,0,STR_PAD_LEFT) . ".pdf");

?>