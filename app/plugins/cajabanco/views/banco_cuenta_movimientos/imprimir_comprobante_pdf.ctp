<?php 
App::import('Vendor','comprobante_caja');

$PDF = new ComprobanteCaja();

$PDF->SetFontSizeConf(8);

$PDF->responsable = $movimiento[0]['user_created'];
$PDF->Open();
$PDF->textoHeader = 'FECHA: ' . $util->armaFecha($movimiento[0]['fecha_operacion']);

#TITULO DEL REPORTE#
$nroComprobante = str_pad($movimiento[0]['id'], 12, 0, STR_PAD_LEFT);
$nroComprobante = substr($nroComprobante,0,4) . '-' . substr($nroComprobante,-8);

$PDF->titulo['titulo3'] = 'COMPROB. NRO.: ' . $nroComprobante;

	$destinatario = $movimiento[0]['destinatario'];
	if($movimiento[0]['tipo'] == 1):
		$PDF->titulo['titulo2'] = 'EMISION CHEQUE';
		if($movimiento[0]['anulado'] == 1) $PDF->titulo['titulo2'] .= ' ANULADO';
	endif;
	
	if($movimiento[0]['tipo'] == 2):
		$PDF->titulo['titulo2'] = 'DEPOSITO BANCARIO';
	endif;
	
	if($movimiento[0]['tipo'] == 3):
		$PDF->titulo['titulo2'] = 'EXTRACCION DE FONDO';
		$destinatario = (empty($movimiento[0]['destinatario']) ? strtoupper(Configure::read('APLICACION.nombre_fantasia')) : $destinatario);
	endif;
	
	if($movimiento[0]['tipo'] == 4):
		$PDF->titulo['titulo2'] = 'TRASPASO DE FONDO';
	endif;
	
	if($movimiento[0]['tipo'] == 5):
		$PDF->titulo['titulo2'] = 'TRANSFERENCIA BANCARIA';
	endif;
	
	if($movimiento[0]['tipo'] == 6):
		$PDF->titulo['titulo2'] = 'MOVIMIENTO DE BANCO';
	endif;

	
	if($movimiento[0]['tipo'] == 7):
		$PDF->titulo['titulo2'] = 'MOVIMIENTO DE CAJA';
	endif;
	// $PDF->titulo['titulo3'] = 'FECHA: ';

	$PDF->bMargen = 0;
	
	$fontSize = 7;
	$fontSize_titulo = 7;
	
	$PDF->AddPage();
	$PDF->reset();

	$PDF->linea[0] = array(
				'posx' => 5,
				'ancho' => 138,
				'texto' => 'Destinatario: ' . $destinatario,
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
				'texto' => 'Descripcion: ' . $movimiento[0]['descripcion'],
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
				'texto' => 'Cuenta: ' . $movimiento[0]['banco_cuenta'],
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
				'texto' => 'Concepto: ' . $movimiento[0]['concepto'],
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

	
	$PDF->Ln();
	$PDF->linea[0] = array(
				'posx' => 5,
				'ancho' => 138,
				'texto' => $movimiento[0]['importe_letra'],
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

	if($movimiento[0]['tipo'] == 1):
//		$this->emision_cheque($movimiento);
		$PDF->linea[0] = array(
					'posx' => 5,
					'ancho' => 46,
					'texto' => 'NUMERO CHEQUE',
					'borde' => 'LTB',
					'align' => 'L',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#D8DBD4',
					'size' => $fontSize_titulo,
					'family' => 'helvetica'
			);
		$PDF->linea[1] = array(
					'posx' => 51,
					'ancho' => 46,
					'texto' => 'VENCIMIENTO',
					'borde' => 'TB',
					'align' => 'C',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#D8DBD4',
					'size' => $fontSize_titulo,
					'family' => 'helvetica'
			);
		$PDF->linea[2] = array(
					'posx' => 97,
					'ancho' => 46,
					'texto' => 'IMPORTE',
					'borde' => 'TRB',
					'align' => 'R',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#D8DBD4',
					'size' => $fontSize_titulo,
					'family' => 'helvetica'
			);
		$PDF->Imprimir_linea();
		$PDF->Ln();
		
		$PDF->linea[0] = array(
					'posx' => 5,
					'ancho' => 46,
					'texto' => $movimiento[0]['numero_operacion'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '',
					'size' => $fontSize_titulo,
					'family' => 'helvetica'
			);
		$PDF->linea[1] = array(
					'posx' => 51,
					'ancho' => 46,
					'texto' => $util->armaFecha($movimiento[0]['fecha_vencimiento']),
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '',
					'size' => $fontSize_titulo,
					'family' => 'helvetica'
			);
		$PDF->linea[2] = array(
					'posx' => 97,
					'ancho' => 46,
					'texto' => $movimiento[0]['importe'],
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '',
					'size' => $fontSize_titulo,
					'family' => 'helvetica'
			);
		$PDF->Imprimir_linea();
		$PDF->Ln();
	
	endif;
	
	
	if($movimiento[0]['tipo'] == 2):
//		$this->deposito_bancario($movimiento);
		if(isset($movimiento[0]['cheque_tercero'])):
			$PDF->linea[0] = array(
						'posx' => 5,
						'ancho' => 138,
						'texto' => 'NUMERO OPERACION: ' . $movimiento[0]['numero_operacion'],
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
						'ancho' => 15,
						'texto' => 'VENCIM.',
						'borde' => 'LTB',
						'align' => 'L',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#D8DBD4',
						'size' => 6,
						'family' => 'helvetica'
				);
			$PDF->linea[1] = array(
						'posx' => 20,
						'ancho' => 15,
						'texto' => 'CHEQUE NRO.',
						'borde' => 'TB',
						'align' => 'L',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#D8DBD4',
						'size' => 6,
						'family' => 'helvetica'
				);
			$PDF->linea[2] = array(
						'posx' => 35,
						'ancho' => 45,
						'texto' => 'LIBRADOR',
						'borde' => 'TB',
						'align' => 'C',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#D8DBD4',
						'size' => 6,
						'family' => 'helvetica'
				);
			$PDF->linea[3] = array(
						'posx' => 80,
						'ancho' => 45,
						'texto' => 'BANCO',
						'borde' => 'TB',
						'align' => 'C',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#D8DBD4',
						'size' => 6,
						'family' => 'helvetica'
				);
			
			$PDF->linea[4] = array(
						'posx' => 125,
						'ancho' => 20,
						'texto' => 'IMPORTE',
						'borde' => 'TRB',
						'align' => 'R',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#D8DBD4',
						'size' => 6,
						'family' => 'helvetica'
				);
			
			$PDF->Imprimir_linea();
//			$PDF->Ln();
			
			foreach($movimiento[0]['cheque_tercero'] as $cheque):
				$PDF->linea[0] = array(
							'posx' => 5,
							'ancho' => 15,
							'texto' => $util->armaFecha($cheque['BancoChequeTercero']['fecha_vencimiento']),
							'borde' => '',
							'align' => 'C',
							'fondo' => 0,
							'style' => '',
							'colorf' => '',
							'size' => 6,
							'family' => 'helvetica'
					);
				$PDF->linea[1] = array(
							'posx' => 20,
							'ancho' => 15,
							'texto' => $cheque['BancoChequeTercero']['numero_cheque'],
							'borde' => '',
							'align' => 'L',
							'fondo' => 0,
							'style' => '',
							'colorf' => '',
							'size' => 6,
							'family' => 'helvetica'
					);
				$PDF->linea[2] = array(
							'posx' => 35,
							'ancho' => 45,
							'texto' => $cheque['BancoChequeTercero']['librador'],
							'borde' => '',
							'align' => 'L',
							'fondo' => 0,
							'style' => '',
							'colorf' => '',
							'size' => 6,
							'family' => 'helvetica'
					);
				$PDF->linea[3] = array(
							'posx' => 80,
							'ancho' => 45,
							'texto' => $cheque['BancoChequeTercero']['banco'],
							'borde' => '',
							'align' => 'L',
							'fondo' => 0,
							'style' => '',
							'colorf' => '',
							'size' => 6,
							'family' => 'helvetica'
					);
				
				$PDF->linea[4] = array(
							'posx' => 125,
							'ancho' => 20,
							'texto' => $cheque['BancoChequeTercero']['importe'],
							'borde' => '',
							'align' => 'R',
							'fondo' => 0,
							'style' => '',
							'colorf' => '',
							'size' => 6,
							'family' => 'helvetica'
					);
				
				$PDF->Imprimir_linea();
//				$PDF->Ln();
					
			endforeach;
		else:
			$PDF->linea[0] = array(
						'posx' => 5,
						'ancho' => 46,
						'texto' => 'NRO. OPERACION',
						'borde' => 'LTB',
						'align' => 'C',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#D8DBD4',
						'size' => $fontSize_titulo,
						'family' => 'helvetica'
				);
			$PDF->linea[1] = array(
						'posx' => 51,
						'ancho' => 46,
						'texto' => 'D E S C R I P C I O N',
						'borde' => 'TB',
						'align' => 'C',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#D8DBD4',
						'size' => $fontSize_titulo,
						'family' => 'helvetica'
				);
			$PDF->linea[2] = array(
						'posx' => 97,
						'ancho' => 46,
						'texto' => 'IMPORTE',
						'borde' => 'TRB',
						'align' => 'R',
						'fondo' => 1,
						'style' => 'B',
						'colorf' => '#D8DBD4',
						'size' => $fontSize_titulo,
						'family' => 'helvetica'
				);
			
			$PDF->Imprimir_linea();
			$PDF->Ln();
			
			$PDF->linea[0] = array(
							'posx' => 5,
							'ancho' => 46,
							'texto' => $movimiento[0]['numero_operacion'],
							'borde' => 'TRB',
							'align' => 'C',
							'fondo' => 1,
							'style' => 'B',
							'colorf' => '#D8DBD4',
							'size' => $fontSize_titulo,
							'family' => 'helvetica'
					);
			$PDF->linea[1] = array(
							'posx' => 51,
							'ancho' => 46,
							'texto' => $movimiento[0]['banco_cuenta'],
							'borde' => 'TRB',
							'align' => 'C',
							'fondo' => 1,
							'style' => 'B',
							'colorf' => '#D8DBD4',
							'size' => $fontSize_titulo,
							'family' => 'helvetica'
					);
				
			$PDF->linea[2] = array(
							'posx' => 97,
							'ancho' => 46,
							'texto' => $movimiento[0]['importe'],
							'borde' => 'TRB',
							'align' => 'R',
							'fondo' => 1,
							'style' => 'B',
							'colorf' => '#D8DBD4',
							'size' => $fontSize_titulo,
							'family' => 'helvetica'
					);
				
			$PDF->Imprimir_linea();
			$PDF->Ln();
		endif;
	endif;
	
	if($movimiento[0]['tipo'] == 3 || $movimiento[0]['tipo'] == 4 || $movimiento[0]['tipo'] == 5 || $movimiento[0]['tipo'] == 6):
//		$this->extraccion_fondo($movimiento);
		$PDF->linea[0] = array(
					'posx' => 5,
					'ancho' => 46,
					'texto' => 'NRO.OPER.',
					'borde' => 'LTB',
					'align' => 'L',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#D8DBD4',
					'size' => $fontSize_titulo,
					'family' => 'helvetica'
			);
		$PDF->linea[1] = array(
					'posx' => 51,
					'ancho' => 46,
					'texto' => 'CUENTA BANCO',
					'borde' => 'TB',
					'align' => 'C',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#D8DBD4',
					'size' => $fontSize_titulo,
					'family' => 'helvetica'
			);
		$PDF->linea[2] = array(
					'posx' => 97,
					'ancho' => 46,
					'texto' => 'IMPORTE',
					'borde' => 'TRB',
					'align' => 'R',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#D8DBD4',
					'size' => $fontSize_titulo,
					'family' => 'helvetica'
			);
		$PDF->Imprimir_linea();
		$PDF->Ln();
		
		$PDF->linea[0] = array(
					'posx' => 5,
					'ancho' => 40,
					'texto' => $movimiento[0]['numero_operacion'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '',
					'size' => $fontSize_titulo,
					'family' => 'helvetica'
			);
		$PDF->linea[1] = array(
					'posx' => 55,
					'ancho' => 46,
					'texto' => $movimiento[0]['banco_cuenta'],
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '',
					'size' => 6,
					'family' => 'helvetica'
			);
		$PDF->linea[2] = array(
					'posx' => 102,
					'ancho' => 41,
					'texto' => $movimiento[0]['importe'],
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '',
					'size' => $fontSize_titulo,
					'family' => 'helvetica'
			);
		$PDF->Imprimir_linea();
		$PDF->Ln();
	endif;
	
	
	if($movimiento[0]['tipo'] == 7):
//		$this->caja($movimiento);
		$PDF->linea[0] = array(
					'posx' => 5,
					'ancho' => 92,
					'texto' => 'D E S C R I P C I O N',
					'borde' => 'TBL',
					'align' => 'L',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#D8DBD4',
					'size' => 7.5,
					'family' => 'helvetica'
			);
		$PDF->linea[1] = array(
					'posx' => 97,
					'ancho' => 46,
					'texto' => 'IMPORTE',
					'borde' => 'TRB',
					'align' => 'R',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#D8DBD4',
					'size' => 7.5,
					'family' => 'helvetica'
			);
		$PDF->Imprimir_linea();
		$PDF->Ln();
		
		$PDF->linea[1] = array(
					'posx' => 5,
					'ancho' => 60,
					'texto' => $movimiento[0]['banco_cuenta'] . ' EFECTIVO',
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '',
					'size' => 7.5,
					'family' => 'helvetica'
			);
		$PDF->linea[2] = array(
					'posx' => 97,
					'ancho' => 46,
					'texto' => $movimiento[0]['importe'],
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '',
					'size' => $fontSize_titulo,
					'family' => 'helvetica'
			);
		$PDF->Imprimir_linea();
		$PDF->Ln();
	endif;
	
	
	
	
	$PDF->Ln(10);
	// Total
	$PDF->linea[0] = array(
				'posx' => 93,
				'ancho' => 25,
				'texto' => 'IMPORTE',
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
				'texto' => '$ ' . $movimiento[0]['importe'],
				'borde' => 'LTRB',
				'align' => 'R',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#D8DBD4',
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
	$PDF->Output("ComprobanteNro" . str_pad($movimiento[0]['id'],8,0,STR_PAD_LEFT) . ".pdf");

	return true;

	function emision_cheque($movimiento){
		$PDF->linea[0] = array(
					'posx' => 5,
					'ancho' => 46,
					'texto' => 'NUMERO CHEQUE',
					'borde' => 'LTB',
					'align' => 'L',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#D8DBD4',
					'size' => $fontSize_titulo,
					'family' => 'helvetica'
			);
		$PDF->linea[1] = array(
					'posx' => 51,
					'ancho' => 46,
					'texto' => 'VENCIMIENTO',
					'borde' => 'TB',
					'align' => 'C',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#D8DBD4',
					'size' => $fontSize_titulo,
					'family' => 'helvetica'
			);
		$PDF->linea[2] = array(
					'posx' => 97,
					'ancho' => 46,
					'texto' => 'IMPORTE',
					'borde' => 'TRB',
					'align' => 'R',
					'fondo' => 1,
					'style' => 'B',
					'colorf' => '#D8DBD4',
					'size' => $fontSize_titulo,
					'family' => 'helvetica'
			);
		$PDF->Imprimir_linea();
		$PDF->Ln();
		
		$PDF->linea[0] = array(
					'posx' => 5,
					'ancho' => 46,
					'texto' => $movimiento[0]['numero_operacion'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '',
					'size' => $fontSize_titulo,
					'family' => 'helvetica'
			);
		$PDF->linea[1] = array(
					'posx' => 51,
					'ancho' => 46,
					'texto' => $util->armaFecha($movimiento[0]['fecha_vencimiento']),
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '',
					'size' => $fontSize_titulo,
					'family' => 'helvetica'
			);
		$PDF->linea[2] = array(
					'posx' => 97,
					'ancho' => 46,
					'texto' => $movimiento[0]['importe'],
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '',
					'size' => $fontSize_titulo,
					'family' => 'helvetica'
			);
		$PDF->Imprimir_linea();
		$PDF->Ln();
	
		return true;		
	}    
	

?>