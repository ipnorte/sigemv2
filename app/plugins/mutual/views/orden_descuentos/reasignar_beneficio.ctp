<?php echo $this->renderElement('head',array('title' => 'REASIGNACION DE BENEFICIO','plugin' => 'config'))?>
<?php echo $this->renderElement('personas/search',array('accion' => 'reasignar_beneficio','plugin' => 'pfyj'))?>
<?php //   if(!empty($personas))echo $this->renderElement('personas/grilla_personas_paginada',array('personas'=>$personas,'accion'=>'/mutual/orden_descuentos/reasignar_beneficio/','icon' => 'controles/editpaste.png','plugin' => 'pfyj'))?>


<?php 
echo $this->renderElement(
							'personas/grilla_personas_paginada',
							array(
									'plugin' => 'pfyj',
									'accion'=>'/mutual/orden_descuentos/reasignar_beneficio/',
									'icon' => 'controles/editpaste.png',
									'busquedaAvanzada' => false,
									'datos_post' => $this->data
							));
?>