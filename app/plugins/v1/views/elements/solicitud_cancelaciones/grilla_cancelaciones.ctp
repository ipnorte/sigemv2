<table>
	<tr>
		<th>#</th>
		<th>CODIGO</th>
		<th>BENEFICIARIO</th>
		<th>CONCEPTO</th>
		<th>DEUDA</th>
		<th>VENCIMIENTO</th>
		<th></th>
	</tr>
	<?php foreach($cancelaciones as $cancelacion):?>
		<tr>
			<td><?php echo $cancelacion['SolicitudCancelaciones']['id_cancelacion']?></td>
			<td><?php echo $cancelacion['SolicitudCancelaciones']['codigo_item']?></td>
			<td><?php echo $cancelacion['SolicitudCancelaciones']['beneficiario']?></td>
			<td><?php echo $cancelacion['SolicitudCancelaciones']['concepto']?></td>
			<td align="right"><?php echo $util->nf($cancelacion['SolicitudCancelaciones']['importe_deuda_cancela'])?></td>
			<td align="center"><?php echo $util->armaFecha($cancelacion['SolicitudCancelaciones']['vencimiento'])?></td>
			<td><?php echo $controles->openWindow('Generar Orden','/mutual/cancelacion_ordenes/generar/'.$persona_id)?></td>
		</tr>
	<?php endforeach;?>
</table>
