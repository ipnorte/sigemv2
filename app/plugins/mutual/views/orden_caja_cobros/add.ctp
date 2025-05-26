<h4>ORDEN DE COBRO POR CAJA GENERADA CORRECTAMENTE!</h4>
<div class="row">
	COMPROBANTE: <strong>#<?php echo $orden['OrdenCajaCobro']['id']?></strong>
</div>
<div class="row">
	VENCIMIENTO: <strong><?php echo $util->armaFecha($orden['OrdenCajaCobro']['fecha_vto'])?></strong>
</div>
<div class="row">
	IMPORTE SELECCIONADO: <strong><?php echo number_format($orden['OrdenCajaCobro']['importe'],2)?></strong>
	&nbsp;|&nbsp;IMPORTE ABONADO: <strong><?php echo number_format($orden['OrdenCajaCobro']['importe_cobrado'],2)?></strong>
	&nbsp;
	<?php if($orden['OrdenCajaCobro']['tipo_imputacion']!= 0):?>
		(se imputa el IMPORTE ABONADO de la cuota m&aacute;s vieja a la m&aacute;s nueva.)
	<?php endif;?>
</div>
<br/>
<hr/>
<div class="areaDatoForm">
<h3>DETALLE DE CUOTAS A PAGAR E IMPUTACION</h3>
	<table>
		<tr>
			<th>PERIODO</th>
			<th>ORDEN</th>
			<th>TIPO / NUMERO</th>
			<th>PROVEEDOR / PRODUCTO</th>
			<th>CUOTA</th>
			<th>CONCEPTO</th>
			<th>SELECCIONADO</th>
			<th>ABONADO</th>
			<th>SALDO</th>
		</tr>
		<?php 
		$ACU_TOTAL_CUOTA = 0;
		$ACU_TOTAL_ENTREGA = 0;
		$ACU_TOTAL_SALDO = 0;
		?>
		<?php foreach($orden['OrdenCajaCobroCuota'] as $cuota):?>
			<?php 
			$ACU_TOTAL_CUOTA += $cuota['OrdenDescuentoCuota']['importe'];
			$ACU_TOTAL_ENTREGA += $cuota['importe_abonado'];
			$ACU_TOTAL_SALDO += $cuota['saldo_cuota'];
			?>
			<tr>
			
				<td><strong><?php echo $util->periodo($cuota['OrdenDescuentoCuota']['periodo'])?></strong></td>
				<td align="center"><?php echo $cuota['OrdenDescuentoCuota']['orden_descuento_id']?></td>
				<td nowrap="nowrap"><?php echo $cuota['OrdenDescuentoCuota']['tipo_nro']?></td>
				<td nowrap="nowrap"><?php echo $cuota['OrdenDescuentoCuota']['proveedor_producto']?></td>
				<td align="center"><?php echo $cuota['OrdenDescuentoCuota']['cuota_de_cuotas']?></td>
				<td><?php echo $cuota['OrdenDescuentoCuota']['tipo_cuota_desc']?></td>
				<td align="right"><?php echo $util->nf($cuota['OrdenDescuentoCuota']['importe'])?></td>
				<td align="right"><strong><?php echo $util->nf($cuota['importe_abonado'])?></strong></td>
				<td align="right">
					<?php if($cuota['saldo_cuota'] != 0):?>
						<span style="color: red;"><strong><?php echo $util->nf($cuota['saldo_cuota'])?></strong></span>
					<?php else:?>
						<span style="color: green;"><strong><?php echo $util->nf($cuota['saldo_cuota'])?></strong></span>
					<?php endif;?>
				</td>			
			
			</tr>
		<?php endforeach;?>
		
		<tr>
			<th colspan="6" style="text-align: right;">TOTALES</th>
			<th style="text-align: right;"><?php echo $util->nf($ACU_TOTAL_CUOTA)?></th>
			<th style="text-align: right;"><?php echo $util->nf($ACU_TOTAL_ENTREGA)?></th>
			<th style="text-align: right;"><?php echo $util->nf($ACU_TOTAL_SALDO)?></th>
		</tr>		
		
	</table>
</div>	
<hr/>
<div class="row">
	<?php echo $frm->btnForm(array('URL'=>'/mutual/orden_descuento_cobros/add_recibo/'.$orden['OrdenCajaCobro']['id'],'LABEL' => 'RECAUDAR ESTA ORDEN'))?>
</div>
<?php //   DEBUG($orden)?>

