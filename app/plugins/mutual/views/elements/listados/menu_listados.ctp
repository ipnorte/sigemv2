<?php 
$tabs = array(
				0 => array('url' => '/mutual/listados/ordenes_dto_por_fecha/','label' => 'Orden de Dto.', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/mutual/listados/consumos_por_fecha/','label' => 'Ord. de Consumos', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
				2 => array('url' => '/mutual/listados/padron_servicios/','label' => 'Ord. de Servicios', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
				3 => array('url' => '/mutual/listados/cancelaciones_por_fecha/','label' => 'Cancelaciones', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
				4 => array('url' => '/mutual/listados/cobros_por_fecha/','label' => 'Cobrado', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
				5 => array('url' => '/mutual/listados/reporte_inaes/','label' => 'INAES', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
//				6 => array('url' => '/mutual/listados/reporte_inaesA9/','label' => 'INAES Art. 9', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
				7 => array('url' => '/mutual/listados/listado_deuda/','label' => 'Listado de Deuda', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
				8 => array('url' => '/mutual/listados/listado_reintegros/','label' => 'Reintegros', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
			);
echo $cssMenu->menuTabs($tabs);			
?>