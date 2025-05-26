<?php echo $this->renderElement('head',array('title' => 'APROBAR ORDEN DE COMPRA #'.$mutual_producto_solicitud_id,'plugin' => 'config'))?>
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
		<h4>LIQUIDACION DE LA ORDEN COMPRA</h4>
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
	<?php echo $frm->create(null,array('action' => 'pendientes_aprobar'))?>

		<?php if($solicitud['MutualProductoSolicitud']['permanente'] == 1):?>
		
			<div class="notices">Con la aprobaci&oacute;n de la presente Orden, se genera la PRIMER CUOTA correspondiente al periodo <strong><?php echo $util->periodo($solicitud['MutualProductoSolicitud']['periodo_ini'],true)?></strong></div>
			<div style="clear: both;width: 100%;"></div>
		<?php endif;?>
		
		<?php echo $frm->hidden('MutualProductoSolicitud.id',array('value' => $mutual_producto_solicitud_id))?>
		<?php echo $frm->hidden('MutualProductoSolicitud.aprobar',array('value' => 1))?>
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'APROBAR ORDEN DE CONSUMO / SERVICIO','URL' => ( empty($fwrd) ? "/mutual/mutual_producto_solicitudes/pendientes_aprobar" : $fwrd) ))?>
