<?php echo $this->renderElement('head',array('title' => 'CONSULTAR ORDEN DE DESCUENTO','plugin' => 'config'))?>
<?php echo $this->renderElement('orden_descuento/form_search_by_numero',array('accion' => 'consulta','plugin' => 'mutual','orden_descuento_id' => $ordenId))?>

<?php if($ordenId != 0):?>
	
	<h3>CONSULTA DE ORDEN DE DESCUENTO</h3>

	<?php echo $this->requestAction("/mutual/orden_descuentos/view/$ordenId/$socioId/1/0/1")?>

<?php endif;?>