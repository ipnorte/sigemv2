<?php echo $this->renderElement('head',array('title' => 'APROBAR ORDENES DE CONSUMO / SERVICIO','plugin' => 'config'))?>

<?php if(empty($persona)) echo $this->renderElement('personas/search',array('accion' => 'aprobar','plugin' => 'pfyj'))?>
<?php if(!empty($personas))echo $this->renderElement('personas/grilla_personas_paginada',array('personas'=>$personas,'accion'=>'/mutual/mutual_producto_solicitudes/aprobar/','icon' => 'controles/cart_put.png','plugin' => 'pfyj'))?>

<?php if(!empty($persona)){
	echo $this->renderElement('personas/datos_personales',array('persona_id'=>$persona['Persona']['id'],'plugin' => 'pfyj'));
	if(empty($solicitudes)) echo $controles->btnRew('Regresar','/mutual/mutual_producto_solicitudes/aprobar/'.$persona['Persona']['id']);
}?>
<?php 
/**********************************************************************************************************************
 * FORMULARIO DE FORMA DE PAGO
 **********************************************************************************************************************/
?>
<?php if(!empty($mutual_producto_solicitud_id)):?>
	<?php echo $this->requestAction('/mutual/mutual_producto_solicitudes/view/'.$mutual_producto_solicitud_id)?>
	<?php 
	echo $ajax->form(array('type' => 'post',
	    'options' => array(
	        'model'=>'MutualProductoSolicitudPago',
	        'update'=>'grilla_forma_pagos',
	        'url' => array('plugin' => 'mutual','controller' => 'mutual_producto_solicitudes', 'action' => 'cargar_forma_pago/'.$mutual_producto_solicitud_id),
			'loading' => "$('spinner').show();$('grilla_forma_pagos').hide();",
			'complete' => "$('grilla_forma_pagos').show();$('spinner').hide();"
	    )
	));
	?>	
	<div class="areaDatoForm">
		<h4>LIQUIDACION DE LA ORDEN COMPRA / SERVICIO</h4>
		<table class="tbl_form">
			<tr>
				<td>FORMA DE PAGO</td>
				<td>
				<?php echo $frm->input('MutualProductoSolicitudPago.forma_pago',array('type'=>'select','options'=>array('MUTUFPAG0001' => 'EFECTIVO', 'MUTUFPAG0002' => 'CHEQUE'),'empty'=>false,'selected' => '','label'=>""));?>				
				</td>
			</tr>
			<tr>
				<td>BANCO</td>
				<td>
				<?php echo $this->renderElement('banco/combo',array(
																	'plugin'=>'config',
																	'label' => " ",
																	'model' => 'MutualProductoSolicitudPago.banco_id',
																	'disable' => false,
																	'empty' => true,
																	'tipo' => 0
				))?>				
				</td>
			</tr>
			<tr>
				<td>NRO. COMPROBANTE</td><td><?php echo $frm->input('MutualProductoSolicitudPago.nro_comprobante',array('size'=>50,'maxlenght'=>50)); ?></td>
			</tr>
			<tr>
				<td>IMPORTE</td><td><?php echo $frm->money('MutualProductoSolicitudPago.importe')?></td>
			</tr>
			<tr>
				<td>OBSERVACIONES</td>
				<td><?php echo $frm->textarea('MutualProductoSolicitudPago.observaciones',array('cols' => 60, 'rows' => 3))?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo $frm->submit("CARGAR FORMA DE PAGO",array('id' => 'btn_cargar_forma_pago'))?></td>
			</tr>			
			<tr><td colspan="2"><?php echo $controles->ajaxLoader('spinner','CARGANDO FORMA DE PAGO....')?></td></tr>
							
		</table>
	</div>
	<?php echo $frm->hidden('MutualProductoSolicitudPago.mutual_producto_solicitud_id',array('value' => $mutual_producto_solicitud_id))?>
	<?php echo $frm->hidden('aprobar',array('value' => 0))?>
	</form>
	<?php 
	/***************************************************************************************************************************
	 * GRILLA AJAX CON DATOS DE O LOS PAGOS
	 ***************************************************************************************************************************/
	?>	
	<div id="grilla_forma_pagos"><?php echo $this->requestAction('/mutual/mutual_producto_solicitudes/cargar_forma_pago/'.$mutual_producto_solicitud_id)?></div>
	<hr/>
	<?php 
	/***************************************************************************************************************************
	 * APRUEBO LA ORDEN
	 ***************************************************************************************************************************/
	?>
	<?php echo $frm->create(null,array('action' => 'aprobar/'.$persona['Persona']['id']))?>
		<?php echo $frm->hidden('MutualProductoSolicitud.id',array('value' => $mutual_producto_solicitud_id))?>
		<?php echo $frm->hidden('MutualProductoSolicitud.aprobar',array('value' => 1))?>
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'APROBAR ORDEN DE CONSUMO / SERVICIO','URL' => ( empty($fwrd) ? "/mutual/mutual_producto_solicitudes/aprobar/".$persona['Persona']['id'] : $fwrd) ))?>
<?php endif;?>

<?php 
/**********************************************************************************************************************
 * GRILLA DE SOLICITUDES PENDIENTES DE APROBAR
 **********************************************************************************************************************/
?>
<?php if(!empty($solicitudes)):?>

	<h3>ORDENES DE COMPRAS / SERVICIOS PENDIENTES DE APROBACION</h3>
	
		<table>
			<tr>
				<th></th>
				<th>#</th>
				<th>FECHA CARGA</th>
				<th>FECHA PAGO</th>
				<th>INICIA</th>
				<th>PROVEEDOR - PRODUCTO</th>
				<th>TOTAL</th>
				<th>CUOTAS</th>
				<th>IMPORTE</th>
				<th>PER</th>
				<th>SC</th>
				<th>BENEFICIO</th>
				<th></th>
				
			
			</tr>
		
		<?php
		$i = 0;
		foreach ($solicitudes as $sol):
			$i++;
		?>	
			<tr id="TRL_<?php echo $i?>">
				<td><?php echo $frm->btnForm(array('URL'=>'/mutual/mutual_producto_solicitudes/aprobar/'.$persona['Persona']['id'].'/?ORD='.$sol['MutualProductoSolicitud']['id'],'LABEL' => 'APROBAR'))?></td>
				<td align="center" nowrap="nowrap"><?php echo $sol['MutualProductoSolicitud']['id']?></td>
				<td nowrap="nowrap" align="center"><?php echo $util->armaFecha($sol['MutualProductoSolicitud']['fecha'])?></td>
				<td nowrap="nowrap" align="center"><?php echo $util->armaFecha($sol['MutualProductoSolicitud']['fecha_pago'])?></td>
				<td align="center"><?php echo $util->periodo($sol['MutualProductoSolicitud']['periodo_ini'])?></td>
				<td nowrap="nowrap"><strong><?php echo $sol['MutualProducto']['Proveedor']['razon_social_resumida']?></strong> - <?php echo $this->requestAction('/config/global_datos/valor/'.$sol['MutualProducto']['tipo_producto'])?> <?php echo ($sol['MutualProductoSolicitud']['nro_referencia_proveedor'] != '' ? '(REF: '.$sol['MutualProductoSolicitud']['nro_referencia_proveedor'].')' : '')?></td>
				<td align="right"><strong><?php echo number_format($sol['MutualProductoSolicitud']['importe_total'],2)?></strong></td>
				<td align="center"><strong><?php echo $sol['MutualProductoSolicitud']['cuotas']?></strong></td>
				<td align="right"><?php echo number_format($sol['MutualProductoSolicitud']['importe_cuota'],2);?></td>
				<td align="center"><?php echo $controles->OnOff2($sol['MutualProductoSolicitud']['permanente'],true)?></td>
				<td align="center"><?php echo $controles->OnOff($sol['MutualProductoSolicitud']['sin_cargo'],true)?></td>
				<td><?php echo $this->requestAction('/pfyj/persona_beneficios/view/'.$sol['MutualProductoSolicitud']['persona_beneficio_id'])?></td>
				<td align="center"><?php echo $controles->btnImprimir('','/mutual/mutual_producto_solicitudes/imprimir_orden_pdf/'.$sol['MutualProductoSolicitud']['id'].'/'.$sol['MutualProductoSolicitud']['permanente'],'blank')?></td>
<!--				<td align="center"></td>-->
				
			</tr>
		
		<?php endforeach;?>	
		
		</table>
<?php endif;?>