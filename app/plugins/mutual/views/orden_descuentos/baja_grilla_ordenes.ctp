<?php echo $this->renderElement('head',array('title' => 'CUOTAS :: CAMBIO DE SITUACION','plugin' => 'config'))?>
<?php echo $this->renderElement('personas/datos_personales',array('persona_id'=>$persona['Persona']['id'],'plugin' => 'pfyj'));?>

<h3>LISTADO DE ORDENES DE DESCUENTOS VIGENTES</h3>

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
//	debug($sol);
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
		<td colspan="8" style="background-color:#F5f7f7;display:none;" id="DETALLE_CUOTAS_<?php echo $ord['OrdenDescuento']['id']?>">
			<h3 style="margin:2px;">DETALLE DE CUOTAS</h3>
				<table>
					<tr>
						<th>PERIODO</th>
						<th>CUOTA</th>
						<th>CONCEPTO</th>
						<th>ESTADO</th>
						<th>SITUACION</th>
						<th>VENCIMIENTO</th>
						<th>IMPORTE</th>
						<th></th>
					</tr>
					<?php foreach($ord['OrdenDescuentoCuota'] as $cuota):?>
						<tr id="LTR_<?php echo $ord['OrdenDescuento']['id']?>_<?php echo $cuota['id']?>">
							<td><?php echo $util->periodo($cuota['periodo'])?></td>
							<td align="center"><?php echo $cuota['nro_cuota']?></td>
							<td><?php echo $util->globalDato($cuota['tipo_cuota'],'concepto_1')?></td>
							<td align="center"><?php echo $cuota['estado']?></td>
							<td align="center"><?php echo $util->globalDato($cuota['situacion'],'concepto_1')?></td>
							<td align="center"><?php echo $util->armaFecha($cuota['vencimiento'])?></td>
							<td align="right"><?php echo $util->nf($cuota['importe'])?></td>
							<td><input type="checkbox" name="data[OrdenDescuentoCuota][check_id][<?php echo $cuota['id']?>]" value="1" onclick="toggleCell('LTR_<?php echo $ord['OrdenDescuento']['id']?>_<?php echo $cuota['id']?>', this)"/></td>
						</tr>
					<?php endforeach;?>
				</table>
		</td>
	</tr>
<?php endforeach;?>	

</table>
