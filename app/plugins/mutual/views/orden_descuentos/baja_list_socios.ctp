<?php echo $this->renderElement('head',array('title' => 'BAJA DE ORDEN DE DESCUENTO','plugin' => 'config'))?>
<?php echo $this->renderElement('personas/search',array('accion' => 'baja','plugin' => 'pfyj'))?>
<?php //   if(!empty($personas))echo $this->renderElement('personas/grilla_personas_paginada',array('personas'=>$personas,'accion'=>'/mutual/orden_descuentos/baja/','icon' => 'controles/editpaste.png','plugin' => 'pfyj'))?>
<?php 
echo $this->renderElement(
							'personas/grilla_personas_paginada',
							array(
									'plugin' => 'pfyj',
									'accion'=>'/mutual/orden_descuentos/baja/',
									'icon' => 'controles/editpaste.png',
									'busquedaAvanzada' => false,
									'datos_post' => $this->data
							));
?>