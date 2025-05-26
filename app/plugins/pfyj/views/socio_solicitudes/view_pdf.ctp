<?php 
//debug($datos);
//exit;

App::import('Vendor','solicitud_credito_general_pdf');
$PDF = new SolicitudCreditoGeneralPDF();

$membrete = array(
                'L1' => Configure::read('APLICACION.nombre_fantasia'),
                'L2' => Configure::read('APLICACION.domi_fiscal'),
                'L3' => Configure::read('APLICACION.telefonos')." - INAES ".Configure::read('APLICACION.matricula_inaes')
);

$PDF->PIE = FALSE;

$PDF->imprimirSolicitudAfiliacion($membrete,array('MutualProductoSolicitud' => $datos['SocioSolicitud']));

$PDF->Output("solicitud_afiliacion_".$datos['SocioSolicitud']['id'].".pdf");


?>