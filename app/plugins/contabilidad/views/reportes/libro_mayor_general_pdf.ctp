<?php 

App::import('Vendor','libro_pdf');

$PDF = new LibroPDF();

$PDF->SetTitle("LIBRO MAYOR");
$PDF->titulo['titulo1'] = "LIBRO MAYOR";
$PDF->titulo['titulo2'] = "DESDE FECHA: " . $util->armaFecha($fecha_desde) . " - HASTA FECHA: " . $util->armaFecha($fecha_hasta);
$PDF->titulo['titulo3'] = "";

$PDF->SetFontSizeConf(9);


$PDF->Open();

$W0 = array(15, 12, 103, 20, 20, 20);
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
			'texto' => 'NRO.AS.',
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
			'texto' => 'R E F E R E N C I A',
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
			'texto' => 'D E B E ',
			'borde' => 'LTBR',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->encabezado[0][4] = array(
			'posx' => $L0[4],
			'ancho' => $W0[4],
			'texto' => 'H A B E R ',
			'borde' => 'LTBR',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
$PDF->encabezado[0][5] = array(
			'posx' => $L0[5],
			'ancho' => $W0[5],
			'texto' => 'S A L D O ',
			'borde' => 'LTBR',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
	///////	


	$DEBE  = 0;
	$HABER = 0;
	$SALDO = 0;
	
	
	$i=0;
	while($i < count($libroMayorGeneral)):
	
		
		$DEBE  = 0;
		$HABER = 0;
		$SALDO = 0;
		
		$PDF->aFooter = array();

		$cuenta = $libroMayorGeneral[$i]['AsientoRenglon']['co_plan_cuenta_id'];
//		$PDF->titulo['titulo3'] = $libroMayorGeneral[$i]['PlanCuenta']['cuenta'] . ' - ' . $libroMayorGeneral[$i]['PlanCuenta']['descripcion'];
		$PDF->textoHeader = $libroMayorGeneral[$i]['PlanCuenta']['cuenta'] . ' - ' . $libroMayorGeneral[$i]['PlanCuenta']['descripcion'];
		$PDF->titulo['titulo3'] = 'LIBRO MAYOR';
		$PDF->AddPage();
		$PDF->Reset();
		

			$PDF->aFooter[0] = array(
				'posx' => $L0[0],
				'ancho' => $W0[0],
				'texto' => '',
				'borde' => 'LTB',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);
						
			$PDF->aFooter[1] = array(
				'posx' => $L0[1],
				'ancho' => $W0[1],
				'texto' => '',
				'borde' => 'TB',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);
						
			$PDF->aFooter[2] = array(
				'posx' => $L0[2],
				'ancho' => $W0[2],
				'texto' => 'TRANSPORTE: ',
				'borde' => 'TRB',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);
						
			$PDF->aFooter[3] = array(
				'posx' => $L0[3],
				'ancho' => $W0[3],
				'texto' => number_format($DEBE,2),
				'borde' => 'LTBR',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);
						
			$PDF->aFooter[4] = array(
				'posx' => $L0[4],
				'ancho' => $W0[4],
				'texto' => number_format($HABER,2),
				'borde' => 'LTBR',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);
						
			$PDF->aFooter[5] = array(
				'posx' => $L0[5],
				'ancho' => $W0[5],
				'texto' => number_format($SALDO,2),
				'borde' => 'LTBR',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);


		while($cuenta == $libroMayorGeneral[$i]['AsientoRenglon']['co_plan_cuenta_id'] && $i < count($libroMayorGeneral)):
			$DEBE += $libroMayorGeneral[$i]['AsientoRenglon']['debe'];
			$HABER += $libroMayorGeneral[$i]['AsientoRenglon']['haber'];
			$SALDO = $DEBE - $HABER;
			
			$PDF->aFooter[3]['texto'] = number_format($DEBE,2);
			$PDF->aFooter[4]['texto'] = number_format($HABER,2);
			$PDF->aFooter[5]['texto'] = number_format($SALDO,2);
			
			$PDF->linea[0] = array(
				'posx' => $L0[0],
				'ancho' => $W0[0],
				'texto' => date('d/m/Y',strtotime($libroMayorGeneral[$i]['AsientoRenglon']['fecha'])),
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
				'texto' => $libroMayorGeneral[$i]['Asiento']['nro_asiento'],
				'borde' => 'LR',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);
						
			$PDF->linea[2] = array(
				'posx' => $L0[2],
				'ancho' => $W0[2],
				'texto' => $libroMayorGeneral[$i]['Asiento']['referencia'],
				'borde' => 'LR',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => (strlen($libroMayorGeneral[$i]['Asiento']['referencia']) < 82 ? $fontSizeBody : $fontSizeBody-2)
			);
						
			$PDF->linea[3] = array(
				'posx' => $L0[3],
				'ancho' => $W0[3],
				'texto' => ($libroMayorGeneral[$i]['AsientoRenglon']['debe'] > 0 ? number_format($libroMayorGeneral[$i]['AsientoRenglon']['debe'],2) : ''),
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
				'texto' => ($libroMayorGeneral[$i]['AsientoRenglon']['haber'] > 0 ? number_format($libroMayorGeneral[$i]['AsientoRenglon']['haber'],2) : ''),
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
				'texto' => number_format($SALDO,2),
				'borde' => 'LR',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);
						
			$PDF->Imprimir_linea();

			$i++;
			if($i >= count($libroMayorGeneral)) break;
		endwhile;

		if($DEBE > 0 || $HABER > 0):

			$PDF->linea[0] = array(
				'posx' => $L0[0],
				'ancho' => $W0[0],
				'texto' => '',
				'borde' => 'LTB',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);
						
			$PDF->linea[1] = array(
				'posx' => $L0[1],
				'ancho' => $W0[1],
				'texto' => '',
				'borde' => 'TB',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);
						
			$PDF->linea[2] = array(
				'posx' => $L0[2],
				'ancho' => $W0[2],
				'texto' => 'TOTAL: ',
				'borde' => 'TRB',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);
						
			$PDF->linea[3] = array(
				'posx' => $L0[3],
				'ancho' => $W0[3],
				'texto' => number_format($DEBE,2),
				'borde' => 'LTBR',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);
						
			$PDF->linea[4] = array(
				'posx' => $L0[4],
				'ancho' => $W0[4],
				'texto' => number_format($HABER,2),
				'borde' => 'LTBR',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);
						
			$PDF->linea[5] = array(
				'posx' => $L0[5],
				'ancho' => $W0[5],
				'texto' => number_format($SALDO,2),
				'borde' => 'LTBR',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody
			);
						
			$PDF->Imprimir_linea();



		endif;


		if($i >= count($libroMayorGeneral)) break;
		
	endwhile;
	
	$PDF->aFooter = array();






$PDF->Output("libro_mayor_general.pdf");
exit;

?>