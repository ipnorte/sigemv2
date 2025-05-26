<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'ESTADO DE CUENTA'))?>
<?php echo $this->renderElement('personas/search',array('accion' => 'estado_cuenta','plugin' => 'pfyj','nro_socio' => true))?>
<?php //   if(!empty($personas))echo $this->renderElement('personas/grilla_personas_paginada',array('personas'=>$personas,'accion'=>'/pfyj/personas/estado_cuenta/','icon' => 'controles/editpaste.png','plugin' => 'pfyj'))?>


<?php 
echo $this->renderElement(
							'personas/grilla_personas_paginada',
							array(
									'plugin' => 'pfyj',
									'accion'=>'/pfyj/personas/estado_cuenta/',
									'icon' => 'controles/editpaste.png',
									'busquedaAvanzada' => false,
									'datos_post' => $this->data
							));
?>
