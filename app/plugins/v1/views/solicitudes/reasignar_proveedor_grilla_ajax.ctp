<?php if(!empty($solicitudes)):?>

<table>

	<tr>
	
		
		<th>SOLICITANTE</th>
		<th>SOLICITUD NRO</th>
		<th>FECHA</th>
		<th>ESTADO</th>
		<th>SOLICITADO</th>
		<th>CUOTAS</th>
		<th></th>
	
	</tr>
	<?php $i = 0;?>
	<?php foreach($solicitudes as $solicitud):?>
	
		<?php $i++;?>
	
		<tr id="TRL_<?php echo $i?>">
			
			<td><?php echo $solicitud['solicitante']?></td>
			<td align="center"><?php echo $solicitud['nro_solicitud']?></td>
			<td><?php echo $util->armaFecha($solicitud['fecha_solicitud'])?></td>
			<td><?php echo $solicitud['estado_descripcion']?></td>
			<td align="right"><?php echo number_format($solicitud['en_mano'],2)?></td>
			<td align="center"><?php echo $solicitud['cuotas']?></td>
			<td><input type="checkbox" name="data[Solicitud][nro_solicitud][<?php echo $solicitud['nro_solicitud']?>]" value="<?php echo number_format(round($solicitud['en_mano'],2) * 100,0,".","")?>" id="SolicitudNroSolicitud_<?php echo $i?>" onclick="chkOnclick()"/></td>
		</tr>
	
	<?php endforeach;?>
	<tr class="totales">
		<th colspan="4">TOTAL SELECCIONADO</th><th id="total_seleccionado">0.00</th><th></th><th></th>
	</tr>
	<tr>
		<td align="right">ASIGNAR ORDEN DE DESCUENTO A</td>
		<td colspan="3">
		<?php echo $this->renderElement('proveedor/combo_general',array(
																		'plugin'=>'proveedores',
																		'metodo' => "proveedores_reasignables_list",
																		'model' => 'Solicitud.reasignar_proveedor_id',
																		'empty' => false,
		))?>				
		</td>
		<td><input type="submit" value="REASIGNAR" id="btnReasignar"/><input type="button" value="CANCELAR" onclick="javascript:window.location='<?php echo $this->base?>/v1/solicitudes/reasignar_proveedor'" /></td>
		<td colspan="2"><div id="spinner" style="display: none; float: left;color:red;"><?php echo $html->image('controles/ajax-loader.gif'); ?></div></td>
	</tr>

</table>
<input type="hidden" name="Rows" id="Rows" value="<?php echo count($solicitudes)?>" />

<?php else:?>

	<div class='notices_error' style="width: 100%">
		NO EXISTEN SOLICITUDES PARA ESTE PRODUCTO SIN REASIGNAR
	</div>
	<input type="button" value="CANCELAR" onclick="javascript:window.location='<?php echo $this->base?>/v1/solicitudes/reasignar_proveedor'" />
<?php endif;?>