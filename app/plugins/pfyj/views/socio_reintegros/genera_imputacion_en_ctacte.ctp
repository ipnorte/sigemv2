<?php if(!empty($imputacion)):?>
<h3>DETALLE DE CUOTAS A IMPUTAR</h3>
	<table>
		<tr>
			<th>PERIODO</th>
			<th>ORDEN</th>
			<th>TIPO / NUMERO</th>
			<th>PROVEEDOR / PRODUCTO</th>
			<th>CUOTA</th>
			<th>CONCEPTO</th>
			<th>SALDO ACTUAL</th>
			<th>IMPUTA</th>
			<th>SALDO</th>
		</tr>
		<?php 
		$ACU_TOTAL_CUOTA = 0;
		$ACU_TOTAL_IMPUTA = 0;
		$ACU_TOTAL_SALDO = 0;
		?>
		<?php foreach($imputacion['cuotas'] as $cuota):?>
			<?php 
//			debug($cuota['OrdenDescuentoCuota']);
			$ACU_TOTAL_CUOTA += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
			$ACU_TOTAL_IMPUTA += $cuota['OrdenDescuentoCuota']['importe_aimputar'];
			$ACU_TOTAL_SALDO += $cuota['OrdenDescuentoCuota']['n_saldocuota'];
			?>
			<tr>
			
				<td><strong><?php echo $util->periodo($cuota['OrdenDescuentoCuota']['periodo'])?></strong></td>
				<td align="center"><?php echo $cuota['OrdenDescuentoCuota']['orden_descuento_id']?></td>
				<td nowrap="nowrap"><?php echo $cuota['OrdenDescuentoCuota']['tipo_nro']?></td>
				<td nowrap="nowrap"><?php echo $cuota['OrdenDescuentoCuota']['proveedor_producto']?></td>
				<td align="center"><?php echo $cuota['OrdenDescuentoCuota']['cuota']?></td>
				<td><?php echo $cuota['OrdenDescuentoCuota']['tipo_cuota_desc']?></td>
				<td align="right"><?php echo $util->nf($cuota['OrdenDescuentoCuota']['saldo_cuota'])?></td>
				<td align="right"><strong><?php echo $util->nf($cuota['OrdenDescuentoCuota']['importe_aimputar'])?></strong></td>
				<td align="right">
					<?php if($cuota['OrdenDescuentoCuota']['n_saldocuota'] != 0):?>
						<span style="color: red;"><strong><?php echo $util->nf($cuota['OrdenDescuentoCuota']['n_saldocuota'])?></strong></span>
					<?php else:?>
						<span style="color: green;"><strong><?php echo $util->nf($cuota['OrdenDescuentoCuota']['n_saldocuota'])?></strong></span>
					<?php endif;?>
				</td>			
			
			</tr>
		<?php endforeach;?>
		
		<tr>
			<th colspan="6" style="text-align: right;">TOTALES</th>
			<th style="text-align: right;"><?php echo $util->nf($ACU_TOTAL_CUOTA)?></th>
			<th style="text-align: right;"><?php echo $util->nf($ACU_TOTAL_IMPUTA)?></th>
			<th style="text-align: right;"><?php echo $util->nf($ACU_TOTAL_SALDO)?></th>
		</tr>		
		
	</table>
	<?php foreach($this->data['SocioReintegro']['id'] as $id => $socioReintegro):?>
		<input type="hidden" name="data[SocioReintegro][id][<?php echo $id?>]" value="<?php echo $socioReintegro?>"/>
	<?php endforeach;?>
	<?php echo $frm->hidden('SocioReintegro.importe_imputa',array('value' => $ACU_TOTAL_IMPUTA))?>
<?php endif;?>


