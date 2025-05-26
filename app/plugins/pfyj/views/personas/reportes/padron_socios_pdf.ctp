<?php 

//debug($datos);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF("L");

$PDF->SetTitle("PADRON DE PERSONAS / SOCIOS");
$PDF->SetFontSizeConf(8.5);


$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo3'] =  $opciones[$asinc['Asincrono']['p1']];
$PDF->titulo['titulo2'] =  ($asinc['Asincrono']['p2'] == 1 ? "INCLUYE DEUDA - PERIODO CORTE: " . $util->periodo($asinc['Asincrono']['p3'],true,"/") : "");
$PDF->titulo['titulo1'] = "PADRON DE PERSONAS / SOCIOS";

// TDOCNDOC | APENOM | DOMICILIO | NRO SOCIO | FECHA ALTA | ULTIMA CALIFICACION | BAJA | CAUSA BAJA | CUOTA SOCIAL
// 277

$W1 = array(20,45,65,15,17,15,20,15,27,20,18);
$L1 = $PDF->armaAnchoColumnas($W1);

$fontSizeHeader = 7;

$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => 'DOCUMENTO',
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
			'texto' => 'APELLIDO Y NOMBRE',
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
			'texto' => 'DOMICILIO',
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
			'texto' => 'SOCIO',
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
			'texto' => 'CATEGORIA',
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
			'texto' => 'ALTA',
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
			'texto' => 'CALIFICACION',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][7] = array(
			'posx' => $L1[7],
			'ancho' => $W1[7],
			'texto' => 'BAJA',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	
$PDF->encabezado[0][8] = array(
			'posx' => $L1[8],
			'ancho' => $W1[8],
			'texto' => 'MOTIVO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][9] = array(
			'posx' => $L1[9],
			'ancho' => $W1[9],
			'texto' => 'CUOTA SOCIAL',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->encabezado[0][10] = array(
			'posx' => $L1[10],
			'ancho' => $W1[10],
			'texto' => 'OTRA DEUDA',
			'borde' => 'TBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);					
$PDF->AddPage();
$PDF->Reset();

if(!empty($datos)):

	$fontSizeBody = 7;

	$ACUM_CSOCIAL = 0;
	$CANTIDAD_SOCIOS = 0;
	$ACUM_DEUDA = 0;
	
	foreach($datos as $dato):

		$CANTIDAD_SOCIOS++;
		$ACUM_CSOCIAL += $dato['decimal_2'];
		$ACUM_DEUDA += $dato['decimal_1'];
		
	
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => $W1[0],
					'texto' => $dato['texto_1'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[1] = array(
					'posx' => $L1[1],
					'ancho' => $W1[1],
					'texto' => substr($dato['texto_2'],0,30),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[2] = array(
					'posx' => $L1[2],
					'ancho' => $W1[2],
					'texto' => substr($dato['texto_3'],0,50),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody - 1
			);	
		$PDF->linea[3] = array(
					'posx' => $L1[3],
					'ancho' => $W1[3],
					'texto' => $dato['texto_4'],
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
					'texto' => $dato['texto_10'],
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
					'texto' => $dato['texto_7'],
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[6] = array(
					'posx' => $L1[6],
					'ancho' => $W1[6],
					'texto' => substr($dato['texto_5'],0,17),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody - 1
			);
		$PDF->linea[7] = array(
					'posx' => $L1[7],
					'ancho' => $W1[7],
					'texto' => $dato['texto_8'],
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[8] = array(
					'posx' => $L1[8],
					'ancho' => $W1[8],
					'texto' => substr($dato['texto_9'],0,20),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody
			);
		$PDF->linea[9] = array(
					'posx' => $L1[9],
					'ancho' => $W1[9],
					'texto' => $util->nf($dato['decimal_2'])." (".$dato['entero_1'].")",
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody - 1
			);
		$PDF->linea[10] = array(
					'posx' => $L1[10],
					'ancho' => $W1[10],
					'texto' => $util->nf($dato['decimal_1']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSizeBody - 1
			);																			
		$PDF->Imprimir_linea();	
			
	endforeach;
	// 20 + 18
	// 277 - 38 = 239
	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => 239,
				'texto' => $opciones[$asinc['Asincrono']['p1']] . ": " . $CANTIDAD_SOCIOS,
				'borde' => 'T',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => 9
		);	
	$PDF->linea[1] = array(
				'posx' => $L1[0] + (239),
				'ancho' => 20,
				'texto' => $util->nf($ACUM_CSOCIAL),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody - 1
		);
	$PDF->linea[2] = array(
				'posx' => $L1[0] + (239 + 20),
				'ancho' => 18,
				'texto' => $util->nf($ACUM_DEUDA),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $fontSizeBody - 1
		);				
	$PDF->Imprimir_linea();	
	

endif;

$PDF->Output("padron_socios.pdf");
exit;


?>