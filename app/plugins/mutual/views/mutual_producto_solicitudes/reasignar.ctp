<?php echo $this->renderElement('head',array('title' => 'REASIGNAR SOLICITUDES','plugin' => 'config'))?>
<div class="areaDatoForm">
<script type="text/javascript">
    
Event.observe(window, 'load', function(){
	<?php if($show == 1):?>
		$('reasignar_proveedor_search').disable();	
	<?php else:?>
		$('MutualProductoSolicitudNumero').focus();	
	<?php endif;?>
});


function validateForm(){
	return confirm("ASIGNAR LA SOLICITUD <?php echo $solicitud['MutualProductoSolicitud']['id']?> AL PROVEEDOR " + getTextoSelect('MutualProductoSolicitudReasignarProveedorId') + "?");
}

function btnAnulaOnClick(){

	if(confirm("ANULAR LA REASIGNACION DE LA SOLICITUD #<?php echo $solicitud['MutualProductoSolicitud']['id']?>?")){
		window.location='<?php echo $this->base?>/mutual/mutual_producto_solicitudes/reasignar/<?php echo $solicitud['MutualProductoSolicitud']['id']?>/?do=ANULAR';
	}	
	
}



</script>
	<?php echo $form->create(null,array('action' => 'reasignar', 'id' => 'reasignar_proveedor_search', 'onsubmit' => "document.getElementById('SolicitudCodigoProductoDescripcion').value = getTextoSelect('SolicitudCodigoProducto');"));?>
	<table class="tbl_form">
		<tr>
			<td>SOLICITUD</td><td><?php echo $frm->number('MutualProductoSolicitud.numero',array('size'=>11,'maxlength'=>10,'value'=> $nroSolicitud)); ?></td>
			<td><?php echo $frm->submit("CARGAR SOLICITUD")?></td>
		</tr>
	</table>
	<?php echo $frm->hidden('MutualProductoSolicitud.reasigna',array('value' => 0))?>
	<?php echo $frm->end()?>
</div>

<?php if($show == 1 && !empty($solicitud['MutualProductoSolicitud']['id'])):?>
	<?php echo $this->requestAction('/mutual/mutual_producto_solicitudes/view/'.$nroSolicitud)?>
	<?php if(!empty($solicitud['MutualProductoSolicitud']['reasignar_proveedor_id'])):?>
		<div class='notices_error' style="width: 100%">
			<strong>ATENCION!</strong><br/>
			La presente solicitud ya fu&eacute; marcada para ser reasignada el <?php echo $solicitud['MutualProductoSolicitud']['reasignar_proveedor_fecha']?> por el usuario <strong><?php echo $solicitud['MutualProductoSolicitud']['reasignar_proveedor_usuario']?></strong>.
			La Orden de Descuento que se emitir&aacute; ser&aacute; asignada al proveedor <strong><?php echo $solicitud['MutualProductoSolicitud']['proveedor_reasignada_a']?></strong>
			<?php if($solicitud['MutualProductoSolicitud']['aprobada'] == 0):?>
			<br/>
			<input type="button" value="ANULAR LA REASIGNACION" onclick="btnAnulaOnClick()" />
			<?php endif;?>
			
		</div>
	<?php endif;?>
	<h3>REASIGNAR LA SOLICITUD</h3>
	<div class="areaDatoForm">
	<?php echo $frm->create(null,array('action' => 'reasignar', 'id' => 'reasignar_proveedor_process', 'onsubmit' => "return validateForm()"));?>
	<table class="tbl_form">
		<tr>
			<td align="right">ASIGNAR ORDEN DE DESCUENTO A</td>
			<td><?php echo $frm->input('MutualProductoSolicitud.reasignar_proveedor_id',array('type' => 'select', 'options' => $solicitud['MutualProductoSolicitud']['proveedor_reasignable_a']))?></td>
			<td><input type="submit" value="REASIGNAR" id="btnReasignar" <?php echo (empty($solicitud['MutualProductoSolicitud']['proveedor_reasignable_a']) ? "disabled" : "")?>/></td>
			<td><input type="button" value="CANCELAR" id="btnCancelar" onclick="javascript:window.location='<?php echo $this->base?>/mutual/mutual_producto_solicitudes/reasignar'" /></td>
		</tr>
	
	</table>
	<?php echo $frm->hidden('MutualProductoSolicitud.numero',array('value' => $solicitud['MutualProductoSolicitud']['id']))?>
	<?php echo $frm->hidden('MutualProductoSolicitud.reasigna',array('value' => 1))?>
	<?php echo $frm->end()?>	
	</div>
<?php endif;?>