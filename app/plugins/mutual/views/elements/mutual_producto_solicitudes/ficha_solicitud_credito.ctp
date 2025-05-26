<?php if(!isset($fPago)) $fPago = false?>

<div class="areaDatoForm3">
	<h4>SOLICITUD DE CREDITO Nro. <?php echo $solicitud['MutualProductoSolicitud']['nro_print']?>
	</h4>
	<div class="row">
		FECHA CARGA: <?php echo $util->armaFecha($solicitud['MutualProductoSolicitud']['fecha'])?>
		&nbsp;|&nbsp;ESTADO: <strong><?php echo $util->globalDato($solicitud['MutualProductoSolicitud']['estado'])?></strong>
	</div>
	<br/>
	<div class="row">
		PROVEEDOR - PRODUCTO: <strong><?php echo $solicitud['MutualProductoSolicitud']['proveedor_producto']?></strong>
	</div>
	<br/>
	<div class="row">
		CAPITAL SOLICITADO: <strong><?php echo number_format($solicitud['MutualProductoSolicitud']['importe_solicitado'],2);?></strong>
		&nbsp;|&nbsp;
		NETO A PERCIBIR : <strong><?php echo number_format($solicitud['MutualProductoSolicitud']['importe_percibido'],2);?></strong>
	</div>
	<br/>
	<div class="row">
		TOTAL A REINTEGRAR: <strong><?php echo number_format($solicitud['MutualProductoSolicitud']['importe_total'],2);?></strong>
		&nbsp;|&nbsp;
		CANTIDAD DE CUOTAS: <strong><?php echo $solicitud['MutualProductoSolicitud']['cuotas_print']?></strong>
		&nbsp;|&nbsp;
		MONTO DE LA CUOTA CUOTA: <strong><?php echo number_format($solicitud['MutualProductoSolicitud']['importe_cuota'],2);?></strong>

	</div>
	<br/>
	<div class="row">
		BENEFICIO: <strong><?php echo $solicitud['MutualProductoSolicitud']['beneficio_str']?></strong>		
	</div>
	<br/>
	<?php if(!empty($solicitud['MutualProductoSolicitud']['observaciones'])):?>
		<div class="areaDatoForm2" style="font-size: x-small;">
		<?php echo $solicitud['MutualProductoSolicitud']['observaciones']?>
		</div>
		<br/>
	<?php endif;?>
	<?php if(!empty($solicitud['MutualProductoSolicitud']['reasignar_proveedor_id'])):?>
		<div class='notices_error' style="width: auto;">
			REASIGNAR A <strong><?php echo $solicitud['MutualProductoSolicitud']['proveedor_reasignada_a']?></strong>
			&nbsp;|&nbsp;
			USUARIO: <strong><?php echo $solicitud['MutualProductoSolicitud']['reasignar_proveedor_usuario']?></strong> | FECHA: <?php echo $solicitud['MutualProductoSolicitud']['reasignar_proveedor_fecha']?>
		</div>	
		<br/>
	<?php endif;?>
	<div class=row>
		<?php if($solicitud['MutualProductoSolicitud']['aprobada'] == 1):?>
		FECHA PAGO: <?php echo $util->armaFecha($solicitud['MutualProductoSolicitud']['fecha_pago'])?>
		&nbsp;|&nbsp;
		<?php endif;?>
		INICIA EN: <strong><?php echo $util->periodo($solicitud['MutualProductoSolicitud']['periodo_ini'])?></strong>
		&nbsp;|&nbsp;1er. VTO CLIENTE: <strong><?php echo $util->armaFecha($solicitud['MutualProductoSolicitud']['primer_vto_socio'])?></strong>
	</div>
	<br/>
	<div class=row>
		FORMA DE PAGO: <strong><?php echo $solicitud['MutualProductoSolicitud']['forma_pago_desc']?></strong>
	</div>
        <?php if(!empty($solicitud['MutualProductoSolicitud']['tna'])):?>
        <br/>
        <div class=row>
            <table>
            <tr>
                <td style="text-align: left;">TNA</td><td style="text-align: right;"><strong><?php echo number_format($solicitud['MutualProductoSolicitud']['tna'],2)?>%</strong></td>
                <td style="text-align: left;">TEM</td><td style="text-align: right;"><strong><?php echo number_format($solicitud['MutualProductoSolicitud']['tnm'],2)?>%</strong></td>
                <?php if(floatval($solicitud['MutualProductoSolicitud']['gasto_admin_porc'])!= 0):?>
                <td style="text-align: left;">GASTO ADM.</td><td style="text-align: right;"><strong><?php echo number_format($solicitud['MutualProductoSolicitud']['gasto_admin_porc'],2)?>%</strong></td>
                <?php endif;?>
                <?php if(floatval($solicitud['MutualProductoSolicitud']['sellado_porc'])!= 0):?>
                <td style="text-align: left;">SELLADO.</td><td style="text-align: right;"><strong><?php echo number_format($solicitud['MutualProductoSolicitud']['sellado_porc'],2)?>%</strong></td>
                <?php endif;?>
                <?php if(floatval($solicitud['MutualProductoSolicitud']['iva_porc']) != 0):?>
                <td style="text-align: left;">IVA</td><td style="text-align: right;"><strong><?php echo number_format($solicitud['MutualProductoSolicitud']['iva_porc'],2)?>%</strong></td>
                <?php endif;?>
                <?php if(floatval($solicitud['MutualProductoSolicitud']['cft'])!= 0):?>
                <td style="text-align: left;">CFT</td><td style="text-align: right;"><strong><?php echo number_format($solicitud['MutualProductoSolicitud']['cft'],2)?>%</strong></td>
                <?php endif;?>
            </tr>
                
            </table>
        </div>
        <?php endif;?>

		<?php if(!empty($solicitud['MutualProductoSolicitud']['detalle_calculo_plan'])):?>
			
			<?php echo $this->renderElement('mutual_producto_solicitudes/detalle_credito',array('detalle_calculo_plan'=> $solicitud['MutualProductoSolicitud']['detalle_calculo_plan'],'plugin' => 'mutual'));?>
					
		<?php endif;?>			

	<?php if(!empty($solicitud['MutualProductoSolicitud']['MutualProductoSolicitudPago'])):?>
	<br/>
		<h4>FORMA DE LIQUIDACION</h4>
		<table>
			<tr>
				<th>MEDIO</th><th>OBSERVACIONES</th><th>IMPORTE</th>
			</tr>
			<?php foreach ($solicitud['MutualProductoSolicitud']['MutualProductoSolicitudPago'] as $pago):?>
				<tr>
					<td><?php echo $pago['forma_pago_desc']?></td>
					<td><?php echo $pago['observaciones']?></td>
					<td align="right"><?php  echo $util->nf($pago['importe'])?></td>
				<tr>
				
			<?php endforeach;?>

		</table>
	<?php endif;?>
	<?php if(!empty($solicitud['MutualProductoSolicitud']['vendedor_id'])):?>
	<br/>
	<div class=row>
		VENDEDOR: <strong><?php echo $solicitud['MutualProductoSolicitud']['vendedor_nombre']?></strong>
		<?php if(!empty($solicitud['MutualProductoSolicitud']['vendedor_remito_id'])):?>
		&nbsp;|&nbsp; CONSTANCIA DE PRESENTACION: <strong><?php echo $controles->linkModalBox($solicitud['MutualProductoSolicitud']['vendedor_remito'],array('title' => 'CONSTANCIA DE PRESENTACION #' . $solicitud['MutualProductoSolicitud']['vendedor_remito_id'],'url' => '/ventas/vendedores/ficha_remito/'.$solicitud['MutualProductoSolicitud']['vendedor_remito_id'],'h' => 450, 'w' => 850))?></strong>
		<?php endif;?>	
	<div class=row>	
	<?php endif;?>
	<?php if(!empty($solicitud['MutualProductoSolicitudCancelacion'])):?>
	<br/>
	<div class=row>
		<h4>ORDENES DE CANCELACION ASOCIADAS</h4>
		<table>
			<tr>
			<?php foreach ($solicitud['MutualProductoSolicitudCancelacion'] as $cancelacion):?>
					<td><?php echo $controles->linkModalBox("CANCELACION #".$cancelacion['cancelacion_orden_id'],array('title' => 'DETALLE ORDEN DE CANCELACION','url' => '/mutual/cancelacion_ordenes/vista_detalle/'.$cancelacion['cancelacion_orden_id'],'h' => 450, 'w' => 750))?></td>
			<?php endforeach;?>
			</tr>
		</table>
	</div>	
	<?php endif;?>
	<?php if(!empty($solicitud['MutualProductoSolicitudInstruccionPago'])):?>
	<br/>
	<div class=row>
		<h4>INSTRUCCION DE PAGO</h4>
		<table>
			<tr>
				<th>A LA ORDEN DE</th>
				<th>CONCEPTO</th>
				<th>IMPORTE</th>
			</tr>
			<?php $totalInstruccion = 0;?>
			<?php foreach ($solicitud['MutualProductoSolicitudInstruccionPago'] as $instruccion):?>
				<tr>
					<td><?php echo $instruccion['a_la_orden_de']?></td>
					<td><?php echo $instruccion['concepto']?></td>
					<td align="right"><?php echo $util->nf($instruccion['importe'])?></td>
				</tr>
				<?php $totalInstruccion += $instruccion['importe'];?>
			<?php endforeach;?>
			<tr class="totales">
				<td colspan="2">TOTAL</td>
				<td align="right"><?php  echo $util->nf($totalInstruccion)?></td>
			</tr>			
		</table>
	</div>
	<?php endif;?>
	<?php if(!empty($solicitud['MutualProductoSolicitudDocumento'])):?>
		<div class=row>
			
			<ul style="list-style-type: square;border: 1px solid #858265;padding: 5px;margin: 5px 0px 5px 0px;">
			<h4>DOCUMENTACION ADJUNTA</h4>
			<div class="actions">
				<?php echo $controles->botonGenerico('download_attach_zipped/'.$solicitud['MutualProductoSolicitud']['id'],'controles/disk_multiple.png','DESCARGAR Y EMPAQUETAR TODOS LOS ADJUNTOS',array('target' => '_blank'))?>
			</div>
                            <?php foreach ($solicitud['MutualProductoSolicitudDocumento'] as $documento):?>
                                    <li style="margin-left: 20px;"><?php echo $controles->botonGenerico('/mutual/mutual_producto_solicitudes/download_attach/'.$documento['MutualProductoSolicitudDocumento']['id'],'controles/attach.png',$documento['GlobalDato']['concepto_1']." (".$documento['MutualProductoSolicitudDocumento']['file_name'].")")?></li>
                            <?php endforeach;?>
			</ul>
		</div>		
	
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
	<?php //   if($solicitud['MutualProductoSolicitud']['orden_descuento_id'] != 0 && $showOrdenDto == 1):?>
		<br/><br/>
		<?php echo $controles->btnToggle('detalleOrdenDto','ORDEN DE DESCUENTO EMITIDA #'.$solicitud['MutualProductoSolicitud']['orden_descuento_id'])?>
		<div id="detalleOrdenDto" style="display: none;clear: both;">
		<?php echo $this->requestAction('/mutual/orden_descuentos/view/'.$solicitud['MutualProductoSolicitud']['orden_descuento_id'])?>
		</div>
	<?php endif;?>
	<?php if($solicitud['MutualProductoSolicitud']['orden_descuento_seguro_id'] != 0):?>
	<?php //   if($solicitud['MutualProductoSolicitud']['orden_descuento_seguro_id'] != 0 && $showOrdenDto == 1):?>
		<br/><br/>
		<?php echo $controles->btnToggle('detalleOrdenDto2','ORDEN DE DESCUENTO EMITIDA #'.$solicitud['MutualProductoSolicitud']['orden_descuento_seguro_id'])?>
		<div id="detalleOrdenDto2" style="display: none;clear: both;">
		<?php echo $this->requestAction('/mutual/orden_descuentos/view/'.$solicitud['MutualProductoSolicitud']['orden_descuento_seguro_id'])?>
		</div>
	<?php endif;?>	
</div>
<div style="clear: both;"></div>
<?php // debug($solicitud)?>