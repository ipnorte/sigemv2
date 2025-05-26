<hr/>
<h3>VISTA PREVIA DEL CONVENIO</h3>
<table>
	<tr>
		<th>PROVEEDOR</th>
		<th>TIPO CONVENIO</th>
		<th>ORGANISMO</th>
		<th>BENEFICIO</th>
		<th>TOTAL</th>
		<th>CUOTAS</th>
		<th>IMPORTE</th>
	</tr>
	<tr>
		<td><?php echo $convenio['SocioConvenio']['proveedor_razon_social']?></td>
		<td><?php echo $convenio['SocioConvenio']['tipo_convenio_desc']?></td>
		<td><?php echo $convenio['SocioConvenio']['organismo_desc']?></td>
		<td><?php echo $convenio['SocioConvenio']['beneficio_str']?></td>
		<td align="right"><?php echo $util->nf($convenio['SocioConvenio']['importe_total'])?></td>
		<td align="center"><?php echo $convenio['SocioConvenio']['cuotas']?></td>
		<td align="right"><?php echo $util->nf($convenio['SocioConvenio']['importe_cuota'])?></td>
	</tr>
	<tr>
	
		<td colspan="7">
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
	<tr>
		<td colspan="7">
		
			<h4>DETALLE DE CUOTAS A GENERAR</h4>
			<table>
			
				<tr>
					<th class="subtabla">PRODUCTO</th>
					<th class="subtabla">PERIODO</th>
					<th class="subtabla">CUOTA</th>
					<th class="subtabla">CONCEPTO</th>
					<th class="subtabla">IMPORTE</th>
					<th class="subtabla">VTO</th>
				</tr>
				<?php foreach($convenio['orden_descuento']['OrdenDescuentoCuota'] as $cuota):?>
				
					<tr>
					
						<td><?php echo $util->globalDato($cuota['tipo_producto'])?></td>
						<td><?php echo $util->periodo($cuota['periodo'])?></td>
						<td align="center"><?php echo $cuota['nro_cuota']?></td>
						<td><?php echo $util->globalDato($cuota['tipo_cuota'])?></td>
						<td align="right"><?php echo $util->nf($cuota['importe'])?></td>
						<td><?php echo $util->armaFecha($cuota['vencimiento'])?></td>
					
					</tr>
				
				<?php endforeach;?>
			
			</table>
			
		
		</td>
	
	</tr>
</table>
<?php echo $frm->create(null,array('action' => 'crear_convenio/'.$socio['Socio']['id'], 'onsumbit' => "return confirm('GENERAR EL CONVENIO?')"))?>
<?php echo $frm->hidden('SocioConvenio.persona_beneficio_id',array('value' => $this->data['SocioConvenio']['persona_beneficio_id']))?>
<?php echo $frm->hidden('SocioConvenio.fecha.day',array('value' => $this->data['SocioConvenio']['fecha']['day']))?>
<?php echo $frm->hidden('SocioConvenio.fecha.month',array('value' => $this->data['SocioConvenio']['fecha']['month']))?>
<?php echo $frm->hidden('SocioConvenio.fecha.year',array('value' => $this->data['SocioConvenio']['fecha']['year']))?>
<?php echo $frm->hidden('SocioConvenio.cuotas',array('value' => $this->data['SocioConvenio']['cuotas']))?>
<?php echo $frm->hidden('SocioConvenio.socio_id',array('value' => $this->data['SocioConvenio']['socio_id']))?>
<?php echo $frm->hidden('SocioConvenio.importe',array('value' => $this->data['SocioConvenio']['importe']))?>
<?php echo $frm->hidden('SocioConvenio.generar',array('value' => 1))?>
<?php foreach($this->data['SocioConvenioCuota']['orden_descuento_cuota_id'] as $orden_descuento_cuota_id => $importeCuota):?>
	<input type="hidden" name="data[SocioConvenioCuota][orden_descuento_cuota_id][<?php $orden_descuento_cuota_id?>]" value="<?php echo $importeCuota?>"/>
<?php endforeach;?>

<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'APROBAR CONVENIO DE PAGO','URL' => ( empty($fwrd) ? "/pfyj/socio_convenios/index/".$socio['Socio']['id']."/1" : $fwrd) ))?>

<?php //   debug($convenio['orden_descuento'])?>