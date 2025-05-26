<?php echo $this->renderElement('head',array('title' => 'ACTUALIZACION DE IMPORTES :: ORDENES DE DESCUENTO ','plugin' => 'config'))?>
<?php 
$tabs = array(
				0 => array('url' => '/mutual/orden_descuentos/actualizar_importe_puntual','label' => 'Proceso Puntual', 'icon' => 'controles/user.png','atributos' => array(), 'confirm' => null),
				2 => array('url' => '/mutual/orden_descuentos/actualizar_importe_masivo','label' => 'Proceso Masivo', 'icon' => 'controles/group_add.png','atributos' => array(), 'confirm' => null),
			);
echo $cssMenu->menuTabs($tabs,false);			
?>
<h3>PROCESO MASIVO DE ACTUALIZACION DE IMPORTES</h3>