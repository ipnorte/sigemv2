<?php 

// debug($errores);
// exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF();

$PDF->SetTitle("LISTADO SOPORTE DISKETTE CBU :: ".$diskette['banco_intercambio_nombre']." :: ". $util->periodo($liquidacion['Liquidacion']['periodo'],true));
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

$PDF->textoHeader = "";

#TITULO DEL REPORTE#
$PDF->titulo['titulo1'] = $diskette['banco_intercambio_nombre'];
$PDF->titulo['titulo2'] = $liquidacion['Liquidacion']['organismo'] . "|" . $liquidacion['Liquidacion']['periodo_desc'] . "|A DEBITAR EL: " . $diskette['fecha_debito'];
$PDF->titulo['titulo3'] = 'LISTADO DE SOPORTE DE DISKETTE';

$W1 = array(10,90,5,40,25,20);
$L1 = $PDF->armaAnchoColumnas($W1);

$fontSizeHeader = 7;

$PDF->encabezado = array();
$PDF->encabezado[0] = array();	

$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => 'LINEA',
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
			'texto' => 'DNI - APELLIDO Y NOMBRE',
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
			'texto' => 'REG',
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
			'texto' => 'SUCURSAL - CUENTA',
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
			'texto' => 'CBU',
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
			'texto' => 'IMPORTE',
			'borde' => 'TBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);

$PDF->AddPage();
$PDF->reset();
$size = 7;
$reg = 0;

$linea =  $regT = $ACU_IMPOT = $ACU_IMPODTOT = $ACU_REGISTROST = 0;
$aACUM_DISK = array();
$aACUM_DISK_TOTALES = array();

$turnoActual = NULL;
$primero = true;

foreach($datos as $socio){

	if($turnoActual != $socio['LiquidacionSocio']['turno_pago']){

		$turnoActual = $socio['LiquidacionSocio']['turno_pago'];

		if(!$primero){

			$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => $W1[0] + $W1[1] + $W1[2] + $W1[3] + $W1[4],
				'texto' => "SUBTOTAL [".$reg." REG.]",
				'borde' => 'T',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $size
			);	
			
			$PDF->linea[5] = array(
				'posx' => $L1[5],
				'ancho' => $W1[5],
				'texto' => $util->nf($ACU_IMPO),
				'borde' => 'T',
				'align' => 'R',
				'fondo' => 2,
				'style' => 'B',
				'colorf' => '#ccc',
				'size' => $size
			);		
		
			$PDF->Imprimir_linea();	
		
			

		}
		
		$ACU_IMPOT += $ACU_IMPO;
		$ACU_IMPODTOT += $ACU_IMPODTO;
		$ACU_REGISTROST += $ACU_REGISTROS;
		$regT += $reg;

		$primero = false;


		$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => 190,
			'texto' => $socio['GlobalDato']['concepto_1'],
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $size + 3
		);
		$PDF->Ln(3);
		$PDF->Imprimir_linea();

		$reg = $ACU_IMPO = $ACU_IMPODTO = $ACU_REGISTROS = 0;

	}


	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0],
		'texto' => $linea,
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);
	
	$PDF->linea[1] = array(
		'posx' => $L1[1],
		'ancho' => $W1[1],
		'texto' => substr($socio['LiquidacionSocio']['documento'] ." - ". utf8_encode($socio['LiquidacionSocio']['apenom']),0,55),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);
	$PDF->linea[2] = array(
		'posx' => $L1[2],
		'ancho' => $W1[2],
		'texto' => $socio['LiquidacionSocio']['registro'],
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);	

	$PDF->linea[3] = array(
		'posx' => $L1[3],
		'ancho' => $W1[3],
		'texto' => $socio['LiquidacionSocio']['sucursal']."-".$socio['LiquidacionSocio']['nro_cta_bco'],
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);
	$PDF->linea[4] = array(
		'posx' => $L1[4],
		'ancho' => $W1[4],
		'texto' => $socio['LiquidacionSocio']['cbu'],
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);				
	$PDF->linea[5] = array(
		'posx' => $L1[5],
		'ancho' => $W1[5],
		'texto' => $util->nf($socio['LiquidacionSocio']['importe_adebitar']),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size
	);		

	$ACU_IMPO += $socio['LiquidacionSocio']['importe_adebitar'];
	$ACU_IMPODTO += $socio['LiquidacionSocio']['importe_dto'];
	$ACU_REGISTROS++;
	$reg++;
	$linea++;

	$PDF->Imprimir_linea();	
}

$PDF->linea[0] = array(
	'posx' => $L1[0],
	'ancho' => $W1[0] + $W1[1] + $W1[2] + $W1[3] + $W1[4],
	'texto' => "SUBTOTAL [".$reg." REG.]",
	'borde' => 'T',
	'align' => 'L',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size
);	

$PDF->linea[5] = array(
	'posx' => $L1[5],
	'ancho' => $W1[5],
	'texto' => $util->nf($ACU_IMPO),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 2,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size
);		

$PDF->Imprimir_linea();	

$ACU_IMPOT += $ACU_IMPO;
$ACU_IMPODTOT += $ACU_IMPODTO;
$ACU_REGISTROST += $ACU_REGISTROS;
$regT += $reg;


$PDF->ln(2);

$PDF->linea[0] = array(
	'posx' => $L1[0],
	'ancho' => $W1[0] + $W1[1] + $W1[2] + $W1[3] + $W1[4],
	'texto' => "TOTAL A DEBITAR " . " [ARCHIVO: " . $diskette['archivo'] . " | " .$regT." REG.]",
	'borde' => 'LTB',
	'align' => 'L',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size + 2
);	

$PDF->linea[5] = array(
	'posx' => $L1[5],
	'ancho' => $W1[5],
	'texto' => $util->nf($ACU_IMPOT),
	'borde' => 'TBR',
	'align' => 'R',
	'fondo' => 1,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size + 2
);		

$aACUM_DISK_TOTALES['registros'] = $regT;
$aACUM_DISK_TOTALES['importe_adebitar'] = $ACU_IMPOT;

$PDF->Imprimir_linea();


#######################################################################################################
#PAGINA CON EL RESUMEN DE TURNOS
#######################################################################################################

$W1 = array(20,140,30);
$L1 = $PDF->armaAnchoColumnas($W1);

$fontSizeHeader = 7;

$PDF->encabezado = array();
$PDF->encabezado[0] = array();	

$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => 'REGISTROS',
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
			'texto' => 'EMPRESA - TURNO',
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
			'texto' => 'IMPORTE',
			'borde' => 'TBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);

$PDF->AddPage();
$PDF->reset();

$PDF->linea[0] = array(
	'posx' => $L1[0],
	'ancho' => 170,
	'texto' => "RESUMEN DE CONTROL",
	'borde' => '',
	'align' => 'L',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => 10
);	

$PDF->Imprimir_linea();
$PDF->ln(2);

foreach($resumen_operativo as $turno):


	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0],
		'texto' => $turno[0]['liquidados'],
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size + 1
	);
	$PDF->linea[1] = array(
		'posx' => $L1[1],
		'ancho' => $W1[1],
		'texto' => $turno[0]['turno_pago_desc'],
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size + 1
	);	
	$PDF->linea[2] = array(
		'posx' => $L1[2],
		'ancho' => $W1[2],
		'texto' => $util->nf($turno[0]['importe_adebitar']),
		'borde' => '',
		'align' => 'R',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#ccc',
		'size' => $size + 1
	);		
	
	$PDF->Imprimir_linea();

endforeach;

$PDF->linea[0] = array(
	'posx' => $L1[0],
	'ancho' => $W1[0],
	'texto' => $aACUM_DISK_TOTALES['registros'],
	'borde' => 'T',
	'align' => 'L',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size + 1
);	
$PDF->linea[1] = array(
	'posx' => $L1[1],
	'ancho' => $W1[1],
	'texto' => "TOTAL A DEBITAR " . " [ARCHIVO: " . $diskette['archivo']."]",
	'borde' => 'T',
	'align' => 'L',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size + 1
);
$PDF->linea[2] = array(
	'posx' => $L1[2],
	'ancho' => $W1[2],
	'texto' => $util->nf($aACUM_DISK_TOTALES['importe_adebitar']),
	'borde' => 'T',
	'align' => 'R',
	'fondo' => 0,
	'style' => 'B',
	'colorf' => '#ccc',
	'size' => $size + 1
);		
$PDF->Imprimir_linea();


########################################################################################
#PAGINA CON DETALLE DE ERRORES
########################################################################################
if(!empty($errores)){
	$W1 = array(70,5,40,25,20,30);
	//$W1 = array(5,60,5,70,30,20);
	$L1 = $PDF->armaAnchoColumnas($W1);
	
	$fontSizeHeader = 7;
	
	$PDF->encabezado = array();
	$PDF->encabezado[0] = array();	
	
	$PDF->encabezado[0][0] = array(
				'posx' => $L1[0],
				'ancho' => $W1[0],
				'texto' => 'DNI - APELLIDO Y NOMBRE',
				'borde' => 'TBL',
				'align' => 'C',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);
	$PDF->encabezado[0][1] = array(
				'posx' => $L1[1],
				'ancho' => $W1[1],
				'texto' => 'REG',
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
				'texto' => 'SUCURSAL - CUENTA',
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
				'texto' => 'CBU',
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
				'texto' => 'IMPORTE',
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
				'texto' => 'MOTIVO',
				'borde' => 'TBR',
				'align' => 'C',
				'fondo' => 1,
				'style' => '',
				'colorf' => '#ccc',
				'size' => $fontSizeHeader
		);
	$PDF->AddPage();
	$PDF->reset();
	
	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => 170,
		'texto' => "REGISTROS EXCLUIDOS",
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => 10
	);	
	
	$PDF->Imprimir_linea();
	$PDF->ln(2);
	
	$size = 6;
	

	$aACUM_DISK = array();
	$aACUM_DISK_TOTALES = array();

	$reg = $ACU_IMPO = $ACU_IMPODTO = $ACU_REGISTROS = 0;
	
	foreach($errores as $socio){
		$ACU_IMPO += $socio['LiquidacionSocio']['importe_adebitar'];
		$ACU_IMPODTO += $socio['LiquidacionSocio']['importe_dto'];
		$ACU_REGISTROS++;
		$reg++;
	
		$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W1[0],
			'texto' => substr($socio['LiquidacionSocio']['documento'] ." - ". $socio['LiquidacionSocio']['apenom'],0,55),
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $size
		);
		$PDF->linea[1] = array(
			'posx' => $L1[1],
			'ancho' => $W1[1],
			'texto' => $socio['LiquidacionSocio']['registro'],
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $size
		);	

		$PDF->linea[2] = array(
			'posx' => $L1[2],
			'ancho' => $W1[2],
			'texto' => $socio['LiquidacionSocio']['sucursal']."-".$socio['LiquidacionSocio']['nro_cta_bco'],
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $size
		);
		$PDF->linea[3] = array(
			'posx' => $L1[3],
			'ancho' => $W1[3],
			'texto' => $socio['LiquidacionSocio']['cbu'],
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $size
		);				
		$PDF->linea[4] = array(
			'posx' => $L1[4],
			'ancho' => $W1[4],
			'texto' => $util->nf($socio['LiquidacionSocio']['importe_adebitar']),
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $size
		);		
		$PDF->linea[5] = array(
			'posx' => $L1[5],
			'ancho' => $W1[5],
			'texto' => $socio['LiquidacionSocio']['error_intercambio'],
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $size
		);		
		$PDF->Imprimir_linea();	
	}
	$PDF->linea[0] = array(
		'posx' => $L1[0],
		'ancho' => $W1[0] + $W1[1] + $W1[2] + $W1[3],
		'texto' => "TOTAL [".$reg." REG.]",
		'borde' => 'T',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $size + 1
	);	
	
	$PDF->linea[4] = array(
		'posx' => $L1[4],
		'ancho' => $W1[4],
		'texto' => $util->nf($ACU_IMPO),
		'borde' => 'T',
		'align' => 'R',
		'fondo' => 2,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $size + 1
	);		
	$PDF->linea[5] = array(
		'posx' => $L1[5],
		'ancho' => $W1[5],
		'texto' => "",
		'borde' => 'T',
		'align' => 'R',
		'fondo' => 2,
		'style' => 'B',
		'colorf' => '#ccc',
		'size' => $size + 1
	);	
	$PDF->Imprimir_linea();		
}


$PDF->Output("listado_diskette.pdf");

?>