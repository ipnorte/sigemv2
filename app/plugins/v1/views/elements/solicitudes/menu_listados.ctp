<?php echo $this->renderElement('head',array('title' => 'LISTADOS','plugin' => 'config'))?>
<?php 
$tabs = array(
				0 => array('url' => '/v1/solicitudes/listados/1','label' => 'Control Ventas Productores', 'icon' => 'controles/pdf.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/v1/solicitudes/listados/2','label' => 'Control Ventas Proveedores', 'icon' => 'controles/pdf.png','atributos' => array(), 'confirm' => null),
				2 => array('url' => '/v1/solicitudes/listados/3','label' => 'Control Creditos', 'icon' => 'controles/pdf.png','atributos' => array(), 'confirm' => null),
			);
echo $cssMenu->menuTabs($tabs);			
?>