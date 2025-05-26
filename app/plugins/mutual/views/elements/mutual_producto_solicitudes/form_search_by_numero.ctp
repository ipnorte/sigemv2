<script language="Javascript" type="text/javascript">
Event.observe(window, 'load', function() {
	$('MutualProductoSolicitudAproxId').focus();
	
});
</script>
<div id="FormSearch">
	<?php echo $form->create(null,array('action'=> $accion));?>
	<table>
		<tr>
			<td><?php echo $frm->number('MutualProductoSolicitud.aprox_id',array('label'=>'NRO DE SOLICITUD','size'=>11,'maxlength'=>10,'value'=>(isset($orden_descuento_id) && !empty($orden_descuento_id) ? $orden_descuento_id : ''))); ?></td>
			<td><?php echo $frm->submit('APROXIMAR',array('class' => 'btn_consultar'));?></td>
		</tr>
	</table>
	<?php echo $form->end();?> 
</div>