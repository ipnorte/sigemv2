<?php 
$tabs = array(
				0 => array('url' => '/contabilidad/asientos/index','label' => 'Asientos', 'icon' => 'controles/page_world.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/contabilidad/asientos/asiento_apertura/'.$ejercicio['id'],'label' => 'Asiento Apertura', 'icon' => 'controles/folder-open.png','atributos' => array(), 'confirm' => null),
				2 => array('url' => '/contabilidad/asientos/add/'.$ejercicio['id'],'label' => 'Nuevo Asiento', 'icon' => 'controles/application_add.png','atributos' => array(), 'confirm' => null),
				3 => array('url' => '/contabilidad/asientos/cierre_periodo/'.$ejercicio['id'],'label' => 'Cierre per&iacute;odo', 'icon' => 'controles/lock.png','atributos' => array(), 'confirm' => null),
				4 => array('url' => '/contabilidad/asientos/cierre_ejercicio/'.$ejercicio['id'],'label' => 'Cierre Ejercicio', 'icon' => 'controles/lock_add.png','atributos' => array(), 'confirm' => null),
				);
echo $cssMenu->menuTabs($tabs,false);			
?>

