<?php echo $this->renderElement('head',array('title' => 'CUOTAS :: CAMBIO DE SITUACION','plugin' => 'config'))?>
<?php //   echo $this->renderElement('personas/datos_personales',array('persona_id'=>$persona['Persona']['id'],'plugin' => 'pfyj'))?>
<?php echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$persona,'link' => true,'plugin' => 'pfyj'))?>
<h3>LISTADO DE ORDENES DE DESCUENTOS</h3>

<table>

	<tr>
		<th></th>
		<th>ORDEN</th>
		<th>INICIA</th>
		<th>1er VTO</th>
		<th>TIPO / NUMERO</th>
		<th>PROVEEDOR - PRODUCTO</th>
		<th>DEVENGADO</th>
		<th>PER</th>
		<th>BENEFICIO</th>
	</tr>

<?php
$i = 0;

foreach ($ordenes as $ord):

	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
//	debug($ord);
?>	
	<tr class="<?php echo $ord['OrdenDescuento']['tipo_orden_dto']?>">
		<td><?php echo $controles->btnToggle('DETALLE_CUOTAS_'. $ord['OrdenDescuento']['id'],'','controles/bullet_arrow_down.png')?></td>
		<td align="center"><?php echo $html->link($ord['OrdenDescuento']['id'],'/mutual/orden_descuentos/view/'.$ord['OrdenDescuento']['id'].'/'.$ord['OrdenDescuento']['socio_id'].'/1/1')?></td>
		<td nowrap="nowrap"><?php echo $util->periodo($ord['OrdenDescuento']['periodo_ini'])?></td>
		<td nowrap="nowrap"><?php echo $util->armaFecha($ord['OrdenDescuento']['primer_vto_socio'])?></td>
		<td nowrap="nowrap"><?php echo $ord['OrdenDescuento']['tipo_orden_dto']?> #<?php echo $ord['OrdenDescuento']['numero']?></td>
		<td nowrap="nowrap"><?php echo $ord['OrdenDescuento']['proveedor_producto']?></td>
		<td align="right"><?php echo number_format($ord['OrdenDescuento']['importe_devengado'],2)?></td>
		<td align="center"><?php echo $controles->OnOff2($ord['OrdenDescuento']['permanente'],true)?></td>
		<td><?php echo $this->requestAction('/pfyj/persona_beneficios/view/'.$ord['OrdenDescuento']['persona_beneficio_id'])?></td>
	</tr>
	<tr>
		<td colspan="9" style="background-color:#F5f7f7;display:none;" id="DETALLE_CUOTAS_<?php echo $ord['OrdenDescuento']['id']?>">
			
			<h3 style="margin:2px;">CUOTAS ADEUDADAS</h3>
				<?php echo $frm->create(null,array('action' => 'cambiar_situacion/'.$persona['Persona']['id'],'id' => 'FormCambioSituacion_' . $ord['OrdenDescuento']['id']))?>
				
				<table>
					<tr>
						<th>TIPO / NUMERO</th>
						<th>PERIODO</th>
						<th>PROVEEDOR - PRODUCTO</th>
						<th>CUOTA</th>
						<th>CONCEPTO</th>
						<th>ESTADO</th>
						<th>SITUACION</th>
						<th>VENCIMIENTO</th>
						<th>IMPORTE</th>
						<th>SALDO</th>
						<th></th>
					</tr>
					<?php foreach($ord['OrdenDescuentoCuota'] as $cuota):?>
						<?php 
				  		$bloqueo = array();
				  		if(!empty($cuota['OrdenDescuentoCuota']['bloqueo_liquidacion'])) $bloqueo = $cuota['OrdenDescuentoCuota']['bloqueo_liquidacion'];
						?>
						<tr id="LTR_<?php echo $ord['OrdenDescuento']['id']?>_<?php echo $cuota['OrdenDescuentoCuota']['id']?>" class="<?php echo $cuota['OrdenDescuentoCuota']['estado']?>">
							<td align="center"><?php echo $cuota['OrdenDescuentoCuota']['tipo_nro']?></td>
							<td><?php echo $util->periodo($cuota['OrdenDescuentoCuota']['periodo'])?></td>
							<td><?php echo $cuota['OrdenDescuentoCuota']['proveedor_producto']?></td>
							<td align="center"><?php echo $cuota['OrdenDescuentoCuota']['cuota']?></td>
							<td><?php echo $cuota['OrdenDescuentoCuota']['tipo_cuota_desc']?></td>
							<td align="center"><?php echo $cuota['OrdenDescuentoCuota']['estado_desc']?></td>
							<td align="center"><?php echo $cuota['OrdenDescuentoCuota']['situacion_desc']?></td>
							<td align="center"><?php echo $util->armaFecha($cuota['OrdenDescuentoCuota']['vencimiento'])?></td>
							<td align="right"><?php echo $util->nf($cuota['OrdenDescuentoCuota']['importe'])?></td>
							<td align="right"><?php echo $util->nf($cuota['OrdenDescuentoCuota']['saldo_cuota'])?></td>
							<td>
								<?php if(!empty($bloqueo) && $bloqueo['id'] != 0):?>
									<span style="color: red;"><?php echo "LIQ #".$bloqueo['id'] . " " . $bloqueo['liquidacion']?></span>
								<?php elseif($cuota['OrdenDescuentoCuota']['saldo_cuota'] > 0 || $cuota['OrdenDescuentoCuota']['estado'] == 'B'):?>							
									<input type="checkbox" name="data[OrdenDescuentoCuota][check_id][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="1" onclick="toggleCell('LTR_<?php echo $ord['OrdenDescuento']['id']?>_<?php echo $cuota['OrdenDescuentoCuota']['id']?>', this)"/>
								<?php endif;?>
							</td>
						</tr>
					<?php endforeach;?>
				</table>
				<div class="areaDatoForm2">
				
					<table class="tbl_form">
						<tr>
							<td>NUEVA SITUACION</td>
							<td>
								<?php echo $this->renderElement('global_datos/combo',array(
																									'plugin'=>'config',
																									'label' => '.',
																									'model' => 'OrdenDescuentoCuota.situacion',
																									'prefijo' => 'MUTUSICU',
																									'disable' => false,
																									'empty' => false,
																									'selected' => '0',
																									'logico' => true,
								))?>				
							</td>
						</tr>
						<tr>
							<td valign="top">OBSERVACIONES</td>
							<td><?php echo $frm->textarea('observaciones',array('cols' => 60, 'rows' => 10))?></td>
						</tr>
						<tr>
							<td colspan="2"><?php echo $frm->submit('CAMBIAR SITUACION DE CUOTAS ORDEN #' . $ord['OrdenDescuento']['id'])?></td>
						</tr>
					</table>
					<?php echo $frm->end();?>
				
				</div>
		</td>
	</tr>
<?php endforeach;?>	

</table>