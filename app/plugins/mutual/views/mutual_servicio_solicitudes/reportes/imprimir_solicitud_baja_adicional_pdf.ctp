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

$PDF->SetTitle("SOLICITUD DE BAJA DE ADICIONAL AL SERVICIO #".$id);
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo3'] = "SOLICITUD DE BAJA DE ADICIONAL AL SERVICIO";
$PDF->titulo['titulo2'] = ""; //"FECHA ALTA SERVICIO: ". $util->armaFecha($solicitud['MutualServicioSolicitud']['fecha_alta_servicio']);
$PDF->titulo['titulo1'] = "FECHA EMISION: " .$util->armaFecha($solicitud['MutualServicioSolicitudAdicional'][0]['fecha_emision_baja']);
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

//$PDF->Ln(3);
	
//$PDF->linea[3] = array(
//			'posx' => 120,
//			'ancho' => 80,
//			'texto' => "COBERTURA HASTA EL " . $util->armaFecha($solicitud['MutualServicioSolicitud']['fecha_baja_servicio']),
//			'borde' => 'LBTR',
//			'align' => 'C',
//			'fondo' => 1,
//			'style' => 'B',
//			'colorf' => '#D8DBD4',
//			'size' => $size + 3
//	);	
//$PDF->Imprimir_linea();



	$PDF->Ln(3);
	
	$PDF->SetFontSizeConf(8);
//	$PDF->Cell(0,0,'Sres. Comisión Directiva:',0);
//	$PDF->Ln(3);
	$PDF->MultiCell(0,0,"Sres. Comisión Directiva:\nPor la presente, solicito a Uds. la BAJA de las siguientes personas al servicio de " . $solicitud['MutualServicioSolicitud']['mutual_proveedor_servicio']. ", en las condiciones pactadas por la ".strtoupper(Configure::read('APLICACION.nombre_fantasia'))." con dicha Empresa, las que declaro conocer y aceptar.\n",0,'J');
	
	$size = $size - 1;
	

	$PDF->Ln(2);
	
	$PDF->linea[1] = array(
				'posx' => 10,
				'ancho' => 20,
				'texto' => "DETALLE DEL ADICIONAL DADO DE BAJA",
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
			'texto' => "COBERTURA HASTA",
			'borde' => 'TBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
	);						
	$PDF->Imprimir_linea();	
	
	

			
if(!empty($solicitud['MutualServicioSolicitudAdicional'])):
	
	foreach($solicitud['MutualServicioSolicitudAdicional'] as $adicional):
	
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
				'texto' => $util->armaFecha($adicional["fecha_baja"]),
				'borde' => 'R',
				'align' => 'C',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => $size
		);						
		$PDF->Imprimir_linea();	
	
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
$PDF->Ln(10);
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


$PDF->Ln(8);

##########################################################################################################################
# CODIGO DE BARRA CUERPO MUTUAL
##########################################################################################################################

$PDF->barCode($solicitud['MutualServicioSolicitud']['barcode']);



$PDF->Output("solicitud_servicio_mutual_".$solicitud['MutualServicioSolicitud']['id'].".pdf");

?>