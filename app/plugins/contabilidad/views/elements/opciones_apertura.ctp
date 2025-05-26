<?php 
$tabs = array(
				0 => array('url' => '/contabilidad/asientos/apertura_manual/'.$ejercicio['id'],'label' => 'Asientos Apertura Manual', 'icon' => 'controles/page_world.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/contabilidad/asientos/apertura_automatico/'.$ejercicio['id'],'label' => 'Asiento Apertura Automatico', 'icon' => 'controles/folder-open.png','atributos' => array(), 'confirm' => null),
				);
echo $cssMenu->menuTabs($tabs,false);			
?>

