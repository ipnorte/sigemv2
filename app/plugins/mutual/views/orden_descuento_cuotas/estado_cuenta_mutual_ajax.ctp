<?php if(count($cuotas)!=0):?>
	
	<table style="width: 100%;" class="tbl_grilla">
	
		<tr>
			<?php if(!empty($proveedor_razon_social)):?>
				<td>PROVEEDOR:</td>
				<td colspan="4"><strong><?php echo $proveedor_razon_social?></strong></td>
			<?php else:?>
				<td colspan="5"></td>
			<?php endif;?>
			<td colspan="10" align="right"><?php echo $controles->botonGenerico('/mutual/orden_descuento_cuotas/estado_cuenta_pdf/'.$socio['Socio']['id'].'/'.$periodo_d.'/'.$periodo_h."/".($solo_deuda ? 1 : 0).'/'.$proveedor_id.'/'.$codigo_organismo.'/'.($discrimina_pagos ? 1 : 0),'controles/pdf.png','IMPRIMIR',array('target' => 'blank'))?></td>
		</tr>	
		
		<tr>
			<th>ORD.DTO.</th>
			<th>ORGANISMO</th>
			<th>TIPO / NUMERO</th>
			<th>COD - NRO</th>
			<th>PROVEEDOR / PRODUCTO</th>
			<th>CUOTA</th>
			<th>CONCEPTO</th>
			<th>VTO / PAGO</th>
			<th>ESTADO</th>
			<th>SIT</th>
			<th colspan="2">IMPORTE</th>
			<th>PAGADO</th>
			<th>SALDO CUOTA</th>
			<th></th>
			
		</tr>
		<?php foreach($cuotas as $periodo => $detalle):?>
		
			<tr>
				<th colspan="15" style="font-size:13px;background-color: #e2e6ea;border:0"><h4 style="text-align: left;color:#000000;"><?php echo $util->periodo($periodo,true)?></h4></th>
			</tr>
			<?php if($detalle['atraso'] != 0):?>
				<tr>
					<td style="border-bottom: 1px solid #D8DBD4;color:red;" colspan="13" align="right"><strong>SALDO ANTERIOR</strong></td>
					<td style="border-bottom: 1px solid #D8DBD4;color:red;background-color: #FBEAEA;" align="right"><strong><?php echo number_format($detalle['atraso'],2)?></strong></td>
					<td style="border-bottom: 1px solid #D8DBD4;color:red;" align="right"><?php echo $controles->btnModalBox(array('title' => 'ATRASO A '.$util->periodo($periodo,true),'url' => '/mutual/orden_descuento_cuotas/ver_atraso/'.$socio['Socio']['id'].'/'.$periodo.'/'.$proveedor_id.'/'.$codigo_organismo,'h' => 500, 'w' => 950))?></td>
				</tr>				
							
			<?php endif;?>
			<?php $ACU_IMPO_CUOTA = 0;?>
			<?php $ACU_PAGO_CUOTA = 0;?>
			<?php $ACU_SALDO_CUOTA = 0;?>
			<?php $ACU_SALDO_CUOTA_ACUM = $detalle['atraso'];?>
			<?php foreach($detalle['detalle_cuotas'] as $cuota):?>
				<?php //   debug($cuota)?>
				<?php $ACU_IMPO_CUOTA += ($cuota['OrdenDescuentoCuota']['estado'] != 'B' ? $cuota['OrdenDescuentoCuota']['importe'] : 0);?>
				<?php $ACU_PAGO_CUOTA += ($cuota['OrdenDescuentoCuota']['estado'] != 'B' ? $cuota['OrdenDescuentoCuota']['pagado'] : 0);?>
				<?php $ACU_SALDO_CUOTA += $cuota['OrdenDescuentoCuota']['saldo_cuota'];?>
				<?php $ACU_SALDO_CUOTA_ACUM += $cuota['OrdenDescuentoCuota']['saldo_cuota'];?>
			
				<tr class="<?php echo $cuota['OrdenDescuentoCuota']['estado']?>">
					<td align="center"><?php echo $controles->linkModalBox($cuota['OrdenDescuentoCuota']['orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cuota['OrdenDescuentoCuota']['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$cuota['OrdenDescuentoCuota']['orden_descuento_id'].'/'.$cuota['OrdenDescuentoCuota']['socio_id'],'h' => 450, 'w' => 750))?></td>
					<td><?php echo $cuota['OrdenDescuentoCuota']['organismo']?></td>
					<td nowrap="nowrap">
						<?php
							switch ($cuota['OrdenDescuentoCuota']['tipo_orden_dto']){
								case 'OCOMP':
									echo $controles->linkModalBox($cuota['OrdenDescuentoCuota']['tipo_nro'],array('title' => 'ORDEN DE CONSUMO / SERVICIO #' . $cuota['OrdenDescuentoCuota']['numero_odto'],'url' => '/mutual/mutual_producto_solicitudes/view/'.$cuota['OrdenDescuentoCuota']['numero_odto'],'h' => 450, 'w' => 850));
									break;								
								case 'CONVE':
									echo $controles->linkModalBox($cuota['OrdenDescuentoCuota']['tipo_nro'],array('title' => 'CONVENIO DE PAGO #' . $cuota['OrdenDescuentoCuota']['numero_odto'],'url' => '/pfyj/socio_convenios/view/'.$cuota['OrdenDescuentoCuota']['numero_odto'],'h' => 450, 'w' => 850));
									break;
								case Configure::read('APLICACION.tipo_orden_dto_credito'):
									echo $controles->linkModalBox($cuota['OrdenDescuentoCuota']['tipo_nro'],array('title' => 'SOLICITUD DE CREDITO #' . $cuota['OrdenDescuentoCuota']['numero_odto'],'url' => '/mutual/mutual_producto_solicitudes/view/'.$cuota['OrdenDescuentoCuota']['numero_odto'],'h' => 450, 'w' => 850));
									break;									
								default:
									echo $cuota['OrdenDescuentoCuota']['tipo_nro'];
									break;	
							}
							
//							if($cuota['OrdenDescuentoCuota']['tipo_orden_dto'] != 'CONVE')echo $tipoAndNro;
//							else echo $controles->linkModalBox($tipoAndNro,array('title' => 'CONVENIO DE PAGO #' . $cuota['OrdenDescuento']['numero'],'url' => '/pfyj/socio_convenios/view/'.$cuota['OrdenDescuento']['numero'],'h' => 450, 'w' => 850))
						?>
					</td>
					<td align="center"><?php echo $cuota['OrdenDescuentoCuota']['codigo_comercio_referencia']?>&nbsp;-&nbsp;<?php echo $cuota['OrdenDescuentoCuota']['nro_orden_referencia']?></td>
					<td><?php echo $cuota['OrdenDescuentoCuota']['proveedor_producto']?></td>
					<td align="center"><?php echo ($cuota['OrdenDescuentoCuota']['tipo_orden_dto'] != 'MUTUTPROCFIJ' ? $cuota['OrdenDescuentoCuota']['cuota']: '')?></td>
					<td><?php echo $cuota['OrdenDescuentoCuota']['tipo_cuota_desc']?></td>
					<td align="center"><?php echo $util->armaFecha(( $cuota['OrdenDescuentoCuota']['estado'] != 'P' ? $cuota['OrdenDescuentoCuota']['vencimiento'] : $cuota['OrdenDescuentoCuota']['fecha_ultimo_pago']))?></td>
					<td><?php echo $cuota['OrdenDescuentoCuota']['estado_desc']?></td>
					<td><?php echo $cuota['OrdenDescuentoCuota']['situacion_desc']?></td>
					<td align="right"><?php echo ($cuota['OrdenDescuentoCuota']['importe'] < 0 ? '<span style="color:red;">'.number_format($cuota['OrdenDescuentoCuota']['importe'],2).'</span>' : number_format($cuota['OrdenDescuentoCuota']['importe'],2)) ?></td>
					<td align="center"><?php //   echo ( $cuota['OrdenDescuentoCuota']['estado'] == 'A' ? $controles->vencida($cuota['OrdenDescuentoCuota']['vencida']) : '')?></td>
					<td align="right"><?php echo number_format($cuota['OrdenDescuentoCuota']['pagado'],2)?></td>
					<td align="right"><?php echo ($cuota['OrdenDescuentoCuota']['saldo_cuota'] < 0 ? '<span style="color:red;">'.number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2).'</span>' : number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2)) ?></td>
					<td align="center"><?php echo $controles->btnModalBox(array('title' => 'DETALLE CUOTA','url' => '/mutual/orden_descuento_cuotas/view/'.$cuota['OrdenDescuentoCuota']['id'],'h' => 450, 'w' => 750))?></td>
					
				</tr>
				<?php if(!empty($cuota['OrdenDescuentoCuota']['cobros'])):?>
					<?php foreach($cuota['OrdenDescuentoCuota']['cobros'] as $cobro):?>
					<tr class="info_pago">
						<td colspan="7"></td>
						<td align="center"><?php echo $util->armaFecha($cobro['OrdenDescuentoCobroCuota']['fecha_cobro'])?></td>
						<td><?php echo $util->periodo($cobro['OrdenDescuentoCobroCuota']['periodo_cobro'])?></td>
						<td colspan="3"><?php echo $cobro['OrdenDescuentoCobroCuota']['tipo_cobro_desc']?></td>
						<td align="right"><?php echo $util->nf($cobro['OrdenDescuentoCobroCuota']['importe'])?></td>
						<td colspan="2">
							<?php if($cobro['OrdenDescuentoCobroCuota']['cancelacion_orden_id'] != 0):?>
								<?php echo $controles->linkModalBox('ORD.CANC. #'.$cobro['OrdenDescuentoCobroCuota']['cancelacion_orden_id'],array('title' => 'DETALLE ORDEN DE CANCELACION','url' => '/mutual/cancelacion_ordenes/vista_detalle/'.$cobro['OrdenDescuentoCobroCuota']['cancelacion_orden_id'],'h' => 450, 'w' => 750))?>
							<?php endif;?>
							<?php if($cobro['OrdenDescuentoCobroCuota']['reversado'] == 1):?>
								<span style='background-color:red;color:white;padding:2px;font-weight:bold;'>R</span>
							<?php endif;?>
						</td>
					</tr>
					<?php endforeach;?>
					<?php //   debug($cuota['OrdenDescuentoCuota']['cobros'])?>
				<?php endif;?>						
			<?php endforeach;?>	
			<tr>
				<td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" colspan="10" align="right"><strong>TOTAL PERIODO</strong></td>
				<td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_IMPO_CUOTA,2)?></strong></td>
				<td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
				<td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_PAGO_CUOTA,2)?></strong></td>
				<td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA,2)?></strong></td>
				<td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
			</tr>
			<tr>
				<td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" colspan="13" align="right"><strong>SALDO ACUMULADO A <?php echo $util->periodo($cuota['OrdenDescuentoCuota']['periodo'],true)?></strong></td>
				<td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA_ACUM,2)?></strong></td>
				<td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
			</tr>
			
			<?php if($detalle['liquidado'] != 0 && $proveedor_id == 0 && $codigo_organismo == "0"):?>
				<tr>
					<td colspan="15">
						<div style="padding: 3px;border: 1px solid green;background-color:#F2FEE9; color:green;margin-bottom: 5px;text-align: left;font-size: 10px;">
							<?php echo $controles->btnModalBox(array('title' => 'LIQUIDACION '.$util->periodo($periodo,true),'url' => '/mutual/liquidaciones/by_socio_periodo/'.$socio['Socio']['id'].'/'.$periodo,'h' => 450, 'w' => 950))?>	
							LIQUIDACION: <strong><?php echo number_format($detalle['liquidado_total'],2)?></strong> (PERIODO: <?php echo number_format($detalle['liquidado_periodo'],2)?> ** ATRASO: <?php echo number_format($detalle['liquidado_atraso'],2)?>) 
							| A DEBITAR: <strong><?php echo number_format($detalle['adebitar'],2)?></strong> 
							| ACREDITADO PENDIENTE DE IMPUTAR:  <strong><?php echo number_format($detalle['pendiente_imputar'],2)?> </strong> 
							| IMPUTADO: <strong><?php echo number_format($detalle['imputado_total'],2)?> </strong> (PERIODO: <?php echo number_format($detalle['imputado_periodo'],2)?> ** ATRASO: <?php echo number_format($detalle['imputado_atraso'],2)?>) 
							<!-- | SALDO: <strong> <?php //   echo number_format($detalle['liquidado_total'] - $detalle['imputado_total'],2)?></strong> -->
						</div>
					</td>
				</tr>
			<?php endif;?>
		<?php endforeach;?>	
		<tr>
			<td colspan="15" align="right"><?php echo $controles->botonGenerico('/mutual/orden_descuento_cuotas/estado_cuenta_pdf/'.$socio['Socio']['id'].'/'.$periodo_d.'/'.$periodo_h."/".($solo_deuda ? 1 : 0).'/'.$proveedor_id.'/'.$codigo_organismo.'/'.($discrimina_pagos ? 1 : 0),'controles/pdf.png','IMPRIMIR',array('target' => 'blank'))?></td>
		</tr>	
		

	</table>
	<?php //   echo $this->renderElement('orden_descuento_cuotas/resumen_deuda',array('socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<?php else:?>
<h4>NO EXISTEN CUOTAS PARA EL PERIODO INDICADO</h4>	
<?php endif;?>