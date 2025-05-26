<?php 

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("LIBRO MAYOR BORRADOR");
$PDF->titulo['titulo1'] = "LIBRO MAYOR BORRADOR";
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
	$titulo3 = $PDF->fontSizeTitulo3;
	while($i < count($aMayorDetalle)):
		$DEBE  = 0;
		$HABER = 0;
		$SALDO = 0;

		$PDF->fontSizeTitulo3 = $titulo3;
		
		$cuenta = $aMayorDetalle[$i]['MutualAsientoRenglon']['co_plan_cuenta_id'];
		$PDF->titulo['titulo3'] = $aMayorDetalle[$i]['MutualAsientoRenglon']['cuenta'] . ' - ' . $aMayorDetalle[$i]['MutualAsientoRenglon']['descripcion'];

		if(strlen($aMayorDetalle[$i]['MutualAsientoRenglon']['cuenta'] . ' - ' . $aMayorDetalle[$i]['MutualAsientoRenglon']['descripcion']) > 44): 
			$PDF->fontSizeTitulo3 -= 1;
			if(strlen($aMayorDetalle[$i]['MutualAsientoRenglon']['cuenta'] . ' - ' . $aMayorDetalle[$i]['MutualAsientoRenglon']['descripcion']) > 50): 
				$PDF->fontSizeTitulo3 -= 2;
			endif;
		endif;

		$PDF->AddPage();
		$PDF->Reset();
		
		while($cuenta == $aMayorDetalle[$i]['MutualAsientoRenglon']['co_plan_cuenta_id'] && $i < count($aMayorDetalle)):
			$DEBE += $aMayorDetalle[$i]['MutualAsientoRenglon']['debe'];
			$HABER += $aMayorDetalle[$i]['MutualAsientoRenglon']['haber'];
			$SALDO = $DEBE - $HABER;
			$reduccion = 0;
			
			if(strlen($aMayorDetalle[$i]['MutualAsientoRenglon']['referencia']) > 68) $reduccion = 0.5;
			if(strlen($aMayorDetalle[$i]['MutualAsientoRenglon']['referencia']) > 73) $reduccion = 1;
			if(strlen($aMayorDetalle[$i]['MutualAsientoRenglon']['referencia']) > 79) $reduccion = 1.5;
			if(strlen($aMayorDetalle[$i]['MutualAsientoRenglon']['referencia']) > 95) $reduccion = 2.5;
			
			
			$PDF->linea[0] = array(
				'posx' => $L0[0],
				'ancho' => $W0[0],
				'texto' => date('d/m/Y',strtotime($aMayorDetalle[$i]['MutualAsientoRenglon']['fecha'])),
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
				'texto' => $aMayorDetalle[$i]['MutualAsientoRenglon']['mutual_asiento_id'],
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
				'texto' => $aMayorDetalle[$i]['MutualAsientoRenglon']['referencia'],
				'borde' => 'LR',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeBody - $reduccion
//				'size' => (strlen($aMayorDetalle[$i]['MutualAsientoRenglon']['referencia']) < 69 ? $fontSizeBody : $fontSizeBody-0.5)
			);
						
			$PDF->linea[3] = array(
				'posx' => $L0[3],
				'ancho' => $W0[3],
				'texto' => number_format($aMayorDetalle[$i]['MutualAsientoRenglon']['debe'],2),
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
				'texto' => number_format($aMayorDetalle[$i]['MutualAsientoRenglon']['haber'],2),
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
			if($i >= count($aMayorDetalle)) break;
		endwhile;

		if($i >= count($aMayorDetalle)) break;
		
	endwhile;
	







$PDF->Output("libro_mayor_borrador.pdf");
exit;

?>