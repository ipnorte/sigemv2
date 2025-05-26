<?php echo $this->renderElement('head',array('title' => 'OBSERVAR SOLICITUD #'.$solicitud['MutualProductoSolicitud']['nro_print'],'plugin' => 'config'))?>
<?php if($solicitud['MutualProductoSolicitud']['socio_id'] != 0):?>
	<?php echo $this->renderElement('socios/apenom',array('socio_id' => $solicitud['MutualProductoSolicitud']['socio_id'], 'plugin' => 'pfyj'))?>
<?php else:?>
	<?php echo $this->renderElement('personas/datos_personales_info_header',array('persona_id'=>$solicitud['MutualProductoSolicitud']['persona_id'],'plugin' => 'pfyj'))?>
<?php endif;?>
<?php echo $this->renderElement('mutual_producto_solicitudes/ficha_solicitud_credito',array('solicitud'=>$solicitud,'fPago'=>true,'plugin' => 'mutual'))?>
<div class="areaDatoForm">
<?php echo $frm->create(null,array('action' => 'observar/' .$solicitud['MutualProductoSolicitud']['id']))?>
<table class="tbl_form">
	<tr>
		<td>MOTIVO</td>
		<td><?php echo $frm->textarea('MutualProductoSolicitud.observaciones',array('cols' => 60, 'rows' => 3))?></td>
	</tr>
</table>
<?php echo $frm->hidden('MutualProductoSolicitud.id',array('value' => $solicitud['MutualProductoSolicitud']['id']))?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'OBSERVAR ESTA ORDEN','URL' => ( empty($fwrd) ? "/mutual/mutual_producto_solicitudes/creditos_pendientes_aprobar" : $fwrd) ))?>
</div>