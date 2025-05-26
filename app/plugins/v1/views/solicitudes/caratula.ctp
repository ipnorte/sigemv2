<?php //   DEBUG($solicitud)?>
<h1>SOLICITUD DE CREDITO Nro. <?php echo $nro_solicitud?> :: (<?php echo $solicitud['Solicitud']['estado_descripcion']?>)</h1>
<hr>
<?php echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$persona,'link' => false,'plugin' => 'pfyj'))?>

<h3 style="border-bottom: 1px solid;">BENEFICIO POR EL CUAL SE DESCUENTA</h3>
<?php echo $solicitud['Beneficio']['string']?>

<h3 style="border-bottom: 1px solid;">LIQUIDACION Y PAGO DEL PRESTAMO</h3>
<?php echo $this->renderElement('solicitudes/grilla_liquidacion', array('solicitud' => $solicitud, 'plugin' => 'v1'))?>

<?php if(count($solicitud['SolicitudCancelacionOrden'])!=0):?>

	<h3 style="border-bottom: 1px solid;">ORDENES DE CANCELACION</h3>
		<?php echo $this->renderElement('solicitud_cancelaciones/ordenes_cancelacion_info_pago', array('cancelaciones' => $solicitud['SolicitudCancelacionOrden'],'persona_id' => $persona['Persona']['id'], 'plugin' => 'v1'))?>
	
<?php elseif(count($solicitud['Cancelaciones'])!=0):?>
	<h3 style="border-bottom: 1px solid;">CANCELACIONES</h3>
		<?php echo $this->renderElement('solicitud_cancelaciones/cancelaciones_info_pago', array('cancelaciones' => $solicitud['Cancelaciones'],'persona_id' => $persona['Persona']['id'], 'plugin' => 'v1'))?>

<?php endif;?>
