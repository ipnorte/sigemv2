<?php echo $this->renderElement('socios/apenom',array('socio_id' => $orden['OrdenCajaCobro']['socio_id'],'plugin'=>'pfyj'))?>

	<h3>DETALLE DE LA ORDEN</h3>
	<div class="row">
		COMPROBANTE: <strong>#<?php echo $orden['OrdenCajaCobro']['id']?></strong>
		|
		VENCIMIENTO: <strong><?php echo $util->armaFecha($orden['OrdenCajaCobro']['fecha_vto'])?></strong>
		|
		IMPORTE: <strong><?php echo number_format($orden['OrdenCajaCobro']['importe_cobrado'],2)?></strong>
	</div>
	<div class="row" style="color:gray;">
		EMITIDO POR: <strong><?php echo $orden['OrdenCajaCobro']['user_created']?></strong> - <?php echo $orden['OrdenCajaCobro']['created']?>
	</div>
<!--	<div style="width:100%; ;height: 300px; overflow: scroll;">-->
		<table>
		  <tr>
			<th>PERIODO</th>
			<th>ORDEN</th>
			<th>TIPO / NUMERO</th>
			<th>PROVEEDOR / PRODUCTO</th>
			<th>CUOTA</th>
			<th>CONCEPTO</th>
			<th>VTO</th>
			<th>A PAGAR</th>
			<th>SALDO</th>
		  </tr>
		  <?php
		  	foreach($orden['OrdenCajaCobroCuota'] as $cuota):
		  ?>
			  <tr>
				<td><strong><?php echo $util->periodo($cuota['OrdenDescuentoCuota']['periodo'])?></strong></td>
				<td align="center"><?php echo $cuota['OrdenDescuentoCuota']['orden_descuento_id']?></td>
				<td nowrap="nowrap"><?php echo $cuota['OrdenDescuentoCuota']['tipo_orden_dto']?> #<?php echo $cuota['OrdenDescuentoCuota']['OrdenDescuento']['numero']?></td>
				<td nowrap="nowrap"><strong><?php echo $cuota['OrdenDescuentoCuota']['Proveedor']['razon_social_resumida']?></strong> - <?php echo $this->requestAction('/config/global_datos/valor/'.$cuota['OrdenDescuentoCuota']['tipo_producto'])?></td>
				<td align="center"><strong><?php echo $cuota['OrdenDescuentoCuota']['nro_cuota'].'/'. $cuota['OrdenDescuentoCuota']['OrdenDescuento']['cuotas']?></strong></td>
				<td><?php echo $this->requestAction('/config/global_datos/valor/'.$cuota['OrdenDescuentoCuota']['tipo_cuota'])?></td>
				<td align="center"><strong><?php echo $util->armaFecha($cuota['OrdenDescuentoCuota']['vencimiento'])?></strong></td>
				<td align="right"><?php echo number_format($cuota['importe_abonado'],2)?></td>
				<td align="right">
					<?php if($cuota['saldo_cuota'] != 0):?>
						<span style="color: red;"><strong><?php echo $util->nf($cuota['saldo_cuota'])?></strong></span>
					<?php else:?>
						<span style="color: green;"><strong><?php echo $util->nf($cuota['saldo_cuota'])?></strong></span>
					<?php endif;?>				
				</td>
			  </tr>
		  <?php endforeach;?>
		</table>
<!--	</div>	-->

		<?php echo $frm->hidden('OrdenCajaCobro.orden_caja_cobro_importe',array('value' => $orden['OrdenCajaCobro']['importe']))?>
		<?php echo $frm->hidden('OrdenCajaCobro.orden_caja_cobro_importe',array('value' => $orden['OrdenCajaCobro']['importe_cobrado']))?>
