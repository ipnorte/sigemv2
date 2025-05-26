<?php 
$tabs = array(
				0 => array('url' => '/contabilidad/asientos/asiento_resultado/'.$ejercicio['id'],'label' => 'Asientos Resultado', 'icon' => 'controles/page_world.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/contabilidad/asientos/asiento_final/'.$ejercicio['id'],'label' => 'Asiento Final', 'icon' => 'controles/folder-open.png','atributos' => array(), 'confirm' => null),
				);
echo $cssMenu->menuTabs($tabs,false);			
?>

