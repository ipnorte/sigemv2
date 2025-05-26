<?php 
//App::import('Vendor','htmlpdf');
//$PDF = new HTMLPDF();
//$PDF->SetTitle('ORDEN DE CONSUMO / SERVICIO #'.$id);
////$PDF->imprimir(0xFF);
//$PDF->imprimir($tpl); 
//$PDF->Output("orden_consumo_servicio_mutual.pdf");

//DEBUG($orden);
//EXIT;
//$ini = parse_ini_file(CONFIGS.'mutual.ini', true);
//
//debug($ini['autorizacion_debito']);
//
//debug(json_decode($ini['general']['imprimir_autorizacion_debito']));
//
//exit;

App::import('Vendor','mutual_producto_solicitud_pdf');

$PDF = new MutualProductoSolicitudPDF();

$PDF->SetTitle("ORDEN DE CONSUMO / SERVICIO #".$id);
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo3'] = "ORDEN DE CONSUMO / SERVICIO";
$PDF->titulo['titulo2'] = ($orden['MutualProductoSolicitud']['permanente'] == 1 ? "FECHA DE INICIO: " : "FECHA PAGO / ENTREGA: ") . $util->armaFecha($orden['MutualProductoSolicitud']['fecha_pago']);
$PDF->titulo['titulo1'] = "FECHA EMISION: " .$util->armaFecha($orden['MutualProductoSolicitud']['fecha']);
$PDF->textoHeader = $orden['MutualProductoSolicitud']['tipo_numero'];

$PDF->AddPage();
$PDF->reset();

$size = 8;

$PDF->linea[1] = array(
			'posx' => 10,
			'ancho' => 20,
			'texto' => "SOLICITANTE",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
$PDF->linea[2] = array(
			'posx' => 32,
			'ancho' => 95,
			'texto' => $orden['MutualProductoSolicitud']['beneficiario'],
			'borde' => 'LTBR',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#F5f7f7',
			'size' => $size
	);
$PDF->linea[3] = array(
			'posx' => 130,
			'ancho' => 10,
			'texto' => "CUIT",
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#F5f7f7',
			'size' => $size
	);
$PDF->linea[4] = array(
			'posx' => 140,
			'ancho' => 25,
			'texto' => $orden['MutualProductoSolicitud']['beneficiario_cuit_cuil'],
			'borde' => 'LTBR',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#F5f7f7',
			'size' => $size
	);
$PDF->linea[5] = array(
			'posx' => 165,
			'ancho' => 15,
			'texto' => "SOCIO #",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
	);	
$PDF->linea[6] = array(
			'posx' => 180,
			'ancho' => 20,
			'texto' => $orden['MutualProductoSolicitud']['socio_id'],
			'borde' => 'LTBR',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#F5f7f7',
			'size' => $size
	);			
$PDF->Imprimir_linea();

$PDF->Ln(1);

$PDF->linea[1] = array(
			'posx' => 10,
			'ancho' => 20,
			'texto' => "PRODUCTO",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
$PDF->linea[2] = array(
			'posx' => 30,
			'ancho' => 80,
			'texto' => substr($orden['MutualProductoSolicitud']['proveedor_producto'],0,46),
			'borde' => 'LTBR',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#F5f7f7',
			'size' => $size
	);
	
if($orden['MutualProductoSolicitud']['permanente'] == 0):	
	
	$PDF->linea[3] = array(
				'posx' => 100,
				'ancho' => 20,
				'texto' => "TOTAL",
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
	$PDF->linea[4] = array(
				'posx' => 120,
				'ancho' => 20,
				'texto' => $util->nf($orden['MutualProductoSolicitud']['importe_total']),
				'borde' => 'LTBR',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $size
		);	
	
	$PDF->linea[5] = array(
				'posx' => 140,
				'ancho' => 12,
				'texto' => "CUOTAS",
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);	
	$PDF->linea[6] = array(
				'posx' => 152,
				'ancho' => 5,
				'texto' => $orden['MutualProductoSolicitud']['cuotas'],
				'borde' => 'LTBR',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $size
		);	
		
	$PDF->linea[7] = array(
				'posx' => 157,
				'ancho' => 24,
				'texto' => "IMPORTE CUOTA",
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);	
	$PDF->linea[8] = array(
				'posx' => 181,
				'ancho' => 19,
				'texto' => $util->nf($orden['MutualProductoSolicitud']['importe_cuota']),
				'borde' => 'LTBR',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $size
		);
else:

	$PDF->linea[3] = array(
				'posx' => 100,
				'ancho' => 40,
				'texto' => "CUOTA MENSUAL",
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
	$PDF->linea[4] = array(
				'posx' => 140,
				'ancho' => 20,
				'texto' => $util->nf($orden['MutualProductoSolicitud']['importe_cuota']),
				'borde' => 'LTBR',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $size
		);	

endif;

$PDF->Imprimir_linea();

$PDF->Ln(1);
if(!empty($orden['MutualProductoSolicitud']['observaciones'])):
	$PDF->SetFontSizeConf(6);
	$PDF->Cell(25,4,'OBSERVACIONES:',0);
	$PDF->SetX(29);
	$PDF->MultiCell(0,0,str_replace("\n","",substr($orden['MutualProductoSolicitud']['observaciones'],0,250)),0,'L');
	$PDF->Ln(1);
else:
	$PDF->Ln(8);
endif;
$PDF->SetFontSizeConf(7);
$PDF->MultiCell(0,0,"IMPORTANTE: La entidad no abonará el excedente de las compras cuyo valor supere el importe autorizado. La presente orden carece de validez si no se acompaña con el D.N.I. del titular.\n",0,'J');
$PDF->Ln(8);
$PDF->linea[1] = array(
			'posx' => 20,
			'ancho' => 40,
			'texto' => "FIRMA DEL AFILIADO",
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 6
	);
$PDF->linea[2] = array(
			'posx' => 70,
			'ancho' => 60,
			'texto' => "SELLO",
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 6
	);
$PDF->linea[3] = array(
			'posx' => 150,
			'ancho' => 40,
			'texto' => "P/COMISION DIRECTIVA",
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 6
	);		
$PDF->Imprimir_linea();	

##########################################################################################################################
#IMPRIMO LA AUTORIZACION DEL DESCUENTO
##########################################################################################################################

$PDF->Ln(5);
$PDF->linea[1] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => "AUTORIZACION DE DESCUENTO",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 10
	);
$PDF->Ln(2);	
$PDF->Imprimir_linea();	
$PDF->SetFontSizeConf(8);
$STR = "Por la presente, AUTORIZO a la ". strtoupper(Configure::read('APLICACION.nombre_fantasia')).", a descontar de mis haberes mensuales, a travéz de los Códigos de su titularidad, los montos correspondientes según el plan detallado al que adhiero, en forma mensual y consecutiva en un todo de acuerdo con los datos consignados en esta Solicitud.\n";
//$STR = utf8_encode($STR);
$PDF->MultiCell(0,0,$STR,0,'J');
$PDF->Ln(1);
$PDF->linea[1] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => "DATOS DEL DESCUENTO",
			'borde' => '',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 9
	);
$PDF->Imprimir_linea();	
$PDF->Ln(3);
$size = 7;

$PDF->linea[1] = array(
			'posx' => 10,
			'ancho' => 23,
			'texto' => "MEDIO DE PAGO",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
$PDF->linea[2] = array(
			'posx' => 33,
			'ancho' => 167,
			'texto' => $orden['MutualProductoSolicitud']['organismo_desc'] ." - ".$orden['MutualProductoSolicitud']['beneficio_str'],
			'borde' => 'LTBR',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#F5f7f7',
			'size' => $size
	);
$PDF->Imprimir_linea();
$PDF->Ln(1);
$PDF->linea[1] = array(
			'posx' => 10,
			'ancho' => 23,
			'texto' => "INICIA EN",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
$PDF->linea[2] = array(
			'posx' => 33,
			'ancho' => 30,
			'texto' => $util->periodo($orden['MutualProductoSolicitud']['periodo_ini'],true),
			'borde' => 'LTBR',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#F5f7f7',
			'size' => $size
	);	
$PDF->linea[3] = array(
			'posx' => 63,
			'ancho' => 30,
			'texto' => "VTO PRIMER CUOTA EL",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
	);	
$PDF->linea[4] = array(
			'posx' => 93,
			'ancho' => 20,
			'texto' => $util->armaFecha($orden['MutualProductoSolicitud']['primer_vto_socio']),
			'borde' => 'LTBR',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#F5f7f7',
			'size' => $size
	);
if($orden['MutualProductoSolicitud']['orden_descuento_id'] != 0):
	$PDF->linea[5] = array(
				'posx' => 113,
				'ancho' => 33,
				'texto' => "ORDEN DE DESCUENTO #",
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
	$PDF->linea[6] = array(
				'posx' => 146,
				'ancho' => 54,
				'texto' => $orden['MutualProductoSolicitud']['orden_descuento_id'],
				'borde' => 'LTBR',
				'align' => 'L',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $size
		);
endif;					
$PDF->Imprimir_linea();
$PDF->Ln(3);
$PDF->SetFontSizeConf(8);
$STR = "NOTA: Dejo expresa constancia que en caso de no poderse realizar los descuentos de la forma pactada, AUTORIZO en forma expresa a la ". strtoupper(Configure::read('APLICACION.nombre_fantasia'))." a seguir realizando los descuentos correspondientes a la total cancelación de las obligaciones por mi contraídas en este acto, con más los intereses y gastos por mora que pudieren corresponder.\n";
//$STR = utf8_encode($STR);
$PDF->MultiCell(0,0,$STR,0,'J');
$PDF->Ln(10);
$PDF->firmaSocio();

$PDF->Ln(5);

##########################################################################################################################
# CODIGO DE BARRA CUERPO MUTUAL
##########################################################################################################################

$PDF->barCode($orden['MutualProductoSolicitud']['barcode']);

$PDF->Ln(15);

##########################################################################################################################
#IMPRIMO EL PAGARE (SI NO ES UNA ORDEN PERMANENTE)
##########################################################################################################################
$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);

if($orden['MutualProductoSolicitud']['permanente'] == 0 && (!isset($INI_FILE['general']['ocom_imprime_pagare_new_page']) || $INI_FILE['general']['ocom_imprime_pagare_new_page'] == 0)):	
	$size = 8;
	$PDF->linea[1] = array(
				'posx' => 10,
				'ancho' => 190,
				'texto' => "PAGARE SIN PROTESTO (Art. 50 D.Ley 5965/63)",
				'borde' => '',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => 12
		);
	$PDF->Imprimir_linea();	
	$PDF->Ln(2);
	$PDF->linea[1] = array(
				'posx' => 10,
				'ancho' => 80,
				'texto' => "VENCE EL _______ DE _____________________ DE ___________",
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
	$PDF->linea[2] = array(
				'posx' => 150,
				'ancho' => 50,
				'texto' => "$ ".$util->nf($orden['MutualProductoSolicitud']['importe_total']),
				'borde' => '',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => $size + 3
		);	
	$PDF->Imprimir_linea();
	
	$PDF->Ln(2);
	
	$PDF->SetFontSizeConf(8);
	
	$pagareNombre = strtoupper(Configure::read('APLICACION.nombre_fantasia'));
	$pagareDomici = strtoupper(Configure::read('APLICACION.domi_fiscal'));
	
	if($orden['MutualProductoSolicitud']['proveedor_pagare_blank'] == 1){
		$pagareNombre = $pagareDomici = "____________________________________________________________";
	}
	
	//strtoupper(Configure::read('APLICACION.nombre_fantasia'))
	$STR = "PAGARE SIN PROTESTO (Art. 50 D.Ley 5965/63) a $pagareNombre o a su orden la cantidad de pesos ".$orden['MutualProductoSolicitud']['total_letras']." ($ ".$util->nf($orden['MutualProductoSolicitud']['importe_total']).") por igual valor recibido en ___________________ a mi entera satisfacción, pagadero en $pagareDomici. Conforme a lo dispuesto por el Art. 36 del D.Ley 5965 se amplia el plazo de la presente a cuatro años.\n";
//	$STR = utf8_encode($STR);
	$PDF->MultiCell(0,0,$STR,0,'J');
	$PDF->Ln(10);
	$PDF->firmaSocio();
	
	$PDF->Ln(5);
endif;

$size = 8;
$PDF->Ln(10);

##########################################################################################################################
#IMPRIMO EL CUPON
##########################################################################################################################
//$PDF->setFooterMargin(2);
//$PDF->SetAutoPageBreak(TRUE, 1);
$PDF->SetY(-75);

$PDF->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 4));
$PDF->linea[1] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => "TALON DE CONTROL PARA EL COMERCIO",
			'borde' => 'T',
			'align' => 'R',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 5
	);
$PDF->Imprimir_linea();

$PDF->Ln(3);

$PDF->Reset();

$PDF->imprimirMembrete();

$PDF->linea[1] = array(
			'posx' => 10,
			'ancho' => 20,
			'texto' => "SOLICITANTE",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
$PDF->linea[2] = array(
			'posx' => 30,
			'ancho' => 100,
			'texto' => $orden['MutualProductoSolicitud']['beneficiario'],
			'borde' => 'LTBR',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#F5f7f7',
			'size' => $size
	);
$PDF->linea[3] = array(
			'posx' => 130,
			'ancho' => 15,
			'texto' => "SOCIO #",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
	);	
$PDF->linea[4] = array(
			'posx' => 145,
			'ancho' => 55,
			'texto' => $orden['MutualProductoSolicitud']['socio_id'],
			'borde' => 'LTBR',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#F5f7f7',
			'size' => $size
	);			
$PDF->Imprimir_linea();

$PDF->Ln(1);

$PDF->linea[1] = array(
			'posx' => 10,
			'ancho' => 20,
			'texto' => "PRODUCTO",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
$PDF->linea[2] = array(
			'posx' => 30,
			'ancho' => 80,
			'texto' => substr($orden['MutualProductoSolicitud']['proveedor_producto'],0,46),
			'borde' => 'LTBR',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#F5f7f7',
			'size' => $size
	);
if($orden['MutualProductoSolicitud']['permanente'] == 0):	
	
	$PDF->linea[3] = array(
				'posx' => 100,
				'ancho' => 20,
				'texto' => "TOTAL",
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
	$PDF->linea[4] = array(
				'posx' => 120,
				'ancho' => 20,
				'texto' => $util->nf($orden['MutualProductoSolicitud']['importe_total']),
				'borde' => 'LTBR',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $size
		);	
	
	$PDF->linea[5] = array(
				'posx' => 140,
				'ancho' => 12,
				'texto' => "CUOTAS",
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);	
	$PDF->linea[6] = array(
				'posx' => 152,
				'ancho' => 5,
				'texto' => $orden['MutualProductoSolicitud']['cuotas'],
				'borde' => 'LTBR',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $size
		);	
		
	$PDF->linea[7] = array(
				'posx' => 157,
				'ancho' => 24,
				'texto' => "IMPORTE CUOTA",
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);	
	$PDF->linea[8] = array(
				'posx' => 181,
				'ancho' => 19,
				'texto' => $util->nf($orden['MutualProductoSolicitud']['importe_cuota']),
				'borde' => 'LTBR',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $size
		);
else:

	$PDF->linea[3] = array(
				'posx' => 100,
				'ancho' => 40,
				'texto' => "CUOTA MENSUAL",
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
	$PDF->linea[4] = array(
				'posx' => 140,
				'ancho' => 20,
				'texto' => $util->nf($orden['MutualProductoSolicitud']['importe_cuota']),
				'borde' => 'LTBR',
				'align' => 'R',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#F5f7f7',
				'size' => $size
		);	

endif;	
	
$PDF->Imprimir_linea();
$PDF->Ln(1);
if(!empty($orden['MutualProductoSolicitud']['observaciones'])):
	$PDF->SetFontSizeConf(6);
	$PDF->Cell(25,4,'OBSERVACIONES:',0);
	$PDF->SetX(29);
	$PDF->MultiCell(0,0,str_replace("\n","",substr($orden['MutualProductoSolicitud']['observaciones'],0,250)),0,'L');
	$PDF->Ln(1);
else:
	$PDF->Ln(8);
endif;
$PDF->SetFontSizeConf(7);
$PDF->MultiCell(0,0,"IMPORTANTE: La entidad no abonará el excedente de las compras cuyo valor supere el importe autorizado. La presente orden carece de validez si no se acompaña con el D.N.I. del titular.\n",0,'J');
$PDF->Ln(8);
$PDF->linea[1] = array(
			'posx' => 20,
			'ancho' => 40,
			'texto' => "FIRMA DEL AFILIADO",
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 6
	);
$PDF->linea[2] = array(
			'posx' => 70,
			'ancho' => 60,
			'texto' => "SELLO",
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 6
	);
$PDF->linea[3] = array(
			'posx' => 150,
			'ancho' => 40,
			'texto' => "P/COMISION DIRECTIVA",
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => 6
	);		
$PDF->Imprimir_linea();

##########################################################################################################################
# CODIGO DE BARRAS TALON CLIENTE
##########################################################################################################################
$PDF->Ln(5);
$PDF->barCode($orden['MutualProductoSolicitud']['barcode']);


$PDF->HEADER = false;
$PDF->PIE = false;

if(isset($INI_FILE['general']['ocom_imprime_pagare_new_page']) && $INI_FILE['general']['ocom_imprime_pagare_new_page'] == 1){
    $PDF->HEADER = false;
    $PDF->PIE = false;
    $PDF->AddPage();
    $PDF->reset();
    $PDF->imprimirPagare($orden,false);  
}

if(isset($orden['MutualProductoSolicitud']['proveedor_plan_anexos']) && !empty($orden['MutualProductoSolicitud']['proveedor_plan_anexos'])){
    foreach($orden['MutualProductoSolicitud']['proveedor_plan_anexos'] as $anexo){
        if($anexo == "ocom_imprime_auto_debito_nacion") $PDF->imprimirAutorizacionBancoNacion(__DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "bna2.png",$orden);
        if($anexo == "ocom_imprime_auto_debito_bcocba") $PDF->imprimirAutorizacionDebitoBcoCordoba($orden); 
        if($anexo == "ocom_imprime_auto_debito_margen") $PDF->imprimirAutorizacionDebitoMargenComercial($orden); 
        if($anexo == "ocom_imprime_pago_directo_rio") $PDF->imprimeAutoPagoDirectoSantanderRio($orden);
        if($anexo == "ocom_imprime_mutuo_minuta") $PDF->imprimeMinutaMutuo($orden);
        if($anexo == "ocom_imprime_mutuo") $PDF->imprimeContratoMutuo($orden);
        if($anexo == "ocom_imprime_pago_directo_bco_pcia_bsas") $PDF->imprimeAutoPagoDirectoBancoPciaBsAs(__DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "logo_bco_pcia_bsas.png",$orden);   
        if($anexo == "ocom_modelo_liquidacion") $PDF->imprime_modelo_liquidacion_cuotas($orden);
        if($anexo == "ocom_imprime_auto_debito_cuenca") $PDF->imprime_autorizacion_debito_cuenca($orden);
        if($anexo == "ocom_imprime_auto_tarjeta_debito") {$PDF->imprimeAutoDebitoTarjeta($orden);}
        if($anexo == "ocom_imprime_auto_tarjeta_debito_ii") {$PDF->imprimeAutoDebitoTarjetaModeloII($orden);}
        if($anexo == "ocom_imprime_solicitud_amtec") {$PDF->ocom_imprime_solicitud_amtec($orden);}
    }
}

//if(isset($INI_FILE['general']['ocom_imprime_auto_debito_nacion']) && $INI_FILE['general']['ocom_imprime_auto_debito_nacion'] == 1){
//    $PDF->imprimirAutorizacionBancoNacion(__DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "bco_nacion.jpg",$orden);
//}
//if(isset($INI_FILE['general']['ocom_imprime_auto_debito_bcocba']) && $INI_FILE['general']['ocom_imprime_auto_debito_bcocba'] == 1){
//    $PDF->imprimirAutorizacionDebitoBcoCordoba($orden);
//}
//if(isset($INI_FILE['general']['ocom_imprime_auto_debito_margen']) && $INI_FILE['general']['ocom_imprime_auto_debito_margen'] == 1){
//    $PDF->imprimirAutorizacionDebitoMargenComercial($orden);
//}
//if(isset($INI_FILE['general']['ocom_imprime_pago_directo_rio']) && $INI_FILE['general']['ocom_imprime_pago_directo_rio'] == 1){
//    $PDF->imprimeAutoPagoDirectoSantanderRio($orden);
//}
//if(isset($INI_FILE['general']['ocom_imprime_mutuo']) && $INI_FILE['general']['ocom_imprime_mutuo'] == 1){
//    $PDF->imprimeMinutaMutuo($orden);
//    $PDF->imprimeContratoMutuo($orden);
//}




//exit;
$PDF->Output("orden_consumo_servicio_mutual.pdf");

?>