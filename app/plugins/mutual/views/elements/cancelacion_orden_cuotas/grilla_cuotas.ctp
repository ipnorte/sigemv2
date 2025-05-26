<?php 
$cuotas = $this->requestAction('/mutual/cancelacion_orden_cuotas/cuotas_by_orden/'.$orden_cancelacion_id);
//debug($cuotas);
?>
<div style="height: 300px; overflow: scroll;">
	<h4>DETALLE DE CUOTAS INCLUIDAS EN LA CANCELACION</h4>
	<table>
	  <tr>
<!--		<th>PERIODO</th>-->
		<th>ORDEN</th>
		<th>TIPO / NUMERO</th>
		<th>PROVEEDOR / PRODUCTO</th>
		<th>CUOTA</th>
		<th>CONCEPTO</th>
		<th>VTO</th>
		<th>IMPORTE</th>
	  </tr>
	  <?php
	  	$TOTAL = 0;
	  	foreach($cuotas as $cuota):
	  		$TOTAL += $cuota['CancelacionOrdenCuota']['importe'];
	  ?>
		  <tr>
<!--			<td><strong><?php echo $util->periodo($cuota['CancelacionOrdenCuota']['OrdenDescuentoCuota']['periodo'])?></strong></td>-->
			<td align="center"><?php echo $cuota['CancelacionOrdenCuota']['OrdenDescuentoCuota']['orden_descuento_id']?></td>
			<td nowrap="nowrap"><?php echo $cuota['CancelacionOrdenCuota']['OrdenDescuentoCuota']['tipo_nro']?></td>
			<td nowrap="nowrap"><?php echo $cuota['CancelacionOrdenCuota']['OrdenDescuentoCuota']['proveedor_producto']?></td>
			<td align="center"><strong><?php echo $cuota['CancelacionOrdenCuota']['OrdenDescuentoCuota']['cuota']?></strong></td>
			<td><?php echo $cuota['CancelacionOrdenCuota']['OrdenDescuentoCuota']['tipo_cuota_desc']?></td>
			<td align="center"><strong><?php echo $util->armaFecha($cuota['CancelacionOrdenCuota']['OrdenDescuentoCuota']['vencimiento'])?></strong></td>
			<td align="right"><?php echo number_format($cuota['CancelacionOrdenCuota']['importe'],2)?></td>
		  </tr>
	  <?php endforeach;?>
	  	<tr>
	  		<th colspan="6" style="text-align:right;">TOTAL</th>
	  		<th align="right" style="text-align:right;"><?php echo number_format($TOTAL,2)?></th>
	  	</tr>
	</table>
</div>	