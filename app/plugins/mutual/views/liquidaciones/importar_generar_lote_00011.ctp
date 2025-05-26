<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: GENERAR LOTE DE IMPORTACION DE DATOS'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>

<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>
<h3>GENERAR LOTE DE IMPORTACION DE DATOS</h3>
<div class="areaDatoForm">
	<?php echo $frm->create(null,array('action'=>'importar_generar_lote/'.$liquidacion['Liquidacion']['id'].'/00011'))?>
	<table class="tbl_form">
		<tr>
			<td>ARCHIVO GENERADO</td>
			<td>
                                <?php 
                                if(!isset($this->data['LiquidacionSocioEnvio']['id']) && empty($this->data['LiquidacionSocioEnvio']['id'])) $seleted = $lotes[0]['LiquidacionSocioEnvio']['id'];
                                else $seleted = $this->data['LiquidacionSocioEnvio']['id'];
                                ?>
				<select name="data[LiquidacionSocioEnvio][id]" id="LiquidacionSocioEnvioId">
					<?php foreach ($lotes as $lote):?>
						<option value="<?php echo $lote['LiquidacionSocioEnvio']['id']?>" <?php echo ($lote['LiquidacionSocioEnvio']['id'] == $seleted ? "selected=selected" : "")?>>
							BANCO: <?php echo $lote['LiquidacionSocioEnvio']['banco_nombre']?>
							|
							DEBITO EL: <?php echo $util->armaFecha($lote['LiquidacionSocioEnvio']['fecha_debito'])?>
							|
							ARCHIVO: <?php echo $lote['LiquidacionSocioEnvio']['archivo']?>
							|
							TOTAL A DEBITAR: <?php echo $util->nf($lote['LiquidacionSocioEnvio']['importe_debito'])?>
                                                         [CREADO EL 
							<?php echo $lote['LiquidacionSocioEnvio']['created']?>
                                                        ]
						</option>
					<?php endforeach;?>
				</select>
			</td>
		</tr>
		<tr>
			<td>IDENTIFICADOR DE DEBITO</td>
			<td><?php echo $frm->number('LiquidacionSocioEnvio.identificador_debito',array('maxlength' => 22,'size' => 25))?></td>
		</tr>
		<tr>
			<td>NRO DE CUENTA</td>
			<td><?php echo $frm->number('LiquidacionSocioEnvio.nro_cta_bco',array('maxlength' => 22,'size' => 25))?></td>
		</tr>

		<tr>
			<td>GENERAR LOTE PARCIAL</td>
			<td><input type="checkbox" name="data[LiquidacionSocioEnvio][preimputar]" value="1" <?php echo ($preimputar == 1 ? "checked = checked" : "")?>/></td>
		</tr>
				
		<tr>
			<td colspan="2"><?php echo $frm->submit("PROCESAR")?></td>
		</tr>
		
	</table>
	<?php echo $frm->hidden('LiquidacionSocioEnvio.liquidacion_id',array('value' => $liquidacion['Liquidacion']['id']))?>
	<?php echo $frm->hidden('LiquidacionSocioEnvio.procesar',array('value' =>'CABECERA'))?>
	<?php echo $frm->end();?>

</div>
<?php if(!empty($datosResumen) && $preimputar == 0):?>
	
	<table>
		<tr>
			<th>CODIGO</th>
			<th>CONCEPTO</th>
			<th>REGISTROS</th>
			<th>IMPORTE</th>
		</tr>
		<?php foreach ($datosResumen as $dato):?>
			<tr>
				<td><?php echo $dato['liquidacion_socio_envio_registros']['codigo_rendicion']?></td>
				<td><?php echo $dato[0]['descripcion_codigo']?></td>
				<td align="center"><?php echo $dato[0]['registros']?></td>
				<td align="right"><?php echo $util->nf($dato[0]['importe'])?></td>
			</tr>
		<?php endforeach;?>
	</table>
	<BR/>
	
	<div>
	<?php echo $html->link("GENERAR ARCHIVO PARA PRE-IMPUTACION",'/mutual/liquidaciones/importar_generar_lote_download/'.$this->data['LiquidacionSocioEnvio']['id'].'/00011',array('target' => 'blank'))?>
	
	</div>
	<?php //   debug($datosResumen)?>
	
<?php endif;?>



<?php if(!empty($registros) && $preimputar == 0):?>
	<h3>DETALLE DE REGISTROS QUE CUMPLEN LA CONDICION</h3>
	<?php echo $frm->create(null,array('action'=>'importar_generar_lote/'.$liquidacion['Liquidacion']['id'].'/00011'))?>
	<table>
		<tr>
			<th>ID DEBITO</th>
			<th>REGISTRO</th>
			<th>IMPORTE</th>
			<th>PROCESADO</th>
			<th>CODIGO - CONCEPTO GRABADO</th>
			<th>ASIGNAR CODIGO</th>
		</tr>
		<?php foreach ($registros as $registro):?>
			<tr>
				<td><?php echo $registro['LiquidacionSocioEnvioRegistro']['identificador_debito']?></td>
				<td><?php echo $registro['LiquidacionSocioEnvioRegistro']['registro']?></td>
				<td align="right"><strong><?php echo $util->nf($registro['LiquidacionSocioEnvioRegistro']['importe_adebitar'])?></strong></td>
				<td align="center" style="color: red;"><strong><?php echo ($registro['LiquidacionSocioEnvioRegistro']['procesado'] ? "SI" : "NO")?></strong></td>
				<td style="color: red;"><strong><?php echo $registro['LiquidacionSocioEnvioRegistro']['codigo_rendicion']." - ".$registro['LiquidacionSocioEnvioRegistro']['descripcion_codigo']?></strong></td>
				<td>
					<select name="data[LiquidacionSocioEnvioRegistro][id][<?php echo $registro['LiquidacionSocioEnvioRegistro']['id']?>]">
						<option value="|" selected="selected"></option>
						<?php foreach ($codigos as $codigo):?>
							<option value="<?php echo $codigo['BancoRendicionCodigo']['codigo']."|". $codigo['BancoRendicionCodigo']['descripcion']?>"><?php echo $codigo['BancoRendicionCodigo']['descripcion'] ?></option>
						<?php endforeach;?>
					</select>
				</td>
			</tr>
		<?php endforeach;?>
	</table>
	<?php echo $frm->hidden('LiquidacionSocioEnvio.procesar',array('value' =>'DETALLE'))?>
	<?php echo $frm->hidden('LiquidacionSocioEnvio.identificador_debito',array('value' =>$this->data['LiquidacionSocioEnvio']['identificador_debito']))?>
	<?php echo $frm->hidden('LiquidacionSocioEnvio.id',array('value' =>$this->data['LiquidacionSocioEnvio']['id']))?>
	<?php echo $frm->end("ACTUALIZAR INFORMACION");?>	
	<?php //   debug($codigos)?>
	<?php //   debug($registros)?>
<?php endif;?>

<?php if($preimputar == 1):?>
	<?php echo $frm->create(null,array('action'=>'importar_generar_lote/'.$liquidacion['Liquidacion']['id'].'/00011'))?>
	<h3>DETALLE DE REGISTROS NO PRE-IMPUTADOS/NO INFORMADOS</h3>
	<table>
		<tr>
			<th>SUCURSAL</th>
			<th>CUENTA</th>
			<th>SOCIO</th>
			<th>TIPO - DOCUMENTO | APELLIDO Y NOMBRE</th>
			<th>IMPORTE</th>
			<th></th>
			<th></th>
		</tr>
		<?php $cantidad = $total = 0;?>
		<?php foreach ($registros as $registro):?>
			<tr>
				<td align="center"><?php echo $registro['decode']['sucursal']?></td>
				<td><strong><?php echo $registro['decode']['nro_cta_bco']?></strong></td>
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
			<th colspan="4">TOTAL DEL ARCHIVO ENVIADO *** NO PRE-IMPUTADO ***</th>
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
			<th>SUCURSAL</th>
			<th>CUENTA</th>
			<th>SOCIO</th>
			<th>COBRADO</th>
                        <th>NO COBRADO</th>
			<th></th>
			<th></th>
		</tr>
		<?php $TOTAL_COB = $TOTAL_NCOB = 0;?>
		<?php foreach ($selected as $registro):?>
			<?php 
                            
                            if($registro['decode']['indica_pago'] == 1){
                                $TOTAL_COB += $registro['decode']['importe_debitado'];
                            }else{
                                $TOTAL_NCOB += $registro['decode']['importe_debitado'];
                            }
                        ?>
                <tr class="activo_<?php echo $registro['decode']['indica_pago']?>">
				<td align="center"><?php echo $registro['decode']['sucursal']?></td>
				<td><strong><?php echo $registro['decode']['nro_cta_bco']?></strong></td>
				<td><?php echo $registro['socio_apenom']?></td>
				<td align="right"><?php if($registro['decode']['indica_pago'] == 1) echo $util->nf($registro['decode']['importe_debitado'])?></td>
                                <td align="right"><?php //   if($registro['decode']['indica_pago'] == 0) echo $util->nf($registro['decode']['importe_debitado'])?></td>
				<td align="center"><?php if($registro['decode']['indica_pago'] == 0) echo $registro['codigo_estado']?></td>
				<td><?php if($registro['decode']['indica_pago'] == 0) echo $registro['codigo_estado_desc']?></td>
			</tr>
		<?php endforeach;?>
		<tr class="totales">
			<th colspan="3">TOTAL COBRADO</th>
			<th><?php echo $util->nf($TOTAL_COB)?></th>
                        <th><?php //   echo $util->nf($TOTAL_NCOB)?></th>
			<th colspan="2"></th>
		</tr>		
	</table>
	<?php echo $frm->create(null,array('action'=>'importar_generar_lote/'.$liquidacion['Liquidacion']['id'].'/00011'))?>
	<div class="areaDatoForm">
		<h3>GENERAR LOTE DE COBRANZA Y ASOCIARLO A LA LIQUIDACION</h3>
        
        <?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>
        <hr/>
		<table class="tbl_form">
			<tr><td>FECHA ACREDITACION</td><td><?php echo $frm->calendar('LiquidacionSocioEnvio.fecha_acreditacion','',date('Y-m-d'),date("Y")-1,date("Y"))?></td></tr>
		</table>
        <?php echo $frm->hidden('LiquidacionSocioEnvio.liquidacion_id',array('value' => $liquidacion['Liquidacion']['id']))?>
        <?php echo $frm->hidden('LiquidacionSocioEnvio.periodo',array('value' => $liquidacion['Liquidacion']['periodo']))?>
        <?php echo $frm->hidden('LiquidacionSocioEnvio.codigo_organismo',array('value' => $liquidacion['Liquidacion']['codigo_organismo']))?>
		<?php echo $frm->hidden('LiquidacionSocioEnvio.lote',array('value' => base64_encode(serialize($selected))))?>
		<?php echo $frm->hidden('LiquidacionSocioEnvio.preimputar',array('value' => 1))?>
		<?php echo $frm->hidden('LiquidacionSocioEnvio.procesar',array('value' =>'GENERAR_LOTE'))?>
		<?php echo $frm->hidden('LiquidacionSocioEnvio.id',array('value' =>$this->data['LiquidacionSocioEnvio']['id']))?>
		<?php echo $frm->end("PROCESAR");?>
	</div>
	<?php //   debug($selected)?>

<?php endif;?>


<?php //   debug($liquidacion)?>