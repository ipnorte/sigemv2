<?php 

App::import('Vendor','orden_pago_a5');

$PDF = new OrdenPagoA5();

// $PDF->SetTitle("Orden de Pago");
$PDF->SetFontSizeConf(8);

$PDF->responsable = $aOrdenDePago['OrdenPago']['user_created'];
$PDF->Open();
$PDF->textoHeader = 'FECHA ORDEN: ' . $aOrdenDePago['OrdenPago']['fecha_pago'];

#TITULO DEL REPORTE#
$PDF->titulo['titulo3'] = 'ORDEN DE PAGO NRO.: ' . str_pad($aOrdenDePago['OrdenPago']['nro_orden_pago'],8,0,STR_PAD_LEFT);
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
				'texto' => 'Señor/es: ' . $aOrdenDePago['Proveedor']['razon_social'],
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
				'texto' => 'Domicilio: ' . $aOrdenDePago['Proveedor']['domicilio'],
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
				'ancho' => 138,
				'texto' => 'I.V.A.: ' . $aOrdenDePago['Proveedor']['iva_concepto'],
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
				'ancho' => 69,
				'texto' => 'C.U.I.T.: ' . $aOrdenDePago['Proveedor']['formato_cuit'],
				'borde' => 'LB',
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
				'texto' => 'Ingresos Brutos: ' . $aOrdenDePago['Proveedor']['nro_ingresos_brutos'],
				'borde' => 'BR',
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
				'ancho' => 138,
				'texto' => 'L    I    Q    U    I    D    A    C    I    O    N',
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
	foreach($aOrdenDePago['detalle'] as $liquidacion){
		$nContadorLiquidacion += 1;
		$PDF->linea[0] = array(
					'posx' => 5,
					'ancho' => 113,
					'texto' => $liquidacion['tipo_comprobante_desc'],
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
	
	foreach($aOrdenDePago['forma'] as $forma){
//		$PDF->linea[0] = array(
//					'posx' => 5,
//					'ancho' => 113,
//					'texto' => $forma['concepto'] . ' ' . $forma['forma_pago_desc'],
//					'borde' => '',
//					'align' => 'L',
//					'fondo' => 0,
//					'style' => '',
//					'colorf' => '',
//					'size' => $fontSize,
//					'family' => 'helvetica'
//			);
//	
//		$PDF->linea[1] = array(
//					'posx' => 118,
//					'ancho' => 25,
//					'texto' => $forma['importe'],
//					'borde' => '',
//					'align' => 'R',
//					'fondo' => 0,
//					'style' => '',
//					'colorf' => '',
//					'size' => $fontSize,
//					'family' => 'helvetica'
//			);
//		
//		$PDF->Imprimir_linea();
			
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
						'texto' => $forma['concepto'] . ' ' . $forma['forma_pago_desc'],
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
						'texto' => $forma['forma_pago_desc'],
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
				'texto' => 'IMPORTE DEL PAGO',
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
				'texto' => '$ ' . $aOrdenDePago['OrdenPago']['importe'],
				'borde' => 'LTRB',
				'align' => 'R',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $fontSize,
				'family' => 'helvetica'
		);
		
	$PDF->Imprimir_linea();
	$PDF->Ln();
	$PDF->linea[0] = array(
				'posx' => 5,
				'ancho' => 24,
				'texto' => $aOrdenDePago['OrdenPago']['importe_letra'],
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '',
				'size' => $fontSize,
				'family' => 'helvetica'
		);
	$PDF->Imprimir_linea();
		
	$PDF->Ln(20);
	$PDF->linea[0] = array(
				'posx' => 17,
				'ancho' => 40,
				'texto' => 'R E C I B I O',
				'borde' => 'T',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '',
				'size' => $fontSize,
				'family' => 'helvetica'
		);
	
	$PDF->linea[1] = array(
				'posx' => 91,
				'ancho' => 40,
				'texto' => 'A U T O R I Z O',
				'borde' => 'T',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '',
				'size' => $fontSize,
				'family' => 'helvetica'
		);
	$PDF->Imprimir_linea();
		
	
	$PDF->Ln();
	$PDF->Output("OrdenPagoNro" . str_pad($aOrdenDePago['OrdenPago']['nro_orden_pago'],8,0,STR_PAD_LEFT) . ".pdf");

?>