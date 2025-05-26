<?php echo $this->renderElement('proveedor/proveedor_header',array('proveedor' => $proveedores, 'plugin' => 'proveedores'))?>
<h3>CANCELACIONES</h3>

<div class="areaDatoForm">
<?php echo $frm->create(null,array('action' => 'cancelaciones/' . $proveedores['Proveedor']['id'],'id' => 'form_proveedor_cancelaciones'))?>
	<table class="tbl_form">
		<tr>
			<td>VTO DESDE</td><td><?php echo $frm->calendar('Movimientos.cance_fecha_desde','',$fecha_desde,'1990',date("Y") + 1)?></td>
			<td>VTO HASTA</td><td><?php echo $frm->calendar('Movimientos.cance_fecha_hasta','',$fecha_hasta,'1990',date("Y") + 1)?></td>
			<td><?php echo $frm->submit("CONSULTAR")?></td>
		</tr>
	</table>
	<?php echo $frm->hidden('Movimientos.imprimir',array('value' => 0))?>
<?php echo $frm->end()?>	
</div>

<?php if(!empty($cancelaciones)):?>

	<?php echo $frm->create(null,array('action' => 'cancelaciones/' . $proveedores['Proveedor']['id'],'id' => 'form_proveedor_cancelaciones'))?>

	<?php if(!empty($cancelaciones['recibidas'])):?>

	<h3>CANCELACIONES RECIBIDAS</h3>
	
	<table class="tbl_grilla">
		<tr>
			<td colspan="17" align="right"><?php echo $frm->submit("IMPRIMIR")?></td>
		</tr>
		<tr>
			<th colspan="17">DETALLE DE CANCELACIONES RECIBIDAS</th>
		</tr>
		<tr>
			<th rowspan="2">#</th>
			<th rowspan="2">VTO</th>
			<th rowspan="2">SOCIO</th>
			<th colspan="2">OPERACION CANCELADA</th>
			<th colspan="3">OPERACION GENERADA</th>
			<th colspan="4">COBRO EMITIDO</th>
			<th colspan="2">COMPROBANTE INGRESO</th>
			<th rowspan="2">PAGADO</th>
			<th rowspan="2">SALDO</th>
			<th rowspan="2"></th>
		</tr>
		<tr>
		
			<th>ORDEN DTO</th>
			<th>TIPO NUMERO</th>
		
			<th>ORDEN DTO</th>
			<th>TIPO NUMERO</th>
			<th>PROVEEDOR</th>
			
			<th>#</th>
			<th>FECHA</th>
			<th>IMPORTE</th>
			<th>ORIGEN FONDO</th>
			
			<th>NUMERO</th>
			<th>FECHA</th>
						
		</tr>
		<?php foreach ($cancelaciones['recibidas'] as $cancelacion):?>
		
			<tr>
				<td><?php echo $controles->linkModalBox($cancelacion['cancelacion_id'],array('title' => 'DETALLE ORDEN DE CANCELACION','url' => '/mutual/cancelacion_ordenes/vista_detalle/'.$cancelacion['cancelacion_id'],'h' => 450, 'w' => 750))?></td>
				<td><?php echo $util->armaFecha($cancelacion['cancelacion_vto'])?></td>
				<td style="font-weight: bold;"><?php echo $cancelacion['socio']?></td>
				
				
				<td style="background: #ECF6FC;; text-align: center;"><?php echo $controles->linkModalBox($cancelacion['cancela_orden_dto'],array('title' => 'ORDEN DE DESCUENTO #' . $cancelacion['cancela_orden_dto'],'url' => '/mutual/orden_descuentos/view/'.$cancelacion['cancela_orden_dto'].'/'.$cancelacion['socio_id'],'h' => 450, 'w' => 750))?></td>
				<td style="background: #ECF6FC;"><?php echo $cancelacion['cancela_expediente']?></td>

				<td style="background-color: #dde2ee; text-align: center;"><?php echo $controles->linkModalBox($cancelacion['nueva_orden_dto'],array('title' => 'ORDEN DE DESCUENTO #' . $cancelacion['nueva_orden_dto'],'url' => '/mutual/orden_descuentos/view/'.$cancelacion['nueva_orden_dto'].'/'.$cancelacion['socio_id'],'h' => 450, 'w' => 750))?></td>
				<td style="background-color: #dde2ee;"><?php echo $cancelacion['nuevo_expediente']?></td>				
				<td style="background-color: #dde2ee;"><?php echo $cancelacion['nuevo_expediente_proveedor']?></td>
				
				<td style="background: #CDEB8B; ; text-align: center;"><?php echo $controles->linkModalBox($cancelacion['socio_orden_cobro_id'],array('title' => 'ORDEN DE COBRO #' . $cancelacion['socio_orden_cobro_id'],'url' => '/mutual/orden_descuento_cobros/view/'.$cancelacion['socio_orden_cobro_id'],'h' => 450, 'w' => 750))?></td>
				<td style="background: #CDEB8B;"><?php echo $util->armaFecha($cancelacion['socio_orden_cobro_fecha'])?></td>
				<td style="background: #CDEB8B;" align="right"><?php echo $util->nf($cancelacion['socio_orden_cobro_importe'])?></td>
				<td style="background: #CDEB8B;"><?php echo $cancelacion['origen_fondo']?></td>
				
				<td style="background: #FFFF88;"><?php echo $cancelacion['comprobante_numero']?></td>
				<td style="background: #FFFF88;"><?php echo $cancelacion['comprobante_fecha']?></td>
	
				<td align="right"><?php echo $util->nf($cancelacion['importe_pagado'])?></td>
				<td align="right"><?php echo $util->nf($cancelacion['saldo'])?></td>
				
				<td><input type="checkbox" name="data[CancelacionOrdenRecibidas][id][<?php echo $cancelacion['cancelacion_id']?>]" value="<?php echo $cancelacion['cancelacion_id']?>"/></td>
				
			</tr>
		
		<?php endforeach;?>
	</table>	

	<?php endif;?>
	<?php if(!empty($cancelaciones['realizadas'])):?>

	<h3>CANCELACIONES EFECTUADAS</h3>

	<table class="tbl_grilla">
		<tr>
			<td colspan="17" align="right"><?php echo $frm->submit("IMPRIMIR")?></td>
		</tr>	
		<tr>
			<th colspan="17">DETALLE DE CANCELACIONES EFECTUADAS</th>
		</tr>
		<tr>
			<th rowspan="2">#</th>
			<th rowspan="2">VTO</th>
			<th rowspan="2">SOCIO</th>
			<th colspan="2">OPERACION GENERADA</th>
			<th colspan="3">OPERACION CANCELADA</th>
			<th colspan="4">COBRO EMITIDO</th>
			<th colspan="2">COMPROBANTE INGRESO</th>
			<th rowspan="2">RETENIDO</th>
			<th rowspan="2">SALDO</th>	
			<th rowspan="2"></th>			
		</tr>
		<tr>
		
			<th>ORDEN DTO</th>
			<th>TIPO NUMERO</th>
		
			<th>ORDEN DTO</th>
			<th>TIPO NUMERO</th>
			<th>PROVEEDOR</th>
			
			<th>#</th>
			<th>FECHA</th>
			<th>IMPORTE</th>
			<th>ORIGEN FONDO</th>
			
			<th>NUMERO</th>
			<th>FECHA</th>
						
		</tr>
		<?php foreach ($cancelaciones['realizadas'] as $cancelacion):?>
		
			<tr>
				<td><?php echo $controles->linkModalBox($cancelacion['cancelacion_id'],array('title' => 'DETALLE ORDEN DE CANCELACION','url' => '/mutual/cancelacion_ordenes/vista_detalle/'.$cancelacion['cancelacion_id'],'h' => 450, 'w' => 750))?></td>
				<td><?php echo $cancelacion['cancelacion_vto']?></td>
				<td style="font-weight: bold;"><?php echo $cancelacion['socio']?></td>
				
				<td style="background-color: #dde2ee; text-align: center;"><?php echo $controles->linkModalBox($cancelacion['nueva_orden_dto'],array('title' => 'ORDEN DE DESCUENTO #' . $cancelacion['nueva_orden_dto'],'url' => '/mutual/orden_descuentos/view/'.$cancelacion['nueva_orden_dto'].'/'.$cancelacion['socio_id'],'h' => 450, 'w' => 750))?></td>
				<td style="background-color: #dde2ee;"><?php echo $cancelacion['nuevo_expediente']?></td>				
				
				<td style="background: #ECF6FC;; text-align: center;"><?php echo $controles->linkModalBox($cancelacion['cancela_orden_dto'],array('title' => 'ORDEN DE DESCUENTO #' . $cancelacion['cancela_orden_dto'],'url' => '/mutual/orden_descuentos/view/'.$cancelacion['cancela_orden_dto'].'/'.$cancelacion['socio_id'],'h' => 450, 'w' => 750))?></td>
				<td style="background: #ECF6FC;"><?php echo $cancelacion['cancela_expediente']?></td>
				<td style="background: #ECF6FC;"><?php echo $cancelacion['cancela_comercio']?></td>
				
				<td style="background: #CDEB8B; ; text-align: center;"><?php echo $controles->linkModalBox($cancelacion['socio_orden_cobro_id'],array('title' => 'ORDEN DE COBRO #' . $cancelacion['socio_orden_cobro_id'],'url' => '/mutual/orden_descuento_cobros/view/'.$cancelacion['socio_orden_cobro_id'],'h' => 450, 'w' => 750))?></td>
				<td style="background: #CDEB8B;"><?php echo $cancelacion['socio_orden_cobro_fecha']?></td>
				<td style="background: #CDEB8B;" align="right"><?php echo $cancelacion['socio_orden_cobro_importe']?></td>
				<td style="background: #CDEB8B;"><?php echo $cancelacion['origen_fondo']?></td>
				
				<td style="background: #FFFF88;"><?php echo $cancelacion['comprobante_numero']?></td>
				<td style="background: #FFFF88;"><?php echo $cancelacion['comprobante_fecha']?></td>
				
				<td align="right"><?php echo $util->nf($cancelacion['importe_retenido'])?></td>
				<td align="right"><?php echo $util->nf($cancelacion['saldo'])?></td>
				
				<td><input type="checkbox" name="data[CancelacionOrdenEfectuadas][id][<?php echo $cancelacion['cancelacion_id']?>]" value="<?php echo $cancelacion['cancelacion_id']?>"/></td>
				
			</tr>
		
		<?php endforeach;?>
	</table>
	
	<?php echo $frm->hidden('Movimientos.cance_fecha_desde',array('value' => $fecha_desde))?>
	<?php echo $frm->hidden('Movimientos.cance_fecha_hasta',array('value' => $fecha_hasta))?>
	<?php echo $frm->hidden('Movimientos.imprimir',array('value' => 1))?>
	<?php echo $frm->end()?>

	<?php //   debug($cancelaciones)?>
	<?php endif;?>

<?php endif;?>