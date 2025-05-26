
<?php if($menuPersonas == 1) echo $this->renderElement('personas/padron_header',array('persona' => $persona,'plugin'=>'pfyj'))?>
<?php if($persona['Persona']['fallecida'] == 1):?>
	<div class="notices_error">PERSONA REGISTRADA COMO FALLECIDA EL <?php echo $util->armaFecha($persona['Persona']['fecha_fallecimiento'])?></div>
<?php endif;?>
<h3>MODIFICAR IMPORTE MENSUAL ORDEN DE CONSUMO / SERVICIO PERMANENTE #<?php echo $this->data['MutualProductoSolicitud']['id']?></h3>
<?php echo $this->renderElement('mutual_producto_solicitudes/ficha',array('solicitud'=>$solicitud,'plugin' => 'mutual'))?>

<?php echo $frm->create(null,array('action' => 'modificar_importe_orden_permanente/'.$solicitud['MutualProductoSolicitud']['id'],'id' => 'modificarOrdenProductoMutual'))?>

<div class="areaDatoForm">
	<h4>MODIFICAR IMPORTE</h4>
	<table class="tbl_form">
	
		<tr>
			<tr>
				<td>NUEVO IMPORTE MENSUAL</td>
				<td><input name="data[MutualProductoSolicitud][importe_cuota]" type="text" value="<?php echo $solicitud['MutualProductoSolicitud']['importe_cuota']?>" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true)" id="MutualProductoSolicitudImporteCuota" /></td>
			</tr>
			<tr>
				<td>ACTUALIZAR CUOTAS ADEUDADAS</td>
				<td><input type="checkbox" name="data[MutualProductoSolicitud][recalcular_cuotas]" value="1" id="MutualProductoSolicitudRecalcularCuotas" onclick="$('mensaje_actualizacion_cuota').toggle();$('MutualProductoSolicitudAplicarDesde').toggle();"/></td>
			</tr>
			<tr id="MutualProductoSolicitudAplicarDesde" style="display: none;">
				<td>APLICAR A PARTIR DEL PERIODO</td>
				<td>
					<select name="data[MutualProductoSolicitud][aplicar_desde]">
					<?php foreach($periodos as $periodo):?>
						<option  value="<?php echo $periodo?>"><?php echo $util->periodo($periodo,true)?></option>
					<?php endforeach;?>
					</select>
				</td>
			</tr>
		</tr>
	
	</table>
	<?php echo $frm->hidden('id',array('value' => $solicitud['MutualProductoSolicitud']['id']))?>
	<?php echo $frm->hidden('fecha',array('value' => $solicitud['MutualProductoSolicitud']['fecha']))?>
	<?php echo $frm->hidden('fecha_pago',array('value' => $solicitud['MutualProductoSolicitud']['fecha_pago']))?>
	<?php echo $frm->hidden('persona_id',array('value' => $persona['Persona']['id']))?>
</div>
<div class="notices_error" id="mensaje_actualizacion_cuota" style="display: none;"><strong>ATENCION:</strong> Se actualizar&aacute;n TODAS las cuotas ADEUDADAS (vencidas y a vencer).</div>
<div style="clear: both;width: 100%;"></div>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR','URL' => ( empty($fwrd) ? "/mutual/mutual_producto_solicitudes/by_persona/".$persona['Persona']['id'] : $fwrd) ))?>