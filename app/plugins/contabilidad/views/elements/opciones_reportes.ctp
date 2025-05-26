<?php 
$tabs = array(
				0 => array('url' => '/contabilidad/reportes/libro_subdiario/'.$ejercicio['id'],'label' => 'SubDiario Caja', 'icon' => 'controles/page_world.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/contabilidad/reportes/libro_diario/'.$ejercicio['id'],'label' => 'Libro Diario', 'icon' => 'controles/page_world.png','atributos' => array(), 'confirm' => null),
				2 => array('url' => '/contabilidad/reportes/libro_mayor_general/'.$ejercicio['id'],'label' => 'Libro Mayor', 'icon' => 'controles/folder-open.png','atributos' => array(), 'confirm' => null),
				3 => array('url' => '/contabilidad/reportes/libro_suma_saldos/'.$ejercicio['id'],'label' => 'Libro Sumas y Saldos', 'icon' => 'controles/application_add.png','atributos' => array(), 'confirm' => null),
				4 => array('url' => '/contabilidad/reportes/balance_general/'.$ejercicio['id'],'label' => 'Balance', 'icon' => 'controles/application_add.png','atributos' => array(), 'confirm' => null),
				5 => array('url' => '/contabilidad/reportes/agrupar_asientos/'.$ejercicio['id'],'label' => 'Agrupar Asientos', 'icon' => 'controles/page_world.png','atributos' => array(), 'confirm' => null),
			);
echo $cssMenu->menuTabs($tabs,false);			
?>

