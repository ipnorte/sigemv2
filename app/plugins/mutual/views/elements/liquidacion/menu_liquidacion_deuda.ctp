<?php 
$tabs = array(
				0 => array('url' => '/mutual/liquidaciones/proceso','label' => 'PROCESO LIQUIDACION DEUDA', 'icon' => 'controles/calculator.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/mutual/liquidaciones/consulta/','label' => 'CONSULTAR LIQUIDACIONES', 'icon' => 'controles/cart.png','atributos' => array(), 'confirm' => null),
				2 => array('url' => '/mutual/liquidaciones/reporte_general_imputacion','label' => 'REPORTE GENERAL IMPUTACION', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
				3 => array('url' => '/mutual/liquidaciones/reporte_control_vtos','label' => 'CONTROL VENCIMIENTOS', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
//				4 => array('url' => '/mutual/liquidaciones/consolidado','label' => 'POSICION CONSOLIDADA', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
//				3 => array('url' => '/mutual/liquidaciones/importar','label' => 'SUBIR ARCHIVOS', 'icon' => 'controles/disk_multiple.png','atributos' => array(), 'confirm' => null),
			);

$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
if(isset($INI_FILE['general']['sp_liquida_deuda_cbu']) && $INI_FILE['general']['sp_liquida_deuda_cbu'] == 1){
	$tabs[0] = array('url' => '/mutual/liquidaciones/proceso_nuevo','label' => 'PROCESO LIQUIDACION DEUDA II', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null);
}else{
    
}

echo $cssMenu->menuTabs($tabs,false);			
?>