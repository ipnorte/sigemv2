<?php 
//debug($asientos);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF('L');

$PDF->SetTitle("PLAN DE CUENTAS");
$PDF->titulo['titulo1'] = "";
$PDF->titulo['titulo2'] = $ejercicioDescripcion;
$PDF->titulo['titulo3'] = "PLAN DE CUENTA";

$PDF->SetFontSizeConf(9);

//$PDF->textoHeader = 'LIBRO DIARIO BORRADOR';

$PDF->Open();

$W0 = array(148, 41, 15, 73);
$L0 = $PDF->armaAnchoColumnas($W0);

$PDF->encabezado = array();
$fontSizeHeader = 9;
$fontSizeBody = 8;

	

$PDF->encabezado[0][0] = array(
			'posx' => $L0[0],
			'ancho' => $W0[0],
			'texto' => 'C U E N T A  -  D E S C R I P C I O N',
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
			'texto' => 'R U B R O',
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
			'texto' => 'R.AS.',
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
			'texto' => 'SUMARIZA',
			'borde' => 'LTBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#ccc',
			'size' => $fontSizeHeader
	);
		
	///////	


$PDF->AddPage();
$PDF->Reset();
		

foreach ($planCuenta as $cuenta):
	$posX = $L0[0];
	if($cuenta['PlanCuenta']['nivel'] == 2) $posX += 7;
	if($cuenta['PlanCuenta']['nivel'] == 3) $posX += 14;
	if($cuenta['PlanCuenta']['nivel'] == 4) $posX += 21;
	if($cuenta['PlanCuenta']['nivel'] == 5) $posX += 28;
	if($cuenta['PlanCuenta']['nivel'] == 6) $posX += 35;
	
	$fontSizeNivel = 9.5;
	if($cuenta['PlanCuenta']['nivel'] == 2) $fontSizeNivel = 9;
	if($cuenta['PlanCuenta']['nivel'] == 3) $fontSizeNivel = 8.5;
	if($cuenta['PlanCuenta']['nivel'] == 4) $fontSizeNivel = 8;
	if($cuenta['PlanCuenta']['nivel'] == 5) $fontSizeNivel = 7.5;
	if($cuenta['PlanCuenta']['nivel'] == 6) $fontSizeNivel = 7;
	
	$rubro = 'ACTIVO';
	if($cuenta['PlanCuenta']['tipo_cuenta'] == 'PA') $rubro = 'PASIVO';
	if($cuenta['PlanCuenta']['tipo_cuenta'] == 'PN') $rubro = 'PATRIMONIO NETO';
	if($cuenta['PlanCuenta']['tipo_cuenta'] == 'RP') $rubro = 'RESULTADO POSITIVO';
	if($cuenta['PlanCuenta']['tipo_cuenta'] == 'RN') $rubro = 'RESULTADO NEGATIVO';
	
	$PDF->linea[0] = array(
		'posx' => $posX,
		'ancho' => $W0[0],
		'texto' => $cuenta['PlanCuenta']['cuenta'] . ' - ' . $cuenta['PlanCuenta']['descripcion'],
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => ($cuenta['PlanCuenta']['nivel'] == 1 ? 'B' : ''),
		'colorf' => '#ccc',
		'size' => $fontSizeNivel
//		'size' => ($cuenta['PlanCuenta']['nivel'] == 1 ? $fontSizeHeader : $fontSizeBody)
	);
				
	$PDF->linea[1] = array(
		'posx' => $L0[1],
		'ancho' => $W0[1],
		'texto' => $rubro,
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => ($cuenta['PlanCuenta']['nivel'] == 1 ? 'B' : ''),
		'colorf' => '#ccc',
		'size' => $fontSizeBody
//		'size' => ($cuenta['PlanCuenta']['nivel'] == 1 ? $fontSizeHeader : $fontSizeBody)
	);
				
	$PDF->linea[2] = array(
		'posx' => $L0[2],
		'ancho' => $W0[2],
		'texto' => ($cuenta['PlanCuenta']['imputable'] == 0 ? 'NO' : 'SI'),
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => ($cuenta['PlanCuenta']['nivel'] == 1 ? 'B' : ''),
		'colorf' => '#ccc',
		'size' => $fontSizeBody
//		'size' => ($cuenta['PlanCuenta']['nivel'] == 1 ? $fontSizeHeader : $fontSizeBody)
	);
				
	$PDF->linea[3] = array(
		'posx' => $L0[3],
		'ancho' => $W0[3],
		'texto' => $cuenta['PlanCuenta']['co_plan_cuenta_id'],
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => ($cuenta['PlanCuenta']['nivel'] == 1 ? 'B' : ''),
		'colorf' => '#ccc',
		'size' => $fontSizeBody
//		'size' => ($cuenta['PlanCuenta']['nivel'] == 1 ? $fontSizeHeader : $fontSizeBody)
	);
				
	$PDF->Imprimir_linea();
				
	
endforeach;

$PDF->Output("plan_cuenta.pdf");
exit;

?>