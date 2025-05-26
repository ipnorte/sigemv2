<?php 
//	debug($aPagos);
//	if(!empty($aPagos)):
		foreach ($aPagos as $pago):
			if($pago['liquidacion_id'] > 0):
				echo "<h3>COBRANZA POR DESCUENTO</h3>";
			elseif($pago['cancelacion_orden_id'] > 0 || $pago['orden_caja_cobro_id'] > 0):
				if($pago['orden_caja_cobro_id'] > 0):
					echo "<h3>ORDEN DE CAJA COBRO NRO.: " . $pago['orden_caja_cobro_id'] . "</H3>";
				else:
					echo "<h3>CANCELACION ORDEN NRO.: " . $controles->linkModalBox($pago['cancelacion_orden_id'],array('title' => 'DETALLE ORDEN DE CANCELACION','url' => '/mutual/cancelacion_ordenes/vista_detalle/'.$pago['cancelacion_orden_id'],'h' => 450, 'w' => 750)) . "</H3>";
				endif;		
?>
			<div>
				<h3><?php echo $pago['OrdenDescuentoCobro']['tipo_cobro_desc']?></h3>
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<th class="dato">PERIODO</th>
						<th class="dato">ORD.DTO.</th>
						<th class="dato">TIPO / NUMERO</th>
						<th class="dato">PROVEEDOR / PRODUCTO</th>
						<th class="dato">CUOTA</th>
						<th class="dato">CONCEPTO</th>
						<th class="dato">COBRADO</th>
					</tr>
					<?php $ACU_TOTAL_CUOTA = 0;?>
					<?php foreach($pago['OrdenDescuentoCobroCuota'] as $cuota):?>
						<?php $saldoCuota = $cuota['OrdenDescuentoCuota']['importe'] - $cuota['importe'];?>
						<?php $ACU_TOTAL_CUOTA += $cuota['importe'];?>
						<tr class="<?php echo ($cuota['reversado'] == 1 ? "activo_0" : "")?>">
							<td class="dato"><?php echo $util->periodo($cuota['OrdenDescuentoCuota']['periodo'])?></td>
							<td class="dato"align="center"><?php echo $controles->linkModalBox($cuota['OrdenDescuentoCuota']['orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cuota['OrdenDescuentoCuota']['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$cuota['OrdenDescuentoCuota']['orden_descuento_id'].'/'.$cuota['OrdenDescuentoCuota']['socio_id'],'h' => 450, 'w' => 750))?></td>
							<td class="dato"><?php echo $cuota['OrdenDescuentoCuota']['tipo_nro']?></td>
							<td class="dato"><?php echo $cuota['OrdenDescuentoCuota']['proveedor_producto']?></td>
							<td class="dato" align="center"><?php echo $cuota['OrdenDescuentoCuota']['cuota']?></td>
							<td class="dato"><?php echo $cuota['OrdenDescuentoCuota']['tipo_cuota_desc']?></td>
							<td class="dato" align="right"><?php echo $util->nf($cuota['importe'])?></td>
						</tr>
					<?php endforeach;?>	
					<tr>
						<th colspan="6" class="totales">TOTAL COBRADO</th>
						<th class="totales"><?php echo $util->nf($ACU_TOTAL_CUOTA)?></th>
					</tr>
				</table>
			</div>
<?php 
		else:
?>
		<h3>FACTURA DE PROVEEDOR</h3>
<?php 
		endif;
	endforeach;
?>
		