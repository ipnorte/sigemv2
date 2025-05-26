<h1>RECAUDACION POR CAJA</h1>
<hr>
<?php echo $this->renderElement('personas/search',array('plugin' => 'pfyj'))?>
<?php //   if(!empty($personas))echo $this->renderElement('personas/grilla_personas_paginada',array('personas'=>$personas,'accion'=>'/mutual/orden_descuento_cobros/add/','icon'=>'controles/money_dollar.png','plugin' => 'pfyj'))?>
<?php 
echo $this->renderElement(
							'personas/grilla_personas_paginada',
							array(
									'plugin' => 'pfyj',
									'accion'=>'/mutual/orden_descuento_cobros/add/',
									'icon' => 'controles/money_dollar.png',
									'busquedaAvanzada' => false,
									'datos_post' => $this->data
							));
?>