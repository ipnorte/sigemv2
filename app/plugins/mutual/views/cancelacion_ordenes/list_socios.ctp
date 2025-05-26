<?php echo $this->renderElement('head',array('title' => 'GENERAR ORDEN DE CANCELACION','plugin' => 'config'))?>
<?php echo $this->renderElement('personas/search',array('accion' => 'list_socios','plugin' => 'pfyj'))?>
<?php //   if(!empty($personas))echo $this->renderElement('personas/grilla_personas_paginada',array('personas'=>$personas,'accion'=>'/mutual/cancelacion_ordenes/generar/','icon' => 'controles/editpaste.png','plugin' => 'pfyj'))?>

<?php 
echo $this->renderElement(
							'personas/grilla_personas_paginada',
							array(
									'plugin' => 'pfyj',
									'accion'=>'/mutual/cancelacion_ordenes/generar/',
									'icon' => 'controles/editpaste.png',
									'busquedaAvanzada' => false,
									'datos_post' => $this->data
							));
?>
