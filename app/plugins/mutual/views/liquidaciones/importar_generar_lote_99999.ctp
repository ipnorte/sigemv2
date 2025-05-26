<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: GENERAR LOTE DE IMPORTACION DE DATOS'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>

<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>
<h3>GENERAR LOTE DE IMPORTACION DE DATOS</h3>
<div class="areaDatoForm">
	<?php echo $frm->create(null,array('action'=>'importar_generar_lote/'.$liquidacion['Liquidacion']['id'].'/99999'))?>
	<table class="tbl_form">
		<tr>
			<td>ARCHIVO GENERADO</td>
			<td>
				<select name="data[LiquidacionSocioEnvio][id]" id="LiquidacionSocioEnvioId">
					<?php foreach ($lotes as $lote):?>
						<option value="<?php echo $lote['LiquidacionSocioEnvio']['id']?>" selected="<?php echo ($lote['LiquidacionSocioEnvio']['id'] == $this->data['LiquidacionSocioEnvio']['id'] ? "selected" : "")?>">
							BANCO: <?php echo $lote['LiquidacionSocioEnvio']['banco_nombre']?>
							|
							DEBITO EL: <?php echo $util->armaFecha($lote['LiquidacionSocioEnvio']['fecha_debito'])?>
							|
							ARCHIVO: <?php echo $lote['LiquidacionSocioEnvio']['archivo']?>
							|
							TOTAL A DEBITAR: <?php echo $util->nf($lote['LiquidacionSocioEnvio']['importe_debito'])?>
						</option>
					<?php endforeach;?>
				</select>
			</td>
		</tr>		
		<tr>
			<td colspan="2"><?php echo $frm->submit("PROCESAR")?></td>
		</tr>
		
	</table>
	<?php echo $frm->hidden('LiquidacionSocioEnvio.preimputar',array('value' => 1))?>
	<?php echo $frm->hidden('LiquidacionSocioEnvio.liquidacion_id',array('value' => $liquidacion['Liquidacion']['id']))?>
	<?php echo $frm->hidden('LiquidacionSocioEnvio.procesar',array('value' =>'CABECERA'))?>
	<?php echo $frm->end();?>

</div>

<?php if($preimputar == 1):?>
	<?php echo $frm->create(null,array('action'=>'importar_generar_lote/'.$liquidacion['Liquidacion']['id'].'/99999'))?>
	<h3>DETALLE DE REGISTROS NO PRE-IMPUTADOS/NO INFORMADOS</h3>
	<table>
		<tr>
			<th>SOCIO</th>
			<th>TIPO - DOCUMENTO | APELLIDO Y NOMBRE</th>
			<th>IMPORTE</th>
			<th></th>
			<th></th>
		</tr>
		<?php $cantidad = $total = 0;?>
		<?php foreach ($registros as $registro):?>
			<tr>
				<td align="center"><?php echo $registro['socio_id']?></td>
				<td><?php echo $registro['socio_apenom']?></td>
				<td align="right"><?php echo $util->nf($registro['decode']['importe_debitado'])?></td>
				<td>
					<input type="checkbox" name="data[LiquidacionSocioEnvioRegistro][include_id][<?php echo $registro['LiquidacionSocioEnvioRegistro']['id']?>]"/>
					<input type="hidden" name="data[LiquidacionSocioEnvioRegistro][registro_serialized][<?php echo $registro['LiquidacionSocioEnvioRegistro']['id']?>]" value="<?php echo base64_encode(serialize($registro))?>"/>
				</td>
				<td>
					<select name="data[LiquidacionSocioEnvioRegistro][estado_id][<?php echo $registro['LiquidacionSocioEnvioRegistro']['id']?>]">
						<option value="|" selected="selected"></option>
						<?php foreach ($codigos as $codigo):?>
							<option value="<?php echo $codigo['BancoRendicionCodigo']['codigo']."|". $codigo['BancoRendicionCodigo']['descripcion']?>"><?php echo $codigo['BancoRendicionCodigo']['descripcion'] ?></option>
						<?php endforeach;?>
					</select>
				</td>				
			</tr>
			<?php $total += $registro['decode']['importe_debitado']?>
		<?php endforeach;?>
		<tr class="totales">
			<th colspan="2">TOTAL DEL ARCHIVO ENVIADO *** NO PRE-IMPUTADO ***</th>
			<th><?php echo $util->nf($total)?></th>
			<th></th>
			<th></th>
		</tr>
	</table>
	<?php echo $frm->hidden('LiquidacionSocioEnvio.preimputar',array('value' => 1))?>
	<?php echo $frm->hidden('LiquidacionSocioEnvio.procesar',array('value' =>'GENERAR_PREVIEW_LOTE'))?>
	<?php echo $frm->hidden('LiquidacionSocioEnvio.id',array('value' =>$this->data['LiquidacionSocioEnvio']['id']))?>
	<?php echo $frm->end("GENERAR VISTA PREVIA DEL ARCHIVO A EMITIR");?>
	<?php //   debug($registros)?>
<?php endif;?>
<?php if(!empty($selected)):?>
	<h3>DETALLE DE LOS REGISTROS A INCLUIR EN EL ARCHIVO</h3>
	<table>
		<tr>
			<th>SOCIO</th>
			<th>IMPORTE</th>
			<th></th>
			<th></th>
		</tr>
		<?php $TOTAL = 0;?>
		<?php foreach ($selected as $registro):?>
			<?php $TOTAL += $registro['decode']['importe_debitado']?>
			<tr>
				<td><?php echo $registro['socio_apenom']?></td>
				<td align="right"><?php echo $util->nf($registro['decode']['importe_debitado'])?></td>
				<td align="center"><?php echo $registro['codigo_estado']?></td>
				<td><?php echo $registro['codigo_estado_desc']?></td>
			</tr>
		<?php endforeach;?>
		<tr class="totales">
			<th colspan="2">SOCIO</th>
			<th><?php echo $util->nf($TOTAL)?></th>
			<th colspan="2"></th>
		</tr>		
	</table>
	<?php echo $frm->create(null,array('action'=>'importar_generar_lote/'.$liquidacion['Liquidacion']['id'].'/99999'))?>
	<div class="areaDatoForm">
		<h3>GENERAR LOTE DE COBRANZA</h3>
		<table class="tbl_form">
			<tr><td>FECHA ACREDITACION</td><td><?php echo $frm->calendar('LiquidacionSocioEnvio.fecha_acreditacion','',date('Y-m-d'),date("Y")-1,date("Y"))?></td></tr>
		</table>
	
		<?php echo $frm->hidden('LiquidacionSocioEnvio.lote',array('value' => base64_encode(serialize($selected))))?>
		<?php echo $frm->hidden('LiquidacionSocioEnvio.preimputar',array('value' => 1))?>
		<?php echo $frm->hidden('LiquidacionSocioEnvio.procesar',array('value' =>'GENERAR_LOTE'))?>
		<?php echo $frm->hidden('LiquidacionSocioEnvio.id',array('value' =>$this->data['LiquidacionSocioEnvio']['id']))?>
		<?php echo $frm->end("GENERAR ARCHIVO");?>
	</div>
	<?php //   debug($selected)?>

<?php endif;?>