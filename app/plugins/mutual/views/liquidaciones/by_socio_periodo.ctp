<?php echo $this->renderElement('socios/apenom',array('socio_id' => $socio_id, 'plugin' => 'pfyj'))?>
<h3>DETALLE DE LIQUIDACION DE <?php echo $util->periodo($periodo,true)?></h3>
<hr/>
<h3>RESUMEN</h3>
	<table>
		<tr>
			<th>FECHA LIQUIDACION</th>
			<th>ESTADO</th>
			<th>ORGANISMO</th>
			<th>IDENTIFICACION</th>
			<th>A DEBITAR</th>
			<th>DEBITADO</th>
			<th>STATUS</th>
			<th>IMPUTADO</th>
			<th>IMPUTADA</th>
		</tr>
		<?php $TOTAL = 0;?>
		<?php $TOTAL_DEBITO = 0;?>
		<?php $TOTAL_IMPUTADO = 0;?>
		<?php foreach($liquidacion as $liquidado):?>
			<?php //   debug($liquidado)?>
			<?php $TOTAL += $liquidado['LiquidacionSocio']['importe_dto'];?>
			<?php $TOTAL_DEBITO += $liquidado['LiquidacionSocio']['importe_debitado'];?>
			<?php $TOTAL_IMPUTADO += $liquidado['LiquidacionSocio']['importe_imputado'];?>
			<tr>
				<td style="<?php echo ($liquidado['LiquidacionSocio']['indica_pago'] == 0 ? "background-color: #FBEAEA;" : "background-color: #F2FEE9;")?>"><?php echo $liquidado['Liquidacion']['created']?></td>
				<td style="<?php echo ($liquidado['LiquidacionSocio']['indica_pago'] == 0 ? "background-color: #FBEAEA;" : "background-color: #F2FEE9;")?>"><?php echo ($liquidado['Liquidacion']['cerrada'] == 0 ? 'ABIERTA' : 'CERRADA')?></td>
				<td style="<?php echo ($liquidado['LiquidacionSocio']['indica_pago'] == 0 ? "background-color: #FBEAEA;" : "background-color: #F2FEE9;")?>"><?php echo $util->globalDato($liquidado['LiquidacionSocio']['codigo_organismo'])?></td>
				<td style="<?php echo ($liquidado['LiquidacionSocio']['indica_pago'] == 0 ? "background-color: #FBEAEA;" : "background-color: #F2FEE9;")?>"><?php echo $liquidado['LiquidacionSocio']['beneficio_str']?></td>
				<td style="<?php echo ($liquidado['LiquidacionSocio']['indica_pago'] == 0 ? "background-color: #FBEAEA;" : "background-color: #F2FEE9;")?>" align="right"><?php echo $util->nf($liquidado['LiquidacionSocio']['importe_dto'])?></td>
				<td style="<?php echo ($liquidado['LiquidacionSocio']['indica_pago'] == 0 ? "background-color: #FBEAEA;" : "background-color: #F2FEE9;")?>" align="right"><?php echo $util->nf($liquidado['LiquidacionSocio']['importe_debitado'])?></td>
				<td style="<?php echo ($liquidado['LiquidacionSocio']['indica_pago'] == 0 ? "background-color: #FBEAEA;" : "background-color: #F2FEE9;")?>" align="center"><?php echo $liquidado['LiquidacionSocio']['status']?></td>
				<td style="<?php echo ($liquidado['LiquidacionSocio']['indica_pago'] == 0 ? "background-color: #FBEAEA;" : "background-color: #F2FEE9;")?>" align="right"><?php echo $util->nf($liquidado['LiquidacionSocio']['importe_imputado'])?></td>
				<td style="<?php echo ($liquidado['LiquidacionSocio']['indica_pago'] == 0 ? "background-color: #FBEAEA;" : "background-color: #F2FEE9;")?>" align="center"><?php echo $controles->onOff($liquidado['LiquidacionSocio']['imputada'])?></td>
			</tr>
		<?php endforeach;?>
		<tr>
			<th colspan="4" style="text-align: right;">TOTAL</th>
			<th style="text-align: right;"><?php echo $util->nf($TOTAL)?></th>
			<th style="text-align: right;"><?php echo $util->nf($TOTAL_DEBITO)?></th>
			<th></th>
			<th style="text-align: right;"><?php echo $util->nf($TOTAL_IMPUTADO)?></th>
			<th></th>
		</tr>		
	</table>
<h3>DETALLE DE CUOTAS INCLUIDAS EN LA LIQUIDACION</h3>
	<table>
		<tr>
			<th>ORD.DTO.</th>
			<th>TIPO / NUMERO</th>
			<th>COD - NRO</th>
			<th>PROVEEDOR / PRODUCTO</th>
			<th>PERIODO</th>
			<th>CONCEPTO</th>
			<th>A DEBITAR</th>
			<th>IMPUTADO</th>
		</tr>
		<?php $TOTAL = 0;?>
		<?php $TOTAL_DEBITO = 0;?>
		<?php foreach($cuotas as $cuota):?>
			<?php $TOTAL += $cuota['LiquidacionCuota']['importe'];?>
			<?php $TOTAL_DEBITO += $cuota['LiquidacionCuota']['importe_debitado'];?>
			<tr>
				<td style="<?php echo ($cuota['LiquidacionCuota']['periodo_cuota'] != $periodo ? "background-color: #FBEAEA;" : "")?>"><?php echo $cuota['OrdenDescuento']['id']?></td>
				<td style="<?php echo ($cuota['LiquidacionCuota']['periodo_cuota'] != $periodo ? "background-color: #FBEAEA;" : "")?>"><?php echo $cuota['OrdenDescuento']['tipo_orden_dto']?> #<?php echo $cuota['OrdenDescuento']['numero']?></td>
				<td style="<?php echo ($cuota['LiquidacionCuota']['periodo_cuota'] != $periodo ? "background-color: #FBEAEA;" : "")?>"><?php echo $cuota['OrdenDescuentoCuota']['codigo_comercio_referencia']?>&nbsp;-&nbsp;<?php echo $cuota['OrdenDescuentoCuota']['nro_orden_referencia']?></td>
				<td style="<?php echo ($cuota['LiquidacionCuota']['periodo_cuota'] != $periodo ? "background-color: #FBEAEA;" : "")?>"><?php echo $cuota['Proveedor']['razon_social_resumida'].' / '.$util->globalDato($cuota['LiquidacionCuota']['tipo_producto'])?></td>
				<td style="<?php echo ($cuota['LiquidacionCuota']['periodo_cuota'] != $periodo ? "background-color: #FBEAEA;" : "")?>"><?php echo $util->periodo($cuota['LiquidacionCuota']['periodo_cuota'])?></td>
				<td style="<?php echo ($cuota['LiquidacionCuota']['periodo_cuota'] != $periodo ? "background-color: #FBEAEA;" : "")?>"><?php echo $util->globalDato($cuota['LiquidacionCuota']['tipo_cuota'])?></td>
				<td style="<?php echo ($cuota['LiquidacionCuota']['periodo_cuota'] != $periodo ? "background-color: #FBEAEA;" : "")?>" align="right"><?php echo $util->nf($cuota['LiquidacionCuota']['importe'])?><?php echo ($cuota['LiquidacionCuota']['mutual_adicional_pendiente_id'] != 0 ? ' (*)':'')?></td>
				<td style="<?php echo ($cuota['LiquidacionCuota']['periodo_cuota'] != $periodo ? "background-color: #FBEAEA;" : "")?>" align="right"><?php echo $util->nf($cuota['LiquidacionCuota']['importe_debitado'])?></td>
			</tr>
		<?php //   debug($cuota)?>
		<?php endforeach;?>
		<tr>
			<th colspan="6" style="text-align: right;">TOTAL</th>
			<th style="text-align: right;"><?php echo $util->nf($TOTAL)?></th>
			<th style="text-align: right;"><?php echo $util->nf($TOTAL_DEBITO)?></th>
		</tr>
		<?php if(!empty($pendientes)):?>
			<tr>
				<td colspan="8"><h4>(*) CUOTAS ADICIONALES </h4></td>
			</tr>
			<tr>
				<td colspan="8">
					<table>
						<tr>
							<th class="subtabla">ORD.DTO.</th>
							<th class="subtabla">TIPO / NUMERO</th>
							<th class="subtabla">COD - NRO</th>
							<th class="subtabla">PROVEEDOR / PRODUCTO</th>
							<th class="subtabla">PERIODO</th>
							<th class="subtabla">CONCEPTO</th>
							<th class="subtabla">CALCULO</th>
							<th class="subtabla">IMPORTE</th>
						</tr>
						<?php $TOTAL = 0;?>
						<?php $TOTAL_DEBITO = 0;?>
						<?php foreach($pendientes as $cuota):?>
							<?php $TOTAL += $cuota['LiquidacionCuota']['importe'];?>
							<?php $TOTAL_DEBITO += $cuota['LiquidacionCuota']['importe_debitado'];?>
							<tr>
								<td style="<?php echo ($cuota['LiquidacionCuota']['periodo_cuota'] != $periodo ? "background-color: #FBEAEA;" : "")?>"><?php echo $cuota['OrdenDescuento']['id']?></td>
								<td style="<?php echo ($cuota['LiquidacionCuota']['periodo_cuota'] != $periodo ? "background-color: #FBEAEA;" : "")?>"><?php echo $cuota['OrdenDescuento']['tipo_orden_dto']?> #<?php echo $cuota['OrdenDescuento']['numero']?></td>
								<td style="<?php echo ($cuota['LiquidacionCuota']['periodo_cuota'] != $periodo ? "background-color: #FBEAEA;" : "")?>"><?php echo $cuota['OrdenDescuento']['codigo_comercio_referencia']?>&nbsp;-&nbsp;<?php echo $cuota['OrdenDescuento']['nro_orden_referencia']?></td>
								<td style="<?php echo ($cuota['LiquidacionCuota']['periodo_cuota'] != $periodo ? "background-color: #FBEAEA;" : "")?>"><?php echo $cuota['Proveedor']['razon_social_resumida'].' / '.$util->globalDato($cuota['LiquidacionCuota']['tipo_producto'])?></td>
								<td style="<?php echo ($cuota['LiquidacionCuota']['periodo_cuota'] != $periodo ? "background-color: #FBEAEA;" : "")?>"><?php echo $util->periodo($cuota['LiquidacionCuota']['periodo_cuota'])?></td>
								<td style="<?php echo ($cuota['LiquidacionCuota']['periodo_cuota'] != $periodo ? "background-color: #FBEAEA;" : "")?>"><?php echo $util->globalDato($cuota['LiquidacionCuota']['tipo_cuota'])?></td>
								<td align="center">
									<?php if($cuota['MutualAdicionalPendiente']['deuda_calcula'] == 1):?>
										s/DEUDA TOTAL<br/>
									<?php elseif($cuota['MutualAdicionalPendiente']['deuda_calcula'] == 2):?>	
										s/DEUDA VENCIDA<br/>
									<?php elseif($cuota['MutualAdicionalPendiente']['deuda_calcula'] == 3):?>	
										s/DEUDA PERIODO<br/>						
									<?php endif;?>
									<?php if($cuota['MutualAdicionalPendiente']['tipo'] == 'P'):?>
										<?php echo $cuota['MutualAdicionalPendiente']['total_deuda']?> x <?php echo $cuota['MutualAdicionalPendiente']['valor']?>% = <?php echo $cuota['MutualAdicionalPendiente']['importe']?>
									<?php else:?>
										Fijo = <?php echo $cuota['MutualAdicionalPendiente']['valor']?>	
									<?php endif;?>
								</td>
								<td style="<?php echo ($cuota['LiquidacionCuota']['periodo_cuota'] != $periodo ? "background-color: #FBEAEA;" : "")?>" align="right"><?php echo $util->nf($cuota['LiquidacionCuota']['importe'])?></td>
							</tr>
						<?php endforeach;?>
						<tr>
							<th colspan="7" style="text-align: right;">TOTAL</th>
							<th style="text-align: right;"><?php echo $util->nf($TOTAL)?></th>
						</tr>
					</table>				
				</td>
			</tr>			
		<?php endif;?>
	</table>
