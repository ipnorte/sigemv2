<?php 

debug($recuperosEmitidos);
exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF('L');

$PDF->SetTitle("LISTADO DE RECUPEROS");
$PDF->SetFontSizeConf(8.5);

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->titulo['titulo1'] = "";
$PDF->titulo['titulo2'] = 'LIQUIDACION ' . $liquidacion['Liquidacion']['organismo'] . "" . $liquidacion['Liquidacion']['periodo_desc'];
$PDF->titulo['titulo3'] = 'RECUPEROS DE CUOTAS EMITIDOS';

// 277
$W = array(20,25,25,20,52,10,45,20,20,20,20);
$L = $PDF->armaAnchoColumnas($W);


?>