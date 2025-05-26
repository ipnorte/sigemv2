<?php if(!isset($fPago)) $fPago = false?>
<div class="areaDatoForm3">
	<h4>ORDEN DE CONSUMO / SERVICIO :: 
	<?php echo $solicitud['MutualProductoSolicitud']['tipo_orden_dto']?> #<?php echo $solicitud['MutualProductoSolicitud']['id']?>
	<?php if($solicitud['MutualProductoSolicitud']['sin_cargo'] == 1) echo " *** SIN CARGO ***"?>
	</h4>
	<div classs="row">
		FECHA CARGA: <?php echo $util->armaFecha($solicitud['MutualProductoSolicitud']['fecha'])?>
		&nbsp;|&nbsp;FECHA PAGO: <?php echo $util->armaFecha($solicitud['MutualProductoSolicitud']['fecha_pago'])?>
		&nbsp;|&nbsp;ESTADO: <strong><?php echo $util->globalDato($solicitud['MutualProductoSolicitud']['estado'])?>  <?php // echo ($solicitud['MutualProductoSolicitud']['aprobada'] == 1 ? 'APROBADA' : 'EMITIDA')?></strong>
	</div>
	<div classs="row">
		INICIA EN: <strong><?php echo $util->periodo($solicitud['MutualProductoSolicitud']['periodo_ini'])?></strong>
		&nbsp;|&nbsp;1er. VTO SOCIO: <strong><?php echo $util->armaFecha($solicitud['MutualProductoSolicitud']['primer_vto_socio'])?></strong>
	</div>
	<div classs="row">
		PROVEEDOR - PRODUCTO: <strong><?php echo $solicitud['MutualProductoSolicitud']['proveedor_producto']?></strong>
	</div>
	<div classs="row">
		IMPORTE CUOTA: <strong><?php echo number_format($solicitud['MutualProductoSolicitud']['importe_cuota'],2);?></strong>
		<?php echo ($solicitud['MutualProductoSolicitud']['permanente'] == 0 ? ' ('.$solicitud['MutualProductoSolicitud']['cuotas']. ' CUOTAS) ' : ' (PERMANENTE)')?>
		<?php if($solicitud['MutualProductoSolicitud']['permanente'] == 0):?>
		&nbsp;|&nbsp;TOTAL: <strong><?php echo number_format($solicitud['MutualProductoSolicitud']['importe_total'],2);?></strong>
		<?php endif;?>
	</div>
	<div classs="row">
		BENEFICIO: <strong><?php echo $solicitud['MutualProductoSolicitud']['beneficio_str']?></strong>
	</div>
	<?php if(!empty($solicitud['MutualProductoSolicitud']['observaciones'])):?>
		<div class="areaDatoForm2" style="font-size: x-small;">
		<?php echo $solicitud['MutualProductoSolicitud']['observaciones']?>
		</div>
	<?php endif;?>
	<?php if($fPago && !empty($solicitud['MutualProductoSolicitud']['MutualProductoSolicitudPago'])):?>
		<h4>FORMA DE PAGO</h4>
		<table>
			<tr>
				<th class="subtabla">FORMA</th>
				<th class="subtabla">BANCO</th>
				<th class="subtabla">NRO.COMPROBANTE</th>
				<th class="subtabla">IMPORTE</th>
				<th class="subtabla">OBSERVACIONES</th>
			</tr>
			<?php $ACU = 0;?>
			<?php foreach($solicitud['MutualProductoSolicitud']['MutualProductoSolicitudPago'] as $pago):?>
				<?php $ACU += $pago['importe'];?>
				<tr>
					<td><?php echo $pago['forma_pago_desc']?></td>
					<td><?php echo $pago['banco']?></td>
					<td align="center"><?php echo $pago['nro_comprobante']?></td>
					<td align="right"><?php echo $util->nf($pago['importe'])?></td>
					<td><?php echo $pago['observaciones']?></td>
				</tr>
			<?php endforeach;?>
				<tr>
					<th colspan="3" class="totales">TOTAL</th>
					<th class="totales"><?php echo $util->nf($ACU)?></th>
					<th class="totales"></th>
				</tr>
		</table>		
	<?php endif;?>	
	<?php if(!empty($solicitud['MutualProductoSolicitudEstado'])):?>
		<div class="areaDatoForm2" style="width: auto;">
            <h3>Historial de Estados</h3>
            <table>
                <tr>
                    <th>ESTADO</th><th>OBSERVACIONES</th><th>FECHA</th><th>USUARIO</th>
                </tr>
                <?php foreach($solicitud['MutualProductoSolicitudEstado'] as $estado):?>
                <tr>
                    <td><strong><?php echo $util->globalDato($estado['estado'])?></strong></td>
                    <td><?php echo $estado['observaciones']?></td>
                    <td><?php echo $estado['created']?></td>
                    <td><?php echo $estado['user_created']?></td>
                </tr>
                <?php endforeach;?>                
            </table>
		</div>	
		<br/>
	<?php endif;?> 
	<?php if($solicitud['MutualProductoSolicitud']['orden_descuento_id'] != 0):?>
		<?php echo $controles->btnToggle('detalleOrdenDto','DETALLE DE LA ORDEN DE DESCUENTO #'.$solicitud['MutualProductoSolicitud']['orden_descuento_id'])?>
			<div id="detalleOrdenDto" style="display: none;clear: both;">
		<?php echo $this->requestAction('/mutual/orden_descuentos/view/'.$solicitud['MutualProductoSolicitud']['orden_descuento_id'])?>
			</div>
	<?php endif;?>
</div>
<div style="clear: both;"></div>