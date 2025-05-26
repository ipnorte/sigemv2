<?php echo $this->renderElement('head',array('title' => 'CUOTAS :: CAMBIO DE SITUACION','plugin' => 'config'))?>
<?php echo $this->renderElement('personas/search',array('accion' => 'cambiar_situacion','plugin' => 'pfyj'))?>
<?php //   echo $this->renderElement('personas/grilla_personas_paginada',array('personas'=>$personas,'accion'=>'/mutual/orden_descuento_cuotas/cambiar_situacion/','icon' => 'controles/editpaste.png','plugin' => 'pfyj'))?>


<?php 
echo $this->renderElement(
							'personas/grilla_personas_paginada',
							array(
									'plugin' => 'pfyj',
									'accion'=>'/mutual/orden_descuento_cuotas/cambiar_situacion/',
									'icon' => 'controles/editpaste.png',
									'busquedaAvanzada' => false,
									'datos_post' => $this->data
							));
?>
