<?php if(!empty($pagos)):?>
<h4>FORMA DE PAGO</h4>
<table>
	<tr>
		<th>FORMA</th>
		<th>BANCO</th>
		<th>NRO.COMPROBANTE</th>
		<th>IMPORTE</th>
		<th>OBSERVACIONES</th>
		<th></th>
	</tr>
	<?php $ACU = 0;?>
	<?php foreach($pagos as $pago):?>
		<?php $ACU += $pago['MutualProductoSolicitudPago']['importe'];?>
		<tr>
			<td><?php echo $pago['MutualProductoSolicitudPago']['forma_pago_desc']?></td>
			<td><?php echo $pago['MutualProductoSolicitudPago']['banco']?></td>
			<td align="center"><?php echo $pago['MutualProductoSolicitudPago']['nro_comprobante']?></td>
			<td align="right"><?php echo $util->nf($pago['MutualProductoSolicitudPago']['importe'])?></td>
			<td><?php echo $pago['MutualProductoSolicitudPago']['observaciones']?></td>
			<td><?php echo $controles->btnAjax('controles/user-trash-full.png','/mutual/mutual_producto_solicitudes/eliminar_forma_pago/'.$pago['MutualProductoSolicitudPago']['id'],'grilla_forma_pagos',null,'Eliminar Forma de Pago?')?></td>
		</tr>
	<?php endforeach;?>
		<tr>
			<th colspan="3" class="totales">TOTAL</th>
			<th class="totales"><?php echo $util->nf($ACU)?></th>
			<th class="totales"></th>
			<th class="totales"></th>
		</tr>
</table>
<?php endif;?>