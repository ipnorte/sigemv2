<h3>DETALLE DE ATRASO A <?php echo $util->periodo($periodo,true)?></h3>
<?php if(count($cuotas)!=0):?>
<table>
	<?php if($codigo_organismo != "0"):?>
		<tr>
			<td>ORGANISMO:</td>
			<td colspan="11"><strong><?php echo $util->globalDato($codigo_organismo)?></strong></td>
		</tr>
	<?php endif;?>

	<?php if(!empty($proveedor_razon_social)):?>
		<tr>
			<td>PROVEEDOR:</td>
			<td colspan="11"><strong><?php echo $proveedor_razon_social?></strong></td>
		</tr>
	<?php endif;?>
	
	<tr>
		<th>ORDEN</th>
		<th>TIPO / NUMERO</th>
		<th>COD - NRO</th>
		<th>PROVEEDOR / PRODUCTO</th>
		<th>CUOTA</th>
		<th>CONCEPTO</th>
		<th>VTO</th>
		<th>ESTADO</th>
		<th>SIT</th>
		<th colspan="2">IMPORTE</th>
		<th>PAGADO</th>
		<th>SALDO CUOTA</th>
	</tr>
	<?php foreach($cuotas as $periodo => $detalle):?>
	
		<tr>
			<th colspan="13" style="font-size:13px;background-color: #e2e6ea"><h4 style="text-align: left;color:#000000;"><?php echo $util->periodo($periodo,true)?></h4></th>
		</tr>
		
		<?php $ACU_IMPO_CUOTA = 0;?>
		<?php $ACU_PAGO_CUOTA = 0;?>
		<?php $ACU_SALDO_CUOTA = 0;?>
		<?php $ACU_SALDO_CUOTA_ACUM = $detalle['atraso'];?>
		
		<?php foreach($detalle['detalle_cuotas'] as $cuota):?>
			<?php //   debug($cuota)?>
		
			<?php $ACU_IMPO_CUOTA += $cuota['OrdenDescuentoCuota']['importe'];?>
			<?php $ACU_PAGO_CUOTA += $cuota['OrdenDescuentoCuota']['pagado'];?>
			<?php $ACU_SALDO_CUOTA += $cuota['OrdenDescuentoCuota']['saldo_cuota'];?>
			<?php $ACU_SALDO_CUOTA_ACUM += $cuota['OrdenDescuentoCuota']['saldo_cuota'];?>

			<tr class="<?php echo $cuota['OrdenDescuentoCuota']['estado']?>">
				<td align="center"><?php echo $controles->linkModalBox($cuota['OrdenDescuentoCuota']['orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cuota['OrdenDescuentoCuota']['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$cuota['OrdenDescuentoCuota']['orden_descuento_id'],'h' => 450, 'w' => 700))?></td>
				<td nowrap="nowrap"><?php echo $cuota['OrdenDescuentoCuota']['tipo_nro']?></td>
				<td align="center"><?php echo $cuota['OrdenDescuentoCuota']['codigo_comercio_referencia']?>&nbsp;-&nbsp;<?php echo $cuota['OrdenDescuentoCuota']['nro_orden_referencia']?></td>
				<td><?php echo $cuota['OrdenDescuentoCuota']['proveedor_producto']?></td>
				<td align="center"><?php echo $cuota['OrdenDescuentoCuota']['cuota']?></td>
				<td><?php echo $cuota['OrdenDescuentoCuota']['tipo_cuota_desc']?></td>
				<td align="center"><?php echo $util->armaFecha($cuota['OrdenDescuentoCuota']['vencimiento'])?></td>
				<td><?php echo $cuota['OrdenDescuentoCuota']['estado_desc']?></td>
				<td><?php echo $cuota['OrdenDescuentoCuota']['situacion_desc']?></td>
				<td align="right"><?php echo number_format($cuota['OrdenDescuentoCuota']['importe'],2)?></td>
				<td align="center"><?php echo ( $cuota['OrdenDescuentoCuota']['estado'] == 'MUTUESCUADEU' ? $controles->vencida($cuota['OrdenDescuentoCuota']['vencida']) : '')?></td>
				<td align="right"><?php echo number_format($cuota['OrdenDescuentoCuota']['pagado'],2)?></td>
				<td align="right"><?php echo ($cuota['OrdenDescuentoCuota']['saldo_cuota'] < 0 ? '<span style="color:red;">'.number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2).'</span>' : number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2)) ?></td>				
			</tr>				
		<?php endforeach;?>
		<tr>
			<td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" colspan="9" align="right"><strong>TOTAL PERIODO</strong></td>
			<td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_IMPO_CUOTA,2)?></strong></td>
			<td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
			<td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_PAGO_CUOTA,2)?></strong></td>
			<td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA,2)?></strong></td>
<!--			<td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>-->
		</tr>
		<tr>
			<td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" colspan="12" align="right"><strong>SALDO ACUMULADO A <?php echo $util->periodo($periodo,true)?></strong></td>
			<td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA_ACUM,2)?></strong></td>
<!--			<td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>-->
		</tr>		
	<?php endforeach;?>

</table>
<?php //   debug($cuotas)?>
<?php endif;?>