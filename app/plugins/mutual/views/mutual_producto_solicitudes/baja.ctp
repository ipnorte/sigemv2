<?php echo $this->renderElement('head',array('title' => 'BAJA ORDEN DE COMPRA','plugin' => 'config'))?>

<?php if(empty($persona)) echo $this->renderElement('personas/search',array('accion' => 'baja','plugin' => 'pfyj'))?>
<?php //   if(!empty($personas))echo $this->renderElement('personas/grilla_personas_paginada',array('personas'=>$personas,'accion'=>'/mutual/mutual_producto_solicitudes/baja/','icon' => 'controles/cart.png','plugin' => 'pfyj'))?>

<?php 
echo $this->renderElement(
							'personas/grilla_personas_paginada',
							array(
									'plugin' => 'pfyj',
									'accion'=>'/mutual/mutual_producto_solicitudes/baja/',
									'icon' => 'controles/cart.png',
									'busquedaAvanzada' => false,
									'datos_post' => $this->data
							));
?>


<?php if(!empty($persona)){
	echo $this->renderElement('personas/datos_personales',array('persona_id'=>$persona['Persona']['id'],'plugin' => 'pfyj'));
	echo $controles->btnRew('Regresar','/mutual/mutual_producto_solicitudes/baja');
}?>

<?php if(!empty($solicitudes)):?>

	<h3>ORDENES DE COMPRAS</h3>
	
	<div class="notices"><strong>ATENCION!: </strong> Este proceso afecta elimina a las Ordenes de Consumo y/o Servicio cuya Orden de Descuento NO REGISTRA PAGOS.</div>
	
	<?php echo $frm->create(null,array('action' => 'baja/'.$persona['Persona']['id']))?>	
		<table>
			<tr>
				<th></th>
				<th>NUMERO</th>
				<th>FECHA CARGA</th>
				<th>FECHA PAGO</th>
				<th>INICIA</th>
				<th>ESTADO</th>
				<th>PROVEEDOR - PRODUCTO</th>
				<th>TOTAL</th>
				<th>CUOTAS</th>
				<th>IMPORTE</th>
				<th>PER</th>
				<th>BENEFICIO</th>
				<th></th>
				<th></th>
			
			</tr>
		
		<?php
		$i = 0;
		foreach ($solicitudes as $sol):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		//	debug($sol);
		?>	
			<tr<?php echo $class;?>>
				<td align="center"><input type="checkbox" name="data[MutualProductoSolicitud][check_id][<?php echo $sol['MutualProductoSolicitud']['id']?>]" value="1"/></td>
				<td align="center" nowrap="nowrap"><?php echo $sol['MutualProductoSolicitud']['tipo_orden_dto']?> #<?php echo $sol['MutualProductoSolicitud']['id']?></td>
				<td nowrap="nowrap" align="center"><?php echo $util->armaFecha($sol['MutualProductoSolicitud']['fecha'])?></td>
				<td nowrap="nowrap" align="center"><?php echo $util->armaFecha($sol['MutualProductoSolicitud']['fecha_pago'])?></td>
				<td align="center"><?php echo $util->periodo($sol['MutualProductoSolicitud']['periodo_ini'])?></td>
				<td align="center"><?php echo $this->requestAction('/config/global_datos/valor/'.$sol['MutualProductoSolicitud']['estado'])?></td>
				<td nowrap="nowrap"><strong><?php echo $sol['MutualProducto']['Proveedor']['razon_social_resumida']?></strong> - <?php echo $this->requestAction('/config/global_datos/valor/'.$sol['MutualProducto']['tipo_producto'])?> <?php echo ($sol['MutualProductoSolicitud']['nro_referencia_proveedor'] != '' ? '(REF: '.$sol['MutualProductoSolicitud']['nro_referencia_proveedor'].')' : '')?></td>
				<td align="right"><strong><?php echo number_format($sol['MutualProductoSolicitud']['importe_total'],2)?></strong></td>
				<td align="center"><strong><?php echo $sol['MutualProductoSolicitud']['cuotas']?></strong></td>
				<td align="right"><?php echo number_format($sol['MutualProductoSolicitud']['importe_cuota'],2);?></td>
				<td align="center"><?php echo $controles->OnOff2($sol['MutualProductoSolicitud']['permanente'],true)?></td>
				<td><?php echo $this->requestAction('/pfyj/persona_beneficios/view/'.$sol['MutualProductoSolicitud']['persona_beneficio_id'])?></td>
				<td align="center"><?php echo $controles->btnImprimir('','/mutual/mutual_producto_solicitudes/imprimir_orden_pdf/'.$sol['MutualProductoSolicitud']['id'].'/'.$sol['MutualProductoSolicitud']['permanente'],'blank')?></td>
				<td align="center"><?php echo $controles->btnModalBox(array('title' => 'DETALLE DE LA SOLICITUD '.$sol['MutualProductoSolicitud']['tipo_orden_dto'].'#' . $sol['MutualProductoSolicitud']['id'],'url' => '/mutual/mutual_producto_solicitudes/view/'.$sol['MutualProductoSolicitud']['id'],'h' => 450, 'w' => 750))?></td>
			</tr>
		
		<?php endforeach;?>	
		
		</table>
		
	<?php echo $frm->hidden('procesar_baja',array('value' => 1))?>
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'DAR DE BAJA','URL' => ( empty($fwrd) ? "/mutual/mutual_producto_solicitudes/baja" : $fwrd) ))?>

<?php endif;?>