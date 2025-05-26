<?php 
App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("Resumen de Liquidacion");
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo1'] = 'RESUMEN PERIODO ' . $util->periodo($periodo);
$PDF->titulo['titulo2'] = 'FECHA PROCESO: ' .$resumen['Liquidacion']['created'] .' - ESTADO: ' . ($resumen['Liquidacion']['cerrada'] == 1 ? 'CERRADA' : 'ABIERTA');
$PDF->titulo['titulo3'] = 'LIQUIDACION DE DEUDA - '.$util->globalDato($resumen['Liquidacion']['codigo_organismo']);

//ORGANISMO	PRODUCTO	CONCEPTO	IMPORTE LIQUIDADO	CANTIDAD DE CUOTAS PROCESADAS

$W1 = array(60,55,25,25,25);
$L1 = $PDF->armaAnchoColumnas($W1);

$PDF->bMargen = 10;

$fontSizeHeader = 7;

$PDF->encabezado = array();
$PDF->encabezado[0] = array();	

$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => 'PROVEEDOR / PRODUCTO',
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
			'texto' => 'TIPO CUOTA',
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
			'texto' => 'ATRASO',
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
			'texto' => 'PERIODO',
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
			'texto' => 'TOTAL',
			'borde' => 'TBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);	

$PDF->AddPage();
$PDF->reset();

imprimirGrilla($PDF,$L1,$W1,$deuda,'TOTALES');


$PDF->Output("resumen_liquidacion_deuda_$periodo.pdf");




















/**
 * imprime la grilla
 * @param unknown_type $PDF
 * @param unknown_type $L1
 * @param unknown_type $W1
 * @param unknown_type $datos
 * @param unknown_type $MSG
 */
function imprimirGrilla(&$PDF,$L1,$W1,$datos,$MSG){
	
	$PDF->reset();
	
	$fontSize = 9;
	$ACU_TOTAL = 0;
	$ACU_PERIODO = 0;
	$ACU_ATRASO = 0;
		
	foreach($datos as $liquidacion):
//		debug($liquidacion);
		
		$ACU_TOTAL += $liquidacion['Proveedor']['total'];
		$ACU_PERIODO += $liquidacion['Proveedor']['total_periodo'];
		$ACU_ATRASO += $liquidacion['Proveedor']['total_atraso'];		
		
		$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => $liquidacion['Proveedor']['razon_social'],
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => 9
		);
		$PDF->Imprimir_linea();
		$fontSize = 8;
		foreach($liquidacion['Proveedor']['liquidacion'] as $cuota):

			$PDF->linea[0] = array(
						'posx' => $L1[0],
						'ancho' => $W1[0],
						'texto' => substr($cuota['LiquidacionCuota']['tipo_producto_desc'],0,30),
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSize
				);
			$PDF->linea[1] = array(
						'posx' => $L1[1],
						'ancho' => $W1[1],
						'texto' => $cuota['LiquidacionCuota']['tipo_cuota_desc'],
						'borde' => '',
						'align' => 'L',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSize
				);
			$PDF->linea[2] = array(
						'posx' => $L1[2],
						'ancho' => $W1[2],
						'texto' => number_format($cuota['LiquidacionCuota']['total_atraso'],2),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSize
				);
			$PDF->linea[3] = array(
						'posx' => $L1[3],
						'ancho' => $W1[3],
						'texto' => number_format($cuota['LiquidacionCuota']['total_periodo'],2),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSize
				);
			$PDF->linea[4] = array(
						'posx' => $L1[4],
						'ancho' => $W1[4],
						'texto' => number_format($cuota['LiquidacionCuota']['total'],2),
						'borde' => '',
						'align' => 'R',
						'fondo' => 0,
						'style' => '',
						'colorf' => '#ccc',
						'size' => $fontSize
				);																					
			
			$PDF->Imprimir_linea();			
		
		endforeach;
		$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0] + $W1[1],
			'texto' => '',
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSize
		);
				
		$PDF->linea[2] = array(
			'posx' => $L1[2],
			'ancho' => $W1[2],
			'texto' => number_format($liquidacion['Proveedor']['total_atraso'],2),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSize
		);
		
		$PDF->linea[3] = array(
			'posx' => $L1[3],
			'ancho' => $W1[3],
			'texto' => number_format($liquidacion['Proveedor']['total_periodo'],2),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSize
		);

		$PDF->linea[4] = array(
			'posx' => $L1[4],
			'ancho' => $W1[4],
			'texto' => number_format($liquidacion['Proveedor']['total'],2),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSize
		);		
		
		$PDF->Imprimir_linea();			
		$PDF->reset();
	endforeach;
	
	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0] + $W1[1],
		'texto' => $MSG,
		'borde' => 'LTB',
		'align' => 'R',
		'fondo' => 1,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => 8
	);

	$PDF->linea[2] = array(
		'posx' => $L1[2],
		'ancho' => $W1[2],
		'texto' => number_format($ACU_ATRASO,2),
		'borde' => 'TB',
		'align' => 'R',
		'fondo' => 1,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => 8
	);	
	
	$PDF->linea[3] = array(
		'posx' => $L1[3],
		'ancho' => $W1[3],
		'texto' => number_format($ACU_PERIODO,2),
		'borde' => 'TB',
		'align' => 'R',
		'fondo' => 1,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => 8
	);	
	
	$PDF->linea[4] = array(
		'posx' => $L1[4],
		'ancho' => $W1[4],
		'texto' => number_format($ACU_TOTAL,2),
		'borde' => 'TBR',
		'align' => 'R',
		'fondo' => 1,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => 8
	);
	

	$PDF->Imprimir_linea();		
	
}

?>