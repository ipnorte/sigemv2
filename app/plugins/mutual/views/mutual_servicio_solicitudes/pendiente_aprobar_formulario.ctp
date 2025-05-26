<?php echo $this->renderElement('head',array('title' => 'APROBAR ORDEN DE SERVICIO #'.$mutual_servicio_solicitud_id,'plugin' => 'config'))?>
<?php echo $this->renderElement('socios/apenom',array('socio_id' => $solicitud['MutualServicioSolicitud']['socio_id'], 'plugin' => 'pfyj'))?>
<?php echo $this->renderElement('mutual_servicio_solicitudes/ficha',array('plugin' => 'mutual','id' => $mutual_servicio_solicitud_id))?>

<?php echo $frm->create(null,array('action' => 'pendientes_aprobar'))?>

		<?php echo $frm->hidden('MutualServicioSolicitud.id',array('value' => $mutual_servicio_solicitud_id))?>
		<?php echo $frm->hidden('MutualServicioSolicitud.aprobar',array('value' => 1))?>
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'APROBAR ORDEN DE SERVICIO','URL' => ( empty($fwrd) ? "/mutual/mutual_producto_solicitudes/pendientes_aprobar" : $fwrd) ))?>


