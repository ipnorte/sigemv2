<?php echo $this->renderElement('socios/apenom',array('socio_id' => $convenio['SocioConvenio']['socio_id'], 'plugin' => 'pfyj'))?>
<h3>DETALLE DEL CONVENIO #<?php echo $convenio['SocioConvenio']['id']?></h3>
	<table>
		<tr>
			<th>ORD.DTO.</th>
			<th>PROVEEDOR</th>
			<th>TIPO CONVENIO</th>
			<th>ORGANISMO</th>
			<th>BENEFICIO</th>
			<th>TOTAL</th>
			<th>CUOTAS</th>
			<th>IMPORTE</th>
		</tr>
		<tr>
			<td align="center"><?php echo $convenio['SocioConvenio']['orden_descuento_id']?></td>
			<td><?php echo $convenio['SocioConvenio']['proveedor_razon_social']?></td>
			<td><?php echo $convenio['SocioConvenio']['tipo_convenio_desc']?></td>
			<td><?php echo $convenio['SocioConvenio']['organismo_desc']?></td>
			<td><?php echo $convenio['SocioConvenio']['beneficio_str']?></td>
			<td align="right"><?php echo $util->nf($convenio['SocioConvenio']['importe_total'])?></td>
			<td align="center"><?php echo $convenio['SocioConvenio']['cuotas']?></td>
			<td align="right"><?php echo $util->nf($convenio['SocioConvenio']['importe_cuota'])?></td>
		</tr>
		<tr>
		
			<td colspan="8">
				<h4>DETALLE DE CUOTAS ORIGINALES INCLUIDAS</h4>

				<table>
					<tr>
						<th class="subtabla">ORD.DTO.</th>
						<th class="subtabla">ORGANISMO</th>
						<th class="subtabla">TIPO / NUMERO</th>
						<th class="subtabla">COD - NRO</th>
						<th class="subtabla">PROVEEDOR / PRODUCTO</th>
						<th class="subtabla">CUOTA</th>
						<th class="subtabla">CONCEPTO</th>
						<th class="subtabla">ESTADO</th>
						<th class="subtabla">SIT</th>
						<th class="subtabla">IMPORTE</th>
						<th class="subtabla">POND.</th>
			
					</tr>
					<?php $ACU_IMPO_CUOTA = 0;?>
					<?php $ACU_PONDERADO = 0;?>					
					<?php foreach ($convenio['SocioConvenioCuota'] as $cuotaOriginal):?>	
							<?php $ACU_IMPO_CUOTA += $cuotaOriginal['importe'];?>
							<?php $ACU_PONDERADO += $cuotaOriginal['ponderacion'];?>
							<tr>
								<td align="center"><?php echo $cuotaOriginal['OrdenDescuentoCuota']['orden_descuento_id']?></td>
								<td><?php echo $cuotaOriginal['OrdenDescuentoCuota']['organismo']?></td>
								<td nowrap="nowrap"><?php echo $cuotaOriginal['OrdenDescuentoCuota']['tipo_nro']?></td>
								<td align="center"><?php echo $cuotaOriginal['OrdenDescuentoCuota']['codigo_comercio_referencia']?>&nbsp;-&nbsp;<?php echo $cuotaOriginal['OrdenDescuentoCuota']['nro_orden_referencia']?></td>
								<td><?php echo $cuotaOriginal['OrdenDescuentoCuota']['proveedor_producto']?></td>
								<td align="center"><?php echo ($cuotaOriginal['OrdenDescuentoCuota']['tipo_orden_dto'] != 'MUTUTPROCFIJ' ? $cuotaOriginal['OrdenDescuentoCuota']['cuota'] : '')?></td>
								<td><?php echo $cuotaOriginal['OrdenDescuentoCuota']['tipo_cuota_desc']?></td>
								<td><?php echo $cuotaOriginal['OrdenDescuentoCuota']['estado_desc']?></td>
								<td><?php echo $cuotaOriginal['OrdenDescuentoCuota']['situacion_desc']?></td>
								<td align="right"><?php echo ($cuotaOriginal['importe'] < 0 ? '<span style="color:red;">'.$util->nf($cuotaOriginal['importe']).'</span>' : $util->nf($cuotaOriginal['importe'])) ?></td>
								<td align="center"><?php echo $util->nf($cuotaOriginal['ponderacion']) ?>%</td>
							</tr>						
					<?php endforeach;?>
					<tr>
						<th class="subtabla" colspan="9" style="text-align: right;">TOTALES</th>
						<th class="subtabla"><?php echo $util->nf($ACU_IMPO_CUOTA) ?></th>
						<th class="subtabla"><?php echo $util->nf($ACU_PONDERADO) ?>%</th>
					</tr>
				</table>
			</td>
		</tr>
		</table>
<h3>DETALLE DE ORDEN DE DESCUENTO #<?php echo $convenio['SocioConvenio']['orden_descuento_id']?></h3>	
<?php echo $this->renderElement('orden_descuento/resumen_by_id',array('id' => $convenio['SocioConvenio']['orden_descuento_id'],'detallaCuotas' => true, 'plugin' => 'mutual'))?>
	
