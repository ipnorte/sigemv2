<?php 
$tabs = array(
				0 => array('url' => '/mutual/orden_descuentos/liquidacion_proceso','label' => 'PROCESO LIQUIDACION DEUDA', 'icon' => 'controles/calculator.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/mutual/orden_descuentos/liquidacion_consulta/','label' => 'CONSULTAR LIQUIDACIONES', 'icon' => 'controles/cart.png','atributos' => array(), 'confirm' => null),
				2 => array('url' => '/mutual/orden_descuentos/liquidacion_exportar','label' => 'EXPORTAR DATOS', 'icon' => 'controles/disk_multiple.png','atributos' => array(), 'confirm' => null),
				3 => array('url' => '/mutual/liquidaciones/importar','label' => 'IMPORTAR DATOS', 'icon' => 'controles/database_add.png','atributos' => array(), 'confirm' => null),
			);
echo $cssMenu->menuTabs($tabs,false);			
?>