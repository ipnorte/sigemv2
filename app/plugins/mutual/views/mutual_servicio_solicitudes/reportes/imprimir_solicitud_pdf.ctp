<?php 
//App::import('Vendor','htmlpdf');
//$PDF = new HTMLPDF();
//$PDF->SetTitle('ORDEN DE CONSUMO / SERVICIO #'.$id);
////$PDF->imprimir(0xFF);
//$PDF->imprimir($tpl); 
//$PDF->Output("orden_consumo_servicio_mutual.pdf");

//DEBUG($solicitud);
//EXIT;

App::import('Vendor','generico_pdf');

$PDF = new GenericoPDF();

$PDF->SetTitle("SOLICITUD DE SERVICIO #".$id);
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo3'] = "ORDEN DE SERVICIO";
$PDF->titulo['titulo2'] = ""; //"FECHA ALTA SERVICIO: ". $util->armaFecha($solicitud['MutualServicioSolicitud']['fecha_alta_servicio']);
$PDF->titulo['titulo1'] = "FECHA EMISION: " .$util->armaFecha($solicitud['MutualServicioSolicitud']['fecha_emision']) . " | " . $solicitud['MutualServicioSolicitud']['user_created'];
$PDF->textoHeader = $solicitud['MutualServicioSolicitud']['tipo_numero'];


$PDF->AddPage();
$PDF->reset();

$size = 8;

$PDF->linea[1] = array(
			'posx' => 10,
			'ancho' => 20,
			'texto' => "TITULAR",
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
			'texto' => $solicitud['MutualServicioSolicitud']['titular_tdocndoc_apenom'],
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
			'texto' => $solicitud['MutualServicioSolicitud']['socio_id'],
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
			'texto' => "DOMICILIO",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
$PDF->linea[2] = array(
			'posx' => 30,
			'ancho' => 170,
			'texto' => $solicitud['MutualServicioSolicitud']['titular_domicilio'],
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
			'texto' => "SERVICIO",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
$PDF->linea[2] = array(
			'posx' => 30,
			'ancho' => 170,
			'texto' => $solicitud['MutualServicioSolicitud']['mutual_proveedor_servicio_ref'],
			'borde' => 'LTBR',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#F5f7f7',
			'size' => $size
	);
	
$PDF->Imprimir_linea();	

$PDF->Ln(3);
	
$PDF->linea[3] = array(
			'posx' => 120,
			'ancho' => 80,
			'texto' => "COBERTURA DESDE EL " . $util->armaFecha($solicitud['MutualServicioSolicitud']['fecha_alta_servicio']),
			'borde' => 'LBTR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size + 3
	);
//$PDF->linea[4] = array(
//			'posx' => 170,
//			'ancho' => 30,
//			'texto' => $util->armaFecha($solicitud['MutualServicioSolicitud']['fecha_alta_servicio']),
//			'borde' => 'TBR',
//			'align' => 'L',
//			'fondo' => 1,
//			'style' => 'B',
//			'colorf' => '#D8DBD4',
//			'size' => $size + 3
//	);	


$PDF->Imprimir_linea();

if(!empty($solicitud['MutualServicioSolicitudAdicional'])):

	$PDF->Ln(3);
	
	$PDF->SetFontSizeConf(8);
//	$PDF->Cell(0,0,'Sres. Comisión Directiva:',0);
//	$PDF->Ln(3);
	$PDF->MultiCell(0,0,"Sres. Comisión Directiva:\nPor la presente, solicito a Uds. la incorporación de las siguientes personas al servicio de " . $solicitud['MutualServicioSolicitud']['mutual_proveedor_servicio']. ", en las condiciones pactadas por la ".strtoupper(Configure::read('APLICACION.nombre_fantasia'))." con dicha Empresa, las que declaro conocer y aceptar.\n",0,'J');
	
	$size = $size - 1;
	

	$PDF->Ln(2);
	
	$PDF->linea[1] = array(
				'posx' => 10,
				'ancho' => 20,
				'texto' => "DETALLE DE ADICIONALES INCORPORADOS",
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
	$PDF->Imprimir_linea();	
	
	$PDF->Ln(2);
	
	$PDF->linea[1] = array(
			'posx' => 10,
			'ancho' => 20,
			'texto' => "DOCUMENTO",
			'borde' => 'LTB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$PDF->linea[2] = array(
			'posx' => 30,
			'ancho' => 50,
			'texto' => "NOMBRE Y APELLIDO",
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$PDF->linea[3] = array(
			'posx' => 80,
			'ancho' => 20,
			'texto' => "VINCULO",
			'borde' => 'TB',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$PDF->linea[4] = array(
			'posx' => 100,
			'ancho' => 70,
			'texto' => "DOMICILIO",
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$PDF->linea[5] = array(
			'posx' => 170,
			'ancho' => 30,
			'texto' => "COBERTURA DESDE",
			'borde' => 'TBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
	);						
	$PDF->Imprimir_linea();	
	

	foreach($solicitud['MutualServicioSolicitudAdicional'] as $adicional):

		if(empty($adicional["fecha_baja"])):
	
			$PDF->linea[1] = array(
					'posx' => 10,
					'ancho' => 20,
					'texto' => $adicional["adicional_tdocndoc"],
					'borde' => 'L',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => $size
			);
			$PDF->linea[2] = array(
					'posx' => 30,
					'ancho' => 50,
					'texto' => $adicional["adicional_apenom"],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => $size
			);
			$PDF->linea[3] = array(
					'posx' => 80,
					'ancho' => 20,
					'texto' => $adicional["adicional_vinculo"],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => $size
			);
			$PDF->linea[4] = array(
					'posx' => 100,
					'ancho' => 70,
					'texto' => substr($adicional["adicional_domicilio"],0,70),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => $size - 1
			);
			$PDF->linea[5] = array(
					'posx' => 170,
					'ancho' => 30,
					'texto' => $util->armaFecha($adicional["fecha_alta"]),
					'borde' => 'R',
					'align' => 'C',
					'fondo' => 0,
					'style' => 'B',
					'colorf' => '#D8DBD4',
					'size' => $size
			);
									
			$PDF->Imprimir_linea();	
			
		endif;
			
	endforeach;
	$PDF->linea[1] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => "",
			'borde' => 'T',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
	);						
	$PDF->Imprimir_linea();	
	
	$size = $size + 1;

endif;


$PDF->Ln(1);
if(!empty($solicitud['MutualServicioSolicitud']['observaciones'])):
	$PDF->SetFontSizeConf(6);
	$PDF->Cell(25,4,'OBSERVACIONES:',0);
	$PDF->SetX(29);
	$PDF->MultiCell(0,0,str_replace("\n","",substr($solicitud['MutualServicioSolicitud']['observaciones'],0,250)),0,'L');
	$PDF->Ln(1);
else:
	$PDF->Ln(8);
endif;
//$PDF->SetFontSizeConf(7);
//$PDF->MultiCell(0,0,"IMPORTANTE: La entidad no abonará el excedente de las compras cuyo valor supere el importe autorizado. La presente orden carece de validez si no se acompaña con el D.N.I. del titular.\n",0,'J');
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
$STR = "Por la presente, AUTORIZO a la ". strtoupper(Configure::read('APLICACION.nombre_fantasia')).", a descontar de mis haberes mensuales, a trav�z de los C�digos de su titularidad, los montos correspondientes seg�n el plan detallado al que adhiero, en forma mensual y consecutiva en un todo de acuerdo con los datos consignados en esta Solicitud.\n";
$STR = utf8_encode($STR);
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
			'texto' => $solicitud['MutualServicioSolicitud']['beneficio'],
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
			'texto' => $util->periodo($solicitud['MutualServicioSolicitud']['periodo_desde'],true),
			'borde' => 'LTBR',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#F5f7f7',
			'size' => $size
	);	
$PDF->linea[3] = array(
			'posx' => 63,
			'ancho' => 25,
			'texto' => "IMPORTE MENSUAL",
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
	);	
$PDF->linea[4] = array(
			'posx' => 88,
			'ancho' => 25,
			'texto' => $util->nf($solicitud['MutualServicioSolicitud']['importe_mensual_total']),
			'borde' => 'LTBR',
			'align' => 'R',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#F5f7f7',
			'size' => $size
	);
if($solicitud['MutualServicioSolicitud']['orden_descuento_id'] != 0):
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
				'texto' => $solicitud['MutualServicioSolicitud']['orden_descuento_id'],
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
$STR = "NOTA: Dejo expresa constancia que en caso de no poderse realizar los descuentos de la forma pactada, AUTORIZO en forma expresa a la ". strtoupper(Configure::read('APLICACION.nombre_fantasia'))." a seguir realizando los descuentos correspondientes a la total cancelaci�n de las obligaciones por mi contra�das en este acto, con m�s los intereses y gastos por mora que pudieren corresponder.\n";
$STR = utf8_encode($STR);
$PDF->MultiCell(0,0,$STR,0,'J');
$PDF->Ln(10);
$PDF->firmaSocio();

$PDF->Ln(5);

##########################################################################################################################
# CODIGO DE BARRA CUERPO MUTUAL
##########################################################################################################################

$PDF->barCode($solicitud['MutualServicioSolicitud']['barcode']);

//$PDF->Ln(15);

##########################################################################################################################
#IMPRIMO EL CUPON
##########################################################################################################################
//$PDF->SetY(-75);
//
//$PDF->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 4));
//$PDF->linea[1] = array(
//			'posx' => 10,
//			'ancho' => 190,
//			'texto' => "TALON DE CONTROL PARA EL COMERCIO",
//			'borde' => 'T',
//			'align' => 'R',
//			'fondo' => 0,
//			'style' => '',
//			'colorf' => '#D8DBD4',
//			'size' => 5
//	);
//$PDF->Imprimir_linea();
//
//$PDF->Ln(3);
//
//$PDF->Reset();
//
//$PDF->imprimirMembrete();
//
//$PDF->linea[1] = array(
//			'posx' => 10,
//			'ancho' => 20,
//			'texto' => "SOLICITANTE",
//			'borde' => '',
//			'align' => 'L',
//			'fondo' => 0,
//			'style' => '',
//			'colorf' => '#D8DBD4',
//			'size' => $size
//	);
//$PDF->linea[2] = array(
//			'posx' => 30,
//			'ancho' => 100,
//			'texto' => $solicitud['MutualServicioSolicitud']['titular_tdocndoc_apenom'],
//			'borde' => 'LTBR',
//			'align' => 'L',
//			'fondo' => 1,
//			'style' => 'B',
//			'colorf' => '#F5f7f7',
//			'size' => $size
//	);
//$PDF->linea[3] = array(
//			'posx' => 130,
//			'ancho' => 15,
//			'texto' => "SOCIO #",
//			'borde' => '',
//			'align' => 'L',
//			'fondo' => 0,
//			'style' => '',
//			'colorf' => '#D8DBD4',
//			'size' => $size
//	);	
//$PDF->linea[4] = array(
//			'posx' => 145,
//			'ancho' => 55,
//			'texto' => $solicitud['MutualServicioSolicitud']['socio_id'],
//			'borde' => 'LTBR',
//			'align' => 'L',
//			'fondo' => 1,
//			'style' => 'B',
//			'colorf' => '#F5f7f7',
//			'size' => $size
//	);			
//$PDF->Imprimir_linea();
//
//$PDF->Ln(1);
//
//$PDF->linea[1] = array(
//			'posx' => 10,
//			'ancho' => 20,
//			'texto' => "SERVICIO",
//			'borde' => '',
//			'align' => 'L',
//			'fondo' => 0,
//			'style' => '',
//			'colorf' => '#D8DBD4',
//			'size' => $size
//	);
//$PDF->linea[2] = array(
//			'posx' => 30,
//			'ancho' => 80,
//			'texto' => substr($solicitud['MutualServicioSolicitud']['mutual_proveedor_servicio_ref'],0,46),
//			'borde' => 'LTBR',
//			'align' => 'L',
//			'fondo' => 1,
//			'style' => 'B',
//			'colorf' => '#F5f7f7',
//			'size' => $size
//	);
//
//
//$PDF->linea[3] = array(
//			'posx' => 100,
//			'ancho' => 40,
//			'texto' => "CUOTA MENSUAL",
//			'borde' => '',
//			'align' => 'R',
//			'fondo' => 0,
//			'style' => '',
//			'colorf' => '#D8DBD4',
//			'size' => $size
//	);
//$PDF->linea[4] = array(
//			'posx' => 140,
//			'ancho' => 20,
//			'texto' => $util->nf($solicitud['MutualServicioSolicitud']['importe_mensual_total']),
//			'borde' => 'LTBR',
//			'align' => 'R',
//			'fondo' => 1,
//			'style' => 'B',
//			'colorf' => '#F5f7f7',
//			'size' => $size
//	);	
//
//
//	
//$PDF->Imprimir_linea();
//$PDF->Ln(1);
//if(!empty($solicitud['MutualServicioSolicitud']['observaciones'])):
//	$PDF->SetFontSizeConf(6);
//	$PDF->Cell(25,4,'OBSERVACIONES:',0);
//	$PDF->SetX(29);
//	$PDF->MultiCell(0,0,str_replace("\n","",substr($solicitud['MutualServicioSolicitud']['observaciones'],0,250)),0,'L');
//	$PDF->Ln(1);
//else:
//	$PDF->Ln(8);
//endif;
//$PDF->SetFontSizeConf(7);
//$PDF->MultiCell(0,0,"IMPORTANTE: La entidad no abonará el excedente de las compras cuyo valor supere el importe autorizado. La presente orden carece de validez si no se acompaña con el D.N.I. del titular.\n",0,'J');
//$PDF->Ln(8);
//$PDF->linea[1] = array(
//			'posx' => 20,
//			'ancho' => 40,
//			'texto' => "FIRMA DEL AFILIADO",
//			'borde' => 'T',
//			'align' => 'C',
//			'fondo' => 0,
//			'style' => '',
//			'colorf' => '#D8DBD4',
//			'size' => 6
//	);
//$PDF->linea[2] = array(
//			'posx' => 70,
//			'ancho' => 60,
//			'texto' => "SELLO",
//			'borde' => '',
//			'align' => 'C',
//			'fondo' => 0,
//			'style' => '',
//			'colorf' => '#D8DBD4',
//			'size' => 6
//	);
//$PDF->linea[3] = array(
//			'posx' => 150,
//			'ancho' => 40,
//			'texto' => "P/COMISION DIRECTIVA",
//			'borde' => 'T',
//			'align' => 'C',
//			'fondo' => 0,
//			'style' => '',
//			'colorf' => '#D8DBD4',
//			'size' => 6
//	);		
//$PDF->Imprimir_linea();
//
//##########################################################################################################################
//# CODIGO DE BARRAS TALON CLIENTE
//##########################################################################################################################
//$PDF->Ln(5);
//$PDF->barCode($solicitud['MutualServicioSolicitud']['barcode']);


$PDF->Output("solicitud_servicio_mutual_".$solicitud['MutualServicioSolicitud']['id'].".pdf");

?>