<?php 

// debug($proveedores);
// debug($ctacte);
// exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF('L');

$PDF->SetTitle("CUENTA CORRIENTE");
$PDF->SetFontSizeConf(11);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo3'] = 'CUENTA CORRIENTE';
$PDF->titulo['titulo2'] = $proveedores['Proveedor']['razon_social'];
// $PDF->titulo['titulo3'] = 'FECHA: ';

$PDF->bMargen = 10;

$fontSize = 8;
$fontSize_titulo = 10;

$PDF->AddPage();
$PDF->reset();

	// $PDF->linea[0] = array(
	// 			'posx' => 10,
	// 			'ancho' => 277,
	// 			'texto' => 'Se�or/es: ' . $proveedores['Proveedor']['razon_social'],
	// 			'borde' => 'LTR',
	// 			'align' => 'L',
	// 			'fondo' => 0,
	// 			'style' => 'B',
	// 			'colorf' => '',
	// 			'size' => $fontSize_titulo,
	// 			'family' => 'helvetica'
	// 	);
	// $PDF->Imprimir_linea();
	

	// $PDF->linea[0] = array(
	// 			'posx' => 10,
	// 			'ancho' => 277,
	// 			'texto' => 'Domicilio: ' . $proveedores['Proveedor']['domicilio'],
	// 			'borde' => 'LBR',
	// 			'align' => 'L',
	// 			'fondo' => 0,
	// 			'style' => 'B',
	// 			'colorf' => '',
	// 			'size' => $fontSize_titulo,
	// 			'family' => 'helvetica'
	// 	);
	// $PDF->Imprimir_linea();
	// $PDF->Ln();
	

	// $PDF->linea[0] = array(
	// 			'posx' => 10,
	// 			'ancho' => 277,
	// 			'texto' => 'I.V.A.: ' . $proveedores['Proveedor']['iva_concepto'],
	// 			'borde' => 'LTR',
	// 			'align' => 'L',
	// 			'fondo' => 0,
	// 			'style' => 'B',
	// 			'colorf' => '',
	// 			'size' => $fontSize_titulo,
	// 			'family' => 'helvetica'
	// 	);
	// $PDF->Imprimir_linea();
	
	// $PDF->linea[0] = array(
	// 			'posx' => 10,
	// 			'ancho' => 95,
	// 			'texto' => 'C.U.I.T.: ' . $proveedores['Proveedor']['formato_cuit'],
	// 			'borde' => 'LB',
	// 			'align' => 'L',
	// 			'fondo' => 0,
	// 			'style' => 'B',
	// 			'colorf' => '',
	// 			'size' => $fontSize_titulo,
	// 			'family' => 'helvetica'
	// 	);

	// $PDF->linea[1] = array(
	// 			'posx' => 105,
	// 			'ancho' => 182,
	// 			'texto' => 'Ingresos Brutos: ' . $proveedores['Proveedor']['nro_ingresos_brutos'],
	// 			'borde' => 'BR',
	// 			'align' => 'L',
	// 			'fondo' => 0,
	// 			'style' => 'B',
	// 			'colorf' => '',
	// 			'size' => $fontSize_titulo,
	// 			'family' => 'helvetica'
	// 	);
	
	// $PDF->Imprimir_linea();
	// $PDF->Ln();

	$PDF->linea[0] = array(
				'posx' => 10,
				'ancho' => 20,
				'texto' => 'F E C H A',
				'borde' => 'LTRB',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => $fontSize_titulo,
				'family' => 'helvetica'
		);

	$PDF->linea[1] = array(
				'posx' => 30,
				'ancho' => 173,
				'texto' => 'C O N C E P T O',
				'borde' => 'LTRB',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => $fontSize_titulo,
				'family' => 'helvetica'
		);
		
	$PDF->linea[2] = array(
				'posx' => 203,
				'ancho' => 28,
				'texto' => 'D  E  B  E',
				'borde' => 'LTRB',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => $fontSize_titulo,
				'family' => 'helvetica'
		);
		
	$PDF->linea[3] = array(
				'posx' => 231,
				'ancho' => 28,
				'texto' => 'H  A  B  E  R',
				'borde' => 'LTRB',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => $fontSize_titulo,
				'family' => 'helvetica'
		);
		
	$PDF->linea[4] = array(
				'posx' => 259,
				'ancho' => 28,
				'texto' => 'S  A  L  D  O',
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
	
	// $fontSize -= 3;	

	foreach($ctacte as $renglon){
		$PDF->linea[0] = array(
					'posx' => 10,
					'ancho' => 20,
					'texto' => date('d/m/Y',strtotime($renglon['ProveedorCtacte']['fecha'])),
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '',
					'size' => $fontSize,
					'family' => 'helvetica'
			);
	
		$PDF->linea[1] = array(
					'posx' => 30,
					'ancho' => 173,
					'texto' => substr($renglon['ProveedorCtacte']['concepto']. " | " . $renglon['ProveedorCtacte']['comentario'],0,117),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '',
					'size' => $fontSize,
					'family' => 'helvetica'
			);
			
		$PDF->linea[2] = array(
					'posx' => 203,
					'ancho' => 28,
					'texto' => number_format($renglon['ProveedorCtacte']['debe'],2, ',','.'),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '',
					'size' => $fontSize,
					'family' => 'helvetica'
			);
			
		$PDF->linea[3] = array(
					'posx' => 231,
					'ancho' => 28,
					'texto' => number_format($renglon['ProveedorCtacte']['haber'],2, ',','.'),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '',
					'size' => $fontSize,
					'family' => 'helvetica'
			);
			
		$PDF->linea[4] = array(
					'posx' => 259,
					'ancho' => 28,
					'texto' => number_format($renglon['ProveedorCtacte']['saldo'],2, ',','.'),
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
	
	
	
	
	$PDF->Ln();
	$PDF->Output("CtaCte" . $proveedores['Proveedor']['id'] . ".pdf");

?>