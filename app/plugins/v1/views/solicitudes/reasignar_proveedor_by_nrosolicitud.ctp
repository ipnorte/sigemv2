<?php echo $this->renderElement('head',array('title' => 'SOLICITUDES DE CREDITO :: REASIGNAR PROVEEDOR','plugin' => 'config'))?>

<?php 
$tabs = array(
				0 => array('url' => '/v1/solicitudes/reasignar_proveedor','label' => 'REASIGNAR SOLICITUD', 'icon' => 'controles/pin.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/v1/solicitudes/reasignar_proveedor_config','label' => 'CONFIGURACION', 'icon' => 'controles/16-security-key.png','atributos' => array(), 'confirm' => null),
			);
echo $cssMenu->menuTabs($tabs,false);			
?>

<div class="areaDatoForm">
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($show == 1):?>
		$('reasignar_proveedor_search').disable();	
	<?php else:?>
		$('SolicitudNumero').focus();	
	<?php endif;?>
});

function validateForm(){
	return confirm("ASIGNAR LA SOLICITUD <?php echo $solicitud['Solicitud']['nro_solicitud']?> AL PROVEEDOR " + getTextoSelect('SolicitudReasignarProveedorId') + "?")
}

function btnAnulaOnClick(){

	if(confirm("ANULAR LA REASIGNACION DE LA SOLICITUD #<?php echo $solicitud['Solicitud']['nro_solicitud']?>?")){
		window.location='<?php echo $this->base?>/v1/solicitudes/reasignar_proveedor/<?php echo $solicitud['Solicitud']['nro_solicitud']?>/?do=ANULAR';
	}	
	
}



</script>
	<?php echo $form->create(null,array('action' => 'reasignar_proveedor', 'id' => 'reasignar_proveedor_search', 'onsubmit' => "document.getElementById('SolicitudCodigoProductoDescripcion').value = getTextoSelect('SolicitudCodigoProducto');"));?>
	<table class="tbl_form">
		<tr>
			<td>SOLICITUD</td><td><?php echo $frm->number('Solicitud.numero',array('size'=>11,'maxlength'=>10,'value'=> $nroSolicitud)); ?></td>
			<td><?php echo $frm->submit("BUSCAR")?></td>
		</tr>
	</table>
	<?php echo $frm->hidden('Solicitud.reasigna',array('value' => 0))?>
	<?php echo $frm->end()?>
</div>

<?php if($show == 1 && !empty($solicitud['Solicitud']['nro_solicitud'])):?>


<div class="areaDatoForm2">

	<h4>SOLICITUD DE CREDITO #<?php echo $solicitud['Solicitud']['nro_solicitud']?> (<?php echo $solicitud['Solicitud']['estado_descripcion']?>)</h4>
	
	<table class="tbl_form">
		<tr>
			<td>SOLICITANTE:</td><td colspan="3"><strong><?php echo $solicitud['Solicitud']['solicitante']?></strong></td>
			
		</tr>
		<tr>
		<td>PRODUCTO:</td><td colspan="3"><strong><?php echo $solicitud['Solicitud']['proveedor_producto']?></strong></td>
		</tr>
		<tr>
			<td colspan="4">
				CAPITAL: <strong><?php echo $util->nf($solicitud['Solicitud']['solicitado'])?></strong> | EN MANO: <strong><?php echo $util->nf($solicitud['Solicitud']['en_mano'])?></strong>
				| CUOTAS: <strong><?php echo $util->nf($solicitud['Solicitud']['cuotas'],0)?></strong> | IMPORTE CUOTA: <strong><?php echo $util->nf($solicitud['Solicitud']['monto_cuota'])?></strong>
			</td>
		</tr>
		<?php if(!empty($solicitud['Cancelaciones'])):?>
			<tr>
				<td colspan="4">
					IMPORTE CANCELACION: <strong><?php echo $util->nf($solicitud['Solicitud']['total_cancelado'])?></strong>
					| NETO A PERCIBIR: <strong><?php echo $util->nf($solicitud['Solicitud']['en_mano'] - $solicitud['Solicitud']['total_cancelado'])?></strong>
				</td>
			</tr>
		<?php endif;?>
		<tr>
			<td>BENEFICIO:</td>
			<td colspan="3"><strong><?php echo $solicitud['Beneficio']['string']?></strong></td>
		</tr>
		<?php if(!empty($solicitud['Solicitud']['reasignar_proveedor_id'])):?>
			<tr>
				<td colspan="4">
				<div class='notices_error' style="width: 100%">
					<strong>ATENCION!</strong><br/>
					La presente solicitud ya fu&eacute; marcada para ser reasignada el <?php echo date("d-m-Y", strtotime($solicitud['Solicitud']['reasigna_proveedor_fecha']))?> por el usuario <strong><?php echo $solicitud['Solicitud']['reasigna_proveedor_user']?></strong>.
					La Orden de Descuento que se emitir&aacute; ser&aacute; asignada al proveedor <strong><?php echo $solicitud['Solicitud']['reasignar_proveedor_razon_social']?></strong>
					<?php if($solicitud['Solicitud']['estado'] != 14 && $solicitud['Solicitud']['estado'] != 19):?>
					<br/>
					<input type="button" value="ANULAR LA REASIGNACION" onclick="btnAnulaOnClick()" />
					<?php endif;?>
				</div>
				</td>
			</tr>
		<?php endif;?>	
			
	</table>
	<?php echo $this->renderElement('orden_descuento/by_numero', array('tipo' => 'EXPTE','numero'=> $solicitud['Solicitud']['nro_solicitud'], 'plugin' => 'mutual'))?>
	
	<?php //   debug($solicitud)?>
	
</div>

<h3>REASIGNAR LA SOLICITUD</h3>

<div class="areaDatoForm">
	
	<?php if($datosOperativos['ERROR'] == 1):?>
		<div id="msgErrorConfig" class='notices_error' style="width: 99%"><?php echo $datosOperativos['MENSAJE']?></div>
	<?php endif;?>

	<?php echo $frm->create(null,array('action' => 'reasignar_proveedor', 'id' => 'reasignar_proveedor_process', 'onsubmit' => "return validateForm()"));?>

	<table class="tbl_form">
	
		<tr>
			<?php if($datosOperativos['ERROR'] == 0):?>
				<td align="right">ASIGNAR ORDEN DE DESCUENTO A</td>
				<td><?php echo $frm->input('Solicitud.reasignar_proveedor_id',array('type' => 'select', 'options' => $datosOperativos['PROVEEDORES']))?></td>
				<td><input type="submit" value="REASIGNAR" id="btnReasignar" <?php echo ($datosOperativos['ERROR'] == 1 ? "disabled='disabled'" : "")?>/></td>
			<?php endif;?>
			<td><input type="button" value="CANCELAR" id="btnCancelar" onclick="javascript:window.location='<?php echo $this->base?>/v1/solicitudes/reasignar_proveedor'" /></td>
		</tr>
	
	</table>
	<?php echo $frm->hidden('Solicitud.numero',array('value' => $solicitud['Solicitud']['nro_solicitud']))?>
	<?php echo $frm->hidden('Solicitud.reasigna',array('value' => 1))?>
	<?php echo $frm->end()?>
</div>

<?php elseif($show == 1):?>
<div class='notices_error' style="width: 100%">
NUMERO DE SOLICITUD INEXSISTENTE.
</div>

<?php endif;?>

<?php //   debug($solicitud)?>