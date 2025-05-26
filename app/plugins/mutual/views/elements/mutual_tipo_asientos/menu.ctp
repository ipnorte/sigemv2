<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'CONFIGURACION TIPO DE ASIENTOS'))?>
<?php 
$tabs = array(
				0 => array('url' => '/mutual/mutual_tipo_asientos','label' => 'Tipos de Asientos', 'icon' => 'controles/calculator.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/mutual/mutual_tipo_asientos/vincular','label' => 'Vincular Tipo Asientos con Productos', 'icon' => 'controles/configure.png','atributos' => array(), 'confirm' => null),
			);
echo $cssMenu->menuTabs($tabs);	
?>