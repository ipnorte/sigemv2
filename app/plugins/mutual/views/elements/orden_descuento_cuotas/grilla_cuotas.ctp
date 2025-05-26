<?php 
$cuotas = $this->requestAction('/mutual/orden_descuento_cuotas/cuotas_by_odescuento/'.$orden_descuento_id.'/'.(isset($detallaPagos) ? $detallaPagos : 0));
// debug($cuotas);
// exit;
$ACU_IMPO_CUOTA = 0;
$ACU_PAGO_CUOTA = 0;
$ACU_SALDO_CUOTA = 0;
?>
<?php if(!empty($cuotas)):?>
	<table>
	
		<tr>
			<td colspan="10"></td>
			<td align="center"><?php echo $controles->botonGenerico('/mutual/orden_descuentos/impresion/'.$orden_descuento_id.'/PDF','controles/printer.png','',array('target' => 'blank'))?></td>
			<td align="center"><?php echo $controles->botonGenerico('/mutual/orden_descuentos/impresion/'.$orden_descuento_id.'/XLS','controles/ms_excel.png','',array('target' => 'blank'))?></td>
			</td>
		</tr>
	
		
		<tr>
			<th>CUOTA</th>
			<th>PERIODO</th>
			<th>CONCEPTO</th>
			<th>ESTADO</th>
			<th>SIT</th>
			<th colspan="2">IMPORTE</th>
			<th>PAGADO</th>
			<th>F.UL.PAG.</th>
			<th>SALDO</th>
			<th>ACUM</th>
			<th></th>
			
		</tr>	
		
		<?php
			$ACUM = 0;
                        $nCuoPerm = 0;
			foreach($cuotas as $cuota):
				$ACUM += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
				$ACU_IMPO_CUOTA += $cuota['OrdenDescuentoCuota']['importe'];
				$ACU_PAGO_CUOTA += $cuota['OrdenDescuentoCuota']['pagado'];
				$ACU_SALDO_CUOTA += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
                                $nCuoPerm++;
		?>
			<tr class="<?php echo $cuota['OrdenDescuentoCuota']['estado']?>">
				<td align="center">
                                    <strong>
                                    <?php if($cuota['OrdenDescuentoCuota']['orden_descuento_permanente']):?>
                                    <?php echo$nCuoPerm ."/" . count($cuotas)?>    
                                    <?php else:?>    
                                    <?php // echo $cuota['OrdenDescuentoCuota']['nro_cuota'].'/'. $cuota['OrdenDescuento']['cuotas']?>
                                    <?php echo $cuota['OrdenDescuentoCuota']['cuota']?>
                                    <?php endif;?>    
                                    </strong>
                                </td>
				<td><strong><?php echo $util->periodo($cuota['OrdenDescuentoCuota']['periodo'])?></strong></td>
<!--				<td align="center"><strong><?php //   echo $util->armaFecha(( $cuota['OrdenDescuentoCuota']['estado'] != 'P' ? $cuota['OrdenDescuentoCuota']['vencimiento'] : $cuota['OrdenDescuentoCuota']['fecha_ultimo_pago']))?></strong></td>-->
                <td><?php echo $cuota['OrdenDescuentoCuota']['tipo_cuota_desc']?></td>
				<td><?php echo $cuota['OrdenDescuentoCuota']['estado_desc']?></td>
				<td><?php echo $cuota['OrdenDescuentoCuota']['situacion_desc']?></td>
				<td align="right"><?php echo ($cuota['OrdenDescuentoCuota']['importe'] < 0 ? '<span style="color:red;">'.number_format($cuota['OrdenDescuentoCuota']['importe'],2).'</span>' : number_format($cuota['OrdenDescuentoCuota']['importe'],2)) ?></td>
				<td align="center"><?php echo ( $cuota['OrdenDescuentoCuota']['estado'] == 'A' ? $controles->vencida($cuota['OrdenDescuentoCuota']['vencida']) : '')?></td>
				<td align="right"><?php echo number_format($cuota['OrdenDescuentoCuota']['pagado'],2)?></td>
				<td align="center"><?php echo $util->armaFecha($cuota['OrdenDescuentoCuota']['fecha_ultimo_pago'])?></td>
				<td align="right" style="font-weight: bold;"><?php echo ($cuota['OrdenDescuentoCuota']['saldo_cuota'] < 0 ? '<span style="color:red;">'.number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2).'</span>' : number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2)) ?></td>
				<td align="right"><strong><?php echo number_format($ACUM,2)?></strong></td>
				<td align="center"><?php echo $controles->btnModalBox(array('title' => 'DETALLE CUOTA','url' => '/mutual/orden_descuento_cuotas/view/'.$cuota['OrdenDescuentoCuota']['id'],'h' => 450, 'w' => 750))?></td>
				
			</tr>
			<?php if(!empty($cuota['OrdenDescuentoCuota']['cobros'])):?>
				<?php foreach($cuota['OrdenDescuentoCuota']['cobros'] as $cobro):?>
				<tr class="info_pago">
					<td colspan="3"></td>
					<td align="center"><?php echo $util->armaFecha($cobro['OrdenDescuentoCobroCuota']['fecha_cobro'])?></td>
					<td><?php echo $util->periodo($cobro['OrdenDescuentoCobroCuota']['periodo_cobro'])?></td>
					<td colspan="4"><?php echo $cobro['OrdenDescuentoCobroCuota']['tipo_cobro_desc']?></td>
					<td align="right"><?php echo $util->nf($cobro['OrdenDescuentoCobroCuota']['importe'])?></td>
					<td colspan="3">
						<?php if($cobro['OrdenDescuentoCobroCuota']['cancelacion_orden_id'] != 0):?>
							<?php echo $controles->linkModalBox('ORD.CANC. #'.$cobro['OrdenDescuentoCobroCuota']['cancelacion_orden_id'],array('title' => 'DETALLE ORDEN DE CANCELACION','url' => '/mutual/cancelacion_ordenes/vista_detalle/'.$cobro['OrdenDescuentoCobroCuota']['cancelacion_orden_id'],'h' => 450, 'w' => 750))?>
						<?php endif;?>
						<?php if($cobro['OrdenDescuentoCobroCuota']['reversado'] == 1):?>
							<span style='background-color:red;color:white;padding:2px;font-weight:bold;'>R</span>
						<?php endif;?>
					</td>
					
				</tr>
				<?php endforeach;?>			
			<?php endif;?>
			
		<?php endforeach;?>

		<tr>
			<th colspan="4" style="text-align: right;">TOTALES </th>
			<th></th>
			<th style="text-align: right;"><?php echo number_format($ACU_IMPO_CUOTA,2)?></th>
			<th></th>
			<th style="text-align: right;"><?php echo number_format($ACU_PAGO_CUOTA,2)?></th>
			<th></th>
			<th style="text-align: right;"><?php echo number_format($ACU_SALDO_CUOTA,2)?></th>
			<th></th>
			<th></th>
			
		</tr>
	</table>
	
<?php endif;?>	