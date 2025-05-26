<?php if(!empty($deuda)):?>
	<table>
		<tr>
			<th>PROVEEDOR</th>
			<th>PRODUCTO</th>
			<th>CONCEPTO</th>
			<th>ATRASO</th>
			<th>PERIODO</th>
			<th>TOTAL</th>
		</tr>
		<?php 
		$ACU_TOTAL = 0;
		$ACU_PERIODO = 0;
		$ACU_ATRASO = 0;
		?>
		<?php foreach($deuda as $liquidacion):
				$ACU_TOTAL += $liquidacion['Proveedor']['total'];
				$ACU_PERIODO += $liquidacion['Proveedor']['total_periodo'];
				$ACU_ATRASO += $liquidacion['Proveedor']['total_atraso'];
		?>
			<tr>
				<th colspan="6" style="font-size:13px;background-color: #e2e6ea;text-align: left;color: black;"><?php echo $liquidacion['Proveedor']['razon_social']?></th>
			</tr>
			<?php foreach($liquidacion['Proveedor']['liquidacion'] as $cuota):?>				
			<tr>
				<td></td>
				<td><?php echo $cuota['LiquidacionCuota']['tipo_producto_desc']?></td>
				<td><?php echo $cuota['LiquidacionCuota']['tipo_cuota_desc']?></td>
				<td align="right"><?php echo $util->nf($cuota['LiquidacionCuota']['total_atraso'])?></td>
				<td align="right"><?php echo $util->nf($cuota['LiquidacionCuota']['total_periodo'])?></td>
				<td align="right"><?php echo $util->nf($cuota['LiquidacionCuota']['total'])?></td>
			</tr>
			<?php endforeach;?>
			<tr>
				<td colspan="3" style="text-align: right;font-weight: bold;">SUB-TOTAL PROVEEDOR</td>
				<td style="text-align: right;border-top: 1px solid;font-weight: bold;"><?php echo $util->nf($liquidacion['Proveedor']['total_atraso'])?></td>
				<td style="text-align: right;border-top: 1px solid;font-weight: bold;"><?php echo $util->nf($liquidacion['Proveedor']['total_periodo'])?></td>
				<td style="text-align: right;border-top: 1px solid;font-weight: bold;"><?php echo $util->nf($liquidacion['Proveedor']['total'])?></td>
			</tr>			
		<?php endforeach;?>
		<tr>
			<th colspan="3" style="text-align: right;">TOTALES</th>
			<th style="text-align: right;"><?php echo $util->nf($ACU_ATRASO)?></th>
			<th style="text-align: right;"><?php echo $util->nf($ACU_PERIODO)?></th>
			<th style="text-align: right;"><?php echo $util->nf($ACU_TOTAL)?></th>
		</tr>
	</table>
	<?php //   DEBUG($deuda)?>
<?php endif;?>