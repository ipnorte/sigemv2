<?php echo $this->renderElement('head',array('title' => 'CONSULTAR SOLICITUD DE CREDITO','plugin' => 'config'))?>
<?php echo $this->renderElement('mutual_producto_solicitudes/form_search_by_numero',array('accion' => 'consulta','plugin' => 'mutual','orden_descuento_id' => $solicitudID))?>

<?php if($solicitudID != 0):?>
	
	<h3>CONSULTA DE SOLICITUD</h3>

	<?php echo $this->requestAction("/mutual/mutual_producto_solicitudes/view/$solicitudID/1")?>

<?php endif;?>