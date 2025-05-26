<?php
if($menuPersonas == 1) {echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'));}
else {echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'));}
?>
<h3>ORDENES DE CANCELACION</h3>
<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => $menuPersonas,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<!-- <div class="actions"><?php //   echo $controles->botonGenerico('generar/'.$socio['Persona']['id'],'controles/add.png','Nueva Orden de Cancelacion',array('target' => '_blank'))?></div> -->

	<div class="actions">
		<?php echo $controles->botonGenerico('generar/'.$socio['Persona']['id'],'controles/add.png','Nueva Orden de Cancelacion PROPIA',array('target' => '_blank'))?>
		&nbsp;|&nbsp;
		<?php echo $controles->botonGenerico('terceros_generar/'.$socio['Persona']['id'],'controles/add.png','Nueva Orden de Cancelacion de TERCEROS',array('target' => '_blank'))?>
	</div>

<h4>LISTADO DE ORDENES PROCESADAS</h4>
<?php if(!empty($cancelaciones)):?>
	<?php //   debug($cancelaciones)?>
	
	<table>
		<tr>
			<th>#</th>
			<th>TIPO</th>
			<th>ORDEN</th>
			<th>TIPO / NUMERO</th>
			<th>PROVEEDOR / PRODUCTO</th>
			<th>CUOTAS</th>
			<th>A LA ORDEN DE</th>
			<th>CONCEPTO</th>
			<th>DEUDA PROVEEDOR</th>
			<th>SALDO ORDEN DTO</th>
			<th>IMPORTE SELECCIONADO</th>
			<th>DEBITO / CREDITO</th>
			<th>VENCIMIENTO</th>
			<th>TOTAL ORDEN</th>
			<th>OBSERVACIONES</th>
			<th>CANCELA CON</th>
			<th>FECHA IMPUTACION</th>
			<th></th>
			<th></th>
		</tr>
		<?php foreach($cancelaciones as $cancelacion):?>
			<tr>
				<td><strong><?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['id'],array('title' => 'DETALLE ORDEN DE CANCELACION','url' => '/mutual/cancelacion_ordenes/vista_detalle/'.$cancelacion['CancelacionOrden']['id'],'h' => 450, 'w' => 750))?></strong></td>
				<td align="center"><?php echo $cancelacion['CancelacionOrden']['tipo_cancelacion_desc']?></td>
				<td align="center"><?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cancelacion['CancelacionOrden']['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$cancelacion['CancelacionOrden']['orden_descuento_id'].'/'.$cancelacion['CancelacionOrden']['socio_id'],'h' => 450, 'w' => 750))?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['tipo_nro_odto']?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['proveedor_producto_odto']?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['cuotas_str']?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['a_la_orden_de']?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['concepto']?></td>
				<td align="right"><strong><?php echo number_format($cancelacion['CancelacionOrden']['importe_proveedor'],2)?></strong></td>
				<td align="right"><?php echo number_format($cancelacion['CancelacionOrden']['saldo_orden_dto'],2)?></td>
				<td align="right"><?php echo number_format($cancelacion['CancelacionOrden']['importe_seleccionado'],2)?></td>
				<td align="right">
					<?php
						if(!empty($cancelacion['CancelacionOrden']['tipo_cuota_diferencia'])){
							echo $this->requestAction('/config/global_datos/valor/' . $cancelacion['CancelacionOrden']['tipo_cuota_diferencia']);
							echo "&nbsp;= \$";
							echo number_format($cancelacion['CancelacionOrden']['importe_diferencia'],2);
						}
					?>
				</td>
				<td align="center"><strong><?php echo $util->armaFecha($cancelacion['CancelacionOrden']['fecha_vto'])?></strong></td>
				<td align="right"><strong><?php echo number_format($cancelacion['CancelacionOrden']['total_orden'],2)?></strong></td>
				<td><?php echo $cancelacion['CancelacionOrden']['observaciones']?></td>
				<td align="center">
				
					<?php if($cancelacion['CancelacionOrden']['nueva_orden_dto_id'] != 0):?>
						<?php echo $controles->linkModalBox('ORDEN DTO. #'.$cancelacion['CancelacionOrden']['nueva_orden_dto_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cancelacion['CancelacionOrden']['nueva_orden_dto_id'],'url' => '/mutual/orden_descuentos/view/'.$cancelacion['CancelacionOrden']['nueva_orden_dto_id'].'/'.$cancelacion['CancelacionOrden']['socio_id'],'h' => 450, 'w' => 750))?>
						<br/>
						<strong>
						<?php echo $cancelacion['CancelacionOrden']['norden_tipo_nro_odto']?>
						|
						<?php echo $cancelacion['CancelacionOrden']['norden_proveedor_producto_odto']?>
						</strong>
					<?php endif;?>
					<?php if(!empty($cancelacion['CancelacionOrden']['nro_solicitud'])):?>
						<?php echo $cancelacion['CancelacionOrden']['solicitud_str']?>
					<?php endif;?>
				
				</td>
				<td align="center"><?php echo $util->armaFecha($cancelacion['CancelacionOrden']['fecha_imputacion'])?></td>
				<td><?php echo $controles->btnImprimir('','/mutual/cancelacion_ordenes/view/'.$cancelacion['CancelacionOrden']['id'].'/1','_blank')?></td>
				<td><?php //   echo $controles->botonGenerico('anular/'.$cancelacion['CancelacionOrden']['id'],'controles/stop1.png','',null,'ANULAR LA ORDEN #'.$cancelacion['CancelacionOrden']['id'])?></td>
		<?php endforeach;?>
		</table>
<?php else:?>
	NO EXISTEN ORDENES DE CANCELACION PROCESADAS
<?php endif;?>
<br/>
<br/>
<br/>
<?php if(count($cancelaciones_emitidas) != 0):?>
	
<h4>DETALLE DE ORDENES DE CANCELACION EMITIDAS</h4>
	<table>
		<tr>
			<th>#</th>
			<th>TIPO</th>
			<th>ORDEN</th>
			<th>TIPO / NUMERO</th>
			<th>PROVEEDOR / PRODUCTO</th>
			<th>CONCEPTO</th>
			<th>A LA ORDEN DE</th>
			<th>DEUDA PROVEEDOR</th>
			<th>SALDO ORDEN DTO</th>
			<th>IMPORTE SELECCIONADO</th>
			<th>DEBITO/CREDITO</th>
			<th>VENCIMIENTO</th>
			<th></th>
			<th></th>
			
		</tr>
		<?php foreach($cancelaciones_emitidas as $cancelacion):?>
			<tr>
				<td><strong><?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['id'],array('title' => 'DETALLE ORDEN DE CANCELACION','url' => '/mutual/cancelacion_ordenes/vista_detalle/'.$cancelacion['CancelacionOrden']['id'],'h' => 450, 'w' => 750))?></strong></td>
				<td align="center"><?php echo $cancelacion['CancelacionOrden']['tipo_cancelacion_desc']?></td>
				<td align="center"><?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cancelacion['CancelacionOrden']['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$cancelacion['CancelacionOrden']['orden_descuento_id'].'/'.$cancelacion['CancelacionOrden']['socio_id'],'h' => 450, 'w' => 750))?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['tipo_nro_odto']?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['proveedor_producto_odto']?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['a_la_orden_de']?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['concepto']?></td>
				<td align="right"><strong><?php echo number_format($cancelacion['CancelacionOrden']['importe_proveedor'],2)?></strong></td>
				<td align="right"><?php echo number_format($cancelacion['CancelacionOrden']['saldo_orden_dto'],2)?></td>
				<td align="right"><?php echo number_format($cancelacion['CancelacionOrden']['importe_seleccionado'],2)?></td>
				<td align="right">
					<?php
						if(!empty($cancelacion['CancelacionOrden']['tipo_cuota_diferencia'])){
							echo $this->requestAction('/config/global_datos/valor/' . $cancelacion['CancelacionOrden']['tipo_cuota_diferencia']);
							echo "&nbsp;= \$";
							echo number_format($cancelacion['CancelacionOrden']['importe_diferencia'],2);
						}
					?>
				</td>
				<td align="center"><strong><?php echo $util->armaFecha($cancelacion['CancelacionOrden']['fecha_vto'])?></strong></td>
				<td><?php echo $controles->btnImprimir('','/mutual/cancelacion_ordenes/view/'.$cancelacion['CancelacionOrden']['id'].'/1','_blank')?></td>
				<td><?php echo $controles->botonGenerico('/mutual/cancelacion_ordenes/borrar_desde_padron/'.$cancelacion['CancelacionOrden']['id'].'/'.$cancelacion['CancelacionOrden']['socio_id'],'controles/user-trash.png','',null,'Borrar la Orden de Cancelacion #'.$cancelacion['CancelacionOrden']['id'])?></td>
		<?php endforeach;?>	
	</table>
<div style="clear: both;"><br/></div>
<hr/>
	<?php echo $frm->btnForm(array('LABEL' => 'GENERAR ORDEN DE COBRO POR CAJA','URL' => '/mutual/cancelacion_ordenes/generar_orden_cobro_caja_recibo/'.$socio['Persona']['id']))?>
	<?php // echo $frm->btnForm(array('LABEL' => 'ORDEN DE COBRO CAJA A�OS ANTER.','URL' => '/mutual/cancelacion_ordenes/generar_orden_cobro_caja/'.$socio['Persona']['id']))?>
<?php endif;?>

<?php //   debug($cancelaciones_emitidas)?>