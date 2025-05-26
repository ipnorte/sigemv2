<?php echo $this->renderElement('socios/apenom',array('socio_id' => $orden['CancelacionOrden']['socio_id'], 'plugin' => 'pfyj'))?>
<?php echo $this->renderElement('cancelacion_orden/resumen',array('orden' => $orden,'detalle_cuotas'=>true, 'plugin' => 'mutual'));?>
