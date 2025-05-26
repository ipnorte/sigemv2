<table>
	<?php foreach($cancelaciones as $cancelacion):?>
	<tr>
		<th style="text-align:left;border:1px solid;" colspan="2">#<?php echo $cancelacion['SolicitudCancelaciones']['id_cancelacion']?> - <?php echo $cancelacion['SolicitudCancelaciones']['beneficiario']?></th>
	</tr>
	<tr>
		<td>IMPORTE: <strong><?php echo $util->nf($cancelacion['SolicitudCancelaciones']['importe_deuda_cancela'])?></strong></td>
		<td>CONCEPTO: <strong><?php echo $cancelacion['SolicitudCancelaciones']['concepto']?></strong> </td>
	</tr>
	<tr>
	
		<td colspan="2">
		
			<table>
				<tr>
					<th style="background-color:#F5f7f7;color:gray;">OPERACION</th>
					<th style="background-color:#F5f7f7;color:gray;">PROVEEDOR</th>
					<th style="background-color:#F5f7f7;color:gray;">O.COMPRA</th>
					<th style="background-color:#F5f7f7;color:gray;">CUOTAS</th>
					<th style="background-color:#F5f7f7;color:gray;">IMP.CUOTA</th>
					<th style="background-color:#F5f7f7;color:gray;">FECHA</th>
					<th style="background-color:#F5f7f7;color:gray;">DETALLE</th>
					<th style="background-color:#F5f7f7;color:gray;">OBS.</th>
					<th style="background-color:#F5f7f7;color:gray;">PEND.</th>
				</tr>
				<?php foreach($cancelacion['SolicitudCancelacionDetalle'] as $detalle):?>
					<tr>
						<td><?php echo $detalle['SolicitudCancelacionDetalle']['tipo_liquidacion_desc']?></td>
						<td><?php echo $detalle['SolicitudCancelacionDetalle']['proveedor']?></td>
						<td align="center"><?php echo $detalle['SolicitudCancelacionDetalle']['nro_orden_compra']?></td>
						<td align="center"><?php echo $detalle['SolicitudCancelacionDetalle']['cuotas']?></td>
						<td align="right"><?php echo $util->nf($detalle['SolicitudCancelacionDetalle']['importe_cuota'])?></td>
						<td align="center"><?php echo $util->armaFecha($detalle['SolicitudCancelacionDetalle']['fecha_operacion_pago'])?></td>
						<td><?php echo $detalle['SolicitudCancelacionDetalle']['detalle_ope_ban']?></td>
						<td><?php echo $detalle['SolicitudCancelacionDetalle']['observaciones']?></td>
						<td align="center"><?php echo $controles->onOff($detalle['SolicitudCancelacionDetalle']['pendiente'],true)?></td>
					</tr>				
				
				<?php endforeach;?>
				
			</table>
		
		</td>
	
	</tr>
	
	<?php endforeach;?>

</table>
<?php //   debug($cancelaciones)?>