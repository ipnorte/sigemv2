<?php if(!empty($cobro)):?>
	<h3>DETALLE DE LA ORDEN DE COBRO #<?php echo $cobro['OrdenDescuentoCobro']['id'] ." ". ($cobro['OrdenDescuentoCobro']['anulado']==1 ? " *** ANULADO *** " : "")?></h3>
	<?php if($cobro['OrdenDescuentoCobro']['socio_id'] != 0):?>
	<?php echo $this->renderElement('socios/apenom',array('socio_id' => $cobro['OrdenDescuentoCobro']['socio_id'], 'plugin' => 'pfyj'))?>
	<?php endif;?>
	
	<div class="areaDatoForm3">
		<?php if($cobro['OrdenDescuentoCobro']['anulado']==1):?>
			<span style="color:red;font-weight: bold;"> *** ANULADO ***</span>
		<?php endif;?>
		<table>
			<tr>
				<th>#</th>
				<th>TIPO COBRO</th>
				<th>FECHA PAGO</th>
				<th>PERIODO PAGO</th>
				<th>RECIBO Nro</th>
				<th>TOTAL</th>
				<th>USUARIO</th>
			</tr>
			<tr>
				<td align="center"><?php echo $cobro['OrdenDescuentoCobro']['id']?></td>
				<td nowrap="nowrap"><?php echo $cobro['OrdenDescuentoCobro']['tipo_cobro_desc']?></td>
				<td align="center"><?php echo $util->armaFecha($cobro['OrdenDescuentoCobro']['fecha'])?></td>
				<td align="center"><?php echo (!empty($cobro['OrdenDescuentoCobro']['periodo_cobro']) ? $util->periodo($cobro['OrdenDescuentoCobro']['periodo_cobro']) : 'S/D')?></td>
				<td><?php echo $cobro['OrdenDescuentoCobro']['nro_recibo']?></td>
				<td align="right"><strong><?php echo number_format($cobro['OrdenDescuentoCobro']['importe'],2)?></strong></td>
				<td align="center"><?php echo $cobro['OrdenDescuentoCobro']['user_created']?> - <?php echo $cobro['OrdenDescuentoCobro']['created']?></td>
			</tr>
		</table>
		<h4>DETALLE DE CUOTAS COBRADAS</h4>
		<table>
			<tr>
				<th>PERIODO</th>
				<th>ORD.DTO.</th>
				<th>TIPO / NUMERO</th>
				<th>PROVEEDOR / PRODUCTO</th>
				<th>CUOTA</th>
				<th>CONCEPTO</th>
				<th>COBRADO</th>
<!--				<th>SALDO</th>-->
				<th></th>
			</tr>
			<?php $ACU_TOTAL_CUOTA = 0;?>
			<?php foreach($cobro['OrdenDescuentoCobroCuota'] as $cuota):?>
				<?php $saldoCuota = $cuota['OrdenDescuentoCuota']['importe'] - $cuota['importe'];?>
				<?php $ACU_TOTAL_CUOTA += $cuota['importe'];?>
				<tr class="<?php echo ($cuota['reversado'] == 1 ? "activo_0" : "")?>">
					<td><?php echo $util->periodo($cuota['OrdenDescuentoCuota']['periodo'])?></td>
					<td align="center"><?php echo $controles->linkModalBox($cuota['OrdenDescuentoCuota']['orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cuota['OrdenDescuentoCuota']['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$cuota['OrdenDescuentoCuota']['orden_descuento_id'].'/'.$cuota['OrdenDescuentoCuota']['socio_id'],'h' => 450, 'w' => 750))?></td>
					<td><?php echo $cuota['OrdenDescuentoCuota']['tipo_nro']?></td>
					<td><?php echo $cuota['OrdenDescuentoCuota']['proveedor_producto']?></td>
					<td align="center"><?php echo $cuota['OrdenDescuentoCuota']['cuota']?></td>
					<td><?php echo $cuota['OrdenDescuentoCuota']['tipo_cuota_desc']?></td>
					<td align="right"><?php echo $util->nf($cuota['importe'])?></td>
					<td align="center"><?php echo ($cuota['reversado'] == 1 ? "<span style='background-color:red;color:white;padding:2px;font-weight:bold;'>R</span>":"")?></td>
				</tr>
			<?php endforeach;?>	
			<tr>
				<th colspan="6" class="totales">TOTAL COBRADO</th>
				<th class="totales"><?php echo $util->nf($ACU_TOTAL_CUOTA)?></th>
<!--				<th class="totales"></th>-->
				<th class="totales"></th>
			</tr>
		</table>
	</div>
<?php endif;?>	