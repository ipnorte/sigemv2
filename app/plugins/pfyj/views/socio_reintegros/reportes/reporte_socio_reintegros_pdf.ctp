<?php 

//debug($reintegros);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF('L');

$PDF->SetTitle("REITEGROS SOCIO #".$socio['Socio']['id']);
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->textoHeader =  "SOCIO #" . $socio['Socio']['id'] . ' (S.E.U.O.)';
$PDF->titulo['titulo3'] = "RESUMEN DE REINTEGROS";
$PDF->titulo['titulo2'] = $apenom;
$PDF->titulo['titulo1'] = "";

//277
$W1 = array(10,65,25,25,25,25,25,77);
$L1 = $PDF->armaAnchoColumnas($W1);

$fontSizeHeader = 7;

$PDF->encabezado[0] = array();
$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => '#',
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
			'texto' => 'LIQUIDACION ORIGEN',
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
			'texto' => 'DEBITADO',
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
			'texto' => 'IMPUTADO',
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
			'texto' => 'REINTEGRO',
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
			'texto' => 'PAGOS',
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
			'texto' => 'SALDO',
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
			'texto' => 'OBSERVACIONES',
			'borde' => 'TBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
$PDF->AddPage();
$PDF->reset();	

$fontSize = 8;

$ACUM_DEBITO = 0;
$ACUM_IMPUTA = 0;
$ACUM_REINTEGRO = $ACUM_PAGOS = $ACUM_SALDO = 0;

foreach($reintegros as $reintegro):


	$ACUM_DEBITO += $reintegro['SocioReintegro']['importe_debitado'];
	$ACUM_IMPUTA += $reintegro['SocioReintegro']['importe_imputado'];
	$ACUM_REINTEGRO += $reintegro['SocioReintegro']['importe_reintegro'];
	$ACUM_PAGOS += $reintegro['SocioReintegro']['pagos'];
	$ACUM_SALDO += $reintegro['SocioReintegro']['saldo'];
	
	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0],
		'texto' => $reintegro['SocioReintegro']['id'],
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
		'texto' => $reintegro['SocioReintegro']['liquidacion_str'],
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
		'texto' => $util->nf($reintegro['SocioReintegro']['importe_debitado']),
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
		'texto' => $util->nf($reintegro['SocioReintegro']['importe_imputado']),
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
		'texto' => $util->nf($reintegro['SocioReintegro']['importe_reintegro']),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $fontSize
	);
	$PDF->linea[5] = array(
		'posx' => $L1[5],
		'ancho' => $W1[5],
		'texto' => $util->nf($reintegro['SocioReintegro']['pagos']),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $fontSize
	);		
//	$estado = "PENDIENTE";
//	if($reintegro['SocioReintegro']['imputado_deuda'] == 1):
//		$estado = "IMPUTADO EN CTA.CTE. | " . ($reintegro['SocioReintegro']['orden_descuento_cobro_id'] != 0 ?  'ORDEN COBRO #'.$reintegro['SocioReintegro']['orden_descuento_cobro_id'] : "");
//	elseif($reintegro['SocioReintegro']['reintegrado'] == 1):
//		$estado = "ABONADO AL SOCIO";
//	endif;
	
	$PDF->linea[6] = array(
		'posx' => $L1[6],
		'ancho' => $W1[6],
		'texto' => $util->nf($reintegro['SocioReintegro']['saldo']),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $fontSize
	);
	
	$PDF->linea[7] = array(
		'posx' => $L1[7],
		'ancho' => $W1[7],
		'texto' => implode(", ", $reintegro['SocioReintegro']['ordenes_pago_numeros']),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $fontSize - 1
	);	
	
	$PDF->Imprimir_linea();	
	
	if(!empty($reintegro['SocioReintegro']['cobro'])):
	
		$PDF->ln(1);
	
		$string = "ORDEN DE COBRO #".$reintegro['SocioReintegro']['cobro']['OrdenDescuentoCobro']['id'] . " | ";
		$string .= $util->globalDato($reintegro['SocioReintegro']['cobro']['OrdenDescuentoCobro']['tipo_cobro']);
		$string .= " | " . $util->armaFecha($reintegro['SocioReintegro']['cobro']['OrdenDescuentoCobro']['fecha']);
		$string .= " | " . $util->periodo($reintegro['SocioReintegro']['cobro']['OrdenDescuentoCobro']['periodo_cobro']);
		$string .= " | $ " . $util->nf($reintegro['SocioReintegro']['cobro']['OrdenDescuentoCobro']['importe']);
		
		$PDF->linea[1] = array(
			'posx' => 20,
			'ancho' => 180,
			'texto' => $string,
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSize
		);

		$PDF->Imprimir_linea();	
		
		$fontSize2 = 6;
		
		$ACU_COBRO = 0;
		$PDF->ln(1);
		
		foreach($reintegro['SocioReintegro']['cobro']['OrdenDescuentoCobroCuota'] as $cuota):
		
			$ACU_COBRO += $cuota['importe'];
		
			$PDF->linea[1] = array(
				'posx' => 20,
				'ancho' => 20,
				'texto' => "ORD.#".$cuota['OrdenDescuentoCuota']['orden_descuento_id'],
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSize2
			);
			
			$PDF->linea[2] = array(
				'posx' => 40,
				'ancho' => 20,
				'texto' => $cuota['OrdenDescuentoCuota']['tipo_nro'],
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSize2
			);			

			$PDF->linea[3] = array(
				'posx' => 60,
				'ancho' => 80,
				'texto' => $cuota['OrdenDescuentoCuota']['proveedor_producto'] ." - ". $cuota['OrdenDescuentoCuota']['tipo_cuota_desc'],
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSize2
			);			
			$PDF->linea[4] = array(
				'posx' => 140,
				'ancho' => 10,
				'texto' => $cuota['OrdenDescuentoCuota']['cuota'],
				'borde' => '',
				'align' => 'C',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSize2
			);
			$PDF->linea[5] = array(
				'posx' => 150,
				'ancho' => 20,
				'texto' => $util->nf($cuota['importe']),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSize2
			);
			$PDF->linea[6] = array(
				'posx' => 170,
				'ancho' => 20,
				'texto' => "(SALDO ".$util->nf($cuota['OrdenDescuentoCuota']['saldo_cuota']).")",
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSize2
			);									
			$PDF->Imprimir_linea();	
		
		endforeach;
		$PDF->linea[1] = array(
			'posx' => 20,
			'ancho' => 130,
			'texto' => "TOTAL COBRO",
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSize2
		);
		
		$PDF->linea[2] = array(
			'posx' => 150,
			'ancho' => 20,
			'texto' => $util->nf($ACU_COBRO),
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSize2
		);
		$PDF->linea[3] = array(
			'posx' => 170,
			'ancho' => 30,
			'texto' => "",
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSize2
		);		
		$PDF->Imprimir_linea();			
	
	endif;
	
	$PDF->ln(3);

endforeach;


$PDF->linea[0] = array(
	'posx' => $L1[0],
	'ancho' => $W1[0] + $W1[1],
	'texto' => "TOTALES",
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
	'texto' => $util->nf($ACUM_DEBITO),
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
	'texto' => $util->nf($ACUM_IMPUTA),
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
	'texto' => $util->nf($ACUM_REINTEGRO),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $fontSize
);

$PDF->linea[5] = array(
	'posx' => $L1[5],
	'ancho' => $W1[5],
	'texto' => $util->nf($ACUM_PAGOS),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $fontSize
);
$PDF->linea[6] = array(
	'posx' => $L1[6],
	'ancho' => $W1[6],
	'texto' => $util->nf($ACUM_SALDO),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $fontSize
);
$PDF->linea[7] = array(
	'posx' => $L1[7],
	'ancho' => $W1[7],
	'texto' => "",
	'borde' => 'T',
	'align' => 'L',
	'fondo' => 0,
	'style' => '',
	'colorf' => '#ccc',
	'size' => $fontSize
);

$PDF->Imprimir_linea();

$PDF->ln(3);

$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => '*** S.E.U.O. (SALVO ERROR U OMISION) ***',
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => 6
	);
$PDF->Imprimir_linea();

$PDF->Output("reintegros_socio_".$socio['Socio']['id'].".pdf");


?>