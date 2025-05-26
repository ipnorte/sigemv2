<table>
	<tr>
		<th colspan="2">DETALLE DE LA LIQUIDACION DEL PRESTAMO</th>
	</tr>
	<tr>
		<td colspan="2"><?php echo $solicitud['Solicitud']['proveedor_producto']?></td>
	</tr>
	<tr>
		<td>Pr&eacute;stamo Solicitado</td><td align="right"><?php echo number_format($solicitud['Solicitud']['en_mano'],2)?></td>
	</tr>
	<?php if($solicitud['Solicitud']['total_cancelado'] != 0):?>
		<tr>
			<td>Menos Cancelaciones</td><td align="right"><?php echo number_format($solicitud['Solicitud']['total_cancelado'],2)?></td>
		</tr>
	<?php endif;?>
	<tr>
		<td><strong>NETO A PERCIBIR</strong></td><td align="right"><strong><?php echo number_format($solicitud['Solicitud']['en_mano'] - $solicitud['Solicitud']['total_cancelado'],2)?></strong></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><strong>Composici&oacute;n del Plan</strong></td>
	</tr>
	<tr>
		<td>Pr&eacute;stamo Solicitado</td><td align="right"><?php echo number_format($solicitud['Solicitud']['en_mano'],2)?></td>
	</tr>
	<tr>
		<td>Cuotas</td><td align="right"><?php echo $solicitud['Solicitud']['cuotas']?></td>
	</tr>
	<tr>
		<td><strong>Monto Cuota Pura</strong></td><td align="right"><strong><?php echo number_format($solicitud['Solicitud']['monto_cuota'],2)?></strong></td>
	</tr>
	<tr>
		<td>+<?php echo $solicitud['Solicitud']['cuota_seguro_concepto']?></td><td align="right"><?php echo $solicitud['Solicitud']['monto_seguro']?></td>
	</tr>				
	<tr>
		<td>+<?php echo $solicitud['Solicitud']['cuota_social_concepto']?></td><td align="right"><?php echo $solicitud['Solicitud']['monto_cuota_social']?></td>
	</tr>
	<tr>
		<td><strong>CUOTA TOTAL</strong></td><td align="right"><strong><?php echo number_format($solicitud['Solicitud']['cuota_total'],2)?></strong></td>
	</tr>		
</table>
<table>
	<tr>
		<td>FORMA DE PAGO DEL PRESTAMO:</td>
		<td style="background-color:#D8DBD4;">
			<strong><?php echo $solicitud['Solicitud']['forma_pago']?></strong>
		</td>
		<?php if(!empty($solicitud['Solicitud']['banco']) && $solicitud['Solicitud']['codigo_fpago'] != '0001'):?>
			<tr>
				<td>BANCO</td>
				<td><?php echo ($solicitud['Solicitud']['codigo_fpago']=='0003' ? $solicitud['Solicitud']['dato_giro'] : $solicitud['Solicitud']['banco'])?></td>
			</tr>
		<?php endif;?>
		<?php if(!empty($solicitud['Solicitud']['nro_operacion_pago'])):?>
			<tr>
				<td>NRO.OPERACION</td>
				<td><?php echo $solicitud['Solicitud']['nro_operacion_pago']?></td>
			</tr>	
		<?php endif;?>
		<?php if(!empty($solicitud['Solicitud']['fecha_operacion_pago'])):?>
			<tr>
				<td>FECHA OPERACION</td>
				<td><?php echo $util->armaFecha($solicitud['Solicitud']['fecha_operacion_pago'])?></td>	
			</tr>
		<?php endif;?>
		<?php if(!empty($solicitud['Solicitud']['nro_credito_proveedor'])):?>
			<tr>
				<td>NRO CREDITO PROVEEDOR</td>
				<td><?php echo $solicitud['Solicitud']['nro_credito_proveedor']?></td>						
			</tr>
		<?php endif;?>
	</tr>
</table>




<?php //   debug($solicitud)?>